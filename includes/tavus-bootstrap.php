<?php

class Tavus_Bootstrap
{
    public function __construct()
    {
        require_once __DIR__ . '/tavus-api.php';
        require_once __DIR__ . '/tavus-rest.php';
        require_once __DIR__ . '/tavus-admin.php';

        add_shortcode('tavus_integration', [$this, 'registerShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function registerShortcode()
    {
        return '<div id="tavus-resources"></div>';
    }

    public function enqueueAssets()
    {
        if (!is_page('learning')) {
            return;
        }

        if (defined('CT_FW_GET') || isset($_GET['ct_builder'])) {
            return;
        }

        $manifestPath = __DIR__ . '/../dist/.vite/manifest.json';

        if (!file_exists($manifestPath)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (isset($manifest['src/main.tsx'])) {
            $entry = $manifest['src/main.tsx'];
            $base = plugin_dir_url(__DIR__) . 'dist';
            if (!empty($entry['css'])) {
                foreach ($entry['css'] as $cssFile) {
                    wp_enqueue_style('tavus-' . sanitize_title($cssFile), "{$base}/{$cssFile}");
                }
            }
            wp_enqueue_script('tavus-app', "{$base}/{$entry['file']}", [], null, true);
            add_filter('script_loader_tag', function ($tag, $handle, $src) {
                if ('tavus-app' === $handle) {
                    return '<script type="module" src="' . esc_url($src) . '"></script>';
                }
                return $tag;
            }, 10, 3);
        }
    }
}
