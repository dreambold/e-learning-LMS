<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_file_away';

$data =  array(
	'name' => __( 'File Manager (File Away)', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Add a File Away file manager', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__('Content', 'woffice'),
			'description' => esc_html__('Choose here what you want to generate', 'woffice'),
			"param_name" => "file_away_kind",
			'value' => array(
				esc_html__('Directory View','woffice') => 'fileaway',
				esc_html__('Files Upload','woffice') => 'fileup',
			),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__('Select a directory', 'woffice'),
			"param_name" => "file_away_directory",
			'value' => array(
				esc_html__('1','woffice') => '1',
				esc_html__('2','woffice') => '2',
				esc_html__('3','woffice') => '3',
				esc_html__('4','woffice') => '4',
				esc_html__('5','woffice') => '5',
			),
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__('Optional Sub directory', 'woffice'),
			'description' => __('The path to a sub directory from the directory you have choose in the last option. Like : test/ ', 'woffice'),
			"param_name" => "file_away_sub",
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => esc_html__('Additional attributes', 'woffice'),
			'description' => __('If you want add additional attributes, you can just put here, separating them with a space. Example: manager=on images=only', 'woffice'),
			"param_name" => "file_away_customattr",
		),

		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;
