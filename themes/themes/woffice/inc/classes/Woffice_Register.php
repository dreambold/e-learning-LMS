<?php
/**
 * Class Woffice_Register
 *
 * Load everything related to the Woffice register changes
 *
 * @since 2.1.3
 * @author Alkalab
 */
use ComponentManualUserApprove\classes\Eonet_MUA_Message;

if( ! class_exists( 'Woffice_Register' ) ) {
    class Woffice_Register
    {

        private $username;
        private $email;
        private $password;
        private $website;
        private $first_name;
        private $last_name;


        /**
         * Woffice_Register constructor
         */
        public function __construct()
        {
            if (get_option('users_can_register') != '1')
                return;

            add_action('wp_ajax_nopriv_WofficeRegisterRedirect', array($this, 'register_redirect'));
            add_action('wp_ajax_WofficeRegisterRedirect', array($this, 'register_redirect'));
            add_shortcode('woffice_registration_form', array($this, 'shortcode'));
            add_action('user_register', array($this,'send_new_user_notifications'));
            add_action('wp_head', array($this, 'recaptcha_header_script'));

            // Password Strength Script
            add_action( 'wp_enqueue_scripts', array($this, 'passwordStrengthAsset'));

        }

        /**
         * We redirect to the home page after the user was registered
         * And we log it
         */
        public function register_redirect(){

            check_ajax_referer( 'WofficeRegisterRedirectNonce', 'security' );
            // We grab the user's ID
            if (isset($_POST['user_id']) && $_POST['user_id'] > 0) {
                $user_id = $_POST['user_id'];
                // Get the ID
                $user = get_user_by( 'id', $user_id );
                if( $user ) {
                    // Login :
                    wp_set_current_user( $user_id, $user->user_login );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $user->user_login );
                    // Ajax content :
                    echo '<div class="woffice-ajax-main"><i class="fa fa-check-circle"></i> '.__('Redirecting', 'woffice').'...</div>';
                }
            }
            wp_die();

        }

        /**
         * We return the register form's markup
         *
         * @return void
         */
        public function registration_form()
        {

            ?>

            <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']) . '#register-form'; ?>"
                  id="register-form">
                <div class="login-form">
                    <div class="form-group">
                        <input name="reg_name" type="text" class="login-field"
                               value="<?php echo(isset($_POST['reg_name']) ? $_POST['reg_name'] : null); ?>"
                               placeholder="<?php _e("Username", "woffice"); ?>" id="reg-name" required/>
                        <label class="login-field-icon fui-user" for="reg-name"></label>
                    </div>

                    <div class="form-group">
                        <input name="reg_email" type="email" class="login-field"
                               value="<?php echo(isset($_POST['reg_email']) ? $_POST['reg_email'] : null); ?>"
                               placeholder="<?php _e("Email", "woffice"); ?>" id="reg-email" required/>
                        <label class="login-field-icon fui-mail" for="reg-email"></label>
                    </div>

                    <div class="form-group">
                        <?php do_action( 'bp_signup_password_errors' ); ?>
                        <input name="reg_password" type="password" class="login-field password-entry"
                               value="<?php echo(isset($_POST['reg_password']) ? $_POST['reg_password'] : null); ?>"
                               placeholder="<?php _e("Password", "woffice"); ?>" id="reg-pass" required/>
                        <label class="login-field-icon fui-lock" for="reg-pass"></label>
                        <div id="pass-strength-result"></div>
                        <p class="description"><?php echo wp_get_password_hint(); ?></p>
                    </div>

                    <?php
                    $register_password_confirmation = woffice_get_settings_option( 'register_password_confirmation' );
                    if ( $register_password_confirmation == 'yep' ) : ?>
                        <div class="form-group">
                            <?php do_action( 'bp_signup_password_confirm_errors' ); ?>
                            <input name="reg_password_confirmation" type="password" class="login-field password-entry-confirm"
                                value="<?php echo(isset($_POST['reg_password_confirmation']) ? $_POST['reg_password_confirmation'] : null); ?>"
                                placeholder="<?php _e("Password confirmation", "woffice"); ?>" id="reg-pass-confirmation" required/>
                            <label class="login-field-icon fui-lock" for="reg-pass-confirmation"></label>
                        </div>
                    <?php endif; ?>


                    <div class="form-group">
                        <input name="reg_fname" type="text" class="login-field"
                               value="<?php echo(isset($_POST['reg_fname']) ? $_POST['reg_fname'] : null); ?>"
                               placeholder="<?php _e("First Name", "woffice"); ?>" id="reg-fname"/>
                        <label class="login-field-icon fui-user" for="reg-fname"></label>
                    </div>

                    <?php
                    $register_last_name = woffice_get_settings_option( 'register_last_name' );
                    if ( $register_last_name == 'yep' ) : ?>
                        <div class="form-group">
                            <input name="reg_lname" type="text" class="login-field"
                                value="<?php echo(isset($_POST['reg_lname']) ? $_POST['reg_lname'] : null); ?>"
                                placeholder="<?php _e("Last Name", "woffice"); ?>" id="reg-lname"/>
                            <label class="login-field-icon fui-user" for="reg-lname"></label>
                        </div>
                    <?php endif; ?>

                    <?php
                    /*
                     * ROLE FIELD
                    */
                    $register_role = woffice_get_settings_option('register_role');
                    $excluded_roles = woffice_get_settings_option('roles_excluded_in_the_form');
                    if ($register_role == "yep") {
                        /* Roles array ready for options */
                        global $wp_roles;
                        $tt_roles = array();
                        $excluded_summed = array_unique(array_merge($excluded_roles, array('administrator', 'super_admin')), SORT_REGULAR);
                        foreach ($wp_roles->roles as $key => $value) {
                            if (!in_array($key, $excluded_summed)) {
                                $tt_roles[$key] = $value['name'];
                            }
                        }
                        $tt_roles_tmp = array('nope' => __("Default", "woffice")) + $tt_roles;
                        /**
                         * Filter `woffice_register_roles`
                         * You can use unset() to remove any role from the roles array
                         *
                         * @param array $tt_roles_tmp The array of roles
                         */
                        $tt_roles_tmp = apply_filters('woffice_register_roles', $tt_roles_tmp);
                        ?>
                        <div class="form-group">
                            <label class="login-field-icon fui-role"
                                   for="reg-role"><?php _e("Role", "woffice"); ?></label>

                            <select class="form-control" name="reg_role" class="login-field">
                                <?php foreach ($tt_roles_tmp as $key => $role) {
                                    printf('<option value="%s">%s</option>', esc_attr($key), esc_html($role));
                                } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <?php
                    /*
                     * We display the Xprofile fields
                     */
                    $register_buddypress = woffice_get_settings_option('register_buddypress');

                    //Remove the BuddyPress field username
                    $register_buddypress_excluded_fields = array( 'field_1' );

                    /**
                     * Filter the array of the custom fields excluded in the registration
                     *
                     * @param array $register_buddypress_excluded_fields
                     */
                    $register_buddypress_excluded_fields = apply_filters( 'woffice_registration_xprofile_fields_excluded', $register_buddypress_excluded_fields );

                    if (($register_buddypress === "yep") && woffice_bp_is_active('xprofile')) :

                            $register_buddypress_specific = woffice_get_settings_option('register_buddypress_specific');

                        ?>

                            <h4><?php _e('Profile Details', 'woffice'); ?></h4>

                            <?php
                                // If we only want to show specific xprofile fields
                                if (sizeof($register_buddypress_specific) > 0 &&  (woffice_bp_is_active('xprofile'))) :
                            ?>

                                <?php  if ( bp_has_profile() ) : while (bp_profile_groups()) : bp_the_profile_group(); ?>

                                    <?php while (bp_profile_fields()) : bp_the_profile_field(); ?>

                                        <?php
                                            // We exclude the non-relevant fields (could be improved though)
                                            if ( !in_array(bp_get_the_profile_field_id(), $register_buddypress_specific)) continue;
                                        ?>

                                        <div class="form-group">
                                            <?php
                                            $field_id = bp_get_the_profile_field_input_name();
                                            if (in_array($field_id, $register_buddypress_excluded_fields))
                                                continue;

                                            $field_type = bp_xprofile_create_field_type(bp_get_the_profile_field_type());
                                            $field_type->edit_field_html();
                                            ?>
                                        </div>

                                <?php endwhile; endwhile; endif; ?>

                            <?php
                                else :
                            ?>

                                <?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
                                <?php if (woffice_bp_is_active('xprofile')) : if (bp_has_profile(array('profile_group_id' => 1, 'fetch_field_data' => false))) : while (bp_profile_groups()) : bp_the_profile_group(); ?>
                                    <?php while (bp_profile_fields()) : bp_the_profile_field(); ?>

                                        <div class="form-group">
                                            <?php
                                            $field_id = bp_get_the_profile_field_input_name();
                                            if ( in_array( $field_id, $register_buddypress_excluded_fields) )
                                                continue;

                                            $field_type = bp_xprofile_create_field_type(bp_get_the_profile_field_type());
                                            $field_type->edit_field_html();
                                            ?>
                                        </div>


                                    <?php endwhile; ?>

                                    <input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids"
                                           value="<?php bp_the_profile_field_ids(); ?>"/>

                                <?php endwhile; endif; endif; ?>

                            <?php endif; ?>

                    <?php endif; ?>


                    <?php
                    /**
                     * Before the captcha, in the user registration form. Useful to add custom content at the end of the form
                     */
                    do_action('woffice_register_form_before_captcha'); ?>

                    <?php
                    /*
                     * Built-In Captcha code
                     */
                    $register_captcha = woffice_get_settings_option('register_captcha');
                    if ($register_captcha == "yep") {
                        $register_captcha_question = woffice_get_settings_option('register_captcha_question');
                        ?>
                        <div class="form-group">
                            <input name="reg_captcha" type="text" class="login-field"
                                   value="<?php echo(isset($_POST['reg_captcha']) ? $_POST['reg_captcha'] : null); ?>"
                                   placeholder="<?php echo esc_attr($register_captcha_question); ?>" id="reg_captcha" required/>
                            <label class="login-field-icon fui-user" for="reg_captcha"></label>
                        </div>
                        <?php

                    }
                    ?>

                    <?php
                    /*
                     * ReCaptcha code
                     */
                    $recatpcha_enable = woffice_get_settings_option('recatpcha_enable');
                    if ($recatpcha_enable == "yep") { ?>
                        <?php // We check for the keys :
                        $recatpcha_key_site = woffice_get_settings_option('recatpcha_key_site');
                        $recatpcha_key_secret = woffice_get_settings_option('recatpcha_key_secret');
                        if (!empty($recatpcha_key_site) && !empty($recatpcha_key_secret)) { ?>
                            <div class="g-recaptcha" data-sitekey="<?php echo $recatpcha_key_site; ?>"></div>
                        <?php } else {
                            _e('One of the key is missing so the Recaptcha API is not established.', 'woffice');
                        } ?>
                    <?php } ?>

                    <input class="btn btn-default w-100" type="submit" name="reg_submit"
                           value="<?php _e('Register', 'woffice'); ?>"/>
            </form>

            <?php
        }

        /**
         * We validate the data
         *
         * @return WP_Error
         */
        public function validation()
        {

            if (empty($this->username) || empty($this->password) || empty($this->email)) {
                return new WP_Error('field', __('Required form field is missing', 'woffice'));
            }

            if (strlen($this->username) < 4) {
                return new WP_Error('username_length', __('Username too short. At least 4 characters is required', 'woffice'));
            }

            if (!self::isPasswordValid($this->password))
                return new WP_Error('password', __('Your password does not have a valid format', 'woffice'));

            $register_password_confirmation = woffice_get_settings_option( 'register_password_confirmation' );
            if ( $register_password_confirmation == 'yep' ) {
                if ($this->password != $_POST['reg_password_confirmation']) {
                    return new WP_Error('password', __("Password confirmation doesn't match", 'woffice'));
                }
            }

            if (!is_email($this->email)) {
                return new WP_Error('email_invalid', __('Email is not valid', 'woffice'));
            }

            list($email_user, $email_domain) = explode('@', $this->email);
            if ($email_domain == 'mail.ru' or $email_domain == 'rambler.ru' or $email_domain == 'gmail.com') {

                return new WP_Error('email_invalid', __('<strong> ERROR </ strong>: Sorry, but registration of users with mailbox "'.$email_domain.'" is prohibited.', 'woffice'));
            } 

            if (email_exists($this->email)) {
                return new WP_Error('email', __('Email Already in use', 'woffice'));
            }

            if(username_exists($this->username)) {
                return new WP_Error('username', __('Username Already in use', 'woffice'));
            }


            // Check for custom domain :
	        if(!self::isEmailAllowed($this->email)) {
		        return new WP_Error('email', __('Email domain is incorrect', 'woffice'));
            }

            if (!empty($website)) {
                if (!filter_var($this->website, FILTER_VALIDATE_URL)) {
                    return new WP_Error('website', __('Website is not a valid URL', 'woffice'));
                }
            }

            $details = array(
                'Username' => $this->username
            );

            foreach ($details as $field => $detail) {
                if (!validate_username($detail)) {
                    return new WP_Error('name_invalid', sprintf( esc_html__('Sorry, the %s you entered is not valid', 'woffice'), $field ) );
                }
            }

            /* Captcha Check */
            $register_captcha = woffice_get_settings_option('register_captcha');
            if ($register_captcha == "yep") {
                $register_captcha_answer = woffice_get_settings_option('register_captcha_answer');
                if ($_POST["reg_captcha"] === '' || $register_captcha_answer != $_POST["reg_captcha"]) {
                    return new WP_Error('captcha', __('Sorry, the captcha is not valid.', 'woffice'));
                }

            }

            /* Xprofile Fields : for required fields */
            $register_buddypress = woffice_get_settings_option('register_buddypress');
            if ($register_buddypress == "yep") {
                /* We add the xprofile fields */
                if (woffice_bp_is_active('xprofile')) :
                    if (bp_has_profile(array('profile_group_id' => 1, 'fetch_field_data' => false))) :
                        while (bp_profile_groups()) : bp_the_profile_group();
                            while (bp_profile_fields()) : bp_the_profile_field();

                                // We check if it's required :
                                if (bp_get_the_profile_field_is_required() == "1") {
                                    $field = bp_get_the_profile_field_input_name();

                                    //Remove the BuddyPress field username
                                    if($field == 'field_1')
                                        continue;

                                    // If it's a date input
                                    if(bp_get_the_profile_field_type() == 'datebox') {
                                        if ( empty( $_POST[$field . '_day'] ) ||
                                             empty( $_POST[$field . '_month'] ) ||
                                             empty( $_POST[$field . '_year'] ) )
                                        {
                                            return new WP_Error($field, __('Sorry, this fields is required', 'woffice') . ': ' . bp_get_the_profile_field_name());
                                        }

                                    } else {
                                            $value = $_POST[$field];
                                            // If it's empty & required we throw the error
                                            if (empty($value)) {
                                                return new WP_Error($field, __('Sorry, this fields is required', 'woffice') . ': ' . bp_get_the_profile_field_name());
                                            }
                                    }

                                }
                            endwhile;
                        endwhile;
                    endif;
                endif;
            }


        }

        /**
         * We register the user
         *
         * @return void the message / alert
         */
        public function registration()
        {

            /* We first check for the roles */
            $register_role = woffice_get_settings_option('register_role');
            $default_role = get_option('default_role');
            if (isset($_POST["reg_role"]) && $_POST["reg_role"] != "nope" && $register_role == "yep") {
                $role = $_POST["reg_role"];
            } else {
                $role = $default_role;
            }

            $user_data = array(
                'user_login'    => esc_attr($this->username),
                'user_email'    => esc_attr($this->email),
                'user_pass'     => esc_attr($this->password),
                'first_name'    => esc_attr($this->first_name),
                'last_name'     => esc_attr($this->last_name),
                'display_name'  => '',
                'role'          => esc_attr($role)
            );

            if (is_wp_error($this->validation())) {

                $color_notifications = woffice_get_settings_option('color_notifications');
                echo '<div class="infobox fa-exclamation-triangle" style="background-color: ' . $color_notifications . ';">';
                echo '<strong>' . $this->validation()->get_error_message() . '</strong>';
                echo '</div>';

            } else {

	            /**
	             * Filter `woffice_register_user_data`
                 *
                 * @param array
                 *
                 * @return array
	             */
	            $user_data = apply_filters('woffice_register_user_data', $user_data);

                $register_user = wp_insert_user($user_data);
                $user_id = $register_user;

                if (!is_wp_error($register_user)) {

                    // Send confirmation email or registration email
                    if(woffice_is_enabled_confirmation_email()) {
                        global $wpdb;

                        // Update the user status to '2', ie "not activated"
                        // (0 = active, 1 = spam, 2 = not active).
                        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->users} SET user_status = 2 WHERE ID = %d", $user_id ) );


                        $activation_key = wp_generate_password( 32, false );
                        bp_update_user_meta( $user_id, 'activation_key', $activation_key );

                        $user = get_userdata($user_id);
                        $args = array(
                            'user_login'     => $user_data['user_login'],
                            'user_email'     => $user_data['user_email'],
                            'activation_key' => $activation_key,
                            'meta' => array(
                                'first_name' => $this->first_name,
                                'last_name' => $this->last_name,
                                'roles' => $user->roles,
                                'allcaps' => $user->allcaps
                            )
                        );
	                    
                        // WordPress creates these options automatically on
                        // wp_insert_user(), but we delete them so that inactive
                        // signups don't appear in various user counts.
                        delete_user_option( $user_id, 'capabilities' );
                        delete_user_option( $user_id, 'user_level'   );

                        BP_Signup::add( $args );

                        // Send Activation email
                        if (apply_filters('bp_core_signup_send_activation_key', true, $user_id, $user_data['user_email'], $activation_key, array())) {

                            bp_core_signup_send_validation_email($user_id, $user_data['user_email'], $activation_key, $user_data['user_login']);
                        }
                    }

                    $color_notifications_green = (function_exists('fw_get_db_settings_option')) ? fw_get_db_settings_option('color_notifications_green') : '';
                    echo '<div id="success-register" class="infobox fa-check-circle" style="background-color: ' . $color_notifications_green . ';" data-user="'.$user_id.'">';

                    // Default successful message
	                $successful_message = '<strong>' . __('Registration complete. You can now ', 'woffice') . ' <a href="' . wp_login_url() . '">' . __('Sign In', 'woffice') . '</a></strong>';

	                if( woffice_is_enabled_confirmation_email() ) {
		                $successful_message = '<strong>' . __('Registration complete. We sent you a confirmation email to:', 'woffice') . ' ' . $user_data['user_email'] . '</strong>';
                    } else {
		                if( woffice_manual_user_approve_enabled() ) {
			                $successful_message = '<strong>' . __('Registration complete. Please wait for approval. ', 'woffice') . '</strong>';
                        }
                    }

                    /**
                     * Filter the message printed when a new member sign up successfully
                     *
                     * @param string $succesful_message
                     */
                    echo apply_filters('woffice_registration_completed_message', $successful_message);

                    echo '</div>';

                    $register_buddypress = woffice_get_settings_option('register_buddypress');
                    if ($register_buddypress == "yep") {
                        /* We add the xprofile fields */
                        if (woffice_bp_is_active('xprofile')) {
	                        if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) {
		                        while ( bp_profile_groups() ) {
			                        bp_the_profile_group();

			                        while ( bp_profile_fields() ) {
				                        bp_the_profile_field();

				                        $field = bp_get_the_profile_field_input_name();
				                        $value = '';

				                        // Remove the BuddyPress field username
				                        if ( $field == 'field_1' ) {
					                        continue;
				                        }

				                        /* We manage the fields types here */
				                        if ( 'datebox' == bp_get_the_profile_field_type() ) {
					                        if ( isset( $_POST[ $field . "_day" ] ) && isset( $_POST[ $field . "_month" ] ) && isset( $_POST[ $field . "_year" ] ) ) {
						                        $day_r   = $_POST[ $field . "_day" ];
						                        $month_r = $_POST[ $field . "_month" ];
						                        $year_r  = $_POST[ $field . "_year" ];
					                        }
					                        $date  = date( 'Y-m-d H:i:s', strtotime( $day_r . $month_r . $year_r ) );
					                        $value = $date;
				                        } else if (isset($_POST[$field])) {
					                        $value = $_POST[$field];
				                        }

				                        if ( ! empty( $value ) ) {
					                        $value_ready = $value;
					                        $field_id    = bp_get_the_profile_field_id();
					                        $save        = xprofile_set_field_data($field_id, $register_user, $value_ready);
				                        }

			                        }
		                        }
	                        }


                        }
                    }


	                if (woffice_bp_is_active('xprofile')) {

		                $name = $this->first_name;
		                if ($this->last_name)
			                $name = $name . ' ' . $this->last_name;

		                if (empty($name) || ' ' == $name) {
			                $name = bp_get_user_meta($user_id, 'nickname', true);
		                }

		                xprofile_set_field_data(1, $user_id, $name);

		                xprofile_sync_wp_profile($user_id);

		                bp_update_user_meta($user_id, 'first_name', $this->first_name);
		                bp_update_user_meta($user_id, 'last_name',  $this->last_name);
                    }

                } else {
                    $color_notifications = woffice_get_settings_option('color_notifications');
                    echo '<div class="infobox fa-exclamation-triangle" style="background-color: ' . $color_notifications . ';">';
                    echo '<strong>' . $register_user->get_error_message() . '</strong>';
                    echo '</div>';
                }

            }

        }

        /**
         * We check the custom captcha from the Theme Settings
         *
         * @return WP_Error
         */
        function recaptchaCheck()
        {

            /* Google ReCaptcha Check */
            $recatpcha_enable = woffice_get_settings_option('recatpcha_enable');
            if ($recatpcha_enable == "yep") {

                // We check the post variable
                if (isset($_POST['g-recaptcha-response'])) {
                    $re_captcha = $_POST['g-recaptcha-response'];
                }

                // If it exists
                if (!$re_captcha) {
                    return new WP_Error('captcha', __('Sorry, the captcha is empty.', 'woffice'));
                    $this->validation();
                }

                // API check
                $recatpcha_key_secret = woffice_get_settings_option('recatpcha_key_secret');
                // make a GET request to the Google reCAPTCHA Server
                $request_url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $recatpcha_key_secret . '&response=' . esc_attr($re_captcha) . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

                $request_recaptcha = wp_remote_get($request_url);
                if (is_array($request_recaptcha) && array_key_exists('body', $request_recaptcha)) {
                    $response_php = json_decode($request_recaptcha["body"], true);
                } else {
                    $response_php["success"] = false;
                }
                //return new WP_Error('captcha', fw_print($request_recaptcha));

                // The response check
                if ($response_php["success"] != true) {
                    return new WP_Error('captcha', __('Sorry, the captcha is not valid.', 'woffice'));
                }

            }

        }

        /**
         * We render the form shortcode
         *
         * @return string
         */
        function shortcode()
        {

            ob_start();

            if (isset($_POST['reg_submit']) && $_POST['reg_submit']) {

                $this->username = $_POST['reg_name'];
                $this->email = $_POST['reg_email'];
                $this->password = $_POST['reg_password'];
                $this->first_name = $_POST['reg_fname'];
                $this->last_name = $_POST['reg_lname'];


                // We check for captcha error
                $return_captcha = $this->recaptchaCheck();
                if (is_wp_error($return_captcha)) {
                    /*Error Dislay*/
                    $color_notifications = woffice_get_settings_option('color_notifications');
                    echo '<div class="infobox fa-exclamation-triangle" style="background-color: ' . $color_notifications . ';">';
                    echo '<strong>' . $return_captcha->get_error_message() . '</strong>';
                    echo '</div>';
                } // If no error
                else {
                    // We check other fields
                    $this->validation();
                    // We register
                    $this->registration();
                }

            }

            $this->registration_form();
            return ob_get_clean();
        }

        /**
         * Add the Recaptcha library
         *
         * @return void
         */
        public function recaptcha_header_script() {
            $login_page_slug = woffice_get_login_page_name();
            $recatpcha_enable = woffice_get_settings_option('recatpcha_enable');
            if (is_page($login_page_slug) && $recatpcha_enable == "yep"){
                echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            }
        }

        /**
         * Send a notification to a new user
         *
         * @param $user_id
         */
        public function send_new_user_notifications( $user_id ) {

            if( ! woffice_is_custom_login_page_enabled() || woffice_is_enabled_confirmation_email() || function_exists('eonet_manual_user_approve'))
                return;

            $send_email_to_user = woffice_get_settings_option('register_new_user_email');
            if ($send_email_to_user && function_exists('woffice_send_user_registration_email'))
                woffice_send_user_registration_email($user_id);

            $send_email_to_admin = woffice_get_settings_option('register_new_user_email_to_admin');
            $who = ($send_email_to_admin) ? 'admin' : false;

            /**
             * Filter 'woffice_send_new_user_notifications_to'
             *
             * @param string $who
             * @param int $user_id
             *
             */
            $who = apply_filters( 'woffice_send_new_user_notifications_to', $who, $user_id );

            if ( $who == false ) {
                return;
            }

            wp_send_new_user_notifications( $user_id, $who );
        }

        /**
         * Validate password strength
         *
         * @param {string} $password
         * @return bool
         */
        public static function isPasswordValid($password)
        {
            $strong_pwd = woffice_get_settings_option('register_strong_password');

            $length_pwd = strlen($password) > 5;

            if ($strong_pwd === "yep") {
                $r1='/[A-Z]/';  //Uppercase
                $r2='/[a-z]/';  //lowercase
                $r3='/[!@#$%^&*()-_=+{};:,<.>]/';  // special char
                $r4='/[0-9]/';  //numbers

                $regexValid = ( preg_match_all($r1,$password, $o) && preg_match_all($r2,$password, $o)
                            && preg_match_all($r3,$password, $o)
                            && preg_match_all($r4,$password, $o));

                return ($length_pwd && $regexValid);

            } else {
                //Default - password valid if more than 5 characters
                return $length_pwd;

            }

        }

        /**
         * Load the password strength meter .js file
         */
        public function passwordStrengthAsset()
        {

            if(is_user_logged_in() || !function_exists('bp_core_get_js_dependencies'))
                return;

            $asset = get_template_directory_uri() . '/js/password-verify.min.js';

            $dependencies = array_merge( bp_core_get_js_dependencies(), array(
                'password-strength-meter',
            ) );

            wp_enqueue_script( 'bp-legacy-password-verify', $asset, $dependencies);

        }

	    /**
         * Whether an email is allowed to register to the intranet or not based on the Theme Options
         * This must be used after a registration allowed check
         *
	     * @param  string     $email
         * @return boolean
	     */
        public static function isEmailAllowed($email) {

	        $register_custom_domain_array = woffice_get_settings_option('register_custom_domain_array');

	        if (empty($register_custom_domain_array)) {
		        return true;
	        }

	        $email_array  = explode('@', $email);
	        $email_domain = $email_array[1];

	        return (in_array($email_domain, $register_custom_domain_array));

        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Register();