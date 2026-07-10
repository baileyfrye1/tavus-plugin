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

    public function fetchVideos()
    {
        $response = wp_remote_get("{$this->baseUrl}/videos", [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
        ]);

        if (is_wp_error($response)) {
            throw new \RuntimeException($response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response), true);
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

        $response = wp_remote_get("{$this->baseUrl}/faces?face_ids={$queryString}", [
            'headers' => [
                'x-api-key' => $this->apiKey
            ]
        ]);

        if (is_wp_error($response)) {
            throw new \RuntimeException($response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body['data'] ?? [];
    }

    private function getFaceIds()
    {
        //TODO: Replace with fetching avatar face ids from admin page when built
        return ['r3f427f43c9d', 'r4ba1277e4fb'];
    }
}
