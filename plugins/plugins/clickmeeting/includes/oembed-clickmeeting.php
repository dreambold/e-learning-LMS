<?php

/**
 * The plugin clickmeeting file
 *
 *
 * @link              clickmeeting.com
 * @since             1.0.0
 * @package           Clickmeeting
 *
 * @clickmeting-plugin
 * Plugin Name:       Clickmeeting
 * Plugin URI:        clickmeeting.com
 * Description:       ClickMeeting  is  a  platform  that  allows for  webinars, online  meetings, presentations,  lectures  and collaborations.
 * Version:           1.0.0
 * Author:            Clickmeeting
 * Author URI:        clickmeeting.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function clickmeeting_shortcode($atts, $content = null)
{

    if ( false === stripos( $content, 'clickmeeting.com') ) {
        return $content;
    }

    // Set default arguments
    $defaults = array(
        'width'  => 1024,
        'height' => 768,
        'popup'  => 'off',
        'lang'   => 'en',
    );

    extract(shortcode_atts($defaults, $atts));

    $chr = (false === strpos($content, '?'))? '?':'&';
    return '<div class="clickmeeting-iframe"><iframe src="' . esc_attr($content) . $chr .'lang='. esc_attr($lang) .'&popup='. esc_attr($popup) .'" width="'. esc_attr($width) .'" height="'. esc_attr($height). '" scrolling="no" frameborder="0" allowfullscreen style="display: block;border: none;"></iframe></div>';

}

function register_shortcodes(){
    add_shortcode('clickmeeting', 'clickmeeting_shortcode');
}

add_action( 'init', 'register_shortcodes');



