<?php
/*
 *  Based on some work of autoptimize plugin
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WpsolMinificationHtml
 */
class WpsolMinificationHtml extends WpsolMinificationBase
{
    /**
     * Init Minify HTML params
     *
     * @var string
     */
    private $minifyHTML = false;
    /**
     * Init Keep comments params
     *
     * @var boolean
     */
    private $keepcomments = false;
    /**
     * Init forcexhtml params
     *
     * @var boolean
     */
    private $forcexhtml = false;
    /**
     * Init exclude params
     *
     * @var array
     */
    private $exclude = array('<!-- ngg_resource_manager_marker -->');

    /**
     * Read and filter from content html
     *
     * @param array $options Option to minify
     *
     * @return boolean
     */
    public function read($options)
    {
        // Remove the HTML comments?
        $this->keepcomments = (bool)$options['keepcomments'];

        if ($options['minifyHTML']) {
            $this->minifyHTML = true;
        }

        /**
         * Apply filter to add strings to be excluded from HTML minification
         *
         * @param array Default value
         *
         * @return array
         */
        $this->exclude = apply_filters('wpsol_html_minification_exclude_string', $this->exclude);

        // Nothing else for HTML
        return true;
    }

    /**
     * Joins and optimizes CSS
     *
     * @return boolean
     */
    public function minify()
    {
        /**
         * Should we minify the specified inline html content
         *
         * @param true Minify by default the content
         * @param string Html content
         *
         * @return boolean
         */
        $noptimizeHTML = apply_filters('wpsol_html_do_minify', true, $this->content);
        if (!$noptimizeHTML) {
            return false;
        }


        if (class_exists('Minify_HTML')) {
            // wrap the to-be-excluded strings in noptimize tags
            foreach ($this->exclude as $exclString) {
                if (strpos($this->content, $exclString) !== false) {
                    $replString = '<!--noptimize-->' . $exclString . '<!--/noptimize-->';
                    $this->content = str_replace($exclString, $replString, $this->content);
                }
            }

            // noptimize me
            $this->content = $this->hideNoptimize($this->content);

            // Minify html
            $options = array('keepComments' => $this->keepcomments);
            if ($this->forcexhtml) {
                $options['xhtml'] = true;
            }

            if (method_exists('Minify_HTML', 'minify')) {
                $tmp_content = Minify_HTML::minify($this->content, $options);
                if (!empty($tmp_content)) {
                    $this->content = $tmp_content;
                    unset($tmp_content);
                }
            }

            // restore noptimize
            $this->content = $this->restoreNoptimize($this->content);

            // remove the noptimize-wrapper from around the excluded strings
            foreach ($this->exclude as $exclString) {
                $replString = '<!--noptimize-->' . $exclString . '<!--/noptimize-->';
                if (strpos($this->content, $replString) !== false) {
                    $this->content = str_replace($replString, $exclString, $this->content);
                }
            }

            return true;
        }

        // Didn't minify :(
        return false;
    }

    /**
     * Does nothing
     *
     * @return boolean
     */
    public function cache()
    {
        //No cache for HTML
        return true;
    }

    /**
     * Returns the content
     *
     * @return string
     */
    public function getcontent()
    {
        return $this->content;
    }
}
