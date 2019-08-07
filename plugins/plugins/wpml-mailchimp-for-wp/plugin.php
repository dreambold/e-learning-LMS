<?php
/**
 * MailChimp for WordPress Multilingual
 *
 * @package     wpml
 * @author      OnTheGoSystems
 * @copyright   2017 OTGS
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: MailChimp for WordPress Multilingual
 * Plugin URI:  https://wpml.org
 * Description: This 'glue' plugin makes it easier to translate with WPML content provided in MailChimp for WordPress plugin.
 * Version:     0.0.3
 * Author:      OnTheGoSystems
 * Author URI:  https://wpml.org
 * Text Domain: wpml-mailchimp-for-wp
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'WPML_MAILCHIMP_FOR_WP_PATH', dirname( __FILE__ ) );

$autoloader_dir = WPML_MAILCHIMP_FOR_WP_PATH . '/vendor';
if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	$autoloader = $autoloader_dir . '/autoload.php';
} else {
	$autoloader = $autoloader_dir . '/autoload_52.php';
}
require_once $autoloader;

/**
 * Init function.
 */
function wpml_mailchimp_for_wp() {
	$wpml_mailchimp = new WPML_Compatibility_MailChimp();
	$wpml_mailchimp->add_hooks();
}

add_action( 'wpml_loaded', 'wpml_mailchimp_for_wp' );