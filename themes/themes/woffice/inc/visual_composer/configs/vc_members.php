<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_members';

global $wp_roles;
$tt_roles = array();
foreach ($wp_roles->roles as $key=>$value){
	$tt_roles[$value['name']] = $key;
}
$tt_roles_tmp = array(__("All Users","woffice") => 'all' ) + $tt_roles;

$options = array(
	'role'  => array(
		'type'  => 'select',
		'value' => 'all',
		'label' => __('Select members type', 'woffice'),
		'choices' => $tt_roles_tmp,
	),
);

$data =  array(
	'name' => __( 'Members', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Display avatars of the members with link to their profiles', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Select members type', 'woffice'),
			"param_name" => "role",
			'value' => $tt_roles_tmp,
		),
		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;