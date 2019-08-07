<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */
get_header();
global $post;
?>

<style>
	.sfwd_help_text_link {
		background: #00215fb8;
	}
	.sfwd_option_div textarea, .sfwd_option_div select, .sfwd_option_div input {
		margin: 0 0 50px 0 !important;
	}
	input[type=checkbox], input[type=radio] {
		margin: 10px 0 !important;
		width: 50px;
    	height: 50px;
	}
</style>






	<div id="left-content">

		<?php //GET THEME HEADER CONTENT
			woffice_title(get_the_title()); ?> 	
			
		<?php // Start the Loop.
		while (have_posts()):
			the_post(); ?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">
			
			<div id="content" style="top:0;">

				<!-- START CONTENT -->
				<form id="featured_upload" method="post" enctype="multipart/form-data">
					<input type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />
					<?php wp_nonce_field('my_image_upload', 'my_image_upload_nonce'); ?>
					<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Upload" />
				</form>
				<?php
					require_once ABSPATH . 'wp-admin/includes/media.php';
					require_once ABSPATH . 'wp-admin/includes/file.php';
					require_once ABSPATH . 'wp-admin/includes/image.php';
					$attachment_id = media_handle_upload('my_image_upload', $_POST['id']);
					if (is_wp_error($attachment_id)) {
						echo $attachment_id->get_error_message();
					} else {
						$post_id = get_post($attachment_id);
						$guid = $post_id->guid;
						echo '<img class="thumb" src="' . $guid . '"/>';
						// echo '<input type="hidden" name="attachment_id" id="attachment_id" value="'.$attachment_id.'" />';
						
					}
				?>
			<?php
			if (get_the_title() == 'Add Course') {
                include_once( plugin_dir_path( __FILE__ ) . 'includes/course.php' );
            }
			else if (get_the_title() == 'Add Lesson') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/lesson.php' );				
			}
			elseif (get_the_title() == 'Add Topic') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/topic.php' );				
			}
			elseif (get_the_title() == 'Add Quizz') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/quizz.php' );				
			}
			elseif (get_the_title() == 'Add Question') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/question.php' );				
			}
			elseif (get_the_title() == 'Create Webinar') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/webinar.php' );				
			}
			elseif (get_the_title() == 'Create VOR-ORT SCHULUNGEN') {
				include_once( plugin_dir_path( __FILE__ ) . 'includes/offline.php' );				
			}
			?>		
			</div>
			<script>
				$(document).ready(function() {

					$('ul.tabs li').click(function() {
						var tab_id = $(this).attr('data-tab');

						$('ul.tabs li').removeClass('current');
						$('.tab-content').removeClass('current');

						$(this).addClass('current');
						$("#" + tab_id).addClass('current');
					})

				})
			</script>		
		</div><!-- END #content-container -->
		
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php // END THE LOOP
    
endwhile; ?>

<?php
get_footer();
