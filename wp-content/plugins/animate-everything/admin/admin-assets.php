<?php

function galoover_ae_load_website_script()
{
    wp_register_style(
        'library-animated-style',
        GALOOVER_AE_PLUGIN_URL . '/assets/css/animate.min.css',
        array(),
        '',
        'all'
    );
    wp_register_style(
        'library-loading-style',
        GALOOVER_AE_PLUGIN_URL . '/assets/css/loading.min.css',
        array(),
        '',
        'all'
    );
    wp_enqueue_style( 'library-animated-style' );
    wp_enqueue_style( 'library-loading-style' );
}

add_action( 'wp_enqueue_scripts', 'galoover_ae_load_website_script' );
add_action( 'admin_enqueue_scripts', 'galoover_ae_load_admin_script' );
function galoover_ae_load_admin_script()
{
    $uri = $_SERVER[REQUEST_URI];
    $search = strpos( $uri, 'admin.php?page=animated-anything' );
    
    if ( $search !== false ) {
        wp_enqueue_media();
        wp_enqueue_style( 'farbtastic' );
        wp_enqueue_script( 'farbtastic' );
        wp_enqueue_script(
            'iframe',
            GALOOVER_AE_PLUGIN_URL . '/assets/js/iframe.min.js',
            array( 'jquery' ),
            '1.0',
            true
        );
        wp_enqueue_script(
            'function',
            GALOOVER_AE_PLUGIN_URL . '/assets/js/main.min.js',
            array( 'jquery', 'farbtastic' ),
            '1.0',
            true
        );
        wp_enqueue_script(
            'tether',
            GALOOVER_AE_PLUGIN_URL . '/assets/js/tether.min.js',
            array( 'jquery' ),
            '1.2.4',
            true
        );
        wp_enqueue_script(
            'boostrap',
            GALOOVER_AE_PLUGIN_URL . '/assets/js/bootstrap.min.js',
            array( 'jquery' ),
            '4.0.0',
            true
        );
        wp_enqueue_script(
            'boostrap-slider',
            GALOOVER_AE_PLUGIN_URL . '/assets/js/bootstrap-slider.min.js',
            array( 'jquery' ),
            '10.0.0',
            true
        );
        wp_register_style(
            'library-bootstrap-style',
            GALOOVER_AE_PLUGIN_URL . '/assets/css/bootstrap.min.css',
            array(),
            '',
            'all'
        );
        wp_enqueue_style( 'library-bootstrap-style' );
        wp_register_style(
            'library-bootstrap-slider-style',
            GALOOVER_AE_PLUGIN_URL . '/assets/css/bootstrap-slider.min.css',
            array(),
            '',
            'all'
        );
        wp_enqueue_style( 'library-bootstrap-slider-style' );
        wp_register_style(
            'library-admin-style-style',
            GALOOVER_AE_PLUGIN_URL . '/assets/css/admin.style.min.css',
            array(),
            '',
            'all'
        );
        wp_enqueue_style( 'library-admin-style-style' );
    }

}
