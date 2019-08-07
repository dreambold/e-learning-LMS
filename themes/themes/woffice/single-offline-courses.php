<?php
/**
 * The Template for displaying all single posts
 */

global $post;

$current_user_is_admin  = woffice_current_is_admin();
$edit_allowed           = (Woffice_Frontend::edit_allowed('offline-courses') == true) ? true : false;
$delete_allowed         = (Woffice_Frontend::edit_allowed('offline-courses', 'delete') == true) ? true : false;
if ($edit_allowed) {
	$process_result = Woffice_Frontend::frontend_process('offline-courses', $post->ID);
}

get_header(); ?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT

			woffice_title(get_the_title()); ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">
					<?php // We check for the role : 
					if (woffice_is_user_allowed()) { ?>
						
						<?php // Include the page content template.
						get_template_part( 'content');
						?>
						
						<?php
						/*
						 * FRONT END EDIT
						 */
						if ($edit_allowed || $delete_allowed) { ?>
							
							<div class="frontend-wrapper box">
								<div class="intern-padding">
							
									<div class="text-center" id="blog-bottom">
									
										<?php if($edit_allowed) : ?>
                                            <a class="btn btn-default frontend-wrapper__toggle" data-action="display"><i class="fa fa-pencil-alt"></i> <?php _e("Edit Post", "woffice"); ?></a>
                                        <?php endif; ?>

										<?php
										/**
                                         * Delete Button
                                         * From version 1.8.6 if an user is allowed to edit then is allowed also to delete
										 * if (is_user_logged_in() && (current_user_can('edit_others_posts') || $current_user->ID == $post->post_author) ) {
                                         */
										if($delete_allowed) {
                                            echo '<a onclick="return confirm(\'' . __('Are you sure you wish to delete article :', 'woffice') . ' ' . get_the_title() . ' ?\')" href="' . get_delete_post_link(get_the_ID(), '') . '" class="btn btn-secondary">
												<i class="fa fa-trash"></i> ' . __("Delete", "woffice") . '
											</a>';
                                        }
										//}
										 ?>

									</div>

                                    <?php if($edit_allowed) : ?>
                                        <?php Woffice_Frontend::frontend_render('offline-courses', $process_result, get_the_ID()); ?>
                                    <?php endif; ?>
									
								</div>
							</div>
						
						<?php } ?>
					
					
						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}
						?>
					
					<?php } else { 
						get_template_part( 'content', 'private' );
					} ?>

				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->
		
	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();