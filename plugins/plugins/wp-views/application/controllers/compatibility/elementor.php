<?php

namespace OTGS\Toolset\Views\Controller\Compatibility;

/**
 * Class Elementor
 *
 * Handles the compatibility between Views and Elementor.
 *
 * @package OTGS\Toolset\Views\Controller\Compatibility
 *
 * @since 2.7.0
 */
class Elementor extends Base {
	/**
	 * @var \ElementorPro\Modules\ThemeBuilder\Module
	 */
	private $elementor_theme_builder_module;

	public function initialize() {
		$this->init_hooks();
	}

	private function init_hooks() {
		add_filter( 'elementor/frontend/builder_content_data', array( $this, 'maybe_post_or_post_body_shortcode_uses_content_template' ), 10, 2 );
	}

	/**
	 * Callback for the "elementor/frontend/builder_content_data" filter that is applied when the content build with the
	 * Elementor builder is retrieved.
	 *
	 * The filter is applied upon the sequence of widget for the current post. Here we are checking if either the post uses
	 * a Content Template or the "wpv-post-body" shortcode is rendered using a Content Template. For each of the before
	 * mentioned cases we are returning an empty widget sequence (empty Elementor builder content) and for all the other
	 * cases we are returning the saved sequence of widgets.
	 *
	 * @param $data
	 * @param $post_id
	 *
	 * @return array
	 */
	public function maybe_post_or_post_body_shortcode_uses_content_template( $data, $post_id ) {
		// Check if the currently rendered template is a "Header" or a "Footer" template. In this case the rendering
		// of the template should not be affected.
		if ( $this->is_header_or_footer_template() ) {
			return $data;
		}

		global $post;

		if ( null === $post ) {
			return $data;
		}

		$view_template_post_meta = get_post_meta( $post->ID, '_views_template', true );

		// The currently rendered post uses the "wpv-post-body" (either in a Content Template assigned to it or in an
		// inline Content Template of a View that list this among other posts).
		if ( isset( $post->view_template_override ) ) {
			// The "wpv-post-body" shortcode uses a Content Template as an override so it needs to be determined whether
			// the Content Template exists or not. If it does, the Elementor builder designed content must be skipped
			// and the content of the post using the Content Template should be displayed instead...
			if ( 'none' !== strtolower( $post->view_template_override ) ) {
				// ... get the Content Template ID of the template with name equals to the template override used as a
				// shortcode attribute....
				$ct_id = apply_filters( 'wpv_get_template_id_by_name', 0, $post->view_template_override );

				// If the Content Template exists, the Elementor builder designed content must be skipped and the content
				// of the post using the Content Template should be displayed instead.
				if ( 0 !== $ct_id ) {
					return array();
				}
			}

			return $data;
		}

		// If the post has a Content Template assigned to it, skip the Elementor builder designed content and display
		// the content of the post using the Content Template.
		if ( (bool) $view_template_post_meta ) {
			return array();
		}

		return $data;
	}

	/**
	 * Checks if the currently rendered Elementor Template is a "Header" or a "Footer" template. In this case the rendering
	 * of the template should not be affected.
	 *
	 * @return bool
	 *
	 * @since 2.6.4.1
	 */
	private function is_header_or_footer_template() {
		if (
			null === $this->elementor_theme_builder_module && // If the dependency is not injected and...
			! class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) && // the \ElementorPro\Modules\ThemeBuilder\Module doesn't exist and...
			! is_callable( array( '\ElementorPro\Modules\ThemeBuilder\Module', 'instance' ) ) && // the "instance" method is not callable and...
			! is_callable( array( '\ElementorPro\Modules\ThemeBuilder\Module', 'get_locations_manager' ) ) // the "get_locations_manager" method is not callable and...
		) {
			// this means that we are not unit testing the current class and that Elementor PRO is not activated, or the classes
			// for this job have changed.
			return false;
		}

		$this->elementor_theme_builder_module = null !== $this->elementor_theme_builder_module ? $this->elementor_theme_builder_module : \ElementorPro\Modules\ThemeBuilder\Module::instance();

		$current_location = $this->elementor_theme_builder_module->get_locations_manager()->get_current_location();
		if ( in_array( $current_location, array( 'header', 'footer' ), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Used only for unit testing purposes to inject the dependency, as the dependency cannot be injected in the class
	 * constructor basically because it's not ready at that point
	 *
	 * @param \ElementorPro\Modules\ThemeBuilder\Module|null $elementor_theme_builder_module
	 */
	public function set_elementor_theme_builder_module_dependency( \ElementorPro\Modules\ThemeBuilder\Module $elementor_theme_builder_module = null ) {
		$this->elementor_theme_builder_module = $elementor_theme_builder_module
			? $elementor_theme_builder_module
			: \ElementorPro\Modules\ThemeBuilder\Module::instance();
	}
}
