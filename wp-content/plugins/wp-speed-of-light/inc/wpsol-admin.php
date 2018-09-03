<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin functions here
 */
class WpsolAdmin
{
    /**
     * WpsolAdmin constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'registerMenuPage'));
        /**
         * Load admin js *
        */
        add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
        //** load languages **//
        add_action(
            'init',
            function () {
                load_plugin_textdomain(
                    'wp-speed-of-light',
                    false,
                    dirname(plugin_basename(WPSOL_FILE)) . '/languages/'
                );
            }
        );

        $this->ajaxHandle();
    }

    /**
     * Register menu page
     *
     * @return void
     */
    public function registerMenuPage()
    {
        // add main menu
        $page_title = __('WP Speed of Light:', 'wp-speed-of-light') . ' ' . __('Dashboard', 'wp-speed-of-light');
        $menu_title = __('WP Speed of Light', 'wp-speed-of-light');
        $admin_page =add_menu_page(
            $page_title,
            $menu_title,
            'manage_options',
            'wpsol_dashboard',
            array($this, 'loadPage'),
            'dashicons-performance'
        );

        /**
         * Filter Capability and Role to display menu.
         *
         * @param string Capability name
         *
         * @return string
         */
        $manage_options_cap = apply_filters('wpsol_manage_options_capability', 'manage_options');

        $count = '';
        if (!is_multisite() && current_user_can('manage_options')) {
            $systemCheck = WpsolSpeedOptimization::systemCheck();

            if ($systemCheck) {
                if (!isset($count_system)) {
                    $count_system = WpsolSpeedOptimization::countSystemCheck();
                }
                $count = '<span class="update-plugins count-'.$count_system.'"><span class="plugin-count">' . number_format_i18n($count_system) . '</span></span>';
            }
        }


        // add submenu
        $submenu_pages = array(
            array(
                'wpsol_dashboard',
                '',
                __('Dashboard', 'wp-speed-of-light'),
                $manage_options_cap,
                'wpsol_dashboard',
                array($this, 'loadPage'),
                null,
            ),
            array(
                'wpsol_dashboard',
                '',
                sprintf(__('Speed optimization %s', 'wp-speed-of-light'), $count),
                $manage_options_cap,
                'wpsol_speed_optimization',
                array($this, 'loadPage'),
                null,
            ),
            array(
                'wpsol_dashboard',
                '',
                __('Speed analysis', 'wp-speed-of-light'),
                $manage_options_cap,
                'wpsol_speed_analysis',
                array($this, 'loadPage'),
                null,
            ),
        );

        if (!is_plugin_active('wp-speed-of-light-addon/wp-speed-of-light-addon.php')) {
            $more_speedup = array(
                'wpsol_dashboard',
                '',
                '<span style="color:orange">' . __('More SpeedUp', 'wp-speed-of-light') . '</span>',
                $manage_options_cap,
                'wpsol_more_speedup',
                array($this, 'loadPage'),
                null,
            );

            array_push($submenu_pages, $more_speedup);
        }

        if (count($submenu_pages)) {
            foreach ($submenu_pages as $submenu_page) {
                // Add submenu page
                $admin_page = add_submenu_page(
                    $submenu_page[0],
                    $submenu_page[2] . ' - ' . __('WP Speed of Light:', 'wp-speed-of-light'),
                    $submenu_page[2],
                    $submenu_page[3],
                    $submenu_page[4],
                    $submenu_page[5]
                );
            }
        }
    }

    /**
     * Include display page
     *
     * @return void
     */
    public function loadPage()
    {
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                // phpcs:enable
                case 'wpsol_speed_analysis':
                    include_once WPSOL_PLUGIN_DIR . 'inc/views/speed-analysis.php';
                    break;
                case 'wpsol_speed_optimization':
                    include_once WPSOL_PLUGIN_DIR . 'inc/views/speed-optimization.php';
                    break;
                case 'wpsol_more_speedup':
                    include_once WPSOL_PLUGIN_DIR . 'inc/views/more_speedup.php';
                    break;
                default:
                    include_once WPSOL_PLUGIN_DIR . 'inc/views/dashboard.php';
                    break;
            }
        }
    }

    /**
     * Load script for backend
     *
     * @return void
     */
    public function loadAdminScripts()
    {
        $current_screen = get_current_screen();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');

        if ($current_screen->base === 'toplevel_page_wpsol_dashboard' ||
            $current_screen->base === 'wp-speed-of-light_page_wpsol_speed_analysis' ||
            $current_screen->base === 'wp-speed-of-light_page_wpsol_speed_optimization'
        ) {
            wp_enqueue_script(
                'wpsol-material_tabs',
                plugins_url('js/material/tabs.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_script(
                'wpsol-tooltip',
                plugins_url('js/material/tooltip.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );
            wp_enqueue_script(
                'wpsol-global',
                plugins_url('js/material/global.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );
            wp_enqueue_script(
                'wpsol-velocity',
                plugins_url('js/material/velocity.min.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );

            wp_enqueue_style(
                'style-light-speed-jquery-ui-fresh',
                plugins_url('css/jquery-ui-fresh.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_script(
                'wpsol-waves',
                plugins_url('js/material/waves.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );

            wp_enqueue_script(
                'wpsol-speed_cookie',
                plugins_url('js/jquery.cookie.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_style(
                'wpsol-css-framework',
                plugins_url('css/wp-css-framework/style.css', dirname(__FILE__))
            );
        }

//
//
//        DASHBOARD
        if ($current_screen->base === 'toplevel_page_wpsol_dashboard') {
            wp_enqueue_style(
                'wpsol-dashboard',
                plugins_url('/css/dashboard.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_script(
                'wpsol-dashboard',
                plugins_url('js/dashboard.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
        }
//
//
//
//
//        ANALYSIS
        if ($current_screen->base === 'wp-speed-of-light_page_wpsol_speed_analysis') {
            wp_enqueue_script(
                'wpsol-speed_analysis',
                plugins_url('js/wpsol-speed-analysis.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
            $ajax_non =  wp_create_nonce('wpsolAnalysisJS');
            wp_localize_script('wpsol-speed_analysis', 'wpsolAnalysisJS', array('ajaxnonce' => $ajax_non));

            wp_enqueue_script(
                'wpsol-speed_tablesorter',
                plugins_url('js/jquery.tablesorter.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            // LOAD STYLES
            wp_enqueue_style(
                'wpsol-analysis',
                plugins_url('css/speed_analysis.css', dirname(__FILE__))
            );

            wp_enqueue_style(
                'style-light-speed-jquery-ui-fresh',
                plugins_url('css/jquery-ui-fresh.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );
        }

        if ($current_screen->base === 'wp-speed-of-light_page_wpsol_speed_optimization') {
            // Load jquery ui first tab jquery
            wp_enqueue_script(
                'wpsol-jquery-ui',
                plugins_url('js/jquery-ui.min.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_script(
                'wpsol-speed-optimization',
                plugins_url('js/wpsol-speed-optimization.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_script(
                'wpsol-import-export',
                plugins_url('js/wpsol-import-export.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
            wp_localize_script('wpsol-import-export', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            $ajax_non =  wp_create_nonce('wpsolImportExportCheck');
            wp_localize_script('wpsol-import-export', 'ajaxNonce', array('ajaxnonce' => $ajax_non));


            wp_enqueue_script(
                'jquery-qtip',
                plugins_url('js/jquery.qtip.min.js', dirname(__FILE__)),
                array('jquery'),
                '2.2.1',
                true
            );

            wp_enqueue_script(
                'wpsol-js-framework',
                plugins_url('js/wp-js-framework/script.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            // Load style
            //
            // LOAD STYLE
            wp_enqueue_style(
                'wpsol-speed-optimization',
                plugins_url('css/speed-optimization.css', dirname(__FILE__))
            );

            wp_enqueue_style(
                'jquery-qtip',
                plugins_url('css/jquery.qtip.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_style('wpsol-quirk', plugins_url('/css/quirk.css', dirname(__FILE__)));

            wp_enqueue_style('wpsol-import-export', plugins_url('/css/import_export.css', dirname(__FILE__)));
        }


        if ($current_screen->base === 'wp-speed-of-light_page_wpsol_more_speedup') {
            wp_enqueue_style(
                'wpsol-more-speedup',
                plugins_url('/css/more-speedup.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );
        }
    }

    /**
     * Add ajax handle action
     *
     * @return void
     */
    public function ajaxHandle()
    {
        add_action('wp_ajax_wpsol_load_page_time', array('WpsolSpeedAnalysis', 'loadPageTime'));
        add_action('wp_ajax_wpsol_start_scan_query', array('WpsolSpeedAnalysis', 'startScanQuery'));
        add_action('wp_ajax_wpsol_stop_scan_query', array('WpsolSpeedAnalysis', 'stopScanQuery'));
        add_action('wp_ajax_wpsol_ajax_clean_cache', array('WpsolConfiguration', 'ajaxCleanCache'));
        add_action('wp_ajax_wpsol_more_details', array('WpsolSpeedAnalysis', 'moreDetails'));
        add_action('wp_ajax_wpsol_delete_details', array('WpsolSpeedAnalysis', 'deleteDetails'));
        add_action('wp_ajax_wpsol_check_response_dashboard', array('WpsolDashboard', 'checkResponseDashboard'));
        add_action('wp_ajax_wpsol_export_configuration', array('WpsolImportExport', 'exportConfiguration'));
    }
}
