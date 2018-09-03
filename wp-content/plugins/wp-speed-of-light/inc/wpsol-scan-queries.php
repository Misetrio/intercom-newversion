<?php
/*
 *  Based on some work of query-monitor plugin
 */
if (!defined('SAVEQUERIES')) {
    define('SAVEQUERIES', true);
}
if (!defined('SOL_DB_EXPENSIVE')) {
    define('SOL_DB_EXPENSIVE', 0.05);
}
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolDBQueries
 */
class WpsolDBQueries
{
    /**
     * Init data params
     *
     * @var array
     */
    protected $data = array(
        'types' => array(),
        'component_times' => array(),
    );
    /**
     * Init filtered_trace params
     *
     * @var void
     */
    protected $filtered_trace = null;
    /**
     * Init abspath params
     *
     * @var null
     */
    protected static $abspath = null;
    /**
     * Init contentpath params
     *
     * @var null
     */
    protected static $contentpath = null;
    /**
     * Init file dirs params
     *
     * @var array
     */
    protected static $file_dirs = array();
    /**
     * Init file components params
     *
     * @var array
     */
    protected static $file_components = array();
    /**
     * Init ignore_class params
     *
     * @var array
     */
    protected static $ignore_class = array(
        'wpdb' => true,
        'QueryMonitor' => true,
        'ExtQuery' => true,
        'W3_Db' => true,
        'Debug_Bar_PHP' => true,
    );
    /**
     * Init ignore_method params
     *
     * @var array
     */
    protected static $ignore_method = array();
    /**
     * Init ignore_func params
     *
     * @var array
     */
    protected static $ignore_func = array(
        'include_once' => true,
        'require_once' => true,
        'include' => true,
        'require' => true,
        'call_user_func_array' => true,
        'call_user_func' => true,
        'trigger_error' => true,
        '_doing_it_wrong' => true,
        '_deprecated_argument' => true,
        '_deprecated_file' => true,
        '_deprecated_function' => true,
        'dbDelta' => true,
    );
    /**
     * Init show_args params
     *
     * @var array
     */
    protected static $show_args = array(
        'do_action' => 1,
        'apply_filters' => 1,
        'do_action_ref_array' => 1,
        'apply_filters_ref_array' => 1,
        'get_template_part' => 2,
        'get_extended_template_part' => 2,
        'load_template' => 'dir',
        'dynamic_sidebar' => 1,
        'get_header' => 1,
        'get_sidebar' => 1,
        'get_footer' => 1,
        'get_site_by_path' => 3,
    );
    /**
     * Init filtered params
     *
     * @var boolean
     */
    protected static $filtered = false;
    /**
     * Init trace params
     *
     * @var null
     */
    protected $trace = null;
    /**
     * Init calling_line params
     *
     * @var integer
     */
    protected $calling_line = 0;
    /**
     * Init calling_file params
     *
     * @var string
     */
    protected $calling_file = '';
    /**
     * Init id params
     *
     * @var string
     */
    public $id = 'db_queries';
    /**
     * Init db_objects params
     *
     * @var array
     */
    public $db_objects = array();

    /**
     * WpsolDBQueries constructor.
     */
    public function __construct()
    {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions -- List back trace to use, no debug code
        $this->trace = debug_backtrace(false);

        if (!defined('DOING_AJAX')) {
            // Hook shutdown
            register_shutdown_function(array($this, 'shutdownHandler'));
        }
    }

    /**
     * Run analys queries
     *
     * @return void
     */
    public function shutdownHandler()
    {
        $this->process();
    }

    /**
     * Return type of data
     *
     * @param string $type Type data
     *
     * @return void
     */
    protected function logType($type)
    {

        if (isset($this->data['types'][$type])) {
            $this->data['types'][$type]++;
        } else {
            $this->data['types'][$type] = 1;
        }
    }

