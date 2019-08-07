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


			<form id="featured_upload" method="POST" action="<?php echo get_site_url('','wp-content/plugins/courses-maker/save-question.php'); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
				<div class="add-header">
					<h1><input id="add_title" name="add_title" title="Questions Title" type="text" autocomplete="on"  placeholder="Questions Title"></h1>
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
				<input type="hidden" id="add_post_type" name="add_post_type" value="sfwd-question">
				<input type="hidden" id="add_comment_count" name="add_comment_count" value="0">
				<div id="primary" class="content-area">
					<div class="sfwd sfwd_options sfwd-courses_settings">
						<h2>Questions</h2>
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

						<div id="learndash_question_type" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span>Answer type</span></h2>
						<div class="inside">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<ul>
															<li><input id="learndash-question-type-single" type="radio" name="answerType" value="single" checked="checked">
											<label for="learndash-question-type-single">Single choice</label></li>
																<li><input id="learndash-question-type-multiple" type="radio" name="answerType" value="multiple">
											<label for="learndash-question-type-multiple">Multiple choice</label></li>

															</ul>
									</fieldset>
									</div>
						</div>

						<div id="learndash_question_points" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span>Points (required)</span></h2>
						<div class="inside">
							<p class="description">
								Points for this question (Standard is 1 point)			</p>
							<label>
								<input name="points" class="small-text" value="1" type="number" min="1"> Points			</label>
							<p class="description">
								This points will be rewarded, only if the user closes the question correctly.</p>
						</div>


						<div id="learndash_question_answers" class="postbox ">
<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Answers (required)</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Answers (required)</span></h2>
<div class="inside">
			<div class="inside answer_felder">

				<div class="classic_answer" style="display: block;">
					<ul class="answerList ui-sortable">
						
	<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
		<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
			<thead>
				<tr>
					<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; ">Options</th>
					<th style="padding: 5px;">Answer</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
						<div>
							<label>
								<input type="radio" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox">
								Correct							</label>
						</div>
						<div style="padding-top: 5px;">
							<label>
								<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
								Allow HTML							</label>
						</div>

					</td>
					<td style="padding: 5px; vertical-align: top;">
						<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"></textarea>
					</td>
				</tr>
			</tbody>
		</table>

		
	</li>

	
					<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
		<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
			<thead>
				<tr>
					<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; ">Options</th>
					<th style="padding: 5px;">Answer</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
						<div>
							<label>
								<input type="radio" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox">
								Correct							</label>
						</div>
						<div style="padding-top: 5px;">
							<label>
								<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
								Allow HTML							</label>
						</div>

					</td>
					<td style="padding: 5px; vertical-align: top;">
						<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		
		
	</li><li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
		<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
			<thead>
				<tr>
					<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; ">Options</th>
					<th style="padding: 5px;">Answer</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
						<div>
							<label>
								<input type="radio" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox">
								Correct							</label>
						</div>
						<div style="padding-top: 5px;">
							<label>
								<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
								Allow HTML							</label>
						</div>

					</td>
					<td style="padding: 5px; vertical-align: top;">
						<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		
		
	</li><li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
		<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
			<thead>
				<tr>
					<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; ">Options</th>
					<th style="padding: 5px;">Answer</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
						<div>
							<label>
								<input type="radio" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox">
								Correct							</label>
						</div>
						<div style="padding-top: 5px;">
							<label>
								<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
								Allow HTML							</label>
						</div>
					</td>
					<td style="padding: 5px; vertical-align: top;">
						<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		
	</li></ul>
				</div>

			</div>
			</div>
</div>


<div id="sfwd-question" class="postbox ">
<h2 class="hndle ui-sortable-handle"><span>LearnDash Question Settings</span></h2>
<div class="inside">
<div class="sfwd sfwd_options sfwd-question_settings"><div class="sfwd_input " id="sfwd-question_quiz"><span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;align-items: center;display: flex;" title="Click for Help!" onclick="toggleVisibility('sfwd-question_quiz_tip');"><img src="https://finanzrecht-service.de/wp-content/plugins/sfwd-lms/assets/images/question.png"><label class="sfwd_label textinput">Associated Quiz</label></a></span><span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-question_quiz">	<option value="0">-- Select a Quiz --</option>
	<?php                                     
	$query = new WP_Query( array(
	'post_type' => 'sfwd-question',
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
</div><div class="sfwd_help_text_div" style="display:none" id="sfwd-question_quiz_tip"><label class="sfwd_help_text">Associate this Question with a Quiz.</label></div></span><p style="clear:left"></p></div></div></div>
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