<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_learndash';

$data =  array(
	'name' => __( 'LearnDash', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Display Learndash elements', 'woffice' ),
	'params' => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Type', 'woffice'),
			"description" => esc_html__('What do you want to display', 'woffice'),
			"param_name" => "type",
			'value' => array(
				esc_html__('Current User data (Courses, scores, certificates..)','woffice') => 'user_list',
				esc_html__('Courses list','woffice') => 'courses_list',
				esc_html__('Lessons list','woffice') => 'lessons_list',
				esc_html__('Quizzes list','woffice') => 'quizzes_list',
				esc_html__('Course\'s content','woffice') => 'course_content',
			)
		),
		array(
			"type" => "textfield",
			"heading" => esc_html__('Number', 'woffice'),
			'description' => __('Insert an integer please (Min: 0 - Max: 100)', 'woffice'),
			"param_name" => "num",
			"value" => "10"
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Order', 'woffice'),
			"param_name" => "order",
			'value' => array(
				esc_html__('Ascending', 'woffice') => 'ASC',
				esc_html__('Descending','woffice') => 'DESC',
			),
		),
		array(
			"type" => "textfield",
			"heading" => esc_html__('Optional Tag', 'woffice'),
			'description' => __('Display only the ones from a specific tag. We need its slug here.', 'woffice'),
			"param_name" => "tag",
		),
		array(
			"type" => "textfield",
			"heading" => esc_html__('Optional Category', 'woffice'),
			'description' => __('Display only the ones from a specific category. We need its slug here.', 'woffice'),
			"param_name" => "category",
		),
		array(
			"type" => "checkbox",
			"heading" => esc_html__('Current user only', 'woffice'),
			"param_name" => "only_current_user",
			'description' => esc_html__( 'Display the data only for the current user ? (Not all courses, but his courses for example). ONLY FOR THE COURSES', 'woffice' ),
			'value' => '',
		),

		vc_map_add_css_animation(),
	),
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;