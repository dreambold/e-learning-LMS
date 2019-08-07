<?php
/**
 * Template Name: Dashboard
 */

get_header(); 
?>

	<?php // Get layout's data :
	$dashboard_drag_drop = woffice_get_settings_option('dashboard_drag_drop'); ?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT
			woffice_title(get_the_title()); ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">

					<?php
                    /**
                     * Add something before the dashboard content. This is performed before the permission check also, so it will
                     * be displayed also if the users isn't allowed to display the dashboard page
                     */
					do_action('woffice_before_dashboard');
					?>
					
					<?php if ( is_active_sidebar( 'dashboard' ) && woffice_is_user_allowed() ) : ?>

						<?php
                        /**
                         * Add something before the dashboard content. This is performed after the permission check also, so it will
                         * be displayed only if the user is allowed to display the dashboard page.
                         */
                        do_action('woffice_before_dashboard_allowed'); ?>

						<?php
						$dashboard_columns = woffice_get_settings_option('dashboard_columns');
						$dashboard_columns_class = 'masonry-layout--' . $dashboard_columns . '-columns';

						$dashboard_drag_drop_class = (is_user_logged_in() && $dashboard_drag_drop == "yep") ? 'is-draggie' : 'is-fixed';
						?>
						<div id="dashboard" class="<?php echo  $dashboard_columns_class . ' ' . $dashboard_drag_drop_class?>">
							<?php // LOAD THE WIDGETS
							$user_custom_widgets = get_user_meta(get_current_user_id(), 'woffice_dashboard_order', true);
							if (is_user_logged_in() && !empty($user_custom_widgets) && $dashboard_drag_drop == "yep" && class_exists('Woffice_Dashboard')) :
								Woffice_Dashboard::woffice_dashboard_widgets($user_custom_widgets);
							else :
								dynamic_sidebar( 'dashboard' );
							endif;
							?>
						</div>

						<?php
                        /**
                         * Add something after the dashboard content. This is performed after the permission check also, so it will
                         * be displayed only if the user is allowed to display the dashboard page.
                         */
                         do_action('woffice_after_dashboard_allowed'); ?>
						
					<?php else: ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>

					<?php
                    /**
                     * Add something before the dashboard content. This is performed before the permission check also, so it will
                     * be displayed also if the users isn't allowed to display the dashboard page
                     */
                    do_action('woffice_after_dashboard'); ?>
					
				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();


