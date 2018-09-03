<?php

/**
 * Plugin Name:       Gallery of Animated Posts
 * Description:       Display a gallery of animated posts unsing a shortcode for theme Gridsby.
 * Version:           1.0.0
 * Author:            Marcus Hogh
 * Author URI:        http://lenscapades.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gallery-of-animated-posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gallery-of-animated-posts.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_gallery_of_animated_posts() {

	$plugin = new Gallery_Of_Animated_Posts();
	$plugin->run();

}
run_gallery_of_animated_posts();
