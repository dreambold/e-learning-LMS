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

		<?php  //GET THEME HEADER CONTENT

		woffice_title(get_the_title()); ?> 	
			
		<?php // Start the Loop.
		while ( have_posts() ) : the_post(); ?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">
			<div id="content" style="top:0;">

			<!-- START CONTENT -->
			<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">
				<input type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />
				<?php wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' ); ?>
				<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Upload" />
			</form>
			<?php
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';


				$attachment_id = media_handle_upload( 'my_image_upload', $_POST['id'] );

				if( is_wp_error($attachment_id) ){
					echo $attachment_id->get_error_message();
				}
				else {
					echo "The file has been loaded successfully!";
					$post_id = get_post( $attachment_id );
					$guid = $post_id->guid;
					echo '<img class="thumb" src="'.$guid.'" alt="'.the_title().'" />';
					// echo '<input type="hidden" name="attachment_id" id="attachment_id" value="'.$attachment_id.'" />';
				}

				?>	


			<form id="featured_upload" method="POST" action="<?php echo get_site_url('','wp-content/plugins/courses-maker/save-lessons.php'); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
				<div class="add-header">
					<h1><input id="add_title" name="add_title" title="Title" type="text" autocomplete="on"  placeholder="Title"></h1>
				</div>
				<input type="hidden" id="id" name="id" value="<?php // echo get_the_ID();?>">
				<input type="hidden" id="attachment_id" name="attachment_id" value="<?php if (!is_wp_error($attachment_id)) {echo $attachment_id;}?>">
				<input type="hidden" id="add_user_id" name="add_user_id" value="<?php echo get_current_user_id();?>">
				<input type="hidden" id="add_date" name="add_date" value="<?php echo current_time("Y-m-d H:i:s");?>">
				<input type="hidden" id="add_date_gmt" name="add_date_gmt" value="<?php echo get_gmt_from_date(current_time("Y-m-d H:i:s"));?>">
				<input type="hidden" id="add_status" name="add_status" value="publish">
				<input type="hidden" id="add_comment" name="add_comment" value="closed">
				<input type="hidden" id="add_ping" name="add_ping" value="closed">
				<input type="hidden" id="add_password" name="add_password" value="">
				<input type="hidden" id="add_date" name="add_modified" value="<?php echo current_time("Y-m-d H:i:s");?>">
				<input type="hidden" id="add_date_gmt" name="add_modified_gmt" value="<?php echo get_gmt_from_date(current_time("Y-m-d H:i:s"));?>">
				<input type="hidden" id="add_parent" name="add_parent" value="">
				<input type="hidden" id="site_url" name="site_url" value="<?php echo get_site_url() ?>">
				<input type="hidden" id="add_menu_order" name="add_menu_order" value="0">
				<input type="hidden" id="add_post_type" name="add_post_type" value="sfwd-lessons">
				<input type="hidden" id="add_comment_count" name="add_comment_count" value="0">
				<div id="primary" class="content-area">
					<div class="sfwd sfwd_options sfwd-courses_settings">
						<h2>Content</h2>
						<?php
						$args = array(
							'wpautop' => 1,
							'media_buttons' => 1,
							'textarea_rows' => 10,
							'tabindex' => 0,
							'editor_css' => '',
							'editor_class' => '',
							'teeny' => 0,
							'dfw' => 0,
							'tinymce' => 1,
							'quicktags' => 0  
						);
						wp_editor('', 'add_content', $args);
						?>
					</div>
					<div class="sfwd sfwd_options sfwd-courses_settings">
						<h2>Excerpt</h2>
						<?php
						$args = array(
							'wpautop' => 1,
							'media_buttons' => 0,
							'textarea_rows' => 5,
							'tabindex' => 0,
							'editor_css' => '',
							'editor_class' => '',
							'teeny' => 0,
							'dfw' => 0,
							'tinymce' => 1,
							'quicktags' => 0  
						);
						wp_editor('', 'add_excerpt', $args);
						?>
					</div>


					<div class="edit-post-layout__metaboxes">
   <div class="edit-post-meta-boxes-area is-normal">
      <div class="edit-post-meta-boxes-area__container">
            <div id="poststuff" class="sidebar-open">
               <div id="postbox-container-2" class="postbox-container">
                  <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                     <div id="sfwd-lessons" class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span>Lesson Settings</span></h2>
                        <div class="inside">
                           <div class="sfwd sfwd_options sfwd-lessons_settings">
                              <div class="sfwd_input " id="sfwd-lessons_lesson_materials">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Options for Lesson materials" onclick="toggleVisibility('sfwd-lessons_lesson_materials_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Lesson Materials</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><textarea name="sfwd-lessons_lesson_materials" rows="2" cols="57"></textarea></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_course">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Associate this Lesson with a Course." onclick="toggleVisibility('sfwd-lessons_course_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Associated Course</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div">
                                    <select name="sfwd-lessons_course">
                                          <option selected=""  value="">-- Select a Course --</option>
                                          <?php                                     
                                             $query = new WP_Query( array(
                                             'post_type' => 'sfwd-courses',
                                             'orderby' => 'author',
                                             ) );
                                             if ($query->have_posts()) {
                                                while ($query->have_posts() ) : $query->the_post();
                                          ?>
                                                <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
                                          <?php 
                                             endwhile;
                                             }
                                          ?>
                                          \n
                                       </select>
                                    </div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_forced_lesson_time">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Minimum time a user has to spend on Lesson page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec" onclick="toggleVisibility('sfwd-lessons_forced_lesson_time_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Forced Lesson Timer</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_forced_lesson_time" type="text" size="57" value=""></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_upload" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want to make it mandatory to upload assignment" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_upload_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Upload Assignment</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_upload" type="checkbox" checked=""></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_auto_approve_assignment" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want to auto-approve the uploaded assignment" onclick="toggleVisibility('sfwd-lessons_auto_approve_assignment_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Auto Approve Assignment</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_auto_approve_assignment" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_count" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Enter the maximum number of assignment uploads allowed. Default is 1. Use 0 to unlimited." onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_count_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Limit number of uploaded files</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_count" type="number" class="small-text" placeholder="Default is 1" min="1" step="1" value="1"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_deletion_enabled" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Allow Student to Delete own Assignment(s)" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_deletion_enabled_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Allow Student to Delete own Assignment(s)</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_deletion_enabled" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_enabled" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Allow this assignment to be assigned points when it is approved." onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_enabled_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Award Points for Assignment</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_enabled" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_amount">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Assign the max amount of points someone can earn for this assignment." onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_amount_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Set Number of Points for Assignment</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_amount" type="number" min="0" step="1" value="0"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_extensions" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Enter comma-separated list of allowed file extensions: pdf, xls, zip or leave blank for any." onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_extensions_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Allowed File Extensions</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_extensions" type="text" size="57" placeholder="Example: pdf, xls, zip" value=""></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_size" style="">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Enter maximim file upload size. Example: 100KB, 2M, 2MB, 1G. Maximum upload file size: 2M" onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_size_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Allowed File Size</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_size" type="text" size="57" placeholder="Maximum upload file size: 2M" value="2M"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_sample_lesson">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want this lesson and all its topics to be available for free." onclick="toggleVisibility('sfwd-lessons_sample_lesson_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Sample Lesson</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_sample_lesson" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_visible_after">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Make Lesson visible ____ days after sign-up" onclick="toggleVisibility('sfwd-lessons_visible_after_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Make Lesson visible X Days After Sign-up</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_visible_after" type="number" class="small-text" min="0" step="1" value="0"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_visible_after_specific_date">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Set the date that you would like this lesson to become available." onclick="toggleVisibility('sfwd-lessons_visible_after_specific_date_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Make Lesson Visible on Specific Date</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div">
                                       <div class="ld_date_selector">
                                          <span class="screen-reader-text">Month</span>
                                          <select class="ld_date_mm" name="sfwd-lessons_visible_after_specific_date[mm]">
                                             <option value=""></option>
                                             <option value="01" data-text="Jan">01-Jan</option>
                                             <option value="02" data-text="Feb">02-Feb</option>
                                             <option value="03" data-text="Mar">03-Mar</option>
                                             <option value="04" data-text="Apr">04-Apr</option>
                                             <option value="05" data-text="May" selected="selected">05-May</option>
                                             <option value="06" data-text="Jun">06-Jun</option>
                                             <option value="07" data-text="Jul">07-Jul</option>
                                             <option value="08" data-text="Aug">08-Aug</option>
                                             <option value="09" data-text="Sep">09-Sep</option>
                                             <option value="10" data-text="Oct">10-Oct</option>
                                             <option value="11" data-text="Nov">11-Nov</option>
                                             <option value="12" data-text="Dec">12-Dec</option>
                                          </select>
                                          <span class="screen-reader-text">Day</span><input type="number" placeholder="DD" min="1" max="31" class="ld_date_jj" name="sfwd-lessons_visible_after_specific_date[jj]" value="" size="2" maxlength="2" autocomplete="off">, <span class="screen-reader-text">Year</span><input type="number" placeholder="YYYY" min="0000" max="9999" class="ld_date_aa" name="sfwd-lessons_visible_after_specific_date[aa]" value="" size="4" maxlength="4" autocomplete="off"> @ <span class="screen-reader-text">Hour</span><input type="number" min="0" max="23" placeholder="HH" class="ld_date_hh" name="sfwd-lessons_visible_after_specific_date[hh]" value="" size="2" maxlength="2" autocomplete="off">:<span class="screen-reader-text">Minute</span><input type="number" min="0" max="59" placeholder="MM" class="ld_date_mn" name="sfwd-lessons_visible_after_specific_date[mn]" value="" size="2" maxlength="2" autocomplete="off">
                                       </div>
                                    </div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_enabled">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want to show a video as part of the progression." onclick="toggleVisibility('sfwd-lessons_lesson_video_enabled_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Enable Video Progression</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_enabled" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_url">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="URL to video. The video will be added above the Lesson content." onclick="toggleVisibility('sfwd-lessons_lesson_video_url_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Video URL</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_url" type="text" size="57" value=""></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_start">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want the video to auto-start on page load." onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_start_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Auto Start Video</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_start" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_show_controls">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Show Video Controls. By default controls are disabled. Only used for YouTube and local videos." onclick="toggleVisibility('sfwd-lessons_lesson_video_show_controls_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Show Video Controls</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_show_controls" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_shown">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Select when to show video in relation to sub-steps." onclick="toggleVisibility('sfwd-lessons_lesson_video_shown_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">When to show video</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div">
                                       <select name="sfwd-lessons_lesson_video_shown">
                                          <option selected="" value="AFTER">After (default) - Video is shown after completing sub-steps</option>
                                          <option value="BEFORE">Before - Video is shown before completing sub-steps</option>
                                          \n
                                       </select>
                                    </div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_complete">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this if you want the Lesson to auto-complete after the video completes." onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_complete_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Auto Complete Lesson</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_complete" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_complete_delay">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Time delay in second between the time the video finishes and the auto complete occurs. Example 0 no delay, 5 for five seconds." onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_complete_delay_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Auto Complete Delay</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_complete_delay" type="number" class="small-text" min="0" step="1" value="0"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                              <div class="sfwd_input " id="sfwd-lessons_lesson_video_hide_complete_button">
                                 <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;cursor:pointer;" title="Check this to hide the complete button." onclick="toggleVisibility('sfwd-lessons_lesson_video_hide_complete_button_tip');"><img src="<?php echo plugin_dir_url( __FILE__ ). 'assets/images/question.png'; ?>">Hide Complete Button</a></span>
                                 <span class="sfwd_option_input">
                                    <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_hide_complete_button" type="checkbox"></div>
                                 </span>
                                 <p style="clear:left"></p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
      </div>
      <div class="edit-post-meta-boxes-area__clear"></div>
   </div>
</div>					



				</div>
				<div>
					<input class="button" type="submit" value="Save" />
				</div>

			</form>
			</div>		
		</div><!-- END #content-container -->
		
		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php // END THE LOOP 
endwhile; ?>

<?php 
get_footer();
