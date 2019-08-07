<?php

/**

* Plugin Name: Passwordless Login

* Plugin URI: http://www.cozmsolabs.com

* Description: Shortcode based login form. Enter an email/username and get link via email that will automatically log you in.

* Version: 1.0.7

* Author: Cozmoslabs, sareiodata

* Author URI: http:/www.cozmoslabs.com

* License: GPL2

* Text Domain: passwordless-login

* Domain Path: /languages

*/

/* Copyright Cozmoslabs.com



This program is free software; you can redistribute it and/or modify

it under the terms of the GNU General Public License, version 2, as

published by the Free Software Foundation.



This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the

GNU General Public License for more details.



You should have received a copy of the GNU General Public License

along with this program; if not, write to the Free Software

Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

*/



// Start writing code after this line!





/**

 * Definitions

 *

 *

 */

define( 'PASSWORDLESS_LOGIN_VERSION', '1.0.7' );

define( 'WPA_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );

define( 'WPA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



/**

 * Function that initiates the plugin text domain

 *

 * @since v.1.0.6

 *

 * @return void

 */

function wpa_load_plugin_textdomain(){

    load_plugin_textdomain( 'passwordless-login', false, WPA_PLUGIN_URL . '/languages/' );

}

add_action('init', 'wpa_load_plugin_textdomain');





/**

 * Function that creates the "Basic Information" submenu page

 *

 * @since v.2.0

 *

 * @return void

 */

function wpa_register_basic_info_submenu_page() {

	add_submenu_page( 'users.php', __( 'Passwordless Login', 'passwordless-login' ), __( 'Passwordless Login', 'passwordless-login' ), 'manage_options', 'passwordless-login', 'wpa_basic_info_content' );

}

add_action( 'admin_menu', 'wpa_register_basic_info_submenu_page', 2 );



/**

 * Function that adds content to the "Passwordless Auth" submenu page

 *

 * @since v.1.0

 *

 * @return string

 */

function wpa_basic_info_content() {

?>

	<div class="wrap wpa-wrap wpa-info-wrap">

		<div class="wpa-badge <?php echo PASSWORDLESS_LOGIN_VERSION; ?>"><?php printf( __( 'Version %s', 'passwordless-login' ), PASSWORDLESS_LOGIN_VERSION ); ?></div>

		<h1><?php printf( __( '<strong>Passwordless Login</strong> <small>v.</small>%s', 'passwordless-login' ), PASSWORDLESS_LOGIN_VERSION ); ?></h1>

		<p class="wpa-info-text"><?php printf( __( 'A front-end login form without a password.', 'passwordless-login' ) ); ?></p>

		<p><strong style="font-size: 30px; vertical-align: middle; color:#d54e21;"><?php echo get_option('wpa_total_logins', '0'); ?></strong> successful logins without passwords.</p>

		<hr />

		<h2 class="wpa-callout"><?php _e( 'One time password for WordPress', 'passwordless-login' ); ?></h2>

		<div class="wpa-row wpa-2-col">

			<div>

				<h3><?php _e( '[passwordless-login] shortcode', 'passwordless-login' ); ?></h3>

				<p><?php _e( 'Just place <strong class="nowrap">[passwordless-login]</strong> shortcode in a page or a widget and you\'re good to go.', 'passwordless-login' ); ?></p>

			</div>

			<div>

				<h3><?php _e( 'An alternative to passwords', 'passwordless-login'  ); ?></h3>

				<p><?php _e( 'Passwordless Authentication <strong>dose not</strong> replace the default login functionality in WordPress. Instead you can have the two work in parallel.', 'passwordless-login' ); ?></p>

				<p><?php _e( 'Join the discussion here: <a href="http://www.cozmoslabs.com/?p=31550&utm_source=wpbackend&utm_medium=link&utm_content=link&utm_campaign=passwordless">WordPress Passwordless Authentication</a>', 'passwordless-login' ); ?></p>

			</div>

		</div>

		<hr/>

		<div>

			<h3><?php _e( 'Take control of the login and registration process with Profile Builder', 'passwordless-login' );?></h3>

			<p><?php _e( 'Improve upon Passwordless Authentication using the free <a href="https://wordpress.org/plugins/profile-builder/">Profile Builder</a> plugin:', 'passwordless-login' ); ?></p>

			<div class="wpa-row wpa-3-col">

				<div><p><?php _e('Front-End registration, edit profile and login forms.', 'passwordless-login'); ?></p></div>

				<div><p><?php _e('Drag and drop to reorder / remove default user profile fields.', 'passwordless-login'); ?></p></div>

				<div><p><?php _e('Allow users to log in with their username or email.', 'passwordless-login'); ?></p></div>

				<div><p><?php _e('Enforce minimum password length and minimum password strength.', 'passwordless-login'); ?></p></div>

			</div>

			<p><a href="https://wordpress.org/plugins/profile-builder/" class="button button-primary button-large"><?php _e( 'Learn More About Profile Builder', 'passwordless-login' ); ?></a></p>

		</div>

	</div>

<?php

}





/**

 * Add scripts and styles to the back-end

 *

 * @since v.1.0

 *

 * @return void

 */

function wpa_print_script( $hook ){

	if ( ( $hook == 'users_page_passwordless-login' ) ){

		wp_enqueue_style( 'wpa-back-end-style', WPA_PLUGIN_URL . 'assets/style-back-end.css', false, PASSWORDLESS_LOGIN_VERSION );

	}

}

add_action( 'admin_enqueue_scripts', 'wpa_print_script' );



/**

 * Add scripts and styles to the front-end

 *

 * @since v.1.0

 *

 * @return void

 */

function wpa_add_plugin_stylesheet() {

	if (  file_exists( WPA_PLUGIN_DIR . '/assets/style-front-end.css' )  ){

		wp_register_style( 'wpa_stylesheet', WPA_PLUGIN_URL . 'assets/style-front-end.css' );

		wp_enqueue_style( 'wpa_stylesheet' );

	}

}

add_action( 'wp_print_styles', 'wpa_add_plugin_stylesheet' );



/**

 * Shortcode for the passwordless login form

 *

 * @since v.1.0

 *

 * @return html

 */

function wpa_front_end_login(){

	ob_start();

	$account = ( isset( $_POST['user_email_username']) ) ? $account = sanitize_text_field( $_POST['user_email_username'] ) : false;

	$nonce = ( isset( $_POST['nonce']) ) ? $nonce = sanitize_key( $_POST['nonce'] ) : false;

	$error_token = ( isset( $_GET['wpa_error_token']) ) ? $error_token = sanitize_key( $_GET['wpa_error_token'] ) : false;



	$sent_link = wpa_send_link($account, $nonce);



	if( $account && !is_wp_error($sent_link) ){

		echo '<p class="wpa-box wpa-success">'. apply_filters('wpa_success_link_msg', __('Please check your email. You will soon receive an email with a login link.', 'passwordless-login') ) .'</p>';

	} elseif ( is_user_logged_in() ) {

		$current_user = wp_get_current_user();

		echo '<p class="wpa-box wpa-alert">'.apply_filters('wpa_success_login_msg', sprintf(__( 'You are currently logged in as %1$s. %2$s', 'passwordless-login' ), '<a href="'.$authorPostsUrl = get_author_posts_url( $current_user->ID ).'" title="'.$current_user->display_name.'">'.$current_user->display_name.'</a>', '<a href="'.wp_logout_url( $redirectTo = wpa_curpageurl() ).'" title="'.__( 'Log out of this account', 'passwordless-login' ).'">'. __( 'Log out', 'passwordless-login').' &raquo;</a>' ) ) . '</p><!-- .alert-->';

	} else {

		if ( is_wp_error($sent_link) ){

			echo '<p class="wpa-box wpa-error">' . apply_filters( 'wpa_error', $sent_link->get_error_message() ) . '</p>';

		}

		if( $error_token ) {

			echo '<p class="wpa-box wpa-error">' . apply_filters( 'wpa_invalid_token_error', __('Your token has probably expired. Please try again.', 'passwordless-login') ) . '</p>';

		}



		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		//Setting up the label for the password request form based on the Allows Users to Login With Profile Builder Option

		if (is_plugin_active('profile-builder-pro/index.php') || is_plugin_active('profile-builder/index.php') || is_plugin_active('profile-builder-hobbyist/index.php')) {

			$wppb_general_options = get_option('wppb_general_settings');



			if ($wppb_general_options !== false) {

				if ($wppb_general_options['loginWith'] == 'email')

					$label = __('Login with email', 'passwordless-login') . '<br>';

				else if ($wppb_general_options['loginWith'] == 'username')

					$label = __('Login with username', 'passwordless-login') . '<br>';

				else

					$label = __('Login with email or username', 'passwordless-login');

			}

		}

		else

			$label = __('Login with email or username', 'passwordless-login');

		?>

	<form name="wpaloginform" id="wpaloginform" action="" method="post">

		<p>

			<label for="user_email_username"><?php echo( apply_filters('wpa_change_form_label', $label)) ; ?></label>

			<input type="text" name="user_email_username" id="user_email_username" class="input" value="<?php echo esc_attr( $account ); ?>" size="25" />

			<input type="submit" name="wpa-submit" id="wpa-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" />

		</p>

		<?php do_action('wpa_login_form'); ?>

		<?php wp_nonce_field( 'wpa_passwordless_login_request', 'nonce', false ) ?>



	</form>

<?php

	}



	$output = ob_get_contents();

	ob_end_clean();

	return $output;

}

add_shortcode( 'passwordless-login', 'wpa_front_end_login' );



add_filter('widget_text', 'do_shortcode');



/**

 * Checks to see if an account is valid. Either email or username

 *

 * @since v.1.0

 *

 * @return bool / WP_Error

 */

function wpa_valid_account( $account ){

	if( is_email( $account ) ) {

		$account = sanitize_email( $account );

	} else {

		$account = sanitize_user( $account );

	}



	if( is_email( $account ) && email_exists( $account ) ) {

		return $account;

	}



	if( ! is_email( $account ) && username_exists( $account ) ) {

		$user = get_user_by( 'login', $account );

		if( $user ) {

			return $user->data->user_email;

		}

	}



	return new WP_Error( 'invalid_account', __( 'The username or email you provided do not exist. Please try again.', 'passwordless-login' ) );

}



/**

 * Sends an email with the unique login link.

 *

 * @since v.1.0

 *

 * @return bool / WP_Error

 */

function wpa_send_link( $email_account = false, $nonce = false ){

	if ( $email_account  == false ){

		return false;

	}

	$valid_email = wpa_valid_account( $email_account  );

	$errors = new WP_Error;

	if (is_wp_error($valid_email)){

		$errors->add('invalid_account', $valid_email->get_error_message());

	} else{

		$blog_name = get_bloginfo( 'name' );

		$blog_name = esc_attr( $blog_name );



		//Filters to change the content type of the email

		add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));



		$unique_url = wpa_generate_url( $valid_email , $nonce );

		$subject = apply_filters('wpa_email_subject', sprintf(__("Login at %s", 'passwordless-login'), $blog_name));

		$message = apply_filters('wpa_email_message', 
		'<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="m_-8920630574609770550bodyTable" style="border-collapse:collapse;height:100%;margin:0;padding:0;width:100%;background-color:#d9d9d9">
		<tbody>
		   <tr>
			  <td align="center" valign="top" id="m_-8920630574609770550bodyCell" style="height:100%;margin:0;padding:0;width:100%;border-top:0">
				 <table border="0" cellpadding="0" cellspacing="0" width="600" class="m_-8920630574609770550flexibleContainer" style="border-collapse:collapse">
					<tbody>
					   <tr>
						  <td align="center" valign="top">
							 <table border="0" cellpadding="0" cellspacing="0" width="620" id="m_-8920630574609770550templatePreheader" style="border-collapse:collapse;background-color:#d9d9d9;border-top:0;border-bottom:0">
								<tbody>
								   <tr>
									  <td align="center" valign="top">
										 <table border="0" cellpadding="0" cellspacing="0" width="600" class="m_-8920630574609770550flexibleContainer" style="border-collapse:collapse">
											<tbody>
											   <tr>
												  <td valign="top" class="m_-8920630574609770550preheaderContainer" style="padding-bottom:0px">
													 <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnTextBlock" style="min-width:100%;border-collapse:collapse">
														<tbody class="m_-8920630574609770550mcnTextBlockOuter">
														   <tr>
															  <td valign="top" class="m_-8920630574609770550mcnTextBlockInner" style="padding-top:0px">
																 &nbsp;
															  </td>
														   </tr>
														</tbody>
													 </table>
												  </td>
											   </tr>
											</tbody>
										 </table>
									  </td>
								   </tr>
								</tbody>
							 </table>
						  </td>
					   </tr>
					   <tr>
						  <td align="center" valign="top" style="padding-bottom:40px">
							 <table border="0" cellpadding="0" cellspacing="0" width="620" id="m_-8920630574609770550templateContainer" style="background-color:#ffffff;border-collapse:collapse;border:0">
								<tbody>
								   <tr>
									  <td align="center" valign="top" style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px">
										 <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
											<tbody>
											   <tr>
												  <td align="center" valign="top">
													 <table border="0" cellpadding="0" cellspacing="0" width="600" id="m_-8920630574609770550templateUpperBody" style="border-collapse:collapse;background-color:#ffffff;border-top:0">
														<tbody>
														   <tr>
															  <td valign="middle" width="100%" class="m_-8920630574609770550bodyContainer">
																 <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnImageBlock" style="min-width:100%;border-collapse:collapse">
																	<tbody class="m_-8920630574609770550mcnImageBlockOuter">
																	   <tr>
																		  <td valign="top" style="padding:0px" class="m_-8920630574609770550mcnImageBlockInner">
																			 <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="m_-8920630574609770550mcnImageContentContainer" style="min-width:100%;border-collapse:collapse">
																				<tbody>
																				   <tr>
																					  <td class="m_-8920630574609770550mcnImageContent" valign="top" style="padding-right:0px;padding-left:0px;padding-top:0;padding-bottom:0;text-align:center">
																						 <img align="center" alt="" src="https://ci5.googleusercontent.com/proxy/Lc3SwvNaguUg7LstEZysfgAuXPpidFlB__S_ti_DeIh5kA5VvDUUG7b9GPSnfWgnNXRILX3KsrnOhjgHOMfhtvlpN4mW0VND1ZmaxYWu3TNBOq-nFhoYKaiRM_BdcrAT5qhECw1x3jcsIfU6afSC51xRyuYUVmg1Fgw=s0-d-e1-ft#https://gallery.mailchimp.com/edf7c23871b59a8fd6be73430/images/2e9c7311-24b8-4cfd-881b-e864be51c894.png" width="600" style="max-width:1200px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;height:auto;outline:none;text-decoration:none" class="m_-8920630574609770550mcnImage CToWUd a6T" tabindex="0">
																					  </td>
																				   </tr>
																				</tbody>
																			 </table>
																		  </td>
																	   </tr>
																	</tbody>
																 </table>
																 <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnDividerBlock" style="min-width:100%;border-collapse:collapse;table-layout:fixed!important">
																	<tbody class="m_-8920630574609770550mcnDividerBlockOuter">
																	   <tr>
																		  <td class="m_-8920630574609770550mcnDividerBlockInner" style="min-width:100%;padding:9px 18px 18px">
																			 <table class="m_-8920630574609770550mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
																				<tbody>
																				   <tr>
																					  <td>
																						 <span></span>
																					  </td>
																				   </tr>
																				</tbody>
																			 </table>
																		  </td>
																	   </tr>
																	</tbody>
																 </table>
															  </td>
														   </tr>
														</tbody>
													 </table>
												  </td>
											   </tr>
											   <tr>
												  <td align="center" valign="top">
													 <table border="0" cellpadding="0" cellspacing="0" width="600" id="m_-8920630574609770550templateLowerBody" style="border-collapse:collapse;background-color:#ffffff;border-bottom:0">
														<tbody>
														   <tr>
															  <td valign="middle" width="100%" class="m_-8920630574609770550bodyContainer">
																 <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnTextBlock" style="min-width:100%;border-collapse:collapse">
																	<tbody class="m_-8920630574609770550mcnTextBlockOuter">
																	   <tr>
																		  <td valign="top" class="m_-8920630574609770550mcnTextBlockInner" style="padding-top:9px">
																			 <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%;min-width:100%;border-collapse:collapse" width="100%" class="m_-8920630574609770550mcnTextContentContainer">
																				<tbody>
																				   <tr>
																					  <td valign="top" class="m_-8920630574609770550mcnTextContent" style="padding-top:0;padding-right:18px;padding-bottom:9px;padding-left:18px;word-break:break-word;color:#000;font-family:Helvetica;font-size:15px;line-height:150%;text-align:left">
																						 <strong>Lieber Teilnehmer,</strong><br>Vielen Dank für Ihr Interesse an dem Informations- und Schulungsportal der Opel Bank. Mit Klick auf folgenden Link erhalten Sie Zugang:<br><br>
																					  </td>
																				   </tr>
																				</tbody>
																			 </table>
																		  </td>
																	   </tr>
																	</tbody>
																 </table>
																 <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnButtonBlock" style="min-width:100%">
																	<tbody class="m_-8920630574609770550mcnButtonBlockOuter">
																	   <tr>
																		  <td style="padding-top:0;padding-right:18px;padding-bottom:18px;padding-left:18px" valign="top" align="center" class="m_-8920630574609770550mcnButtonBlockInner">
																			 <table border="0" cellpadding="0" cellspacing="0" class="m_-8920630574609770550mcnButtonContentContainer" style="border-collapse:separate!important;border-radius:0px;background-color:#ffd700">
																				<tbody>
																				   <tr>
																					  <td align="center" valign="middle" class="m_-8920630574609770550mcnButtonContent" style="font-family:Helvetica;font-size:18px;padding:15px 30px">
																						 '.
		sprintf(__('<a href="%s" target="_blank">Zugang zum Portal</a>', 'passwordless-login'), esc_url($unique_url), esc_url($unique_url)),$unique_url, $valid_email).
		'</td>
		</tr>
	 </tbody>
  </table>
</td>
</tr>
<tr>
<td valign="top" class="m_-8920630574609770550mcnTextContent" style="padding-top:0;padding-right:18px;padding-bottom:9px;padding-left:18px;word-break:break-word;color:#000;font-family:Helvetica;font-size:15px;line-height:150%;text-align:left">
  <p style="font-family:Helvetica;font-size:15px">Bitte beachten Sie, dass der Link etwa eine Stunde gültig ist. Anschließend muss der Zugang erneut angefordert werden.</p>
  <p style="font-family:Helvetica;font-size:15px"><br><strong>Sie haben Fragen?</strong><br>
	 Zum Informations- &amp; Schulungssportal: 09171-8523824<br>
	 Zum Wholesale-Online Programm:	06142-22 07 003<br>
	 E-Mail:	<a href="mailto:info@training.opelbank.de" target="_blank">info@training.opelbank.de</a>
  </p>
</td>
</tr>
</tbody>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnDividerBlock" style="min-width:100%;border-collapse:collapse;table-layout:fixed!important">
<tbody class="m_-8920630574609770550mcnDividerBlockOuter">
<tr>
<td class="m_-8920630574609770550mcnDividerBlockInner" style="min-width:100%;padding:9px 18px">
  <table class="m_-8920630574609770550mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
	 <tbody>
		<tr>
		   <td>
			  <span></span>
		   </td>
		</tr>
	 </tbody>
  </table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="600" id="m_-8920630574609770550templateFooter" style="border-collapse:collapse;background-color:#000;border-top:10px solid #ffffff;border-bottom:0">
<tbody>
<tr>
<td valign="top" class="m_-8920630574609770550footerContainer" style="padding-top:9px;padding-bottom:9px">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_-8920630574609770550mcnTextBlock" style="min-width:100%;border-collapse:collapse">
<tbody class="m_-8920630574609770550mcnTextBlockOuter">
<tr>
<td valign="top" class="m_-8920630574609770550mcnTextBlockInner" style="padding-top:9px">
  <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%;min-width:100%;border-collapse:collapse" width="100%" class="m_-8920630574609770550mcnTextContentContainer">
	 <tbody>
		<tr>
		   <td valign="top" class="m_-8920630574609770550mcnTextContent" style="padding-top:0;padding-right:18px;padding-bottom:9px;padding-left:18px;word-break:break-word;color:#d7d7d7;font-family:Helvetica;font-size:11px;line-height:125%;text-align:left">
			  <strong>IMPRESSUM</strong><span class="HOEnZb"><font color="#888888"><br><br>
			  Opel Bank GmbH<br>
			  Internetseite: <a href="https://training.opelbank.de" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://training.opelbank.de&amp;source=gmail&amp;ust=1559892903973000&amp;usg=AFQjCNGrSbbTkDcq4QgxXoMp1YqEyjkJ2Q">training.opelbank.de</a><br>
			  Pflichtangaben: <a href="https://www.opelbank.de/impressum/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.opelbank.de/impressum/&amp;source=gmail&amp;ust=1559892903973000&amp;usg=AFQjCNGdW4IufzKxoSpaOrZ2NxRaIfe7wQ">www.opelbank.de/impressum</a><br>
			  Datenschutzbestimmungen: <a href="https://www.opelbank.de/datenschutz/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.opelbank.de/datenschutz/&amp;source=gmail&amp;ust=1559892903973000&amp;usg=AFQjCNHmIqjub4dXqj6b3veCoH_xeTNmgA">www.opelbank.de/datenschutz</a><br>
			  Postanschrift: K65/PKZ 98-01, Mainzer Straße 190, 65428 Rüsselsheim
			  </font></span>
		   </td>
		</tr>
	 </tbody>
  </table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>';
		$sent_mail = wp_mail( $valid_email, $subject, $message );



		if ( !$sent_mail ){

			$errors->add('email_not_sent', __('There was a problem sending your email. Please try again or contact an admin.', 'passwordless-login'));

		}

	}

	$error_codes = $errors->get_error_codes();



	if (empty( $error_codes  )){

		return false;

	}else{

		return $errors;

	}

}



