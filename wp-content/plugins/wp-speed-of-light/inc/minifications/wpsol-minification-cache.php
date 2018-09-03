<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/*
 *  Based on some work of autoptimize plugin
 */

/**
 * Class WpsolMinificationCache
 */
class WpsolMinificationCache
{
    /**
     * Init file name variable
     *
     * @var string
     */
    private $filename;
    /**
     * Init mine params
     *
     * @var string
     */
    private $mime;
    /**
     * Init cache directory params
     *
     * @var string
     */
    private $cachedir;
    /**
     * Init delayed params
     *
     * @var string
     */
    private $delayed;

    /**
     * WpsolMinificationCache constructor.
     *
     * @param string $md5 MD5 hash
     * @param string $ext Extension
     *
     * @return void
     */
    public function __construct($md5, $ext = 'php')
    {
        $this->cachedir = WPSOL_MINIFICATION_CACHE;
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            $this->cachedir = WPSOL_MINIFICATION_CACHE . $blog_id . '/';
        }
        $this->delayed = WPSOL_CACHE_DELAY;
        $this->nogzip = WPSOL_CACHE_NOGZIP;
        if ($this->nogzip === false) {
            $this->filename = WPSOL_CACHEFILE_PREFIX . $md5 . '.php';
        } else {
            if (in_array($ext, array('js', 'css'))) {
                $this->filename = $ext . '/' . WPSOL_CACHEFILE_PREFIX . $md5 . '.' . $ext;
            } else {
                $this->filename = '/' . WPSOL_CACHEFILE_PREFIX . $md5 . '.' . $ext;
            }
        }
    }

    /**
     * Check cache directory exist
     *
     * @return boolean
     */
    public function check()
    {
        if (!file_exists($this->cachedir . $this->filename)) {
            // No cached file, sorry
            return false;
        }
        // Cache exists!
        return true;
    }

    /**
     * Get content of cache exist
     *
     * @return boolean|string
     */
    public function retrieve()
    {
        if ($this->check()) {
            if ($this->nogzip === false) {
                return file_get_contents($this->cachedir . $this->filename . '.none');
            } else {
                return file_get_contents($this->cachedir . $this->filename);
            }
        }
        return false;
    }

    /**
     * Create minify cache files
     *
     * @param string $code Content to cache
     * @param string $mime Mine cache
     *
     * @return void
     */
    public function cache($code, $mime)
    {
        if ($this->nogzip === false) {
            $file = ($this->delayed ? 'delayed.php' : 'default.php');
            $phpcode = file_get_contents(WPSOL_PLUGIN_DIR . '/inc/minifications/config/' . $file);
            $phpcode = str_replace(array('%%CONTENT%%', 'exit;'), array($mime, ''), $phpcode);
            file_put_contents($this->cachedir . $this->filename, $phpcode, LOCK_EX);
            file_put_contents($this->cachedir . $this->filename . '.none', $code, LOCK_EX);
            if (!$this->delayed) {
                // Compress now!
                file_put_contents(
                    $this->cachedir . $this->filename . '.deflate',
                    gzencode($code, 9, FORCE_DEFLATE),
                    LOCK_EX
                );
                file_put_contents($this->cachedir . $this->filename . '.gzip', gzencode($code, 9, FORCE_GZIP), LOCK_EX);
            }
        } else {
            // Write code to cache without doing anything else
            file_put_contents($this->cachedir . $this->filename, $code, LOCK_EX);
        }
    }

    /**
     * Get minify ,cache file name
     *
     * @return string
     */
    public function getname()
    {
        return $this->filename;
    }

    /**
     * Create folder cache
     *
     * @return boolean
     */
    public static function createCacheMinificationFolder()
    {
        if (!defined('WPSOL_MINIFICATION_CACHE')) {
            // We didn't set a cache
            return false;
        }
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            foreach (array('', 'js', 'css') as $checkDir) {
                if (!self::checkCacheDir(WPSOL_MINIFICATION_CACHE . $blog_id . '/' . $checkDir)) {
                    return false;
                }
            }

            /**
             * Write index.html here to avoid prying eyes
             */
            $indexFile = WPSOL_MINIFICATION_CACHE . $blog_id . '/index.html';
            if (!is_file($indexFile)) {
                file_put_contents(
                    $indexFile,
                    '<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>'
                );
            }

            /**
             * Write .htaccess here to overrule wp_super_cache
             */
            $htAccess = WPSOL_MINIFICATION_CACHE . $blog_id . '/.htaccess';
        } else {
            foreach (array('', 'js', 'css') as $checkDir) {
                if (!self::checkCacheDir(WPSOL_MINIFICATION_CACHE . $checkDir)) {
                    return false;
                }
            }
            /**
             * Write index.html here to avoid prying eyes
             */
            $indexFile = WPSOL_MINIFICATION_CACHE . '/index.html';
            if (!is_file($indexFile)) {
                file_put_contents(
                    $indexFile,
                    '<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>'
                );
            }

            /**
             * Write .htaccess here to overrule wp_super_cache
             */
            $htAccess = WPSOL_MINIFICATION_CACHE . '/.htaccess';
        }

        if (!is_file($htAccess)) {
            /**
             * Create wp-content/AO_htaccess_tmpl with
             * whatever htaccess rules you might need
             * if you want to override default AO htaccess
             */
            $htaccess_tmpl = WP_CONTENT_DIR . '/AO_htaccess_tmpl';
            if (is_file($htaccess_tmpl)) {
                $htAccessContent = file_get_contents($htaccess_tmpl);
            } elseif (is_multisite()) {
                $htAccessContent = '<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
        <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all granted
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order allow,deny
        Allow from all
    </Files>
</IfModule>';
            } else {
                $htAccessContent = '<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
    <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all denied
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order deny,allow
        Deny from all
    </Files>
</IfModule>';
            }

            file_put_contents($htAccess, $htAccessContent);
        }
        // All OK
        return true;
    }

    /**
     * Check dir cache
     *
     * @param string $dir Directory of cache
     *
     * @return boolean
     */
    public static function checkCacheDir($dir)
    {
        // Check and create if not exists
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
            if (!file_exists($dir)) {
                return false;
            }
        }

        // check if we can now write
        if (!is_writable($dir)) {
            return false;
        }

        // and write index.html here to avoid prying eyes
        $indexFile = $dir . '/index.html';
        if (!is_file($indexFile)) {
            file_put_contents(
                $indexFile,
                '<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>'
            );
        }

        return true;
    }

    /**
     * Clean minification file
     *
     * @return boolean
     */
    public static function clearMinification()
    {
        if (!self::createCacheMinificationFolder()) {
            return false;
        }
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            // scan the cachedirs
            foreach (array('', 'js', 'css') as $scandirName) {
                $scan[$scandirName] = scandir(WPSOL_MINIFICATION_CACHE . $blog_id . '/' . $scandirName);
            }
            // clear the cachedirs
            foreach ($scan as $scandirName => $scanneddir) {
                $thisAoCacheDir = rtrim(WPSOL_MINIFICATION_CACHE . $blog_id . '/' . $scandirName, '/') . '/';
                foreach ($scanneddir as $file) {
                    if (!in_array($file, array('.', '..')) &&
                        strpos($file, WPSOL_CACHEFILE_PREFIX) !== false &&
                        is_file($thisAoCacheDir . $file)) {
                        unlink($thisAoCacheDir . $file);
                    }
                }
            }
            unlink(WPSOL_MINIFICATION_CACHE . $blog_id . '/.htaccess');
        } else {
            // scan the cachedirs
            foreach (array('', 'js', 'css') as $scandirName) {
                $scan[$scandirName] = scandir(WPSOL_MINIFICATION_CACHE . $scandirName);
            }
            // clear the cachedirs
            foreach ($scan as $scandirName => $scanneddir) {
                $thisAoCacheDir = rtrim(WPSOL_MINIFICATION_CACHE . $scandirName, '/') . '/';
                foreach ($scanneddir as $file) {
                    if (!in_array($file, array('.', '..')) &&
                        strpos($file, WPSOL_CACHEFILE_PREFIX) !== false &&
                        is_file($thisAoCacheDir . $file)) {
                        unlink($thisAoCacheDir . $file);
                    }
                }
            }

            unlink(WPSOL_MINIFICATION_CACHE . '/.htaccess');
        }
        return true;
    }

    /**
     * Init class WpsolMinificationCache
     *
     * @return WpsolMinificationCache
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
