<?php

/**
 * Adds and applies shortcode attribute filter.
 */
class WPML_Compatibility_MailChimp_Shortcode_Attributes_Filter {

	public function add_hooks() {
		add_filter( 'shortcode_atts_mc4wp_form', array( $this, 'apply_filters' ) );
	}

	/**
	 * @param array $attributes Shortcode attributes.
	 *
	 * @return array $attributes Filtered shortcode attributes.
	 */
	public function apply_filters( $attributes ) {

		if ( isset( $attributes['id'] ) ) {
			$attributes['id'] = apply_filters( 'wpml_object_id', $attributes['id'], 'mc4wp-form', true );
		}

		return $attributes;
	}
}