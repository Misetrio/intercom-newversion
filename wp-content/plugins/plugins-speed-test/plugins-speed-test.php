<?php
/**
 *
 * @link              http://www.wpspeedster.com
 * @since             1.0
 * @package           Plugins_Speed_Test
 *
 * @wordpress-plugin
 * Plugin Name:       Plugins Speed Test
 * Plugin URI:        http://blog.wpspeedster.com/plugins-speed-test-plugin/
 * Description:       This plugin shows resource impact of specific plugins on your blog.
 * Version:           1.1
 * Author:            Csaba Kissi
 * Author URI:        http://www.wpspeedster.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugins-speed-test
 * Domain Path:       /languages
 */

register_activation_hook(__FILE__, 'activate_plugins_speed_test');
register_deactivation_hook(__FILE__, 'deactivate_plugins_speed_test');

function activate_plugins_speed_test()
{
}

function deactivate_plugins_speed_test()
{
}

add_action( 'admin_enqueue_scripts', 'wp_pst_plugin_scripts_init' );
add_filter( 'manage_plugins_columns', 'wp_pst_add_plugins_column' );
add_action( 'manage_plugins_custom_column' , 'wp_pst_render_plugins_column', 10, 3 );

function wp_pst_plugin_scripts_init($hook) {
    if($hook != 'plugins.php') return;
    wp_register_script('wp_pst_plugin-script', plugins_url( 'assets/js/script.js', __FILE__ ),array('jquery'));
    wp_enqueue_script('wp_pst_plugin-script');
    wp_enqueue_style( 'wp_pst_plugin-style', plugins_url( 'assets/css/style.css', __FILE__ ));
}

function wp_pst_add_plugins_column( $columns ) {
    $columns['wp_pst_column'] = 'Speed Impact';
    return $columns;
}


function wp_pst_render_plugins_column( $column_name, $plugin_file, $plugin_data ) {
    if ( 'wp_pst_column' == $column_name ) :
        ?>
        <div class="plugin-description">
            <table class="wp_pst">
                <tr><td title="Speed Impact on Blogs' Home Page">Home Page Impact:</td><td  style="margin:0"><span id="hp_<?php echo $plugin_data['slug']?>">-</span></td></tr>
                <tr><td title="Speed Impact on Blogs' Post Page">Post Page Impact:</td><td><span id="pp_<?php echo $plugin_data['slug']?>">-</span></td></tr>
                <tr><td title="Size of the resources added (in kB)">Resources added:</td><td><span id="rs_<?php echo $plugin_data['slug']?>">-</span></td></tr>
                <tr><td title="Number of Database tables created">DB tables:</td><td><span id="db_<?php echo $plugin_data['slug']?>">-</span></td></tr>
            </table>
            <div class="row-actions visible"><span class="activate"><a href="http://www.wpspeedster.com/plugin/speedtest/<?php echo $plugin_data['slug'] ?>" title="More Speed Test Details" class="edit">More Details</a></span></div>
        </div>
    <?php
    endif;
}