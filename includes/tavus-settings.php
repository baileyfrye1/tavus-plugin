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

    <?php if ($activeTab === 'faces') : ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('tavus_faces');
            do_settings_sections($settingsSection);
            submit_button();
            ?>
        </form>
    <?php elseif ($activeTab === 'tools') : ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('tavus_tools');
            do_settings_sections($settingsSection);
            submit_button();
            ?>
        </form>
    <?php else : ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('tavus_general');
            do_settings_sections($settingsSection);
            submit_button();
            ?>
        </form>
    <?php endif ?>
</div>
