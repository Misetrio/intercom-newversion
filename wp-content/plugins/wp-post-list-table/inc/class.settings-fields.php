<?php

/**
 * Post list table setting fields
 */


if ( !class_exists('wpb_plt_setting_fields' ) ):
class wpb_plt_setting_fields {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WPB_PLT_WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( __( 'Post list table settings', WPB_PLT_TEXTDOMAIN ), __( 'Post list settings', WPB_PLT_TEXTDOMAIN ), 'delete_posts', 'wpb_post_list_table', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'wpb_plt_general',
                'title' => __( 'General Settings', WPB_PLT_TEXTDOMAIN )
            )
        );
        return $sections;
    }

    /**
     * Table content checkbox content
     */

    function wpb_plt_table_content(){
        $contents = array(
            'no'        => __( 'Row Number', WPB_PLT_TEXTDOMAIN ),
            'id'        => __( 'Post ID', WPB_PLT_TEXTDOMAIN ),
            'title'     => __( 'Post Title', WPB_PLT_TEXTDOMAIN ),
            'author'    => __( 'Post Author', WPB_PLT_TEXTDOMAIN ),
            'date'      => __( 'Post Date', WPB_PLT_TEXTDOMAIN ),
            'category'  => __( 'Post Category', WPB_PLT_TEXTDOMAIN ),
            'tag'       => __( 'Post Tags', WPB_PLT_TEXTDOMAIN ),
            'comment'   => __( 'Post Comment', WPB_PLT_TEXTDOMAIN ),
            'edit_link' => __( 'Post Edit Link', WPB_PLT_TEXTDOMAIN ),
        );

        /* WooCommerce Content */

        if ( class_exists( 'WooCommerce' ) ) { 
            $contents_woo = array( 
                'price'     => __( 'WooCommerce Price', WPB_PLT_TEXTDOMAIN ),
                'sku'       => __( 'WooCommerce SKU', WPB_PLT_TEXTDOMAIN ),
                'stock'     => __( 'WooCommerce Stock', WPB_PLT_TEXTDOMAIN ),
                'review'    => __( 'WooCommerce Review', WPB_PLT_TEXTDOMAIN ),
                'cart'      => __( 'WooCommerce Add to cart', WPB_PLT_TEXTDOMAIN ),
            );
            $contents = array_merge( $contents, $contents_woo );
        }

        /* Woo LightBox */

        if ( class_exists( 'WooCommerce' ) && function_exists( 'get_wpb_woocommerce_lightbox' ) ) { 
            $contents_woo_light_box = array( 
                'wpb_woo_lightbox'     => __( 'WPB WooCommerce LightBox', WPB_PLT_TEXTDOMAIN ),
            );
            $contents = array_merge( $contents, $contents_woo_light_box );
        }

        /* YIT Quickview */

        if( class_exists( 'YITH_WCQV_Frontend' ) ){
            $contents_yith_quickview = array( 
                'yith_quickview'     => __( 'YITH WooCommerce QuickView', WPB_PLT_TEXTDOMAIN ),
            );
            $contents = array_merge( $contents, $contents_yith_quickview );
        }

        /* YIT Wish List */

        if ( class_exists('YITH_WCWL') ) {
            $contents_yith_wishlist = array( 
                'yith_wishlist'     => __( 'YITH WooCommerce Wish List', WPB_PLT_TEXTDOMAIN ),
            );
            $contents = array_merge( $contents, $contents_yith_wishlist );
        }

        /* YIT Compare */

        if ( class_exists('YITH_Woocompare') ) {
            $contents_yith_compare = array( 
                'yith_compare'     => __( 'YITH WooCommerce Compare', WPB_PLT_TEXTDOMAIN ),
            );
            $contents = array_merge( $contents, $contents_yith_compare );
        }


        return $contents;
    }


    /**
     * Get all custom post types for select option
     */
    
    function wpb_plt_post_type_select(){

        $args = array(
            'public'   => true,
            '_builtin' => false
        );

        $rerutn_object = get_post_types( $args );
        $rerutn_object['post'] = 'Post';

        return $rerutn_object;
    }
    

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'wpb_plt_general' => array(
                array(
                    'name'      => 'wpb_plt_post_type_select',
                    'label'     => __( 'Select Post Type', WPB_PLT_TEXTDOMAIN ),
                    'desc'      => __( 'You can select your own custom post type. Default: post.', WPB_PLT_TEXTDOMAIN ),
                    'type'      => 'select',
                    'default'   => 'post',
                    'options'   => $this->wpb_plt_post_type_select(),
                ),
                array(
                    'name'    => 'table_content',
                    'label'   => __( 'Table content', WPB_PLT_TEXTDOMAIN ),
                    'desc'    => __( 'Select table content.', WPB_PLT_TEXTDOMAIN ),
                    'type'    => 'multicheck',
                    'default' => array('no' => 'no', 'title' => 'title', 'author' => 'author', 'date' => 'date', 'category' => 'category', 'comment' => 'comment', 'edit_link' => 'edit_link'),
                    'options' => $this->wpb_plt_table_content(),
                ),
                array(
                    'name'    => 'table_style',
                    'label'   => __( 'Table Style', WPB_PLT_TEXTDOMAIN ),
                    'desc'    => __( 'Select a table style', WPB_PLT_TEXTDOMAIN ),
                    'type'    => 'select',
                    'default' => 'bordered',
                    'options' => array(
                        'default'   => __( 'Default', WPB_PLT_TEXTDOMAIN ),
                        'striped'   => __( 'Striped', WPB_PLT_TEXTDOMAIN ),
                        'bordered'  => __( 'Bordered', WPB_PLT_TEXTDOMAIN ),
                        'hover'     => __( 'Hover', WPB_PLT_TEXTDOMAIN ),
                    )
                )
            )
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

new wpb_plt_setting_fields();