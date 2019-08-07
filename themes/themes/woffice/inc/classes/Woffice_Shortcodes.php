<?php
if( ! class_exists( 'Woffice_Shortcodes' ) ) {
	/**
	 * Class Woffice_Shortcodes
	 *
	 * Declare the shortcodes and handle the VC Mapping.
	 *
	 * @since 2.5.4
	 *
	 */
	class Woffice_Shortcodes{

		/**
		 * @var null|Woffice_Shortcodes The unique instance of the class
		 */
		protected static $instance = null;

		/**
		 * @var array The names of all the shortcodes
		 */
		public $shortcodes = array(
			'vc_assigned_tasks',
			'vc_bigbluebutton',
			'vc_file_away',
			'vc_eventon',
			'vc_dp_pro_event_calendar',
			'vc_frontend_creation',
			'vc_members',
			'vc_learndash',
			'vc_trello'
		);

		/**
		 * Woffice_Shortcodes constructor
		 */
		protected function __construct()
		{

			// If VC is active, override the templates folder
			if( function_exists( 'vc_set_shortcodes_templates_dir' ) )
				vc_set_shortcodes_templates_dir( get_template_directory() . '/inc/visual_composer/shortcodes' );


			add_action( 'vc_before_init', array( $this, 'integrateWithVC') );
		}

		/**
		 * Return the unique instance of the class
		 *
		 * @return Woffice_Shortcodes|null
		 */
		public static function instance() {
			if(is_null(static::$instance))
				static::$instance = new static();

			return static::$instance;
		}

		/**
		 * Map the shortcodes to convert them in WPBakery Page builder Elements
		 */
		public function integrateWithVC() {


			$mapping_shortcodes = array();

			foreach( $this->shortcodes as $shortcode ) {

				$attributes = include (get_template_directory() . '/inc/visual_composer/configs/' . $shortcode . '.php');

				vc_map( $attributes );

			}


		}

		/**
		 * Parse the attribute of the shortcodes with the default values and return the right, formatted values
		 *
		 * @param string $shortcode_name
		 * @param array $atts
		 * @param array $default
		 *
		 * @return array
		 */
		public static function getShortcodeAttributes( $shortcode_name, $atts, $default = array() ) {

			$default['additional_class'] = '';
			$default['css'] = '';

			// Attributes
			$css_class = '';
			if(function_exists('vc_map_get_attributes')) {
				$atts = vc_map_get_attributes( $shortcode_name, $atts );
				$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $atts );
			} else {
				$atts = shortcode_atts(
					$default,
					$atts,
					$shortcode_name . '_filter'
				);
			}

			$atts['additional_class'] .=  $css_class;

			return $atts;
		}

		/**
		 * Add the parameters additional_class and css to each shortcode to map with VC
		 *
		 * @param $shortcode
		 *
		 */
		public static function addStandardVcParameters( &$shortcode ) {

			$shortcode['params'][] = array(
				'type' => 'el_id',
				'heading' => __( 'Element ID', 'js_composer' ),
				'param_name' => 'el_id',
				'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
			);
			$shortcode['params'][] = array(
				'type' => 'textfield',
				'heading' => __( 'Extra class name', 'js_composer' ),
				'param_name' => 'el_class',
				'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			);
			$shortcode['params'][] = array(
				'type' => 'css_editor',
				'heading' => __( 'CSS box', 'js_composer' ),
				'param_name' => 'css',
				'group' => __( 'Design Options', 'js_composer' ),
			);


		}

	}
}

if(!function_exists('woffice_vc_schortcodes')) {
	/**
	 * Return the unique instance of the class
	 *
	 * @return Woffice_Shortcodes
	 */
	function woffice_vc_schortcodes() {

		return Woffice_Shortcodes::instance();
	}
}
/**
 * Let's fire it :
 */
add_action('after_setup_theme', 'woffice_vc_schortcodes');

if(class_exists('WPBakeryShortCode')) {

	class WPBakeryShortCode_vc_assigned_tasks extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_bigbluebutton extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_file_away extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_eventon extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_dp_pro_event_calendar extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_frontend_creation extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_members extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_learndash extends WPBakeryShortCode { }

	class WPBakeryShortCode_vc_trello extends WPBakeryShortCode { }

}