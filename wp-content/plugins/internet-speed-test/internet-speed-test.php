<?php
/*
Plugin Name: Internet Speed Test
Description: The plugin allows you to embed speed test for your website via a shortcode.
Version:     1.0.0
Author:      Meter.net
Author URI:  https://www.meter.net
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add Shortcode
function internet_speed_test_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'layout' => 'dark', // dark, light
			'language' => 'auto', // auto, wordpress, $supported_languages
			'default-language' => 'auto', // auto, $supported_languages
		),
		$atts,
		'internet-speed-test'
	);

	$parameters = array();
	$iframe_url = '//www.metercustom.net/test/';
	$supported_languages = array('cs', 'de', 'en', 'es', 'fr', 'hu', 'it', 'ja', 'pl', 'pt', 'ru', 'sk', 'en_gb' );

	// Check if light theme
	if ( $atts['layout'] == 'light' ) {
		$parameters[] = 'th=w';
	}

	// Check for the langauge
	if ( in_array( $atts['language'], $supported_languages) ) {
		
		$parameters[] = 'hl='.$atts['language'];

	} elseif ( $atts['language'] == 'wordpress' ) {
		
		$locale = strtolower( get_locale() );
		$sublocale = substr( $locale, 0, 2);
		
		if ( $locale == 'en_gb' ) {
			$parameters[] = 'hl='.$locale;
		} elseif ( in_array( $sublocale, $supported_languages ) ) {
			$parameters[] = 'hl='.$sublocale;
		} else {
			if ( in_array( $atts['default-language'], $supported_languages ) ) {
				$parameters[] = 'hl='.$atts['default-language'];
			} 
		}
		
	}

	// Add paramters to the url
	if ( count( $parameters) > 0 ) {
		$iframe_url .= '?'.implode( '&', $parameters );
	}

	// Return the iframe
	return '<div style="min-height:360px;"><div style="width:100%;height:0;padding-bottom:50%;position:relative;"><iframe style="border:none;position:absolute;top:0;left:0;width:100%;height:100%;min-height:360px;border:none;overflow:hidden !important;" src="'. $iframe_url .'"></iframe></div></div>';

}
add_shortcode( 'internet-speed-test', 'internet_speed_test_shortcode' );