<?php
/**
* Template Name: Projects
*/

$process_result = array();

if (function_exists( 'woffice_projects_extension_on' )){

	$projects_create = woffice_get_settings_option('projects_create'); 				
	if (Woffice_Frontend::role_allowed($projects_create)):
	
		$process_result = Woffice_Frontend::frontend_process('project');
		
	endif;
	
}

get_header();
?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT

			woffice_title(get_the_title()); ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">
					<?php if (woffice_is_user_allowed()) { ?>
						<?php
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');
						?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <?php if ( woffice_project_content_exists() ): ?>
                                <div id="projects-page-content" class="box content">
                                    <div class="intern-padding">
                                        <?php
                                        // THE PAGE CONTENT
                                        the_content();
                                        //DISABLED IN THIS THEME
                                        wp_link_pages(array('echo'  => 0));
                                        //EDIT LINK
                                        edit_post_link( __( 'Edit', 'woffice' ), '<span class="edit-link">', '</span>' );

                                        // Thee project filter
                                        if (function_exists( 'woffice_projects_extension_on' )) {
                                            woffice_projects_filter();
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>

						<!-- LOOP ALL THE PROJECTS-->
						<?php // GET POSTS
						if (function_exists( 'woffice_projects_extension_on' )){

							$project_query_args = woffice_get_projects_loop_query_args();

							$project_query = new WP_Query($project_query_args);

							$project_query->posts = woffice_sort_projects_by_completion_date( $project_query->posts );

							if ( $project_query->have_posts() ) :

								// We check for the layout
								$projects_layout = woffice_get_settings_option('projects_layout');
								
								$projects_layout_class = '';
								if($projects_layout == "masonry") {
									$projects_layout_class = 'masonry-layout';
									$masonry_columns = woffice_get_settings_option('projects_masonry_columns');

									$projects_layout_class .= ' masonry-layout--'.$masonry_columns.'-columns';
								}

								echo'<ul id="projects-list" class="'. $projects_layout_class .'">';
								// LOOP
								while($project_query->have_posts()) : $project_query->the_post();

									get_template_part('template-parts/content', 'project');
								
								endwhile;
								echo '</ul>';
                                woffice_paging_nav($project_query);
							else :
								get_template_part( 'content', 'none' );
							endif;
							wp_reset_postdata();


							// CHECK IF USER CAN CREATE PROJECT POST
							$projects_create = woffice_get_settings_option('projects_create');
							if (Woffice_Frontend::role_allowed($projects_create)): ?>

                                <div class="frontend-wrapper box intern-padding">

                                    <div class="center content" id="projects-bottom">
                                        <?php
                                        /**
                                         * Filter the text of the button "Create a project"
                                         *
                                         * @param string
                                         */
                                        $new_project_button_text = apply_filters('woffice_new_project_button_text', __("Create a project", "woffice")); ?>
                                        <a href="javascript:void(0)" class="btn btn-default frontend-wrapper__toggle" data-action="display" id="show-project-create"><i class="fa fa-plus-square"></i> <?php echo $new_project_button_text; ?></a>
                                    </div>

                                    <?php Woffice_Frontend::frontend_render('project', $process_result); ?>

                                </div>

							<?php endif;

						 }?>

					<?php
					} else { 
						get_template_part( 'content', 'private' );
					}
					?>
					</div>
				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();



