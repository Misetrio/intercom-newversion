<?php
if (!defined('ABSPATH')) {
    exit;
}
$default_speed_optimization = array(
    'speedup' => array(
        'nav_name' => __('SpeedUp', 'wp-speed-of-light'),
        'content' => 'speed-up',
        'icon' => 'insert_chart'
    ),
    'wordpress' => array(
        'nav_name' => __('WordPress', 'wp-speed-of-light'),
        'content' => 'wordpress',
        'icon' => ''
    ),
    'group_and_minify' => array(
        'nav_name' => __('Group & Minify', 'wp-speed-of-light'),
        'content' => 'group-and-minify',
        'icon' => 'folder'
    ),
    'advanced' => array(
        'nav_name' => __('Advanced', 'wp-speed-of-light'),
        'content' => 'advanced',
        'icon' => 'build'
    ),
    'image_compression' => array(
        'nav_name' => __('Image Compression', 'wp-speed-of-light'),
        'content' => 'image-compression',
        'icon' => 'compare'
    ),
    'database_cleanup' => array(
        'nav_name' => __('Database Cleanup', 'wp-speed-of-light'),
        'content' => 'database-cleanup',
        'icon' => 'layers'
    ),
    'cdn' => array(
        'nav_name' => __('Cdn Integration', 'wp-speed-of-light'),
        'content' => 'cdn',
        'icon' => 'public'
    ),
    'configuration' => array(
        'nav_name' => __('Configuration', 'wp-speed-of-light'),
        'content' => 'configuration',
        'icon' => 'settings'
    ),
    'system_check' => array(
        'nav_name' => __('System Check', 'wp-speed-of-light'),
        'content' => 'system-check',
        'icon' => 'verified_user'
    )
);

$systemCheck = WpsolSpeedOptimization::systemCheck();
?>

<div class="ju-main-wrapper">
    <div class="ju-left-panel">
        <div class="ju-logo">
            <a href="https://www.joomunited.com/" target="_blank">
                <img src="<?php echo esc_url(WPSOL_PLUGIN_URL . 'css/images/JoomUnited-logo.png'); ?>"
                     srcset="<?php echo esc_url(WPSOL_PLUGIN_URL . 'css/images/JoomUnited-logo.png'); ?>"
                     alt="<?php esc_html_e('JoomUnited logo', 'wp-speed-of-light') ?>">
            </a>
        </div>
        <div class="ju-menu-search">
            <i class="material-icons mi mi-search ju-menu-search-icon">search</i>
            <input type="text" class="ju-menu-search-input" style="margin: 0" size="16"
                   placeholder="<?php esc_html_e('Search settings', 'wp-speed-of-light') ?>">
        </div>
        <ul class="tabs ju-menu-tabs">
            <?php foreach ($default_speed_optimization as $k => $v) : ?>
                <li class="tab" data-tab-title="<?php echo esc_attr($v['nav_name']) ?>">
                    <a href="#<?php echo esc_attr($k); ?>" class="link-tab white-text waves-effect waves-light">
                        <?php if ($k === 'wordpress') { ?>
                            <span class="dashicons dashicons-wordpress wpsol-icon-dash mi menu-tab-icon" style="margin-right: 3px;"></span>
                        <?php } else { ?>
                            <i class="material-icons mi wpsol-icon-menu menu-tab-icon"><?php echo esc_html($v['icon']); ?></i>
                        <?php } ?>
                        <span class="name tab-title""><?php echo esc_html($v['nav_name']); ?></span>
                    </a>
                    <?php if ($k === 'system_check') :?>
                        <?php if ($systemCheck) : ?>
                            <i class="material-icons system-checkbox material-icons-menu-alert">info</i>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="ju-right-panel">
        <?php foreach ($default_speed_optimization as $k => $v) :?>
            <div class="ju-content-wrapper" id="<?php echo esc_attr($k); ?>" style="display: none">
                <?php include_once(WPSOL_PLUGIN_DIR . 'inc/views/pages/'.$v['content'].'.php'); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clear"></div>
</div>
