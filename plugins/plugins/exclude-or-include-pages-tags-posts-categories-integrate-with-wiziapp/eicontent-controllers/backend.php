<?php

class EIContent_Backend {

	/**
	* @var object Refer to global $wpdb
	*/
	private $_db;

	/**
	* @var object EIContent_Model instance
	*/
	private $_model;

	/**
	* @var array
	*/
	private $_checked_array = array(
		'wizi_included_site' => 'checked="checked"',
		'wizi_included_app'  => 'checked="checked"',
	);

	/**
	* @var array
	*/
	private $_tables_array = array( 'posts', 'terms', 'links', );

	private $_is_deactivation = TRUE;

	/**
	* Set _db and _model properties.
	* Activate activation and unactivation hooks.
	* Choose to trigger Site Front End or Back End hooks
	*/
	public function __construct() {
		$this->_db = &$GLOBALS['wpdb'];
		$this->_model = new EIContent_Model($this->_db);
	}

	/*
	"Activation - Deactivation" Part
	*/

	/**
	* On Activation Event add two Exclude Include Content plugin columns
	* to WP posts and terms tables.
	*
	* @return void
	*/
	public function activate() {
		try {
			foreach ( $this->_tables_array as $table ) {
				// Check, if Exclude Include Content plugin columns not exists already
				$columns_names = $this->_db->get_col( "SHOW COLUMNS FROM `" . $this->_db->$table . "`" );
				if ( in_array( 'wizi_included_site', $columns_names ) || in_array( 'wizi_included_app', $columns_names ) ) {
					$message = 'Activation failed, the wizi_included_site or the wizi_included_app columns already exist in the ' . $this->_db->$table . ', please try again.';
					throw new Exception($message);
				}

				$sql =
				"ALTER TABLE `" . $this->_db->$table . "`" .
				"ADD COLUMN `wizi_included_site` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL COMMENT 'Is Post included to Site', " .
				"ADD COLUMN `wizi_included_app`  TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL COMMENT 'Is Post included to WiziApp';";

				if ( ! $this->_db->query( $sql ) ) {
					$message = 'Activation failed, creating new columns in the ' . $this->_db->$table . ' problem. ' . $this->_db->last_error . '.';
					throw new Exception($message);
				}
			}

		} catch (Exception $e) {
			// If error happened, remove added columns
			$this->_is_deactivation = FALSE;
			$this->deactivate();

			echo
			'<script type="text/javascript">alert("' . $e->getMessage() . '")</script>' . PHP_EOL .
			$e->getMessage();

			exit;
		}
	}

	function required_plugins() {

        $plugins = array(

            // This is an example of how to include a plugin bundled with a theme.
            array(
                    'name'           => 'Wiziapp- Android App Maker - Create Android App for your WP site within minutes',
                'slug'               => 'wiziapp-create-your-own-native-iphone-app',
                'required'           => false,
            ),
        );

        $config = array(
            'id'           => 'wiziapp',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'parent_slug'  => 'plugins.php',            // Parent menu slug.
            'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.

            'strings'      => array(
                'notice_can_install_recommended'  => _n_noop(
                    'New - Recommending the following plugin: %1$s.',
                    'New - Recommending the following plugin: %1$s.',
                    'wiziapp'
                ),
            ),
        );

        tgmpa( $plugins, $config );
    }

	/**
	* On Deactivation Event or unseccessful Activation Event
	* remove two Exclude Include Content plugin columns
	* from WP posts and terms tables.
	*
	* @param bool Optional, default - FALSE. Is the Exclude Include Content plugin not activated yet.
	* @return void
	*/
	public function deactivate() {
		$message = array();
		$is_successful = TRUE;
		foreach ( $this->_tables_array as $table ) {
			$columns_names = $this->_db->get_col( "SHOW COLUMNS FROM `" . $this->_db->$table . "`", 0 );

			foreach ( array( 'wizi_included_site', 'wizi_included_app', ) as $column ) {
				if ( in_array( $column, $columns_names ) ) {
					// If Exclude Include Content plugin column exist...
					if ( ! $this->_db->query( "ALTER TABLE `" . $this->_db->$table . "` DROP COLUMN `" . $column . "`;" ) ) {
						$message[] = 'delete the ' . $column . ' column of the Exclude Include Content plugin from the ' . $this->_db->$table . ' table problem.';
						$message[] = $this->_db->last_error . '.';
						$is_successful = FALSE;
					}
				}
			}
		}

		if ( ! $is_successful && $this->_is_deactivation ) {
			echo
			'<script type="text/javascript">alert("' . 'Deactivation failed, ' . implode(' ', $message) . '")</script>' . PHP_EOL .
			'Deactivation failed,<br />' . implode('<br />', $message);
			exit;
		}
	}

