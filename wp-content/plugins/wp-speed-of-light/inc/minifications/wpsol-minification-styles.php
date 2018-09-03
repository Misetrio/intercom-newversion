<?php
/*
 *  Based on some work of autoptimize plugin
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WpsolMinificationStyles
 */
class WpsolMinificationStyles extends WpsolMinificationBase
{
    /**
     * Init minify css params
     *
     * @var boolean
     */
    private $minifyCSS = false;
    /**
     * Init css params
     *
     * @var array
     */
    private $css = array();
    /**
     * Init css code params
     *
     * @var array
     */
    private $csscode = array();
    /**
     * Init url params
     *
     * @var array
     */
    private $url = array();
    /**
     * Init rest of content params
     *
     * @var string
     */
    private $restofcontent = '';
    /**
     * Init mhtml params
     *
     * @var string
     */
    private $mhtml = '';
    /**
     * Init data uris params
     *
     * @var boolean
     */
    private $datauris = false;
    /**
     * Init hash map params
     *
     * @var array
     */
    private $hashmap = array();
    /**
     * Init already minified params
     *
     * @var boolean
     */
    private $alreadyminified = false;
    /**
     * Init inline params
     *
     * @var boolean
     */
    private $inline = false;
    /**
     * Init defer params
     *
     * @var boolean
     */
    private $defer = false;
    /**
     * Init defer inline params
     *
     * @var boolean
     */
    private $defer_inline = false;
    /**
     * Init white list params
     *
     * @var array
     */
    private $whitelist = array();
    /**
     * Init css inline size params
     *
     * @var string
     */
    private $cssinlinesize = '';
    /**
     * Init google font params
     *
     * @var array
     */
    private $grfonts = array('fonts.googleapis.com');
    /**
     * Init group fonts params
     *
     * @var boolean
     */
    private $group_fonts = false;
    /**
     * Init include inline params
     *
     * @var boolean
     */
    private $include_inline = false;
    /**
     * Init inject min late params
     *
     * @var string
     */
    private $inject_min_late = '';
    /**
     * Init group css params
     *
     * @var boolean
     */
    private $group_css = false;
    /**
     * Init css group value params
     *
     * @var array
     */
    private $css_group_val = array();
    /**
     * Init css min array params
     *
     * @var array
     */
    private $css_min_arr = array();
    /**
     * Init isset min file params
     *
     * @var boolean
     */
    private $issetminfile = false;
    /**
     * Init url group params
     *
     * @var array
     */
    private $url_group_arr = array();
    /**
     * Init css exclude params
     *
     * @var array
     */
    private $css_exclude = array();

