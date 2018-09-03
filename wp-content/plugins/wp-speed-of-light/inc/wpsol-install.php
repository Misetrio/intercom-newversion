<?php
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Class WpsolInstall
 */
class WpsolInstall
{
    /**
     * WpsolInstall constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'installRedirects'));
        add_action('admin_init', array($this, 'wpsolFirstInstall'));
        //Update option when update plugin
        add_action('admin_init', array($this, 'wpsolUpdateVersion'));
    }

    /**
     * Redirect when active plugin
     *
     * @return void
     */
    public function installRedirects()
    {
        // Setup/welcome
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            switch ($_GET['page']) {
                // phpcs:enable
                case 'wpsol-wizard':
                    require_once(WPSOL_PLUGIN_DIR . 'inc/install-wizard/install-wizard.php');
                    break;
            }
        }
    }

    /**
     * First install plugin
     *
     * @return void
     */
    public function wpsolFirstInstall()
    {
        if (self::checkNewInstall()) {
            update_option('wpsol_version', WPSOL_VERSION);
            wp_safe_redirect(admin_url('index.php?page=wpsol-wizard'));
        }
    }

    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     * @return void
     */
    public static function pluginActivation()
    {
        WP_Filesystem();
        $opts = get_option('wpsol_optimization_settings');

        if (empty($opts)) {
            $opts['speed_optimization'] = array();
            $opts['advanced_features'] = array();
        }
        $default_opts = array(
            'speed_optimization' => array(
                'act_cache' => 1,
                'add_expires' => 1,
                'clean_cache' => 40,
                'clean_cache_each_params' => 2,
                'devices' => array(
                    'cache_desktop' => 1,
                    'cache_tablet' => 1,
                    'cache_mobile' => 1,
                ),
                'query_strings' => 1,
                'remove_rest_api' => 0,
                'remove_rss_feed' => 0,
                'cache_external_script' => 0,
                'disable_page' => array(),
            ),
            'advanced_features' => array(
                'html_minification' => 0,
                'css_minification' => 0,
                'js_minification' => 0,
                'cssgroup_minification' => 0,
                'jsgroup_minification' => 0
            )
        );

        $opts['speed_optimization'] = array_merge($default_opts['speed_optimization'], $opts['speed_optimization']);
        $opts['advanced_features'] = array_merge($default_opts['advanced_features'], $opts['advanced_features']);

        update_option('wpsol_optimization_settings', $opts);

        //config by default
        $config = get_option('wpsol_configuration');
        if (empty($config)) {
            $config = array(
                'disable_user' => 0,
                'display_clean' => 1,
                'webtest_api_key' => '',
            );
        }
        $config['display_clean'] = 1;
        update_option('wpsol_configuration', $config);

        // default cdn
        $cdn_integration = get_option('wpsol_cdn_integration');
        if (empty($cdn_integration)) {
            $cdn_integration = array(
                'cdn_active' => 0,
                'cdn_url' => '',
                'cdn_content' => array('wp-content', 'wp-includes'),
                'cdn_exclude_content' => array('.php'),
                'cdn_relative_path' => 1,
                'third_parts' => array(),
            );
        }
        $default_cdn = array(
            'cdn_active' => 0,
            'cdn_url' => '',
            'cdn_content' => array('wp-content', 'wp-includes'),
            'cdn_exclude_content' => array('.php'),
            'cdn_relative_path' => 1,
            'third_parts' => array(),
        );
        $cdn_integration = array_merge($default_cdn, $cdn_integration);
        update_option('wpsol_cdn_integration', $cdn_integration);

        //add header to htaccess by default
        WpsolSpeedOptimization::addExpiresHeader(true);
        WpsolSpeedOptimization::addGzipHtacess(true);
        //automatic config start cache
        WpsolCache::factory()->write();
        WpsolCache::factory()->writeConfigCache();

        if (!empty($opts) && !empty($opts['speed_optimization']['act_cache'])) {
            WpsolCache::factory()->toggleCaching(true);
        }

        //display message plugin active
        if (version_compare($GLOBALS['wp_version'], WPSOL_MINIMUM_WP_VERSION, '<')) {
            deactivate_plugins(basename(__FILE__));
            wp_die(
                '<p>The <strong>WP Speed of Light</strong> plugin requires WordPress '.
                esc_html(WPSOL_MINIMUM_WP_VERSION).'or higher.</p>',
                'Plugin Activation Error',
                array('response' => 200, 'back_link' => true)
            );
        }
    }

    /**
     * Removes all connection options
     *
     * @static
     * @return void
     */
    public static function pluginDeactivation()
    {
        WP_Filesystem();
        WpsolCache::factory()->cleanUp();
        WpsolCache::factory()->toggleCaching(false);
        WpsolCache::factory()->cleanConfig();

        WpsolMinificationCache::clearMinification();
        //delete header in htacctess
        WpsolSpeedOptimization::addExpiresHeader(false);
        WpsolSpeedOptimization::addGzipHtacess(false);
    }
    /**
     * Update option when plugin updated
     *
     * @return void
     */
    public function wpsolUpdateVersion()
    {
        global $wpdb;
        $option_ver = 'wpsol_db_version';
        $db_installed = get_option($option_ver, false);
        $opts = get_option('wpsol_optimization_settings');
        $config = get_option('wpsol_configuration');
        $default_opts = array(
            'speed_optimization' => array(
                'act_cache' => 1,
                'add_expires' => 1,
                'clean_cache' => 40,
                'clean_cache_each_params' => 2,
                'devices' => array(
                    'cache_desktop' => 1,
                    'cache_tablet' => 1,
                    'cache_mobile' => 1,
                ),
                'query_strings' => 1,
                'disable_page' => array(),
            ),
            'advanced_features' => array(
                'html_minification' => 0,
                'css_minification' => 0,
                'js_minification' => 0,
                'cssgroup_minification' => 0,
                'jsgroup_minification' => 0
            )
        );

        $default_config = array(
            'disable_user' => 0,
            'display_clean' => 1,
            'webtest_api_key' => ''
        );

        if (!$db_installed) {
            // update option wpsol_optimization_settings
            $this->wpsolUpdateOption($opts, $default_opts);
            update_option($option_ver, '1.3.0');
            $db_installed = '1.3.0';
        }

        if (version_compare($db_installed, '1.4.0', '<')) {
            $default_opts['speed_optimization']['query_strings'] = 1;
            $this->wpsolUpdateOption($opts, $default_opts);
            update_option($option_ver, '1.4.0');
        }

        if (version_compare($db_installed, '1.5.1', '<')) {
            // Move clean cache after from optimization to configuration tab.
            $disable_page = array();
            if (!empty($config['disable_page'])) {
                $disable_page = $config['disable_page'];
            }
            $opts['speed_optimization']['disable_page'] = $disable_page;
            unset($config['disable_page']);
            // Update configuration.
            $opts['speed_optimization']['clean_cache_each_params'] = 2;
            $opts['speed_optimization']['cleanup_on_save'] = 1;
            $opts['advanced_features']['fontgroup_minification'] = 0;
            $opts['advanced_features']['excludefiles_minification'] = 0;
            update_option('wpsol_optimization_settings', $opts);
            update_option('wpsol_configuration', $config);
            update_option($option_ver, '1.5.1');
        }
        if (version_compare($db_installed, '2.0.0', '<')) {
            // default cdn
            $cdn_integration = array(
                'cdn_active' => 0,
                'cdn_url' => '',
                'cdn_content' => array('wp-content', 'wp-includes'),
                'cdn_exclude_content' => array('.php'),
                'cdn_relative_path' => 1,
                'third_parts' => array()
            );
            update_option('wpsol_cdn_integration', $cdn_integration);
            update_option($option_ver, '2.0.0');
        }

        if (version_compare($db_installed, '2.1.0', '<')) {
            // Update configuration
            $opts['speed_optimization']['cache_external_script'] = 0;
            $opts['speed_optimization']['remove_rest_api'] = 0;
            $opts['speed_optimization']['remove_rss_feed'] = 0;
            update_option('wpsol_optimization_settings', $opts);
            update_option($option_ver, '2.1.0');
        }
    }

    /**
     * Update optimization when plugin updated
     *
     * @param array $opts         Current option
     * @param array $default_opts Default option
     *
     * @return boolean
     */
    public static function wpsolUpdateOption($opts, $default_opts)
    {
        if (!empty($opts['speed_optimization']) && !empty($opts['advanced_features'])) {
            $opts['speed_optimization'] = array_merge($default_opts['speed_optimization'], $opts['speed_optimization']);
            $opts['advanced_features'] = array_merge($default_opts['advanced_features'], $opts['advanced_features']);
            update_option('wpsol_optimization_settings', $opts);
            return true;
        }
        return false;
    }

    /**
     * UPdate configuration when update plugin
     *
     * @param array $opts         Current option
     * @param array $default_opts Default option
     *
     * @return boolean
     */
    public static function wpsolUpdateConfiguration($opts, $default_opts)
    {
        if (!empty($opts)) {
            $opts = array_merge($default_opts, $opts);
            update_option('wpsol_configuration', $opts);
            return true;
        }
        return false;
    }

    /**
     * Is this a brand new wpsol install?
     *
     * @return boolean
     */
    private static function checkNewInstall()
    {
        return is_null(get_option('wpsol_version', null)) && is_null(get_option('wpsol_db_version', null));
    }
}
