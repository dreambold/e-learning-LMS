<?php
function woffice_child_scripts() {
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		$theme_info = wp_get_theme();
		wp_enqueue_style( 'woffice-child-stylesheet', get_stylesheet_uri(), array(), $theme_info->get( 'Version' ) );
	}
}
add_action('wp_enqueue_scripts', 'woffice_child_scripts', 30);
