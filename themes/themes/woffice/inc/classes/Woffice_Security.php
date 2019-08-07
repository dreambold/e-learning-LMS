<?php
/**
 * Class Woffice_Security
 *
 * This class handles the redirection process in Woffice
 * As well as the custom login page hooks / actions
 * It also generates an unique Woffice key attached to the site
 *
 * @since 2.1.3
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_Security' ) ) {
    class Woffice_Security
    {

    	static $custom_login_enabled = null;

        /**
         * Woffice_Security constructor
         */
        public function __construct()
        {

	        add_action( 'init', array($this,'init_custom_login_actions'));

            add_action( 'template_redirect', array($this,'redirect_user'));
            add_action( 'after_switch_theme', array($this,'create_login_page'));
            add_action( 'fw_settings_form_saved', array($this,'create_login_page'));

	        // Rest API checks
	        add_filter( 'rest_authentication_errors', array( $this, 'rest_authentication_errors_filter' ), 100 );
	        add_filter( 'rest_pre_dispatch',            array( $this, 'rest_if_user_is_allowed' ), 100, 3 );

	        // Unique key
            add_action( 'fw_settings_form_saved', array($this,'unique_key_generator'));

        }

	    /**
	     * Init all the actions that happen only if the custom login page is enabled
	     */
        public function init_custom_login_actions() {

	        if ( ! woffice_is_custom_login_page_enabled() )
	        	return;

	        $this->redirect_login_page();

	        add_action( 'wp_login_failed', array($this, 'login_failed'));
	        add_filter( 'authenticate', array($this, 'filter_verify_username_password'), 0, 3);
	        add_action( 'wp_logout', array($this,'logout_page'));

	        add_action( 'admin_head', array($this,'custom_auth_css'));
	        add_action( 'login_form_lostpassword', array($this,'password_lost'));
	        add_action( 'login_form_lostpassword', array($this,'redirect_to_custom_lostpassword'));
	        add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
	        add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );
	        add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
	        add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );

	        if( function_exists( 'bp_is_active') ) {
		        remove_filter( 'authenticate', 'bp_core_signup_disable_inactive', 30, 3 );
		        add_filter( 'authenticate', array($this, 'core_signup_disable_inactive'), 30, 3);

		        remove_action( 'login_form_bp-resend-activation', 'bp_members_login_resend_activation_email' );
		        add_action( 'login_form_bp-resend-activation', array( $this, 'members_login_resend_activation_email' ) );
	        }

        }

        /**
         * We create the login page
         * If it doesn't exist yet
         */
        public function create_login_page(){

            $login_custom = woffice_get_settings_option( 'login_custom' );
            if ( $login_custom == "nope" ) {
                 return;
            }

            // CREATE THE LOGIN PAGE
            global $wpdb;
            $table_name = $wpdb->prefix . 'posts';
            $check_page = $wpdb->get_row("SELECT post_name FROM ".$table_name." WHERE post_name = 'login'", 'ARRAY_A');
            if(empty($check_page)) {
                $prop_page = array(
                    'ID' 			=> '',
                    'post_title'    => 'Login',
                    'post_content'  => '',
                    'post_excerpt'  => '',
                    'post_name' => 'login',
                    'post_type' 	=> 'page',
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'page_template' => 'page-templates/login.php'
                );
                wp_insert_post($prop_page);
            }
        }

	    /**
	     * Validate the custom post types
	     *
	     * @param boolean $public - whether it's a public site or not
	     * @return string 'unauthorized', 'authorized', 'unhandled'
	     */
	    private function validate_custom_post_types($public = false)
	    {
	    	$status = 'unhandled';

		    // Check if the current page is a feed and if it is public
		    $allowed_feed = (is_feed() && !woffice_get_settings_option('feeds_private'));

		    if ($allowed_feed) {
		    	return 'authorized';
		    }

	    	global $post;

			if (!isset($post->ID) || empty($post->ID))
				return $status;

		    $current_post_type = get_post_type($post->ID);
		    $privacy_settings = null;

		    $supported_post_types = array(
			    'project',
			    'post',
			    'wiki',
			    'directory',
		    );

		    foreach ($supported_post_types as $post_type) {

		    	$handle = false;

		    	if ($post_type === $current_post_type || is_post_type_archive($post_type))
				    $handle = true;

		    	if (!$handle)
		    		continue;

			    $privacy_settings = woffice_get_settings_option('privacy_'. $post_type);
		    }

		    if (!$privacy_settings)
		    	return $status;

		    if ($privacy_settings === 'default') {
			    $only_logged_users = !$public;
		    } elseif ($privacy_settings === 'private') {
			    $only_logged_users = true;
		    } else {
			    $only_logged_users = false;
		    }

		    $status = ($only_logged_users && !is_user_logged_in()) ? 'unauthorized' : 'authorized';

	    	return $status;

	    }


        /**
         * We redirect the user
         * This is the most important function for the security
         * We check whether the user is allowed or not
         * That's also the function behind the redirection loop issue
         */
        public function redirect_user()
        {

	        // We get the site status
	        $public = woffice_get_settings_option( 'public' );

        	// We validate the custom post types first
	        $custom_post_types_allowed = $this->validate_custom_post_types(($public === 'yep'));
        	if ($custom_post_types_allowed === 'unauthorized')
		        $this->login_redirection();
        	elseif ($custom_post_types_allowed == 'authorized')
		        return;

            // If site is public & user isn't logged we'll check for the pages
            if (!is_user_logged_in() && ($public == "nope" || (function_exists( 'is_buddypress') && is_buddypress()))) {

                /**
                 * If it's a 404 that means it could be a redirection issue (loop)
                 * It can be fixed by refreshing the .htaccess file
                 *
                 * @param bool
                 */
                $is_flushing_enabled = apply_filters('woffice_hard_flush_on_not_found', true);
                if (is_404() && $is_flushing_enabled) {
                    flush_rewrite_rules();
                }

                /**
                 * Exclude specific paths from the redirection process
                 * Can be used for custom APIs
                 * i.e array('/foo','/bar')
                 *
                 * @param array
                 */
                $excluded_paths = apply_filters('woffice_redirected_excluded_paths', array());

                if (!empty($excluded_paths) && is_array($excluded_paths)) {
                    $uri = $_SERVER['REQUEST_URI'];
                    $has_excluded_path = false;
                    foreach ($excluded_paths as $path) {
                        if (strpos($uri, $path) !== false) {
                            $has_excluded_path = true;
                            break;
                        }
                    }
                    if ($has_excluded_path)
                        return;
                }


                // We get the login page to avoid infinite loop :
                $login_page_slug = woffice_get_login_page_name();
                if (!is_page($login_page_slug)) {

                    // We need it to know if tge first one is condition is checked (BuddyPress)
                    $buddypress_check_passed = false;

                    // We check for BuddyPress components :
                    if ( function_exists( "woffice_is_user_allowed_buddypress" ) ) {
                        // We run it only for BuddyPress pages
                        if ( is_buddypress() ) {
                            $buddypress_check = woffice_is_user_allowed_buddypress( "redirect" );
                            if ( $buddypress_check == false ) {
	                            woffice_redirect_to_login( '', false );
                                exit();
                            } else {
                                $buddypress_check_passed = true;
                            }
                        }
                    }

                    if ($buddypress_check_passed != true) {

                        // We check for excluded page - Check if there is some pages that need to be public
                        $excluded_pages       = woffice_get_settings_option( 'excluded_pages' );
                        $the_pages_tmp        = array();
                        $it_is_blog_component = false;
                        if (!empty($excluded_pages)) {

                            // We check for the Blog page :
                            $page_for_posts    = get_option( 'page_for_posts' );
                            $ID_page_for_posts = (!empty( $page_for_posts)) ? $page_for_posts : 0;
                            // If the blog page is in the excluded pages && it's this page:
                            if (in_array( $ID_page_for_posts, $excluded_pages) && $ID_page_for_posts != 0 && (is_home() || is_singular( 'post' ))) {
                                $it_is_blog_component = true;
                            }

                            // We fill the array
                            foreach ($excluded_pages as $page) {
                                $the_pages_tmp[] = $page;
                            }

                        } else {
                            $the_pages_tmp = array( "-1" );
                        }

                        // We check for the Woocommerce products :
                        if (function_exists('is_woocommerce')) {
                            $products_public = woffice_get_settings_option('products_public');
                            // Could use is_woocommerce() ?
                            if ($products_public == "yep" && (is_product() || is_shop() || is_product_category())) {
                                $allowed_product = true;
                            } else {
                                $allowed_product = false;
                            }
                        } else {
                            $allowed_product = false;
                        }

                        // Check if the current page is a feed and if it is public
	                    $allowed_feed = (is_feed() && !woffice_get_settings_option('feeds_private'));

	                    /**
	                     * Use this filter to additional checks before decide if redirect the user to the login page or not
	                     *
	                     * @param bool $redirected If the user have to be redirected to the login page or not
	                     */
                        $filter_applied = apply_filters_deprecated('woffice_redirect_user_additional_check', array(true), '2.6.0', 'woffice_user_redirect_to_login');

                        $user_not_allowed = apply_filters('woffice_user_redirect_to_login', (
                        	! is_page($the_pages_tmp)
	                        && $it_is_blog_component == false
	                        && $allowed_product == false
	                        && $allowed_feed == false
	                        && $filter_applied
                        ));

                        // If it's not one of the excluded pages AND Not the blog page, we redirect :
                        if ($user_not_allowed) {
                            $this->login_redirection();
                        }

                    }

                }

            }

        }

	    /**
	     * Login redirection
	     */
        private function login_redirection() {
	        /**
	         * If the user isn't allowed to the current page, he is redirected, this hook do something
	         * before the redirection
	         */
	        do_action('woffice_before_redirect_unallowed_user_to_login');

	        if (woffice_is_custom_login_page_enabled()) {
		        woffice_redirect_to_login('', false);
		        exit();
	        } else {
		        wp_redirect(esc_url(home_url( '/wp-login.php' )));
		        exit();
	        }

        }

        /**
         * Redirect the user to the login page
         */
        public function redirect_login_page()
        {

            $page_viewed = basename( $_SERVER['REQUEST_URI'] );
            $page_viewed = explode('?', $page_viewed, 2);

            // Check if the page is the standard login page, and not lost page or logout page
            if (
                (
                    $page_viewed[0] == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && !isset($page_viewed[1])
                    ||
                    isset($page_viewed[1]) && strpos($page_viewed[1], 'redirect_to') !== false
                )
                &&
                (
                    !(isset($page_viewed[1]) && strpos($page_viewed[1], 'action=logout') !== false)
                )
            ) {
                woffice_redirect_to_login('', false);
                exit;
            }

        }

        /**
         * On login fail, we redirect to the custom login page
         */
        public function login_failed()
        {

            woffice_redirect_to_login( 'login=failed' );
            exit;

        }

        /**
         * Filter to redirect on login password or username are empty
         *
         * @param $user
         * @param $username
         * @param $password
         */
        public function filter_verify_username_password( $user, $username, $password ) {

            // if the user submit the form with missing credential
            if (isset($_POST['wp-submit']) && ( $username == "" || $password == "")) {
                woffice_redirect_to_login('login=empty');
                exit;
            }

        }

	    /**
	     * Display a "resend email" link when an unregistered user attempts to log in.
	     *
	     * @since 2.5.7
	     *
	     * @param WP_User|WP_Error|null $user     Either the WP_User or the WP_Error object.
	     * @param string                $username The inputted, attempted username.
	     * @param string                $password The inputted, attempted password.
	     * @return WP_User|WP_Error
	     */
        public function core_signup_disable_inactive( $user = null, $username = '', $password ='' ) {

	        // Login form not used.
	        if ( empty( $username ) && empty( $password ) ) {
		        return $user;
	        }

	        // An existing WP_User with a user_status of 2 is either a legacy
	        // signup, or is a user created for backward compatibility. See
	        // {@link bp_core_signup_user()} for more details.
	        if ( is_a( $user, 'WP_User' ) && 2 == $user->user_status ) {
		        $user_login = $user->user_login;

		        // If no WP_User is found corresponding to the username, this
		        // is a potential signup.
	        } elseif ( is_wp_error( $user ) && 'invalid_username' == $user->get_error_code() ) {
		        $user_login = $username;

		        // This is an activated user, so bail.
	        } else {
		        return $user;
	        }

	        // Look for the unactivated signup corresponding to the login name.
	        $signup = BP_Signup::get( array( 'user_login' => sanitize_user( $user_login ) ) );


	        // No signup or more than one, something is wrong. Let's bail.
	        if ( empty( $signup['signups'][0] ) || $signup['total'] > 1 ) {
		        return $user;
	        }

	        // Unactivated user account found!
	        // Set up the feedback message.
	        $signup_id = $signup['signups'][0]->signup_id;

	        woffice_redirect_to_login('not_active_user=' . $signup_id);
	        exit;

        }

	    /**
	     * On the login screen, resends the activation email for a user.
	     *
	     * @since 2.5.7
	     *
	     * @see core_signup_disable_inactive()
	     */
        public function members_login_resend_activation_email() {
	        global $error;

	        if ( empty( $_GET['id'] ) || empty( $_GET['_wpnonce'] ) ) {
		        return;
	        }

	        // Verify nonce.
	        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'bp-resend-activation' ) ) {
		        die( 'Security check' );
	        }

	        $signup_id = (int) $_GET['id'];

	        // Resend the activation email.
	        // also updates the 'last sent' and '# of emails sent' values.
	        $resend = BP_Signup::resend( array( $signup_id ) );

	        // Add feedback message.
	        woffice_redirect_to_login('resend_activation=' . (int)(( ! empty( $resend['errors'] ) )) );
	        exit;

        }

        /**
         * Logout redirection
         */
        public function logout_page()
        {
            woffice_redirect_to_login( 'login=false' );
            exit;
        }

        /**
         * Custom CSS for the login popup on Wordpress admin
         *
         * @return null|void
         */
        public function custom_auth_css()
        {
            echo '<style type="text/css">#wp-auth-check-wrap #wp-auth-check{margin: 0 0 0 -45%;width: 90%;}</style>';
        }

        /**
         * Handles the lost password action
         */
        public function password_lost()
        {
            $login_rest_password = woffice_get_settings_option( 'login_rest_password' );
            if ($login_rest_password == "yep" && 'POST' == $_SERVER['REQUEST_METHOD'] ) :
                $errors          = retrieve_password();
                $login_page_slug = woffice_get_login_page_name();
                $login_page      = home_url( '/' . $login_page_slug . '/' );
                if ( is_wp_error( $errors ) ) {
                    // Errors found
                    $redirect_url = $login_page . "?type=lost-password";
                    $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
                } else {
                    // Email sent
                    $redirect_url = $login_page;
                    $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
                }

                wp_redirect( $redirect_url );
                exit;
            endif;
        }

        /**
         * Handles the reset password action
         */
        public function do_password_reset()
        {

            if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
                $rp_key = $_REQUEST['rp_key'];
                $rp_login = $_REQUEST['rp_login'];

                $user = check_password_reset_key( $rp_key, $rp_login );

                if ( ! $user || is_wp_error( $user ) ) {
                    if ( $user && $user->get_error_code() === 'expired_key' ) {
                        woffice_redirect_to_login( 'type=reset-password&errors=expiredkey' );
                    } else {
                        woffice_redirect_to_login( 'type=reset-password&errors=invalidkey' );
                    }
                    exit;
                }

                if ( isset( $_POST['pass1'] ) ) {
                    if ( $_POST['pass1'] != $_POST['pass2'] ) {
                        // Passwords don't match
                        woffice_redirect_to_login( 'type=reset-password&errors=password_reset_mismatch&key='. $rp_key .'&login='. $rp_login);
                    }

                    if ( empty( $_POST['pass1'] ) ) {
                        // Password is empty
                        woffice_redirect_to_login( 'type=reset-password&errors=password_reset_empty&key='. $rp_key .'&login='. $rp_login);
                        exit;
                    }

                    if (!Woffice_Register::isPasswordValid($_POST['pass1'])) {
                        woffice_redirect_to_login( 'type=reset-password&errors=password_reset_strength&key='. $rp_key .'&login='. $rp_login);
                        exit;
                    }


                    // Parameter checks OK, reset password
                    reset_password( $user, $_POST['pass1'] );
                    woffice_redirect_to_login( 'password=changed');
                } else {
                    echo __("Invalid request.","woffice");
                }

                exit;
            }

        }

        /**
         * Handling the redirection to the custom reset password page
         */
        public function redirect_to_custom_password_reset()
        {

            if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
                // Verify key / login combo
                $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
                if ( ! $user || is_wp_error( $user ) ) {
                    if ( $user && $user->get_error_code() === 'expired_key' ) {
                        woffice_redirect_to_login( 'type=reset-password&errors=expiredkey' );
                    } else {
                        woffice_redirect_to_login( 'type=reset-password&errors=invalidkey' );
                    }
                    exit;
                }
                woffice_redirect_to_login( 'type=reset-password&login='. urlencode($_REQUEST['login']) .'&key='. $_REQUEST['key'] );
                exit;
            }

        }

        /**
         * Handling the redirection to the custom lost password page
         */
        public function redirect_to_custom_lostpassword()
        {

            $login_rest_password = woffice_get_settings_option( 'login_rest_password' );
            if ($login_rest_password == "yep" && 'GET' == $_SERVER['REQUEST_METHOD'] ) :

                if ( is_user_logged_in() ) {
                    wp_redirect( home_url() );
                    exit;
                }
                woffice_redirect_to_login( 'type=lost-password' );
                exit;

            endif;
        }

        /**
         * Redirect to home page after lost password action
         */
        public function lost_password_after_redirect() {

            if ( ! woffice_is_custom_login_page_enabled() ) {
                return;
            }

            wp_redirect( home_url() );
            exit;
        }

	    /**
	     * Check if the site is private and the current user si not logged in.
	     *
	     * @param  mixed $error Default value.
	     * @return mixed WP_Error if disabled, otherwise the default value.
	     */
	    public function rest_authentication_errors_filter( $error ) {

            /**
             * Lets you deactivate the WordPress REST API by default
             *
             * @param bool
             */
		    $rest_api_enabled = apply_filters( 'woffice_rest_api_enabled', true );

		    if(!$rest_api_enabled)
			    return $error;

		    $is_website_public = woffice_get_settings_option('public');
			
		    $allowed = ( $is_website_public == 'yep' || is_user_logged_in() );

		    /**
		     * Filter the permissions on the displaying of the REST-API
		     *
		     * @since 2.5.0
		     *
		     * @param bool $allowed
		     * @param $error
		     */
		    $allowed = apply_filters( 'woffice_rest_api_allowed', $allowed, $error );

		    if( $allowed )
				return $error;

		    /*
		     * Deactivated for now as is_user_logged_in() return false every time
		     * And anyway, the request goes through: woffice_is_user_allowed() on the next hook
		     *
		     * If the site requires authentication and the current user is not logged in, return an error message
		     */
		    //$error = new WP_Error( 'rest_disabled', __( 'You are not allowed to see the Rest API.', 'woffice' ), array( 'status' => 401 ) );

		    return $error;

	    }

	    /**
	     * Check if the current user is allowed to display the post required
	     *
	     * @param mixed           $result      Response to replace the requested version with. Can be anything
	     *                                     a normal endpoint can return, or null to not hijack the request.
	     * @param WP_REST_Server  $rest_server Server instance.
	     * @param WP_REST_Request $request     Request used to generate the response.
	     * @return mixed
	     */
	    public function rest_if_user_is_allowed( $result, $rest_server, $request ) {
			
		    // If the user is allowed, return the standard value
		    if( woffice_is_user_allowed())
			    return $result;

		    // Get the route for the request.
		    $route = $request->get_route();

		    // Return a WP_Error is authentication is required but there
		    // is no current user logged in.
		    $result = new WP_Error(
			    'rest_cannot_view',
			    sprintf( __( 'You are not allowed to see the get this information from the REST API', 'woffice' ), $route ),
			    array( 'status' => 401 )
		    );

		    return $result;

	    }

        /**
         * Generate an unique ID for the site regarding Alkalab's API
         *
         * @return mixed
         */
	    public function unique_key_generator() {

            $existing_key = get_option('woffice_key');

            if(!empty($existing_key))
                return true;

            $key = woffice_get_random(32);

            $update_option = update_option('woffice_key', $key);

            return $update_option;

        }

    }
}
/**
 * Let's fire it :
 */
new Woffice_Security();



