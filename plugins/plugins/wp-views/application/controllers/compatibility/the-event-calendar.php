<?php

namespace OTGS\Toolset\Views\Controller\Compatibility;

/**
 * Class TheEventsCalendar
 *
 * Handles the compatibility between Views and The Events Calendar.
 *
 * @package OTGS\Toolset\Views\Controller\Compatibility
 *
 * @since 2.6.4
 */
class TheEventsCalendar extends Base {
	private $the_events_calendar_is_active;

	private $tribe_events_query_class_exists;

	public function __construct(
		\Toolset_Condition_Plugin_The_Events_Calendar_Active $the_events_calendar_is_active = null,
		\Toolset_Condition_Plugin_The_Events_Calendar_Tribe_Events_Query_Class_Exists $tribe_events_query_class_exists = null
	) {
		$this->the_events_calendar_is_active = $the_events_calendar_is_active
			? $the_events_calendar_is_active
			: new \Toolset_Condition_Plugin_The_Events_Calendar_Active();

		$this->tribe_events_query_class_exists = $tribe_events_query_class_exists
			? $tribe_events_query_class_exists
			: new \Toolset_Condition_Plugin_The_Events_Calendar_Tribe_Events_Query_Class_Exists();
	}

	public function initialize() {
		$this->init_hooks();
	}

	private function init_hooks() {
		add_filter( 'wpv_filter_query', array( $this, 'remove_the_events_calendar_pre_get_posts' ), 10, 3 );

		add_filter( 'wpv_filter_query_post_process', array( $this, 'restore_the_events_calendar_pre_get_posts' ), 10, 3 );
	}

	/**
	 * Removes the "pre_get_posts" action from the Events Calendar.
	 *
	 * When loading the output of a View with post selection coming from the custom post type of The Events Calendar, the
	 * third-party plugin hijacks the query and injects "where" values that prevent the View to properly fetch the needed
	 * posts. We are temporarily removing the "pre_get_posts" action while the View is rendered and we are restoring it
	 * later.
	 *
	 * @param array  $query         The query arguments.
	 * @param array  $view_settings The View settings.
	 * @param string $id            The View ID.
	 *
	 * @return array The query arguments.
	 */
	public function remove_the_events_calendar_pre_get_posts( $query, $view_settings, $id ) {
		if (
			$this->the_events_calendar_is_active->is_met() &&
			$this->tribe_events_query_class_exists->is_met()
		) {
			remove_action( 'pre_get_posts', array( 'Tribe__Events__Query', 'pre_get_posts' ), 50 );
		}

		return $query;
	}

	/**
	 * Restores the "pre_get_posts" action from the Events Calendar.
	 *
	 * @param array  $query         The query arguments.
	 * @param array  $view_settings The View settings.
	 * @param string $id            The View ID.
	 *
	 * @return array The query arguments.
	 */
	public function restore_the_events_calendar_pre_get_posts( $query, $view_settings, $id ) {
		if (
			$this->the_events_calendar_is_active->is_met() &&
			$this->tribe_events_query_class_exists->is_met()
		) {
			add_action( 'pre_get_posts', array( 'Tribe__Events__Query', 'pre_get_posts' ), 50 );
		}

		return $query;
	}
}
