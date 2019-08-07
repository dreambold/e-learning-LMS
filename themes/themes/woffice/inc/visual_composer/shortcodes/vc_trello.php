<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_trello';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];

echo '<div id="' . $atts['el_id'] . '" class="shortcode-trello ' . $css_class . '">';

	if (class_exists('wp_trello')){
		if (!empty($atts['trello_id'])):
			echo do_shortcode('[wp-trello type="'.$atts['type'].'" id="'.$atts['trello_id'].'" link="'.$atts['link'].'"]');
		else :
			_e('Please select an ID for the Trello Element.','woffice');
		endif;
	}
	else {
		_e('Please install the plugin ','woffice');
		echo '<a href="https://wordpress.org/plugins/wp-trello/" target="_blank">WP Trello</a>';
	}

echo '</div>';