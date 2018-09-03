<?php
/*
Plugin Name: SPEEDRON Speed Test
Plugin URI: http://www.speedron.com/support/wordpress-speed-test-plugin-widget
Description: Test your Internet Connection Speed:  Download and Upload
Version: 1.1.0
Author: SPEEDRON.com
Author URI: http://www.speedron.com
License: GPL2
*/

/* Copyright 2014 SPEEDRON.com (email : http://www.speedron.com/contact/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; 
*/

// Start class speedron_speedtest_widget //

class speedron_speedtest_widget extends WP_Widget {

    // Constructor //

    function speedron_speedtest_widget() {
        
        $widget_ops = array( 'classname' => 'speedron_speedtest_widget', 'description' => 'Test your Internet Connection Speed:  Download and Upload' );
        $control_ops = array( 'id_base' => 'speedron_speedtest_widget' );
        $this->WP_Widget( 'speedron_speedtest_widget', 'SPEEDRON Speed Test', $widget_ops, $control_ops );
    }

    // Extract Args //

    function widget($args, $instance) {

        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $width = $instance['width'];


        // Before widget //

        echo $before_widget;

        // Title of widget //

        if ( $title ) { echo $before_title . $title . $after_title; }

        // Widget output //

        echo '<br><iframe src="http://www.speedron.com/widget/'.$width.'" scrolling="no" frameborder="0" width="'.$width.'" height="'.$width.'"></iframe>';
        echo '<center><a href="http://www.speedron.com/" title="Internet Connection Speed Test">Speed Test</a> widget by SPEEDRON.com</center>';

        // After widget //

        echo $after_widget;

    }

    // Update Settings //

    function update($new_instance, $old_instance) {

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['width'] = strip_tags($new_instance['width']);

        return $instance;

    }

    // Widget Control Panel //

    function form($instance) {

        $defaults = array( 'title' => 'Test Internet Connection', 'width' => 160);
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <br>
        <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>'" value="<?php echo $instance['title']; ?>" />

        <label for="<?php echo $this->get_field_id('width'); ?>">Widget width in px:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" type="text" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />

        <br><br>
        
        <?php 

    }

}

// End class soup_widget
add_action('widgets_init', create_function('', 'return register_widget("speedron_speedtest_widget");'));

?>