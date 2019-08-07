<?php

/**
 * Checks if requested form language is matching requested language
 * and provides redirection URL if necessary.
 */
class WPML_Compatibility_MailChimp_Redirection {

	/**
	 * @var array Filtered WPML active languages
	 */
	private $active_languages;
	/**
	 * @var int MailChimp form ID.
	 */
	private $form_id;
	/**
	 * @var string
	 */
	private $requested_language;

	/**
	 * @param int    $form_id            MailChimp form ID.
	 * @param string $requested_language Requested language code.
	 */
	function __construct( $form_id, $requested_language ) {

		$this->form_id            = $form_id;
		$this->requested_language = $requested_language;
		$this->active_languages   = apply_filters( 'wpml_active_languages', array() );
	}

	public function add_hooks() {
		if ( array_key_exists( 'wpml_warning_missing', $_GET ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_translation' ) );
		} elseif ( array_key_exists( 'wpml_warning_duplicate', $_GET ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_duplicated_form' ) );
		}
	}

	public function admin_notice_duplicated_form() {
		$this->render_admin_notice( $_GET['wpml_warning_duplicate'], __( 'WPML Translation Management > MailChimp Form in %s is duplicated, please translate it separately.', 'wpml-mailchimp-for-wp' ) );
	}

	public function admin_notice_missing_translation() {
		$this->render_admin_notice( $_GET['wpml_warning_missing'], __( 'WPML Translation Management > Please translate MailChimp Form to %s.', 'wpml-mailchimp-for-wp' ) );
	}

	private function render_admin_notice( $language_code, $message ) {
		if ( $this->is_language_code_valid( $language_code ) ) {
			$requested_language = apply_filters( 'wpml_translated_language_name', false, $language_code );
			echo '<div class="notice notice-error"><p>' . sprintf( $message, $requested_language ) . '</p></div>';
		}
	}

	/**
	 * Checks if:
	 * - requested language is valid (redirects to original form)
	 * - form language matches requested language (redirects to translated form)
	 * - translated form exist (redirects to original form with warning)
	 * - form is duplicated (redirects to original form with warning message)
	 *
	 * @return string Redirection URL or empty string if redirection is not needed.
	 */
	public function get_redirection_url() {

		$form_language   = $this->get_form_language( $this->form_id );
		$duplicated_from = apply_filters( 'wpml_master_post_from_duplicate', $this->form_id );

		if ( $duplicated_from ) {
			return $this->get_form_url( $duplicated_from, array( 'wpml_warning_duplicate' => $form_language ) );
		}

		if ( ! $this->is_language_code_valid( $this->requested_language ) ) {
			return $this->get_form_url( $this->form_id );
		}

		if ( $this->requested_language !== $form_language ) {

			$translated_form_id = apply_filters( 'wpml_object_id', $this->form_id, 'mc4wp-form', false, $this->requested_language );

			if ( $translated_form_id ) {

				$duplicated_from = apply_filters( 'wpml_master_post_from_duplicate', $translated_form_id );

				if ( $duplicated_from ) {

					$translated_form_language = $this->get_form_language( $translated_form_id );

					return $this->get_form_url( $duplicated_from,
					                            array(
						                            'wpml_warning_duplicate' => $translated_form_language
					                            ) );
				}

				return $this->get_form_url( $translated_form_id );
			}

			return $this->get_form_url( $this->form_id,
			                            array(
				                            'wpml_warning_missing' => $this->requested_language
			                            ) );
		}

		return '';
	}

	/**
	 * @param int $form_id MailChimp form ID.
	 *
	 * @return string Language code or empty string.
	 */
	private function get_form_language( $form_id ) {
		return apply_filters( 'wpml_element_language_code',
		                      '',
		                      array(
			                      'element_id'   => $form_id,
			                      'element_type' => 'post_mc4wp-form'
		                      ) );
	}

	/**
	 * @param int        $form_id  MailChimp form ID.
	 * @param array|null $warnings Warning data for URL query.
	 *
	 * @return string URL or empty string if Language Resolution failed.
	 */
	private function get_form_url( $form_id, array $warnings = null ) {

		$url           = '';
		$language_code = $this->get_form_language( $form_id );

		if ( $this->is_language_code_valid( $language_code ) ) {
			$url = $this->adjust_url( $form_id, $language_code, (array) $warnings );
		} else {
			$default_form_id       = (int) get_option( 'mc4wp_default_form_id', 0 );
			$default_form_language = $this->get_form_language( $default_form_id );
			if ( $this->is_language_code_valid( $default_form_language ) ) {
				$url = $this->adjust_url( $default_form_id, $default_form_language, (array) $warnings );
			}
		}

		return $url;
	}

	/**
	 * @param string $language_code Language code.
	 *
	 * @return bool True if language code key is found in active languages.
	 */
	private function is_language_code_valid( $language_code ) {
		return is_string( $language_code ) ? array_key_exists( $language_code, $this->active_languages ) : false;
	}

	/**
	 * @param int    $form_id  MailChimp form ID.
	 * @param string $language Language code.
	 * @param array  $query    URL query parameters.
	 *
	 * @return string Sanitized URL.
	 */
	private function adjust_url( $form_id, $language, array $query ) {

		$query['page']      = 'mailchimp-for-wp-forms';
		$query['view']      = 'edit-form';
		$query['admin_bar'] = 1;
		$query['form_id']   = $form_id;
		$query['lang']      = $language;

		$url_parsed          = array();
		$url_parsed['query'] = http_build_query( $query );

		$url = http_build_url( get_admin_url( null, 'admin.php' ), $url_parsed );

		return esc_url( $url, null, 'redirect' );
	}
}