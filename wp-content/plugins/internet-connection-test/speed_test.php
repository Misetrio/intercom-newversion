<?php
/*
Plugin Name: Internet Connection Test
Plugin URI: http://www.aabweber.com
Description: Allows your users to test internet connection speed
Author: Andrew Buldakovsky
Version: 0.3
Author URI: http://www.aabweber.com
License: GPLv3
*/

include_once(__DIR__.'/speed_test-setup.php');



/**
 * Add CSS & JS files in HEAD section
 */
add_action('wp_enqueue_scripts', 'speed_test_add_custom_head_fields');

function speed_test_add_custom_head_fields(){
    wp_enqueue_style('internet-connection-test-style',  plugins_url( 'speed.css' , __FILE__ ));
    wp_enqueue_script('internet-connection-test-script-config',  plugins_url( 'config.js' , __FILE__ ));
    wp_enqueue_script('internet-connection-test-script',  plugins_url( 'speed.js' , __FILE__ ), Array(
        'internet-connection-test-script-config', 'jquery'
    ));
}

function getSpeedTestHTML($lbl_calculating = SpeedTestWidget::LBL_CALCULATING, $lbl_your_speed = SpeedTestWidget::LBL_YOUR_SPEED, $lbl_start = SpeedTestWidget::LBL_START_TEST){
    ob_start();
    if(is_file(get_template_directory().'/spt-widget.php')){
        include_once get_template_directory().'/spt-widget.php';
    }else{
        include_once __DIR__.'/spt-widget.php';
    }
    return ob_get_clean();
}

function showSpeedTestHTML($lbl_calculating = SpeedTestWidget::LBL_CALCULATING, $lbl_your_speed = SpeedTestWidget::LBL_YOUR_SPEED, $lbl_start = SpeedTestWidget::LBL_START_TEST){
    echo getSpeedTestHTML($lbl_calculating,  $lbl_your_speed, $lbl_start);
}

class SpeedTestWidget extends WP_Widget {
    const LBL_CALCULATING   = 'calculating...';
    const LBL_YOUR_SPEED    = 'Your speed:';
    const LBL_START_TEST    = 'Test your internet speed';

    function SpeedTestWidget() {
        parent::__construct( false, 'Internet Speed Test', Array('description' => 'This widget allows your users to test internet connection speed'), Array('width' => 200, 'height'=>30) );
    }

    function widget( $args, $instance ) {
        showSpeedTestHTML(
            $this->getValueWithDefault($instance, 'calculating', self::LBL_CALCULATING),
            $this->getValueWithDefault($instance, 'your_speed', self::LBL_YOUR_SPEED),
            $this->getValueWithDefault($instance, 'start', self::LBL_START_TEST)
        );
    }

    function getValueWithDefault($inst, $value, $default){
        return isset($inst[$value]) && $inst[$value] ? $inst[$value] : $default;
    }

    function update( $new_instance, $old_instance ){
        $instance = [];
        $instance['calculating'] = $this->getValueWithDefault($new_instance, 'calculating', self::LBL_CALCULATING);
        $instance['your_speed'] = $this->getValueWithDefault($new_instance, 'your_speed', self::LBL_YOUR_SPEED);
        $instance['start'] = $this->getValueWithDefault($new_instance, 'start', self::LBL_START_TEST);
        return $instance;
    }

    private function echoField($name, $value){
        echo '<input type="text" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'" value="'.htmlspecialchars($value).'"/><br />';
    }

    function form( $instance ){
        $instance['calculating'] = $this->getValueWithDefault($instance, 'calculating', self::LBL_CALCULATING);
        $instance['your_speed'] = $this->getValueWithDefault($instance, 'your_speed', self::LBL_YOUR_SPEED);
        $instance['start'] = $this->getValueWithDefault($instance, 'start', self::LBL_START_TEST);

        echo '"Calculating" label: ';
        $this->echoField('calculating', $instance['calculating']);
        echo '"Start test" label: ';
        $this->echoField('start', $instance['start']);
        echo '"Your speed" label: ';
        $this->echoField('your_speed', $instance['your_speed']);
    }
}

function speed_test_register_widgets() {
    register_widget( 'SpeedTestWidget' );
}

add_action( 'widgets_init', 'speed_test_register_widgets' );
