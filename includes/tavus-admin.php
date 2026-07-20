<?php

if (!defined('ABSPATH')) {
    exit;
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
        register_setting('tavus_settings', 'tavus_settings', [$this, 'sanitizeSettings']);

        add_settings_section('tavus_general', 'General', null, 'tavus-general');
        add_settings_field('api_key', 'API Key', [$this, 'renderApiKeyField'], 'tavus-general', 'tavus_general');

        add_settings_section('tavus_faces', 'Face IDs', null, 'tavus-faces');
        add_settings_field('face_ids_parent', 'Parent/Caregiver', [$this, 'renderParentFacesField'], 'tavus-faces', 'tavus_faces');
        add_settings_field('face_ids_healthcare', 'Healthcare Provider', [$this, 'renderHealthcareFacesField'], 'tavus-faces', 'tavus_faces');

        add_settings_section('tavus_tools', 'Tools', null, 'tavus-tools');
    }

    private function sanitizeSettings()
    {
        $testVar = "";
    }

    public function renderApiKeyField()
    {
        $settings = $this->getSettings();
        $apiKey = $settings['api_key'] ?? "";
?>
        <label for="api-key">Api Key:</label>
        <input type="text" name="tavus_settings[api_key]" value="<?= esc_attr($apiKey) ?>" />
    <?php }

    public function renderParentFacesField()
    {
        $settings = $this->getSettings();
        $faceIds = $settings['face_ids']['parent-caregiver'] ?? [];
    ?>
        <h3>Parents/Caregivers</h3>

        <div class="add-face">
            <input type="text" placeholder="Enter Face ID" />
            <button type="button" class="button button-secondary">Add</button>
        </div>

        <div>
            <?php foreach ($faceIds as $faceId) : ?>
                <input type="hidden" name="tavus_settings[face_ids][parent-caregiver][]" value="<?php echo esc_attr($faceId); ?>" />
                <span><?= esc_html($faceId) ?></span>
            <?php endforeach ?>
        </div>
    <?php }

    public function renderHealthcareFacesField()
    {
        $settings = $this->getSettings();
        $faceIds = $settings['face_ids']['healthcare-provider'] ?? [];
    ?>
        <h3>Healthcare Providers</h3>

        <div class="add-face">
            <input type="text" placeholder="Enter Face ID" />
            <button type="button" class="button button-secondary">Add</button>
        </div>

        <div>
            <?php foreach ($faceIds as $faceId) : ?>
                <input type="hidden" name="tavus_settings[face_ids][parent-caregiver][]" value="<?php echo esc_attr($faceId); ?>" />
                <span><?= esc_html($faceId) ?></span>
            <?php endforeach ?>
        </div>
<?php }

    private function getSettings(): array
    {
        $settings = [
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
        ];

        return get_option('tavus_settings', $settings);
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
