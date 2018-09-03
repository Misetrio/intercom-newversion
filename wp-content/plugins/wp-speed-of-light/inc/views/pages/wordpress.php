<?php
if (!defined('ABSPATH')) {
    exit;
}
$optimization = get_option('wpsol_optimization_settings');

$disabled_addon_class = 'addon-disabled';
$disabled_addon_attr = 'disabled="disabled"';
$disabled_panel = '';
if (class_exists('WpsolAddonSpeedOptimization')) {
    $disabled_addon_class = '';
    $disabled_addon_attr = '';
    $disabled_panel = 'pannel-addon-enabled';
}

$advanced = get_option('wpsol_advanced_settings');
$remove_emojis_checked = '';
$disable_gravatar_checked = '';
if (!empty($advanced) &&
    isset($advanced['remove_emojis']) &&
    $advanced['remove_emojis'] === 1) {
    $remove_emojis_checked = 'checked="checked"';
}
if (!empty($advanced) &&
    isset($advanced['disable_gravatar']) &&
    $advanced['disable_gravatar'] === 1) {
    $disable_gravatar_checked = 'checked="checked"';
}
?>

<div class="content-wordpress wpsol-optimization">
    <form class="" method="post">
        <input type="hidden" name="action" value="wpsol_save_wordpress">
        <input type="hidden" name="page" value="wordpress" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('WordPress Features', 'wp-speed-of-light')?></label>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'wordpress') :  ?>
            <div id="message-wordpress" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="query-strings" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Remove query strings from static resources like 
                                   CSS & JS files inside the Header to 
                                   improve your scores on services like Pingdom,
                                    GTmetrix, PageSpeed and YSlow', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Remove query strings', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-optimization" id="query-strings"
                                       name="query-strings"
                                       value="1"
                                    <?php
                                    if (!empty($optimization)) {
                                        if ($optimization['speed_optimization']['query_strings'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="remove-rest" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Disable the WordPress REST API (API to retrieve data using 
                                   GET requests, used by developers)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable REST API', 'wp-speed-of-light'); ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-optimization" id="remove-rest"
                                       name="remove_rest_api"
                                       value="1"
                                    <?php
                                    if (!empty($optimization)) {
                                        if ($optimization['speed_optimization']['remove_rest_api'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    } ?>
                                />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="remove-rss" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Disable the WordPress RSS feed. RSS feeds allow users to 
                                   subscribe to your blog posts using an RSS feed reader', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable RSS feed', 'wp-speed-of-light'); ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-optimization" id="remove-rss"
                                       name="remove_rss_feed"
                                       value="1"
                                    <?php
                                    if (!empty($optimization)) {
                                        if ($optimization['speed_optimization']['remove_rss_feed'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    } ?>
                                />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="remove-emojis" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('By default emojis load on every single page of your site.
               If you don’t use them, this is simply unnecessary', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Remove Emojis', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="remove-emojis" name="remove-emojis"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                <?php echo esc_attr($remove_emojis_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="disable-gravatar" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Disable Gravatar avatars and use only local avatars
               from your media', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Disable Gravatars', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="disable-gravatar" name="disable-gravatar"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                <?php echo esc_attr($disable_gravatar_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer">
            <button type="submit"
                   class="ju-button orange-button waves-effect waves-light" id="speed-optimization">
            <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>
    </form>
</div>
