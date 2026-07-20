<?php

if (!defined('ABSPATH')) {
    exit;
}


$tabs = [
    'general' => 'General',
    'faces' => 'Faces',
    'tools' => 'Tools',
];

$activeTab = $_GET['tab'] ?? "";

$settingsSection = match ($activeTab) {
    'faces' => 'tavus-faces',
    'tools' => 'tavus-tools',
    default => 'tavus-general'
};

?>

<div class="tavus-admin-wrapper">
    <h1>Tavus Settings</h1>
    <nav class="nav-tab-wrapper">
        <?php foreach ($tabs as $key => $label) : ?>
            <a href="?page=tavus-settings&tab=<?= esc_attr($key) ?>" class="nav-tab <?= $activeTab === $key ? 'nav-tab-active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach ?>
    </nav>

    <form action="options.php" method="post">
        <?php
        settings_fields('tavus_settings');
        do_settings_sections($settingsSection);
        submit_button();
        ?>
    </form>
</div>
