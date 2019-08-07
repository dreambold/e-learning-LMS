<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$shortcode_slug = 'vc_frontend_creation';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];


if ( empty($atts['post_type']))
	return;

if($atts['post_type'] == 'project') {
	$option_name = 'projects_create';
} elseif($atts['post_type'] == 'wiki') {
	$option_name = 'wiki_create';
} else {
	$option_name = 'post_create';
}

$allowed_data = $option_name ? woffice_get_settings_option($option_name) : null ;

if ( !Woffice_Frontend::role_allowed($allowed_data, $atts['post_type']) )
	return;

echo '<div id="' . $atts['el_id']. '" class="woffie-post-creation ' . $css_class . '">';

	// BACKEND SIDE
	$process_value = Woffice_Frontend::frontend_process($atts['post_type'], 0, true);

	// FORM rendering
	Woffice_Frontend::frontend_render($atts['post_type'],$process_value);

echo '</div>';
