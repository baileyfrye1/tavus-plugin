<?php

use WP_REST_Request;

if (!defined('ABSPATH')) {
    exit;
}

class Tavus_API
{
    private $baseUrl = "https://tavusapi.com/v2";
    private $apiKey;
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
}