	/*
	"Show Checkboxes in Admin Panel" Part
	*/

	public function add_page_checkboxes() {
		global $post;

		if ( is_object($post) && property_exists($post, 'post_type') && $post->post_type === 'page' ) {
			$this->_checked_array = array(
				'wizi_included_site' => ( (bool) $post->wizi_included_site ) ? 'checked="checked"' : '',
				'wizi_included_app'  => ( (bool) $post->wizi_included_app ) ? 'checked="checked"' : '',
			);

			EIContent_View::print_checkboxes( $this->_checked_array );
		}
	}

	public function add_category_checkboxes($category) {
		if ( is_object( $category ) && isset( $category->wizi_included_site ) && isset( $category->wizi_included_site ) ) {
			$this->_checked_array = array(
				'wizi_included_site' => ((bool) $category->wizi_included_site ) ? 'checked="checked"' : '',
				'wizi_included_app'  => ((bool) $category->wizi_included_app ) ? 'checked="checked"' : '',
			);
		}

		EIContent_View::print_checkboxes( $this->_checked_array );
	}

	public function add_link_checkboxes() {
		// To avoid double print from double do_action('submitlink_box') exitsting
		/*
		if ( strpos($temp = ob_get_contents(), '<div id="major-publishing-actions">') === FALSE ) {
		return;
		}
		*/
		static $count = 0;
		$count++;
		if ( $count === 1 ) {
			return;
		}

		global $link;
		if ( is_object( $link ) && isset( $link->wizi_included_site ) && isset( $link->wizi_included_site ) ) {
			$this->_checked_array = array(
				'wizi_included_site' => ((bool) $link->wizi_included_site ) ? 'checked="checked"' : '',
				'wizi_included_app'  => ((bool) $link->wizi_included_app ) ? 'checked="checked"' : '',
			);
		}

		EIContent_View::print_checkboxes( $this->_checked_array );
	}

	public function add_link_category_checkboxes($category) {
		if ( is_object( $category ) && isset( $category->wizi_included_site ) && isset( $category->wizi_included_site ) ) {
			$this->_checked_array = array(
				'wizi_included_site' => ((bool) $category->wizi_included_site ) ? 'checked="checked"' : '',
				'wizi_included_app'  => ((bool) $category->wizi_included_app ) ? 'checked="checked"' : '',
			);

			EIContent_View::print_checkboxes( $this->_checked_array );
		}
	}

	public function add_tag_checkboxes($tag) {
		if ( is_object( $tag ) && isset( $tag->term_id ) ) {
			$query = "SELECT `wizi_included_site`, `wizi_included_app` FROM `" . $this->_db->terms . "` WHERE `term_id` = " . intval( $tag->term_id );
			$wiziapp_values = $this->_db->get_row( $query, ARRAY_A );

			$this->_checked_array = array(
				'wizi_included_site' => ( (bool) $wiziapp_values['wizi_included_site'] ) ? 'checked="checked"' : '',
				'wizi_included_app'  => ( (bool) $wiziapp_values['wizi_included_app'] ) ? 'checked="checked"' : '',
			);
		}

		EIContent_View::print_checkboxes( $this->_checked_array );
	}

	public function exclude_wiziapp_push($post) {
		if ( is_object($post) && isset( $post->ID ) && $this->_model->is_excluded_exist( $post->ID ) ) {
			return NULL;
		}

		return $post;
	}

	/*
	"Update Element Exclusion in DB" Part
	*/

	public function update_page_exclusion($page_id, $page) {
		if ( ! ( is_object( $page ) && $page->post_type === 'page' && isset( $_POST['wiziapp_ctrl_present'] ) ) ) {
			return;
		}

		$this->_model->update_element_exclusion( 'posts', array( 'ID' => $page_id ) );
	}

	public function update_term_exclusion($term_id) {
		if ( ! isset($_POST['wiziapp_ctrl_present'] ) ) {
			return;
		}

		$this->_model->update_element_exclusion( 'terms', array( 'term_id' => $term_id ) );
	}

	public function update_link_exclusion($link_id) {
		if ( ! isset($_POST['wiziapp_ctrl_present'] ) ) {
			return;
		}

		$this->_model->update_element_exclusion( 'links', array( 'link_id' => $link_id ) );
	}

}