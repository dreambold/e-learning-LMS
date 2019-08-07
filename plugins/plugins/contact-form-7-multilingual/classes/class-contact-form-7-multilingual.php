<?php

class Contact_Form_7_Multilingual {

	/**
	 * Adds the required hooks.
	 */
	public function init_hooks() {
		add_filter( 'shortcode_atts_wpcf7', array( $this, 'translate_shortcode_form_id' ) );
		add_filter( 'icl_job_elements', array( $this, 'remove_body_from_translation_job' ), 10, 2 );

		add_action( 'save_post', array( $this, 'fix_setting_language_information' ) );
	}

	/**
	 * Translate the `id` in the shortcode attributes on-the-fly.
	 *
	 * @param array $out Shortcode attributes to be filtered.
	 *
	 * @return array
	 */
	public function translate_shortcode_form_id( $out ) {
		$out['id'] = apply_filters( 'wpml_object_id', $out['id'], 'wpcf7_contact_form', true );

		return $out;
	}

	/**
	 * Don't translate the post_content of contact forms.
	 *
	 * @param array $elements Translation job elements.
	 * @param int   $post_id  The post ID.
	 *
	 * @return array
	 */
	public function remove_body_from_translation_job( $elements, $post_id ) {
		// Bail out early if its not a CF7 form.
		if ( 'wpcf7_contact_form' !== get_post_type( $post_id ) ) {
			return $elements;
		}

		$field_types = wp_list_pluck( $elements, 'field_type' );
		$index       = array_search( 'body', $field_types, true );
		if ( false !== $index ) {
			$elements[ $index ]->field_data            = '';
			$elements[ $index ]->field_data_translated = '';
		}

		return $elements;
	}

	/**
	 * CF7 sets post_ID to -1 for new forms.
	 * WPML thinks we are saving a different post and doesn't save language information.
	 * Removing it fixes the misunderstanding.
	 */
	public function fix_setting_language_information() {
		if ( empty( $_POST['_wpnonce'] ) || empty( $_POST['post_ID'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpcf7-save-contact-form_' . $_POST['post_ID'] ) ) {
			return;
		}

		if ( -1 === (int) $_POST['post_ID'] ) {
			unset( $_POST['post_ID'] );
		}
	}

}
