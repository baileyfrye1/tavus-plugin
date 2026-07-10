<?php

if (!defined('ABSPATH')) {
	exit;
}

class Tavus_Rest_Controller
{
	private Tavus_API $api;
	public function __construct()
	{
		$this->api = new Tavus_API();
	}

	public function registerRoutes()
	{
		register_rest_route('tavus/v1', '/videos', [
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => [$this->api, 'fetchVideos'],
			'args' => [
				'face_id' => [
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				],
				/* 'track' => [ */
				/* 	'required' => true, */
				/* 	'type' => 'string', */
				/* 	'sanitize_callback' => 'sanitize_text_field', */
				/* ], */
			]
		]);

		register_rest_route('tavus/v1', '/video/(?P<videoId>[a-zA-Z0-9-]+)', [
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => [$this->api, 'fetchVideo'],
			'args' => [
				'videoId' => [
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				],
				'face_id' => [
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				],
				/* 'track' => [ */
				/* 	'required' => true, */
				/* 	'type' => 'string', */
				/* 	'sanitize_callback' => 'sanitize_text_field', */
				/* ], */
			],
		]);

		register_rest_route('tavus/v1', '/faces', [
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => [$this->api, 'fetchFaces']
		]);
	}
}

add_action('rest_api_init', [new Tavus_Rest_Controller(), 'registerRoutes']);
