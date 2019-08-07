<?php
/**
* Template Name: Login
*/

// If the user is already logged we redirect him back to the login page
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

// We get the logo image
$login_logo_image = woffice_get_settings_option('login_logo_image');

// We save the classes in an array
$classes = array();

// Second landing :
$login_layout = woffice_get_settings_option('login_layout');
array_push($classes, $login_layout);

// Design version
$design_update = woffice_get_settings_option('design_update');
$design_update_class = ($design_update == "2.X") ? "woffice-2-5" : "";

/**
 * Filter to change the design version
 *
 * @param string $design_update_class - you can use "woffice-2-x"
 */
$design_update_class = apply_filters('woffice_design_version', $design_update_class);
array_push($classes, $design_update_class);

?>
<html <?php language_attributes(); ?> style="margin-top: 0 !important;">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<!-- MAKE IT RESPONSIVE -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php // GET FAVICONS
		woffice_favicons();
		?>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/flexie.min.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>
	
	<body <?php body_class($classes); ?>>
	
		<div id="page-wrapper">
			<div id="content-container">

				<?php // Check for Revolution Slider :
				$login_revslider = woffice_get_settings_option('login_revslider'); ?>
	
				<!-- START CONTENT -->
				<section id="woffice-login" class="<?php echo (!empty($login_revslider) ? 'revslider-enabled' : 'revslider-disabled'); ?>">
				
					<div id="woffice-login-left">

						<?php // Revslider :
						if (!empty($login_revslider) && shortcode_exists('rev_slider')) :
							putRevSlider($login_revslider);
						endif; ?>

					</div>
					
					<div id="woffice-login-right">

						<header>
							<?php if (!empty($login_logo_image)) : ?>
								<a href="<?php echo home_url(); ?>" id="login-logo"><img src='<?php echo esc_url($login_logo_image["url"]); ?>'/></a>
							<?php endif; ?>
							
							<?php woffice_login_render_errors(); ?>
							
							<?php // Login Text
							$login_text = woffice_get_settings_option('login_text'); 
							if (!empty($login_text)): ?>

								<p><?php echo $login_text; ?></p>

							<?php endif; ?>

                            <?php if (defined('WOFFICE_CORE_ENABLED')): ?>
							    <?php woffice_login_social_render(); ?>
                            <?php endif; ?>
							
						</header>

                        <div class="login-tabs-wrapper">

                            <?php // Checking what form to display
                            $type = (isset($_GET['type'])) ? $_GET['type'] : "";
                            if ($type == "lost-password") {

                                woffice_login_render_lost_password();

                            } elseif ($type == "reset-password") {

                                woffice_login_render_reset_password();

                            } else {

                                woffice_login_render_form();

                            }
                            ?>

                            <?php woffice_login_render_register(); ?>

                        </div>



						<?php woffice_login_render_footer(); ?>

					</div>
					
				</section>
				<!-- END CONTENT -->
				
			</div>
		</div>
		
		<?php wp_footer(); ?>

		<?php woffice_login_render_script(); ?>

	</body>
</html>