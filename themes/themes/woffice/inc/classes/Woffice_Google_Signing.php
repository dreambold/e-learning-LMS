<?php

if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if( ! class_exists( 'Woffice_Google_Signing' ) ) {
    /**
     * Class Woffice_Google_Signing
     * Signing Up and Signing In with Google in Woffice
     *
     * @package Woffice
     * @category Theme
     * @since 2.5.2
     * @author Alkalab Team
     */
    class Woffice_Google_Signing
    {

        /**
         * Google access token from the API
         *
         * @var string
         */
        private $access_token = '';

        /**
         * Google user details Array
         *
         * @var array
         */
        private $google_details;

        /**
         * Constructor
         */
        public function __construct()
        {

            // Ajax Registration
            add_action( 'wp_ajax_woffice_google', array($this, 'apiCallback'));
            add_action( 'wp_ajax_nopriv_woffice_google', array($this, 'apiCallback'));

        }

        /**
         * Check whether the sign up / in with Google is allowed
         *
         * @return bool
         */
        static function isEnabled() {

            $google_enabled = woffice_get_settings_option('google_enabled', 'nope');
            $google_app_id = woffice_get_settings_option('google_app_id');
            $google_app_secret = woffice_get_settings_option('google_app_secret');

            if($google_enabled === 'yep' && !empty($google_app_id) && !empty($google_app_secret))
                return true;

            return false;

        }

        /**
         * Returns the site Google API Callback URL
         *
         * @return string
         */
        static function getCallbackUrl() {

            return admin_url( 'admin-ajax.php?action=woffice_google' );

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

            $gClient = self::initApi();

            $url = $gClient->createAuthUrl();

            return esc_url($url);

        }

        /**
         * Init the Api Connection
         *
         * @return Google_Client
         */
        static function initApi() {

            $gClient = new Google_Client();
            $gClient->setApplicationName(__("Woffice Google Login","woffice"));
            $gClient->setClientId(woffice_get_settings_option('google_app_id'));
            $gClient->setClientSecret(woffice_get_settings_option('google_app_secret'));
            $gClient->setRedirectUri(self::getCallbackUrl());
            $gClient->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));

            return $gClient;

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

            // Init the api
            $gClient = self::initApi();

            // We use the OauthV2 service
            $google_oauthV2 = new Google_Service_Oauth2($gClient);

            // Get a private token
            try {
                if(isset($_GET['code'])){
                    $gClient->authenticate($_GET['code']);
                }
                $this->access_token = $gClient->getAccessToken();
            }
                // If that fails, we throw our error
            catch (Google_Auth_Exception $e) {
                Woffice_Alert::create()->setType('error')->setContent($e->getMessage())->queue();
            }

            // If we got no token
            if (empty($this->access_token)) {
                header("Location: ". esc_url(home_url('/')), true);
                die();
            }

            // Fetch the user information
            $this->google_details = $google_oauthV2->userinfo->get();

            $user = $this->fetchUser( $this->google_details );

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

            $user_data = $this->google_details;

            // Attempt to update user's first name and last time if empty
            // note: first_name always set

            if (!empty($user_data['familyName']) && empty($user->last_name)) {
                update_user_meta($user->ID, 'last_name', $user_data['familyName']);
                update_user_meta( $user->ID, 'first_name', $user_data['givenName'] );
            }

            // We add an  alert
            Woffice_Alert::create()->setType('success')->setContent(__('Welcome back', 'woffice') .' '. woffice_get_name_to_display($user) .'!')->queue();

        }

        /**
         * Create a new WordPress account using Google Details and redirect once done
         */
        private function createUser() {

            $user = $this->google_details;

	        // If the registration is closed or it's open but email check doesn't match
	        if (!get_option( 'users_can_register' ) || !Woffice_Register::isEmailAllowed($user['email'])) {
		        woffice_redirect_to_login( 'login=social_unauthorized' );
		        return;
	        }

            // Create an username
            $username = sanitize_user(str_replace(' ', '_', strtolower($user['name'])));
            $email = sanitize_email($user['email']);

            // If there isn't any name, use the alias of the email
            if (empty($username)) {
                $email_exploded = explode('@', $email);
                $username = $email_exploded[0];
            }

            // Creating our user
            $new_user_id = wp_create_user($username, wp_generate_password(), $user['email']);

            if (!is_int($new_user_id)) {
                return;
            }

            // Setting the meta
            if (isset($user['givenName']) && !empty($user['givenName']))
                update_user_meta( $new_user_id, 'first_name', $user['givenName'] );

            if (isset($user['familyName']) && !empty($user['familyName']))
                update_user_meta( $new_user_id, 'last_name', $user['familyName'] );

            if (isset($user['user_url']) && !empty($user['user_url']))
                update_user_meta( $new_user_id, 'user_url', $user['link'] );

	        if (isset($user['picture']) && !empty($user['picture']))
		        update_user_meta( $new_user_id, 'woffice_social_avatar', $user['picture'] );

            update_user_meta( $new_user_id, 'user_email', $email );
            update_user_meta( $new_user_id, 'woffice_google_id', $user['id'] );

            /**
             * Action `woffice_google_after_signup`
             *
             * @param $new_user_id int
             */
            do_action('woffice_google_after_signup', $new_user_id);

            // Log the user?
            wp_set_auth_cookie( $new_user_id );

            // We add an  alert
            Woffice_Alert::create()->setType('success')->setContent(__('Welcome', 'woffice') .' '. woffice_get_name_to_display($new_user_id) .'!')->queue();
        }

        /**
         * Using the google details, get the corresponding user into the db
         *
         * @param array $google_details
         *
         * @return false|\WP_User
         */
        private function fetchUser( $google_details) {

            // We look for the `woffice_google_id` to see if there is any match
            $wp_users = get_users(array(
                'meta_key'     => 'woffice_google_id',
                'meta_value'   => $google_details['id'],
                'number'       => 1,
                'count_total'  => false,
            ));

            if(empty($wp_users[0])) {
                $wp_user = get_user_by( 'email', $google_details['email'] );
            } else
                $wp_user = $wp_users[0];

            // If user exists, update the google id, for the future fetches
            if( $wp_user instanceof \WP_User ) {
                add_user_meta( $wp_user->ID, 'woffice_google_id', $google_details['id'] );
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
new Woffice_Google_Signing();