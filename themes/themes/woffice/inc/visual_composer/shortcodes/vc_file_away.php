<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_file_away';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];


if ( !defined('fileaway') ) {

	esc_html_e('Please install File Away plugin and make sure it is activated.','woffice');
	echo '<a href="https://wordpress.org/plugins/file-away/" target="_blank">Plugin Page</a>';

	return;
}

$kind = $atts['file_away_kind'];
$dir = $atts['file_away_directory'];
$sub = $atts['file_away_sub'];
$additional_attributes = $atts['file_away_customattr'];

if ( $kind == "fileup" ) {
	$extra_fields = '';
}
else {
	$extra_fields = 'type="table" directories="true" paginate="false" makedir="true" flightbox="images"';
}
$sub_ready = (!empty($sub)) ? 'sub="'.$sub.'"' : '';

echo '<div id="' . $atts['el_id'] . '" class="' . $css_class . '">';
echo do_shortcode('['.$kind.' base="'.$dir.'" makedir="true" '.$sub_ready.' '.$extra_fields.' '.$additional_attributes.']');
echo '</div>';
?>