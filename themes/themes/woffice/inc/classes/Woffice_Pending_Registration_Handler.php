<?php
/**
 * Class Woffice_Pending_Registration_Handler
 *
 * Handle the compatibility with the plugins Eonet Manual User Approve and New User Approve
 *
 * @since 2.1.3
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_Pending_Registration_Handler' ) ) {
	class Woffice_Pending_Registration_Handler {

		/**
		 * @var Woffice_Pending_Registration_Handler The only instance of the class
		 */
		private static $instance = null;

        /**
         * Woffice_Pending_Registration_Handler constructor.
         */
		private function __construct() {

			//New User Approve Compatibility
			add_filter('new_user_approve_default_authentication_message', array($this,'redirect_pending_users'), 10, 2);
			add_filter('new_user_approve_bypass_password_reset', '__return_true',10,0);

			//Eonet Manual User Approve
			add_action('eonet_mua_before_check_status_on_login', array($this,'redirect_pending_users_mua'));
			add_filter('eonet_mua_approved_access_url', array($this,'approved_access_url'));
			add_action('user_register', array($this,'change_email_notification_behavior'), 100);
		}

        /**
         * Get the class instance
         *
         * @return Woffice_Pending_Registration_Handler
         */
		public static function instance() {
			if(is_null(static::$instance))
				static::$instance = new static();

			return static::$instance;
		}

		/**
		 * Redirect to the login page with error pending_approval
		 * This function is used if is used the plugin New User Approve, instead of Eonet Manual User Approve
		 *
		 * @param $message
		 * @param $status
		 */
		public function redirect_pending_users( $message, $status ) {
			if ( $status == 'pending' ) {
				woffice_redirect_to_login( 'login=pending_approval' );
				exit;
			}
		}

		/**
		 * Redirect to the login page with error pending_approval or denied_approval
		 * This function is used if is used the plugin Eonet Manual User Approve
		 *
		 * @param $status
		 */
		public function redirect_pending_users_mua( $status ) {
			if ( $status == \ComponentManualUserApprove\classes\Eonet_MUA_UserManager::PENDING ) {
				woffice_redirect_to_login( 'login=pending_approval' );
				exit;
			} else if ( $status == \ComponentManualUserApprove\classes\Eonet_MUA_UserManager::DENIED ) {
				woffice_redirect_to_login( 'login=denied_approval' );
				exit;
			}
		}

        /**
         * Approved Access URL
         *
         * @return string
         */
		public function approved_access_url() {
			return get_home_url( '/login/' );
		}

		/**
		 * Change when the email to alert admin and user about registration are sent
		 */
		public function change_email_notification_behavior() {

			if( ! woffice_is_custom_login_page_enabled() || !function_exists('eonet_manual_user_approve')) {
				return;
			}

			//$eonet_mua = new \ComponentManualUserApprove\EonetManualUserApprove();
			$eonet_manual_user_approve = eonet_manual_user_approve();

			remove_action( 'user_register', 'woffice_send_new_user_notifications' );
			add_action( 'user_register', array( $eonet_manual_user_approve, 'send_request_notification_to_admin' ) );
			add_action( 'user_register', array( $eonet_manual_user_approve, 'set_user_status' ) );
		}
	}
}
/**
 * Let's fire it!
 */
Woffice_Pending_Registration_Handler::instance();