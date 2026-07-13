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

        if (count($videos) === 0) {
            return [];
        }

        // If no track is passed, return all videos filtered by avatar
        if (empty($track)) {
            return $this->filterByFaceId($videos, $faceId);
        }

        // Else, filter videos by avatar and track
        return $this->filterByTrackAndFaceId($videos, $faceId, $track);
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

    public function fetchFaces()
    {
        $faceIds = $this->getFaceIds();
        if (empty($faceIds)) {
            return [];
        }

        $queryString = implode(',', $faceIds);

        $path = "faces?face_ids={$queryString}";
        $cacheKey = "tavus_faces_" . md5($queryString);

        $faces = $this->getCachedData($cacheKey, $path);

        return $faces;
    }

    private function getFaceIds()
    {
        //TODO: Replace with fetching avatar face ids from admin page when built
        return ['r3f427f43c9d', 'r4ba1277e4fb'];
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

            set_transient($cacheKey, $data, 6 * HOUR_IN_SECONDS);
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