    /**
     * Reads the page and collects style tags
     *
     * @param array $options Option of minify css
     *
     * @return boolean
     */
    public function read($options)
    {
        /**
         * Should we minify the specified inline css content
         *
         * @param true Default value
         * @param string Css content
         *
         * @return boolean
         */
        $noptimizeCSS = apply_filters('wpsol_css_inline_do_minify', true, $this->content);
        if (!$noptimizeCSS) {
            return false;
        }

        if ($options['groupfonts'] === true) {
            $this->group_fonts = true;
        }

        /**
         * Apply filter inline size of css
         *
         * @param string wpsol_css_inline_max_size
         * @param integer Default value
         *
         * @return integer
         */
        $this->cssinlinesize = apply_filters('wpsol_css_inline_max_size', 256);

        // filter to "late inject minified CSS", default to true for now (it is faster)
        $this->inject_min_late = apply_filters('wpsol_filter_css_inject_min_late', true);

        // Remove everything that's not the header
        if (apply_filters('wpsol_filter_css_justhead', $options['justhead']) === true) {
            $content = explode('</head>', $this->content, 2);
            $this->content = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }

        // include inline?
        if (apply_filters('wpsol_css_include_inline', $options['include_inline']) === true) {
            $this->include_inline = true;
        }

        // group css?
        if (apply_filters('wpsol_css_include_inline', $options['groupcss']) === true) {
            $this->group_css = true;
        }

        //custom css,font exclude
        if (!empty($options['exclude_css'])) {
            $this->css_exclude = $options['exclude_css'];
        }

        if ($options['minifyCSS']) {
            $this->minifyCSS = true;
        }

        // what CSS shouldn't be autoptimized
        $excludeCSS = $options['css_exclude'];

        if (!empty($excludeCSS)) {
            $this->dontmove = array_filter(array_map('trim', explode(',', $excludeCSS)));
        } else {
            $this->dontmove = '';
        }

        // should we defer css?
        // value: true/ false
        $this->defer = $options['defer'];

        // should we inline while deferring?
        // value: inlined CSS
        $this->defer_inline = $options['defer_inline'];

        // should we inline?
        // value: true/ false
        $this->inline = $options['inline'];

        // get cdn url
        $this->cdn_url = $options['cdn_url'];

        // Store data: URIs setting for later use
        $this->datauris = $options['datauris'];

        // noptimize me
        $this->content = $this->hideNoptimize($this->content);

        // exclude (no)script, as those may contain CSS which should be left as is
        if (strpos($this->content, '<script') !== false) {
            $this->content = preg_replace_callback(
                '#<(?:no)?script.*?<\/(?:no)?script>#is',
                function ($matches) {
                    return '%%SCRIPT' . WPSOL_HASH . '%%' . base64_encode($matches[0]) . '%%SCRIPT%%';
                },
                $this->content
            );
        }

        // Save IE hacks
        $this->content = $this->hideIehacks($this->content);

        // hide comments
        $this->content = $this->hideComments($this->content);

        // Get <style> and <link>
        if (preg_match_all('#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $k => $tag) {
                if ($this->group_fonts && $this->isremovable($tag, $this->grfonts)) {
                    $media = array('all');
                    if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                        // google font link
                        $this->css[] = array($media, $source[2]);
                        $this->content = str_replace($tag, '', $this->content);
                    }
                } elseif ($this->ismovable($tag)) {
                    // Get the media
                    if (strpos($tag, 'media=') !== false) {
                        preg_match('#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $medias);
                        $medias = explode(',', $medias[1]);
                        $media = array();
                        foreach ($medias as $elem) {
                            if (empty($elem)) {
                                $elem = 'all';
                            }
                            $media[] = $elem;
                        }
                    } else {
                        // No media specified - applies to all
                        $media = array('all');
                    }

                    if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                        // <link>
                        $url = current(explode('?', $source[2], 2));

                        //exclude css file
                        if (in_array($url, $this->css_exclude)) {
                            continue;
                        }

                        $path = $this->getpath($url);

                        if ($path !== false && preg_match('#\.css$#', $path)) {
                            // Good link
                            $this->css[] = array($media, $path);
                        } else {
                            // Link is dynamic (.php etc)
                            $tag = '';
                        }
                    } else {
                        // inline css in style tags can be wrapped in comment tags, so restore comments
                        $tag = $this->restoreComments($tag);
                        preg_match('#<style.*>(.*)</style>#Usmi', $tag, $code);

                        // and re-hide them to be able to to the removal based on tag
                        $tag = $this->hideComments($tag);

                        if ($this->include_inline) {
                            $regex = '#^.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*$#sm';
                            $code = preg_replace($regex, '$1', $code[1]);
                            // Font check
                            $font_face = array('@font-face');
                            if (!$this->group_fonts && $this->isremovable($code, $font_face)) {
                                continue;
                            }
                            $this->css[] = array($media, 'INLINE;' . $code);
                        } else {
                            $tag = '';
                        }
                    }
                    // Remove the original style tag
                    $this->content = str_replace($tag, '', $this->content);
                }
            }
            return true;
        }
        // Really, no styles?
        return false;
    }

    /**
     * Joins and optimizes CSS
     *
     * @return boolean
     */
    public function minify()
    {
        foreach ($this->css as $group) {
            list($media, $css) = $group;
            if (preg_match('#^INLINE;#', $css)) {
                // <style>
                $css = preg_replace('#^INLINE;#', '', $css);
                $css = $this->fixurls(ABSPATH . '/index.php', $css);
            } else {
                /**
                 * Apply filter to allow or not minifiying a css url
                 *
                 * @param boolean Default check minify value
                 * @param string  Style url
                 *
                 * @return boolean|string
                 */
                $minify_this_css = apply_filters('wpsol_css_url_do_minify', true, $css);
                if (!$minify_this_css) {
                    continue;
                }
                //<link>
                if ($css !== false && file_exists($css) && is_readable($css)) {
                    $cssPath = $css;
                    $css = $this->fixurls($cssPath, file_get_contents($cssPath));
                    $css = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $css);

                    if ($this->canInjectLate($cssPath, $css)) {
                        $css = '%%INJECTLATER' . WPSOL_HASH . '%%' . base64_encode($cssPath) . '|';
                        $css .= md5($css) . '%%INJECTLATER%%';
                    }
                } else {
                    if (strpos($css, '//') === 0) {
                        if (is_ssl()) {
                            $http = 'https:';
                        } else {
                            $http = 'http:';
                        }
                        $css = $http . $css;
                    }
                    if ($this->isremovable($css, $this->grfonts)) {
                        $verify_peer = array(
                            'verify_peer'      => false,
                            'verify_peer_name' => false
                        );
                        // check ssl verify
                        $streamContext = stream_context_create(
                            array('ssl' => $verify_peer)
                        );
                        //get css from server
                        $css = file_get_contents($css, false, $streamContext);
                    } else {
                        $css = '';
                    }
                }
            }
            if ($this->group_css === true) {
                foreach ($media as $elem) {
                    if (!isset($this->csscode[$elem])) {
                        $this->csscode[$elem] = '';
                    }
                    $this->csscode[$elem] .= "\n/*FILESTART*/" . $css;
                }
            } else {
                foreach ($media as $elem) {
                    $this->css_group_val[] = $elem . '_wpsolcssgroup_' . $css;
                }
            }
        }
        if ($this->group_css === true) {
            // Check for duplicate code
            $md5list = array();
            $tmpcss = $this->csscode;
            foreach ($tmpcss as $media => $code) {
                $md5sum = md5($code);
                $medianame = $media;
                foreach ($md5list as $med => $sum) {
                    // If same code
                    if ($sum === $md5sum) {
                        //Add the merged code
                        $medianame = $med . ', ' . $media;
                        $this->csscode[$medianame] = $code;
                        $md5list[$medianame] = $md5list[$med];
                        unset($this->csscode[$med], $this->csscode[$media]);
                        unset($md5list[$med]);
                    }
                }
                $md5list[$medianame] = $md5sum;
            }
            unset($tmpcss);

            // Manage @imports, while is for recursive import management
            foreach ($this->csscode as &$thiscss) {
                // Flag to trigger import reconstitution and var to hold external imports
                $fiximports = false;
                $external_imports = '';

                while (preg_match_all('#^(/*\s?)@import.*(?:;|$)#Um', $thiscss, $matches)) {
                    foreach ($matches[0] as $import) {
                        if ($this->isremovable($import, $this->grfonts)) {
                            $thiscss = str_replace($import, '', $thiscss);
                            $import_ok = true;
                        } else {
                            $regex = '#^.*((?:https?:|ftp:)?//.*\.css).*$#';
                            $url = trim(preg_replace($regex, '$1', trim($import)), " \t\n\r\0\x0B\"'");
                            $path = $this->getpath($url);
                            $import_ok = false;
                            if (file_exists($path) && is_readable($path)) {
                                $code = addcslashes($this->fixurls($path, file_get_contents($path)), '\\');
                                $code = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $code);

                                if ($this->canInjectLate($path, $code)) {
                                    $code = '%%INJECTLATER' . WPSOL_HASH . '%%' . base64_encode($path) . '|';
                                    $code .= md5($code) . '%%INJECTLATER%%';
                                }

                                if (!empty($code)) {
                                    $regex = '#(/\*FILESTART\*/.*)' . preg_quote($import, '#') . '#Us';
                                    $tmp_thiscss = preg_replace($regex, '/*FILESTART2*/' . $code . '$1', $thiscss);
                                    if (!empty($tmp_thiscss)) {
                                        $thiscss = $tmp_thiscss;
                                        $import_ok = true;
                                        unset($tmp_thiscss);
                                    }
                                    unset($code);
                                }
                            }
                        }

                        if (!$import_ok) {
                            // external imports and general fall-back
                            $external_imports .= $import;
                            $thiscss = str_replace($import, '', $thiscss);
                            $fiximports = true;
                        }
                    }
                    $thiscss = preg_replace('#/\*FILESTART\*/#', '', $thiscss);
                    $thiscss = preg_replace('#/\*FILESTART2\*/#', '/*FILESTART*/', $thiscss);
                }

                // add external imports to top of aggregated CSS
                if ($fiximports) {
                    $thiscss = $external_imports . $thiscss;
                }
            }
            unset($thiscss);

            // $this->csscode has all the uncompressed code now.
            $mhtmlcount = 0;
            foreach ($this->csscode as &$code) {
                // Check for already-minified code
                $hash = md5($code);
                $ccheck = new WpsolMinificationCache($hash, 'css');
                if ($ccheck->check()) {
                    $code = $ccheck->retrieve();
                    $this->hashmap[md5($code)] = $hash;
                    continue;
                }
                unset($ccheck);

                // Do the imaging!
                $imgreplace = array();
                preg_match_all('#(background[^;}]*url\((?!\s?"?\s?data)(.*)\)[^;}]*)(?:;|$|})#Usm', $code, $matches);

                if (($this->datauris === true) && (function_exists('base64_encode')) && (is_array($matches))) {
                    foreach ($matches[2] as $count => $quotedurl) {
                        $iurl = trim($quotedurl, " \t\n\r\0\x0B\"'");

                        // if querystring, remove it from url
                        if (strpos($iurl, '?') !== false) {
                            $iurl = strtok($iurl, '?');
                        }

                        $ipath = $this->getpath($iurl);

                        $datauri_max_size = 4096;

                        $datauri_exclude = '';
                        if (!empty($datauri_exclude)) {
                            $no_datauris = array_filter(array_map('trim', explode(',', $datauri_exclude)));
                            foreach ($no_datauris as $no_datauri) {
                                if (strpos($iurl, $no_datauri) !== false) {
                                    $ipath = false;
                                    break;
                                }
                            }
                        }

                        if ($ipath !== false && preg_match('#\.(jpe?g|png|gif|bmp)$#i', $ipath) &&
                            file_exists($ipath) && is_readable($ipath) && filesize($ipath) <= $datauri_max_size
                        ) {
                            $ihash = md5($ipath);
                            $icheck = new WpsolMinificationCache($ihash, 'img');
                            if ($icheck->check()) {
                                // we have the base64 image in cache
                                $headAndData = $icheck->retrieve();
                                $_base64data = explode(';base64,', $headAndData);
                                $base64data = $_base64data[1];
                            } else {
                                // It's an image and we don't have it in cache, get the type
                                $explA = explode('.', $ipath);
                                $type = end($explA);

                                switch ($type) {
                                    case 'jpeg':
                                        $dataurihead = 'data:image/jpeg;base64,';
                                        break;
                                    case 'jpg':
                                        $dataurihead = 'data:image/jpeg;base64,';
                                        break;
                                    case 'gif':
                                        $dataurihead = 'data:image/gif;base64,';
                                        break;
                                    case 'png':
                                        $dataurihead = 'data:image/png;base64,';
                                        break;
                                    case 'bmp':
                                        $dataurihead = 'data:image/bmp;base64,';
                                        break;
                                    default:
                                        $dataurihead = 'data:application/octet-stream;base64,';
                                }

                                // Encode the data
                                $base64data = base64_encode(file_get_contents($ipath));
                                $headAndData = $dataurihead . $base64data;

                                // Save in cache
                                $icheck->cache($headAndData, 'text/plain');
                            }
                            unset($icheck);

                            // Add it to the list for replacement
                            $imgre1 = str_replace($quotedurl, $headAndData, $matches[1][$count]);
                            $imgre2 = str_replace($quotedurl, 'mhtml:%%MHTML%%!' . $mhtmlcount, $matches[1][$count]);
                            $imgreplace[$matches[1][$count]] = $imgre1 . ";\n*" .
                                $imgre2 . ";\n_" . $matches[1][$count] . ';';

                            // Store image on the mhtml document
                            $this->mhtml .= "--_\r\nContent-Location:".$mhtmlcount."\r\n";
                            $this->mhtml .= "Content-Transfer-Encoding:base64\r\n\r\n".$base64data."\r\n";
                            $mhtmlcount++;
                        } else {
                            // just cdn the URL if applicable
                            if (!empty($this->cdn_url)) {
                                $url = trim($quotedurl, " \t\n\r\0\x0B\"'");
                                $cdn_url = $this->urlReplaceCdn($url);
                                $imgreplace[$matches[1][$count]] =
                                    str_replace($quotedurl, $cdn_url, $matches[1][$count]);
                            }
                        }
                    }
                } elseif ((is_array($matches)) && (!empty($this->cdn_url))) {
                    // change background image urls to cdn-url
                    foreach ($matches[2] as $count => $quotedurl) {
                        $url = trim($quotedurl, " \t\n\r\0\x0B\"'");
                        $cdn_url = $this->urlReplaceCdn($url);
                        $imgreplace[$matches[1][$count]] = str_replace($quotedurl, $cdn_url, $matches[1][$count]);
                    }
                }

                if (!empty($imgreplace)) {
                    $code = str_replace(array_keys($imgreplace), array_values($imgreplace), $code);
                }

                // CDN the fonts!
                if ((!empty($this->cdn_url)) &&
                    (version_compare(PHP_VERSION, '5.3.0') >= 0)
                ) {
                    $fontreplace = array();
                    include_once(WPSOL_PLUGIN_DIR . 'inc/minifications/config/minificationFontRegex.php');
                    $fonturl_regex = '';
                    preg_match_all($fonturl_regex, $code, $matches);
                    if (is_array($matches)) {
                        foreach ($matches[8] as $count => $quotedurl) {
                            $url = trim($quotedurl, " \t\n\r\0\x0B\"'");
                            $cdn_url = $this->urlReplaceCdn($url);
                            $fontreplace[$matches[8][$count]] = str_replace($quotedurl, $cdn_url, $matches[8][$count]);
                        }
                        if (!empty($fontreplace)) {
                            $code = str_replace(array_keys($fontreplace), array_values($fontreplace), $code);
                        }
                    }
                }

                if ($this->minifyCSS) {
                    // Minify
                    if (($this->alreadyminified !== true)) {
                        if (class_exists('Minify_CSS_Compressor')) {
                            $tmp_code = trim(Minify_CSS_Compressor::process($code));
                        } elseif (class_exists('CSSmin')) {
                            $cssmin = new CSSmin();
                            if (method_exists($cssmin, 'run')) {
                                $tmp_code = trim($cssmin->run($code));
                            } elseif (is_callable(array($cssmin, 'minify'))) {
                                $tmp_code = trim(CssMin::minify($code));
                            }
                        }
                        if (!empty($tmp_code)) {
                            $code = $tmp_code;
                            unset($tmp_code);
                        }
                    }
                }
                $code = $this->injectMinified($code);

                $this->hashmap[md5($code)] = $hash;
            }
            unset($code);
        } else {
            foreach ($this->css_group_val as $value) {
                $media = substr($value, 0, strpos($value, '_wpsolcssgroup_'));
                $css = substr($value, strpos($value, '_wpsolcssgroup_') + strlen('_wpsolcssgroup_'));

                $hash = md5($css);
                $ccheck = new WpsolMinificationCache($hash, 'css');
                if ($ccheck->check()) {
                    $css_exist = $ccheck->retrieve();
                    $this->css_min_arr[] = $media . '_wpsolmedia_' . $hash . '_wpsolkey_' . $css_exist;
                    continue;
                }
                unset($ccheck);

                // Minify

                if (class_exists('Minify_CSS_Compressor')) {
                    $tmp_code = trim(Minify_CSS_Compressor::process($css));
                } elseif (class_exists('CSSmin')) {
                    $cssmin = new CSSmin();
                    if (method_exists($cssmin, 'run')) {
                        $tmp_code = trim($cssmin->run($css));
                    } elseif (is_callable(array($cssmin, 'minify'))) {
                        $tmp_code = trim(CssMin::minify($css));
                    }
                }
                if (!empty($tmp_code)) {
                    $css = $tmp_code;
                    unset($tmp_code);
                }

                $css = $this->injectMinified($css);

                $this->css_min_arr[] = $media . '_wpsolmedia_' . $hash . '_wpsolkey_' . $css;
            }
            unset($css);
        }
        return true;
    }

    /**
     *  Caches the CSS in uncompressed, deflated and gzipped form.
     *
     * @return void
     */
    public function cache()
    {
        if ($this->datauris) {
            // MHTML Preparation
            $this->mhtml = "/*\r\nContent-Type: multipart/related; boundary=\"_\"\r\n\r\n" . $this->mhtml . "*/\r\n";
            $md5 = md5($this->mhtml);
            $cache = new WpsolMinificationCache($md5, 'txt');
            if (!$cache->check()) {
                // Cache our images for IE
                $cache->cache($this->mhtml, 'text/plain');
            }
            $mhtml = WPSOL_CACHE_URL . $cache->getname();
        }
        if ($this->group_css === true) {
            // CSS cache
            foreach ($this->csscode as $media => $code) {
                $md5 = $this->hashmap[md5($code)];

                if ($this->datauris) {
                    // Images for ie! Get the right url
                    $code = str_replace('%%MHTML%%', $mhtml, $code);
                }

                $cache = new WpsolMinificationCache($md5, 'css');
                if (!$cache->check()) {
                    // Cache our code
                    $cache->cache($code, 'text/css');
                }
                $this->url[$media] = WPSOL_CACHE_URL . $cache->getname();
            }
        } else {
            foreach ($this->css_min_arr as $value) {
                $media = substr($value, 0, strpos($value, '_wpsolmedia_'));
                $code = substr($value, strpos($value, '_wpsolmedia_') + strlen('_wpsolmedia_'));
                $hash = substr($code, 0, strpos($code, '_wpsolkey_'));
                $css = substr($code, strpos($code, '_wpsolkey_') + strlen('_wpsolkey_'));

                $cache = new WpsolMinificationCache($hash, 'css');
                if (!$cache->check()) {
                    // Cache our code
                    $cache->cache($css, 'text/css');
                }
                $url_group = $media . '_wpsolmedia_' . $hash;
                $url_group .= '_wpsolkey_' . WPSOL_CACHE_URL . $cache->getname();
                $this->url_group_arr[] = $url_group;
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
        // restore IE hacks
        $this->content = $this->restoreIehacks($this->content);

        // restore comments
        $this->content = $this->restoreComments($this->content);

        // restore (no)script
        if (strpos($this->content, '%%SCRIPT%%') !== false) {
            $this->content = preg_replace_callback(
                '#%%SCRIPT' . WPSOL_HASH . '%%(.*?)%%SCRIPT%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $this->content
            );
        }

        // restore noptimize
        $this->content = $this->restoreNoptimize($this->content);

        //Restore the full content
        if (!empty($this->restofcontent)) {
            $this->content .= $this->restofcontent;
            $this->restofcontent = '';
        }

        // Inject the new stylesheets
        $replaceTag = array('<title', 'before');

        if ($this->group_css === true) {
            if ($this->inline === true) {
                foreach ($this->csscode as $media => $code) {
                    $payload = '<style type="text/css" media="' . $media . '">' . $code . '</style>';
                    $this->injectInHtml($payload, $replaceTag);
                }
            } else {
                if ($this->defer === true) {
                    $deferredCssBlock = "<script data-cfasync='false'>function lCss(url,media) {";
                    // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                    $deferredCssBlock .= "var d=document;}var l=d.createElement('link');l.rel='stylesheet';l.type='text/css';l.href=url;l.media=media;
aoin=d.getElementsByTagName('noscript')[0];aoin.parentNode.insertBefore(l,aoin.nextSibling);}function deferredCSS() {";
                    $noScriptCssBlock = '<noscript>';
                    $defer_inline_code = $this->defer_inline;

                    if (!empty($defer_inline_code)) {
                        $iCssHash = md5($defer_inline_code);
                        $iCssCache = new WpsolMinificationCache($iCssHash, 'css');
                        if ($iCssCache->check()) {
                            // we have the optimized inline CSS in cache
                            $defer_inline_code = $iCssCache->retrieve();
                        } else {
                            if (class_exists('Minify_CSS_Compressor')) {
                                $tmp_code = trim(Minify_CSS_Compressor::process($this->defer_inline));
                            } elseif (class_exists('CSSmin')) {
                                $cssmin = new CSSmin();
                                $tmp_code = trim($cssmin->run($defer_inline_code));
                            }

                            if (!empty($tmp_code)) {
                                $defer_inline_code = $tmp_code;
                                $iCssCache->cache($defer_inline_code, 'text/css');
                                unset($tmp_code);
                            }
                        }
                        $code_out = '<style type="text/css" id="aoatfcss" media="all">';
                        $code_out .= $defer_inline_code . '</style>';
                        $this->injectInHtml($code_out, $replaceTag);
                    }
                }

                foreach ($this->url as $media => $url) {
                    $url = $this->urlReplaceCdn($url);

                    //Add the stylesheet either deferred (import at bottom) or normal links in head
                    if ($this->defer === true) {
                        $deferredCssBlock .= "lCss('" . $url . "','" . $media . "');";
                        $noScriptCssBlock .= '<link type="text/css" media="' . $media;
                        // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                        $noScriptCssBlock .= '" href="' . $url . '" rel="stylesheet" />';
                    } else {
                        if (strlen($this->csscode[$media]) > $this->cssinlinesize) {
                            $payload = '<link type="text/css" media="' . $media;
                            // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                            $payload .= '" href="' . $url . '" rel="stylesheet" />';
                            $this->injectInHtml($payload, $replaceTag);
                        } elseif (strlen($this->csscode[$media]) > 0) {
                            $payload = '<style type="text/css" media="' . $media . '">';
                            $payload .= $this->csscode[$media] . '</style>';
                            $this->injectInHtml($payload, $replaceTag);
                        }
                    }
                }

                if ($this->defer === true) {
                    $deferredCssBlock .= "}if(window.addEventListener){
window.addEventListener('DOMContentLoaded',deferredCSS,false);}else{window.onload = deferredCSS;}</script>";
                    $noScriptCssBlock .= '</noscript>';
                    $this->injectInHtml($noScriptCssBlock, $replaceTag);
                    $this->injectInHtml($deferredCssBlock, array('</body>', 'before'));
                }
            }
        } else {
            if ($this->inline === true) {
                foreach ($this->csscode as $media => $code) {
                    $payload = '<style type="text/css" media="' . $media . '">' . $code . '</style>';
                    $this->injectInHtml($payload, $replaceTag);
                }
            } else {
                if ($this->defer === true) {
                    $deferredCssBlock = "<script data-cfasync='false'>function lCss(url,media) {
    var d=document;
}var l=d.createElement('link');";
                    // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                    $deferredCssBlock .= "l.rel='stylesheet';l.type='text/css';l.href=url;l.media=media;
aoin=d.getElementsByTagName('noscript')[0];aoin.parentNode.insertBefore(l,aoin.nextSibling);
}function deferredCSS() {";
                    $noScriptCssBlock = '<noscript>';
                    $defer_inline_code = $this->defer_inline;

                    if (!empty($defer_inline_code)) {
                        $iCssHash = md5($defer_inline_code);
                        $iCssCache = new WpsolMinificationCache($iCssHash, 'css');
                        if ($iCssCache->check()) {
                            // we have the optimized inline CSS in cache
                            $defer_inline_code = $iCssCache->retrieve();
                        } else {
                            if (class_exists('Minify_CSS_Compressor')) {
                                $tmp_code = trim(Minify_CSS_Compressor::process($this->defer_inline));
                            } elseif (class_exists('CSSmin')) {
                                $cssmin = new CSSmin();
                                $tmp_code = trim($cssmin->run($defer_inline_code));
                            }

                            if (!empty($tmp_code)) {
                                $defer_inline_code = $tmp_code;
                                $iCssCache->cache($defer_inline_code, 'text/css');
                                unset($tmp_code);
                            }
                        }
                        $code_out = '<style type="text/css" id="aoatfcss" media="all">';
                        $code_out .= $defer_inline_code . '</style>';
                        $this->injectInHtml($code_out, $replaceTag);
                    }
                }

                foreach ($this->url_group_arr as $value) {
                    $media = substr($value, 0, strpos($value, '_wpsolmedia_'));
                    $code = substr($value, strpos($value, '_wpsolmedia_') + strlen('_wpsolmedia_'));
                    $hash = substr($code, 0, strpos($code, '_wpsolkey_'));
                    $url = substr($code, strpos($code, '_wpsolkey_') + strlen('_wpsolkey_'));

                    $cache = new WpsolMinificationCache($hash, 'css');
                    if ($cache->check()) {
                        $csscode = $cache->retrieve();
                    }
                    //Add the stylesheet either deferred (import at bottom) or normal links in head
                    if ($this->defer === true) {
                        $deferredCssBlock .= "lCss('" . $url . "','" . $media . "');";
                        $noScriptCssBlock .= '<link type="text/css" media="' . $media;
                        // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                        $noScriptCssBlock .= '" href="' . $url . '" rel="stylesheet" />';
                    } else {
                        if (strlen($csscode) > $this->cssinlinesize) {
                            $url = $this->urlReplaceCdn($url);
                            $payload = '<link type="text/css" media="' . $media . '" href="';
                            // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                            $payload .= $url . '" rel="stylesheet" />';
                            $this->injectInHtml($payload, $replaceTag);
                        } elseif (strlen($csscode) > 0) {
                            $payload = '<style type="text/css" media="' . $media . '">' . $csscode . '</style>';
                            $this->injectInHtml($payload, $replaceTag);
                        }
                    }
                }

                if ($this->defer === true) {
                    $deferredCssBlock .= "}if(window.addEventListener){
window.addEventListener('DOMContentLoaded',deferredCSS,false);
}else{window.onload = deferredCSS;}</script>";
                    $noScriptCssBlock .= '</noscript>';
                    $this->injectInHtml($noScriptCssBlock, $replaceTag);
                    $this->injectInHtml($deferredCssBlock, array('</body>', 'before'));
                }
            }
        }


        //Return the modified stylesheet
        return $this->content;
    }

    /**
     * Fix urls to avoid breaking URLs
     *
     * @param string $file Url of file
     * @param string $code Css code
     *
     * @return mixed
     */
    public static function fixurls($file, $code)
    {
        $file = str_replace(WPSOL_ROOT_DIR, '/', $file);
        $dir = dirname($file); //Like /wp-content

        // quick fix for import-troubles in e.g. arras theme
        $code = preg_replace('#@import ("|\')(.+?)\.css("|\')#', '@import url("${2}.css")', $code);

        if (preg_match_all('#url\((?!data)(?!\#)(?!"\#)(.*)\)#Usi', $code, $matches)) {
            $replace = array();
            foreach ($matches[1] as $k => $url) {
                // Remove quotes
                $url = trim($url, " \t\n\r\0\x0B\"'");
                $noQurl = trim($url, "\"'");
                if ($url !== $noQurl) {
                    $removedQuotes = true;
                } else {
                    $removedQuotes = false;
                }
                $url = $noQurl;
                if (substr($url, 0, 1) === '/' || preg_match('#^(https?://|ftp://|data:)#i', $url)) {
                    //URL is absolute
                    continue;
                } else {
                    // relative URL
                    $str_replace = str_replace('//', '/', $dir . '/' . $url);
                    $subject = str_replace(' ', '%20', WPSOL_WP_ROOT_URL . $str_replace);
                    $newurl = preg_replace('/https?:/', '', $subject);

                    $hash = md5($url);
                    $code = str_replace($matches[0][$k], $hash, $code);

                    if (!empty($removedQuotes)) {
                        $replace[$hash] = 'url(\'' . $newurl . '\')';
                    } else {
                        $replace[$hash] = 'url(' . $newurl . ')';
                    }
                }
            }
            //Do the replacing here to avoid breaking URLs
            $code = str_replace(array_keys($replace), array_values($replace), $code);
        }
        return $code;
    }

    /**
     * Check whitelist
     *
     * @param string $tag Tag from content
     *
     * @return boolean
     */
    private function ismovable($tag)
    {
        if (!empty($this->whitelist)) {
            foreach ($this->whitelist as $match) {
                if (strpos($tag, $match) !== false) {
                    return true;
                }
            }
            // no match with whitelist
            return false;
        } else {
            if (is_array($this->dontmove)) {
                foreach ($this->dontmove as $match) {
                    if (strpos($tag, $match) !== false) {
                        //Matched something
                        return false;
                    }
                }
            }

            //If we're here it's safe to move
            return true;
        }
    }

    /**
     * Compare path types can Inject Late
     *
     * @param string $cssPath Css to inject
     * @param string $css     Css to replace
     *
     * @return boolean
     */
    private function canInjectLate($cssPath, $css)
    {
        if ((strpos($cssPath, 'min.css') === false) || ($this->inject_min_late !== true)) {
            // late-inject turned off or file not minified based on filename
            return false;
        } elseif (strpos($css, '@import') !== false) {
            // can't late-inject files with imports as those need to be aggregated
            return false;
        } elseif ((strpos($css, '@font-face') !== false) &&
            (apply_filters('wpsol_filter_css_fonts_cdn', false) === true) && (!empty($this->cdn_url))
        ) {
            // don't late-inject CSS with font-src's if fonts are set to be CDN'ed
            return false;
        } elseif ((($this->datauris === true) || (!empty($this->cdn_url))) &&
            preg_match('#background[^;}]*url\(#Ui', $css)
        ) {
            // don't late-inject CSS with images if CDN is set OR is image inlining is on
            return false;
        } else {
            // phew, all is safe, we can late-inject
            return true;
        }
    }
}
