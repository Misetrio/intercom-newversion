<?php
if (!defined('ABSPATH')) {
    exit;
}
$advanced = get_option('wpsol_advanced_settings');

$checked = '';
$prefetching_checked = '';
$lazy_loading_checked = '';
$output = '';
$prefetching_domains = '';
if (!empty($advanced) &&
    isset($advanced['cache_preload']) &&
    $advanced['cache_preload'] === 1) {
    $checked = 'checked="checked"';
}
if (!empty($advanced) &&
    isset($advanced['dns_prefetching']) &&
    $advanced['dns_prefetching'] === 1) {
    $prefetching_checked = 'checked="checked"';
}

if (!empty($advanced['preload_url'])) {
    $output = implode("\n", $advanced['preload_url']);
}
if (!empty($advanced['prefetching_domain'])) {
    $prefetching_domains = implode("\n", $advanced['prefetching_domain']);
}
?>
<div class="content-advanced wpsol-optimization">
    <form method="post">
        <input type="hidden" name="action" value="wpsol_save_advanced">
        <input type="hidden" name="page" value="advanced" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title" style="height: 115px !important;">
            <label><?php esc_html_e('Advanced', 'wp-speed-of-light')?></label>
            <div class="text-intro">
                <blockquote>
                    <?php esc_html_e('This is advanced optimization 
                                featured for advanced users ', 'wp-speed-of-light') ?>
                        <a href="https://www.joomunited.com/documentation/wp-speed-of-light-documentation#toc-4-4-preloading-and-prefetching-2"
                           target="_blank"><?php esc_html_e('CHECK DOCUMENTATION', 'wp-speed-of-light') ?></a>
                    </blockquote>
            </div>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'advanced') :  ?>
            <div id="message-advanced" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="cache-preload" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('After being cleared, the cache will be automatically preloaded based
              the URL list below (require cURL extension to work, usually installed)', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Cache preloading', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="cache-preload" name="cache-preload"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                <?php echo esc_attr($checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="preload-url" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add the URL of the pages you want to
               cache preloading (one URL per line)', 'wp-speed-of-light') ?>">
                    <?php esc_html_e('Preload the following pages : ', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <p><textarea cols="100" rows="7" id="preload-url" class="wpsol-minification" name="preload-url"
                                <?php echo esc_attr($disabled_addon_attr) ?>
                            ><?php echo esc_textarea($output) ?></textarea></p>
                    </li>
                    </ul>
                </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="dns-prefetching" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('DNS (Domain Name System) Prefetching is method of informing the browser of domain names
                referenced on a site so that the client can resolve the DNS for those hosts,
                cache them, and when it comes time to use them, have a faster page loading time', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('DNS Pre-fetching', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="dns-prefetching" name="dns-prefetching"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                <?php echo esc_attr($prefetching_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="prefetching-domain" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add the domains you want to prefetching (one URL per line)', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Prefetching domains', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <p><textarea cols="100" rows="7" id="prefetching-domain" class="wpsol-minification"
                                <?php echo esc_attr($disabled_addon_attr) ?>
                                     name="prefetching-domain" ><?php echo esc_textarea($prefetching_domains) ?></textarea></p>
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
