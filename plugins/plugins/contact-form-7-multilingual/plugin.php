<?php
/**
 * Plugin Name: Contact Form 7 Multilingual
 * Plugin URI:
 * Description: Make forms from Contact Form 7 translatable with WPML | <a href="https://wpml.org/documentation/plugins-compatibility/using-contact-form-7-with-wpml/">Documentation</a>
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 1.0.0
 * Plugin Slug: contact-form-7-multilingual
 *
 * @package wpml/cf7
 */

if ( defined( 'CF7ML_VERSION' ) ) {
	return;
}

define( 'CF7ML_VERSION', '1.0.0' );
define( 'CF7ML_PLUGIN_PATH', dirname( __FILE__ ) );

$autoloader_dir = CF7ML_PLUGIN_PATH . '/vendor';
if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	$autoloader = $autoloader_dir . '/autoload.php';
} else {
	$autoloader = $autoloader_dir . '/autoload_52.php';
}
require_once $autoloader;

function cf7ml_init() {
	$cf7ml = new Contact_Form_7_Multilingual();
	$cf7ml->init_hooks();
}

add_action( 'wpml_tm_loaded', 'cf7ml_init' );
