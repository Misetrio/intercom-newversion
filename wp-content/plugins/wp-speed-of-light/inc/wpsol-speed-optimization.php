<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolSpeedOptimization
 */
class WpsolSpeedOptimization
{

    /**
     * WpsolSpeedOptimization constructor.
     */
    public function __construct()
    {
    }

    /**
     *  Sett action for class
     *
     * @return void
     */
    public function setAction()
    {

        add_action('load-wp-speed-of-light_page_wpsol_speed_optimization', array($this, 'saveSettings'));

        if (class_exists('WpsolAddonSpeedOptimization')) {
            /**
             * Filter check clean up on save.
             *
             * @param boolean Default value
             *
             * @internal
             *
             * @return boolean
             */
            $check_on_save = apply_filters('wpsol_addon_check_cleanup_on_save', false);

            if ($check_on_save) {
                add_action('pre_post_update', array($this, 'purgePostOnUpdate'), 10, 1);
                add_action('save_post', array($this, 'purgePostOnUpdate'), 10, 1);
            }
        } else {
            add_action('pre_post_update', array($this, 'purgePostOnUpdate'), 10, 1);
            add_action('save_post', array($this, 'purgePostOnUpdate'), 10, 1);
        }

        add_action('wp_trash_post', array($this, 'purgePostOnUpdate'), 10, 1);
        add_action('comment_post', array($this, 'purgePostOnNewComment'), 10, 3);
        add_action('wp_set_comment_status', array($this, 'purgePostOnCommentStatusChange'), 10, 2);
        add_action('set_comment_cookies', array($this, 'setCommentCookieExceptions'), 10, 2);

        // Remove query strings
        add_filter('wpsol_query_strings_return', array($this, 'removeQueryStrings'));
    }



    /**
     * Action when save settings optimization
     *
     * @return void
     */
    public function saveSettings()
    {
        if (current_user_can('manage_options')) {
            WP_Filesystem();

            $opts = get_option('wpsol_optimization_settings');
            //save setting speed optimization
            //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- Check admin referer exist, because have 3 action in function
            if (isset($_REQUEST['action'])) {
                check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');
                if ('wpsol_save_speedup' === $_REQUEST['action']) {
                    $this->saveSpeedUp($opts);

                    WpsolCache::factory()->write();
                    //write config for cache
                    WpsolCache::factory()->writeConfigCache();
                    // Reschedule cron events
                    WpsolCleanCacheTime::factory()->unscheduleEvents();
                    WpsolCleanCacheTime::factory()->scheduleEvents();
                }

                //save settings on wordpress page
                if ('wpsol_save_wordpress' === $_REQUEST['action']) {
                    $this->saveWordpress();
                }

                //save settings on minify page
                if ('wpsol_save_minification' === $_REQUEST['action']) {
                    $this->saveMinification($opts);
                }

                //save settings on advanced optimization page
                if ('wpsol_save_advanced' === $_REQUEST['action']) {
                    if (class_exists('WpsolAddonSpeedOptimization')) {
                        do_action('wpsol_addon_storage_advanced_optimization');
                    }
                }

                // Save cdn settings
                if ('wpsol_save_cdn' === $_REQUEST['action']) {
                    $cdn_content         = array();
                    $cdn_exclude_content = array();
                    if (!empty($_REQUEST['cdn-content'])) {
                        $cdn_content = explode(',', $_REQUEST['cdn-content']);
                    }

                    if (!empty($_REQUEST['cdn-exclude-content'])) {
                        $cdn_exclude_content = explode(',', $_REQUEST['cdn-exclude-content']);
                    }

                    $cdn_settings = array(
                        'cdn_active'          => (isset($_REQUEST['cdn-active'])) ? 1 : 0,
                        'cdn_url'             => $_REQUEST['cdn-url'],
                        'cdn_content'         => $cdn_content,
                        'cdn_exclude_content' => $cdn_exclude_content,
                        'cdn_relative_path'   => (isset($_REQUEST['cdn-relative-path'])) ? 1 : 0
                    );

                    if (class_exists('WpsolAddonCDNIntegration')) {
                        $cdn_settings = apply_filters('wpsol_addon_save_cdn_integration', $cdn_settings, $_REQUEST);
                    }

                    update_option('wpsol_cdn_integration', $cdn_settings);
                }

                // Save configuration
                if ('wpsol_save_configuration' === $_REQUEST['action']) {
                    $opts = get_option('wpsol_configuration');
                    if (isset($_POST['disable_user'])) {
                        $opts['disable_user'] = 1;
                    } else {
                        $opts['disable_user'] = 0;
                    }
                    if (isset($_POST['display_clean'])) {
                        $opts['display_clean'] = 1;
                    } else {
                        $opts['display_clean'] = 0;
                    }

                    if (isset($_POST['webtest_api_key'])) {
                        $opts['webtest_api_key'] = $_POST['webtest_api_key'];
                    }

                    if (class_exists('WpsolAddonSpeedOptimization')) {
                        $opts = apply_filters('wpsol_addon_save_configuration', $opts, $_REQUEST);
                    }

                    update_option('wpsol_configuration', $opts);

                    //write config for cache
                    WpsolCache::factory()->writeConfigCache();
                }

                if ('wpsol_save_database' === $_REQUEST['action']) {
                    if (isset($_POST['clean'])) {
                        update_option('wpsol_clean_database_config', $_POST['clean']);
                        foreach ($_POST['clean'] as $type) {
                            WpsolDatabaseCleanup::cleanSystem($type);
                        }
                    }
                    do_action('wpsol_addon_database_cleanup_save_settings');
                    update_option('wpsol_database_cleanup_count_time', date('Y-m-d H:i:s'));
                }
                //clear cache after save settings
                WpsolCache::wpsolCacheFlush();
                WpsolMinificationCache::clearMinification();

                /**
                 * Redirect back to the settings page that was submitted
                 */
                if (isset($_REQUEST['_wp_http_referer']) && isset($_REQUEST['page'])) {
                    wp_safe_redirect(admin_url('admin.php?page=wpsol_speed_optimization&p='.$_REQUEST['page'].'&settings-updated=success#'.$_REQUEST['page']));
                    exit;
                }
            }
        }
    }

