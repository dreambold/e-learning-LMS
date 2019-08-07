<?php

/**
 * Filters MailChimp widget.
 */
class WPML_Compatibility_MailChimp_Widget_Filter {

	/**
	 * Adds widget hooks.
	 */
	public function add_hooks() {
		add_filter( 'widget_display_callback', array( $this, 'widget_display_callback_filter' ), 10, 2 );
	}

	/**
	 * Filters widget instance.
	 *
	 * @param array $instance Widget instance.
	 * @param object $widget WP Widget class object.
	 *
	 * @return array Filtered widget instance.
	 */
	public function widget_display_callback_filter( $instance, $widget ) {

		if ( is_array( $instance ) && isset( $widget->id_base ) && $widget->id_base == 'mc4wp_form_widget' ) {

			$form_id = array_key_exists( 'form_id', $instance ) ? $instance['form_id'] : (int) get_option( 'mc4wp_default_form_id', 0 );

			if ( $form_id ) {
				$instance['form_id'] = apply_filters( 'wpml_object_id', $form_id, 'mc4wp-form', true );
			}
		}

		return $instance;
	}
}