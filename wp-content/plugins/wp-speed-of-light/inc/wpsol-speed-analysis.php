<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolSpeedAnalysis
 */
class WpsolSpeedAnalysis
{
    /**
     * Test page - load time
     *
     * @return void
     */
    public static function loadPageTime()
    {
        $url = '';
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        if (isset($_POST['urlPage'])) {
            $url = $_POST['urlPage'];
        }
        $url = trim($url);
        $element = get_option('wpsol_loadpage_element');
        if (empty($element)) {
            $element = array(
                    'url' => '',
                    'keyAPI' => '',
                    'testID' => ''
            );
        }

        $conf = get_option('wpsol_configuration');
        $result = null;
        if (!empty($conf) && $conf['webtest_api_key'] !== '') {
            $keyAPI = $conf['webtest_api_key'];

            if ($element['url'] !== $url || $element['keyAPI'] !== $keyAPI) {
                $element['url'] = $url;
                $element['keyAPI'] = $keyAPI;
                $element['testID'] = self::getTestId($url, $keyAPI);
                update_option('wpsol_loadpage_element', $element);
            }

            if ($element['testID'] === 'Invalid API Key') {
                echo json_encode('wrong-key');
                exit;
            }

            if (!empty($element['testID'])) {
                $result = self::getResultPagetest($element['testID'], $url);
            }
        } else {
            $result = 'not-key';
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Connect with webpage test api
     *
     * @param string  $page     URL of page check
     * @param string  $key      Key of webpage test api
     * @param boolean $run_time Run time
     * @param boolean $type     Type check
     *
     * @return string
     */
    public static function getTestId($page, $key, $run_time = false, $type = false)
    {
        $testID = '';
        if (!$type) {
            $type = 'xml';
        }
        if (!$run_time) {
            $run_time = 1;
        }
        $runTest = 'http://www.webpagetest.org/runtest.php?url=' .
            $page . '&runs=' . $run_time . '&f=' . $type . '&k=' . $key;
        $response = wp_remote_get($runTest);

        if (is_array($response)) {
            $xmlres = simplexml_load_string($response['body']);

            if ($xmlres) {
                if ((string)$xmlres->statusText === 'Ok') {
                    $testID = (string)$xmlres->data->testId;
                }

                if ((string)$xmlres->statusText === 'Invalid API Key') {
                    $testID = 'Invalid API Key';
                }
            }
        }
        return $testID;
    }

    /**
     * Get result from pagetest api
     *
     * @param string $idTest Id of pagetest
     * @param string $url    URL of page
     *
     * @return integer|null|string
     */
    public static function getResultPagetest($idTest, $url)
    {
        $status = null;
        $analysis = get_option('wpsol_loadtime_lastest');
        if (empty($analysis)) {
            $analysis = array(
                'url' => '',
                'average-loading' => 0,
                'first' => array(
                    'load-time' => 0,
                    'first-byte' => 0,
                    'render' => 0,
                    'caching' => 0,
                    'gzip' => 0,
                    'compression' => 0,
                    'thumbnail' => '',
                    'screenshot' => ''
                ),
                'second' => array(
                    'load-time' => 0,
                    'first-byte' => 0,
                    'render' => 0,
                    'caching' => 0,
                    'gzip' => 0,
                    'compression' => 0
                ));
        }

        //test page
        $urlTest = 'http://www.webpagetest.org/xmlResult/' . $idTest . '/';
        $response = wp_remote_get($urlTest);
        if (is_array($response)) {
            $xmlResult = simplexml_load_string($response['body']);
            /**
             * Action called after a page analysis has been completed on webpagetest.org
             *
             * @param object XML result from webpagetest
             */
            do_action('wpsol_retrieve_raw_page_analysis', $xmlResult);

            if ($xmlResult) {
                $status = (int)$xmlResult->statusCode;
            }
        }
        if ($status < 200) {
            return $status;
        } elseif ($status === 200) {
            $date = date('Y-m-d H:i:s');
            $analysis['url'] = (string)$xmlResult->data->testUrl;
            $analysis['average-loading'] = round(((int)$xmlResult->data->average->firstView->loadTime) / 1000, 3);
            $analysis['first']['load-time'] = round(
                ((int)$xmlResult->data->run[0]->firstView->results->loadTime) / 1000,
                3
            );
            $analysis['first']['first-byte'] = round(
                ((int)$xmlResult->data->run[0]->firstView->results->TTFB) / 1000,
                3
            );
            $analysis['first']['render'] = round(
                ((int)$xmlResult->data->run[0]->firstView->results->render) / 1000,
                3
            );
            $analysis['first']['caching'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->firstView->results->score_cache)
            );
            $analysis['first']['gzip'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->firstView->results->score_gzip)
            );
            $analysis['first']['compression'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->firstView->results->score_compress)
            );
            $analysis['first']['thumbnail'] = (string)$xmlResult->data->run[0]->firstView->thumbnails->screenShot;
            $analysis['first']['screenshot'] = (string)$xmlResult->data->run[0]->firstView->images->screenShot;
            $analysis['second']['load-time'] = round(
                ((int)$xmlResult->data->run[0]->repeatView->results->loadTime) / 1000,
                3
            );
            $analysis['second']['first-byte'] = round(
                ((int)$xmlResult->data->run[0]->repeatView->results->TTFB) / 1000,
                3
            );
            $analysis['second']['render'] = round(
                ((int)$xmlResult->data->run[0]->repeatView->results->render) / 1000,
                3
            );
            $analysis['second']['caching'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->repeatView->results->score_cache)
            );
            $analysis['second']['gzip'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->repeatView->results->score_gzip)
            );
            $analysis['second']['compression'] = self::setStarRating(
                (int)($xmlResult->data->run[0]->repeatView->results->score_compress)
            );

            update_option('wpsol_loadtime_lastest', $analysis);

            //get 10 lastest speed test
            $lastest = get_option('wpsol_loadtime_result');

            if (empty($lastest)) {
                $lastest[] = array(
                    'url' => self::cutUrl($xmlResult),
                    'thumbnail' => (string)$xmlResult->data->run[0]->firstView->thumbnails->screenShot,
                    'load-time' => round(((int)$xmlResult->data->run[0]->firstView->results->loadTime) / 1000, 3),
                    'first-byte' => round(((int)$xmlResult->data->run[0]->firstView->results->TTFB) / 1000, 3),
                    'render' => round(((int)$xmlResult->data->run[0]->firstView->results->render) / 1000, 3),
                    'caching' => (int)($xmlResult->data->run[0]->firstView->results->score_cache),
                    'gzip' => (int)($xmlResult->data->run[0]->firstView->results->score_gzip),
                    'compression' => (int)($xmlResult->data->run[0]->firstView->results->score_compress),
                    'date' => $date,
                );

                update_option('wpsol_loadtime_result', $lastest);
            } else {
                $median = array();
                $median['url'] = self::cutUrl($xmlResult);
                $median['thumbnail'] = (string)$xmlResult->data->run[0]->firstView->thumbnails->screenShot;
                $median['load-time'] = round(((int)$xmlResult->data->run[0]->firstView->results->loadTime) / 1000, 3);
                $median['first-byte'] = round(((int)$xmlResult->data->run[0]->firstView->results->TTFB) / 1000, 3);
                $median['render'] = round(((int)$xmlResult->data->run[0]->firstView->results->render) / 1000, 3);
                $median['caching'] = (int)($xmlResult->data->run[0]->firstView->results->score_cache);
                $median['gzip'] = (int)($xmlResult->data->run[0]->firstView->results->score_gzip);
                $median['compression'] = (int)($xmlResult->data->run[0]->firstView->results->score_compress);
                $median['date'] = $date;

                array_push($lastest, $median);
                if (count($lastest) > 10) {
                    array_shift($lastest);
                }

                update_option('wpsol_loadtime_result', $lastest);
            }

            /**
             * Action called after a page analysis has been completed on webpagetest.org and processed by WP Speed Of Light
             *
             * @param array Analysis result
             */
            do_action('wpsol_retrieve_page_analysis', $lastest);

            return $status;
        } elseif ($status > 200) {
            return $status;
        } else {
            return 'null';
        }
    }

    /**
     * Cut url for xml
     *
     * @param string $xmlResult Result return xml
     *
     * @return mixed|string
     */
    public static function cutUrl($xmlResult)
    {
        $testurl = (string)$xmlResult->data->testUrl;
        $a = strpos($testurl, 'wpsol');
        if ($a !== false) {
            $sub = substr($testurl, $a - 1);
            $testurl = str_replace($sub, '', $testurl);
        }
        return $testurl;
    }

    /**
     * Set value star rating
     *
     * @param integer $xmlEle Element return xml
     *
     * @return integer
     */
    public static function setStarRating($xmlEle)
    {
        $result = 0;
        if ($xmlEle < 0) {
            $result = 0;
        } elseif (0 < $xmlEle && $xmlEle <= 10) {
            $result = 1;
        } elseif (10 < $xmlEle && $xmlEle <= 20) {
            $result = 2;
        } elseif (20 < $xmlEle && $xmlEle <= 30) {
            $result = 3;
        } elseif (30 < $xmlEle && $xmlEle <= 40) {
            $result = 4;
        } elseif (40 < $xmlEle && $xmlEle <= 50) {
            $result = 5;
        } elseif (50 < $xmlEle && $xmlEle <= 60) {
            $result = 6;
        } elseif (60 < $xmlEle && $xmlEle <= 70) {
            $result = 7;
        } elseif (70 < $xmlEle && $xmlEle <= 80) {
            $result = 8;
        } elseif (80 < $xmlEle && $xmlEle <= 90) {
            $result = 9;
        } elseif (90 < $xmlEle) {
            $result = 10;
        }
        return $result;
    }

    /**
     *  Delete details
     *
     * @return void
     */
    public static function deleteDetails()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $url = $_POST['url'];
        $analysis = get_option('wpsol_loadtime_result');
        foreach ($analysis as $k => $v) {
            if ($v['url'] === $url) {
                unset($analysis[$k]);
            }
        }
        update_option('wpsol_loadtime_result', $analysis);
        echo(esc_html(md5($url)));
        exit;
    }

    /**
     * Get result of scan queries
     *
     * @return array
     */
    public function getInfoQueries()
    {
        $result = array('theme' => array(
            'load_time' => 0,
            'type' => array(
                'SELECT' => 0,
                'UPDATE' => 0,
                'SHOW' => 0,
                'INSERT' => 0,
                'DESCRIBE' => 0
            ),
        ),
            'core' => array(
                'load_time' => 0,
                'type' => array(
                    'SELECT' => 0,
                    'UPDATE' => 0,
                    'SHOW' => 0,
                    'INSERT' => 0,
                    'DESCRIBE' => 0
                ),
            ),
            'plugin' => array(
                'total_plugin' => 0,
                'load_time' => 0,
                'details' => array(),
            ));
        $queries = get_option('wpsol_scan_queries');
        if (!empty($queries)) {
            foreach ($queries['dbs']['$wpdb']->rows as $row) {
                $i = 0;
                //get theme
                $compare_theme = strpos($row['stack'], 'theme');
                $compare_core1 = strpos($row['stack'], 'wp-load');
                $compare_core2 = strpos($row['stack'], 'wp-settings');
                $compare_core3 = strpos($row['stack'], 'wp-config');
                $compare_core4 = strpos($row['stack'], 'wp-admin');
                $compare_core5 = strpos($row['stack'], 'wp-blog-header');
                $compare_plugin = strpos($row['stack'], 'plugins');
//            var_dump($compare_plugin);
                if ($compare_theme !== false) {
                    $result['theme']['load_time'] += round($row['ltime'], 5);

                    switch ($row['type']) {
                        case 'SELECT':
                            $i++;
                            $result['theme']['type']['SELECT'] += $i;
                            break;
                        case 'SHOW':
                            $i++;
                            $result['theme']['type']['SHOW'] += $i;
                            break;
                        case 'INSERT':
                            $i++;
                            $result['theme']['type']['INSERT'] += $i;
                            break;
                        case 'UPDATE':
                            $i++;
                            $result['theme']['type']['UPDATE'] += $i;
                            break;
                        case 'DESCRIBE':
                            $i++;
                            $result['theme']['type']['DESCRIBE'] += $i;
                            break;
                    }
                } elseif ($compare_core1 !== false || $compare_core2 !== false ||
                    $compare_core3 !== false || $compare_core4 !== false || $compare_core5 !== false) {
                    $result['core']['load_time'] += round($row['ltime'], 5);

                    switch ($row['type']) {
                        case 'SELECT':
                            $i++;
                            $result['core']['type']['SELECT'] += $i;
                            break;
                        case 'SHOW':
                            $i++;
                            $result['core']['type']['SHOW'] += $i;
                            break;
                        case 'INSERT':
                            $i++;
                            $result['core']['type']['INSERT'] += $i;
                            break;
                        case 'UPDATE':
                            $i++;
                            $result['core']['type']['UPDATE'] += $i;
                            break;
                        case 'DESCRIBE':
                            $i++;
                            $result['core']['type']['DESCRIBE'] += $i;
                            break;
                    }
                }
                if ($compare_plugin !== false) {
                    $stacks = explode(',', $row['stack']);
                    foreach ($stacks as $stack) {
                        if (strpos($stack, 'plugins') !== false) {
                            $str = strstr($stack, 'plugins');
                            $str = rtrim($str, "')");
                            $str = substr($str, 8);
                            $arr = explode('\\', $str);
                            $result['plugin']['details'][$arr[0]]['load_time'] = round($row['ltime'], 5);
                            $result['plugin']['details'][$arr[0]]['type'] = array(
                                'SELECT' => 0,
                                'SHOW' => 0,
                                'INSERT' => 0,
                                'UPDATE' => 0,
                                'DESCRIBE' => 0,
                            );
                            switch ($row['type']) {
                                case 'SELECT':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['SELECT'] += $i;
                                    break;
                                case 'SHOW':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['SHOW'] += $i;
                                    break;
                                case 'INSERT':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['INSERT'] += $i;
                                    break;
                                case 'UPDATE':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['UPDATE'] += $i;
                                    break;
                                case 'DESCRIBE':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['DESCRIBE'] += $i;
                                    break;
                            }
                            $result['plugin']['load_time'] = array_sum($result['plugin']['details'][$arr[0]]);
                        }
                    }
                    $result['plugin']['total_plugin'] = count($result['plugin']['details']);
                }
            }
        }
        update_option('wpsol_database_queries_analysis', $result);
        return $result;
    }

    /**
     *  Scan tab 2
     *
     * @return void
     */
    public static function startScanQuery()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $filename = sanitize_file_name(basename($_POST['wpsol_scan_name_query']));
        // filename option
        $opt = get_option('wpsol_profiles_option');
        if (empty($opt) || !is_array($opt)) {
            $opt = array();
            $flag = false;
        } else {
            $flag = true;
        }
        $opt['query_enabled'] = array(
            'name' => $filename,
        );
        update_option('wpsol_profiles_option', $opt);

        if (false === $flag) {
            self::ajaxDie(0);
        } else {
            self::ajaxDie(1);
        }
    }

    /**
     * Stop scan tab2
     *
     * @return void
     */
    public static function stopScanQuery()
    {
        $opts = get_option('wpsol_profiles_option');
        // Turn off scanning
        $opts['query_enabled'] = false;
        update_option('wpsol_profiles_option', $opts);
        if (!empty($opts) && is_array($opts) && array_key_exists('name', $opts)) {
            self::ajaxDie('');
        } else {
            self::ajaxDie(0);
        }
    }

    /**
     * Stop ajax
     *
     * @param string $message Message display
     *
     * @return void
     */
    public static function ajaxDie($message)
    {
        global $wp_version;
        if (version_compare($wp_version, '3.4') >= 0) {
            wp_die(esc_html($message));
        } else {
            die(esc_html($message));
        }
    }

    /**
     * Display star rating
     *
     * @param integer $check Number check width
     * @param string  $type  Type of star rating
     *
     * @return void
     */
    public function starRating($check, $type)
    {
        ?>
        <div class="progress-rating">
            <div class="determinate" style="width: <?php echo esc_attr($check * 10); ?>%"></div>
        </div>
        <?php
    }

    /**
     * Display more details
     *
     * @return void
     */
    public static function moreDetails()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $output = '';
        if (isset($_POST['url'])) {
            $url = $_POST['url'];
        }
        $loadtimes = get_option('wpsol_loadtime_result');
        foreach ($loadtimes as $v) {
            if ($v['url'] === $url) {
                $output .= '<tr><th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Thumbnail details', 'wp-speed-of-light') . '">' .
                    __('Thumbnail', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Url of Page details', 'wp-speed-of-light') . '">' .
                    __('Url', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Time to load page details', 'wp-speed-of-light') . '">' .
                    __('Load time', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Time to first byte details', 'wp-speed-of-light') . '">' .
                    __('First bytes', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Time to start render details', 'wp-speed-of-light') . '">' .
                    __('Start render', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Score caching details', 'wp-speed-of-light') . '">' .
                    __('Caching', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Score gzip details', 'wp-speed-of-light') . '">' .
                    __('Gzip', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Score image compression details', 'wp-speed-of-light') . '">' .
                    __('Compression', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Date scan details', 'wp-speed-of-light') . '">' .
                    __('Date', 'wp-speed-of-light') . '</th></tr>';

                $output .= '<tr><td><img src="' . $v['thumbnail'] . '"></td>';
                $output .= '<td><a href="' . $v['url'] . '" target="_blank">' . $v['url'] . '</a></td>';
                $output .= '<td>' . $v['load-time'] . '&nbsps</td>';
                $output .= '<td>' . $v['first-byte'] . '&nbsps</td>';
                $output .= '<td>' . $v['render'] . '&nbsps</td>';
                $output .= '<td>' . $v['caching'] . '/100</td>';
                $output .= '<td>' . $v['gzip'] . '/100</td>';
                $output .= '<td>' . $v['compression'] . '/100</td>';
                $output .= '<td>' . $v['date'] . '</td>';
            }
        }
        echo json_encode('<table class="wpsol-table-detail" style="width:100%;border-collapse: collapse;">' .
            $output .
            '</table>');
        exit;
    }

    /**
     * Get resulte of total query
     *
     * @param string $queriesParameter Query get parameter
     * @param string $method           Method of query
     *
     * @return integer
     */
    public function getTotalResultQueries($queriesParameter, $method)
    {
        $type = 0;
        $type += $queriesParameter['theme']['type'][$method];
        $type += $queriesParameter['core']['type'][$method];
        foreach ($queriesParameter['plugin']['details'] as $k => $v) {
            $type += $v['type'][$method];
        }
        return $type;
    }
}
