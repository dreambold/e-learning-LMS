<?php
/**
 * LearnDash Settings field Hidden.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Fields' ) ) && ( ! class_exists( 'LearnDash_Settings_Fields_Hidden' ) ) ) {
	/**
	 * Class to create the settings field.
	 */
	class LearnDash_Settings_Fields_Hidden extends LearnDash_Settings_Fields {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->field_type = 'hidden';

			parent::__construct();
		}

		/**
		 * Function to crete the settiings field.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args An array of field arguments used to process the ouput.
		 * @return void
		 */
		public function create_section_field( $field_args = array() ) {
			$field_args = apply_filters( 'learndash_settings_field', $field_args );
			$html       = apply_filters( 'learndash_settings_field_html_before', '', $field_args );

			$html .= '<input ';
			$html .= $this->get_field_attribute_type( $field_args );
			$html .= $this->get_field_attribute_name( $field_args );
			$html .= $this->get_field_attribute_id( $field_args );
			$html .= $this->get_field_attribute_class( $field_args );
			$html .= $this->get_field_attribute_misc( $field_args );

			if ( isset( $field_args['value'] ) ) {
				$html .= ' value="' . $field_args['value'] . '" ';
			} else {
				$html .= ' value="" ';
			}

			$html .= ' />';

			$html = apply_filters( 'learndash_settings_field_html_after', $html, $field_args );

			echo $html;
		}
	}
}
add_action(
	'learndash_settings_sections_fields_init',
	function() {
		LearnDash_Settings_Fields_Hidden::add_field_instance( 'hidden' );
	}
);