    /**
     * Maybe log dupbe
     *
     * @param string $sql Query string
     * @param string $i   Number
     *
     * @return void
     */
    protected function maybeLogDupe($sql, $i)
    {

        $sql = str_replace(array("\r\n", "\r", "\n"), ' ', $sql);
        $sql = str_replace(array("\t", '`'), '', $sql);
        $sql = preg_replace('/[ ]+/', ' ', $sql);
        $sql = trim($sql);

        $this->data['dupes'][$sql][] = $i;
    }

    /**
     * Return log component
     *
     * @param string $component Find component
     * @param string $ltime     Life time
     * @param string $type      Type of component
     *
     * @return void
     */
    protected function logComponent($component, $ltime, $type)
    {

        if (!isset($this->data['component_times'][$component->name])) {
            $this->data['component_times'][$component->name] = array(
                'component' => $component->name,
                'calls' => 0,
                'ltime' => 0,
                'types' => array()
            );
        }

        $this->data['component_times'][$component->name]['calls']++;
        $this->data['component_times'][$component->name]['ltime'] += $ltime;

        if (isset($this->data['component_times'][$component->name]['types'][$type])) {
            $this->data['component_times'][$component->name]['types'][$type]++;
        } else {
            $this->data['component_times'][$component->name]['types'][$type] = 1;
        }
    }

    /**
     * Get errors
     *
     * @return boolean|mixed
     */
    public function getErrors()
    {
        if (!empty($this->data['errors'])) {
            return $this->data['errors'];
        }
        return false;
    }

    /**
     * Check expensive
     *
     * @param array $row Row of query
     *
     * @return boolean
     */
    public static function isExpensive(array $row)
    {
        return $row['ltime'] > SOL_DB_EXPENSIVE;
    }

    /**
     * Run analys query
     *
     * @return void
     */
    public function process()
    {
        if (!SAVEQUERIES) {
            return;
        }
        $this->data['total_qs'] = 0;
        $this->data['total_time'] = 0;
        $this->data['errors'] = array();
        $this->db_objects = apply_filters('db_objects', array(
            '$wpdb' => $GLOBALS['wpdb']
        ));

        foreach ($this->db_objects as $name => $db) {
            if (is_a($db, 'wpdb')) {
                $this->processDbObject($name, $db);
            } else {
                unset($this->db_objects[$name]);
            }
        }
    }

    /**
     * Log caller
     *
     * @param string  $caller Caller component
     * @param integer $ltime  Life time
     * @param string  $type   Type of component
     *
     * @return void
     */
    protected function logCaller($caller, $ltime, $type)
    {

        if (!isset($this->data['times'][$caller])) {
            $this->data['times'][$caller] = array(
                'caller' => $caller,
                'calls' => 0,
                'ltime' => 0,
                'types' => array()
            );
        }

        $this->data['times'][$caller]['calls']++;
        $this->data['times'][$caller]['ltime'] += $ltime;

        if (isset($this->data['times'][$caller]['types'][$type])) {
            $this->data['times'][$caller]['types'][$type]++;
        } else {
            $this->data['times'][$caller]['types'][$type] = 1;
        }
    }

