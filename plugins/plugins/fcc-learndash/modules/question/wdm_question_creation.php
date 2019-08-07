<?php
global $wpdb;
$sql = "SELECT * FROM {$wpdb->prefix}wp_pro_quiz_category";
$results = $wpdb->get_results($sql);
$category = array();
wp_enqueue_media();
if (count($results) > 0) {
    foreach ($results as $k => $v) {
        $category[ $v->category_id ] = $v->category_name;
    }
}

$sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_status IN ('publish','draft') AND post_type like 'sfwd-quiz'";
$quiz_ids = $wpdb->get_col($sql);
if (count($quiz_ids) == 0) {
    echo sprintf(__('Please Create %s', 'fcc'),LearnDash_Custom_Label::get_label('quiz'));

    return;
}
$quiz_id = (isset($_GET['quiz_id']) ? $_GET['quiz_id'] : '');
$title = '';
$answerPointsActivated = '';
$showPointsInBox = '';
$question = '';
$correctMsg = '';
$incorrectMsg = '';
$tipEnabled = '';
$tipMsg = '';
$correctSameText = '';
$matrixSortAnswerCriteriaWidth = 20;
$answerPointsDiffModusActivated = '';
$disableCorrect = '';
$answerType = '';
$answerData = '';
$points = '';
$category_id = '';
$pro_quiz_id = '';
if(isset($_GET['quiz_id'])){
	$pro_quiz_id=get_post_meta($_GET['quiz_id'],'quiz_pro_id',true);
}
if (isset($_GET[ 'questionid' ])) {
    $question_id = $_GET[ 'questionid' ];
    $sql = "SELECT * FROM {$wpdb->prefix}wp_pro_quiz_question WHERE id = $question_id";
    $results = $wpdb->get_results($sql);
    foreach ($results as $k => $v) {
    	$pro_quiz_id = $v->quiz_id;
    	$quiz_id = learndash_get_quiz_id_by_pro_quiz_id($pro_quiz_id);
        $title = $v->title;
        $points = $v->points;
        $question = $v->question;
        $correctMsg = $v->correct_msg;
        $incorrectMsg = $v->incorrect_msg;
        $correctSameText = $v->correct_same_text;
        $tipEnabled = $v->tip_enabled;
        $tipMsg = $v->tip_msg;
        $answerType = $v->answer_type;
        $showPointsInBox = $v->show_points_in_box;
        $answerPointsActivated = $v->answer_points_activated;
        $category_id = $v->category_id;
        $answerPointsDiffModusActivated = $v->answer_points_diff_modus_activated;
        $disableCorrect = $v->disable_correct;
        $matrixSortAnswerCriteriaWidth = $v->matrix_sort_answer_criteria_width;
        $answerData = maybe_unserialize($v->answer_data);
    }
}
?><script type="text/javascript">
    var wpProQuizLocalize = { "site_url" :"<?php echo site_url(); ?>","delete_msg": "<?php echo sprintf( __('Do you really want to delete the %s question?','fcc'),LearnDash_Custom_Label::get_label('quiz')); ?>", "no_title_msg": "<?php echo __('Title is not filled!','fcc') ?>", "no_question_msg": "<?php echo __('No question deposited!','fcc')?>", "no_question_title_msg": "<?php echo __('No question title deposited!','fcc') ?>", "no_correct_msg": "<?php echo __('Correct answer was not selected!','fcc') ?>", "no_answer_msg": "<?php echo __('No answer deposited!','fcc') ?>", "no_quiz_start_msg": "<?php echo sprintf( __('No %s description filled!','fcc'),LearnDash_Custom_Label::get_label('quiz')) ?>", "fail_grade_result": "<?php echo __('The percent values in result text are incorrect.','fcc') ?>", "no_nummber_points": "<?php echo __('No number in the field \"Points\" or less than 1 ','fcc') ?>", "no_nummber_points_new": "<?php echo __('No number in the field \"Points\" or less than 0','fcc') ?>", "no_selected_quiz": "<?php echo sprintf( __('No %s selected','fcc'),LearnDash_Custom_Label::get_label('quiz')) ?>", "reset_statistics_msg": "<?php echo __('Do you really want to reset the statistic?','fcc') ?>", "no_data_available": "<?php echo __('No data available','fcc') ?>", "no_sort_element_criterion": "<?php echo __('No sort element in the criterion','fcc') ?>", "dif_points": "<?php echo __('\"Different points for every answer\" is not possible at \"Free\" choice','fcc') ?>", "category_no_name": "<?php echo __('You must specify a name.','fcc') ?>", "confirm_delete_entry": "<?php echo __('This entry should really be deleted?','fcc') ?>", "not_all_fields_completed": "<?php echo __('Not all fields completed.','fcc') ?>", "temploate_no_name": "<?php echo __('You must specify a template name.','fcc') ?>", "closeText": "Close", "currentText": "Today", "monthNames": [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ], "monthNamesShort": [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ], "dayNames": [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ], "dayNamesShort": [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ], "dayNamesMin": [ "S", "M", "T", "W", "T", "F", "S" ], "dateFormat": "MM d, yy", "firstDay": "1", "isRTL": "" };
