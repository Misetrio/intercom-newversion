<?php
if (!defined('ABSPATH')) {
    exit;
}
$icon = array(
    'ok' => '<i class="material-icons system-checkbox material-icons-success">check_circle</i>',
    'alert' => '<i class="material-icons system-checkbox material-icons-alert">info</i>',
    'info' => '<img class="system-checkbox material-icons-info bell" src="'.WPSOL_PLUGIN_URL.'css/images/icon-notification.png" />'
);
$apache = array(
    array(
        'id' => 'mod_expires',
        'title' => __('Mod_Expires', 'wp-speed-of-light'),
        'tooltip' => __('Apache module Expires NOT detected. This module is required to setup cache expiration periods for specific files (JS, CSS…) in web browsers. This is definitively recommended for a better performance.', 'wp-speed-of-light')
    ),
    array(
        'id' => 'gzip',
        'title' => __('Gzip Activation', 'wp-speed-of-light'),
        'tooltip' => __('GZIP compression NOT detected. Gzip is zipping pages on a web server before the content is sent to the visitor. This saves bandwidth and increases the loading time significantly', 'wp-speed-of-light')
    ),
    array(
        'id' => 'mod_headers',
        'title' => __('Mod_Headers', 'wp-speed-of-light'),
        'tooltip' => __('Apache module Headers NOT detected. This module helps in giving instruction to the browser whether it should request a specific file from the server or whether they should grab it from the browser\'s cache (it’s faster)', 'wp-speed-of-light')
    ),
    array(
        'id' => 'mod_filter',
        'title' => __('Mod_Filter', 'wp-speed-of-light'),
        'tooltip' => __('Apache module Filter NOT detected. Compression of pages (gzip / deflate) requires Apache modules mod_filter and mod_deflate', 'wp-speed-of-light')
    )
);

$other = array(
    array(
        'id' => 'curl',
        'title' => __('Php_Curl', 'wp-speed-of-light'),
        'tooltip' => __('PHP extension Curl is NOT detected. The cache preloading feature will not work (preload a page cache automatically after a cache purge)', 'wp-speed-of-light')
    ),
    array(
        'id' => 'openssl',
        'title' => __('Php_Openssl', 'wp-speed-of-light'),
        'tooltip' => __('PHP extension Openssl is NOT detected. This extension is used for font minification. There’s chances that it won\'t be possible and produce a PHP errors', 'wp-speed-of-light')
    )
);

$phpInfo = WpsolInstallWizard::parsePhpinfo();
$headerInfo = WpsolInstallWizard::getHeaderInfo();
?>
<form method="post">
    <?php wp_nonce_field('wpsol-setup-wizard', 'wizard_nonce'); ?>
    <div class="environment-wizard-header">
        <div class="title"><?php esc_html_e('Environment Check', 'wp-speed-of-light'); ?></div>
        <p class="text-introduction">
            <?php esc_html_e('We have checked your server environment. 
            If you see some warning below it means that some plugin features may not work properly.
            Reload the page to refresh the results', 'wp-speed-of-light'); ?>
        </p>
    </div>
    <div class="environment-wizard-content">
        <div class="version-container">
            <div class="title"><?php esc_html_e('PHP Version', 'wp-speed-of-light'); ?></div>
            <ul class="field">
                <li class="ju-settings-option full-width">
                    <label class="ju-setting-label">
                        <?php esc_html_e('PHP ', 'wp-speed-of-light'); ?>
                        <?php echo esc_html(PHP_VERSION) ?>
                        <?php esc_html_e('version', 'wp-speed-of-light'); ?>
                    </label>
                    <?php
                    if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                        echo $icon['ok'];
                    } elseif (version_compare(PHP_VERSION, '7.2.0', '<') &&
                              version_compare(PHP_VERSION, '7.0.0', '>=')) {
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                        echo $icon['info'];
                    } else {
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                        echo $icon['alert'];
                    }
                    ?>
                </li>
            </ul>
            <div class="clear"></div>
            <?php if (version_compare(PHP_VERSION, '7.2.0', '<')) : ?>
                <p>
                    <?php esc_html_e('Your PHP version is ', 'wp-speed-of-light'); ?>
                    <?php echo esc_html(PHP_VERSION) ?>
                    <?php esc_html_e('. For performance and security reasons it better to run PHP 7.2+.
                Comparing to previous versions the execution time of PHP 7.X is more than twice as fast and has 30 percent lower memory consumption', 'wp-speed-of-light'); ?>
                </p>
            <?php else : ?>
                <p>
                    <?php esc_html_e('Great ! Your PHP version is ', 'wp-speed-of-light'); ?>
                    <?php echo esc_html(PHP_VERSION) ?>
                </p>
            <?php endif; ?>

        </div>
        <div class="apache-container">
            <div class="title"><?php esc_html_e('Apache Modules', 'wp-speed-of-light'); ?></div>
            <ul class="field">
                <?php foreach ($apache as $v) :
                    $checkapache = false;
                    if (($v['id'] === 'gzip' && $headerInfo['gzip'])) {
                        $checkapache = true;
                    }
                    // Apache modules
                    if (function_exists('apache_get_modules')) {
                        $apacheModules = apache_get_modules();
                        // Check apache modules
                        if (in_array($v['id'], $apacheModules)) {
                            $checkapache = true;
                        }
                    } else {
                        if (isset($phpInfo['apache2handler']['Loaded Modules']) &&
                            strpos($phpInfo['apache2handler']['Loaded Modules'], $v['id']) !== false) {
                            $checkapache = true;
                        }
                    }
                    ?>
                    <li class="ju-settings-option full-width" <?php echo (!$checkapache) ? 'unchecked' : '' ?>>
                        <label for="<?php echo esc_attr($v['id']) ?>" class="ju-setting-label system-check-label">
                            <?php echo esc_html($v['title']) ?>
                        </label>
                        <?php
                        if ($v['id'] === 'mod_expires') {
                            //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                            echo ($checkapache) ? $icon['ok'] : $icon['alert'];
                        } else {
                            //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                            echo ($checkapache) ? $icon['ok'] : $icon['info'];
                        }
                        ?>
                    </li>
                    <?php if (!$checkapache) : ?>
                    <p><?php echo esc_html($v['tooltip']); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="clear"></div>
        </div>

        <div class="other-container">
            <div class="title"><?php esc_html_e('Other Check', 'wp-speed-of-light'); ?></div>
            <ul class="field">
                <?php foreach ($other as $v) :
                    $checkother = false;
                    // Php modules
                    if (function_exists('get_loaded_extensions')) {
                        $phpModules = get_loaded_extensions();
                        if (in_array($v['id'], $phpModules)) {
                            $checkother = true;
                        }
                    } else {
                        if (isset($phpInfo[$v['id']])) {
                            $checkother = true;
                        }
                    }
                    ?>
                    <li class="ju-settings-option full-width">
                        <label for="<?php echo esc_attr($v['id']) ?>" class="ju-setting-label system-check-label"  ">
                            <?php echo esc_html($v['title']) ?>
                        </label>
                        <?php
                        if ($v['id'] === 'curl') {
                            //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                            echo ($checkother) ? $icon['ok'] : $icon['alert'] ;
                        } else {
                            //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon html
                            echo ($checkother) ? $icon['ok'] : $icon['info'] ;
                        }
                        ?>
                    </li>
                    <?php if (!$checkother) : ?>
                    <p><?php echo esc_html($v['tooltip']); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="environment-wizard-footer">
        <input type="submit" value="<?php esc_html_e('Continue', 'wp-speed-of-light'); ?>" class="" name="wpsol_save_step" />
    </div>
</form>
