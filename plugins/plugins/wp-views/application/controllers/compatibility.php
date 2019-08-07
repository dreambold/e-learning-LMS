<?php

namespace OTGS\Toolset\Views\Controller;

/**
 * Class Compatibility
 *
 * Handles the compatibility between Views and other third-party or OTGS plugins.
 *
 * @package OTGS\Toolset\Views\Controller
 *
 * @since 2.7.0
 */
class Compatibility {

	private $compatibility;

	public function __construct( Compatibility\Base $compatibility = null ) {
		$this->compatibility = $compatibility
			? $compatibility
			: null;
	}

	public function initialize() {
		if ( null === $this->compatibility ) {
			$this->initialize_all_integrations();
		} else {
			$this->initialize_single_integration( $this->compatibility );
		}
	}

	private function initialize_all_integrations() {
		// We can even add here a check for WPML being installed and properly configured
		$wpml_integration = new Compatibility\Wpml();
		$this->initialize_single_integration( $wpml_integration );

		if ( did_action( 'elementor/loaded' ) ) {
			$elementor_compatibility = new Compatibility\Elementor();
			$this->initialize_single_integration( $elementor_compatibility );
		}

		$the_events_calendar_is_active = new \Toolset_Condition_Plugin_The_Events_Calendar_Active();
		$tribe_events_query_class_exists = new \Toolset_Condition_Plugin_The_Events_Calendar_Tribe_Events_Query_Class_Exists();
		if (
			$the_events_calendar_is_active->is_met() &&
			$tribe_events_query_class_exists->is_met()
		) {
			$the_events_calendar_compatibility = new Compatibility\TheEventsCalendar();
			$this->initialize_single_integration( $the_events_calendar_compatibility );
		}

		$gutenberg_is_active = new \Toolset_Condition_Plugin_Gutenberg_Active();
		if ( $gutenberg_is_active->is_met() ) {
			$views_editor_blocks = new Compatibility\EditorBlocks\Blocks();
			$this->initialize_single_integration( $views_editor_blocks );
		}
	}

	private function initialize_single_integration( Compatibility\Base $compatibility ) {
		$compatibility->initialize();
	}
}
