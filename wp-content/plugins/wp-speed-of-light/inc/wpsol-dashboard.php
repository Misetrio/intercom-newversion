<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolDashboard
 */
class WpsolDashboard
{
    /**
     * Cache parameter
     *
     * @var integer
     */
    public $cache = 0;
    /**
     * Minification parameter
     *
     * @var integer
     */
    public $minification = 0;

    /**
     * WpsolDashboard constructor.
     */
    public function __construct()
    {
    }

    /**
     * Analysis system for display dashboard
     *
     * @return array
     */
    public function checkDashboard()
    {
        $plugin_enable = array();

        $results = array(
            'cache' => 'warning',
            'gzip' => 'warning',
            'cache-clean' => 'warning',
            'php-version' => 'warning',
            'expires' => 'warning',
            'rest' => 'notice'
        );

        $optimization = get_option('wpsol_optimization_settings');

        if (isset($optimization['speed_optimization']['act_cache']) &&
            $optimization['speed_optimization']['act_cache']) {
            $results['cache'] = 'success';
        }

        if (isset($optimization['speed_optimization']['remove_rest_api']) && $optimization['speed_optimization']['remove_rest_api']) {
            $results['rest'] = 'success';
        }

        if (version_compare(phpversion(), '7.0', '>=') && version_compare(phpversion(), '7.2', '<')) {
            $results['php-version'] = 'notice';
        }
        if (version_compare(phpversion(), '7.2', '>')) {
            $results['php-version'] = 'success';
        }

        $database = get_option('wpsol_clean_database_config');
        $arr = array();
        if (!empty($database)) {
            foreach ($database as $k => $v) {
                if ($v !== 'transient') {
                    $arr[] = $v;
                }
            }
            if (count($arr) > 0) {
                $results['cache-clean'] = 'success';
            }
        }


        //If it has been done is less than a month you can set the setting to OK (so even the free version can validate the setting)
        $old_date = get_option('wpsol_database_cleanup_count_time');

        if (!empty($old_date)) {
            $count_time = strtotime($old_date) + (30 * 24 * 60 * 60);
            if ($count_time < time()) {
                $results['cache-clean'] = 'warning';
            }
        } else {
            $results['cache-clean'] = 'warning';
        }

        return $results;
    }

    /**
     * Analysis system for display dashboard
     *
     * @return array
     */
    public function checkOptimization()
    {
        $results = array (
            'plugins_enable' => 0,
            'plugins_disable' => 0,
            'image_compression' => 0,
            'lazy_loading' => 0,
            'group_files' => 0,
            'database_clean' => 0,
            'group_fonts' => 0,
            'minify_files' => 0
        );
        $advanced = get_option('wpsol_advanced_settings');
        $optimization = get_option('wpsol_optimization_settings');
        $database = get_option('wpsol_db_clean_addon');
        // Check other
        //
        $plugins = get_plugins();
        $plugin_enable = array();
        $plugins_disable = array();
        foreach ($plugins as $k => $v) {
            if (is_plugin_active($k) === true) {
                $plugin_enable[] = $k;
            }
            if (is_plugin_active($k) === false) {
                $plugins_disable[] = $k;
            }
        }
        $results['plugins_enable'] = count($plugin_enable);
        $results['plugins_disable'] = count($plugins_disable);

        //Check additional
        //
        if (is_plugin_active('imagerecycle-pdf-image-compression/wp-image-recycle.php')) {
        // Check image compression active
            $results['image_compression'] = 1;
        }

        if (isset($optimization['advanced_features'])) {
            if ($optimization['advanced_features']['cssgroup_minification'] ||
                $optimization['advanced_features']['jsgroup_minification']) {
                $results['group_files'] = 1;
            }
        }

        if (isset($optimization['advanced_features'])) {
            if ($optimization['advanced_features']['html_minification'] ||
                $optimization['advanced_features']['css_minification'] ||
                $optimization['advanced_features']['js_minification']) {
                $results['minify_files'] = 1;
            }
        }
        // Check advanced
        //
        if (class_exists('WpsolAddonSpeedOptimization')) {
            if (isset($advanced['lazy_loading']) && $advanced['lazy_loading']) {
                $results['lazy_loading'] = 1;
            }

            if (isset($database['db_clean_auto']) && $database['db_clean_auto']) {
                $results['database_clean'] = 1;
            }
            if (isset($optimization['advanced_features']['fontgroup_minification']) &&
                $optimization['advanced_features']['fontgroup_minification']) {
                $results['group_fonts'] = 1;
            }
        }


        return $results;
    }
    /**
     * Check gzip activeed
     *
     * @return void
     */
    public static function checkResponseDashboard()
    {
        $result = array(
            'gzip' => false,
            'expires' => false
        );

        if (!current_user_can('manage_options')) {
            echo json_encode($result);
            exit;
        }
        $headers = self::getHeadersResponse();

        if (isset($headers['Content-Encoding']) && $headers['Content-Encoding'] === 'gzip') {
            $result['gzip'] = true;
        }
        if (isset($headers['expires'])) {
            $result['expires'] = true;
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Get header response
     *
     * @return array|Requests_Utility_CaseInsensitiveDictionary
     */
    public static function getHeadersResponse()
    {
        $url = home_url();
        $args = array(
            'headers' => array(
                'timeout' => 30,
                'redirection' => 10,
            )
        );
        // Retrieve the raw response from the HTTP request
        $response = wp_remote_get($url, $args);
        $headers = wp_remote_retrieve_headers($response);

        return $headers;
    }
}
