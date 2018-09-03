<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once(ABSPATH . 'wp-includes/pluggable.php');
/**
 * Class WpsolConfiguration
 */
class WpsolConfiguration
{
    /**
     * WpsolConfiguration constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'loadScriptsAll'));
        add_action('wp_head', array($this, 'defineAjaxurl'));
        $opts = get_option('wpsol_configuration');
        if (!empty($opts) && $opts['display_clean'] === 1) {
            add_action('admin_bar_menu', array($this, 'actionAdminBarMenu'), 999);
        }
    }

    /**
     *  Load script to all back end
     *
     * @return void
     */
    public function loadScriptsAll()
    {
        if (current_user_can('manage_options')) {
            wp_register_style(
                'style-light-speed',
                plugins_url('css/style.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );
            wp_enqueue_style('style-light-speed');
            wp_enqueue_script(
                'wpsol-scripts-speed',
                plugins_url('js/wpsol-scripts.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
        }
    }

    /**
     * Define ajaxurl
     *
     * @return void
     */
    public function defineAjaxurl()
    {
        if (current_user_can('manage_options')) {
            echo '<script type="text/javascript">
           var ajaxurl = "' . esc_url(admin_url('admin-ajax.php')) . '";
             </script>';
        }
    }

    /**
     * Create menu bar
     *
     * @param WP_Admin_Bar $wp_admin_bar Wp admin bar
     *
     * @return void
     */
    public function actionAdminBarMenu(WP_Admin_Bar $wp_admin_bar)
    {
        if (current_user_can('manage_options')) {
            $title = __('Clean Cache', 'wp-speed-of-light');

            $wp_admin_bar->add_menu(array(
                'id' => 'wpsol-clean-cache-topbar',
                'title' => '<span class="ab-icon"></span>
                   <span class="wpsol-ab-images"">
                   <img style="padding-bottom: 15px;" src="'.
                    WPSOL_PLUGIN_URL.'css/images/Spinner.gif'.
                    '" />
                   </span>
                   <span class="ab-label">' . esc_html($title) . '</span>',
                'href' => '#',
                'meta' => array(
                    'classname' => 'wpsol-cache',
                ),
            ));
        }
    }

    /**
     * Ajax to clean cache
     *
     * @return void
     */
    public static function ajaxCleanCache()
    {
        $size_cache = 0;
        $size_css_cache = 0;
        $size_js_cache = 0;
        $result = array();

        $config = get_option('wpsol_optimization_settings');
        // analysis size cache
        $cachepath = rtrim(WP_CONTENT_DIR, '/') . '/cache/wpsol-cache';
        if (is_dir($cachepath)) {
            $cachedirs = scandir($cachepath);
        }

        if (!empty($cachedirs)) {
            foreach ($cachedirs as $cachedir) {
                if ($cachedir !== '.' && $cachedir !== '..') {
                    $filepath = $cachepath . '/' . $cachedir;
                    if (is_dir($filepath)) {
                        $filedirs = scandir($filepath);
                    }

                    foreach ($filedirs as $filedir) {
                        if ($filedir !== '.' && $filedir !== '..') {
                            if (file_exists($filepath)) {
                                $dir_path = $filepath . '/' . $filedir;
                                $size_cache += filesize($dir_path);
                            }
                        }
                    }
                }
            }
        }
        // analysis size css cache
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            $css_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/wpsol-minification/' . $blog_id . '/css';
        } else {
            $css_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/wpsol-minification/css';
        }
        if (is_dir($css_path)) {
            $file_in_css = scandir($css_path);
        }
        if (!empty($file_in_css)) {
            foreach ($file_in_css as $v) {
                if ($v !== '.' && $v !== '..' && $v !== 'index.html') {
                    $path = $css_path . '/' . $v;
                    $size_css_cache += filesize($path);
                }
            }
        }

        // analysis size js cache
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            $js_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/wpsol-minification/' . $blog_id . '/js';
        } else {
            $js_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/wpsol-minification/js';
        }
        if (is_dir($js_path)) {
            $file_in_js = scandir($js_path);
        }
        if (!empty($file_in_js)) {
            foreach ($file_in_js as $v) {
                if ($v !== '.' && $v !== '..' && $v !== 'index.html') {
                    $path = $js_path . '/' . $v;
                    $size_js_cache += filesize($path);
                }
            }
        }

        $total_size_cache = $size_cache + $size_css_cache + $size_js_cache;

        $result['params'] = self::formatBytes($total_size_cache);

        //clear minification
        $result['status'] = true;
        $message = array();
        if (!WpsolMinificationCache::clearMinification()) {
            $result['status'] = false;
            $message[] = __('Failed to cleanup minification!', 'wp-speed-of-light');
        }

        //delete all cache
        if (!WpsolCache::factory()->wpsolCacheFlush()) {
            $result['status'] = false;
            array_push($message, __('Failed to cleanup cache!', 'wp-speed-of-light'));
        }

        /**
         * Action preload after being cleared.
         *
         * @internal
         */
        do_action('wpsol_addon_preload_cache');

        // Purge third party cache
        $purge_third = '';
        if (is_plugin_active('wp-speed-of-light-addon/wp-speed-of-light-addon.php')) {
            $third = new WpsolAddonFlushThirdPartyCache();
            $purge_third = $third->runPurgeThirdparty();
        }

        if (!empty($purge_third)) {
            $message = array_merge($message, $purge_third);
        }

        /**
         * Action called after cache has been completely cleared
         *
         * @param array Extra informations, origine of clear call, total cache cleared
         */
        do_action('wpsol_purge_cache', array('type' => 'manual', 'total_cache' => $result['params']));

        $result['message'] = $message;

        wp_send_json($result);
    }


    /**
     * Check administrator for exclude
     *
     * @return boolean
     */
    public static function checkAdminRole()
    {
        if (current_user_can('manage_options')) {
            $opts = get_option('wpsol_configuration');
            if (!empty($opts['disable_user'])) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Get relative url from post, page
     *
     * @return array
     */
    public static function wpsolGetUrlPath()
    {
        $total_path = array();
        $total_path[] = get_site_url();
        //get post url
        $arg1 = array(
            'posts_per_page' => -1,
            'post_type' => 'post',
        );
        $posts = get_posts($arg1);
        foreach ($posts as $post) {
            $total_path[] = get_permalink($post->ID);
        }
        //get page url
        $arg2 = array(
            'posts_per_page' => -1,
            'post_type' => 'page',
        );
        $pages = get_posts($arg2);
        foreach ($pages as $page) {
            $total_path[] = get_page_link($page->ID);
        }
        return $total_path;
    }

    /**
     * Convert bytes from natural numbers
     *
     * @param integer $bytes     Input bytes
     * @param integer $precision Precision and accuracy
     *
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2);
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2);
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2);
        } elseif ($bytes >= 1) {
            $bytes = $bytes;
        } else {
            $bytes = '0';
        }
        return $bytes;
    }
}
