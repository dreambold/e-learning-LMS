<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_frontend_creation';

$choices = array();
$choices[  __('Blog Post', 'woffice') ] = 'post';
if (function_exists( 'woffice_wiki_extension_on' )){
	$choices[ __('Wiki Post', 'woffice') ] = 'wiki';
}
if (function_exists( 'woffice_projects_extension_on' )){
	$choices[ __('Project Post', 'woffice') ] = 'project';
}

$data =  array(
	'name' => __( 'Frontend Creation', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Add a form to send new posts, projects or wiki from the frontend', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('New Post Type Created', 'woffice'),
			"param_name" => "post_type",
			'value' => $choices,
		),

		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;