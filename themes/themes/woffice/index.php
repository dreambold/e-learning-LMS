<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */
 

// CHECK IF USER CAN CREATE BLOG POST
$post_create = woffice_get_settings_option('post_create');
$woffice_role_allowed = Woffice_Frontend::role_allowed($post_create, 'post');
if ($woffice_role_allowed):

	$frontend_process = Woffice_Frontend::frontend_process('post');

endif;

get_header(); 
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
		$title = woffice_get_settings_option('index_title');
		woffice_title($title); ?> 	

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				
				<?php // We check for the layout
				$blog_layout = 'classic';
				if (get_post_type() === 'post') {

					$blog_layout           = woffice_get_settings_option( 'blog_layout' );
					$masonry_columns       = woffice_get_settings_option( 'masonry_columns' );
					$masonry_columns_class = 'masonry-layout--' . $masonry_columns . '-columns';
				}

				echo ($blog_layout === "masonry" || isset($_GET['blog_masonry'])) ? '<div id="directory" class="masonry-layout '.$masonry_columns_class.'">' : ''; ?>
				
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php // We check for the role :
							if (woffice_is_user_allowed()) { ?>
								<?php if (($blog_layout == "masonry")) {
									get_template_part( 'content-masonry' );
								}
								else {
									get_template_part( 'content' );
								} ?>
							<?php } ?>
						<?php endwhile; ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
					
				<?php echo ($blog_layout == "masonry" || isset($_GET['blog_masonry'])) ? '</div>' : ''; ?>

				<!-- THE NAVIGATION --> 
				<?php woffice_paging_nav(); ?>
				
				<?php
				/*
				 * FRONT END CREATION
				 */ 
				// CHECK IF USER CAN CREATE BLOG POST
				if ($woffice_role_allowed): ?>
					
					<div class="frontend-wrapper box">
						<div class="intern-padding">
					
							<div class="text-center" id="blog-bottom">

								<?php
                                /**
                                 * Text of the button "Publish a new article" in the posts loop page
                                 *
                                 * @param string
                                 */
                                $new_blog_button_text = apply_filters('woffice_new_blog_article_button_text', __("New Blog Article", "woffice")); ?>
								<?php echo'<a href="#" class="btn btn-default frontend-wrapper__toggle" data-action="display" id="show-blog-create"><i class="fa fa-plus-square"></i> '. $new_blog_button_text .'</a>'; ?>
								
							</div>
							
							<?php Woffice_Frontend::frontend_render('post',$frontend_process); ?>
														
						</div>
					</div>
				
				<?php endif; ?>
			</div>
				
		</div><!-- END #content-container -->

		<?php woffice_scroll_top(); ?>
		
	</div><!-- END #left-content -->

<?php 
get_footer(); 