</script>			

<?php if (isset($_SESSION['update'])) {
    ?>
	<?php if ($_SESSION[ 'update' ] == 2) {
    ?>
		<div class="wdm-update-message"><?php echo __('Questions Updated Successfully.','fcc');
    ?></div>
		
	<?php 
} elseif ($_SESSION['update'] == 1) {
    ?>
		<div class="wdm-update-message"><?php echo __('Questions Updated Successfully.','fcc');
    ?></div>

	<?php 
}
    unset($_SESSION['update']);
}
if(isset($_GET['quiz_id']) && empty($quiz_id)){
	$quiz_id = $_GET['quiz_id'];
}
$back_url = get_permalink(get_option('wdm_quiz_create_page')).'?quizid='.$quiz_id;
?>

<input type="button" value="<?php echo sprintf(__('Back to %s', 'fcc'),LearnDash_Custom_Label::get_label('quiz'));?>" style="float: right;" onclick="location.href = '<?php echo $back_url; ?>';" ><br><br><br>
<form method="post" enctype="multipart/form-data">
	<div class="wrap wpProQuiz_questionEdit">

		<!-- <form action="admin.php?page=wpProQuiz&module=question&action=show&quiz_id=2" method="POST"> -->

		<input type="hidden" name="quiz_id" value="<?php echo $quiz_id;?>">
		<?php
		if(isset($_GET['post_id'])){
			?>
			<input type="hidden" name="post_id" value="<?php echo $_GET['post_id'];?>">
			<?php
		}
		?>
		<div style="clear: both;"></div>
		<!-- <input type="hidden" value="edit" name="hidden_action">
		<input type="hidden" value="1" name="questionId">-->
		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Title', 'fcc'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php echo sprintf(__('The title is used for overview, it is not visible in %s', 'fcc'),LearnDash_Custom_Label::get_label('quiz')); ?></p>
					<input name="title" class="regular-text" value="<?php echo $title; ?>" type="text" id='title'>
				</div>
			</div>			
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Points (required)', 'fcc'); ?></h3>
				<div class="inside">
					<div>
						<p class="description">
							<?php echo __('Points for this question (Standard is 1 point)', 'fcc'); ?></p>
						<label>
							<input name="points" class="small-text" value="<?php echo($answerPointsActivated == '' ? 1 : $points); ?>" type="number" min="1" ><?php echo __('Points', 'fcc'); ?>						</label>
						<p class="description">
							<?php echo __('This points will be rewarded, only if the user closes the question correctly', 'fcc'); ?>						</p>
					</div>
					<?php
					if($answerType != 'essay'){
						?>
						<div style="margin-top: 10px;">
						<?php
					}else{
						?>
						<div style="display:none;margin-top: 10px;">
						<?php
					}
					?>
						<label>
							<input name="answerPointsActivated" type="checkbox" value="1" <?php echo(($answerPointsActivated == 1) ? 'checked' : ''); ?> >
							<?php echo __('Different points for each answer', 'fcc'); ?>						</label>
						<p class="description">
							<?php echo __('If you enable this option, you can enter different points for every answer', 'fcc'); ?>						</p>
					</div>
					<?php
					if($answerType != 'essay'){
						?>
						<div style="margin-top: 10px;" id="wpProQuiz_showPointsBox">
						<?php
					}else{
						?>
						<div style="display:none;margin-top: 10px;" id="wpProQuiz_showPointsBox">
						<?php
					}
					?>
						<label>
							<input name="showPointsInBox" value="1" type="checkbox" <?php echo(($showPointsInBox == 1) ? 'checked' : ''); ?>>
							<?php echo __('Show reached points in the correct- and incorrect message?', 'fcc'); ?>						</label>
					</div>
				</div>
			</div>
						<div class="postbox">
							<h3 class="hndle"><?php echo __('Category (optional)', 'fcc'); ?></h3>
							<div class="inside">
								<p class="description">
									<?php echo __('You can assign classify category for a question. Categories are e.g. visible in statistics function.', 'fcc'); ?></p>
								<p class="description">
									<?php echo __('You can manage categories in global settings.', 'fcc'); ?></p>
								<div>
									<select name="category">
										<option value="-1"><?php echo __('--- Create new category ----', 'fcc'); ?></option>
										<option value="0" <?php echo ($category_id == 0) ? 'selected' : ''; ?>><?php echo __('--- No category ---', 'fcc'); ?></option>
										<?php if (!empty($category)) {
    foreach ($category as $k => $v) {
        ?>
										<option value="<?php echo $k;
        ?>" <?php echo ($k == $category_id) ? 'selected' : '';
        ?>><?php echo $v;
        ?></option>
										<?php 
    }
} ?>
																</select>
								</div>
								<div style="display: none;" id="categoryAddBox">
									<h4><?php echo __('Create new category', 'fcc'); ?></h4>
									<input type="text" name="categoryAdd" value=""> 
									<input type="button" class="button-secondary" name="" id="categoryAddBtn" value="<?php _e('Create', 'fcc');?>">
								</div>
								<div id="categoryMsgBox" style="display:none; padding: 5px; border: 1px solid rgb(160, 160, 160); background-color: rgb(255, 255, 168); font-weight: bold; margin: 5px; ">
									<?php echo 'Kategorie gespeichert'; ?>
								</div>
							</div>
						</div>
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Question (required)', 'fcc'); ?></h3>
				<div class="inside">
					<?php
