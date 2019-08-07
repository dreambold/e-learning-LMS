<?php
/**
 * The Template for displaying all single directory item
 */

global $post;

$single_map             = woffice_get_directory_single_map();
$single_fields          = woffice_get_directory_single_fields('single');
$current_user_is_admin  = woffice_current_is_admin();
$edit_allowed           = (Woffice_Frontend::edit_allowed('directory') == true) ? true : false;
$delete_allowed         = (Woffice_Frontend::edit_allowed('directory', 'delete') == true) ? true : false;
if ($edit_allowed) {
	$process_result = Woffice_Frontend::frontend_process('directory', $post->ID);
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
					<?php $post_classes = array('box','content'); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
						
						<div class="intern-padding">
							
							<div class="fw-container">
								
								<div class="row">

                                    <?php if ( $single_map || $single_fields )
                                        echo '<div class="col-md-6">';
                                    ?>

										<?php /* The content + the excerpt */
                                            the_excerpt();
                                            ?>
										
										<?php /* The Button */
										$item_button_link = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'item_button_link') : '';
										if (!empty($item_button_link)) {
											$item_button_text = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'item_button_text') : '';
											$item_button_icon = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'item_button_icon') : '';
											$icon = (!empty($item_button_icon)) ? '<i class="'. woffice_convert_fa4_to_fa5($item_button_icon) .'"></i> ' : '';
											echo '<hr>';
											echo '<div class="text-center">';
												echo '<a href="'.$item_button_link.'" class="btn btn-default">'.$icon . $item_button_text.'</a>';
											echo '</div>';
										} ?>

                                    <?php if ( $single_map || $single_fields) {
                                        echo '</div>';
	                                    echo '<div class="col-md-6">';
                                    }
                                    ?>

										<?php echo woffice_get_directory_single_map(); ?>
										
										<?php echo woffice_get_directory_single_fields('single'); ?>

                                    <?php if ( $single_map || $single_fields) {
	                                    echo '</div>';
                                    }
                                    ?>
									
								</div>
									
								<?php /* Categories */
								if( has_term('', 'directory-category')): 
									echo '<span class="directory-category"><i class="fa fa-tag"></i>';
									echo get_the_term_list( $post->ID, 'directory-category', '', ', ' );
									echo '</span>';
								endif; ?>
								
							</div>
							
							<?php woffice_post_nav(); ?>
							
						</div>

					</article>

                    <div class="frontend-wrapper box">
                        <div class="intern-padding frontend-wrapper">

                            <div class="text-center" id="directory-bottom">

								<?php if ($edit_allowed) : ?>
                                    <a href="#" class="btn btn-default frontend-wrapper__toggle" data-action="display"><i class="fa fa-edit"></i> <?php _e("Edit Item", "woffice"); ?></a>
								<?php endif; ?>

                            </div>

							<?php if ($edit_allowed) : ?>
								<?php Woffice_Frontend::frontend_render('directory', $process_result, get_the_ID()); ?>
							<?php endif; ?>

                        </div>
                    </div>
					
					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>

				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->
	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();