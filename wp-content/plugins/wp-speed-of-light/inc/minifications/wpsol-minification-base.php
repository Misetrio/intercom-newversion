<?php
/*
 *  Based on some work of autoptimize plugin
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WpsolMinificationBase
 */
abstract class WpsolMinificationBase
{
    /**
     * Init content params
     *
     * @var string
     */
    protected $content = '';
    /**
     * Init tag warning params
     *
     * @var boolean
     */
    protected $tagWarning = false;
    /**
     * Init cdn url params
     *
     * @var string
     */
    protected $cdn_url = '';

    /**
     * WpsolMinificationBase constructor.
     *
     * @param string $content Content of page
     */
    public function __construct($content)
    {
        $this->content = $content;
    }


    /**
     * Reads the page and collects tags
     *
     * @param array $justhead Just header of page
     *
     * @return mixed
     */
    abstract public function read($justhead);

    /**
     * Joins and optimizes collected things
     *
     * @return mixed
     */
    abstract public function minify();

    /**
     * Caches the things
     *
     * @return mixed
     */
    abstract public function cache();

    /**
     * Returns the content
     *
     * @return mixed
     */
    abstract public function getcontent();

    /**
     * Converts an URL to a full path
     *
     * @param string $url Url to get path
     *
     * @return boolean|mixed
     */
    protected function getpath($url)
    {
        if (strpos($url, '%') !== false) {
            $url = urldecode($url);
        }

        // normalize
        if (strpos($url, '//') === 0) {
            if (is_ssl()) {
                $url = 'https:' . $url;
            } else {
                $url = 'http:' . $url;
            }
        } elseif ((strpos($url, '//') === false)
            && (strpos($url, parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST)) === false)) {
            $url = WPSOL_WP_SITE_URL . $url;
        }

