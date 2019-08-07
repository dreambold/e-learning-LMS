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
	.inside ul{
		display: flex;
		flex-direction: column;
    	list-style: none;
	}
	.inside ul li{
		display: flex;
    	align-items: center;
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


			<form id="featured_upload" method="POST" action="<?php echo get_site_url('','wp-content/plugins/courses-maker/save-lessons.php'); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
				<div class="add-header">
					<h1><input id="add_title" name="add_title" title="Topic Title" type="text" autocomplete="on"  placeholder="TopicTitle"></h1>
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
						<h2>Topic Content</h2>
						<?php
						$args = array(
							'wpautop' => 0,
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

					<div id="sfwd-quiz" class="postbox">
<h2 class="hndle ui-sortable-handle"><span>Topic Settings</span></h2>
<div class="inside">
<div class="sfwd sfwd_options sfwd-quiz_settings"><div class="sfwd_input " id="sfwd-quiz_quiz_materials"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_quiz_materials_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Quiz Materials</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><textarea name="sfwd-quiz_quiz_materials" rows="2" cols="57"></textarea></div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_quiz_materials_tip"><label class="sfwd_help_text">Options for Quiz materials</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_repeats"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_repeats_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Repeats</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><input name="sfwd-quiz_repeats" type="text" size="57" value="">
</div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_repeats_tip"><label class="sfwd_help_text">Number of repeats allowed for quiz. Blank = unlimited attempts. 0 = 1 attempt, 1 = 2 attempts, etc.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_threshold"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_threshold_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Certificate Threshold</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><input name="sfwd-quiz_threshold" type="text" size="57" value="0.8">
</div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_threshold_tip"><label class="sfwd_help_text">Minimum score required to award a certificate, between 0 and 1 where 1 = 100%.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_passingpercentage"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_passingpercentage_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Passing Percentage</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><input name="sfwd-quiz_passingpercentage" type="text" size="57" value="80">
</div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_passingpercentage_tip"><label class="sfwd_help_text">Passing percentage required to pass the quiz (number only). e.g. 80 for 80%.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_course"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_course_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Associated Course</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-quiz_course">	<option value="0">-- Select a Course --</option>
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
                                          \n</select>
</div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_course_tip"><label class="sfwd_help_text">Associate this Quiz with a Course.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_lesson"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_lesson_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Associated Lesson</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-quiz_lesson">	<option value="0">-- Select a Lesson or Topic --</option>
	<?php                                     
                                             $query = new WP_Query( array(

												'post_type' => 'sfwd-lessons',
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
	<?php                                     
                                             $query = new WP_Query( array(

												'post_type' => 'sfwd-topic',
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
\n</select>
</div><div class="sfwd_help_text_div" style="display: block;" id="sfwd-quiz_lesson_tip"><label class="sfwd_help_text">Associate this Quiz with a Lesson.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_certificate"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_certificate_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Associated Certificate</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-quiz_certificate">	<option value="0">-- Select a Certificate --</option>
	<option value="239">First step Spanish course</option>
	<option value="158">Certificate test</option>
\n</select>
</div><div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_certificate_tip"><label class="sfwd_help_text">Optionally associate a quiz with a certificate.</label></div></span><p style="clear:left"></p></div><div class="sfwd_input " id="sfwd-quiz_quiz_pro"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_quiz_pro_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Associated Settings</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-quiz_quiz_pro">	<option value="0">-- Select Settings --</option>
	<option value="1">1 - English Quiz</option>
	<option value="2">2 - Spanish Quiz - Step 1</option>
\n</select>
</div><div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_quiz_pro_tip"><label class="sfwd_help_text">If you imported a quiz, use this field to select it. Otherwise, create new settings below. After saving or publishing, you will be able to add questions.<a style="display:none" id="advanced_quiz_preview" class="wpProQuiz_prview" href="#">Preview</a></label></div></span><p style="clear:left"></p></div></div></div>
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

<!-- <script>

jQuery(document).ready(function() {
	jQuery(".button").bind("click", function() {

		var 
	})

});

</script> -->

<?php 
get_footer();