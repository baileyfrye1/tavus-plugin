<?php

if (!defined('ABSPATH')) {
    exit;
}

class Tavus_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerSettingsPage']);
    }

    public function registerSettingsPage()
    {
        add_menu_page(
            'Tavus Settings',
            'Tavus',
            'manage_options',
            'tavus-settings',
            [$this, 'renderPage'],
            'dashicons-video-alt3',
            30
        );
    }

    public function renderPage()
    {
        require_once __DIR__ . '/tavus-settings.php';
    }
}

new Tavus_Admin();