    /**
     * Automatically purge all file based page cache on post changes
     *
     * @param integer $post_id ID of post
     *
     * @return void
     */
    public function purgePostOnUpdate($post_id)
    {
        $post_type = get_post_type($post_id);
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || 'revision' === $post_type) {
            return;
        } elseif (!current_user_can('edit_post', $post_id) && (!defined('DOING_CRON') || !DOING_CRON)) {
            return;
        }

        $config = get_option('wpsol_optimization_settings');

        /**
         * Delete cache after update post or page
         *
         * @param array Type action and attachment ID
         *
         * @ignore Hook already documented
         */
        do_action('wpsol_purge_cache', array('type' => 'post_update','post_id' => $post_id));

        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            WpsolCache::wpsolCacheFlush();
        }
    }

    /**
     * Purge cache con new comment
     *
     * @param integer $comment_ID  ID of comment
     * @param integer $approved    Approved
     * @param string  $commentdata Content of comment
     *
     * @return void
     */
    public function purgePostOnNewComment($comment_ID, $approved, $commentdata)
    {
        if (empty($approved)) {
            return;
        }
        $config = get_option('wpsol_optimization_settings');



        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $post_id = $commentdata['comment_post_ID'];

            /**
             * Delete cache after approve new comment
             *
             * @param array Type action, comment ID, comment data
             *
             * @ignore Hook already documented
             */
            do_action(
                'wpsol_purge_cache',
                array('type' => 'new_comment',
                      'comment_id' => $comment_ID,
                      'comment_data' => $commentdata)
            );

            global $wp_filesystem;

            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }

            $url_path = get_permalink($post_id);
            if ($wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path))) {
                $wp_filesystem->rmdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path), true);
            }
        }
    }

    /**
     * If a comments status changes, purge it's parent posts cache
     *
     * @param integer $comment_ID     ID of comment
     * @param boolean $comment_status Status of commnet
     *
     * @return void
     */
    public function purgePostOnCommentStatusChange($comment_ID, $comment_status)
    {
        $config = get_option('wpsol_optimization_settings');

        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $comment = get_comment($comment_ID);
            $post_id = $comment->comment_post_ID;

            /**
             * Delete cache after changing comment status
             *
             * @param array Type action, comment ID, comment status
             *
             * @ignore Hook already documented
             */
            do_action(
                'wpsol_purge_cache',
                array('type'=> 'comment_update', 'comment_id' => $comment_ID, 'comment_status' => $comment_status)
            );

            global $wp_filesystem;

            WP_Filesystem();

            $url_path = get_permalink($post_id);

            if ($wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path))) {
                $wp_filesystem->rmdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path), true);
            }
        }
    }
    /**
     * Save settings physical
     *
     * @param array $opts Optionm speed optimization
     *
     * @return void
     */
    public function saveSpeedUp($opts)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');
        if (isset($_REQUEST['active-cache'])) {
            $opts['speed_optimization']['act_cache'] = 1;
            WpsolCache::factory()->toggleCaching(true);
        } else {
            $opts['speed_optimization']['act_cache'] = 0;
            WpsolCache::factory()->toggleCaching(false);
        }
        if (isset($_REQUEST['cache-desktop'])) {
            $opts['speed_optimization']['devices']['cache_desktop'] = (int)$_REQUEST['cache-desktop'];
        }
        if (isset($_REQUEST['cache-tablet'])) {
            $opts['speed_optimization']['devices']['cache_tablet'] = (int)$_REQUEST['cache-tablet'];
        }
        if (isset($_REQUEST['cache-mobile'])) {
            $opts['speed_optimization']['devices']['cache_mobile'] = (int)$_REQUEST['cache-mobile'];
        }

        if (isset($_REQUEST['add-expires'])) {
            $opts['speed_optimization']['add_expires'] = 1;
            $this->addExpiresHeader(true);
        } else {
            $opts['speed_optimization']['add_expires'] = 0;
            $this->addExpiresHeader(false);
        }

        if (isset($_REQUEST['cache_external_script'])) {
            $opts['speed_optimization']['cache_external_script'] = 1;
        } else {
            $opts['speed_optimization']['cache_external_script'] = 0;
        }
        // Update advanced option
        $advanced = get_option('wpsol_advanced_settings');
        if (isset($_REQUEST['lazy-loading'])) {
            $advanced['lazy_loading'] = 1;
        } else {
            $advanced['lazy_loading'] = 0;
        }

        if (isset($_REQUEST['exclude-lazyloading-url'])) {
            $input = sanitize_textarea_field($_REQUEST['exclude-lazyloading-url']);
            if (!empty($input)) {
                $input = rawurldecode($input);
                $input = trim($input);
                $input = str_replace(' ', '', $input);
                $input = explode("\n", $input);
            }
            $advanced['exclude_lazy_loading'] = $input;
        } else {
            $advanced['exclude_lazy_loading'] = array();
        }

        update_option('wpsol_advanced_settings', $advanced);
        //
        if (isset($_REQUEST['clean-cache-frequency'])) {
            $opts['speed_optimization']['clean_cache'] = (int)$_REQUEST['clean-cache-frequency'];
        } else {
            $opts['speed_optimization']['clean_cache'] = 0;
        }

        $opts['speed_optimization']['clean_cache_each_params'] = (int)$_REQUEST['clean-cache-each-params'];

        if (isset($_POST['disable_page'])) {
            $input = $_POST['disable_page'];
            //decode url when insert russian character to input text
            $input = rawurldecode($input);
            $input = trim($input);
            $input = str_replace(' ', '', $input);
            $input = explode("\n", $input);

            $opts['speed_optimization']['disable_page'] = $input;
        } else {
            $opts['speed_optimization']['disable_page'] = array();
        }

        if (class_exists('WpsolAddonSpeedOptimization')) {
            /**
             * Filter configuration to store database.
             *
             * @param array List of configuration
             * @param array Request server
             *
             * @internal
             *
             * @return array
             */
            $opts = apply_filters('wpsol_addon_storage_settings', $opts, $_REQUEST);
        }

        //disabled cache mobile and tablet when other mobile plugin installed
        if (file_exists(WP_PLUGIN_DIR . '/wp-mobile-detect/wp-mobile-detect.php') ||
            file_exists(WP_PLUGIN_DIR . '/wp-mobile-edition/wp-mobile-edition.php') ||
            file_exists(WP_PLUGIN_DIR . '/wptouch/wptouch.php') ||
            file_exists(WP_PLUGIN_DIR . '/wiziapp-create-your-own-native-iphone-app/wiziapp.php') ||
            file_exists(WP_PLUGIN_DIR . '/wordpress-mobile-pack/wordpress-mobile-pack.php')
        ) {
            $opts['speed_optimization']['devices']['cache_tablet'] = 3;
            $opts['speed_optimization']['devices']['cache_mobile'] = 3;
        }

        update_option('wpsol_optimization_settings', $opts);
    }

    /**
     * Save setting of Wordpress tab
     *
     * @return void
     */
    public function saveWordpress()
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');
        $opts = get_option('wpsol_optimization_settings');
        $advanced = get_option('wpsol_advanced_settings');

        if (isset($_REQUEST['query-strings'])) {
            $opts['speed_optimization']['query_strings'] = 1;
        } else {
            $opts['speed_optimization']['query_strings'] = 0;
        }

        if (isset($_REQUEST['remove_rest_api'])) {
            $opts['speed_optimization']['remove_rest_api'] = 1;
        } else {
            $opts['speed_optimization']['remove_rest_api'] = 0;
        }

        if (isset($_REQUEST['remove_rss_feed'])) {
            $opts['speed_optimization']['remove_rss_feed'] = 1;
        } else {
            $opts['speed_optimization']['remove_rss_feed'] = 0;
        }

        if (isset($_REQUEST['remove-emojis'])) {
            $advanced['remove_emojis'] = 1;
        } else {
            $advanced['remove_emojis'] = 0;
        }
        if (isset($_REQUEST['disable-gravatar'])) {
            $advanced['disable_gravatar'] = 1;
            if (!file_exists(WPSOL_UPLOAD_AVATAR)) {
                mkdir(WPSOL_UPLOAD_AVATAR, 0777, true);
            }
        } else {
            $advanced['disable_gravatar'] = 0;
        }

        update_option('wpsol_optimization_settings', $opts);
        update_option('wpsol_advanced_settings', $advanced);
    }

    /**
     * Save save_settings_advanced_features
     *
     * @param array $opts Option
     *
     * @return void
     */
    public function saveMinification($opts)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');

        if (isset($_REQUEST['html-minification'])) {
            $opts['advanced_features']['html_minification'] = 1;
        } else {
            $opts['advanced_features']['html_minification'] = 0;
        }
        if (isset($_REQUEST['css-minification'])) {
            $opts['advanced_features']['css_minification'] = 1;
        } else {
            $opts['advanced_features']['css_minification'] = 0;
        }
        if (isset($_REQUEST['js-minification'])) {
            $opts['advanced_features']['js_minification'] = 1;
        } else {
            $opts['advanced_features']['js_minification'] = 0;
        }
        if (isset($_REQUEST['cssgroup-minification'])) {
            $opts['advanced_features']['cssgroup_minification'] = 1;
        } else {
            $opts['advanced_features']['cssgroup_minification'] = 0;
        }
        if (isset($_REQUEST['jsgroup-minification'])) {
            $opts['advanced_features']['jsgroup_minification'] = 1;
        } else {
            $opts['advanced_features']['jsgroup_minification'] = 0;
        }

        if (class_exists('WpsolAddonSpeedOptimization')) {
            /**
             * Storage exclude file to option.
             *
             * @internal
             */
            do_action('wpsol_addon_storage_exclude_file');
            /**
             * Filter configuration to store database.
             *
             * @param array List of configuration
             * @param array Request server
             *
             * @internal
             *
             * @return array
             */
            $opts = apply_filters('wpsol_addon_storage_settings', $opts, $_REQUEST);
        }

        update_option('wpsol_optimization_settings', $opts);
    }

    /**
     * When user posts a comment, set a cookie so we don't show them page cache
     *
     * @param string  WP_Comment $comment Comment of usser
     * @param integer WP_User    $user    Id user
     *
     * @return void
     */
    public function setCommentCookieExceptions($comment, $user)
    {
        $config = get_option('wpsol_optimization_settings');
        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $post_id = $comment->comment_post_ID;

            setcookie(
                'wpsol_commented_posts[' . $post_id . ']',
                parse_url(get_permalink($post_id), PHP_URL_PATH),
                (time() + HOUR_IN_SECONDS * 24 * 30)
            );
        }
    }

    /**
     * Write gzip htaccess to .htaccess
     *
     * @param boolean $check Check to add gzip htaccess
     *
     * @return boolean
     */
    public static function addGzipHtacess($check)
    {
        $htaccessFile = ABSPATH . DIRECTORY_SEPARATOR . '.htaccess';
        $htaccessContent = '';
        $data = '#WP Speed of Light Gzip compression activation
<IfModule mod_deflate.c>
# Launch the compression
SetOutputFilter DEFLATE
# Force deflate for mangled headers
<IfModule mod_setenvif.c>
<IfModule mod_headers.c>
SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$';
        $data .= ' ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
# Remove non-compressible file type
SetEnvIfNoCase Request_URI \
\.(?:gif|jpe?g|png|rar|zip|exe|flv|mov|wma|mp3|avi|swf|mp?g|mp4|webm|webp)$ no-gzip dont-vary
</IfModule>
</IfModule>

# Regroup compressed resource in MIME-types
<IfModule mod_filter.c>
AddOutputFilterByType DEFLATE application/atom+xml \
		                          application/javascript \
		                          application/json \
		                          application/rss+xml \
		                          application/vnd.ms-fontobject \
		                          application/x-font-ttf \
		                          application/xhtml+xml \
		                          application/xml \
		                          font/opentype \
		                          image/svg+xml \
		                          image/x-icon \
		                          text/css \
		                          text/html \
		                          text/plain \
		                          text/x-component \
		                          text/xml
</IfModule>
<IfModule mod_headers.c>
Header append Vary: Accept-Encoding
</IfModule>
</IfModule>

<IfModule mod_mime.c>
AddType text/html .html_gzip
AddEncoding gzip .html_gzip
</IfModule>
<IfModule mod_setenvif.c>
SetEnvIfNoCase Request_URI \.html_gzip$ no-gzip
</IfModule>
#End of WP Speed of Light Gzip compression activation' . PHP_EOL;
        if ($check) {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file

            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }

            if (empty($htaccessContent)) {
                return false;
            }
            //if isset Gzip access
            if (strpos($htaccessContent, 'mod_deflate') !== false ||
                strpos($htaccessContent, 'mod_setenvif') !== false ||
                strpos($htaccessContent, 'mod_headers') !== false ||
                strpos($htaccessContent, 'mod_mime') !== false ||
                strpos($htaccessContent, '#WP Speed of Light Gzip compression activation') !== false) {
                return false;
            }

            $htaccessContent = $data . $htaccessContent;
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        } else {
            if (!is_super_admin()) {
                return true;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }
            if (empty($htaccessContent)) {
                return false;
            }

            $htaccessContent = str_replace($data, '', $htaccessContent);
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        }
    }

    /**
     * Write expires header to .htaccess
     *
     * @param boolean $check Check to add Expires header
     *
     * @return boolean
     */
    public static function addExpiresHeader($check)
    {
        $htaccessFile = ABSPATH . DIRECTORY_SEPARATOR . '.htaccess';
        $htaccessContent = '';
        $expires = '#Expires headers configuration added by Speed of Light plugin' . PHP_EOL .
            '<IfModule mod_expires.c>' . PHP_EOL .
            '   ExpiresActive On' . PHP_EOL .
            '   ExpiresDefault A2592000' . PHP_EOL .
            '   ExpiresByType application/javascript "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType text/javascript "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType text/css "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/jpeg "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/png "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/gif "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/ico "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/x-icon "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/svg+xml "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/bmp "access plus 30 days"' . PHP_EOL .
            '</IfModule>' . PHP_EOL .
            '#End of expires headers configuration' . PHP_EOL;

        if ($check) {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }


            if (empty($htaccessContent)) {
                return false;
            }
            //if isset expires header in htacces
            if (strpos($htaccessContent, 'mod_expires') !== false ||
                strpos($htaccessContent, 'ExpiresActive') !== false ||
                strpos($htaccessContent, 'ExpiresDefault') !== false ||
                strpos($htaccessContent, 'ExpiresByType') !== false) {
                return false;
            }

            $htaccessContent = $expires . $htaccessContent;
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        } else {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }
            if (empty($htaccessContent)) {
                return false;
            }
            $htaccessContent = str_replace($expires, '', $htaccessContent);
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        }
    }
    /**
     * Remove query string from static resources
     *
     * @param string $content Content raw
     *
     * @return mixed
     */
    public function removeQueryStrings($content)
    {
        if ($content !== '') {
            $blog_regexp = self::blogDomainRootUrlRegexp();

            if (!$blog_regexp) {
                return $content;
            }
            $pattern = '~(href|src)=?([\'"])((' .
                $blog_regexp .
                ')?(/[^\'"/][^\'"]*\.([a-z-_]+)([\?#][^\'"]*)?))[\'"]~Ui';
            $content = preg_replace_callback(
                $pattern,
                array($this, 'queryStringsReplaceCallback'),
                $content
            );
        }

        return $content;
    }

    /**
     * Callback replace for js and css file
     *
     * @param string $matches Matches of query string
     *
     * @return string
     */
    public function queryStringsReplaceCallback($matches)
    {
        list ($match, $attr, $quote, $url, , , $extension) = $matches;

        if ($extension === 'js' || $extension === 'css') {
            $url = preg_replace('/[&\?]+(ver=([a-z0-9-_\.]+|[0-9-]+))+[&\?]*([a-z0-9-_=]*)*/i', '', $url);
        }
        return $attr . '=' . $quote . $url . $quote;
    }

    /**
     * Returns domain url regexp
     *
     * @return string
     */
    public static function blogDomainRootUrlRegexp()
    {
        $home_url = get_option('home');
        $parse_url = parse_url($home_url);

        if ($parse_url && isset($parse_url['scheme']) && isset($parse_url['host'])) {
            $scheme = $parse_url['scheme'];
            $host = $parse_url['host'];
            $port = (isset($parse_url['port']) && $parse_url['port'] !== 80 ? ':' . (int)$parse_url['port'] : '');
            $domain_url = sprintf('[%s:]*//%s%s', $scheme, $host, $port);

            return $domain_url;
        }

        return false;
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

    /**
     * Check system information
     *
     * @return boolean
     */
    public static function systemCheck()
    {
        $check = false;
        $headerInfo = self::getHeaderInfo();
        $phpInfo = self::parsePhpinfo();

        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            // Check php version
            $check = true;
        }

        if (!$headerInfo['gzip']) {
            // Check gzip activation
            $check = true;
        }

        if (function_exists('apache_get_modules')) {
            // Check apache modules
            $apacheModules = apache_get_modules();
            if (!in_array('mod_expires', $apacheModules) ||
                !in_array('mod_headers', $apacheModules) ||
                !in_array('mod_filter', $apacheModules)) {
                $check = true;
            }
        } else {
            // If apache get modules does not support
            if (isset($phpInfo['apache2handler']['Loaded Modules']) &&
                (strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_expires') === false ||
                strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_headers') === false ||
                strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_filter') === false)) {
                $check = true;
            }
        }
        // Php modules
        if (function_exists('get_loaded_extensions')) {
            $phpModules = get_loaded_extensions();
            if (!in_array('curl', $phpModules) ||
                !in_array('openssl', $phpModules)) {
                $check = true;
            }
        } else {
            // If get laoded extensions not supports
            if (!isset($phpInfo['curl']) || !isset($phpInfo['openssl'])) {
                $check = true;
            }
        }

        return $check;
    }

    /**
     * Count system error
     *
     * @return integer
     */
    public static function countSystemCheck()
    {
        $count = 0;
        $phpInfo = self::parsePhpinfo();
        $headerInfo = self::getHeaderInfo();

        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            $count++;
        }
        // Gzip
        if (!$headerInfo['gzip']) {
            $count++;
        }
        // Apache modules
        if (function_exists('apache_get_modules')) {
            // Check apache modules
            $apacheModules = apache_get_modules();
            if (!in_array('mod_expires', $apacheModules)) {
                $count++;
            }
            if (!in_array('mod_headers', $apacheModules)) {
                $count++;
            }
            if (!in_array('mod_filter', $apacheModules)) {
                $count++;
            }
        } else {
            // If apache get modules does not support
            if (isset($phpInfo['apache2handler']['Loaded Modules']) && strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_expires') === false) {
                $count++;
            }
            if (isset($phpInfo['apache2handler']['Loaded Modules']) && strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_headers') === false) {
                $count++;
            }
            if (isset($phpInfo['apache2handler']['Loaded Modules']) && strpos($phpInfo['apache2handler']['Loaded Modules'], 'mod_filter') === false) {
                $count++;
            }
        }

        // Php modules
        if (function_exists('get_loaded_extensions')) {
            $phpModules = get_loaded_extensions();
            if (!in_array('curl', $phpModules)) {
                $count++;
            }
            if (!in_array('openssl', $phpModules)) {
                $count++;
            }
        } else {
            // If get laoded extensions not supports
            if (!isset($phpInfo['curl'])) {
                $count++;
            }
            if (!isset($phpInfo['openssl'])) {
                $count++;
            }
        }


        return $count;
    }
    /**
     * Return an instance of the current class, create one if it doesn't exist
     *
     * @since  1.0
     * @return object
     */
    public static function factory()
    {

        static $instance;

        if (!$instance) {
            $instance = new self();
            $instance->setAction();
        }

        return $instance;
    }
}
