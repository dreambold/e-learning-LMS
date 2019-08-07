<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$shortcode_slug = 'vc_eventon';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];

if ( empty($atts['eventon_id']) ) {
	_e( 'Please select an unique ID in the shortcode option.', 'woffice' );
	return;
}


// main calendar display
if ($atts['eventon_type'] == "calendar") {

	$extra_args = '';

	if ( $atts['eventon_open'] )
		$extra_args = ' evc_open="yes"';

	echo do_shortcode('[add_eventon_fc cal_id="'.$atts['eventon_id'].'" show_et_ft_img="yes" ft_event_priority="yes" load_fullmonth="no" '.$extra_args.']');

}
// single event
else {

	$eventon_open = ( $atts['eventon_open'] ) ? 'show_exp_evc="yes" ' : '';

	$eventon_excerpt = ( $atts['eventon_excerpt'] ) ? 'show_excerpt="yes" ' : '';

	$extra_args = $eventon_open . $eventon_excerpt;

	echo '<div id="' . $atts['el_id'] . '" class="' . $css_class . '">';
	echo do_shortcode('[add_single_eventon id="'.$atts['eventon_id'].'" '.$extra_args.' open_as_popup="yes"]');
	echo '</div>';
}


