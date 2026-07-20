<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('tavus_settings', [
    'api_key'   => defined('TAVUS_API_KEY') ? TAVUS_API_KEY : '',
    'face_ids'  => [
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
    ],
]);

// 2. Enqueue the React build (follow bootstrap pattern)
$manifestPath = __DIR__ . '/../dist/.vite/manifest.json';

if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true);

    if (isset($manifest['src/admin.tsx'])) {
        $entry = $manifest['src/admin.tsx'];
        $base  = plugin_dir_url(__DIR__) . 'dist';

        foreach ($entry['css'] as $cssFile) {
            wp_enqueue_style(
                'tavus-admin-' . sanitize_title($cssFile),
                "{$base}/{$cssFile}"
            );
        }

        wp_enqueue_script(
            'tavus-admin',
            "{$base}/{$entry['file']}",
            [],
            null,
            true
        );
        add_filter('script_loader_tag', function ($tag, $handle, $src) {
            if ('tavus-admin' === $handle) {
                return '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }, 10, 3);

        // 3. Localize data into the script
        wp_localize_script('tavus-admin', 'tavusSettings', [
            'data'  => $settings,
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
}

// 4. React mount point
?>
<div class="wrap">
    <h1>Tavus Settings</h1>
    <div id="tavus-settings"></div>
</div>
