<?php
/**
* Archive page for the project
*/
get_header(); 
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title =  $term->name . __(' Archives','woffice'); 
		woffice_title($title); ?> 

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">

				<div id="projects-page-content" class="box content">
					<div class="intern-padding">
						<?php
						// Thee project filter
						woffice_projects_filter();
						?>
					</div>
				</div>

				<?php /* If the directory extension is one we display the items */
				if(function_exists('woffice_projects_extension_on')){

					if ( have_posts() ) :

						// We check for the layout
						$projects_layout = woffice_get_settings_option('projects_layout');
						$projects_layout_class = '';
						if($projects_layout == "masonry") {
							$projects_layout_class = 'masonry-layout';
							$masonry_columns = woffice_get_settings_option('projects_masonry_columns');

							$projects_layout_class .= ' masonry-layout--'.$masonry_columns.'-columns';
						}

						echo'<ul id="projects-list" class="'.$projects_layout_class.'">';

						while(have_posts()) : the_post();

							if (woffice_is_user_allowed_projects()) :

								get_template_part('template-parts/content', 'project');

							endif;

						endwhile;

						wp_reset_postdata();

						echo '</ul>';
                        woffice_paging_nav();
					endif;

				} else {

					get_template_part( 'content', 'none' );

				} ?>

				
			</div>
				
		</div><!-- END #content-container -->
	
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php 
get_footer();
