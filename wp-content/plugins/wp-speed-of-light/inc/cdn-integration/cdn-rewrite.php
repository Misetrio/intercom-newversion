<?php
/*
 * @copyright 2014  Joomunited  ( email : contact _at_ joomunited.com )
 *
 *  Original development of this plugin was kindly funded by Joomunited
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WpsolCDNRewrite
 */
class WpsolCDNRewrite
{
    /**
     * Init blog url params
     *
     * @var null
     */
    private $blog_url = null;
    /**
     * Init cdn url params
     *
     * @var null
     */
    private $cdn_url = null;
    /**
     * Init dir check params
     *
     * @var array
     */
    private $dirs = array();
    /**
     * Init exclude dir params
     *
     * @var array
     */
    private $excludes = array();
    /**
     * Init relative params
     *
     * @var boolean
     */
    private $relative = false;


    /**
     * Get CDN parameter from option
     * WpsolCDNRewrite constructor.
     *
     * @param array $option Option of Cdn
     *
     * @return void
     */
    public function __construct(&$option)
    {
        $this->blog_url = get_option('home');
        $this->cdn_url = $option['cdn_url'];
        $this->dirs = $option['cdn_content'];
        $this->excludes = $option['cdn_exclude_content'];
        $this->relative = $option['cdn_relative_path'];
    }

    /**
     * Replace cdn on html raw
     *
     * @param string $content HTML raw
     *
     * @return mixed
     */
    public function rewrite($content)
    {

        $blog_url = quotemeta($this->blog_url);

        // get dir scope in regex format
        $dirs = $this->getDirScope();

        // regex rule start
        $regex_rule = '#(?<=[(\"\'])';

        // check if relative paths
        if ($this->relative) {
            $regex_rule .= '(?:' . $blog_url . ')?';
        } else {
            $regex_rule .= $blog_url;
        }

        // regex rule end
        $regex_rule .= '/(?:((?:' . $dirs . ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

        // call the cdn rewriter callback
        $new_content = preg_replace_callback($regex_rule, array(&$this, 'replaceCdnUrl'), $content);

        /**
         * Filter new HTML content after it has been replaced by CDN
         *
         * @param string  New html
         *
         * @return string
         */
        $new_content = apply_filters('wpsol_html_after_cdn', $new_content);

        return $new_content;
    }

    /**
     * Get directory scope
     *
     * @return string
     */
    protected function getDirScope()
    {
        // default
        if (empty($this->dirs) || count($this->dirs) < 1) {
            return 'wp\-content|wp\-includes';
        }

        return implode('|', array_map('quotemeta', array_map('trim', $this->dirs)));
    }

    /**
     * Replace cdn url to root url
     *
     * @param array $match Match with cdn url
     *
     * @return mixed
     */
    protected function replaceCdnUrl($match)
    {
        //return file type or directories excluded
        if ($this->excludesCheck($match[0])) {
            return $match[0];
        }

        $parseUrl = parse_url($this->blog_url);
        $scheme = 'http://';
        if (isset($parseUrl['scheme'])) {
            $scheme = $parseUrl['scheme'] . '://';
        }
        $host = $parseUrl['host'];
        //get domain
        $domain = $scheme . $host;

        // check if not a relative path
        if (!$this->relative || strstr($match[0], $this->blog_url)) {
            return str_replace($domain, $this->cdn_url, $match[0]);
        }

        // Relative path
        $pattern = '@(\/\/)?' . $host . '@';
        return preg_replace($pattern, $this->cdn_url, $match[0]);
    }

    /**
     * Check excludes assets
     *
     * @param string $dir Directory to exclude
     *
     * @return boolean
     */
    protected function excludesCheck($dir)
    {
        if (!empty($this->excludes)) {
            foreach ($this->excludes as $exclude) {
                if (stristr($dir, $exclude) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}
