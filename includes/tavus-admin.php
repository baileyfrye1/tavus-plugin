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
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
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
        $settings = $this->getSettings(SettingsGroup::Faces);
        $faceIds = $settings['face_ids']['parent-caregiver'] ?? [];
    ?>
        <h3>Parents/Caregivers</h3>

        <div class="add-face">
            <input type="text" placeholder="Enter Face ID" />
            <button type="button" class="button button-secondary">Add</button>
        </div>

        <div>
            <?php foreach ($faceIds as $faceId) : ?>
                <input type="hidden" name="tavus_face_settings[face_ids][parent-caregiver][]" value="<?php echo esc_attr($faceId); ?>" />
                <span><?= esc_html($faceId) ?></span>
            <?php endforeach ?>
        </div>
    <?php }

    public function renderHealthcareFacesField()
    {
        $settings = $this->getSettings(SettingsGroup::Faces);
        $faceIds = $settings['face_ids']['healthcare-provider'] ?? [];
    ?>
        <h3>Healthcare Providers</h3>

        <div class="add-face">
            <input type="text" placeholder="Enter Face ID" />
            <button type="button" class="button button-secondary">Add</button>
        </div>

        <div>
            <?php foreach ($faceIds as $faceId) : ?>
                <input type="hidden" name="tavus_face_settings[face_ids][healthcare-provider][]" value="<?php echo esc_attr($faceId); ?>" />
                <span><?= esc_html($faceId) ?></span>
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
            SettingsGroup::Faces => [
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
            ]
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

        wp_enqueue_style('tavus-admin', plugin_dir_url(__FILE__) . 'admin.css');
    }
}

new Tavus_Admin();
