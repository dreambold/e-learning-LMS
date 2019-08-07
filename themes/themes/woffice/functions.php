<?php 
/**
 * Theme Includes
 */
require_once get_template_directory() .'/inc/init.php';
require_once get_template_directory() .'/inc/users-meta-box.php';
// Set up Cutsom BP navigation
function my_setup_nav() {
      global $bp;

      bp_core_new_nav_item( array( 
            'name' => __( 'Statistic', 'buddypress' ), 
            'slug' => 'statistic', 
            'position' => 30,
            'screen_function' => 'statistic', 
      ) );
}

add_action( 'bp_setup_nav', 'my_setup_nav' );

// Load a page template for your custom item. You'll need to have an page-statistic.php in your theme root.
function statistic() {
      bp_core_load_template( 'statistic' );
}

function wph_disable_email_domain($errors, $sanitized_user_login, $user_email){
      list($email_user, $email_domain) = explode('@', $user_email);
      if ($email_domain == 'mail.ru' || $email_domain == 'rambler.ru' || $email_domain == 'gmail.com') {
          $errors->add('email_error', '<strong> ERROR </ strong>: Sorry, but
                     registration of users with mailbox '.$email_domain.' 
                     is prohibited.');
      }
      return $errors;
  }
  add_filter('registration_errors', 'wph_disable_email_domain', 10, 3);


  add_filter('show_admin_bar', '__return_false');
