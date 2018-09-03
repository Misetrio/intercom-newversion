<?php

/**
 * Plugin Name: WP Speed of Light
 * Plugin URI: https://www.joomunited.com/wordpress-products/wp-speed-of-light
 * Description: WP Speed of Light is used to speed up your WP site. It will approach the speed of light
 * Version: 2.3.2
 * Text Domain: wp-speed-of-light
 * Domain Path: /languages
 * Author: JoomUnited
 * Author URI: https://www.joomunited.com
 * License: GPL2
 */
/*
 * @copyright 2014  Joomunited  ( email : contact _at_ joomunited.com )
 *
 *  Original development of this plugin was kindly funded by Joomunited
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined('ABSPATH') || die('No direct script access allowed!');
if (!defined('WPSOL_PLUGIN_NAME')) {
    define('WPSOL_PLUGIN_NAME', plugin_basename(__FILE__));
}
if (!defined('WPSOL_MINIMUM_WP_VERSION')) {
    define('WPSOL_MINIMUM_WP_VERSION', '4.0');
}
if (!defined('WPSOL_PLUGIN_URL')) {
    define('WPSOL_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('WPSOL_PLUGIN_DIR')) {
    define('WPSOL_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('WPSOL_SITEURL')) {
    define('WPSOL_SITEURL', get_site_url());
}
if (!defined('WPSOL_VERSION')) {
    define('WPSOL_VERSION', '2.3.2');
}
if (!defined('WPSOL_FILE')) {
    define('WPSOL_FILE', __FILE__);
}
if (!defined('WPSOL_MINIFICATION_CACHE')) {
    define('WPSOL_MINIFICATION_CACHE', WP_CONTENT_DIR . '/cache/wpsol-minification/');
}
if (!defined('WPSOL_CACHE_CHILD_DIR')) {
    define('WPSOL_CACHE_CHILD_DIR', '/cache/wpsol-minification/');
}
if (!defined('WPSOL_CACHEFILE_PREFIX')) {
    define('WPSOL_CACHEFILE_PREFIX', 'wpsol_');
}
define('WPSOL_CACHE_DELAY', true);
if (!defined('WPSOL_WP_CONTENT_NAME')) {
    define('WPSOL_WP_CONTENT_NAME', '/' . wp_basename(WP_CONTENT_DIR));
}

define('WPSOL_CACHE_NOGZIP', true);
define('WPSOL_ROOT_DIR', str_replace(WPSOL_WP_CONTENT_NAME, '', WP_CONTENT_DIR));

// Check jurequiment
if (is_admin()) {
    //Check plugin requirements
    if (version_compare(PHP_VERSION, '5.3', '<')) {
        if (!function_exists('wpsol_disable_plugin')) {
            /**
             * Plugin disable
             *
             * @return void
             */
            function wpsol_disable_plugin()
            {
                if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                    deactivate_plugins(__FILE__);
                    unset($_GET['activate']);
                }
            }
        }

        if (!function_exists('wpsol_show_error')) {
            /**
             * Show error when install
             *
             * @return void
             */
            function wpsol_show_error()
            {
                echo '<div class="error"><p><strong>WP Speed Of Light</strong>
                    need at least PHP 5.3 version, please update php before installing the plugin.</p>
                    </div>';
            }
        }

        //Add actions
        add_action('admin_init', 'wpsol_disable_plugin');
        add_action('admin_notices', 'wpsol_show_error');

        //Do not load anything more
        return;
    }

    if (!class_exists('\Joomunited\WPSOL\JUCheckRequirements')) {
        require_once(WPSOL_PLUGIN_DIR . 'requirements.php');
    }

    if (class_exists('\Joomunited\WPSOL\JUCheckRequirements')) {
        // Plugins name for translate
        $args = array(
            'plugin_name' => esc_html__('WP Speed Of Light', 'wp-speed-of-light'),
            'plugin_path' => 'wp-speed-of-light/wp-speed-of-light.php',
            'plugin_textdomain' => 'wp-speed-of-light',
            'requirements' => array(
                'php_version' => '5.3',
                // Minimum addons version
                'addons_version' => array(
                    'wpsolAddons' => '2.3.0'
                )
            ),
        );
        $wpsolCheck = call_user_func('\Joomunited\WPSOL\JUCheckRequirements::init', $args);

        if (!$wpsolCheck['success']) {
            // Do not load anything more
            unset($_GET['activate']);
            return;
        }
    }
}

