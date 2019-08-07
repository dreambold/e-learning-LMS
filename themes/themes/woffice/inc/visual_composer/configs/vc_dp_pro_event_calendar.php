<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_dp_pro_event_calendar';


$params = array();
$calendars = array();

if(defined('DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS')) {

	global $wpdb, $table_prefix;
	$table_name_calendars = $table_prefix . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	$querystr = "SELECT * FROM $table_name_calendars ORDER BY title ASC";
	$calendars_obj = $wpdb->get_results( $querystr, OBJECT );

	foreach ( $calendars_obj as $calendar_key ) {
		$calendars[ $calendar_key->title ] = $calendar_key->id;
	}

	$params = array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Calendar', 'woffice'),
			"param_name" => "calendar",
			'value' => $calendars,
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('Layout', 'woffice'),
			"param_name" => "type",
			'value' => array(
				esc_html__('Default (calendar)', 'woffice') => 'default',
				esc_html__('Upcoming Events', 'woffice') => 'upcoming',
				esc_html__('Past Events', 'woffice') => 'past',
				esc_html__('Accordion List', 'woffice') => 'accordion',
				esc_html__('Accordion Upcoming Events', 'woffice') => 'accordion-upcoming',
				esc_html__('Add Event', 'woffice') => 'add-event',
				esc_html__('List of Bookings by Logged in User', 'woffice') => 'bookings-user',
				esc_html__('Today Events', 'woffice') => 'today-events',
				esc_html__('Google Map Upcoming Events', 'woffice') => 'gmap-upcoming',
				esc_html__('Grid Upcoming Events', 'woffice') => 'grid-upcoming',
				esc_html__('Compact', 'woffice') => 'compact',
				esc_html__('Countdown', 'woffice') => 'countdown',
			),
			'std' => 'upcoming',
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('View', 'woffice'),
			"param_name" => "view",
			'value' => array(
				esc_html__('Monthly Calendar (Displays a calendar with counters or event titles.)', 'woffice') => 'monthly',
				esc_html__('Monthly Event List (List all events in the month)', 'woffice') => 'monthly-all-events',
				esc_html__('Weekly (Events weekly as a schedule or a list)', 'woffice') => 'weekly',
				esc_html__('Daily (Events daily as a schedule or a list)', 'woffice') => 'daily'
			),
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__('All events', 'woffice'),
			"param_name" => "all-events",
			'value' => array(
				esc_html__('Enable', 'woffice') => true,
				esc_html__('Disable','woffice') => false,
			),
			'std' => false,
		),
		vc_map_add_css_animation()
	);
}

$data =  array(
	'name' => __( 'Pro Event Calendar', 'woffice' ),
	'base' => $shortcode_slug,
	'category' => _x( 'Woffice', 'The tab name for WPBakery Page builder elements', 'woffice' ),
	'description' => __( 'Add a calendar from Pro Event Plugin, can also display frontend form.', 'woffice' ),
	'params' => $params
);

Woffice_Shortcodes::addStandardVcParameters($data);

return $data;