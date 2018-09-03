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
 * Class WpsolRestApi
 */
class WpsolRestApi
{
    /**
     * WpsolRestApi constructor.
     */
    public function __construct()
    {
        $configuration = get_option('wpsol_optimization_settings');
        if (!empty($configuration) && !empty($configuration['speed_optimization']['remove_rest_api'])) {
            add_action('after_setup_theme', array( $this, 'removeApiLinks'));
        }
    }

    /**
     *  This functions should help removing api links in the header ;
     *
     * @return void
     */
    public function removeApiLinks()
    {
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11);
    }
}
