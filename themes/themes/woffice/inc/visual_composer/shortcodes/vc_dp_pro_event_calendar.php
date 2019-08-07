<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$shortcode_slug = 'vc_dp_pro_event_calendar';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];

if ( empty($atts['calendar']) ) {
	esc_html_e( 'Please select an unique ID in the shortcode option.', 'woffice' );
	return;
}


/*
 * Custom parameters from the options :
 */
$all_events = ( $atts['all-events'] ) ? ' include_all_events="1"' : '';
$view = ' view="'.$atts['view'].'"';
$type = ( $atts['type'] !== 'default' ) ? ' type="'.$atts['type'].'"' : '';
$calendar = (!empty($atts['calendar'])) ? ' id="'.$atts['calendar'].'"' : '';

/*
 * We render the shortcode :
 */
echo '<div id="' . $atts['el_id'] . '" class="' . $css_class . '">';
echo do_shortcode('[dpProEventCalendar '. $calendar . $type . $view . $all_events .']');
echo '</div>';
