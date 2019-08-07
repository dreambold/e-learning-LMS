<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_trello';

$data =  array(
	'name' => __( 'Trello', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Add a Trello Element', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Type of Trello element', 'woffice'),
			"param_name" => "type",
			'value' => array(
				esc_html__('Organizations', 'woffice') => 'organizations',
				esc_html__('Boards', 'woffice') => 'boards',
				esc_html__('Lists', 'woffice') => 'lists',
				esc_html__('Cards', 'woffice') => 'cards',
				esc_html__('Card', 'woffice') => 'card',
			)
		),
		array(
			"type" => "textfield",
			"heading" => esc_html__('ID', 'woffice'),
			'description' => __('The ID of the element, you can find it in the URL or in Settings -> WP TRELLO -> API Helper', 'woffice'),
			"param_name" => "trello_id",
			"value" => "10"
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Link', 'woffice'),
			"description" => esc_html__('Display as a link to Trello', 'woffice'),
			"param_name" => "link",
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