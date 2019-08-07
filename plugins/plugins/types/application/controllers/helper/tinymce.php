<?php
/**
 * Utility methods used for TinyMCE issues.
 *
 * @since 2.3
 */
class Types_Helper_TinyMCE {

	/**
	 * It stores the mceInit json data for tinymce editor init
	 *
	 * @var String
	 * @since m2m
	 */
	private $mceinit = array();


	/**
	 * WP Editor mock for testing purposes
	 *
	 * @var null|mock
	 * @since m2m
	 */
	private $_wp_editor;


	/**
	 * Constructor
	 *
	 * @param null|Mock $_wp_editor Used for testing purposes.
	 * @since m2m
	 */
	public function __construct( $_wp_editor = null ) {
		$this->_wp_editor = $_wp_editor;
	}


	/**
	 * Parse settings using _WP_Editors
	 *
	 * @param String $id ID of the editor instance.
	 * @param Array  $arguments Array of editor arguments.
	 * @return Array
	 */
	private function parse_settings( $id, $arguments = array() ) {
		if ( $this->_wp_editor ) {
			return $this->_wp_editor->parse_settings( $id, $arguments );
		} else {
			return _WP_Editors::parse_settings( $id, $arguments );
		}
	}


	/**
	 * Editor settings using _WP_Editors
	 *
	 * @param String $id ID of the editor instance.
	 * @param Array  $settings Array of editor arguments.
	 */
	private function editor_settings( $id, $settings = array() ) {
		if ( $this->_wp_editor ) {
			$this->_wp_editor->editor_settings( $id, $settings );
		} else {
			_WP_Editors::editor_settings( $id, $settings );
		}
	}


	/**
	 * Generates mceInit data for tinymce initialization
	 *
	 * It takes the html rendered inputs ans search for the textarea IDs. Why do we need that? because wp_editor is rendered with a different ID because the same editor can be displayed several times in the same page.
	 *
	 * @param Toolset_Field_Instance[] $fields An array of fields.
	 * @param String                   $html Rendered inputs.
	 * @return Array A list of editor configuration.
	 * @since m2m
	 */
	public function generate_mceinit_data( $fields, $html ) {
		// _WP_Editors doesn't have a public method to use the mceInit data, so a filter is needed. Not the best approach.
		add_filter( 'tiny_mce_before_init', array( $this, 'get_mceinit_data' ), 10, 2 );
		$fields_included = array();
		foreach ( $fields as $field ) {
			if ( 'wysiwyg' === $field->get_field_type()->get_slug() ) {
				$slug = $field->get_definition()->get_slug();
				if ( ! in_array( $slug, $fields_included, true ) ) {
					// Find each textarea instance inside the rendered html.
					preg_match_all( '#' . $slug . '_\d{5}#', $html, $ids );
					foreach ( $ids[0] as $id ) {
						if ( ! in_array( $id, $this->mceinit, true ) ) {
							$settings = $this->parse_settings( $id, array() );
							$settings['textarea_name'] = $slug;
							$this->editor_settings( $id, $settings );
						}
					}
					$fields_included[] = $slug;
				}
			}
		}
		remove_filter( 'tiny_mce_before_init', array( $this, 'get_mceinit_data' ), 10, 2 );

		return $this->mceinit;
	}


	/**
	 * Gets the mceinit data from a filter
	 *
	 * @param String $mceinit mceInit data.
	 * @param String $id editor ID.
	 *
	 * @since m2m
	 * @return String
	 */
	public function get_mceinit_data( $mceinit, $id ) {
		if ( ! preg_match( '#^wpcf-#', $id ) ) {
			$id = 'wpcf-' . $id;
		}

		if ( ! in_array( $id, $this->mceinit, true ) ) {
			$this->mceinit[ $id ] = $mceinit;
			$this->mceinit[ $id ]['formats'] = $this->parse_json( $this->mceinit[ $id ]['formats'] );
			$this->mceinit[ $id ]['wp_shortcut_labels'] = $this->parse_json( $this->mceinit[ $id ]['wp_shortcut_labels'] );
			$this->mceinit[ $id ]['selector'] = '#' . $id;
		}
		return $mceinit;
	}


	/**
	 * Adds quotes and parse to JSON
	 *
	 * @param String $text Json encode.
	 * @return Object
	 * @since m2m
	 */
	private function parse_json( $text ) {
		return json_decode(
			preg_replace( '#(\w+)\:#', '"$1":', $text )
		);
	}
}
