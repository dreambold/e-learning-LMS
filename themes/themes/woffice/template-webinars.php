<?php
/*
Template Name: Webinars
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
					th {
						padding: 25px 10px;
					}
					td {
						padding: 0 10px;
					}
					table {
						box-shadow: 0px 0px 15px 10px rgba(0, 0, 0, 0.28);
					}
					.table-header {
						background:linear-gradient(-30deg, #ecd417 , #ffe82b);
					}
					tr:nth-child(odd) {
						background-color: #f3f3f3;
					}
					tr:nth-child(even) {
						background-color: #fff;
					}
					</style>
						<table>
							<caption>
							</caption>
							<tr class="table-header">
								<th style="width:40%;">Webinar</th>
								<th style="width:15%;">Date</th>
								<th style="width:15%;"></th>
								<th style="width:10%;">Free places</th>
								<th style="width:20%;"></th>
							</tr>

							<?php $webinars = new WP_Query( array( 'post_type' => 'webinars', 'posts_per_page' => 24 ) ); ?>

							<?php while ( $webinars->have_posts() ) : $webinars->the_post(); ?>

							<tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<td><?php the_title(); ?></td>
								<?php 
									$value_start_date = get_post_meta($post->ID, '_webinar_date', true);
									$value_end_date = get_post_meta($post->ID, '_webinar_date_end', true);
									$value_place = get_post_meta($post->ID, '_free_places', true); 
								?>
								<td><?php if ($value_start_date != '') {echo 'From: '.$value_start_date;} ?></td>
								<td><?php  if ($value_end_date != '') {echo 'To: '.$value_end_date;} ?></td>
								<td><?php echo $value_place; ?></td>
								<td><a class="btn btn-default" href="<?php the_permalink() ?>">More</a>
								<form style="margin:0;float: right;" method="POST" action="<?php echo get_template_directory_uri() ?>/reg-on-webinar.php" >
								<input type="hidden" id="user_id" name="user_id" value="<?php echo get_current_user_id(); ?>">
								<input class="button" type="submit" value="Sign up now" />
								</form>
								<?php if( current_user_can( 'delete_posts' ) ) {
	echo '<a  class="btn btn-default" href="'. get_delete_post_link( $post->ID) .'">Delete</a>';}?>				
							</td>
							</tr><!-- post -->
							<?php endwhile; ?>

						</table>

				<?php wp_reset_postdata(); ?> 

				<!-- THE NAVIGATION --> 
				<?php woffice_paging_nav(); ?>
			</div>
				
		</div><!-- END #content-container -->
		
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php 
get_footer();