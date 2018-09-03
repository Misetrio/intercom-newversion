<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WpsolDashboard')) {
    require_once(WPSOL_PLUGIN_DIR . '/inc/wpsol-dashboard.php');
}
$lastest = get_option('wpsol_loadtime_lastest');
$default_img_dashboard = WPSOL_PLUGIN_URL .'css/images/default-dashboard.png';

$permalink = get_site_url();
$find = array( 'http://', 'https://' );
$replace = '';
$output = str_replace($find, $replace, $permalink);

$dashboard = new WpsolDashboard();
$checkdashboard = $dashboard->checkDashboard();
$checkoptimization = $dashboard->checkOptimization();

$icon = array(
        'success' => '<i class="material-icons top-field-icon-right success">check_circle</i>',
        'warning' => '<i class="material-icons top-field-icon-right info">info</i>',
        'notice' => '<img class="custom-material-icon-right" src="'.WPSOL_PLUGIN_URL.'css/images/icon-notification.png" />'
);

?>

<div class="wpsol-dashboard">
    <div class="header">
        <div class="title"><span><?php esc_html_e('Speedup Overview', 'wp-speed-of-light') ?></span></div>
        <div class="sub-title"><a href="<?php echo esc_url($output); ?>" ><?php echo esc_url($output); ?></a></div>
    </div>
    <div class="container">
        <div class="top-content">
            <div class="tc-left">
                <div class="image-dashboard tooltipped" data-position="top"
                     data-tooltip="<?php esc_html_e('Latest performance check page preview', 'wp-speed-of-light') ?>">
                    <!--IMAGE-->
                    <img class="" title="image-dashboard"
                         src="<?php echo esc_url((isset($lastest['first']['screenshot']) && !empty($lastest['first']['screenshot'])) ? $lastest['first']['screenshot'] : $default_img_dashboard) ?>" />
                </div>
            </div>
            <div class="tc-right">
                <div class="list-element-left">
                    <ul class="top-field">
                        <li class="tooltipped dashboad-link" data-position="top"
                            data-id="link-cacheactivation"
                            data-tooltip="<?php esc_html_e('Check for WP Speed of Light cache system activation', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <i class="material-icons top-field-icon-left">delete</i>
                                <span class="panel-title">
                                    <?php esc_html_e('Cache activation', 'wp-speed-of-light') ?>
                                </span>
                                <div class="link-area" id="link-cacheactivation">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>"><i class="material-icons">link</i></a>
                                </div>
                                <?php
                                //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                                echo $icon[$checkdashboard['cache']] ?>
                            </div>
                        </li>
                        <li class="tooltipped" data-position="top"
                            data-tooltip="<?php esc_html_e('Check if Gzip data compression is activated on your server, if not WP Speed of Light will force the activation calling an apache module', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <i class="material-icons top-field-icon-left">description</i>
                                <span class="panel-title gzip-panel">
                                    <?php esc_html_e('Gzip compression', 'wp-speed-of-light') ?>
                                </span>
                            </div>
                        </li>
                        <li class="tooltipped dashboad-link" data-position="top"
                            data-id="link-cacheclean"
                            data-tooltip="<?php esc_html_e('Check if database cleanup has been made recently or scheduled in the PRO ADDON', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <img class="custom-material-icon" src="<?php echo esc_url(WPSOL_PLUGIN_URL.'css/images/icon-cache-clean-up.png')?>" />
                                <span class="panel-title">
                                    <?php esc_html_e('Database cleanup', 'wp-speed-of-light') ?>
                                </span>
                                <div class="link-area" id="link-cacheclean">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#database_cleanup'))?>"><i class="material-icons">link</i></a>
                                </div>
                                <?php
                                //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                                echo $icon[$checkdashboard['cache-clean']] ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="list-element-right">
                    <ul class="top-field">
                        <li class="tooltipped" data-position="top"
                            data-tooltip="<?php esc_html_e('Your PHP version is: ', 'wp-speed-of-light') ?>
                            <?php echo esc_html(phpversion()) ?>
                            <?php esc_html_e('It’s better to use PHP7.2 because comparing to previous 5.6 versions the execution time of PHP 7.X is more than twice as fast and has 30 percent lower memory consumption. PHP 7.2 offer a small additional speed optimization', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <i class="material-icons top-field-icon-left">code</i>
                                <span class="panel-title">
                                    <?php esc_html_e('PHP Version', 'wp-speed-of-light') ?>
                                </span>
                                <?php
                                //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                                echo $icon[$checkdashboard['php-version']] ?>
                            </div>
                        </li>
                        <li class="tooltipped dashboad-link" data-position="top"
                            data-id="link-expires"
                            data-tooltip="<?php esc_html_e('Expires headers gives instruction to the browser whether it should request a specific file from the server or whether they should grab it from the browser\'s cache (it’s faster)', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <i class="material-icons top-field-icon-left">desktop_mac</i>
                                <span class="panel-title expires-panel">
                                    <?php esc_html_e('Expire headers', 'wp-speed-of-light') ?>
                                </span>
                                <div class="link-area" id="link-expires">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                        </li>
                        <li class="tooltipped dashboad-link" data-position="top"
                            data-id="link-restapi"
                            data-tooltip="<?php esc_html_e('Disable the WordPress REST API (API to retrieve data using GET requests, used by developers)', 'wp-speed-of-light') ?>">
                            <div class="panel-body">
                                <i class="material-icons top-field-icon-left">settings</i>
                                <span class="panel-title">
                                    <?php esc_html_e('Rest API', 'wp-speed-of-light') ?>
                                </span>
                                <div class="link-area" id="link-restapi">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#wordpress'))?>"><i class="material-icons">link</i></a>
                                </div>
                                <?php
                                //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                                echo $icon[$checkdashboard['rest']] ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="mid-content">
            <div class="mid-title">
                <span><?php esc_html_e('Latest performance check', 'wp-speed-of-light') ?></span>
                <?php if (!isset($lastest['first']['load-time'])) : ?>
                <div class="mid-link">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_analysis'))?>"><?php esc_html_e('Run a first test now >>', 'wp-speed-of-light') ?></a>
                </div>
                <?php endif; ?>
            </div>

            <div class="mid-panel">
                <div class="panel panel-1">
                    <div class="icon">
                        <i class="material-icons">alarm</i>
                    </div>
                    <ul>
                        <li><span class="title"><?php esc_html_e('Load time', 'wp-speed-of-light') ?></span></li>
                        <li><span class="detail"><?php echo (isset($lastest['first']['load-time'])) ? esc_html($lastest['first']['load-time'].' sec') : '-- : --' ?></span></li>
                    </ul>
                </div>
                <div class="panel panel-2">
                    <div class="icon">
                        <i class="material-icons">settings</i>
                    </div>
                    <ul>
                        <li><span class="title"><?php esc_html_e('First bytes', 'wp-speed-of-light') ?></span></li>
                        <li><span class="detail"><?php echo (isset($lastest['first']['first-byte'])) ? esc_html($lastest['first']['first-byte'].' sec') : '-- : --' ?></span></li>
                    </ul>
                </div>
                <div class="panel panel-3">
                    <div class="icon">
                        <i class="material-icons">description</i>
                    </div>
                    <ul>
                        <li><span class="title"><?php esc_html_e('Start render', 'wp-speed-of-light') ?></span></li>
                        <li><span class="detail"><?php echo (isset($lastest['first']['render'])) ? esc_html($lastest['first']['render'].' sec') : '-- : --' ?></span></li>
                    </ul>
                </div>
                <div class="panel panel-4">
                    <div class="icon">
                        <i class="material-icons">compare</i>
                    </div>
                    <ul>
                        <li><span class="title"><?php esc_html_e('Image Compression', 'wp-speed-of-light') ?></span></li>
                        <li><span class="detail"><?php echo (isset($lastest['first']['compression'])) ? esc_html(($lastest['first']['compression'] * 10). ' %') : '-- : --' ?></span></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="bot-content">
            <div class="bc-left">
                <div class="bot-title">
                    <span class="tooltipped" data-position="top"
                          data-tooltip="<?php esc_html_e('This is the next optimizations to go for a really better performance', 'wp-speed-of-light') ?>"><?php esc_html_e('Additional optimization', 'wp-speed-of-light') ?></span>
                </div>
                <div class="bot-panel">
                    <ul>
                        <li class="dashboad-link" data-id="link-imagecompression">
                            <div class="title">
                                <?php esc_html_e('Image compression', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-imagecompression">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#image_compression'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                            <div class="panel">
                                <span>
                                <?php
                                if ($checkoptimization['image_compression']) {
                                    esc_html_e('The image compression is activated. It helps to reduce your page size significantly while preserving the image quality', 'wp-speed-of-light');
                                } else {
                                    esc_html_e('The image compression is not activated. It helps to reduce your page size significantly while preserving the image quality', 'wp-speed-of-light');
                                } ?>
                                 </span>
                            </div>
                        </li>
                        <li class="dashboad-link" data-id="link-imagelazyload">
                            <div class="title">
                                <?php esc_html_e('Image lazy loading', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-imagelazyload">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                            <div class="panel">
                                <span><?php
                                if ($checkoptimization['lazy_loading']) {
                                    esc_html_e('Loading is activated. Load only images when it\'s visible in the by user (on scroll)', 'wp-speed-of-light');
                                } else {
                                    esc_html_e('Lazy loading is not activated. Load only images when it’s visible in the by user (on scroll)', 'wp-speed-of-light');
                                } ?>
                                </span>
                            </div>
                        </li>
                        <li class="dashboad-link" data-id="link-dbautoclean">
                            <div class="title">
                                <?php esc_html_e('Database auto cleanup', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-dbautoclean">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#database_cleanup'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                            <div class="panel">
                                <span><?php
                                if ($checkoptimization['database_clean']) {
                                    esc_html_e('Database automatic cleanup is activated. Database cleanup remove post revisions, trashed items, comment spam... up to 11 database optimization', 'wp-speed-of-light');
                                } else {
                                    esc_html_e('Database automatic cleanup is not activated. Database cleanup remove post revisions, trashed items, comment spam... up to 11 database optimization', 'wp-speed-of-light');
                                } ?>
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bc-center">
                <div class="bot-title">
                    <span class="tooltipped" data-position="top"
                          data-tooltip="<?php esc_html_e('Advanced optimization settings, require some deep tests on your website to advoid some plugin incompatility issues', 'wp-speed-of-light') ?>">
                        <?php esc_html_e('Advanced optimization', 'wp-speed-of-light') ?></span>
                </div>
                <div class="bot-panel">
                    <ul>
                        <li class="dashboad-link" data-id="link-fileminification">
                            <div class="title">
                                <?php esc_html_e('File minification', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-fileminification">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel">
                                <span>
                                <?php
                                if ($checkoptimization['minify_files']) {
                                     esc_html_e('At least one of your JS, CSS or HTML resources is currently minified', 'wp-speed-of-light');
                                } else {
                                     esc_html_e('None of your JS, CSS or HTML resources is currently minified', 'wp-speed-of-light');
                                }?>
                                </span>
                            </div>
                        </li>
                        <li class="dashboad-link" data-id="link-groupfiles">
                            <div class="title">
                                <?php esc_html_e('Group files', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-groupfiles">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel">
                                <span>
                                    <?php
                                    if ($checkoptimization['group_files']) {
                                        esc_html_e('At least one of your resources, CSS or JS, is currently grouped', 'wp-speed-of-light');
                                    } else {
                                        esc_html_e('None of you resources, CSS or JS, is currently grouped', 'wp-speed-of-light');
                                    }?>
                                </span>
                            </div>

                        </li>
                        <li class="dashboad-link" data-id="link-groupfonts">
                            <div class="title">
                                <?php esc_html_e('Group fonts', 'wp-speed-of-light') ?>
                                <div class="link-area" id="link-groupfonts">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>"><i class="material-icons">link</i></a>
                                </div>
                            </div>
                            <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                            <div class="panel">
                                <span><?php
                                if ($checkoptimization['group_fonts']) {
                                    esc_html_e('Local fonts Google fonts are properly grouped', 'wp-speed-of-light');
                                } else {
                                    esc_html_e('None of your local fonts and Google Fonts are grouped', 'wp-speed-of-light');
                                }?>
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bc-right">
                <div class="bot-title">
                    <span><?php esc_html_e('Other recommendations', 'wp-speed-of-light') ?></span>
                </div>
                <div class="bot-panel">
                    <ul>
                        <li>
                            <div class="panel">
                                <?php
                                if ($checkoptimization['plugins_enable'] >= 20) :
                                    ?>
                                    <span>
                                        <?php esc_html_e('You have more than 20 plugins installed and activated, the less you have better it is for your loading time', 'wp-speed-of-light') ?>
                                    </span>
                                    <?php
                                endif;
                                ?>
                                <?php
                                if ($checkoptimization['plugins_disable'] >= 5) :
                                    ?>
                                    <span>
                                        <?php esc_html_e('You have more than 5 plugins disabled, you may consider removing them if they’re not useful', 'wp-speed-of-light') ?>
                                    </span>
                                <?php endif; ?>

                                <?php
                                if ($checkoptimization['plugins_disable'] < 5 &&
                                    $checkoptimization['plugins_enable'] < 20) :
                                    ?>
                                    <span><?php esc_html_e('Everything look sparking-clean here', 'wp-speed-of-light') ?></span>
                                <?php endif; ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
