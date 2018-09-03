<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolCdnIntegration
 */
class WpsolCdnIntegration
{
    /**
     * WpsolCdnIntegration constructor.
     */
    public function __construct()
    {
        add_action('template_redirect', array($this, 'handleRewriteCdn'));
    }

    /**
     * Execute rewrite cdn
     *
     * @return void
     */
    public function handleRewriteCdn()
    {
        $cdn_integration = get_option('wpsol_cdn_integration');

        if (empty($cdn_integration)) {
            return;
        }

        if ($cdn_integration['cdn_url'] === '') {
            return;
        }

        if (get_option('home') === $cdn_integration['cdn_url']) {
            return;
        }

        $rewrite = new WpsolCDNRewrite($cdn_integration);

        //rewrite CDN Url to html raw
        add_filter('wpsol_cdn_content_return', array(&$rewrite, 'rewrite'));
    }
}
