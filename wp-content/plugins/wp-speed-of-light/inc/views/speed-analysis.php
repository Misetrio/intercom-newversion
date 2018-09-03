<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpsolSpeedAnalysis')) {
    require_once(WPSOL_PLUGIN_DIR . '/inc/wpsol-speed-analysis.php');
}

$wpsol_SpeedAnalysis = new WpsolSpeedAnalysis;
$queriesParameter = $wpsol_SpeedAnalysis->getInfoQueries();
$lastest = get_option('wpsol_loadtime_lastest');
$element = get_option('wpsol_loadpage_element');
$loadtime_result = get_option('wpsol_loadtime_result');
$conf = get_option('wpsol_configuration');
if (!empty($loadtime_result)) {
    $loadtime_result = array_reverse($loadtime_result);
}

// total result queries
if (!empty($queriesParameter)) {
    $plugin_time = 0;
    $select = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'SELECT');
    $show = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'SHOW');
    $update = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'UPDATE');
    foreach ($queriesParameter['plugin']['details'] as $k => $v) {
        $plugin_time += $v['load_time'];
    }
    $time = $queriesParameter['theme']['load_time'] + $queriesParameter['core']['load_time'] + $plugin_time;
}
$active_plugins = 0;
// count total plugin
$active_plugins = count(get_mu_plugins());
foreach (get_plugins() as $plugin => $junk) {
    if (is_plugin_active($plugin)) {
        $active_plugins++;
    }
}