///$content	 = '';
                    $editor_id = 'question';

                    $args = array(
                        'textarea_rows' => 10,
                    );
                    wp_editor($question, $editor_id, $args);

// do_action('admin_print_scripts');
                    ?>

				</div>
			</div>
			<?php
			$sql = "SELECT hide_answer_message_box FROM {$wpdb->prefix}wp_pro_quiz_master WHERE id = ".$pro_quiz_id;
    		$ans_msg_status = $wpdb->get_var($sql);
    		if($ans_msg_status){
			?>
			<br><br>
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Message with the correct / incorrect answer', 'fcc'); ?></h3>
				<div class="inside">
					<?php echo sprintf(__('Deactivated in %s settings.', 'fcc'),LearnDash_Custom_Label::get_label('quiz')); ?></div>
			</div><br><br>
			<?php
			}else{
			?>
			<div style="">
				<div class="postbox">
					<h3 class="hndle"><?php echo __('Message with the correct answer (optional)', 'fcc'); ?></h3>
					<div class="inside">
						<p class="description">
							<?php echo __('This text will be visible if answered correctly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.', 'fcc'); ?></p>
						<div style="padding-top: 10px; padding-bottom: 10px;">
							<label for="wpProQuiz_correctSameText">
								<?php echo __('Same text for correct- and incorrect-message?', 'fcc'); ?>
								<input type="checkbox" name="correctSameText" id="wpProQuiz_correctSameText" value="1" <?php echo(($correctSameText == 1) ? 'checked' : ''); ?>>
							</label>
						</div>
						<?php
///$content	 = '';
                        $editor_id = 'correctMsg';

                        $args = array(
                            'textarea_rows' => 10,
                        );
                        wp_editor($correctMsg, $editor_id, $args);

// do_action('admin_print_scripts');
                        ?>

					</div>
				</div>	
				<div class="postbox" id="wpProQuiz_incorrectMassageBox">
					<h3 class="hndle"><?php echo __('Message with the incorrect answer (optional)', 'fcc'); ?></h3>
					<div class="inside">
						<p class="description">
							<?php echo __('This text will be visible if answered incorrectly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.', 'fcc'); ?></p>
						<?php
///$content	 = '';
                        $editor_id = 'incorrectMsg';
                        wp_editor($incorrectMsg, $editor_id, $args);
                        ?>

					</div>
				</div>
			</div>
			<?php } ?>
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Hint (optional)', 'fcc'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php echo __('Here you can enter solution hint.', 'fcc'); ?></p>
					<div style="padding-top: 10px; padding-bottom: 10px;">
						<label for="wpProQuiz_tip">
							<?php echo __('Activate hint for this question?', 'fcc'); ?>  
							<input type="checkbox" name="tipEnabled" id="wpProQuiz_tip" value="1" <?php echo(($tipEnabled == 1) ? 'checked' : ''); ?>>
						</label>
					</div>
					<div id="wpProQuiz_tipBox" style="display: none;">
						<?php