/**

 * Generates unique URL based on UID and nonce

 *

 * @since v.1.0

 *

 * @return string

 */

function wpa_generate_url( $email = false, $nonce = false ){

	if ( $email  == false ){

		return false;

	}

	/* get user id */

	$user = get_user_by( 'email', $email );

	$token = wpa_create_onetime_token( 'wpa_'.$user->ID, $user->ID  );



	$arr_params = array( 'wpa_error_token', 'uid', 'token', 'nonce' );

	$url = remove_query_arg( $arr_params, wpa_curpageurl() );



    $url_params = array('uid' => $user->ID, 'token' => $token, 'nonce' => $nonce);

    $url = add_query_arg($url_params, $url);



	return $url;

}



/**

 * Automatically logs in a user with the correct nonce

 *

 * @since v.1.0

 *

 * @return string

 */

add_action( 'init', 'wpa_autologin_via_url' );

function wpa_autologin_via_url(){

	if( isset( $_GET['token'] ) && isset( $_GET['uid'] ) && isset( $_GET['nonce'] ) ){

		$uid = sanitize_key( $_GET['uid'] );

		$token  =  sanitize_key( $_REQUEST['token'] );

		$nonce  = sanitize_key( $_REQUEST['nonce'] );



		$hash_meta = get_user_meta( $uid, 'wpa_' . $uid, true);

		$hash_meta_expiration = get_user_meta( $uid, 'wpa_' . $uid . '_expiration', true);

		$arr_params = array( 'uid', 'token', 'nonce' );

		$current_page_url = remove_query_arg( $arr_params, wpa_curpageurl() );



		require_once( ABSPATH . 'wp-includes/class-phpass.php');

		$wp_hasher = new PasswordHash(8, TRUE);

		$time = time();



		if ( ! $wp_hasher->CheckPassword($token . $hash_meta_expiration, $hash_meta) || $hash_meta_expiration < $time || ! wp_verify_nonce( $nonce, 'wpa_passwordless_login_request' ) ){

			wp_redirect( $current_page_url . '?wpa_error_token=true' );

			exit;

		} else {

			wp_set_auth_cookie( $uid );

			delete_user_meta($uid, 'wpa_' . $uid );

			delete_user_meta($uid, 'wpa_' . $uid . '_expiration');



			$total_logins = get_option( 'wpa_total_logins', 0);

			update_option( 'wpa_total_logins', $total_logins + 1);

			wp_redirect( $current_page_url );

			exit;

		}

	}

}



