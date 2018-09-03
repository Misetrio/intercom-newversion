<?php
if (!defined('ABSPATH')) {
    exit;
}
$list_items = array(
    'Cache preloading',
    'Local and Google font optimization',
    'Visual file Inclusion/Exclusion',
    'File cache exclusion rules',
    'DNS Prefetching',
    'Database automatic cleanup',
    'Disable cache by user role',
    'CDN cache clean'
);
$image_src = WPSOL_PLUGIN_URL . 'inc/install-wizard/content/welcome-illustration.png';
?>
<div class="more-speedup">
    <div class="content-image">
        <img src="<?php echo esc_url($image_src); ?>" class="Illustration" />
    </div>
    <div class="content-title">
        <label><?php esc_html_e('Get an ultimate site speedup with', 'wp-speed-of-light') ?></label>
        <br>
        <label><?php esc_html_e('WP Speed of Light PRO ADDON', 'wp-speed-of-light') ?></label>
    </div>
    <div class="content">
        <div class="bc-left">
            <div class="bot-panel">
                <ul>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Image compression', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span><?php esc_html_e('It helps to reduce your page size significantly while preserving the image quality', 'wp-speed-of-light'); ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Image lazy loading', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Load only images when it’s visible in the by user (on scroll)', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Database auto cleanup', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Database cleanup remove post revisions, trashed items, comment spam... up to 11 database optimization', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Cache by user role', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Disable the cache per WordPress user role, useful for optimization or access to no cached website area', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('CDN Cache Cleanup', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Manual or automatic cache cleanup for Varnish, Cloudflare, KeyCDN, MaxCDN and Siteground', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Font Optimization', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Group local fonts and Google fonts in a single file to be served faster', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bc-right">
            <div class="bot-panel">
                <ul>
                    <li>
                        <div class="title">
                            <?php esc_html_e('File Inclusion/Exclusion', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Scan your files and select the ones you want to include/exclude
                                 from the Group and Minify process to avoid any conflicts', 'wp-speed-of-light') ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Cache Exclusion rules', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Exclude a set a set of URL from cache by using a simple rule', 'wp-speed-of-light'); ?>
                                </span>
                        </div>

                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Defer Script Loading', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Move all minified scripts to footer', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Cache preloading', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Preload cache of a selection of pages to be loaded faster', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('DNS Prefetching', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('Prepare DNS loading (Domain Name System) and when it comes time to use them, have a faster page loading time', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                    <li>
                        <div class="title">
                            <?php esc_html_e('Top-notch support', 'wp-speed-of-light') ?>
                        </div>
                        <div class="panel-addon"><?php esc_html_e('Pro addon feature', 'wp-speed-of-light') ?></div>
                        <div class="panel">
                                <span>
                                    <?php esc_html_e('2 levels technical support to help you with your optimization!', 'wp-speed-of-light'); ?>
                                </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="bottom-content">
        <a href="https://www.joomunited.com/wordpress-products/wp-speed-of-light" target="_blank" class="ju-button orange-button waves-effect waves-light wpsol-exclude-files-btn">
            <?php esc_html_e('Check our product page', 'wp-speed-of-light') ?>
        </a>
    </div>
</div>
