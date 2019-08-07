<?php
/**
 * TGM Plugin Activation file
 */

if (!function_exists('woffice_core_bundled_plugin')) {
	/**
	 * Returns Woffice plugin information
	 *
	 * @param $slug
	 * @param string $key
	 *
	 * @return array|string
	 */
	function woffice_core_bundled_plugin( $slug, $key = '' ) {
		$base_url = 'http://hub.alka-web.com/storage/woffice/plugins/n1a9x/';
		$info     = array();

		switch ( $slug ) {

			case 'woffice-core':
				$info = array(
					'name'               => 'Woffice Core',
					'slug'               => 'woffice-core',
					'source'             => get_template_directory() . '/inc/plugins/woffice-core.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => true,
					'version'            => (defined('FW')) ? fw()->theme->manifest->get('version') : '0',
				);
				break;
			case 'unyson':
				$info = array(
					'name'             => 'Unyson',
					'slug'             => 'unyson',
					'force_activation' => false,
					'required'         => true,
				);
				break;
			case 'buddypress':
				$info = array(
					'name'             => 'Buddypress',
					'slug'             => 'buddypress',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'wise-chat':
				$info = array(
					'name'             => 'Wise chat',
					'slug'             => 'wise-chat',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'revslider':
				$info = array(
					'name'               => 'Revolution Slider', 
					'slug'               => 'revslider', 
					'source'             => $base_url . 'revslider.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '5.4.8.3',
				);
				break;
			case 'dpProEventCalendar':
				$info = array(
					'name'               => 'Pro Event Calendar', 
					'slug'               => 'dpProEventCalendar', 
					'source'             => $base_url . 'dpProEventCalendar.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '3.0.5',
				);
				break;
			case 'js_composer':
				$info = array(
					'name'               => 'WPBakery Page builder', 
					'slug'               => 'js_composer', 
					'source'             => $base_url . 'js_composer.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '6.0.3',
				);
				break;
			case 'vc-super-bundle':
				$info = array(
					'name'               => 'Super Bundle for WPBakery Page Builder', 
					'slug'               => 'vc-super-bundle', 
					'source'             => $base_url . 'vc-super-bundle.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '1.4.1',
				);
				break;
			case 'file-away':
				$info = array(
					'name'             => 'File Away',
					'slug'             => 'file-away',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'erp':
				$info = array(
					'name'             => 'WP ERP',
					'slug'             => 'erp',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'gamipress':
				$info = array(
					'name'             => 'GamiPress',
					'slug'             => 'gamipress',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'contact-form-7':
				$info = array(
					'name'             => 'Contact Form 7',
					'slug'             => 'contact-form-7',
					'force_activation' => false,
					'required'         => false,
				);
				break;
			case 'eventON':
				$info = array(
					'name'               => 'EventOn Calendar (Depreciated, try Pro Event above)', 
					'slug'               => 'eventON', 
					'source'             => $base_url . 'eventON.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '2.7.1',
				);
				break;
			case 'eventon-full-cal':
				$info = array(
					'name'               => 'EventOn Asset (Full Calendar ADDON)', 
					'slug'               => 'eventon-full-cal', 
					'source'             => $base_url . 'eventon-full-cal.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
					'version'            => '1.1.6',
				);
				break;
			case 'multiverso':
				$info = array(
					'name'               => 'Multiverso file manager', 
					'slug'               => 'multiverso', 
					'source'             => $base_url . 'multiverso.zip',
					'force_activation'   => false,
					'force_deactivation' => false,
					'required'           => false,
				);
				break;
		}

		if ( isset( $info['source'] ) && strpos( $info['source'], $base_url ) !== false ) {
			$woffice_key = get_option( 'woffice_key' );
			$woffice_key = ( ! empty( $woffice_key ) ) ? $woffice_key : 'N/A';

			$info['source'] = $info['source'] . '?woffice_key=' . $woffice_key;
		}

		if ( ! empty( $key ) ) {
			return $info['key'];
		}

		return $info;
	}
}

if ( !is_multisite() ) {

    require_once dirname( __FILE__ ) . '/plugins/TGM_Plugin_Activation.php';

    /**
     * INSTALL PLUGINS WITH TGM PLUGIN ACTIVATION
     */
    function _action_theme_register_required_plugins()
    {
        tgmpa(array(
	        woffice_core_bundled_plugin('woffice-core'),
	        woffice_core_bundled_plugin('unyson'),
	        woffice_core_bundled_plugin('buddypress'),
	        woffice_core_bundled_plugin('revslider'),
	        woffice_core_bundled_plugin('js_composer'),
	        woffice_core_bundled_plugin('buddypress'),
	        woffice_core_bundled_plugin('wise-chat'),
	        woffice_core_bundled_plugin('vc-super-bundle'),
	        woffice_core_bundled_plugin('file-away'),

			// woffice_core_bundled_plugin('dpProEventCalendar'),
	        // woffice_core_bundled_plugin('gamipress'),
	        // woffice_core_bundled_plugin('erp'),
	        // woffice_core_bundled_plugin('eonet-live-notifications'),
        ));

    }
    add_action( 'tgmpa_register', '_action_theme_register_required_plugins' );

}