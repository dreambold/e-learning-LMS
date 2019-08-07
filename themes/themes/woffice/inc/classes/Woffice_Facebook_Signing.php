<?php

if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

if( ! class_exists( 'Woffice_Facebook_Signing' ) ) {
    /**
     * Class Woffice_Facebook_Signing
     * Signing Up and Signing In with Facebook in Woffice
     *
     * @package Woffice
     * @category Theme
     * @since 2.5.2
     * @author Alkalab
     */
    class Woffice_Facebook_Signing
    {

        /**
         * Facebook access token from the API
         *
         * @var string
         */
        private $access_token = '';

	    /**
	     * @var Facebook|null
	     */
        private static $fb = null;

        /**
         * Facebook user details Array
         *
         * @var array
         */
        private $facebook_details;

        /**
         * Constructor
         */
        public function __construct()
        {

            // Ajax Registration
            add_action( 'wp_ajax_woffice_facebook', array($this, 'apiCallback'));
            add_action( 'wp_ajax_nopriv_woffice_facebook', array($this, 'apiCallback'));

        }

        /**
         * Check whether the sign up / in with Facebook is allowed
         *
         * @return bool
         */
        static function isEnabled() {

            $facebook_enabled = woffice_get_settings_option('facebook_enabled', 'nope');

            $facebook_app_id = woffice_get_settings_option('facebook_app_id');
            $facebook_app_secret = woffice_get_settings_option('facebook_app_secret');

            if($facebook_enabled === 'yep' && !empty($facebook_app_id) && !empty($facebook_app_secret))
                return true;

            return false;

        }

        /**
         * Returns the site Facebook API Callback URL
         *
         * @return string
         */
        static function getCallbackUrl() {

            return admin_url( 'admin-ajax.php?action=woffice_facebook' );

        }

        /**
         * Login URL to Facebook API
         *
         * @return string
         */
        static function getLoginUrl() {

            if(!session_id()) {
                session_start();
            }


            $helper = self::fb()->getRedirectLoginHelper();

            // Optional permissions
            $permissions = ['email'];

            $url = $helper->getLoginUrl(self::getCallbackUrl(), $permissions);

            return esc_url($url);

        }

        /**
         * Init the Api Connection
         *
         * @throws
         *
         * @return void
         */
        static function initApi() {

            $facebook = new Facebook([
                'app_id' => woffice_get_settings_option('facebook_app_id'),
                'app_secret' => woffice_get_settings_option('facebook_app_secret'),
                'default_graph_version' => 'v2.2',
                'persistent_data_handler' => 'session'
            ]);

            self::$fb = $facebook;
        }

	    /**
	     * Returns Facebook instance
	     *
	     * @return Facebook
	     */
        static private function fb() {

        	if( is_null( self::$fb ) ) {
        		self::initApi();
	        }

	        return self::$fb;
        }

        /**
         * Get user details through the Facebook API
         *
         * @link https://developers.facebook.com/docs/facebook-login/permissions#reference-public_profile
         * @return \Facebook\GraphNodes\GraphUser
         */
        private function getUserDetails()
        {

            try {
                $response = self::fb()->get('/me?fields=id,name,first_name,last_name,email,link', $this->access_token);
            } catch(FacebookResponseException $e) {
                $message = esc_html__('Graph returned an error: ','woffice'). $e->getMessage();
                Woffice_Alert::create()->setType('error')->setContent($message)->queue();
	            exit;
            } catch(FacebookSDKException $e) {
                $message = esc_html__('Facebook SDK returned an error: ','woffice'). $e->getMessage();
                Woffice_Alert::create()->setType('error')->setContent($message)->queue();
	            exit;
            }

            return $response->getGraphUser();

        }

	    /**
	     * Get user avatar through the Facebook API
	     *
	     * @link https://developers.facebook.com/docs/facebook-login/permissions#reference-public_profile
	     * @return \Facebook\GraphNodes\GraphNode
	     */
	    private function getUserAvatar( $fb_user_id )
	    {

		    if( ! function_exists( 'bp_core_avatar_handle_upload' ) ) {
				return null;
		    }

		    try {
			    // Returns a `FacebookFacebookResponse` object
			    $response = self::fb()->get(
				    '/' . $fb_user_id . '/picture?redirect=false&height=256&width=256',
				    $this->access_token
			    );
		    } catch(FacebookResponseException $e) {
			    echo 'Graph returned an error: ' . $e->getMessage();
			    exit;
		    } catch(FacebookSDKException $e) {
			    echo 'Facebook SDK returned an error: ' . $e->getMessage();
			    exit;
		    }

		    $avatar = $response->getGraphNode();

		    return $avatar['url'];

	    }

        /**
         * Callback for the facebook API call
         */
        public function apiCallback() {

            if(!self::isEnabled())
                wp_die();

            if(!session_id()) {
                session_start();
            }

            // Load the helper
            $helper = $this->fb()->getRedirectLoginHelper();
            $_SESSION['FBRLH_state'] = $_GET['state'];

            // Try to get access
            try {
                $accessToken = $helper->getAccessToken();
            }
                // When Graph returns an error
            catch(FacebookResponseException $e) {
                $message = esc_html__('Graph returned an error: ','woffice'). $e->getMessage();
                Woffice_Alert::create()->setType('error')->setContent($message)->queue();
            }
                // When validation fails or other local issues
            catch(FacebookSDKException $e) {
                $message = esc_html__('Facebook SDK returned an error: ','woffice'). $e->getMessage();
                Woffice_Alert::create()->setType('error')->setContent($message)->queue();
            }

            // If we got nothing
            if (!isset($accessToken)) {
                header("Location: ". esc_url(home_url('/')), true);
                die();
            }

            // We save the token in our instance
            $this->access_token = $accessToken->getValue();

            $this->facebook_details = $this->getUserDetails();

            $user = $this->fetchUser( $this->facebook_details );

            // If the user exists, log in him, otherwise create it
            if( $user instanceof \WP_User) {
                $this->loginUser( $user );
            } else {
                $this->createUser();
            }

	        header('Location: ' . woffice_get_redirect_page_after_login());
	        exit();

        }

        /**
         * Login an user to WordPress
         *
         * @param \WP_User $user
         *
         * @return bool|void
         */
        private function loginUser( \WP_User $user ) {

            // Log the user
            wp_set_auth_cookie( $user->ID );

            // We add an  alert
            Woffice_Alert::create()->setType('success')->setContent(__('Welcome back', 'woffice') .' '. woffice_get_name_to_display($user->ID) .'!')->queue();

        }

        /**
         * Create a new WordPress account using Facebook Details and redirect once done
         */
        private function createUser() {

            $fb_user = $this->facebook_details;

	        // If the registration is closed or it's open but email check doesn't match
	        if(!get_option( 'users_can_register' ) || !Woffice_Register::isEmailAllowed($fb_user['email'])) {
		        woffice_redirect_to_login( 'login=social_unauthorized' );
		        return;
	        }

            // Create an username
            $username = sanitize_user(str_replace(' ', '_', strtolower($this->facebook_details['name'])));
            $email = sanitize_email($fb_user['email']);

            // If there isn't any name, use the alias of the email
            if( empty($username) ) {
                $email_exploded = explode( '@', $email );
                $username = $email_exploded[0];
            }

            // Creating our user
            $new_user_id = wp_create_user($username, wp_generate_password(), $email);

            if(!is_int($new_user_id)) {
                return;
            }

            // Setting the meta
            update_user_meta( $new_user_id, 'first_name', $fb_user['first_name'] );
            update_user_meta( $new_user_id, 'last_name', $fb_user['last_name'] );
            update_user_meta( $new_user_id, 'user_url', $fb_user['link'] );
            update_user_meta( $new_user_id, 'woffice_facebook_id', $fb_user['id'] );

	        $avatar_url = $this->getUserAvatar( $fb_user['id'] );

	        if( $avatar_url ) {
		        update_user_meta( $new_user_id, 'woffice_social_avatar', $avatar_url );
	        }

            /**
             * Action `woffice_facebook_after_signup`
             *
             * @param int $new_user_id
             */
            do_action('woffice_facebook_after_signup', $new_user_id);

            // Log the user?
            wp_set_auth_cookie( $new_user_id );
            // We add an  alert
            Woffice_Alert::create()->setType('success')->setContent(__('Welcome', 'woffice') .' '. woffice_get_name_to_display($new_user_id) .'!')->queue();

        }

        /**
         * Using the facebook details, get the corresponding user into the db
         *
         * @param array $facebook_details
         *
         * @return false|\WP_User
         */
        private function fetchUser( $facebook_details ) {

            // We look for the `woffice_facebook_id` to see if there is any match
            $wp_users = get_users(array(
                'meta_key'     => 'woffice_facebook_id',
                'meta_value'   => $facebook_details['id'],
                'number'       => 1,
                'count_total'  => false,
            ));

            if(empty($wp_users[0])) {
                $wp_user = get_user_by( 'email', $facebook_details['email'] );
            } else
                $wp_user = $wp_users[0];

            // If user exists, add the facebook id, for the future fetches
            if( $wp_user instanceof \WP_User ) {
                add_user_meta( $wp_user->ID, 'woffice_facebook_id', $facebook_details['id'] );
                return $wp_user;
            }

            //if user doesn't exists
            return false;
        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Facebook_Signing();