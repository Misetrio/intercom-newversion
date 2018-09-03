<?php

/*
Plugin Name: Animate anything
Plugin URI:
Description: Add cool CSS3 animations to your website. Works with every WP Theme & pagebuilder, and No Coding needed.
Author: Galoover
Version: 1.3.2
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'galoover_ae_fs' ) ) {
    function galoover_ae_fs()
    {
        global  $galoover_ae_fs ;
        
        if ( !isset( $galoover_ae_fs ) ) {
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $galoover_ae_fs = fs_dynamic_init( array(
                'id'             => '1942',
                'slug'           => 'animated-anything',
                'type'           => 'plugin',
                'public_key'     => 'pk_5a05e93751f7976a7cd785a36a13f',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'       => 'animated-anything',
                'first-path' => 'admin.php?page=animated-anything',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $galoover_ae_fs;
    }
    
    galoover_ae_fs();
    do_action( 'galoover_ae_fs_loaded' );
    define( 'GALOOVER_AE_PLUGIN_URL', WP_PLUGIN_URL . "/" . dirname( plugin_basename( __FILE__ ) ) );
    include 'admin/admin-function.php';
    include 'admin/admin-assets.php';
    include 'admin/admin-view.php';
    include 'function.php';
}