// analysis queries time
if (function_exists('get_option') &&
    !isset($GLOBALS['WpSoL_DB_Queries']) &&
    basename(__FILE__) !== basename($_SERVER['SCRIPT_FILENAME'])) {
    $opts = get_option('wpsol_profiles_option');
    if (!empty($opts['query_enabled'])) {
        $file = WPSOL_PLUGIN_DIR . 'inc/wpsol-scan-queries.php';
        if (!file_exists($file)) {
            return;
        }
        include_once $file;
        if (class_exists('WpsolDBQueries')) {
            $GLOBALS['WpSoL_DB_Queries'] = new WpsolDBQueries(); // Go
        }
    }

    unset($opts);
}

// load speed analysis file
require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-speed-optimization.php');
require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-cache.php');
require_once(WPSOL_PLUGIN_DIR . 'inc/caches/clean-cache-time.php');
// load minification cache file
require_once(WPSOL_PLUGIN_DIR . 'inc/minifications/wpsol-minification-cache.php');
require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-configuration.php');

$config = new WpsolConfiguration();

// CDN
require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-cdn-integration.php');
require_once(WPSOL_PLUGIN_DIR . 'inc/cdn-integration/cdn-rewrite.php');
new WpsolCdnIntegration();

// Rest api
require_once(WPSOL_PLUGIN_DIR . 'inc/communicate/rest-api.php');
new WpsolRestApi();

// Rss feed
require_once(WPSOL_PLUGIN_DIR . 'inc/communicate/rss-feed.php');
new WpsolRssFeed();

if (is_admin()) {
    //Include the jutranslation helpers
    include_once('jutranslation' . DIRECTORY_SEPARATOR . 'jutranslation.php');
    call_user_func(
        '\Joomunited\WPSOL\Jutranslation\Jutranslation::init',
        __FILE__,
        'wpsol',
        'WP Speed Of Light',
        'wp-speed-of-light',
        'languages' . DIRECTORY_SEPARATOR . 'wp-speed-of-light-en_US.mo'
    );

    register_activation_hook(__FILE__, array('WpsolInstall', 'pluginActivation'));
    register_deactivation_hook(__FILE__, array('WpsolInstall', 'pluginDeactivation'));

    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-install.php');
    new WpsolInstall();

    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-admin.php');
    new WpsolAdmin();
    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-speed-analysis.php');
    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-database-cleanup.php');
    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-dashboard.php');

    //cache when ecommerce installed
    require_once(WPSOL_PLUGIN_DIR . 'inc/caches/ecommerce-cache.php');
    new WpsolEcommerceCache();

    require_once(WPSOL_PLUGIN_DIR . 'inc/wpsol-import-export.php');
    $config = new WpsolImportExport();
} else {
    $domain = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $current_url = $domain . $_SERVER['REQUEST_URI'];
    $opts_config = get_option('wpsol_optimization_settings');
    $exclude_url = array();
    if (isset($opts_config['speed_optimization']['disable_page'])) {
        $exclude_url = $opts_config['speed_optimization']['disable_page'];
    }
    $check_exclude = check_exclude_url($exclude_url, $current_url);

    // Compare current url with rules and exclude urls
    if (!$check_exclude) {
        $check_admin = $config->checkAdminRole();

        /**
         * Check user roles to exclude
         *
         * @param boolean Default value
         *
         * @internal
         *
         * @return boolean
         */
        $check_user_roles = apply_filters('wpsol_addon_check_user_roles', false);

        // Disable optimize and cache for admin user
        if (!$check_admin || !$check_user_roles) {
            //cache minification
            if (WpsolMinificationCache::createCacheMinificationFolder()) {
                $conf = get_option('wpsol_optimization_settings');
                if (!empty($conf['advanced_features']['html_minification']) ||
                    !empty($conf['advanced_features']['css_minification']) ||
                    !empty($conf['advanced_features']['js_minification']) ||
                    !empty($conf['advanced_features']['cssgroup_minification']) ||
                    !empty($conf['advanced_features']['jsgroup_minification']) ||
                    !empty($conf['speed_optimization']['cache_external_script'])
                ) {
                    if (defined('WPSOL_INIT_EARLIER')) {
                        add_action('init', 'wpsol_start_buffering', -1);
                    } else {
                        add_action('template_redirect', 'wpsol_start_buffering', 2);
                    }
                }
            }
        }
    }

    // Call back ob start
    ob_start('wpsol_ob_start_callback');
}

