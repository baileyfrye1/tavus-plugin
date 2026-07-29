<?php

if (!defined('ABSPATH')) {
    exit;
}

enum SettingsGroup: string
{
    case General = 'general';
    case Faces = 'faces';
}

class Tavus_Admin
{
    private Tavus_API $api;
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        $this->api = new Tavus_API();
    }

    public function registerSettingsPage()
    {
        add_menu_page(
            'Tavus Settings',
            'Tavus',
            'manage_options',
            'tavus-settings',
            [$this, 'renderPage'],
            'dashicons-admin-users',
            30
        );
    }

    public function renderPage()
    {
        require_once __DIR__ . '/tavus-settings.php';
    }

    public function registerSettings()
    {
        register_setting('tavus_general', 'tavus_general_settings', [$this, 'sanitizeGeneralSettings']);
        register_setting('tavus_faces', 'tavus_face_settings', [$this, 'sanitizeFaceSettings']);

        add_settings_section('tavus_general', 'General', null, 'tavus-general');
        add_settings_field('api_key', 'API Key', [$this, 'renderApiKeyField'], 'tavus-general', 'tavus_general');
        add_settings_field('cache_ttl', 'Cache TTL', [$this, 'renderCacheTTLField'], 'tavus-general', 'tavus_general');

        add_settings_section('tavus_faces', 'Face IDs', null, 'tavus-faces');
        add_settings_field('face_ids_parent', 'Parent/Caregiver', [$this, 'renderParentFacesField'], 'tavus-faces', 'tavus_faces');
        add_settings_field('face_ids_healthcare', 'Healthcare Provider', [$this, 'renderHealthcareFacesField'], 'tavus-faces', 'tavus_faces');
    }

    private function sanitizeGeneralSettings(array $input)
    {
        $clean = [
            'api_key' => '',
            'cache_ttl' => 1
        ];

        if (!empty($input['api_key'])) {
            $clean['api_key'] = sanitize_text_field($input['api_key']);
        }

        $ttl = (int) ($input['cache_ttl'] ?? 1);
        $clean['cache_ttl'] = max(1, min(24, $ttl));

        return $clean;
    }

    private function sanitizeFaceSettings(array $input)
    {
        $clean = ['face_ids' => []];

        foreach (['parent-caregiver', 'healthcare-provider'] as $track) {
            $clean['face_ids'][$track] = [];
            if (isset($input['face_ids'][$track]) && is_array($input['face_ids'][$track])) {
                $clean['face_ids'][$track] = array_values(array_filter(array_map(
                    'sanitize_text_field',
                    $input['face_ids'][$track]
                )));
            }
        }

        return $clean;
    }

    public function renderApiKeyField()
    {
        $settings = $this->getSettings(SettingsGroup::General);
        $apiKey = $settings['api_key'] ?? "";
?>
        <input class="api-key" type="text" name="tavus_general_settings[api_key]" value="<?= esc_attr($apiKey) ?>" />
    <?php }

    public function renderCacheTTLField()
    {
        $settings = $this->getSettings(SettingsGroup::General);
        $cacheTTL = $settings['cache_ttl'] ?? "";
    ?>
        <input class="api-key" type="number" placeholder="Please enter a number" min="1" max="24" name="tavus_general_settings[cache_ttl]" value="<?= esc_attr($cacheTTL) ?>" />
    <?php }

    public function renderParentFacesField()
    {
        $faces = [];

        try {
            $faces = $this->api->fetchFacesByTrack('parent-caregiver');
        } catch (\Exception $e) {
        }
    ?>
        <h3>Add Parent Face ID</h3>

        <div class="add-face">
            <input type="text" />
            <button type="button" class="button button-secondary" data-track="parent-caregiver">Add</button>
        </div>

        <div class="faces-grid">
            <?php foreach ($faces as $face) :
                $faceId = is_array($face) ? $face['face_id'] : $face;
                $faceName = is_array($face) ? ($face['face_name'] ?? $faceId) : $faceId;
                $thumbnailUrl = is_array($face) ? ($face['thumbnail_video_url'] ?? '') : '';
            ?>
                <div class="face-card" data-face-id="<?= $faceId ?>">
                    <input type="hidden" name="tavus_face_settings[face_ids][parent-caregiver][]" value="<?= esc_attr($faceId); ?>" />
                    <?php if ($thumbnailUrl) : ?>
                        <div class="video-wrapper">
                            <video src="<?= esc_url($thumbnailUrl) ?>"></video>
                            <button type="button" class="remove-face">X</button>
                        </div>
                    <?php endif ?>
                    <span class="face-name"><?= esc_html($faceName) ?></span>
                </div>
            <?php endforeach ?>
        </div>
    <?php }

    public function renderHealthcareFacesField()
    {
        $faces = [];

        try {
            $faces = $this->api->fetchFacesByTrack('healthcare-provider');
        } catch (\Exception $e) {
        }
    ?>
        <h3>Add Healthcare Provider Face ID</h3>

        <div class="add-face">
            <input type="text" />
            <button type="button" class="button button-secondary" data-track="healthcare-provider">Add</button>
        </div>

        <div class="faces-grid">
            <?php foreach ($faces as $face) :
                $faceId = is_array($face) ? $face['face_id'] : $face;
                $faceName = is_array($face) ? ($face['face_name'] ?? $faceId) : $faceId;
                $thumbnailUrl = is_array($face) ? ($face['thumbnail_video_url'] ?? '') : '';
            ?>
                <div class="face-card" data-face-id="<?= $faceId ?>">
                    <input type="hidden" name="tavus_face_settings[face_ids][healthcare-provider][]" value="<?= esc_attr($faceId); ?>" />
                    <?php if ($thumbnailUrl) : ?>
                        <div class="video-wrapper">
                            <video src="<?= esc_url($thumbnailUrl) ?>"></video>
                            <button type="button" class="remove-face">X</button>
                        </div>
                    <?php endif ?>
                    <span class="face-name"><?= esc_html($faceName) ?></span>
                </div>
            <?php endforeach ?>
        </div>
<?php }


    private function getSettings(SettingsGroup $group): array
    {
        $default = match ($group) {
            SettingsGroup::General => [
                'api_key'   => defined('TAVUS_API_KEY') ? TAVUS_API_KEY : '',
                'cache_ttl' => 1,
            ],
            SettingsGroup::Faces => ['face_ids'  => TAVUS_DEFAULT_FACE_IDS]
        };

        $option = match ($group) {
            SettingsGroup::General => 'tavus_general_settings',
            SettingsGroup::Faces => 'tavus_face_settings',
        };

        return get_option($option, $default);
    }

    public function enqueueAdminAssets(string $hook)
    {
        if ($hook !== 'toplevel_page_tavus-settings') {
            return;
        }

        wp_enqueue_style('tavus-admin', plugin_dir_url(__FILE__) . 'assets/admin.css');
        wp_enqueue_script('tavus-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', [], false, true);
    }
}

new Tavus_Admin();
