<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once(WPSOL_PLUGIN_DIR . 'inc/install-wizard/handler-wizard.php');
/**
 * Class WpsolInstallWizard
 */
class WpsolInstallWizard
{
    /**
     * Init step params
     *
     * @var array
     */
    protected $steps = array(
            'environment' => array(
                    'name' => 'Environment Check',
                    'view' => 'viewEvironment',
                    'action' => 'saveEvironment'
            ),
            'quick_config' => array(
                    'name' => 'Quick Configuration',
                    'view' => 'viewQuickConfig',
                    'action' => 'saveQuickConfig'
            ),
            'main_optimization' => array(
                    'name' => 'Main Optimization',
                    'view' => 'viewMainOptimization',
                    'action' => 'saveMainOptimization',
            ),
            'advanced_config' => array(
                    'name' => 'Advanced Configuration',
                    'view' => 'viewAdvancedConfig',
                    'action' => 'saveAdvancedConfig'
            )
    );
    /**
     * Init current step params
     *
     * @var array
     */
    protected $current_step = array();
    /**
     * WpsolInstallWizard constructor.
     */
    public function __construct()
    {
        if (current_user_can('manage_options')) {
            add_action('admin_menu', array($this, 'adminMenus'));
            add_action('admin_init', array($this, 'runWizard'));
        }
    }
    /**
     * Add admin menus/screens.
     *
     * @return void
     */
    public function adminMenus()
    {
        add_dashboard_page('', '', 'manage_options', 'wpsol-wizard', '');
    }

    /**
     * Execute wizard
     *
     * @return void
     */
    public function runWizard()
    {
        // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
        if (!isset($_GET['page']) || 'wpsol-wizard' !== $_GET['page']) {
            return;
        }
        // Enqueue script and style
        wp_enqueue_style(
            'wpsol_wizard',
            plugins_url('install-wizard/install-wizard.css', dirname(__FILE__)),
            array(),
            WPSOL_VERSION
        );
        wp_enqueue_style(
            'wpsol-css-framework',
            WPSOL_PLUGIN_URL.'css/wp-css-framework/style.css'
        );

        // Get step
        $this->steps = apply_filters('wpsol_setup_wizard_steps', $this->steps);
        $this->current_step  = isset($_GET['step']) ? sanitize_key($_GET['step']) : current(array_keys($this->steps));

        // Save action
        if (!empty($_POST['wpsol_save_step']) && isset($this->steps[$this->current_step]['action'])) {
            call_user_func(array('WpsolHandlerWizard', $this->steps[$this->current_step]['action']), $this->current_step);
        }

        // Render
        $this->setHeader();
        if (!isset($_GET['step'])) {
            require_once(WPSOL_PLUGIN_DIR . 'inc/install-wizard/content/viewWizard.php');
        } elseif (isset($_GET['step']) && $_GET['step'] === 'wizard_done') {
            require_once(WPSOL_PLUGIN_DIR . 'inc/install-wizard/content/viewDashboard.php');
        } else {
            $this->setMenu();
            $this->setContent();
        }
        $this->setFooter();
        // phpcs:enable
        exit();
    }


    /**
     * Get next link step
     *
     * @param string $step Current step
     *
     * @return string
     */
    public function getNextLink($step = '')
    {
        if (!$step) {
            $step = $this->current_step;
        }

        $keys = array_keys($this->steps);

        if (end($keys) === $step) {
            return add_query_arg('step', 'wizard_done', remove_query_arg('activate_error'));
        }

        $step_index = array_search($step, $keys, true);
        if (false === $step_index) {
            return '';
        }

        return add_query_arg('step', $keys[$step_index + 1], remove_query_arg('activate_error'));
    }

    /**
     * Output the menu for the current step.
     *
     * @return void
     */
    public function setMenu()
    {
        $output_steps = $this->steps;
        ?>
        <div class="wpsol-wizard-steps">
            <ul class="wizard-steps">
                <?php
                $i = 0;
                foreach ($output_steps as $key => $step) {
                    $position_current_step = array_search($this->current_step, array_keys($this->steps), true);
                    $position_step = array_search($key, array_keys($this->steps), true);
                    $is_visited = $position_current_step > $position_step;
                    $i ++;
                    if ($key === $this->current_step) {
                        ?>
                        <li class="actived"><div class="layer"><?php echo esc_html($i) ?></div></li>
                        <?php
                    } elseif ($is_visited) {
                        ?>
                        <li class="visited">
                            <a href="<?php echo esc_url(add_query_arg('step', $key, remove_query_arg('activate_error'))); ?>">
                                <div class="layer"><?php echo esc_html($i) ?></div></a>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li><div class="layer"><?php echo esc_html($i) ?></div></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
    }


    /**
     * Output the content for the current step.
     *
     * @return void
     */
    public function setContent()
    {
        echo '<div class="wizard-content">';
        if (!empty($this->steps[$this->current_step]['view'])) {
            require_once(WPSOL_PLUGIN_DIR . 'inc/install-wizard/content/' . $this->steps[$this->current_step]['view'] . '.php');
        }
        echo '</div>';
    }

    /**
     * Setup Wizard Header.
     *
     * @return void
     */
    public function setHeader()
    {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e('Speed Of Light &rsaquo; Setup Wizard', 'wp-speed-of-light'); ?></title>
            <?php do_action('admin_print_styles'); ?>
            <?php do_action('admin_head'); ?>
        </head>
        <body class="wpsol-wizard-setup wp-core-ui">
        <div class="wpsol-wizard-content">
        <?php
    }

    /**
     * Setup Wizard Footer.
     *
     * @return void
     */
    public function setFooter()
    {
        ?>
        </div>
        </body>
        </html>
        <?php
    }

    /**
     * Parse module info.
     * Based on https://gist.github.com/sbmzhcn/6255314
     *
     * @return array
     */
    public static function parsePhpinfo()
    {
        ob_start();
        //phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_phpinfo -- Get info modules of phpinfo
        phpinfo(INFO_MODULES);
        $s = ob_get_contents();
        ob_end_clean();
        $s = strip_tags($s, '<h2><th><td>');
        $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
        $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
        $t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
        $r = array();
        $count = count($t);
        $p1 = '<info>([^<]+)<\/info>';
        $p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
        $p3 = '/'.$p1.'\s*'.$p1.'/';
        for ($i = 1; $i < $count; $i++) {
            if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
                $name = trim($matchs[1]);
                $vals = explode("\n", $t[$i + 1]);
                foreach ($vals as $val) {
                    if (preg_match($p2, $val, $matchs)) { // 3cols
                        $r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
                    } elseif (preg_match($p3, $val, $matchs)) { // 2cols
                        $r[$name][trim($matchs[1])] = trim($matchs[2]);
                    }
                }
            }
        }
        return $r;
    }

    /**
     * Check gzip activeed
     *
     * @return array
     */
    public static function getHeaderInfo()
    {
        $result = array(
            'gzip' => false,
            'expires' => false
        );

        $headers = self::getHeadersResponse();

        if (isset($headers['Content-Encoding']) && $headers['Content-Encoding'] === 'gzip') {
            $result['gzip'] = true;
        }
        if (isset($headers['expires'])) {
            $result['expires'] = true;
        }

        return $result;
    }

    /**
     * Get header response
     *
     * @return array
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

new WpsolInstallWizard();
