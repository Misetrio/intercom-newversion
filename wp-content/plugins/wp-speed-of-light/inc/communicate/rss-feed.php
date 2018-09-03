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
 * Class WpsolRssFeed
 */
class WpsolRssFeed
{

    /**
     * WpsolRssFeed constructor.
     */
    public function __construct()
    {
        $configuration = get_option('wpsol_optimization_settings');

        if (!empty($configuration) && !empty($configuration['speed_optimization']['remove_rss_feed'])) {
            add_action('wp_loaded', array( $this, 'removeFeedLinks'));
            add_action('after_setup_theme', array( $this, 'removeRssFeedLinks'));
        }
    }

    /**
     * Remove rss feed link out content
     *
     * @return void
     */
    public function removeRssFeedLinks()
    {
        add_action('do_feed', array($this, 'disableFeed'), 1);
        add_action('do_feed_rdf', array($this, 'disableFeed'), 1);
        add_action('do_feed_rss', array($this, 'disableFeed'), 1);
        add_action('do_feed_rss2', array($this, 'disableFeed'), 1);
        add_action('do_feed_atom', array($this, 'disableFeed'), 1);
        add_action('do_feed_rss2_comments', array($this, 'disableFeed'), 1);
        add_action('do_feed_atom_comments', array($this, 'disableFeed'), 1);
    }


    /**
     * Remove action
     *
     * @return void
     */
    public function removeFeedLinks()
    {
        //Remove feed link from header
        remove_action('wp_head', 'feed_links_extra', 3); //Extra feeds such as category feeds
        remove_action('wp_head', 'feed_links', 2); // General feeds: Post and Comment Feed
    }


    /**
     * Disable feed link out content
     *
     * @return void
     */
    public function disableFeed()
    {
        wp_die(
            esc_html(printf(
                __('No feed available, please visit the %s', 'wp-speed-of-light'),
                '<a href="'. esc_url(home_url('/')) .'">homepage</a> !'
            ))
        );
    }
}