///$content	 = '';
                        $editor_id = 'tipMsg';
                        wp_editor($tipMsg, $editor_id, $args);
                        ?>

					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php echo __('Answer type', 'fcc'); ?></h3>
				<div class="inside">
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="single" checked="checked" <?php echo(($answerType == 'single') ? 'checked' : ''); ?>>
						<?php echo __('Single choice', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="multiple" <?php echo(($answerType == 'multiple') ? 'checked' : ''); ?>>
						<?php echo __('Multiple choice', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="free_answer" <?php echo(($answerType == 'free_answer') ? 'checked' : ''); ?>>
						<?php echo __('"Free" choice', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="sort_answer" <?php echo(($answerType == 'sort_answer') ? 'checked' : ''); ?>>
						<?php echo __('"Sorting" choice', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="matrix_sort_answer" <?php echo(($answerType == 'matrix_sort_answer') ? 'checked' : ''); ?>>
						<?php echo __('"Matrix Sorting" choice', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="cloze_answer" <?php echo(($answerType == 'cloze_answer') ? 'checked' : ''); ?>>
						<?php echo __('Fill in the blank', 'fcc'); ?></label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="assessment_answer" <?php echo(($answerType == 'assessment_answer') ? 'checked' : ''); ?>>
						<?php echo __('Assessment', 'fcc'); ?></label>
					<?php if (version_compare(LEARNDASH_VERSION, '2.2') >= 0) { ?>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="essay" <?php echo ($answerType === 'essay') ? 'checked="checked"' : ''; ?>>
						<?php _e('Essay / Open Answer', 'fcc'); ?>
					</label>
					<?php } ?>
				</div>
			</div>
			<div class="postbox" id="singleChoiceOptions">
				<h3 class="hndle"><?php echo __('Single choice options', 'fcc'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php echo __('If "Different points for each answer" is activated, you can activate a special mode.<br> This changes the calculation of the points', 'fcc'); ?></p>
					<label>
						<input type="checkbox" name="answerPointsDiffModusActivated" value="1" <?php echo(($answerPointsDiffModusActivated == 1) ? 'checked' : ''); ?>>
						<?php echo __('Different points - modus 2 activate', 'fcc'); ?></label>
					<br><br>
					<p class="description">
						<?php echo __('Disables the distinction between correct and incorrect.', 'fcc'); ?><br>
					</p>
					<label>
						<input type="checkbox" name="disableCorrect" value="1" <?php echo(($disableCorrect == 1) ? 'checked' : ''); ?>>
						<?php echo __('disable correct and incorrect', 'fcc'); ?></label>

					<div style="padding-top: 20px;">
						<a href="#" id="clickPointDia"><?php echo __('Explanation of points calculation', 'fcc'); ?></a>
						<style>
							.pointDia td {
								border: 1px solid #9E9E9E;
								padding: 8px;
							}
						</style>
						<table style="border-collapse: collapse; display: none; margin-top: 10px;" class="pointDia">
							<tbody><tr>
									<th>
										<?php echo __('"Different points for each answer" enabled', 'fcc'); ?>
										<br>
										<?php echo __('"Different points - mode 2" disable', 'fcc'); ?></th>
									<th>
										<?php echo __('"Different points for each answer" enabled', 'fcc'); ?> 
										<br>
										<?php echo __('"Different points - mode 2" enabled', 'fcc'); ?></th>
								</tr>
								<tr>
									<td>
										<?php echo sprintf(__('%s - Single Choice - 3 Answers - Diff points mode', 'fcc'),LearnDash_Custom_Label::get_label('question'));
										
										 ; ?><br>
										<br>
										<?php echo __('A=3 Points [correct]', 'fcc'); ?><br>
										<?php echo __('B=2 Points [incorrect]', 'fcc'); ?><br>
										<?php echo __('C=1 Point [incorrect]', 'fcc'); ?><br>
										<br>
										<?php echo __('= 6 Points', 'fcc'); ?><br>

									</td>
									<td>
										<?php echo sprintf(__('%s - Single Choice - 3 Answers - Modus 2', 'fcc'),LearnDash_Custom_Label::get_label('question')); ?><br>
										<br>
										<?php echo __('A=3 Points [correct]', 'fcc'); ?><br>
										<?php echo __('B=2 Points [incorrect]', 'fcc'); ?><br>
										<?php echo __('C=1 Point [incorrect]', 'fcc'); ?><br>
										<br>
										<?php echo __('= 3 Points', 'fcc'); ?><br>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo __('~~~ User 1: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=checked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=correct and checked (correct) = 3 Points', 'fcc'); ?><br>
										<?php echo __('B=incorrect and unchecked (correct) = 2 Points', 'fcc'); ?><br>
										<?php echo __('C=incorrect and unchecked (correct) = 1 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 6 / 6 Points 100%', 'fcc'); ?><br>

									</td>
									<td>
										<?php echo __('~~~ User 1: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=checked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=checked = 3 Points', 'fcc'); ?><br>
										<?php echo __('B=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('C=unchecked = 0 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 3 / 3 Points 100%', 'fcc'); ?></td>
								</tr>
								<tr>
									<td>
										<?php echo __('~~~ User 2: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=checked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=correct and unchecked (incorrect) = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=incorrect and checked (incorrect) = 0 Points', 'fcc'); ?><br>
										<?php echo __('C=incorrect and unchecked (correct) = 1 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 1 / 6 Points 16.67%', 'fcc'); ?><br>

									</td>
									<td>
										<?php echo __('~~~ User 2: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=checked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=checked = 2 Points', 'fcc'); ?><br>
										<?php echo __('C=unchecked = 0 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 2 / 3 Points 66,67%', 'fcc'); ?></td>
								</tr>
								<tr>
									<td>
										<?php echo __('~~~ User 3: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=checked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=correct and unchecked (incorrect) = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=incorrect and unchecked (correct) = 2 Points', 'fcc'); ?><br>
										<?php echo __('C=incorrect and checked (incorrect) = 0 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 2 / 6 Points 33.33%', 'fcc'); ?><br>

									</td>
									<td>
										<?php echo __('~~~ User 3: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=checked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('C=checked = 1 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 1 / 3 Points 33,33%', 'fcc'); ?></td>
								</tr>
								<tr>
									<td>
										<?php echo __('~~~ User 4: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=correct and unchecked (incorrect) = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=incorrect and unchecked (correct) = 2 Points', 'fcc'); ?><br>
										<?php echo __('C=incorrect and unchecked (correct) = 1 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 3 / 6 Points 50%', 'fcc'); ?><br>

									</td>
									<td>
										<?php echo __('~~~ User 4: ~~~', 'fcc'); ?><br>
										<br>
										<?php echo __('A=unchecked', 'fcc'); ?><br>
										<?php echo __('B=unchecked', 'fcc'); ?><br>
										<?php echo __('C=unchecked', 'fcc'); ?><br>
										<br>
										<?php echo __('Result:', 'fcc'); ?><br>
										<?php echo __('A=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('B=unchecked = 0 Points', 'fcc'); ?><br>
										<?php echo __('C=unchecked = 0 Points', 'fcc'); ?><br>
										<br>
										<?php echo __('= 0 / 3 Points 0%', 'fcc'); ?></td>
								</tr>
							</tbody></table>
					</div>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle"><?php echo __('Answers (required)', 'fcc'); ?></h3>
				<div class="inside answer_felder">
					<div class="free_answer" style="display: none;">
						<div class="answerList">
							<p class="description">
								<?php echo __('correct answers (one per line) (answers will be converted to lower case)', 'fcc'); ?></p>
							<p style="border-bottom:1px dotted #ccc;">
								<textarea rows="6" cols="100" class="large-text" name="answerData[][answer]"><?php
                                    if ($answerType == 'free_answer') {
                                        echo $answerData[ 0 ]->getanswer();
                                    } else {
                                        echo '';
                                    }
                                    ?></textarea>
							</p>
						</div>
					</div>
					<div class="sort_answer" style="display: none;">
						<p class="description">
							<?php echo __('Please sort the answers in right order with the "Move" - Button. The answers will be displayed randomly.', 'fcc'); ?></p>
						<ul class="answerList ui-sortable">
							<?php
                            if (!empty($answerData)) {
                                foreach ($answerData as $value) {
                                    ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke; list-style: none;">
								<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse;margin-bottom: 20px;">
									<thead>
										<tr>
											<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
                                    ?></th>
											<th style="padding: 5px;"><?php echo __('Answer', 'fcc');
                                    ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
												<div>
													<label>
														<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1" <?php echo(($answerType == 'sort_answer' && $value->isHtml() != '') ? 'checked' : '');
                                    ?>>
														<?php echo __('Allow HTML', 'fcc');
                                    ?></label>
												</div>
												<div style="padding-top: 5px;" class="wpProQuiz_answerPoints">
													<label>
														<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="<?php echo ($answerType == 'sort_answer') ? $value->getpoints() : '1';
                                    ?>" >
														<?php echo __('Points', 'fcc');
                                    ?></label>
												</div>
											</td>
											<td style="padding: 5px; vertical-align: top;">
												<textarea rows="2" cols="100" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"><?php echo($answerType == 'sort_answer' ? $value->getanswer() : '');
                                    ?></textarea>
											</td>
										</tr>
									</tbody>
								</table>

								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
                                    ?>">
									<input class="upload_image_button button" type="button" value="<?php _e( 'Add Media' ); ?>" />
								<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
                                    ?></a>
							</li>
							<?php 
                                }
                            } else {
                                ?>
<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;list-style: none;">
								<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse;margin-bottom: 20px;">
									<thead>
										<tr>
											<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
                                ?></th>
											<th style="padding: 5px;"><?php echo __('Answer', 'fcc');
                                ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
												<div>
													<label>
														<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1" <?php echo(($answerType == 'sort_answer' && $answerData[ 0 ]->isHtml() != '') ? 'checked' : '');
                                ?>>
														<?php echo __('Allow HTML', 'fcc');
                                ?></label>
												</div>
												<div style="padding-top: 5px;" class="wpProQuiz_answerPoints">
													<label>
														<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="<?php echo ($answerType == 'sort_answer') ? $answer[ 0 ]->getpoints() : '1';
                                ?>" >
														<?php echo __('Points', 'fcc');
                                ?></label>
												</div>
											</td>
											<td style="padding: 5px; vertical-align: top;">
												<textarea rows="2" cols="100" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"><?php echo($answerType == 'sort_answer' ? $answerData[ 0 ]->getanswer() : '');
                                ?></textarea>
											</td>
										</tr>
									</tbody>
								</table>

								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
                                ?>">
								<input type="button" class="button-secondary upload_image_button" value="<?php _e('Add Media', 'fcc');
                                ?>">
								<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
                                ?></a>
							</li>
 	<?php

                            }

                            ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'fcc');?>" style="margin-bottom: 10px;">
					</div>
					<div class="classic_answer" style="display: block;">
						<ul class="answerList ui-sortable">
							<?php
							if ($answerType == 'multiple' || $answerType == 'single' && !empty($answerData)) {
    ?>

								<?php foreach ($answerData as $key => $value) {
    ?>
									<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
										<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
											<thead>
												<tr>
													<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
    ?></th>
													<th style="padding: 5px;"><?php echo __('Answer', 'fcc');
    ?></th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
														<div>
															<label>

																<input type="<?php echo $answerType == 'multiple' ? 'checkbox' : 'radio';
    ?>" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox" <?php echo $value->isCorrect() == 1 ? 'checked' : '';
    ?>>
																<?php echo __('Correct', 'fcc');
    ?></label>
														</div>
														<div style="padding-top: 5px;">
															<label>
																<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1" <?php echo $value->isHtml() == 1 ? 'checked' : '';
    ?> >
																<?php echo __('Allow HTML', 'fcc');
    ?></label>
														</div>
														<div style="padding-top: 5px;" class="wpProQuiz_answerPoints">
															<label>
																<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="<?php echo $value->getpoints() != '' ? $value->getpoints() : '';
    ?>"> 
																<?php echo __('Points', 'fcc');
    ?></label>
														</div>
													</td>
													<td style="padding: 5px; vertical-align: top;">
														<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"><?php echo $value->getanswer();
    ?></textarea>
													</td>
												</tr>
											</tbody>
										</table>

										<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
    ?>">
										<input type="button" class="button-secondary upload_image_button" value="<?php _e('Add Media', 'fcc');
    ?>">
										<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
    ?></a>

									</li>	

								<?php 
}
    ?>
							<?php 
} else {
    ?>
								<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
									<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
										<thead>
											<tr>
												<th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
    ?></th>
												<th style="padding: 5px;"><?php echo __('Answer', 'fcc');
    ?></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
													<div>
														<label>
															<input type="radio" name="answerData[][correct]" value="1" class="wpProQuiz_classCorrect wpProQuiz_checkbox">
															<?php echo __('Correct', 'fcc');
    ?></label>
													</div>
													<div style="padding-top: 5px;">
														<label>
															<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
															<?php echo __('Allow HTML', 'fcc');
    ?></label>
													</div>
													<div style="padding-top: 5px;" class="wpProQuiz_answerPoints">
														<label>
															<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="1"> 
															<?php echo __('Points', 'fcc');
    ?></label>
													</div>
												</td>
												<td style="padding: 5px; vertical-align: top;">
													<textarea rows="2" cols="50" class="large-text wpProQuiz_text" name="answerData[][answer]" style="resize:vertical;"></textarea>
												</td>
											</tr>
										</tbody>
									</table>

									<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
    ?>">
									<input type="button" class="button-secondary upload_image_button" value="<?php _e('Add Media', 'fcc');
    ?>">
									<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
    ?></a>

								</li>
							<?php 
} ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'fcc');?>" style="margin-bottom: 10px;">
					</div>
					<div class="matrix_sort_answer" style="display: none;">
						<p class="description">
							<?php echo __('In this mode, not a list have to be sorted, but elements must be assigned to matching criterion.', 'fcc'); ?></p>
						<p class="description">
							<?php echo __("You can create sort elements with empty criteria, which can't be assigned by user.", 'fcc'); ?>						</p>
						<br>
						<label>
							<?php echo __('Percentage width of criteria table column:', 'fcc'); ?>														<input type="number" min="1" max="99" step="1" name="matrixSortAnswerCriteriaWidth" value="<?php echo $matrixSortAnswerCriteriaWidth; ?>">%
						</label>
						<p class="description">
							<?php echo __("Allows adjustment of the left column's width, and the right column will auto-fill the rest of the available space. Increase this to allow accommodate longer criterion text. Defaults to 20%.", 'fcc'); ?></p>
						<br>
						<ul class="answerList ui-sortable">
							<?php if ($answerType == 'matrix_sort_answer') {
    ?>
								<?php foreach ($answerData as $key => $value) {
    ?>
									<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
										<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
											<thead>
												<tr>
													<th width="130px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
    ?></th>
													<th style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Criterion', 'fcc');
    ?></th>
													<th style="padding: 5px;"><?php echo __('Sort elements', 'fcc');
    ?></th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
														<label class="wpProQuiz_answerPoints">
															<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="<?php echo $value->getpoints();
    ?>"> 
															<?php echo __('Points', 'fcc');
    ?></label>
													</td>
													<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
														<textarea rows="4" name="answerData[][answer]" class="wpProQuiz_text" style="width: 100%; resize:vertical;"><?php echo $value->getanswer();
    ?></textarea>
													</td>
													<td style="padding: 5px; vertical-align: top;">
														<textarea rows="4" name="answerData[][sort_string]" class="wpProQuiz_text" style="width: 100%; resize:vertical;"><?php echo $value->getsortstring();
    ?></textarea>
													</td>
												</tr>
												<tr>
													<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;"></td>
													<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
														<label>
															<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1" <?php echo $value->isHtml() == 1 ? 'checked' : '' ?> >
															<?php echo __('Allow HTML', 'fcc');
    ?></label>
													</td>
													<td style="padding: 5px; vertical-align: top;">
														<label>
															<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][sort_string_html]" value="1" <?php echo $value->isSortStringHtml() == 1 ? 'checked' : '';
    ?> >
															<?php echo __('Allow HTML', 'fcc');
    ?></label>
													</td>
												</tr>
											</tbody>
										</table>

										<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
    ?>">
										<input type="button" class="button-secondary upload_image_button" value="<?php _e('Add Media', 'fcc');
    ?>">
										<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
    ?></a>
									</li>
								<?php 
}
    ?>
							<?php 
} else {
    ?>
								<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
									<table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
										<thead>
											<tr>
												<th width="130px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Options', 'fcc');
    ?></th>
												<th style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php echo __('Criterion', 'fcc');
    ?></th>
												<th style="padding: 5px;"><?php echo __('Sort elements', 'fcc');
    ?></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
													<label class="wpProQuiz_answerPoints">
														<input type="number" min="0" class="small-text wpProQuiz_points" name="answerData[][points]" value="1"> 
														<?php echo __('Points', 'fcc');
    ?></label>
												</td>
												<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
													<textarea rows="4" name="answerData[][answer]" class="wpProQuiz_text" style="width: 100%; resize:vertical;"></textarea>
												</td>
												<td style="padding: 5px; vertical-align: top;">
													<textarea rows="4" name="answerData[][sort_string]" class="wpProQuiz_text" style="width: 100%; resize:vertical;"></textarea>
												</td>
											</tr>
											<tr>
												<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;"></td>
												<td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
													<label>
														<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][html]" value="1">
														<?php echo __('Allow HTML', 'fcc');
    ?></label>
												</td>
												<td style="padding: 5px; vertical-align: top;">
													<label>
														<input type="checkbox" class="wpProQuiz_checkbox" name="answerData[][sort_string_html]" value="1">
														<?php echo __('Allow HTML', 'fcc');
    ?></label>
												</td>
											</tr>
										</tbody>
									</table>

									<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'fcc');
    ?>">
									<input type="button" class="button-secondary upload_image_button" value="<?php _e('Add Media', 'fcc');
    ?>">
									<a href="#" class="button-secondary wpProQuiz_move ui-sortable-handle" style="cursor: move;"><?php echo __('Move', 'fcc');
    ?></a>
								</li>
							<?php 
} ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'fcc');?>" style="margin-bottom: 10px;">
					</div>
					<div class="cloze_answer" style="display: none;">
						<p class="description">
							<?php echo __('Enclose the searched words with { } e.g. "I {play} soccer". Capital and small letters will be ignored.', 'fcc'); ?></p>
						<p class="description">
							<?php echo __('You can specify multiple options for a search word. Enclose the word with [ ] e.g. ', 'fcc'); ?><span style="font-style: normal; letter-spacing: 2px;"><?php echo __(' "I {[play][love][hate]} soccer" </span>. In this case answers play, love OR hate are correct.', 'fcc'); ?></p>
						<p class="description" style="margin-top: 10px;">
							<?php echo __('If mode "Different points for every answer" is activated, you can assign points with |POINTS. Otherwise 1 point will be awarded for every answer.', 'fcc'); ?></p>
						<p class="description">
							<?php echo __('e.g. "I {play} soccer, with a {ball|3}" - "play" gives 1 point and "ball" 3 points.', 'fcc'); ?></p>
						<?php
                        $cloze_id = 'cloze';
                            $cloze_name = 'answerData[cloze][answer]';
                            $settings = array(
                            'textarea_name' => $cloze_name,
                                'textarea_rows' => 10,
                            );
                        if ($answerType == 'cloze_answer') {
                            $cloze = $answerData[0]->getanswer();

                            wp_editor($cloze, $cloze_id, $settings);
                        } else {
                            $cloze = '';
                            wp_editor($cloze, $cloze_id, $settings);
                        }

                        ?>

					</div>
					<div class="assessment_answer" style="display: none;">
						<p class="description">
							<?php echo __('Here you can create an assessment question.', 'fcc'); ?></p>
						<p class="description">
							<?php echo __('Enclose a assesment with {}. The individual assessments are marked with [].', 'fcc'); ?><br>
							<?php echo __('The number of options in the maximum score.', 'fcc'); ?></p>
						<p>
							<?php echo __('Examples:', 'fcc'); ?><br>
							<?php echo __('* less true { [1] [2] [3] [4] [5] } more true', 'fcc'); ?></p>
						<div class="wpProQuiz_demoImgBox">
							<a href="#"><?php echo __('Demo', 'fcc'); ?></a> 
							<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
								<img alt="" src="<?php echo plugins_url('images/assessmentDemo1.png', dirname(dirname(__FILE__))); ?>">
							</div>
						</div>
						<p>
							<?php echo __('* less true { [a] [b] [c] } more true', 'fcc'); ?></p>
						<div class="wpProQuiz_demoImgBox">
							<a href="#"><?php echo __('Demo', 'fcc'); ?></a> 
							<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
								<img alt="" src="<?php echo plugins_url('images/assessmentDemo2.png', dirname(dirname(__FILE__))); ?> ">
							</div>
						</div>
						<p></p>

						<?php
                        $assessment_id = 'assessment';
                            $assessment_name = 'answerData[assessment][answer]';
                            $settings = array(
                            'textarea_name' => $assessment_name,
                                'textarea_rows' => 10,
                            );
                        if ($answerType == 'assessment_answer') {
                            $assessment = $answerData[0]->getanswer();

                            wp_editor($assessment, $assessment_id, $settings);
                        } else {
                            $assessment = '';
                            wp_editor($assessment, $assessment_id, $settings);
                        }

                        ?>

					</div>
					<div class="essay" style="display: none;">
						<?php
							if (!empty($answerData)) {
								$answerDataTemp = $answerData[0];
								$gradingType = $answerDataTemp->getGradedType();
								$gradingProgression = $answerDataTemp->getGradingProgression();
							}
							$essayType = array();
							$essayType['text'] = __('Text Box', 'fcc');
							$essayType['upload'] = __('Upload', 'fcc');

							$essayPointsType = array();
							$essayPointsType['not-graded-none'] = __('Not Graded, No Points Awarded','fcc');
							$essayPointsType['not-graded-full'] = __('Not Graded, Full Points Awarded','fcc');
							$essayPointsType['graded-full'] = __('Graded, Full Points Awarded','fcc');
						?>
						<p class="description">
							<?php echo __('How should the user submit their answer?', 'fcc'); ?></p>
							<select name="answerData[essay][type]" id="essay-type">
								<?php
									foreach ($essayType as $essay_key => $essay_value) {
										if (isset($gradingType) && $essay_key == $gradingType) {
											echo "<option selected='selected' value=".$essay_key.">".$essay_value."</option>";
										} else {
											echo "<option value=".$essay_key.">".$essay_value."</option>";
										}
									}
								?>
							</select>
						<p class="description"><?php echo sprintf(__('This is a question that can be graded and potentially prevent a user from progressing to the next step of the %s.','fcc'),LearnDash_Custom_Label::get_label('course'));?></p>
						<p class="description"><?php _e('The user can only progress if the essay is marked as "Graded" and if the user has enough points to move on.', 'fcc');?></p>
						<p class="description"><?php echo sprintf(__('How should the answer to this question be marked and graded upon %s submission?', 'fcc'),LearnDash_Custom_Label::get_label('quiz'));?></p>
						<select name="answerData[essay][progression]" id="essay-progression">
							<option><?php _e('--Select--','fcc');?></option>
							<?php
								foreach ($essayPointsType as $essayPointsKey => $essayPointsValue) {
									if (isset($gradingProgression) && $essayPointsKey == $gradingProgression) {
											echo "<option selected='selected' value=".$essayPointsKey.">".$essayPointsValue."</option>";
										} else {
											echo "<option value=".$essayPointsKey.">".$essayPointsValue."</option>";
										}
								}
							?>
						</select>
					</div>
				</div>
			</div>

			<div style="float: left;">
				<input type="submit" name="submit" id="saveQuestion" class="button-primary" value="<?php _e('Speichern', 'fcc');?>">
			</div>



			<input type ="hidden" name="wdm_question_action" value="<?php echo(isset($_GET[ 'questionid' ]) ? 'edit' : 'add'); ?>">
			<input type ="hidden" name="fcc-post-type" value="sfwd-question" />
			<?php if (isset($_GET[ 'questionid' ])) {
    ?>
				<input type ="hidden" name ="questionid" value ="<?php echo $_GET[ 'questionid' ];
    ?>">
			<?php 
} ?>
			<div style="clear: both;"></div>

		</div>

	</div>
</form>