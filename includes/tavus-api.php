<?php

if (!defined('ABSPATH')) {
    exit;
}

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

        $videos = $this->getCachedData('tavus_videos_all', 'videos');

        $filteredVideos = [];

        foreach ($videos as $video) {
            if (($video['status'] ?? null) !== 'generating') {
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

        $response = wp_remote_get("{$this->baseUrl}/videos/$videoId", [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
        ]);

        if (is_wp_error($response)) {
            throw new \RuntimeException($response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public function fetchFaces(WP_REST_Request $request)
    {
        $track = $request->get_param('track');

        return $this->fetchFacesByTrack($track);
    }

    public function fetchFacesByTrack(string $track): array
    {
        $faceIds = $this->getFaceIds($track);
        if (empty($faceIds)) {
            return [];
        }

        $queryString = implode(',', $faceIds);

        $path = "faces?face_ids={$queryString}";
        $cacheKey = "tavus_faces_" . md5($queryString);

        return $this->getCachedData($cacheKey, $path);
    }

    public function invalidateTransients()
    {
        delete_transient('tavus_videos_all');

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
        //TODO: Replace with fetching avatar face ids from admin page when built
        // First two parent avatar ids stay, everything else is temporary right now
        $settings = get_option('tavus_face_settings', []);
        $faceIds = $settings['face_ids'] ?? [];

        $parentAvatars = $faceIds['parent-caregiver'] ?? [];
        $healthcareAvatars = $faceIds['healthcare-provider'] ?? [];

        return match ($track) {
            'parent-caregiver' => $parentAvatars,
            'healthcare-provider' => $healthcareAvatars,
            default => array_merge($parentAvatars, $healthcareAvatars)
        };
    }

    private function getCachedData(string $cacheKey, string $endpoint)
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
            $data = $body['data'] ?? [];

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
}
