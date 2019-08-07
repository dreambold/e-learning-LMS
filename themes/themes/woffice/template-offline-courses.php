<?php
/*
Template Name: Offline Courses
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
								<th style="width:20%;">Seminar Title</th>
								<th style="width:20%;">Date</th>
								<th style="width:20%;">Free places</th>
								<th style="width:20%;">Meeting place</th>
								<th style="width:20%;"></th>
							</tr>

							<?php $webinars = new WP_Query( array( 'post_type' => 'offline-courses', 'posts_per_page' => 24 ) ); ?>

							<?php while ( $webinars->have_posts() ) : $webinars->the_post(); ?>
							<?php 
								$value_date = get_post_meta($post->ID, '_offline_date', true);
								$value_date_end = get_post_meta($post->ID, '_offline_date_end', true);
								$value_location = get_post_meta($post->ID, '_location_value', true);
								$value_adress = get_post_meta($post->ID, '_location_adress', true);
								$value_place = get_post_meta($post->ID, '_course_free_places', true);
								?>

							<tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<td><?php the_title(); ?></td>
								<td><?php if ($value_date != '') {echo 'From: '.$value_date. '<br>To: '.$value_date_end;} ?></td>
								<td><?php  if ($value_place != '') {echo $value_place;} ?></td>
								<td><?php  if ($value_location != '') {echo '<b>'.$value_location.'</b><br>'.$value_adress;} ?></td>
								<td><a class="btn btn-default" href="<?php the_permalink() ?>">More</a>
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