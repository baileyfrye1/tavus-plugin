<?php

if (!defined('ABSPATH')) {
    exit;
}

const TAVUS_DEFAULT_FACE_IDS = [
    'parent-caregiver'   => [
        'r3f427f43c9d',
        'r4ba1277e4fb',
        'r1d7cf9edbb4',
        'r1a0108fbd75',
        'r90bbd427f71',
        'rfc63eab317e',
        'rdd4c86e5e1a',
        'rb43357fb2ee',
    ],
    'healthcare-provider' => [
        'r621a6013477',
        'rd3ba0f30551',
    ],
];

class Tavus_API
{
    private $baseUrl = "https://tavusapi.com/v2";
    private string $apiKey;
    public function __construct()
    {
        $this->apiKey = defined('TAVUS_API_KEY') ? TAVUS_API_KEY : '';
    }

    public function fetchVideos(WP_REST_Request $request)
    {
        $faceId = $request->get_param('face_id');
        $track = $request->get_param('track');

        if (empty($faceId)) {
            throw new \InvalidArgumentException("Face ID is required");
        }

        $videos = $this->getCachedData('tavus_videos_all', 'videos?limit=50', true);

        $filteredVideos = [];

        foreach ($videos as $video) {
            if (($video['status'] ?? null) !== 'generating') {
                $video['poster_url'] = $this->getVideoThumbnail($video['download_url']);
                $filteredVideos[] = $video;
            }
        }

        if (count($filteredVideos) === 0) {
            return [];
        }

        // If no track is passed, return all videos filtered by avatar
        if (empty($track)) {
            return $this->filterByFaceId($filteredVideos, $faceId);
        }

        // Else, filter videos by avatar and track
        return $this->filterByTrackAndFaceId($filteredVideos, $faceId, $track);
    }

    public function fetchVideo(WP_REST_Request $request)
    {
        $videoId = $request->get_param('videoId');

        $video = $this->getCachedData("tavus_video_{$videoId}", "videos/{$videoId}", false);

        $video['poster_url'] = $this->getVideoThumbnail($video['download_url']);

        return $video;
    }

    public function fetchFaces(WP_REST_Request $request)
    {
        $track = $request->get_param('track');
        $faceId = $request->get_param('face_id');

        if ($faceId) {
            $cacheKey = "tavus_face_" . md5($faceId);
            return $this->getCachedData($cacheKey, "faces?face_ids={$faceId}", true);
        }

        if (empty($track)) {
            $track = "";
        }

        return $this->fetchFacesByTrack($track);
    }

    public function fetchFacesByTrack(string $track = ''): array
    {
        $faceIds = $this->getFaceIds($track);
        if (empty($faceIds)) {
            return [];
        }

        $queryString = implode(',', $faceIds);

        $path = "faces?face_ids={$queryString}";
        $cacheKey = "tavus_faces_" . md5($queryString);

        return $this->getCachedData($cacheKey, $path, true);
    }

    public function invalidateTransients()
    {
        global $wpdb;

        delete_transient('tavus_videos_all');

        $wpdb->query(
            "DELETE FROM wp_options WHERE option_name LIKE '_transient_tavus_video_%'"
        );

        $tracks = ['parent-caregiver', 'healthcare-provider', null];

        foreach ($tracks as $track) {
            $faceIds = $this->getFaceIds($track);

            if (!empty($faceIds)) {
                $key = 'tavus_faces_' . md5(implode(',', $faceIds));
                delete_transient($key);
            }
        }

        return ['success' => true, 'message' => 'All transients cleared'];
    }

    private function getFaceIds(?string $track = null)
    {
        $settings = get_option('tavus_face_settings', ['face_ids' => TAVUS_DEFAULT_FACE_IDS]);
        $faceIds = $settings['face_ids'] ?? TAVUS_DEFAULT_FACE_IDS;

        $parentAvatars = $faceIds['parent-caregiver'] ?? [];
        $healthcareAvatars = $faceIds['healthcare-provider'] ?? [];

        return match ($track) {
            'parent-caregiver' => $parentAvatars,
            'healthcare-provider' => $healthcareAvatars,
            default => array_merge($parentAvatars, $healthcareAvatars)
        };
    }

    private function getCachedData(string $cacheKey, string $endpoint, bool $isList)
    {
        $data = get_transient($cacheKey);

        if (!$data) {
            $response = wp_remote_get("{$this->baseUrl}/{$endpoint}", [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
            ]);

            if (is_wp_error($response)) {
                throw new \RuntimeException($response->get_error_message());
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            $data = $isList ? ($body['data'] ?? []) : $body;

            $settings = get_option('tavus_general_settings', []);
            $cacheTTL = (int) ($settings['cache_ttl'] ?? 1) * HOUR_IN_SECONDS;

            set_transient($cacheKey, $data, $cacheTTL);
        }

        return $data;
    }

    private function filterByFaceId(array $videos, string $faceId)
    {
        $result = [];

        foreach ($videos as $video) {
            if ($video['replica_id'] === $faceId) {
                $result[] = $video;
            }
        }

        return $result;
    }

    private function filterByTrackAndFaceId(array $videos, string $faceId, string $track)
    {
        $result = [];

        foreach ($videos as $video) {
            $videoTrack = $this->extractTrack($video['video_name']);

            if (($video['replica_id'] ?? null) === $faceId && ($videoTrack === $track)) {
                $result[] = $video;
            }
        }

        return $result;
    }

    private function extractTrack(string $title)
    {
        if (preg_match('/^\[(parent-caregiver|healthcare-provider)\]/i', $title, $matches)) {
            return strtolower($matches[1]);
        }
        return null;
    }

    private function getVideoThumbnail(string $downloadUrl)
    {
        if (empty($downloadUrl)) {
            return null;
        }

        if (preg_match('#stream\.mux\.com/([^/]+)/#', $downloadUrl, $matches)) {
            $playback_id = $matches[1];
            return "https://image.mux.com/{$playback_id}/thumbnail.jpg";
        }

        return null;
    }
}