    /**
     * Update data for option
     *
     * @param integer $id ID of database
     * @param wpdb    $db Database
     *
     * @return void
     */
    public function processDbObject($id, wpdb $db)
    {
        global $EZSQL_ERROR;

        $rows = array();
        $types = array();
        $total_time = 0;
        $has_result = false;
        $has_trace = false;
        $i = 0;

        foreach ((array)$db->queries as $query) {
            # @TODO: decide what I want to do with this:
            // phpcs:ignore WordPress.Security.NonceVerification -- Check wp_admin_bar request, no action
            if (false !== strpos($query[2], 'wp_admin_bar') && !isset($_REQUEST['qm_display_admin_bar'])) {
                continue;
            }

            $sql = $query[0];
            $ltime = $query[1];
            $stack = $query[2];
            $has_trace = isset($query['trace']);
            $has_result = isset($query['result']);

            if (isset($query['result'])) {
                $result = $query['result'];
            } else {
                $result = null;
            }

            $total_time += $ltime;
            $component = $this->getComponent();

            if (isset($query['trace'])) {
                $trace = $query['trace'];
                $caller = $this->getCaller();
                $caller_name = $caller['id'];
                $caller = $caller['display'];
            } else {
                $trace = null;
                $component = null;
                $callers = explode(',', $stack);
                $caller = trim(end($callers));

                if (false !== strpos($caller, '(')) {
                    $caller_name = substr($caller, 0, strpos($caller, '(')) . '()';
                } else {
                    $caller_name = $caller;
                }
            }

            $sql = trim($sql);
            $type = $this->getQueryType($sql);

            $this->logType($type);
            $this->logCaller($caller_name, $ltime, $type);

            $this->maybeLogDupe($sql, $i);

            if ($component) {
                $this->logComponent($component, $ltime, $type);
            }

            if (!isset($types[$type]['total'])) {
                $types[$type]['total'] = 1;
            } else {
                $types[$type]['total']++;
            }

            if (!isset($types[$type]['callers'][$caller])) {
                $types[$type]['callers'][$caller] = 1;
            } else {
                $types[$type]['callers'][$caller]++;
            }

            $row = compact('caller', 'caller_name', 'stack', 'sql', 'ltime', 'result', 'type', 'component', 'trace');

            if (is_wp_error($result)) {
                $this->data['errors'][] = $row;
            }

            if (self::isExpensive($row)) {
                $this->data['expensive'][] = $row;
            }

            $rows[$i] = $row;
            $i++;
        }

        if ('$wpdb' === $id && !$has_result && !empty($EZSQL_ERROR) && is_array($EZSQL_ERROR)) {
            // Fallback for displaying database errors when wp-content/db.php isn't in place
            foreach ($EZSQL_ERROR as $error) {
                $row = array(
                    'caller' => 'Unknown',
                    'caller_name' => 'Unknown',
                    'stack' => array(),
                    'sql' => $error['query'],
                    'result' => new WP_Error('qmdb', $error['error_str']),
                    'type' => '',
                    'component' => false,
                    'trace' => null,
                );
                $this->data['errors'][] = $row;
            }
        }

        $total_qs = count($rows);

        $this->data['total_qs'] += $total_qs;
        $this->data['total_time'] += $total_time;

        # @TODO put errors in here too:
        # @TODO proper class instead of (object)
        $this->data['dbs'][$id] = (object)compact('rows', 'types', 'has_result', 'has_trace', 'total_time', 'total_qs');

        $queries = get_option('wpsol_scan_queries');
        if (!empty($queries)) {
            $queries = array();
        }
        $queries = $this->data;
        update_option('wpsol_scan_queries', $queries);
    }

    /**
     * Get query type
     *
     * @param string $sql SQL query
     *
     * @return array|mixed|string
     */
    public static function getQueryType($sql)
    {
        $type = trim($sql);
        $sql = trim($sql);

        if (0 === strpos($sql, '/*')) {
            // Strip out leading comments such as `/*NO_SELECT_FOUND_ROWS*/` before calculating the query type
            $type = preg_replace('|^/\*[^\*/]+\*/|', '', $sql);
        }

        $type = preg_split('/\b/', trim($type), 2, PREG_SPLIT_NO_EMPTY);
        $type = strtoupper($type[0]);

        return $type;
    }

    /**
     * Get component detail
     *
     * @return boolean|mixed
     */
    public function getComponent()
    {
        $components = array();

        foreach ($this->trace as $item) {
            try {
                if (isset($item['class'])) {
                    if (!is_object($item['class']) && !class_exists($item['class'], false)) {
                        continue;
                    }
                    if (!method_exists($item['class'], $item['function'])) {
                        continue;
                    }
                    $ref = new ReflectionMethod($item['class'], $item['function']);
                    $file = $ref->getFileName();
                } elseif (function_exists($item['function'])) {
                    $ref = new ReflectionFunction($item['function']);
                    $file = $ref->getFileName();
                } elseif (isset($item['file'])) {
                    $file = $item['file'];
                } else {
                    continue;
                }

                $comp = $this->getFileComponent($file);
                $components[$comp->type] = $comp;
            } catch (ReflectionException $e) {
                echo 'Caught exception: ',  esc_html($e->getMessage()), "\n";
            }
        }

        foreach ($this->getFileDirs() as $type => $dir) {
            if (isset($components[$type])) {
                return $components[$type];
            }
        }
        return true;
        # This should not happen
    }

