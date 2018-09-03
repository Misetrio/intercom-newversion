<?php
/*
 *  Based on some work of autoptimize plugin
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WpsolMinificationScripts
 */
class WpsolMinificationScripts extends WpsolMinificationBase
{
    /**
     * Init minify javascript params
     *
     * @var boolean
     */
    private $minifyJS = false;
    /**
     * Init script params
     *
     * @var array
     */
    private $scripts = array();
    /**
     * Init dont move params
     *
     * @var array
     */
    private $dontmove = array('document.write', 'html5.js', 'show_ads.js', 'google_ad',
        'blogcatalog.com/w', 'tweetmeme.com/i', 'mybloglog.com/', 'histats.com/js', 'ads.smowtion.com/ad.js',
        'statcounter.com/counter/counter.js', 'widgets.amung.us', 'ws.amazon.com/widgets', 'media.fastclick.net',
        '/ads/', 'comment-form-quicktags/quicktags.php', 'edToolbar', 'intensedebate.com', 'scripts.chitika.net/',
        '_gaq.push', 'jotform.com/', 'admin-bar.min.js', 'GoogleAnalyticsObject', 'plupload.full.min.js',
        'syntaxhighlighter', 'adsbygoogle', 'gist.github.com', '_stq', 'nonce', 'post_id', 'data-noptimize');
    /**
     * Init do move params
     *
     * @var array
     */
    private $domove = array('gaJsHost', 'load_cmc', 'jd.gallery.transitions.js',
        'swfobject.embedSWF(', 'tiny_mce.js', 'tinyMCEPreInit.go');
    /**
     * Init domove last params
     *
     * @var array
     */
    private $domovelast = array('addthis.com', '/afsonline/show_afs_search.js', 'disqus.js',
        'networkedblogs.com/getnetworkwidget', 'infolinks.com/js/', 'jd.gallery.js.php', 'jd.gallery.transitions.js',
        'swfobject.embedSWF(', 'linkwithin.com/widget.js', 'tiny_mce.js', 'tinyMCEPreInit.go');
    /**
     * Init try catch params
     *
     * @var boolean
     */
    private $trycatch = false;
    /**
     * Init force head params
     *
     * @var boolean
     */
    private $forcehead = false;
    /**
     * Init url params
     *
     * @var string
     */
    private $url = '';
    /**
     * Init restofcontent params
     *
     * @var string
     */
    private $restofcontent = '';
    /**
     * Init md5hash params
     *
     * @var string
     */
    private $md5hash = '';
    /**
     * Init group js params
     *
     * @var boolean
     */
    private $group_js = false;
    /**
     * Init javascript group value params
     *
     * @var array
     */
    private $js_group_val = array();
    /**
     * Init javascript group params
     *
     * @var array
     */
    private $js_group = array();
    /**
     * Init javascript min  params
     *
     * @var array
     */
    private $js_min_arr = array();
    /**
     * Init url group params
     *
     * @var array
     */
    private $url_group_arr = array();
    /**
     * Init javascript exclude params
     *
     * @var array
     */
    private $js_exclude = array();
    /**
     * Init external scripts params
     *
     * @var array
     */
    private $external_scripts = array();
    /**
     * Init match array params
     *
     * @var array
     */
    protected $matches = array();
    /**
     * Init external local path params
     *
     * @var array
     */
    private $external_local_path = array();
    /**
     * Init js after group params
     *
     * @var string
     */
    private $js_after_group = '';
    /**
     * Init cache external params
     *
     * @var boolean
     */
    private $cache_external = false;
    /**
     * Init exclude inline params
     *
     * @var boolean
     */
    private $exclude_inline = false;
    /**
     * Init move to footer params
     *
     * @var boolean
     */
    private $move_to_footer = false;
    /**
     * Init all script compare params
     *
     * @var array
     */
    private $all_script_compare = array();
    /**
     * Init exclude move to footer params
     *
     * @var array
     */
    private $exclude_move_to_footer = array();
    /**
     * Init check allow minify params
     *
     * @var boolean
     */
    private $check_allow_minify = true;
    /**
     * Reads the page and collects script tags
     *
     * @param array $options Option of minify js
     *
     * @return boolean
     */
    public function read($options)
    {
        // only header?
        if ($options['justhead'] === true) {
            $content = explode('</head>', $this->content, 2);
            $this->content = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }
        // group js?
        if ($options['group_js'] === true) {
            $this->group_js = true;
        }
        //turn on minification
        if (!empty($options['minify_js'])) {
            $this->minifyJS = $options['minify_js'];
        }
        //custom js exclude
        if (!empty($options['exclude_js'])) {
            $this->js_exclude = $options['exclude_js'];
        }
        //cache external js
        if (!empty($options['cache_external'])) {
            $this->cache_external = $options['cache_external'];
        }
        //exclude inline script
        if (!empty($options['exclude_inline'])) {
            $this->exclude_inline = $options['exclude_inline'];
        }
        //exclude inline script
        if (!empty($options['move_to_script'])) {
            $this->move_to_footer = $options['move_to_script'];
        }

        //exclude inline script
        if (!empty($options['exclude_move_to_script'])) {
            $this->exclude_move_to_footer = $options['exclude_move_to_script'];
        }

        //Should we add try-catch?
        if ($options['trycatch'] === true) {
            $this->trycatch = true;
        }
        // force js in head?
        if ($options['forcehead'] === true) {
            $this->forcehead = true;
        }
        // get extra exclusions settings or filter
        $excludeJS = $options['js_exclude'];
        if ($excludeJS !== '') {
            $exclJSArr = array_filter(array_map('trim', explode(',', $excludeJS)));
            $this->dontmove = array_merge($exclJSArr, $this->dontmove);
        }
        // noptimize me
        $this->content = $this->hideNoptimize($this->content);

        // Save IE hacks
        $this->content = $this->hideIehacks($this->content);

        // comments
        $this->content = $this->hideComments($this->content);

        //Get script files
        if (preg_match_all('#<script.*</script>#Usmi', $this->content, $matches)) {
            $this->matches = $matches[0];
            foreach ($matches[0] as $tag) {
                // only consider aggregation whitelisted in should_aggregate-function
                if (!$this->shouldAggregate($tag)) {
                    continue;
                }

                if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                    // External script
                    $url = current(explode('?', $source[2], 2));
                    // Exclude file if js exclude exist
                    if ($this->checkExcludeFile($url, $this->js_exclude)) {
                        continue;
                    }
                    $path = $this->getpath($url);
                    if ($path !== false && preg_match('#\.js$#', $path)) {
                        // Set url to compare for move to footer
                        $this->all_script_compare[$url] = $url;
                        if ($this->ismovable($tag)) {
                            //We can merge it
                            $this->scripts[$url] = $path;
                            if ($this->group_js) {
                                $this->content = str_replace($tag, '', $this->content);
                            }
                        }
                    } else {
                        //External script (example: google analytics)
                        //OR Script is dynamic (.php etc)
                        preg_match('/(src=["\'](.*?)["\'])/', $tag, $match);
                        $split = preg_split('/["\']/', $match[0]); // split by quotes
                        if (!empty($split[1])) {
                            $this->external_scripts[$tag] = $split[1];
                        }
                    }
                } else {
                    // Inline script
                    if ($this->ismovable($tag)) {
                        // unhide comments, as javascript may be wrapped in comment-tags for old times' sake
                        $tag = $this->restoreComments($tag);
                        // Set url to compare for move to footer

                        preg_match('#<script.*>(.*)</script>#Usmi', $tag, $code);
                        $code = preg_replace('#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm', '$1', $code[1]);
                        $code = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $code);
                        $this->all_script_compare[$tag] =  $code;
                        // We can minify inline
                        if (!$this->exclude_inline) {
                            $this->scripts[$tag] = 'INLINE;' . $code;
                            if ($this->group_js) {
                                $this->content = str_replace($tag, '', $this->content);
                            }
                        }
                    }
                }
            }
            return true;
        }

        // No script files, great ;-)
        return false;
    }

    /**
     * Joins and optimizes JS
     *
     * @return boolean
     */
    public function minify()
    {
        foreach ($this->scripts as $k => $scriptsrc) {
            $script = '';
            if (preg_match('#^INLINE;#', $scriptsrc)) {
                /**
                 * Should we minify the specified inline javascript content
                 *
                 * @param boolean Default check minify value
                 * @param string  Javascript source
                 *
                 * @return boolean
                 */
                $check_allow_inline_minify = apply_filters('wpsol_js_inline_do_minify', $this->check_allow_minify, $scriptsrc);
                // Check allow inline js
                if (!$check_allow_inline_minify) {
                    continue;
                }

                //Inline script
                $script = preg_replace('#^INLINE;#', '', $scriptsrc);
                // re-hide comments to be able to do the removal based on tag from $this->content
                $script = $this->hideComments($script);
                $script = rtrim($script, ";\n\t\r") . ';';
            } else {
                /**
                 * Should we minify the specified javascript file
                 * The filter should return true to minify the file or false if it should not be minified
                 *
                 * @param boolean Default check minify value
                 * @param string  Script url
                 *
                 * @return boolean
                 */
                $check_allow_minify = apply_filters('wpsol_js_url_do_minify', $this->check_allow_minify, $scriptsrc);
                //Check allow minify
                if (!$check_allow_minify) {
                    continue;
                }

                //External script
                if ($scriptsrc !== false && file_exists($scriptsrc) && is_readable($scriptsrc)) {
                    $script = file_get_contents($scriptsrc);
                    $script = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $script);
                    $script = rtrim($script, ";\n\t\r") . ';';
                }
            }
            //Add try-catch?
            if ($this->trycatch) {
                $script = 'try{' . $script . '}catch(e){}';
            }

            if ($this->group_js) {
                $this->js_group[] = $script;
            } else {
                //Not minify with file min
                if ((strpos($scriptsrc, 'min.js') !== false)) {
                    continue;
                }
                $this->js_group_val[$k] = $script;
            }
        }

        if ($this->group_js) {
            $hashname = '';
            foreach ($this->js_group as $jscode) {
                $hashname .= $jscode;
            }
            //Check for already-minified code
            $this->md5hash = md5($hashname);
            $ccheck = new WpsolMinificationCache($this->md5hash, 'js');
            if ($ccheck->check()) {
                $this->js_after_group = $ccheck->retrieve();
                return true;
            }
            unset($ccheck);

            foreach ($this->js_group as $k => $jscode) {
                if ($this->minifyJS && class_exists('JSMin')) {
                    if (is_callable(array('JSMin', 'minify'))) {
                        $tmp_jscode = trim(JSMin::minify($jscode));
                        if (!empty($tmp_jscode)) {
                            $jscode = $tmp_jscode;
                            unset($tmp_jscode);
                        }
                    }
                }
                $jscode = $this->injectMinified($jscode);
                $this->js_after_group .= "\n".$jscode;
            }
        } else {
            foreach ($this->js_group_val as $k => $jscode) {
                //Check for already-minified code
                $this->md5hash = md5($jscode);
                $ccheck = new WpsolMinificationCache($this->md5hash, 'js');
                if ($ccheck->check()) {
                    $js_exist = $ccheck->retrieve();
                    $this->js_min_arr[$k] = $this->md5hash . '_wpsoljsgroup_' . $js_exist;
                    continue;
                }
                unset($ccheck);

                //$this->jscode has all the uncompressed code now.
                if (class_exists('JSMin')) {
                    if (is_callable(array('JSMin', 'minify'))) {
                        $tmp_jscode = trim(JSMin::minify($jscode));
                        if (!empty($tmp_jscode)) {
                            $jscode = $tmp_jscode;
                            unset($tmp_jscode);
                        }
                    }
                }
                $jscode = $this->injectMinified($jscode);
                $this->js_min_arr[$k] = $this->md5hash . '_wpsoljsgroup_' . $jscode;
            }
        }
        return true;
    }

    /**
     * Caches the JS in uncompressed, deflated and gzipped form.
     *
     * @return void
     */
    public function cache()
    {
        if ($this->group_js) {
            $cache = new WpsolMinificationCache($this->md5hash, 'js');
            if (!$cache->check()) {
                //Cache our code
                $cache->cache($this->js_after_group, 'text/javascript');
            }
            $this->url = WPSOL_CACHE_URL . $cache->getname();
        } else {
            if (!empty($this->js_min_arr)) {
                foreach ($this->js_min_arr as $k => $js_min) {
                    $namehash = substr($js_min, 0, strpos($js_min, '_wpsoljsgroup_'));
                    $js_code = substr($js_min, strpos($js_min, '_wpsoljsgroup_') + strlen('_wpsoljsgroup_'));
                    $cache = new WpsolMinificationCache($namehash, 'js');
                    if (!$cache->check()) {
                        //Cache our code
                        $cache->cache($js_code, 'text/javascript');
                    }
                    $this->url_group_arr[$k] = $namehash .'_wpsoljshash_'. WPSOL_CACHE_URL . $cache->getname();
                }
            }
        }

        if ($this->cache_external) {
            // Cache external script
            if (!empty($this->external_scripts)) {
                foreach ($this->external_scripts as $k => $v) {
                    if (strpos($v, '//') === 0) {
                        if (is_ssl()) {
                            $http = 'https:';
                        } else {
                            $http = 'http:';
                        }
                        $v = $http . $v;
                    }
                    $script = $this->getExternalData($v);
                    if (empty($script)) {
                        continue;
                    }
                    $this->md5hash = md5($script);
                    $ccache = new WpsolMinificationCache($this->md5hash, 'js');
                    if (!$ccache->check()) {
                        //Cache external code
                        $ccache->cache($script, 'text/javascript');
                    }
                    $this->external_local_path[$k] = WPSOL_CACHE_URL . $ccache->getname();
                }
            }
        }
    }

    /**
     * Returns the content
     *
     * @return mixed|string
     */
    public function getcontent()
    {
        // Restore the full content
        if (!empty($this->restofcontent)) {
            $this->content .= $this->restofcontent;
            $this->restofcontent = '';
        }
        // Add the scripts taking forcehead/ deferred (default) into account
        $defer = '';
        $async = '';
        if (!$this->move_to_footer) {
            $replaceTag = array('</head>', 'before');
        } else {
            $replaceTag = array('</body>', 'before');
        }

        if ($this->group_js) {
            $defer = 'defer ';
            $bodyreplacementpayload = '<script type="text/javascript" ' . $defer ;
            $bodyreplacementpayload .= $async . 'src="' . $this->url . '"></script>';
            $this->injectInHtml($bodyreplacementpayload, $replaceTag);
        } else {
            if ($this->move_to_footer && $this->minifyJS) {
                foreach ($this->all_script_compare as $k => $v) {
                    if (array_key_exists($k, $this->url_group_arr)) {
                        $this->all_script_compare[$k] = $this->url_group_arr[$k];
                    }
                }
                foreach ($this->all_script_compare as $k => $v) {
                    if (strpos($v, '_wpsoljshash_')) {
                        $namehash = substr($v, 0, strpos($v, '_wpsoljshash_'));
                        $url = substr($v, strpos($v, '_wpsoljshash_') + strlen('_wpsoljshash_'));
                        if (preg_match('#<script.*</script>#Usmi', $k, $matches)) {
                            $inlin_script = '';
                            $cache = new WpsolMinificationCache($namehash, 'js');
                            if ($cache->check()) {
                                $inlin_script = $cache->retrieve();
                            }
                            if (strlen($inlin_script) > 0) {
                                $script = '<script type="text/javascript" '.$async.$defer.'>'.$inlin_script.'</script>';
                            } else {
                                $script = '<script type="text/javascript" '.$async . $defer.'src="'.$url.'"></script>';
                            }
                        } else {
                            $script = '<script type="text/javascript" '.$async.$defer . 'src="' . $url . '"></script>';
                        }
                    } else {
                        if (preg_match('#<script.*</script>#Usmi', $k, $matches)) {
                            $script = '<script type="text/javascript" ' . $async . $defer . '>'.$v.'</script>';
                        } else {
                            $script = '<script type="text/javascript" ' . $async .$defer . 'src="' . $v . '"></script>';
                        }
                    }
                    // Exclude script from "move to footer"
                    if ($this->checkExcludeFile($k, $this->exclude_move_to_footer)) {
                        $this->injectMinifyToHtml($k, $script);
                    } else {
                        // Remove old script
                        $this->injectMinifyToHtml($k, '');
                        // Inject script to footer
                        $this->injectInHtml($script, $replaceTag);
                    }
                }
            } else {
                foreach ($this->url_group_arr as $k => $v) {
                    $namehash = substr($v, 0, strpos($v, '_wpsoljshash_'));
                    $url = substr($v, strpos($v, '_wpsoljshash_') + strlen('_wpsoljshash_'));

                    if (preg_match('#<script.*</script>#Usmi', $k, $matches)) {
                        $inline_script = '';
                        $cache = new WpsolMinificationCache($namehash, 'js');
                        if ($cache->check()) {
                            $inline_script = $cache->retrieve();
                        }
                        if (strlen($inline_script) > 0) {
                            $script = '<script type="text/javascript" ' . $defer . '>'.$inline_script.'</script>';
                        } else {
                            $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                        }
                    } else {
                        $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                    }
                    $this->injectMinifyToHtml($k, $script);
                }
            }
        }
        //Inject External script
        if (!empty($this->external_local_path)) {
            foreach ($this->external_local_path as $k => $url) {
                $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                $this->injectMinifyToHtml($k, $script);
            }
        }

        // restore comments
        $this->content = $this->restoreComments($this->content);

        // Restore IE hacks
        $this->content = $this->restoreIehacks($this->content);

        // Restore noptimize
        $this->content = $this->restoreNoptimize($this->content);

        // Return the modified HTML
        return $this->content;
    }

    /**
     * Checks agains the blacklist
     *
     * @param string $tag Tag to check is move
     *
     * @return boolean
     */
    private function ismovable($tag)
    {
        foreach ($this->domove as $match) {
            if (strpos($tag, $match) !== false) {
                //Matched something
                return false;
            }
        }

        foreach ($this->domovelast as $match) {
            if (strpos($tag, $match) !== false) {
                //Matched, return false
                return false;
            }
        }

        foreach ($this->dontmove as $match) {
            if (strpos($tag, $match) !== false) {
                //Matched something
                return false;
            }
        }

        //If we're here it's safe to move
        return true;
    }

    /**
     * Determines wheter a <script> $tag should be aggregated or not.
     *
     * We consider these as "aggregation-safe" currently:
     * - script tags without a `type` attribute
     * - script tags with an explicit `type` of `text/javascript`, 'text/ecmascript',
     *   'application/javascript' or 'application/ecmascript'
     * Everything else should return false.
     *
     * @param string $tag Tag to aggregate
     *
     * @return boolean
     *
     * original function by https://github.com/zytzagoo/ on his AO fork, thanks Tomas!
     */
    public function shouldAggregate($tag)
    {
        preg_match('#<(script[^>]*)>#i', $tag, $scripttag);
        if (strpos($scripttag[1], 'type=') === false) {
            return true;
        } elseif (preg_match('/type=["\']?(?:text|application)\/(?:javascript|ecmascript)["\']?/i', $scripttag[1])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get content of external script
     *
     * @param string $url External url
     *
     * @return mixed
     */
    public function getExternalData($url)
    {
        $data = '';
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($ch);
            curl_close($ch);
        }
        return $data;
    }
}