/**

 * Create a nonce like token that you only use once based on transients

 *

 *

 * @since v.1.0

 *

 * @return string

 */

function wpa_create_onetime_token( $action = -1, $user_id = 0 ) {

	$time = time();



	// random salt

	$key = wp_generate_password( 20, false );



	require_once( ABSPATH . 'wp-includes/class-phpass.php');

	$wp_hasher = new PasswordHash(8, TRUE);

	$string = $key . $action . $time;



	// we're sending this to the user

	$token  = wp_hash( $string );

	$expiration = apply_filters('wpa_change_link_expiration', $time + 60*10);

	$expiration_action = $action . '_expiration';



	// we're storing a combination of token and expiration

	$stored_hash = $wp_hasher->HashPassword( $token . $expiration );



	update_user_meta( $user_id, $action , $stored_hash ); // adjust the lifetime of the token. Currently 10 min.

	update_user_meta( $user_id, $expiration_action , $expiration );

	return $token;

}



/**

 * Returns the current page URL

 *

 * @since v.1.0

 *

 * @return string

 */

function wpa_curpageurl() {

    $req_uri = $_SERVER['REQUEST_URI'];



    $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );

    $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );



    // Trim path info from the end and the leading home path from the front.

    $req_uri = ltrim($req_uri, '/');

    $req_uri = preg_replace( $home_path_regex, '', $req_uri );

    $req_uri = trim(home_url(), '/') . '/' . ltrim( $req_uri, '/' );



    return $req_uri;

}





/**

 * Add notices on plugin activation.

 *

 * @since v.1.0

 *

 * @return string

 */



include_once("inc/wpa.class.notices.php");

$learn_more_notice = new WPA_Add_Notices(

	'wpa_learn_more',

	sprintf( __( '<p>Use [passwordless-login] shortcode in your pages or widgets. %1$sLearn more.%2$s  %3$sDismiss%4$s</p>', 'passwordless-login'), "<a href='users.php?page=passwordless-login&wpa_learn_more_dismiss_notification=0'>", "</a>", "<a href='". add_query_arg( 'wpa_learn_more_dismiss_notification', '0' ) ."' class='wpa-dismiss-notification' style='float:right;margin-left:20px;'> ", "</a>" ),

	'updated',	'',	''

);