    /**
     * Get caller of component
     *
     * @return mixed
     */
    public function getCaller()
    {
        $trace = $this->getFilteredTrace();
        return reset($trace);
    }

    /**
     * Get filtered trace
     *
     * @return array|null
     */
    public function getFilteredTrace()
    {

        if (!isset($this->filtered_trace)) {
            $trace = array_map(array($this, 'filterTrace'), $this->trace);
            $trace = array_values(array_filter($trace));
            if (empty($trace) && !empty($this->trace)) {
                $lowest = $this->trace[0];
                $file = self::standardDir($lowest['file'], '');
                $lowest['calling_file'] = $lowest['file'];
                $lowest['calling_line'] = $lowest['line'];
                $lowest['function'] = $file;
                $lowest['display'] = $file;
                $lowest['id'] = $file;
                unset($lowest['class'], $lowest['args'], $lowest['type']);
                $trace[0] = $lowest;
            }

            $this->filtered_trace = $trace;
        }

        return $this->filtered_trace;
    }

    /**
     * Get file from another plugin
     *
     * @param string $file Name of file component
     *
     * @return mixed|object
     */
    public static function getFileComponent($file)
    {

        # @TODO turn this into a class (eg SOL_File_Component)

        $file = self::standardDir($file);

        if (isset(self::$file_components[$file])) {
            return self::$file_components[$file];
        }

        foreach (self::getFileDirs() as $type => $dir) {
            if ($dir && (0 === strpos($file, $dir))) {
                break;
            }
        }

        $context = $type;

        switch ($type) {
            case 'plugin':
            case 'mu-plugin':
                $plug = plugin_basename($file);
                if (strpos($plug, '/')) {
                    $plug = explode('/', $plug);
                    $plug = reset($plug);
                } else {
                    $plug = basename($plug);
                }
                if ('mu-plugin' === $type) {
                    $name = sprintf(__('MU Plugin: %s', 'wp-speed-of-light'), $plug);
                } else {
                    $name = sprintf(__('Plugin: %s', 'wp-speed-of-light'), $plug);
                }
                $context = $plug;
                break;
            case 'go-plugin':
            case 'vip-plugin':
                $plug = str_replace(self::$file_dirs[$type], '', $file);
                $plug = trim($plug, '/');
                if (strpos($plug, '/')) {
                    $plug = explode('/', $plug);
                    $plug = reset($plug);
                } else {
                    $plug = basename($plug);
                }
                $name = sprintf(__('VIP Plugin: %s', 'wp-speed-of-light'), $plug);
                $context = $plug;
                break;
            case 'stylesheet':
                if (is_child_theme()) {
                    $name = __('Child Theme', 'wp-speed-of-light');
                } else {
                    $name = __('Theme', 'wp-speed-of-light');
                }
                break;
            case 'template':
                $name = __('Parent Theme', 'wp-speed-of-light');
                break;
            case 'other':
                $name = self::standardDir($file, '');
                $context = $file;
                break;
            case 'core':
                $name = __('Core', 'wp-speed-of-light');
                break;
            case 'unknown':
            default:
                $name = __('Unknown', 'wp-speed-of-light');
                break;
        }
        self::$file_components[$file] = (object)compact('type', 'name', 'context');
        return self::$file_components[$file];
    }

