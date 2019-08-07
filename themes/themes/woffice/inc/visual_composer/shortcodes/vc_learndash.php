<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_learndash';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];

$atts['num'] = ( is_integer($atts['num']) && ( $atts['num'] >= 0 || $atts['num'] <= 100) ) ? $atts['num'] : 10;

if ($atts['type'] == 'user_list') {
	$shortcode = '[ld_profile]';
}
elseif ($atts['type'] == 'course_content') {
	$shortcode = '[course_content course_id=".123."]';
}
else {
	if ($atts['type'] == 'courses_list') {
		$prefix = "ld_course_list";
	}
	elseif ($atts['type'] == 'lessons_list'){
		$prefix = "ld_lesson_list";
	}
	else {
		$prefix = "ld_quiz_list";
	}

	$num = ' num="'.$atts['num'].'"';
	$only_current_user = ($atts['only_current_user'] == true) ? ' mycourses="true"' : '';
	$order = ' order="'.$atts['order'].'"';
	$tag = (!empty($atts['tag'])) ? ' tag="'.$atts['tag'].'"' : '';
	$category = (!empty($atts['catgeory'])) ? ' category_name="'.$atts['catgeory'].'"' : '';

	$shortcode = '['.$prefix.$num.$only_current_user.$order.$tag.$category.']';
}

echo '<div id="' . $atts['el_id'] . '" class="' . $css_class . '">';
echo do_shortcode($shortcode);
echo '</div>';