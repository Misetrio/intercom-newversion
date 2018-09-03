<?php
/**
 * Created by PhpStorm.
 * User: aabweber
 * Date: 12/02/14
 * Time: 10:30
 */


register_activation_hook(__FILE__, 'speed_test_activate');
register_deactivation_hook(__FILE__, 'speed_test_deactivate');
register_uninstall_hook(__FILE__, 'speed_test_uninstall');


/**
 * On plugin activation
 */
function speed_test_activate() {
    $role = get_role('administrator');
    $role->add_cap("manage_options");
//    add_option(EMO_OPTION_PREFIX.'site_id', '');
}

/**
 * On plugin deactivation
 */
function speed_test_deactivate() {
//    update_option(EMO_OPTION_PREFIX.'site_id', '');
}

/**
 * On plugin uninstalling
 */
function speed_test_uninstall() {
    global $wpdb;
}