    /**
     * Replace standar directory
     *
     * @param string $dir          Standar directory
     * @param null   $path_replace Replace directory
     *
     * @return mixed|string
     */
    public static function standardDir($dir, $path_replace = null)
    {

        $dir = wp_normalize_path($dir);

        if (is_string($path_replace)) {
            if (!self::$abspath) {
                self::$abspath = wp_normalize_path(ABSPATH);
                self::$contentpath = wp_normalize_path(dirname(WP_CONTENT_DIR) . '/');
            }
            $dir = str_replace(array(
                self::$abspath,
                self::$contentpath,
            ), $path_replace, $dir);
        }

        return $dir;
    }

    /**
     * Get file directory
     *
     * @return array
     */
    public static function getFileDirs()
    {
        if (empty(self::$file_dirs)) {
            self::$file_dirs['plugin'] = self::standardDir(WP_PLUGIN_DIR);
            self::$file_dirs['go-plugin'] = self::standardDir(WPMU_PLUGIN_DIR . '/shared-plugins');
            self::$file_dirs['mu-plugin'] = self::standardDir(WPMU_PLUGIN_DIR);
            self::$file_dirs['vip-plugin'] = self::standardDir(get_theme_root() . '/vip/plugins');
            self::$file_dirs['stylesheet'] = self::standardDir(get_stylesheet_directory());
            self::$file_dirs['template'] = self::standardDir(get_template_directory());
            self::$file_dirs['other'] = self::standardDir(WP_CONTENT_DIR);
            self::$file_dirs['core'] = self::standardDir(ABSPATH);
            self::$file_dirs['unknown'] = null;
        }
        return self::$file_dirs;
    }

    /**
     * Filter trace
     *
     * @param array $trace Trace component
     *
     * @return array|null
     */
    public function filterTrace(array $trace)
    {
        if (!self::$filtered && function_exists('did_action') && did_action('plugins_loaded')) {
            # Only run apply_filters on these once
            self::$ignore_class = apply_filters('ignore_class', self::$ignore_class);
            self::$ignore_method = apply_filters('ignore_method', self::$ignore_method);
            self::$ignore_func = apply_filters('ignore_func', self::$ignore_func);
            self::$show_args = apply_filters('show_args', self::$show_args);
            self::$filtered = true;
        }

        $return = $trace;

        if (isset($trace['class'])) {
            if (isset(self::$ignore_class[$trace['class']])) {
                $return = null;
            } elseif (isset(self::$ignore_method[$trace['class']][$trace['function']])) {
                $return = null;
            } elseif (0 === strpos($trace['class'], 'SOL_')) {
                $return = null;
            } else {
                $return['id'] = $trace['class'] . $trace['type'] . $trace['function'] . '()';
                $return['display'] = $trace['class'] . $trace['type'] . $trace['function'] . '()';
            }
        } else {
            if (isset(self::$ignore_func[$trace['function']])) {
                $return = null;
            } elseif (isset(self::$show_args[$trace['function']])) {
                $show = self::$show_args[$trace['function']];
                if ('dir' === $show) {
                    if (isset($trace['args'][0])) {
                        $arg = self::standardDir($trace['args'][0], '~/');
                        $return['id'] = $trace['function'] . '()';
                        $return['display'] = $trace['function'] . "('".$arg."')";
                    }
                } else {
                    $args = array();
                    for ($i = 0; $i < $show; $i++) {
                        if (isset($trace['args'][$i])) {
                            $args[] = '\'' . $trace['args'][$i] . '\'';
                        }
                    }
                    $return['id'] = $trace['function'] . '()';
                    $return['display'] = $trace['function'] . '(' . implode(',', $args) . ')';
                }
            } else {
                $return['id'] = $trace['function'] . '()';
                $return['display'] = $trace['function'] . '()';
            }
        }

        if ($return) {
            $return['calling_file'] = $this->calling_file;
            $return['calling_line'] = $this->calling_line;
        }

        if (isset($trace['line'])) {
            $this->calling_line = $trace['line'];
        }
        if (isset($trace['file'])) {
            $this->calling_file = $trace['file'];
        }

        return $return;
    }
}