        // first check; hostname wp site should be hostname of url
        $thisHost = parse_url($url, PHP_URL_HOST);
        if ($thisHost !== parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST)) {
            /*
            * first try to get all domains from WPML (if available)
            * then explicitely declare $this->cdn_url as OK as well
            * then apply own filter autoptimize_filter_cssjs_multidomain takes an array of hostnames
            * each item in that array will be considered part of the same WP multisite installation
            */
            $multidomains = array();

            $multidomainsWPML = apply_filters('wpml_setting', array(), 'language_domains');
            if (!empty($multidomainsWPML)) {
                $multidomains = array_map(array($this, 'aoGetDomain'), $multidomainsWPML);
            }

            if (!empty($this->cdn_url)) {
                $multidomains[] = parse_url($this->cdn_url, PHP_URL_HOST);
            }

            if (!empty($multidomains)) {
                if (in_array($thisHost, $multidomains)) {
                    $url = str_replace($thisHost, parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST), $url);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // try to remove "wp root url" from url while not minding http<>https
        $tmp_ao_root = preg_replace('/https?/', '', WPSOL_WP_ROOT_URL);
        $tmp_url = preg_replace('/https?/', '', $url);
        $path = str_replace($tmp_ao_root, '', $tmp_url);

        // final check; if path starts with :// or //,
        // this is not a URL in the WP context and we have to assume we can't aggregate
        if (preg_match('#^:?//#', $path)) {
            /**
 * External script/css (adsense, etc)
*/
            return false;
        }

        $path = str_replace('//', '/', WPSOL_ROOT_DIR . $path);
        return $path;
    }

    /**
     * Needed for WPML-filter
     *
     * @param string $in Input to get domain
     *
     * @return mixed|string
     */
    protected function aoGetDomain($in)
    {
        // make sure the url starts with something vaguely resembling a protocol
        if ((strpos($in, 'http') !== 0) && (strpos($in, '//') !== 0)) {
            $in = 'http://' . $in;
        }

        // do the actual parse_url
        $out = parse_url($in, PHP_URL_HOST);

        // fallback if parse_url does not understand the url is in fact a url
        if (empty($out)) {
            $out = $in;
        }

        return $out;
    }

    /**
     * Logger
     *
     * @param string  $logmsg     Message logger
     * @param boolean $appendHTML Position append HTML
     *
     * @return void
     */
    protected function aoLogger($logmsg, $appendHTML = true)
    {
        if ($appendHTML) {
            $logmsg = '<!--noptimize--><!-- ' . $logmsg . ' --><!--/noptimize-->';
            $this->content .= $logmsg;
        } else {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions -- Get error message
            error_log('Error: ' . $logmsg);
        }
    }

    /**
     * Hide everything between noptimize-comment tags
     *
     * @param string $noptimize_in Noptimize-comment input
     *
     * @return mixed
     */
    protected function hideNoptimize($noptimize_in)
    {
        if (preg_match('/<!--\s?noptimize\s?-->/', $noptimize_in)) {
            $noptimize_out = preg_replace_callback(
                '#<!--\s?noptimize\s?-->.*?<!--\s?/\s?noptimize\s?-->#is',
                function ($matches) {
                    return '%%NOPTIMIZE'.WPSOL_HASH.'%%'.base64_encode($matches[0]).'%%NOPTIMIZE%%';
                },
                $noptimize_in
            );
        } else {
            $noptimize_out = $noptimize_in;
        }
        return $noptimize_out;
    }

    /**
     * Unhide noptimize-tags
     *
     * @param string $noptimize_in Noptimize-comment input
     *
     * @return mixed
     */
    protected function restoreNoptimize($noptimize_in)
    {
        if (strpos($noptimize_in, '%%NOPTIMIZE%%') !== false) {
            $noptimize_out = preg_replace_callback(
                '#%%NOPTIMIZE' . WPSOL_HASH . '%%(.*?)%%NOPTIMIZE%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $noptimize_in
            );
        } else {
            $noptimize_out = $noptimize_in;
        }
        return $noptimize_out;
    }

    /**
     * Hide ie hacks
     *
     * @param string $iehacks_in Ie hack input
     *
     * @return mixed
     */
    protected function hideIehacks($iehacks_in)
    {
        if (strpos($iehacks_in, '<!--[if') !== false) {
            $iehacks_out = preg_replace_callback(
                '#<!--\[if.*?\[endif\]-->#is',
                function ($matches) {
                    return '%%IEHACK'.WPSOL_HASH.'%%'.base64_encode($matches[0]).'%%IEHACK%%';
                },
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    /**
     * Restore ie hacks
     *
     * @param string $iehacks_in Ie hack input
     *
     * @return mixed
     */
    protected function restoreIehacks($iehacks_in)
    {
        if (strpos($iehacks_in, '%%IEHACK%%') !== false) {
            $iehacks_out = preg_replace_callback(
                '#%%IEHACK' . WPSOL_HASH . '%%(.*?)%%IEHACK%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    /**
     * Hide comment in file
     *
     * @param string $comments_in Comment input
     *
     * @return mixed
     */
    protected function hideComments($comments_in)
    {
        if (strpos($comments_in, '<!--') !== false) {
            $comments_out = preg_replace_callback(
                '#<!--.*?-->#is',
                function ($matches) {
                    return '%%COMMENTS'.WPSOL_HASH.'%%'.base64_encode($matches[0]).'%%COMMENTS%%';
                },
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }

    /**
     * Restore comments
     *
     * @param string $comments_in Comment input
     *
     * @return mixed
     */
    protected function restoreComments($comments_in)
    {
        if (strpos($comments_in, '%%COMMENTS%%') !== false) {
            $comments_out = preg_replace_callback(
                '#%%COMMENTS' . WPSOL_HASH . '%%(.*?)%%COMMENTS%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }

    /**
     * Replace CDN url
     *
     * @param string $url Url to replace
     *
     * @return mixed|string
     */
    protected function urlReplaceCdn($url)
    {
        $cdn_url = $this->cdn_url;
        if (!empty($cdn_url)) {
            // secondly prepend domain-less absolute URL's
            if ((substr($url, 0, 1) === '/') && (substr($url, 1, 1) !== '/')) {
                $url = rtrim($cdn_url, '/') . $url;
            } else {
                // get wordpress base URL
                $WPSiteBreakdown = parse_url(WPSOL_WP_SITE_URL);
                $WPBaseUrl = $WPSiteBreakdown['scheme'] . '://' . $WPSiteBreakdown['host'];
                if (!empty($WPSiteBreakdown['port'])) {
                    $WPBaseUrl .= ':' . $WPSiteBreakdown['port'];
                }
                // three: replace full url's with scheme
                $tmp_url = str_replace($WPBaseUrl, rtrim($cdn_url, '/'), $url);
                if ($tmp_url === $url) {
                    // last attempt; replace scheme-less URL's
                    $url = str_replace(preg_replace('/https?:/', '', $WPBaseUrl), rtrim($cdn_url, '/'), $url);
                } else {
                    $url = $tmp_url;
                }
            }
        }
        return $url;
    }

    /**
     * Inject already HTML code in optimized JS/CSS
     *
     * @param string $payload    Element to inject
     * @param array  $replaceTag Position in html
     *
     * @return void
     */
    protected function injectInHtml($payload, $replaceTag)
    {
        if (strpos($this->content, $replaceTag[0]) !== false) {
            if ($replaceTag[1] === 'after') {
                $replaceBlock = $replaceTag[0] . $payload;
            } elseif ($replaceTag[1] === 'replace') {
                $replaceBlock = $payload;
            } else {
                $replaceBlock = $payload . $replaceTag[0];
            }
            $strpos = strpos($this->content, $replaceTag[0]);
            $strlen = strlen($replaceTag[0]);
            $this->content = substr_replace($this->content, $replaceBlock, $strpos, $strlen);
        } else {
            $this->content .= $payload;
            if (!$this->tagWarning) {
                $this->content .= '<!--noptimize--><!-- WPSOL found a problem with the HTML in your Theme,';
                $this->content .= 'tag ' . $replaceTag[0] . ' missing --><!--/noptimize-->';
                $this->tagWarning = true;
            }
        }
    }

    /**
     * Check removeable
     *
     * @param string $tag        Tag from content
     * @param array  $removables Removable item
     *
     * @return boolean
     */
    protected function isremovable($tag, $removables)
    {
        foreach ($removables as $match) {
            if (strpos($tag, $match) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Inject already minified code in optimized JS/CSS
     *
     * @param string $in Input to inject minify
     *
     * @return mixed
     */
    protected function injectMinified($in)
    {
        if (strpos($in, '%%INJECTLATER%%') !== false) {
            $out = preg_replace_callback(
                '#%%INJECTLATER' . WPSOL_HASH . '%%(.*?)%%INJECTLATER%%#is',
                function ($matches) {
                    $filepath    = base64_decode(strtok($matches[1], '|'));
                    $filecontent = file_get_contents($filepath);

                    // remove BOM
                    $filecontent = preg_replace('#\x{EF}\x{BB}\x{BF}#', '', $filecontent);

                    // remove comments and blank lines
                    if (substr($filepath, - 3, 3) === '.js') {
                        $filecontent = preg_replace('#^\s*\/\/.*$#Um', '', $filecontent);
                    }

                    $filecontent = preg_replace('#^\s*\/\*[^!].*\*\/\s?#Us', '', $filecontent);
                    $filecontent = preg_replace("#(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#", "\n", $filecontent);

                    // specific stuff for JS-files
                    if (substr($filepath, - 3, 3) === '.js') {
                        if ((substr($filecontent, - 1, 1) !== ';') && (substr($filecontent, - 1, 1) !== '}')) {
                            $filecontent .= ';';
                        }

                        if (get_option('wpsol_js_trycatch') === 'on') {
                            $filecontent = 'try{' . $filecontent . '}catch(e){}';
                        }
                    } elseif ((substr($filepath, - 4, 4) === '.css')) {
                        $filecontent = WpsolMinificationStyles::fixurls($filepath, $filecontent);
                    }

                    // return
                    return "\n" . $filecontent;
                },
                $in
            );
        } else {
            $out = $in;
        }
        return $out;
    }

    /**
     * Check to exclude url from group
     *
     * @param string $url      Url to check
     * @param array  $excludes Element check url
     *
     * @return boolean
     */
    protected function checkExcludeFile($url, $excludes)
    {
        if (!empty($excludes)) {
            foreach ($excludes as $ex) {
                if (empty($ex)) {
                    continue;
                }
                if (strpos($ex, '/') === 0) {
                    $ex = ltrim($ex, '/');
                }
                preg_match_all('@' . $ex . '@', $url, $matches);

                if (!empty($matches[0])) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Inject minify to html
     *
     * @param string $k      Element to inject
     * @param string $script Element to replace
     *
     * @return boolean
     */
    protected function injectMinifyToHtml($k, $script)
    {

        if (!empty($this->matches)) {
            foreach ($this->matches as $tag) {
                if (strpos($tag, $k) !== false) {
                    $this->content = str_replace($tag, $script, $this->content);
                }
            }
        }
        return true;
    }
}
