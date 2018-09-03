<?php

defined('ABSPATH') || exit;

/**
 * Class cache for Ecommerce
 */
class WpsolEcommerceCache
{
    /**
     * WpsolEcommerceCache constructor.
     */
    public function __construct()
    {
        add_action('activated_plugin', array($this, 'detectEcommerceActivation'));
        add_action('deactivated_plugin', array($this, 'detectEcommerceDeactivation'));
        add_action('wp_loaded', array($this, 'updateEcommerceActivation'));
    }


    /**
     * Delete option detect when deactive woo
     *
     * @param string $plugin Plugin name
     *
     * @return void
     */
    public function detectEcommerceDeactivation($plugin)
    {
        if ('woocommerce/woocommerce.php' === $plugin) {
            delete_option('wpsol_ecommerce_detect');
        }
    }

    /**
     * After woocommerce active,merce array disable page config
     *
     * @param string $plugin Plugin name
     *
     * @return void
     */
    public function detectEcommerceActivation($plugin)
    {
        if ('woocommerce/woocommerce.php' === $plugin) {
            update_option('wpsol_ecommerce_detect', 1);
        }
    }


    /**
     * Update option when woocomerce active
     *
     * @return void
     */
    public function updateEcommerceActivation()
    {
        $check = get_option('wpsol_ecommerce_detect');
        if (stripos($_SERVER['REQUEST_URI'], 'wc-setup&step=locale') !== false) {
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            WpsolCache::writeConfigCache();
        }
        if (!empty($check)) {
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            WpsolCache::writeConfigCache();
            update_option('wpsol_ecommerce_detect', 0);
        }
    }

    /**
     * Exclude pages of ecommerce from cache
     *
     * @return array
     */
    public function wpsolEcommerceExcludePages()
    {
        $urls = array();
        $regex = '*';

        if (class_exists('WooCommerce') && function_exists('wc_get_page_id')) {
            $cardId = wc_get_page_id('cart');
            $checkoutId = wc_get_page_id('checkout');
            $myaccountId = wc_get_page_id('myaccount');

            if ($cardId > 0) {
                $urls[] = $this->getBasicUrls($cardId);
                // Get url through multilanguage plugin
                $urls = $this->getTranslateUrls($urls, $cardId);
            }

            if ($checkoutId > 0) {
                $urls[] = $this->getBasicUrls($checkoutId, $regex);
                // Get url through multilanguage plugin
                $urls = $this->getTranslateUrls($urls, $checkoutId, $regex);
            }

            if ($myaccountId > 0) {
                $urls[] = $this->getBasicUrls($myaccountId, $regex);
                // Get url through multilanguage plugin
                $urls = $this->getTranslateUrls($urls, $myaccountId, $regex);
            }

            // Process urls to return
            $urls = array_unique($urls);
            $urls = array_map(array($this, 'rtrimUrls'), $urls);
        }

        return $urls;
    }

    /**
     * Return translate url without translate plugin
     *
     * @param string  $urls   Url to translate
     * @param integer $postID ID of posts
     * @param null    $regex  Regular
     *
     * @return array
     */
    public function getTranslateUrls($urls, $postID, $regex = null)
    {
        // WPML plugin
        if (class_exists('SitePress')) {
            global $sitepress;
            if (isset($sitepress)) {
                $active_languages = $sitepress->get_active_languages();

                if (!empty($active_languages)) {
                    $languages = array_keys($active_languages);
                    foreach ($languages as $language) {
                        $translatedId = icl_object_id($postID, 'page', false, $language);
                        if (empty($translatedId)) {
                            continue;
                        }
                        $urls[] = $this->getBasicUrls($translatedId, $regex);
                    }
                }
            }
        }

        // Polylang plugin
        if (class_exists('Polylang') && function_exists('pll_languages_list') && function_exists('PLL')) {
            $translatedId = pll_get_post_translations($postID);

            if (!empty($translatedId)) {
                foreach ($translatedId as $id) {
                    $urls[] = $this->getBasicUrls($id, $regex);
                }
            }
        }

        // qTranslate-x plugin
        if (is_plugin_active('qtranslate-x/qtranslate.php')) {
            global $q_config;
            if (isset($q_config) && function_exists('qtranxf_convertURL')) {
                $url = $this->getBasicUrls($postID);

                if (!empty($q_config['enabled_languages'])) {
                    foreach ($q_config['enabled_languages'] as $language) {
                        $urls[] = qtranxf_convertURL($url, $language, true);
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * Return basic url without translate plugin
     *
     * @param integer $postID ID of posts
     * @param null    $regex  Regular
     *
     * @return string
     */
    public function getBasicUrls($postID, $regex = null)
    {
        $permalink = get_option('permalink_structure');

        if (!empty($permalink)) {
            // Custom URL structure
            $url = parse_url(get_permalink($postID), PHP_URL_PATH);
        } else {
            $url = get_permalink($postID);
        }

        return $url . $regex;
    }

    /**
     * Remove '/' chacracter of end url
     *
     * @param string $url Url to trim
     *
     * @return string
     */
    public function rtrimUrls($url)
    {
        return rtrim($url, '/');
    }

    /**
     * Init class ecommerce cache
     *
     * @return WpsolEcommerceCache
     */
    public static function factory()
    {
        static $instance;

        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }
}
