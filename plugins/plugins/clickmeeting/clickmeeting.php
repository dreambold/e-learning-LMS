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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include plugin files
 */
include_once ( plugin_dir_path( __FILE__ ) . 'includes/oembed-clickmeeting.php' );

