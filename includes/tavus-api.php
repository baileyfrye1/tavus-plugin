<?php

if (!defined('ABSPATH')) {
    exit;
}

class Tavus_API {
    private $baseUrl = "https://tavusapi.com/v2";
    public function __construct()
    {
        throw new \Exception('Not implemented');
    }

    public function fetchVideos() {
        return wp_remote_get($this->baseUrl . "/videos");
    }

    public function fetchVideo(string $videoId) {
        return wp_remote_get($this->baseUrl . "/videos/$videoId");
    }
}
