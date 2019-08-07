<?php
/**
 * Plugin Name: Yoast SEO Multilingual
 * Plugin URI: https://wpml.org/
 * Description: Compatibility layer for WordPress SEO and WPML | <a href="https://wpml.org/documentation/plugins-compatibility/using-wordpress-seo-with-wpml/">Documentation</a>
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 1.0.0
 * Plugin Slug: wp-seo-multilingual
 *
 * @package wpml/wpseo
 */

// WPML prior to 4.2.5 runs its own compatibility classes.
if ( version_compare( ICL_SITEPRESS_VERSION, '4.2.5', '<' ) ) {
	return;
}

if ( defined( 'WPSEOML_VERSION' ) ) {
	return;
}

define( 'WPSEOML_VERSION', '1.0.0' );
define( 'WPSEOML_PLUGIN_PATH', dirname( __FILE__ ) );

$autoloader_dir = WPSEOML_PLUGIN_PATH . '/vendor';
if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	$autoloader = $autoloader_dir . '/autoload.php';
} else {
	$autoloader = $autoloader_dir . '/autoload_52.php';
}
require_once $autoloader;

// We have to do this early because wordpress-seo does it early too.
$redirector = new WPML_WPSEO_Redirection();
if ( $redirector->is_redirection() ) {
	add_filter( 'wpml_skip_convert_url_string', '__return_true' );
}

/**
 * Initialize plugin when WPML has loaded.
 */
function wpml_wpseo_init() {
	$actions_filters_loader = new WPML_Action_Filter_Loader();
	$actions_filters_loader->load(
		array(
			'WPML_WPSEO_Main_Factory',
		)
	);
}
add_action( 'wpml_loaded', 'wpml_wpseo_init' );

/**
 * We need to do the redirection checks before wordpress-seo loads.
 * To resolve this we move ourselves first in the plugins list.
 * By using priority 1 we will go after WPML core.
 */
add_action( 'activated_plugin', 'wpml_wpseo_loads_first', 1 );
function wpml_wpseo_loads_first() {
	$path    = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
	$plugins = get_option( 'active_plugins' );
	$key     = array_search( $path, (array) $plugins, true );
	if ( $plugins && $key ) {
		array_splice( $plugins, $key, 1 );
		array_unshift( $plugins, $path );
		update_option( 'active_plugins', $plugins );
	}
}
