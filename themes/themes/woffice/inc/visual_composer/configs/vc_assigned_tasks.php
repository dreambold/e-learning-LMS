<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_assigned_tasks';

$data =  array(
	'name' => __( 'Assigned tasks', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Show the assigned tasks of the current logged-in user', 'woffice' ),
	'params' => array(
		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;
