<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_eventon';

$data =  array(
	'name' => __( 'EventOn', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Add a full month calendar from EventON events or a single event', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Type', 'woffice'),
			"param_name" => "eventon_type",
			'value' => array(
				esc_html__('Main Calendar', 'woffice') => 'calendar',
				esc_html__('Single Event','woffice') => 'event',
			),
		),
		array(
			"type" => "textfield",
			"heading" => esc_html__('Unique ID', 'woffice'),
			'description' => __('An unique ID for the Main calendar OR an event ID to display it', 'woffice'),
			"param_name" => "eventon_id",
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Auto Open events', 'woffice'),
			"param_name" => "eventon_open",
			'value' => array(
				esc_html__('Enable', 'woffice') => true,
				esc_html__('Disable','woffice') => false,
			),
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('See event excerpt', 'woffice'),
			"param_name" => "eventon_excerpt",
			'value' => array(
				esc_html__('Enable', 'woffice') => true,
				esc_html__('Disable','woffice') => false,
			),
		),

		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;