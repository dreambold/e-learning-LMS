<?php
/**
 * Adds hooks and redirects to correct screen if necessary.
 */
class WPML_Compatibility_MailChimp {

	/**
	 * Queues hooks for 'init' action.
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'add_init_hooks' ), 100 );
	}

	/**
	 * Adds hooks and checks form language.
	 *
	 * If MailChimp is active, adds hooks to ensure translated content is loaded.
	 * Checks if requested form language is matching requested language
	 * and redirects to correct screen if necessary.
	 */
	public function add_init_hooks() {
		if ( defined( 'MC4WP_VERSION' )
		     && version_compare( MC4WP_VERSION, '4.1.9', '>=' )
		     && apply_filters( 'wpml_setting', false, 'setup_complete' )
		) {

			if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'mailchimp-for-wp-forms' ) {
				$save_action = array_key_exists( '_mc4wp_action', $_POST ) ? $_POST['_mc4wp_action'] : null;
				$view        = array_key_exists( 'view', $_GET ) ? $_GET['view'] : null;

				if ( $save_action === 'edit_form' ) {

					add_filter( 'parse_query', array( $this, 'parse_query_filter' ) );

				} elseif ( $view === 'edit-form' ) {

					$form_id = (int) get_option( 'mc4wp_default_form_id', 0 );
					if ( array_key_exists( 'form_id', $_GET ) ) {
						$requested_form_id = filter_var( $_GET['form_id'], FILTER_VALIDATE_INT );
						if ( get_post_type( $requested_form_id ) === 'mc4wp-form' ) {
							$form_id = $requested_form_id;
						}
					}

					if ( $form_id ) {

						$requested_language = apply_filters( 'wpml_current_language', null );
						if ( array_key_exists( 'lang', $_GET ) ) {
							$requested_language = $_GET['lang'];
						}

						$redirection = new WPML_Compatibility_MailChimp_Redirection( $form_id, $requested_language );
						$redirection->add_hooks();

						$url = $redirection->get_redirection_url();
						if ( $url ) {
							// @codeCoverageIgnoreStart
							wp_safe_redirect( $url );
							exit;
							// @codeCoverageIgnoreEnd
						}
					}
				}
			} elseif ( ! is_admin() ) {
				$attribute_filter = new WPML_Compatibility_MailChimp_Shortcode_Attributes_Filter();
				$attribute_filter->add_hooks();
			}
		}

		if ( ( defined( 'MC4WP_VERSION' ) || defined( 'MC4WP_PREMIUM_VERSION' ) )
		     && ! is_admin()
		     && apply_filters( 'wpml_setting', false, 'setup_complete' )
		) {
			$widget_filter = new WPML_Compatibility_MailChimp_Widget_Filter();
			$widget_filter->add_hooks();
		}
	}

	/**
	 * Filters wp_query to ensure translated form is found.
	 *
	 * @param object $query WP_Query object.
	 *
	 * @return object $query WP_Query object.
	 */
	public function parse_query_filter( $query ) {
		$query->query_vars['suppress_filters'] = false;

		return $query;
	}
}