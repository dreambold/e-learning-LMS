<?php

namespace OTGS\Toolset\Views\Controller;

/**
 * Plugin upgrade controller.
 *
 * Compares current plugin version with a version number stored in the database, and performs upgrade routines if
 * necessary.
 *
 * Note: Filters to add upgrade routines are not provided on purpose, so all routines need to be defined here.
 * 
 * It works with version numbers, which are easier to compare and manipulate with. See convert_version_string_to_number()
 * for details.
 * 
 * First database version is 20700000 so we can not add install routines until 2.6.4+
 *
 * @since 2.6.4
 */
class Upgrade {

	private static $instance;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function initialize() {
		$instance = self::get_instance();
		$instance->check_upgrade();
	}

	/** 
     * Name of the option used to store version number.
     * 
     * @since 2.6.4
     */
	const DATABASE_VERSION_OPTION = 'wpv_database_version';

	/**
	 * Check if a setup and an upgrade are needed, and if yes, perform them.
	 *
	 * @since 2.6.4
	 */
	public function check_upgrade() {
		if ( $this->is_setup_needed() ) {
			$this->do_setup();
		}
		if ( $this->is_upgrade_needed() ) {
			$this->do_upgrade();
		}
	}

	/**
	 * Returns true if a setup is needed.
	 *
	 * @return bool
	 * @since 2.6.4
	 */
	private function is_setup_needed() {
		return ( $this->get_database_version() === 0 );
	}

	/**
	 * Returns true if an upgrade is needed.
	 *
	 * @return bool
	 * @since 2.6.4
	 */
	private function is_upgrade_needed() {
		return ( $this->get_database_version() < $this->get_plugin_version() );
	}

	/**
	 * Check if an upgrade is needed after importing data, and if yes, perform it.
	 * 
	 * @param int|null $from_version The version to upgrade from
	 *
	 * @since 2.6.4
	 */
	public function check_import_upgrade( $from_version ) {
		if ( $this->is_import_upgrade_needed( $from_version ) ) {
			$this->do_upgrade( $from_version );
		}
	}

	/**
	 * Returns true if an upgrade after importing data is needed.
	 * 
	 * @param int|null $from_version The version to upgrade from
	 *
	 * @return bool
	 * @since 2.6.4
	 */
	private function is_import_upgrade_needed( $from_version) {
		return ( $from_version < $this->get_plugin_version() );
	}

	/**
	 * Get current plugin version number.
	 * 
	 * @return int
	 * @since 2.6.4
	 */
	private function get_plugin_version() {
		return $this->convert_version_string_to_number( WPV_VERSION );
	}

	/**
	 * Get number of the version stored in the database.
	 * 
	 * @return int
	 * @since 2.6.4
	 */
	public function get_database_version() {
		$version = (int) get_option( self::DATABASE_VERSION_OPTION, 0 );
		return $version;
	}

	/**
	 * Transform a version string to a version number.
	 * 
	 * The version string looks like this: "major.minor[.maintenance[.revision]]". We expect that all parts have
	 * two digits at most.
	 * 
	 * Conversion to version number is done like this:
	 * $ver_num  = MAJOR      * 1000000
	 *           + MINOR        * 10000
	 *           + MAINTENANCE    * 100
	 *           + REVISION         * 1
	 *
	 * That means, for example "1.8.11.12" will be equal to:
	 *                          1000000
	 *                        +   80000
	 *                        +    1100
	 *                        +      12
	 *                        ---------
	 *                        = 1081112
     *
	 * @param string $version_string
	 * @return int
	 * @since 2.6.4
	 */
	private function convert_version_string_to_number( $version_string ) {
		if ( 0 === $version_string ) {
			return 0;
		}
		
		$version_parts = explode( '.', $version_string );
		$multipliers = array( 1000000, 10000, 100, 1 );

		$version_part_count = count( $version_parts );
		$version = 0;
		for( $i = 0; $i < $version_part_count; ++$i ) {
			$version_part = (int) $version_parts[ $i ];
			$multiplier = $multipliers[ $i ];

			$version += $version_part * $multiplier;
		}

		return $version;
	}

	/**
	 * Update the version number stored in the database.
	 * 
	 * @param int $version_number
	 * @since 2.6.4
	 */
	private function update_database_version( $version_number ) {
		if ( is_numeric( $version_number ) ) {
			update_option( self::DATABASE_VERSION_OPTION, (int) $version_number );
		}
	}

	/**
	 * Get an array of upgrade routines.
	 * 
	 * Each routine is defined as an associative array with two elements:
	 *     - 'version': int, which specifies the *target* version after the upgrade
	 *     - 'callback': callable
	 * 
	 * @return array
	 * @since 2.6.4
	 */
	private function get_upgrade_routines() {
		$upgrade_routines = array(
            /*
			array(
				'version' => xxyyzztt,
				'callback' => array( $this, 'callback' )
            )
            */
		);
		
		return $upgrade_routines;
    }

	/**
	 * Perform the upgrade by calling the appropriate upgrade routines and updating the version number in the database.
	 * 
	 * @param int|null $from_version The version to upgrade from, null to use the current database version
	 *
	 * @since 2.6.4
	 */
	private function do_upgrade( $from_version = null ) {
		$from_version = is_null( $from_version )
			? $this->get_database_version()
			: $from_version;
		$upgrade_routines = $this->get_upgrade_routines();
        $target_version = $this->get_plugin_version();

		// Sort upgrade routines by their version.
		$routines_by_version = array();
		foreach( $upgrade_routines as $key => $row ) {
			$routines_by_version[ $key ] = $row['version'];
		}
		array_multisort( $routines_by_version, SORT_DESC, $upgrade_routines );

		// Run all the routines necessary
		foreach( $upgrade_routines as $routine ) {
			$upgrade_version = (int) toolset_getarr( $routine, 'version' );
			
			if ( $from_version < $upgrade_version && $upgrade_version <= $target_version ) {
				$callback = toolset_getarr( $routine, 'callback' );
				if ( is_callable( $callback ) ) {
					call_user_func( $callback );
				}
				$this->update_database_version( $upgrade_version );
			}
		}

		// Finally, update to current plugin version even if there are no other routines to run, so that
		// this method is not called every time by check_upgrade().
		$this->update_database_version( $target_version );
    }
    
    /**
     * Set database for new sites.
     *
     * @since 2.6.4
     */
    public function do_setup() {
        $this->set_default_settings();
    }

    /**
     * Set default setting values for new sites.
     *
     * @since 2.7.0
     */
    private function set_default_settings() {
        // New sites should not support query filters by meta with spaces in the meta key
        $settings = \WPV_Settings::get_instance();
        $settings->support_spaces_in_meta_filters = false;
        $settings->allow_views_wp_widgets_in_elementor = false;
		$settings->save();
    }
}