WpsolSpeedOptimization::factory();
WpsolCleanCacheTime::factory();

/**
 * Call back ob start - stack
 *
 * @param string $buffer Content of page
 *
 * @return mixed|void
 */
function wpsol_ob_start_callback($buffer)
{
    $conf = get_option('wpsol_optimization_settings');
    $cdn_settings = get_option('wpsol_cdn_integration');

    /**
     * Filter get buffer from minify
     *
     * @param string Content page
     *
     * @internal
     *
     * @return string
     */
    $buffer = apply_filters('wpsol_minify_content_return', $buffer);


    if (!empty($cdn_settings) && !empty($cdn_settings['cdn_active'])) {
        /**
         * Filter get buffer after replace cdn content
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_cdn_content_return', $buffer);
    }

    if (!empty($conf['speed_optimization']['query_strings'])) {
        /**
         * Filter get buffer after remove query strings
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_query_strings_return', $buffer);
    }

    // Add lazy-loading for content
    $domain = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $current_url = $domain . $_SERVER['REQUEST_URI'];
    $advanced_option = get_option('wpsol_advanced_settings');
    $exclude_url = array();
    if (isset($advanced_option['exclude_lazy_loading'])) {
        $exclude_url = $advanced_option['exclude_lazy_loading'];
    }
    $check_exclude = check_exclude_url($exclude_url, $current_url);

    if (!$check_exclude) {
         /**
         * Filter add lazy-loading for content
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_addon_image_lazy_loading', $buffer);
    }
    // Return content
    return $buffer;
}


/**
 * Check url to exclude minify
 *
 * @param array  $exclude_url Url to exclude
 * @param string $current_url Url to check exclude
 *
 * @return boolean
 */
function check_exclude_url($exclude_url, $current_url)
{
    //check disable for page
    if (!empty($exclude_url)) {
        foreach ($exclude_url as $v) {
            if (empty($v)) {
                continue;
            }
            // Clear blank character
            $v = trim($v);
            if (preg_match('/(\/?\&?\(\.?\*\)|\/\*|\*)$/', $v, $matches)) {
                // End of rules is /*, /(*) , /(.*)
                $pattent = substr($v, 0, strpos($v, $matches[0]));
                if ($v[0] === '/') {
                    // A path of exclude url with regex
                    if ((preg_match('@' . $pattent . '@', $current_url, $matches) > 0)) {
                        return true;
                    }
                } else {
                    // Full exclude url with regex
                    if (strpos($current_url, $pattent) !== false) {
                        return true;
                    }
                }
            } else {
                if ($v[0] === '/') {
                    // A path of exclude
                    if ((preg_match('@' . $v . '@', $current_url, $matches) > 0)) {
                        return true;
                    }
                } else {
                    // Whole path
                    if ($v === $current_url) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}

/**
 *  Start loading
 *
 * @return void
 */
function wpsol_start_buffering()
{
    $ao_noptimize = false;
    // check for DONOTMINIFY constant as used by e.g. WooCommerce POS
    if (defined('DONOTMINIFY') && (constant('DONOTMINIFY') === true || constant('DONOTMINIFY') === 'true')) {
        $ao_noptimize = true;
    }

    if (!is_feed() && !$ao_noptimize && !is_admin()) {
        // Config element
        $conf = get_option('wpsol_optimization_settings');
        // Load our base class
        include_once(WPSOL_PLUGIN_DIR . 'inc/minifications/wpsol-minification-base.php');

        // Load extra classes and set some vars
        if (!empty($conf['advanced_features']['html_minification'])) {
            include_once(WPSOL_PLUGIN_DIR . 'inc/minifications/wpsol-minification-html.php');
            // BUG: new minify-html does not support keeping HTML comments, skipping for now
            if (!class_exists('Minify_HTML')) {
                include(WPSOL_PLUGIN_DIR . 'inc/minifications/minify/minify-html.php');
            }
        }

        if (!empty($conf['advanced_features']['js_minification']) ||
            !empty($conf['advanced_features']['jsgroup_minification']) ||
            !empty($conf['speed_optimization']['cache_external_script'])
        ) {
            include_once(WPSOL_PLUGIN_DIR . 'inc/minifications/wpsol-minification-scripts.php');
            if (!class_exists('JSMin')) {
                if (defined('WPSOL_LEGACY_MINIFIERS')) {
                    include(WPSOL_PLUGIN_DIR . 'inc/minifications/minify/jsmin-1.1.1.php');
                } else {
                    include(WPSOL_PLUGIN_DIR . 'inc/minifications/minify/minify-2.1.7-jsmin.php');
                }
            }
            if (!defined('CONCATENATE_SCRIPTS')) {
                define('CONCATENATE_SCRIPTS', false);
            }
            if (!defined('COMPRESS_SCRIPTS')) {
                define('COMPRESS_SCRIPTS', false);
            }
        }
        if (!empty($conf['advanced_features']['css_minification']) ||
            !empty($conf['advanced_features']['cssgroup_minification'])) {
            include_once(WPSOL_PLUGIN_DIR . 'inc/minifications/wpsol-minification-styles.php');
            if (defined('WPSOL_LEGACY_MINIFIERS')) {
                if (!class_exists('Minify_CSS_Compressor')) {
                    include(WPSOL_PLUGIN_DIR . 'inc/minifications/minify/minify-css-compressor.php');
                }
            } else {
                if (!class_exists('CSSmin')) {
                    include(WPSOL_PLUGIN_DIR . 'inc/minifications/minify/yui-php-cssmin-2.4.8-4_fgo.php');
                }
            }
            if (!defined('COMPRESS_CSS')) {
                define('COMPRESS_CSS', false);
            }
        }
        // Now, start the real thing!

        add_filter('wpsol_minify_content_return', 'wpsol_end_buffering');
    }
}

/**
 * Cache css , js and optimize html when start
 *
 * @param string $content Content of page.
 *
 * @return mixed|void
 */
function wpsol_end_buffering($content)
{
    if (stripos($content, '<html') === false ||
        stripos($content, '<html amp') !== false ||
        stripos($content, '<html ⚡') !== false ||
        stripos($content, '<xsl:stylesheet') !== false) {
        return $content;
    }
    // load URL constants as late as possible to allow domain mapper to kick in
    if (function_exists('domain_mapping_siteurl')) {
        define('WPSOL_WP_SITE_URL', domain_mapping_siteurl(get_current_blog_id()));
        define(
            'WPSOL_WP_CONTENT_URL',
            str_replace(get_original_url(WPSOL_WP_SITE_URL), WPSOL_WP_SITE_URL, content_url())
        );
    } else {
        define('WPSOL_WP_SITE_URL', site_url());
        define('WPSOL_WP_CONTENT_URL', content_url());
    }
    if (is_multisite()) {
        $blog_id = get_current_blog_id();
        define('WPSOL_CACHE_URL', WPSOL_WP_CONTENT_URL . WPSOL_CACHE_CHILD_DIR . $blog_id . '/');
    } else {
        define('WPSOL_CACHE_URL', WPSOL_WP_CONTENT_URL . WPSOL_CACHE_CHILD_DIR);
    }
    define('WPSOL_WP_ROOT_URL', str_replace(WPSOL_WP_CONTENT_NAME, '', WPSOL_WP_CONTENT_URL));

    define('WPSOL_HASH', wp_hash(WPSOL_CACHE_URL));
    // Config element
    $conf = get_option('wpsol_optimization_settings');

    // Choose the classes
    $classes = array();
    $groupcss = false;
    $groupjs = false;
    $minifyHtml = false;
    $minifyCss = false;
    $minifyJs = false;
    $groupfonts = false;
    $cache_external = false;
    $exclude_js = array();
    $exclude_css = array();
    $excludeInlineScript = false;
    $moveToFooter = false;
    $excludeScriptMoveToFooter = array();
    if (!empty($conf['advanced_features']['js_minification']) ||
        !empty($conf['advanced_features']['jsgroup_minification']) ||
        !empty($conf['speed_optimization']['cache_external_script'])
    ) {
        $classes[] = 'WpsolMinificationScripts';
    }
    if (!empty($conf['advanced_features']['css_minification']) ||
        !empty($conf['advanced_features']['cssgroup_minification'])) {
        $classes[] = 'WpsolMinificationStyles';
    }
    if (!empty($conf['advanced_features']['html_minification'])) {
        $classes[] = 'WpsolMinificationHtml';
    }
    if (!empty($conf['advanced_features']['html_minification'])) {
        $minifyHtml = true;
    }
    if (!empty($conf['advanced_features']['css_minification'])) {
        $minifyCss = true;
    }
    if (!empty($conf['advanced_features']['js_minification'])) {
        $minifyJs = true;
        /**
         * Filter inline script to exclude from minify.
         *
         * @param boolean Default value
         *
         * @internal
         *
         * @return boolean
         */
        $excludeInlineScript = apply_filters('wpsol_addon_check_exclude_inline_script', false);
        /**
         * Filter parameter to check move minified file to footer.
         *
         * @param boolean Default value
         *
         * @internal
         *
         * @return boolean
         */
        $moveToFooter = apply_filters('wpsol_addon_check_move_script_to_footer', false);
        /**
         * Filter parameter to check exclude script move to footer.
         *
         * @param array Default value
         *
         * @internal
         *
         * @return array
         */
        $excludeScriptMoveToFooter = apply_filters('wpsol_addon_get_exclude_script_move_to_footer', array());
    }
    if (!empty($conf['advanced_features']['cssgroup_minification'])) {
        $groupcss = true;
    }
    if (!empty($conf['advanced_features']['jsgroup_minification'])) {
        $groupjs = true;
    }
    if (!empty($conf['speed_optimization']['cache_external_script'])) {
        $cache_external = true;
    }
    if (class_exists('WpsolAddonSpeedOptimization')) {
        $exclude = get_option('wpsol_addon_exclude_file_lists');
        if (!empty($conf['advanced_features']['excludefiles_minification'])) {
            $exclude_js = $exclude['js-exclude'];
            $exclude_css = $exclude['css-font-exclude'];
        }
        /**
         * Filter to check google font configuration.
         *
         * @param array List of configuration
         *
         * @internal
         *
         * @return array
         */
        $groupfonts = apply_filters('wpsol_addon_check_group_google_fonts', $conf);
    }
    // Set some options
    $classoptions = array(
        'WpsolMinificationScripts' => array(
            'justhead' => false,
            'forcehead' => false,
            'trycatch' => false,
            'js_exclude' => 's_sid, smowtion_size, sc_project, WAU_, 
            wau_add, comment-form-quicktags, edToolbar, ch_client, seal.js',
            'minify_js' => $minifyJs,
            'group_js' => $groupjs,
            'exclude_js' => $exclude_js,
            'cache_external' => $cache_external,
            'exclude_inline' => $excludeInlineScript,
            'move_to_script' => $moveToFooter,
            'exclude_move_to_script' => $excludeScriptMoveToFooter
        ),
        'WpsolMinificationStyles' => array(
            'minifyCSS' => $minifyCss,
            'justhead' => false,
            'datauris' => false,
            'defer' => false,
            'defer_inline' => false,
            'inline' => false,
            'css_exclude' => 'admin-bar.min.css, dashicons.min.css',
            'cdn_url' => '',
            'include_inline' => true,
            'groupcss' => $groupcss,
            'groupfonts' => $groupfonts,
            'exclude_css' => $exclude_css
        ),
        'WpsolMinificationHtml' => array(
            'minifyHTML' => $minifyHtml,
            'keepcomments' => false
        )
    );

    // Run the classes
    foreach ($classes as $name) {
        $instance = new $name($content);

        if ($instance->read($classoptions[$name])) {
            if (!empty($conf['advanced_features']['js_minification']) ||
                !empty($conf['advanced_features']['jsgroup_minification']) ||
                !empty($conf['advanced_features']['css_minification']) ||
                !empty($conf['advanced_features']['cssgroup_minification'])
            ) {
                $instance->minify();
            }
            $instance->cache();
            $content = $instance->getcontent();
        }
        unset($instance);
    }

    return $content;
}


// Load Addons
if (isset($wpsolCheck) && !empty($wpsolCheck['load'])) {
    foreach ($wpsolCheck['load'] as $addonName) {
        if (function_exists($addonName . 'Init')) {
            call_user_func($addonName . 'Init');
        }
    }
}