?>
<div id="wpsol-speed-analysis">
    <div class="ju-main-wrapper" style="margin: 0">
        <div class="ju-right-panel" style="margin: 0; width: auto;background: transparent;" >
            <div class="ju-top-tabs-wrapper">
                <ul class="tabs ju-top-tabs horizontal-tabs">
                    <li class="tab">
                        <a class="link-tab waves-effect waves-light" href="#speedtest">
                            <?php esc_html_e('Loading time', 'wp-speed-of-light') ?>
                        </a>
                    </li>
                    <li class="tab">
                        <a class="link-tab waves-effect waves-light" href="#analysis">
                            <?php esc_html_e('Database queries', 'wp-speed-of-light') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="speedtest" class="tab-content">
        <!--analysis-->
        <div class="tab-analysis-content">
            <div class="header-analysis">
                <div class="title"><?php esc_html_e('Speed analysis', 'wp-speed-of-light') ?></div>
                <div class="panel">
                    <div class="panel-content">
                        <label for="insert-url"
                               class="insert-url"><?php esc_html_e('URL to analyse : ', 'wp-speed-of-light') ?>
                            <?php echo esc_url(home_url()) . '/'; ?></label>
                        <div class="panel-input">
                            <input id="insert-url" type="text" placeholder="<?php esc_html_e('Typing here...', 'wp-speed-of-light') ?>"
                                   name="wpsol_url_speed" value="" class="wpsol_url_speed ju-input"/>
                            <input id="old-url" type="text" readonly="true"
                                   data-url="<?php echo (!empty($element)) ? esc_url($element['url']) : ''; ?>" style="display: none;"/>
                            <input id="main-url" type="text" readonly="true" data-url="<?php echo esc_url(home_url()); ?>"
                                   style="display: none;"/>
                            <input id="speed-button" type="button" value="<?php esc_html_e('Launch speed test', 'wp-speed-of-light') ?>"
                                   name="loadtime-button" class="btn waves-effect waves-light btn-analysis"/>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="message">
                <div id="message-scan" style="display: none; margin-top:20px;" class="notice notice-success">
                    <strong><?php esc_html_e('The speed test is running… it may takes between 1 and 5 minutes,
             keep calm and stay cool :)', 'wp-speed-of-light'); ?>
                    </strong>
                </div>
                <!--progess-->
                <div class="scan-test-progress progress" style="display:none">
                    <div class="indeterminate"></div>
                </div>
                <?php if (!isset($conf['webtest_api_key']) ||
                          (isset($conf['webtest_api_key']) &&
                           $conf['webtest_api_key'] === '')) : ?>
                    <div id="message-not-key" style="" class=" notice notice-success">
                        <?php esc_html_e('To run speed test you need to add into the configuration
                 a free WebPagetest API key ', 'wp-speed-of-light') ?>
                        <a id="register-key" class="btn waves-effect waves-light waves-input-wrapper"
                           href="https://www.webpagetest.org/getkey.php"
                           target="_blank"><?php esc_html_e('GET IT NOW', 'wp-speed-of-light') ?></a> <br><br>
                        <?php esc_html_e('Run free website speed test using real browsers and at real consumer connection speeds.
                 Your results will provide diagnostic information including resources,
                  loading time and optimization.', 'wp-speed-of-light') ?>
                    </div>
                <?php endif; ?>
                <div id="message-error-scan" style="display: none; padding: 10px;" class="notice notice-warning">
                    <strong><?php esc_html_e('An error occurred during the scan . Please check again', 'wp-speed-of-light'); ?>
                        <br>
                        <?php esc_html_e('Please note that scan can’t work for local environments', 'wp-speed-of-light'); ?></strong>
                </div>
                <div id="message-error-apikey-scan" style="display: none; padding: 10px;" class="notice notice-warning">
                    <strong>
                        <?php esc_html_e('Your API key seems invalid, please double check the key or generate a new one', 'wp-speed-of-light'); ?>
                    </strong>
                </div>
            </div>

            <div class="result-content analysis-result-content">
                <div class="box-result">
                    <ul>
                        <li class="col2">
                            <div class="icon icon-1">
                                <i class="material-icons">settings_input_hdmi</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Total plugins ', 'wp-speed-of-light') ?></div>
                                <div class="number blue"><?php echo esc_html($active_plugins); ?></div>
                                <div class="note"><?php esc_html_e('The number of plugins currently activated', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                        <li class="col2">
                            <div class="icon icon-2">
                                <i class="material-icons">alarm</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Loading time', 'wp-speed-of-light') ?></div>
                                <div class="number green"><?php echo (isset($lastest['average-loading'])) ? esc_html($lastest['average-loading']) : 0; ?>
                                    &nbsp;<?php esc_html_e('sec', 'wp-speed-of-light'); ?></div>
                                <div class="note"><?php esc_html_e('Lastest speed analysis test result', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="lastest-details">
                    <div class="loadtime-title"><?php esc_html_e('Lastest analysis details', 'wp-speed-of-light') ?></div>
                    <table width="100%" class="lastest-details-table" style="border-collapse: collapse;">
                        <tr>
                            <th class="tooltipped" data-position="bottom"
                                 data-tooltip="<?php esc_html_e('Name', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Name', 'wp-speed-of-light') ?>
                            </th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Load time', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Load time', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('First byte', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('First byte', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Time start render', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Start render', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Score caching', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 45px;'>
                                <?php esc_html_e('Caching', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Score gzip', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 55px;'>
                                <?php esc_html_e('Gzip', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Score image compresssion', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 15px;'>
                                <?php esc_html_e('Image compression', 'wp-speed-of-light') ?></th>
                        </tr>
                        <tr class="tooltipped" data-position="top"
                            data-tooltip="<?php esc_html_e('This is first time when
                         page loaded', 'wp-speed-of-light') ?>">
                            <td style="border-bottom: 1px solid #EEEEEE;">
                                <?php esc_html_e('First load', 'wp-speed-of-light') ?></td>
                            <td><?php echo esc_html($lastest['first']['load-time']); ?></td>
                            <td><?php echo esc_html($lastest['first']['first-byte']); ?></td>
                            <td><?php echo esc_html($lastest['first']['render']); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['first']['caching'], 'first-caching'); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['first']['gzip'], 'first-gzip'); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['first']['compression'], 'first-compression'); ?></td>
                        </tr>
                        <tr class="tooltipped" data-position="top"
                            data-tooltip="<?php esc_html_e('This is second time when
                         page loaded', 'wp-speed-of-light') ?>">
                            <td style="border-bottom: 1px solid #EEEEEE;">
                                <?php esc_html_e('Second load', 'wp-speed-of-light') ?></td>
                            <td><?php echo esc_html($lastest['second']['load-time']); ?></td>
                            <td><?php echo esc_html($lastest['second']['first-byte']); ?></td>
                            <td><?php echo esc_html($lastest['second']['render']); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['second']['caching'], 'second-caching'); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['second']['gzip'], 'second-gzip'); ?></td>
                            <td><?php $wpsol_SpeedAnalysis->starRating($lastest['second']['compression'], 'second-compression'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="lastest-speed">
                    <div class="loadtime-title"><?php esc_html_e('10 lastest speed tests', 'wp-speed-of-light') ?></div>
                    <table width="100%" class="lastest-details-table  ten-latest-table" id="ten-details"
                           style="border-collapse: collapse;">
                        <tr>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Thumbnail', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Thumbnail', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Url', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('URL', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Load time', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Load time', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Details', 'wp-speed-of-light') ?>"></th>
                        </tr>
                        <?php if (!empty($loadtime_result)) : ?>
                            <?php foreach ($loadtime_result as $v) : ?>
                                <tr id="<?php echo esc_html(md5($v['url'])); ?>">
                                    <td style="text-align: center;width:25%"><img src="<?php echo esc_url($v['thumbnail']) ?>"/>
                                    </td>
                                    <td style="text-align: left;width:35%"><a href="<?php echo esc_url($v['url']) ?>"
                                                                              style="text-decoration: none;"
                                                                              target="_blank"><?php echo esc_url($v['url']) ?></a>
                                    </td>
                                    <td style="text-align: center;width:15%"><?php echo esc_html($v['load-time']) ?> sec</td>
                                    <td style="width:15% ;"><input type="button"
                                                                   value="<?php esc_html_e('More details', 'wp-speed-of-light') ?>"
                                                                   data-url="<?php echo esc_url($v['url']); ?>"
                                                                   class="wpsol-more-details btn waves-effect waves-light"/>
                                    </td>
                                    <td style="width:10% ;">
                                        <img src="<?php echo esc_url(WPSOL_PLUGIN_URL . 'css/images/icon-delete.svg')?>"
                                             alt="Delete icon"
                                             class="clear-test tooltipped" data-position="top"
                                             data-tooltip="<?php esc_html_e('Remove speed test', 'wp-speed-of-light') ?>"
                                             data-url="<?php echo esc_url($v['url']); ?>"
                                        />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div id="analysis" class="tab-content">
        <div class="tab-analysis-content">
            <div class="header-analysis">
                <div class="title"><?php esc_html_e('Speed analysis', 'wp-speed-of-light') ?></div>
                <div class="panel">
                    <div class="panel-content">
                        <label for="insert-url-queries"
                               class="insert-url"><?php esc_html_e('URL to analyse : ', 'wp-speed-of-light') ?>
                            <?php echo esc_url(home_url()) . '/'; ?></label>
                        <div class="panel-input">
                            <input type="hidden" value="<?php echo esc_url(home_url()); ?>" id="main-url-queries"/>
                            <input id="insert-url-queries" type="text" placeholder="<?php esc_html_e('Typing here...', 'wp-speed-of-light') ?>"
                                   name="wpsol_url_queries" value="" class="wpsol_url_queries"/>
                            <input id="query-button" type="button" value="<?php esc_html_e('Launch analysis', 'wp-speed-of-light') ?>"
                                   class="btn waves-effect waves-light btn-analysis"/>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="result-content">
                <!--box result-->
                <div class="box-result">
                    <ul>
                        <li class="col3">
                            <div class="icon icon-1">
                                <i class="material-icons">settings_input_hdmi</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Total plugins ', 'wp-speed-of-light') ?></div>
                                <div class="number blue"><?php echo esc_html($queriesParameter['plugin']['total_plugin']); ?></div>
                                <div class="note"><?php esc_html_e('Queries time =', 'wp-speed-of-light') ?>
                                    &nbsp;<?php echo ($plugin_time) ? esc_html($plugin_time) : 0; ?>
                                    &nbsp;<?php esc_html_e('sec', 'wp-speed-of-light'); ?></div>
                            </div>
                        </li>
                        <li class="col3">
                            <div class="icon icon-2">
                                <i class="material-icons">import_contacts</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Theme ', 'wp-speed-of-light') ?></div>
                                <div class="number green"><?php echo esc_html($queriesParameter['theme']['load_time']); ?>
                                    <?php esc_html_e('Sec', 'wp-speed-of-light'); ?></div>
                                <div class="note"><?php esc_html_e('Lastest speed analysis test result', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                        <li class="col3">
                            <div class="icon icon-3">
                                <span class="dashicons dashicons-wordpress"></span>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('WP Core ', 'wp-speed-of-light') ?></div>
                                <div class="number red"><?php echo esc_html($queriesParameter['core']['load_time']); ?>
                                    <?php esc_html_e('Sec', 'wp-speed-of-light'); ?></div>
                                <div class="note"><?php esc_html_e('WP Core Queries time', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!--table plugins-->
                <div class="table-queries">
                    <table id="table-sorter-queries" class="tablesorter" align="center">
                        <thead>
                        <tr>
                            <th class="top-header"><?php esc_html_e('WordPress & Plugins', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Select', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Show', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Update', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Time', 'wp-speed-of-light'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($queriesParameter as $k => $v) :
                            if ($k === 'theme' || $k === 'core') :
                                ?>
                                <tr>
                                    <td class="top-header" style="text-transform: capitalize"><?php echo esc_html($k); ?></td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['SELECT'])) {
                                            echo esc_html($v['type']['SELECT']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['SHOW'])) {
                                            echo esc_html($v['type']['SHOW']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['UPDATE'])) {
                                            echo esc_html($v['type']['UPDATE']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($v['load_time']); ?>
                                    </td>
                                </tr>

                                <?php
                            endif;
                            if ($k === 'plugin') :
                                foreach ($v['details'] as $key => $value) :
                                    ?>
                                    <tr>
                                        <td class="top-header"><?php echo 'Plugin: ' . esc_html($key); ?></td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['SELECT'])) {
                                                echo esc_html($value['type']['SELECT']);
                                            } else {
                                                echo 0;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['SHOW'])) {
                                                echo esc_html($value['type']['SHOW']);
                                            } else {
                                                echo 0;
                                            }

                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['UPDATE'])) {
                                                echo esc_html($value['type']['UPDATE']);
                                            } else {
                                                echo 0;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($value['load_time']); ?>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            endif;
                        endforeach;
                        ?>

                        </tbody>
                        <tr>
                            <td></td>
                            <td><?php echo ($select) ? esc_html($select) : 0; ?></td>
                            <td><?php echo ($show) ? esc_html($show) : 0; ?></td>
                            <td><?php echo ($update) ? esc_html($update) : 0; ?></td>
                            <td><?php echo ($time) ? esc_html($time) : 0; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dialog for iframe scanner -->
<div id="wpsol-scanner-dialog" class="wpsol-dialog">
    <iframe id="wpsol-scan-frame" frameborder="0"
            data-defaultsrc="">
    </iframe>
    <div id="wpsol-scan-caption">
        <?php esc_html_e('The scanner will analyze the speed and resource usage of all active plugins on your website.
         It may take several minutes, and this window must
         remain open for the scan to finish successfully.', 'wp-speed-of-light'); ?>
    </div>
</div>

<!-- Dialog for progress bar -->
<div id="wpsol-progress-dialog" class="wpsol-dialog">
    <div id="wpsol-scanning-caption">
        <?php esc_html_e('Scanning ...', 'wp-speed-of-light'); ?>
    </div>
    <div id="wpsol-progress"></div>

    <!-- View results button -->
    <div class="wpsol-big-button" id="wpsol-view-results-buttonset" style="display: none;">
        <input type="checkbox" id="wpsol-view-results-submit" class="view-results-button" checked="checked"
               data-scan-name=""/>
        <label for="wpsol-view-results-submit"
               class="btn waves-effect waves-light"><?php esc_html_e('View Results', 'wp-speed-of-light'); ?></label>
    </div>
</div>

<!-- Dialog for more details -->

<div id="wpsol-more-details-dialog" class="wpsol-modal" style="display: none;">
    <span class="wpsol-close">×</span>
    <div class="wpsol-modal-content">
    </div>
</div>
