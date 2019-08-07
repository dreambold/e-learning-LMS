<?php
/*
Template Name: Courses List
*/

get_header(); 
?>

	<div id="left-content">

	<?php  //GET THEME HEADER CONTENT

		woffice_title(get_the_title()); ?>	

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
					<style>
					.relative {
						position: relative;
					}
					.relative h2 {
						max-width: 50%;
						font-size: 18px;
						margin-right: 10px;
						position: absolute;
						z-index: 2;
						bottom: 10px;
						left: 15px;
						background: rgba(255,255,255,0.7);
						padding: 5px;
						border: none;
						box-shadow: 0px 0px 20px 5px #000000;
					}
					.relative img {
						width: 100%;
    					height: auto;
					}
					.relative .btn.btn-default {
						position: absolute;
						bottom: 0;
						right: 0;
						margin-right: 15px !important;
						color: #000000 !important;
						padding: 5px !important;
						border: 0 !important;
    					border-radius: 0 !important;
						font-size: 16px !important;
					}
					.row-4 {
						width:32%;
						margin: 5px;
					}
					.row-12 {
						display: flex;
   						flex-wrap: wrap;
					}
					</style>
						<div class="row-12">

							<?php $courses = new WP_Query( array( 'post_type' => 'sfwd-courses', 'posts_per_page' => 24 ) ); ?>

							<?php while ( $courses->have_posts() ) : $courses->the_post(); ?>

							<div id="post-<?php the_ID(); ?>" class="col-4" <?php post_class(); ?>>

										<div class="relative">
											<h2 class=""><?php the_title(); ?></h2>
											<figure class="">
												<a class="" href="<?php the_permalink() ?>">
													<?php 
													if( has_post_thumbnail() ) { echo get_the_post_thumbnail( $page->ID); }
													else {
														echo '<img style="width:-webkit-fill-available;" src="'.get_template_directory_uri().'/images/default-placeholder.png" />';
													}
													 ?>
												</a>
											</figure>
											<button class="btn btn-default">Sign up now  &#9658;</button>
										</div>

							</div><!-- post -->
							<?php endwhile; ?>

						</div>

				<?php wp_reset_postdata(); ?> 

				<!-- THE NAVIGATION --> 
				<?php woffice_paging_nav(); ?>
			</div>
				
		</div><!-- END #content-container -->
		
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php 
get_footer();