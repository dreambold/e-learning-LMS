<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_bigbluebutton';

$data =  array(
	'name' => __( 'BigBlueButton', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Dropdown list to show all the meetings from your BigBluebutton account.', 'woffice' ),
	'params' => array(
		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;
