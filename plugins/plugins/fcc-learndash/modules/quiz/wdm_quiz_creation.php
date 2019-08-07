<?php
global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id()).'listing/quiz_listing';
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps') == 'yes') ? false : true;
$id='';
if (isset($_GET[ 'quizid' ])) {
if (!is_numeric($_GET[ 'quizid' ])) {
echo __('Sorry, Something went wrong', 'fcc');
return;
}
$table = $wpdb->posts;
$id = $_GET[ 'quizid' ];
$sql = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-quiz' AND post_author = ".get_current_user_id();
$results = $wpdb->get_results($sql);
$quiz_post_id = $results[0]->ID;
$pretectQuizMeta = get_post_meta($quiz_post_id, '_timeLimitCookie', true);
if (count($results) == 0) {
echo __('Sorry, Something went wrong', 'fcc');
return;
}
}
$title = '';
$content = '';
$sfwd_quiz_course = '';
$sfwd_quiz_quiz_materials = "";
$sfwd_quiz_repeats = '';
$sfwd_quiz_threshold = '';
$sfwd_quiz_passingpercentage = '';
$sfwd_quiz_lesson = '';
$sfwd_quiz_certificate = '';
$sfwd_quiz_quiz_pro = '';
$resultText_count = 1;
$custom_field_data = array();
$menu_order = 0;
//quiz options variable declaration
$titleHidden = '';
$btnRestartQuizHidden = '';
$btnViewQuestionHidden = '';
$questionRandom = '';
$answerRandom = '';
$sortCategories = '';
$timeLimit = '';
$statisticsOn = '';
$statisticsIpLock = '';
$quizRunOnce = '';
$quizRunOnceType = '';
$quizRunOnceCookie = '';
$showMaxQuestion = '';
$showMaxQuestionValue = '';
$showMaxQuestionPercent = '';
$prerequisite = '';
$prerequisiteList = array();
$showReviewQuestion = '';
$quizSummaryHide = '';
$skipQuestionDisabled = '';
$emailNotification = '';
$userEmailNotification = '';
$autostart = '';
$startOnlyRegisteredUser = '';
$showPoints = '';
$numberedAnswer = '';
$hideAnswerMessageBox = '';
$disabledAnswerMark = '';
$forcingQuestionSolve = '';
$hideQuestionPositionOverview = '';
$hideQuestionNumbering = '';
$showCategory = '';
$showAverageResult = '';
$showCategoryScore = '';
$hideResultCorrectQuestion = '';
$hideResultQuizTime = '';
$hideResultPoints = '';
$quizModus = '';
$questionsPerPage = '';
$toplistActivated = '';
$toplistDataAddPermissions = '';
$toplistDataAddAutomatic = '';
$toplistDataSort = '';
$toplistDataAddMultiple = '';
$toplistDataAddBlock = '';
$toplistDataShowLimit = '';
$toplistDataShowIn = '';
$formActivated = '';
$formShowPosition = '';
$resultText = '';
$resultGradeEnabled = '';
$preview_url = '';
$table = $wpdb->prefix.'posts';
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";
$results = $wpdb->get_results($sql);
$course_list = array();
if (count($results) > 0) {
foreach ($results as $k => $v) {
$course_list[] = $v->ID;
}
}
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('publish','draft')";
$results = $wpdb->get_results($sql);
$lesson_list = array();
if (count($results) > 0) {
foreach ($results as $k => $v) {
$lesson_list[] = $v->ID;
}
}
$sql = "SELECT ID FROM $table WHERE post_type like 'sfwd-certificates' AND post_status IN ('publish','draft')";
$results = $wpdb->get_results($sql);
$certificate_list = array();
if (count($results) > 0) {
foreach ($results as $k => $v) {
$certificate_list[] = $v->ID;
}
}
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-quiz' AND post_status IN ('publish','draft')";
if (isset($_GET[ 'quizid' ])) {
$sql .= ' AND ID != '.$_GET[ 'quizid' ];
}
$result_tag = $wpdb->get_results($sql);
$quiz_list = array();
if (count($result_tag) > 0) {
foreach ($result_tag as $k => $v) {
$quiz_list[] = $v->ID;
}
}
$category    = array();
$results=get_terms(array(
'taxonomy' => 'category',
'hide_empty' => false,
));
if (count($results) > 0) {
foreach ($results as $value) {
$category[ $value->term_taxonomy_id ] = $value->name;
}
}
$results=get_terms(array(
'taxonomy' => 'post_tag',
'hide_empty' => false,
));
$selected_category   = array();
if (isset($_GET['quizid'])) {
$results = wp_get_post_terms($_GET[ 'quizid' ], 'category');
if (count( $results ) > 0) {
foreach ($results as $value) {
$selected_category[] = $value->term_taxonomy_id;
}
}
}
$results=get_terms(array(
'taxonomy' => 'post_tag',
'hide_empty' => false,
));
$tag     = array();
if (count( $results ) > 0 ) {
foreach ( $results as $value ) {
$tag[ $value->term_taxonomy_id ] = $value->name;
}
}
$selected_tag    = array();
if ( isset( $_GET[ 'quizid' ] ) ) {
$results = wp_get_post_terms($_GET[ 'quizid' ], 'post_tag');
if ( count( $results ) > 0 ) {
foreach ( $results as $value ) {
$selected_tag[] = $value->term_taxonomy_id;
}
}
}
if (isset($_GET[ 'quizid' ])) {
$id = $_GET[ 'quizid' ];
$title = get_the_title($id);
$content_post = get_post($id);
$content = $content_post->post_content;
$content = apply_filters('the_content', $content);
$menu_order = $content_post->menu_order;
$post_meta = maybe_unserialize(get_post_meta($id, '_sfwd-quiz', true));
if(isset($post_meta['sfwd-quiz_quiz_materials']))
$sfwd_quiz_quiz_materials = $post_meta['sfwd-quiz_quiz_materials'];
if (isset($post_meta[ 'sfwd-quiz_course' ])) {
$sfwd_quiz_course = $post_meta[ 'sfwd-quiz_course' ];
}
if (isset($post_meta[ 'sfwd-quiz_repeats' ])) {
$sfwd_quiz_repeats = $post_meta[ 'sfwd-quiz_repeats' ];
}
if (isset($post_meta[ 'sfwd-quiz_threshold' ])) {
$sfwd_quiz_threshold = $post_meta[ 'sfwd-quiz_threshold' ];
}
if (isset($post_meta[ 'sfwd-quiz_passingpercentage' ])) {
$sfwd_quiz_passingpercentage = $post_meta[ 'sfwd-quiz_passingpercentage' ];
}
if (isset($post_meta[ 'sfwd-quiz_lesson' ])) {
$sfwd_quiz_lesson = $post_meta[ 'sfwd-quiz_lesson' ];
}
if (isset($post_meta[ 'sfwd-quiz_certificate' ])) {
$sfwd_quiz_certificate = $post_meta[ 'sfwd-quiz_certificate' ];
}
if (isset($post_meta[ 'sfwd-quiz_quiz_pro' ])) {
$sfwd_quiz_quiz_pro = $post_meta[ 'sfwd-quiz_quiz_pro' ];
}
$sql = "SELECT * FROM {$wpdb->prefix}wp_pro_quiz_master WHERE id = ".$sfwd_quiz_quiz_pro;
$results = $wpdb->get_results($sql);
$titleHidden = $results[ 0 ]->title_hidden;
$btnRestartQuizHidden = $results[ 0 ]->btn_restart_quiz_hidden;
$btnViewQuestionHidden = $results[ 0 ]->btn_view_question_hidden;
$questionRandom = $results[ 0 ]->question_random;
$answerRandom = $results[ 0 ]->answer_random;
$sortCategories = $results[ 0 ]->sort_categories;
$timeLimit = $results[ 0 ]->time_limit;
$statisticsOn = $results[ 0 ]->statistics_on;
$statisticsIpLock = $results[ 0 ]->statistics_ip_lock;
$quizRunOnce = $results[ 0 ]->quiz_run_once;
$quizRunOnceType = $results[ 0 ]->quiz_run_once_type;
$quizRunOnceCookie = $results[ 0 ]->quiz_run_once_cookie;
$showMaxQuestion = $results[ 0 ]->show_max_question;
$showMaxQuestionValue = $results[ 0 ]->show_max_question_value;
$showMaxQuestionPercent = $results[ 0 ]->show_max_question_percent;
$prerequisite = $results[ 0 ]->prerequisite;
if ($prerequisite != '') {
$sql = "SELECT quiz_id FROM {$wpdb->prefix}wp_pro_quiz_prerequisite WHERE prerequisite_quiz_id = $sfwd_quiz_quiz_pro";
$prerequisiteList = $wpdb->get_col($sql);
}
$showReviewQuestion = $results[ 0 ]->show_review_question;
$quizSummaryHide = $results[ 0 ]->quiz_summary_hide;
$skipQuestionDisabled = $results[ 0 ]->skip_question_disabled;
$emailNotification = $results[ 0 ]->email_notification;
$userEmailNotification = $results[ 0 ]->user_email_notification;
$autostart = $results[ 0 ]->autostart;
$startOnlyRegisteredUser = $results[ 0 ]->start_only_registered_user;
$showPoints = $results[ 0 ]->show_points;
$numberedAnswer = $results[ 0 ]->numbered_answer;
$hideAnswerMessageBox = $results[ 0 ]->hide_answer_message_box;
$disabledAnswerMark = $results[ 0 ]->disabled_answer_mark;
$forcingQuestionSolve = $results[ 0 ]->forcing_question_solve;
$hideQuestionPositionOverview = $results[ 0 ]->hide_question_position_overview;
$hideQuestionNumbering = $results[ 0 ]->hide_question_numbering;
$showCategory = $results[ 0 ]->show_category;
$showAverageResult = $results[ 0 ]->show_average_result;
$showCategoryScore = $results[ 0 ]->show_category_score;
$hideResultCorrectQuestion = $results[ 0 ]->hide_result_correct_question;
$hideResultQuizTime = $results[ 0 ]->hide_result_quiz_time;
$hideResultPoints = $results[ 0 ]->hide_result_points;
$quizModus = $results[ 0 ]->quiz_modus;
$questionsPerPage = $results[ 0 ]->questions_per_page;
$toplistActivated = $results[ 0 ]->toplist_activated;
$toplist_data = maybe_unserialize($results[ 0 ]->toplist_data);
$toplistDataAddPermissions = $toplist_data[ 'toplistDataAddPermissions' ];
$toplistDataAddAutomatic = $toplist_data[ 'toplistDataAddAutomatic' ];
$toplistDataSort = $toplist_data[ 'toplistDataSort' ];
$toplistDataAddMultiple = $toplist_data[ 'toplistDataAddMultiple' ];
$toplistDataAddBlock = $toplist_data[ 'toplistDataAddBlock' ];
$toplistDataShowLimit = $toplist_data[ 'toplistDataShowLimit' ];
$toplistDataShowIn = $toplist_data[ 'toplistDataShowIn' ];
$formActivated = $results[ 0 ]->form_activated;
$formShowPosition = $results[ 0 ]->form_show_position;
$resultText = maybe_unserialize($results[ 0 ]->result_text);
$resultGradeEnabled = $results[ 0 ]->result_grade_enabled;
$sql = "SELECT * FROM {$wpdb->prefix}wp_pro_quiz_form WHERE quiz_id = ".$sfwd_quiz_quiz_pro.' ORDER BY sort';
$results = $wpdb->get_results($sql);
foreach ($results as $k => $v) {
$custom_field_data[ $k ][ 'fieldname' ] = $v->fieldname;
$custom_field_data[ $k ][ 'type' ] = $v->type;
$custom_field_data[ $k ][ 'required' ] = $v->required;
$custom_form_data = $v->data;
if ($v->data != '') {
$custom_form_data = array();
$custom_form_data = $v->data;
$custom_form_data = preg_replace('/\["/', '', $custom_form_data, 1);
$custom_form_data = str_lreplace('"]', '', $custom_form_data);
$custom_form_data = str_replace('","', "\n", $custom_form_data);
$custom_field_data[ $k ][ 'data' ] = stripslashes($custom_form_data);
if ($custom_form_data != '') {
}
} else {
$custom_field_data[ $k ][ 'data' ] = null;
}
}
$preview_url = add_query_arg(array('preview' => 1), get_permalink($id));
}
$wdm_quizdata = array(
'admin_url' => admin_url('admin-ajax.php'),
'wdm_selected_lesson_topic_id' => $sfwd_quiz_lesson,
);
wp_localize_script('wdm-quiz-script', 'wdm_topic_data', $wdm_quizdata);
?>
<?php if (isset($_SESSION['update'])) {
?>
<?php if ($_SESSION['update'] == 2) {
?>
<div class="wdm-update-message"><?php echo sprintf( __('%s Updated Successfully.', 'fcc'), LearnDash_Custom_Label::get_label('quiz') );
?></div>
<?php
} elseif ($_SESSION['update'] == 1) {
?>
<div class="wdm-update-message"><?php echo sprintf(__('%s Added Successfully.', 'fcc'), LearnDash_Custom_Label::get_label('quiz') );
?></div>
<?php
}
unset($_SESSION['update']);
}
?>
<?php if (defined('WDM_ERROR')) {
?>
<div class="wdm-error-message"><?php echo WDM_ERROR;
    ?>
</div>
<?php
} ?>
<script type="text/javascript">
/* <![CDATA[ */

var sfwd_data = { "json": "{\"learndash_categories_lang\":\"LearnDash Categories\",\"loading_lang\":\"Loading...\",\"select_a_lesson_lang\":\" <?php echo sprintf( __('-- Select a %s --','fcc'),LearnDash_Custom_Label::get_label('lesson')) ?> \",\"select_a_lesson_or_topic_lang\":\"<?php echo sprintf( __('-- Select a %s or %s --','fcc'),LearnDash_Custom_Label::get_label('lesson'), LearnDash_Custom_Label::get_label('topic')) ?>\",\"advanced_quiz_preview_link\":\"<?php echo site_url(); ?>/admin.php?page=ldAdvQuiz&module=preview&id=\",\"quiz_pro\":2,\"wdm_selected_lesson_topic_id\":\"<?php echo $sfwd_quiz_lesson; ?>\"}" };
/* ]]> */
</script>
<input type="button" value="<?php echo __('Back', 'fcc'); ?>" onclick="location.href = '<?php echo $back_url; ?>';" style="float: right;">
<?php if ($preview_url != '') {
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo __('Preview', 'fcc');
?>" style="float:right;margin-right: 2%;"onclick="window.open('<?php echo $preview_url;
?>')">
<?php
}
?>
<br><br><br>
<div class="fcc-quiz-container">
    <div class="fcc-ajax-overlay" style="display: none;">
        <img src="<?php echo plugins_url('/images/ajax-loader.gif', FCC_PLUGIN_PATH); ?>" alt="Loading..."/>
    </div>
    <form method="post" enctype="multipart/form-data" id="wdm_quiz_form">
        <div id="accordion">
            <h3><?php echo __('Content', 'fcc'); ?></h3>
            <div>
                <span><?php echo __('Title', 'fcc'); ?></span><br>
                <input type="text" name="title" style="width:100%;" value = "<?php echo $title; ?>"><br><br>
                <span><?php _e('Description', 'fcc');?></span>
                <?php
                    ///$content  = '';
                $editor_id = 'wdm_content';
                wp_editor($content, $editor_id);
                // do_action('admin_print_scripts');
                ?>
                <br>
                <?php
                if ( count( $category ) > 0 ) { ?>
                <span><?php echo __('Categories:','fcc'); ?></span><br>
                <div id='wdm_category_list'>
                    <select name="category[]" multiple>
                        <?php if ( count( $category ) > 0 ) { ?>
                        <?php foreach ( $category as $k => $v ) { ?>
                        <option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <br>
                <?php
                }
                if ( count( $tag ) > 0 ) { ?>
                <span><?php echo __('Tags:','fcc'); ?></span><br>
                <div id='wdm_tag_list'>
                    <select name="tag[]" multiple>
                        <?php if ( count( $tag ) > 0 ) { ?>
                        <?php foreach ( $tag as $k => $v ) { ?>
                        <option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_tag ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <br>
                <input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc');?>"><br><br>
                
                <span ><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" ></span>
                <?php if ( isset( $_GET[ 'quizid' ] ) && has_post_thumbnail($_GET['quizid']) ) { ?>
                <?php echo get_the_post_thumbnail( $_GET[ 'quizid' ], array( 100, 100 ) ); ?>
                <?php } ?>
                <br>
                <?php }?>
                <br>
                <div>
                    <label for="order_number"><?php _e('Order','fcc');?></label>
                    <input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
                </div>
            </div>
            <h3><?php _e('Features', 'fcc');?></h3>
            <?php if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) : ?>
            <div>
                <?php $this->loadMetaBoxes(); ?>
                <?php foreach ($this->metaboxes as $metabox): ?>
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo fccGetMetaboxProperty($metabox, 'settings_section_label');?></h3>
                        <div class="panel-actions">
                            <a class="panel-action icon md-minus" aria-expanded="false" data-toggle="panel-collapse"
                            aria-hidden="true"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php // error_log('Metabox For : '.print_r($this->quiz_id, 1)); ?>
                        <?php echo $metabox->show_meta_box(get_post($this->quiz_id)); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
                <div>
                    <div class="sfwd sfwd_options sfwd-quiz_settings">
                        <div class="sfwd_input " id="sfwd-quiz_quiz_materials">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-quiz_quiz_materials_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Materials', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div"><textarea name="sfwd-quiz_quiz_materials" rows="2" cols="57"><?php echo $sfwd_quiz_quiz_materials; ?></textarea></div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_quiz_materials_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Options for %s materials', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <div class="sfwd_input " id="sfwd-quiz_repeats">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_repeats_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo __('Repeats', 'fcc'); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div"><input name="sfwd-quiz_repeats" type="text" size="57" value="<?php echo $sfwd_quiz_repeats; ?>"></div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_repeats_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Number of repeats allowed for %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <div class="sfwd_input " id="sfwd-quiz_threshold">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_threshold_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo __('Certificate Threshold', 'fcc'); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div"><input name="sfwd-quiz_threshold" type="text" size="57" value="<?php echo $sfwd_quiz_threshold; ?>"></div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_threshold_tip"><label class="sfwd_help_text"><?php echo __('Minimum score required to award a certificate, between 0 and 1 where 1 = 100%', 'fcc'); ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <div class="sfwd_input " id="sfwd-quiz_passingpercentage">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_passingpercentage_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo __('Passing Percentage', 'fcc'); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div"><input name="sfwd-quiz_passingpercentage" type="text" size="57" value="<?php echo $sfwd_quiz_passingpercentage; ?>"></div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_passingpercentage_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Passing percentage required to pass the %s (number only). e.g. 80 for 80%%', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <?php if($sharedCourse){ ?>
                        <div class="sfwd_input " id="sfwd-quiz_course">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_course_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo sprintf(__('Associated %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div">
                                    <select name="sfwd-quiz_course">
                                        <option value="0"><?php echo sprintf( __('-- Select a %s --','fcc'),LearnDash_Custom_Label::get_label('course'));?></option>
                                        <?php if (count($course_list) > 0) {
                                        ?>
                                        <?php foreach ($course_list as $k => $v) {
                                        ?>
                                        <option value="<?php echo $v;
                                            ?>" <?php echo(($sfwd_quiz_course == $v) ? 'selected' : '');
                                            ?>><?php echo get_the_title($v);
                                        ?></option>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        } ?>
                                    </select>
                                </div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_course_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Associate with a %s ','fcc'),LearnDash_Custom_Label::get_label('course'));?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <div class="sfwd_input " id="sfwd-quiz_lesson">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_lesson_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo sprintf(__('Associated %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div">
                                    
                                    <select name="sfwd-quiz_lesson">
                                        <option value="0"><?php echo sprintf(__('-- Select a %s or %s --','fcc'),LearnDash_Custom_Label::get_label('lesson'), LearnDash_Custom_Label::get_label('topic'));?></option>
                                        <?php if (count($lesson_list) > 0) {
                                        ?>
                                        <?php foreach ($lesson_list as $k => $v) {
                                        ?>
                                        <option value="<?php echo $v;
                                            ?>" <?php echo(($sfwd_quiz_lesson == $v) ? 'selected' : '');
                                            ?>><?php echo get_the_title($v);
                                        ?></option>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        } ?>
                                    </select>
                                </div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_lesson_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Optionally associate a %s with a %s or %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz'),  LearnDash_Custom_Label::get_label('lesson'),  LearnDash_Custom_Label::get_label('topic'));
                                ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                        <?php } ?>
                        <div class="sfwd_input " id="sfwd-quiz_certificate">
                            <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-quiz_certificate_tip' );"><img src="<?php echo plugins_url('images/question.png', dirname(dirname(__FILE__))); ?>"><label class="sfwd_label textinput"><?php echo __('Associated Certificate', 'fcc'); ?></label></a></span>
                            <span class="sfwd_option_input">
                                <div class="sfwd_option_div">
                                    <select name="sfwd-quiz_certificate">
                                        <option value="0"><?php _e('-- Select a Certificate --', 'fcc');?></option>
                                        <?php  if (!empty($certificate_list)) {
                                        foreach ($certificate_list as $k => $v) {
                                        ?>
                                        <option value="<?php echo $v;
                                            ?>" <?php echo($v == $sfwd_quiz_certificate ? 'selected' : '');
                                            ?>><?php echo get_the_title($v);
                                        ?></option>
                                        <?php
                                        }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="sfwd_help_text_div" style="display:none" id="sfwd-quiz_certificate_tip"><label class="sfwd_help_text"><?Php echo sprintf(__('Optionally associate a %s with a certificate', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></div>
                            </span>
                            <p style="clear:left"></p>
                        </div>
                    </div>
                </div>
                <h3><?php echo __('Settings', 'fcc'); ?></h3>
                <div>
                    <div class="sfwd_input sfwd_no_label " id="sfwd-quiz_quiz_pro_html">
                        <span class="sfwd_option_input">
                            <div class="sfwd_option_div">
                                <style>
                                    .wpProQuiz_quizModus th, .wpProQuiz_quizModus td {
                                        border-right: 1px solid #A0A0A0;
                                        padding: 5px;
                                    }
                                </style>
                                <div class="wrap wpProQuiz_quizEdit">
                                    <div style="clear: both;"></div>
                                    <div id="poststuff">
                                        <input name="name" id="wpProQuiz_title" type="hidden" class="regular-text" value="">
                                        <div class="postbox">
                                            <!--                     <h3 class="hndle ui-sortable-handle">Options</h3>-->
                                            <div class="wrap">
                                                <table class="form-table">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf( __('Hide %s title', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Hide title', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="title_hidden">
                                                                    <input type="checkbox" id="title_hidden" value="1" name="titleHidden" <?php echo(($titleHidden != 0) ? 'checked' : ''); ?>><?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('The title serves as %s heading', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf(__('Hide "Restart %s "button', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('Hide "Restart %s "button', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span>
                                                                    </legend>
                                                                    <label for="btn_restart_quiz_hidden">
                                                                        <input type="checkbox" id="btn_restart_quiz_hidden" value="1" name="btnRestartQuizHidden" <?php echo(($btnRestartQuizHidden != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                    <?php echo sprintf( __('Hide the "Restart %s "button in the Frontend', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>                                       </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Hide "View question" button', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Hide "View question" button', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="btn_view_question_hidden">
                                                                        <input type="checkbox" id="btn_view_question_hidden" value="1" name="btnViewQuestionHidden" <?php echo(($btnViewQuestionHidden != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc');?>        
                                                                </label>
                                                                    <p class="description">
                                                                        <?php echo __('Hide the "View question" button in the Frontend', 'fcc'); ?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Display question randomly', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Display question randomly', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="question_random">
                                                                        <input type="checkbox" id="question_random" value="1" name="questionRandom" <?php echo(($questionRandom != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Display answers randomly', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Display answers randomly', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="answer_random">
                                                                        <input type="checkbox" id="answer_random" value="1" name="answerRandom" <?php echo(($answerRandom != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Sort questions by category', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Sort questions by category', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label>
                                                                    <input type="checkbox" value="1" name="sortCategories" <?php echo(($sortCategories != 0) ? 'checked' : ''); ?>><?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                    <?php echo __('Also works in conjunction with the "display randomly question" option', 'fcc'); ?></p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Time limit', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Time limit', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="time_limit">
                                                                    <input type="number" min="0" class="small-text" id="time_limit" value="<?php echo $timeLimit; ?>" name="timeLimit" ><?php echo __('Seconds', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                        <?php echo __('0 = no limit', 'fcc'); ?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf( __('Protect %s Answers in Browser Cookie', 'fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <?php
                                                                    if (!empty($pretectQuizMeta)) {
                                                                    $timeLimitCookie = $pretectQuizMeta;
                                                                    } else {
                                                                    $timeLimitCookie = 0;
                                                                    }
                                                                    ?>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('Protect %s Answers in Browser Cookie', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span>
                                                                    </legend>
                                                                    <label for="time_limit_cookie">
                                                                    <input type="number" min="0" class="small-text" id="time_limit_cookie" value="<?php echo $timeLimitCookie; ?>" name="timeLimitCookie" ><?php echo __('Seconds', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__("0 = Don't save answers. This option will save the user's answers into a browser cookie until the %s is submitted", 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Statistics', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Statistics', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="statistics_on">
                                                                        <input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo(($statisticsOn != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                    <?php echo sprintf(__('Statistics about right or wrong answers. Statistics will be saved by completed %s , not after every question. The statistics is only visible over administration menu. (internal statistics)', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr id="statistics_ip_lock_tr" style="display: none;">
                                                            <th scope="row">
                                                                <?php echo __('Statistics IP-lock', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Statistics IP-lock', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="statistics_ip_lock">
                                                                        <input type="number" min="0" class="small-text" id="statistics_ip_lock" value="<?php echo $statisticsIpLock; ?>" name="statisticsIpLock">
                                                                    <?php echo __('in minutes (recommended 1440 minutes = 1 day)', 'fcc'); ?>                                       </label>
                                                                    <p class="description">
                                                                        <?php echo __('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', 'fcc'); ?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('View Profile Statistics', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('View Profile Statistics', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label for="statistics_on">
                                                                        <input type="checkbox" id="viewProfileStatistics" value="1" name="viewProfileStatistics" <?php echo((get_post_meta($id, '_viewProfileStatistics',true) == '1') ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                    <?php echo sprintf(__('Enable user to view statistics for this %s on their profile.', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>                                       </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf(__('Execute %s only once', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('Execute %s only once', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="quizRunOnce" <?php echo(($quizRunOnce != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you activate this option, the user can complete the %s only once. Afterwards the %s is blocked for this user.', 'fcc'), LearnDash_Custom_Label::get_label('quiz'), LearnDash_Custom_Label::get_label('quiz')); ?>
                                                                    </p>
                                                                    <div id="wpProQuiz_quiz_run_once_type" style="margin-bottom: 5px; display: none;">
                                                                        <?php echo __('This option applies to:', 'fcc'); ?>
                                                                        <label>
                                                                            <input name="quizRunOnceType" type="radio" value="1" checked="checked" <?php echo(($quizRunOnceType == 1) ? 'checked' : ''); ?>>
                                                                        <?php echo __('all users', 'fcc'); ?>                                           </label>
                                                                        <label>
                                                                            <input name="quizRunOnceType" type="radio" value="2" <?php echo(($quizRunOnceType == 2) ? 'checked' : ''); ?>>
                                                                        <?php echo __('registered users only', 'fcc'); ?>                                           </label>
                                                                        <label>
                                                                            <input name="quizRunOnceType" type="radio" value="3" <?php echo(($quizRunOnceType == 3) ? 'checked' : ''); ?>>
                                                                        <?php echo __('anonymous users only', 'fcc'); ?>                                            </label>
                                                                        <div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
                                                                            <label>
                                                                                <input type="checkbox" value="1" name="quizRunOnceCookie" <?php echo(($quizRunOnceCookie != 0) ? 'checked' : ''); ?>>
                                                                            <?php echo __('user identification by cookie', 'fcc'); ?>                                               </label>
                                                                            <p class="description">
                                                                                <?php echo __('If you activate this option, a cookie is set additionally for unregistered (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.', 'fcc'); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo __('Show only specific number of questions', 'fcc'); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo __('Show only specific number of questions', 'fcc'); ?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="showMaxQuestion" <?php echo(($showMaxQuestion != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc'); ?></label>
                                                                    <p class="description">
                                                                        <?php echo __('If you enable this option, maximum number of displayed questions will be X from X questions. (The output of questions is random)', 'fcc'); ?>
                                                                    </p>
                                                                    <div id="wpProQuiz_showMaxBox" style="display: none;">
                                                                        <label>
                                                                            <?php echo __('How many questions should be displayed simultaneously:', 'fcc'); ?>
                                                                            <input class="small-text" type="text" name="showMaxQuestionValue" value="<?php echo $showMaxQuestionValue; ?>">
                                                                        </label>
                                                                        <label>
                                                                            <input type="checkbox" value="1" name="showMaxQuestionPercent" <?php echo(($showMaxQuestionPercent != 0) ? 'checked' : ''); ?>>
                                                                            <?php echo __('in percent', 'fcc'); ?>
                                                                        </label>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Prerequisites', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span>Prerequisites</span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="prerequisite" <?php echo(($prerequisite != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, you can choose %s , which user have to finish before he can start this %s', 'fcc'),LearnDash_Custom_Label::get_label('quiz'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('In all selected %s statistic function have to be active. If it is not it will be activated automatically.','fcc'),LearnDash_Custom_Label::get_label('quizzes'));?>
                                                                    </p>
                                                                    <div id="prerequisiteBox" style="display: none;">
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th style="width: 120px; padding: 0;"><?php echo LearnDash_Custom_Label::get_label('quiz');?></th>
                                                                                    <th style="padding: 0; width: 50px;"></th>
                                                                                    <th style="padding: 0; width: 125px;"><?php echo sprintf( __('Prerequisites (This %s have to be finished)','fcc'),LearnDash_Custom_Label::get_label('quiz'));?></th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding: 0;">
                                                                                        <select multiple="multiple" size="8" style="width: 200px;" name="quizList">
                                                                                            <?php if (count($quiz_list) > 0) {
                                                                                            ?>
                                                                                            <?php foreach ($quiz_list as $k => $v) {
                                                                                            $quizMeta = get_post_meta($v, '_sfwd-quiz', true);
                                                                                            if (!in_array($quizMeta['sfwd-quiz_quiz_pro'], $prerequisiteList)) {
                                                                                            ?>
                                                                                            <option value="<?php echo $v ?>"><?php echo get_the_title($v);
                                                                                            ?></option>
                                                                                            <?php
                                                                                            }
                                                                                            }
                                                                                            }
                                                                                            ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td style="padding: 0; text-align: center;">
                                                                                        <div>
                                                                                            <input type="button" id="btnPrerequisiteAdd" value=">>">
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="button" id="btnPrerequisiteDelete" value="<<">
                                                                                        </div>
                                                                                    </td>
                                                                                    <td style="padding: 0;">
                                                                                        <select multiple="multiple" size="8" style="width: 200px" name="prerequisiteList[]">
                                                                                            <?php if (!empty($prerequisiteList)) {
                                                                                            foreach ($prerequisiteList as $q_key => $q_value) {
                                                                                            $quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id($q_value);
                                                                                            ?>
                                                                                            <option value="<?php echo $quiz_post_id;
                                                                                                ?>" selected ><?php echo get_the_title($quiz_post_id);
                                                                                            ?></option>
                                                                                            
                                                                                            <?php
                                                                                            }
                                                                                            } ?>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Question overview', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Question overview', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="showReviewQuestion" <?php echo(($showReviewQuestion != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc');?>                                       </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('Add at the top of the %s a question overview, which allows easy navigation. Additional questions can be marked "to review"','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('Additional %s overview will be displayed, before %s is finished.','fcc'),LearnDash_Custom_Label::get_label('quiz'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <?php _e('Question overview:','fcc');?> <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/questionOverview.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <?php _e(LearnDash_Custom_Label::get_label('quiz').'-summary:','fcc');?> <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/quizSummary.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
                                                            <th scope="row">
                                                                <?php echo sprintf(__('%s-summary','fcc'),LearnDash_Custom_Label::get_label('quiz'));
                                                                ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('%s-summary','fcc'),LearnDash_Custom_Label::get_label('quiz'));?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="quizSummaryHide" <?php echo(($quizSummaryHide != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Deactivate','fcc');?>                                     </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, no %s overview will be displayed, before finishing %s','fcc'),LearnDash_Custom_Label::get_label('quiz'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('Skip question','fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Skip question','fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="skipQuestionDisabled" <?php echo(($skipQuestionDisabled != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Deactivate','fcc');?>                                     </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, user won\'t be able to skip question. (only in "Overview -&gt; next" mode). User still will be able to navigate over "Question-Overview"','fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Admin e-mail notification', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Admin e-mail notification', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="radio" name="emailNotification" value="0" checked="checked" <?php echo(($emailNotification == 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Deactivate', 'fcc');?>                                        </label>
                                                                    <label>
                                                                        <br/><input type="radio" name="emailNotification" value="1" <?php echo(($emailNotification == 1) ? 'checked' : ''); ?>>
                                                                    <?php _e('for registered users only', 'fcc');?>                                     </label>
                                                                    <label>
                                                                        <br/><input type="radio" name="emailNotification" value="2" <?php echo(($emailNotification == 2) ? 'checked' : ''); ?>>
                                                                    <?php _e('for all users', 'fcc');?>                                     </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, you will be informed if a user completes this %s','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                    <p class="description">
                                                                        <?php _e('E-Mail settings can be edited in global settings.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('User e-mail notification', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('User e-mail notification', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="userEmailNotification" value="1" <?php echo(($userEmailNotification != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, an email is sent with his %s result to the user. (only registered users)', 'fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                    <p class="description">
                                                                        <?php _e('E-Mail settings can be edited in global settings.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Autostart', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Autostart', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="autostart" value="1" <?php echo(($autostart != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, the %s will start automatically after the page is loaded.','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf(__('Only registered users are allowed to start the %s','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('Only registered users are allowed to start the %s','fcc'),LearnDash_Custom_Label::get_label('quiz'));?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="startOnlyRegisteredUser" value="1" <?php echo(($startOnlyRegisteredUser != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Activate', 'fcc');?>                                        </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, only registered users allowed start the','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e('Question-Options', 'fcc');?></h3>
                                            <div class="wrap">
                                                <table class="form-table">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Show points', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Show points', 'fcc');?></span>
                                                                    </legend>
                                                                    <label for="show_points">
                                                                        <input type="checkbox" id="show_points" name="showPoints" <?php echo(($showPoints != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('Shows in %s , how many points are reachable for respective question.','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Number answers', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Number answers', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="numberedAnswer" <?php echo(($numberedAnswer != 0) ? 'checked' : ''); ?>>
                                                                        <?php echo __('Activate', 'fcc');?>
                                                                    </label>
                                                                    <p class="description">
                                                                        <?php _e('If this option is activated, all answers are numbered (only single and multiple choice)','fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/numbering.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Hide correct- and incorrect message', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Hide correct- and incorrect message', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="hideAnswerMessageBox" <?php echo(($hideAnswerMessageBox != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, no correct- or incorrect message will be displayed.', 'fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/hideAnswerMessageBox.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Correct and incorrect answer mark', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Correct and incorrect answer mark', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="disabledAnswerMark" <?php echo(($disabledAnswerMark != 0) ? 'checked' : ''); ?>>
                                                                    <?php echo __('Deactivate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, answers won\'t be color highlighted as correct or incorrect.','fcc');?>
                                                                    </p>
                                                                    <div style="color:#0073aa;" class="wpProQuiz_demoBox">
                                                                        <a href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/mark.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Force user to answer each question', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Force user to answer each question', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="forcingQuestionSolve" <?php echo(($forcingQuestionSolve != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, the user is forced to answer each question.', 'fcc');?> <br>
                                                                        <?php echo sprintf(__('If the option "Question overview" is activated, this notification will appear after end of the %s otherwise after each question.','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Hide question position overview', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Hide question position overview', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="hideQuestionPositionOverview" <?php echo(($hideQuestionPositionOverview != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, the question position overview is hidden.','fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/hideQuestionPositionOverview.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Hide question numbering', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Hide question numbering', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="hideQuestionNumbering" <?php echo(($hideQuestionNumbering != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, the question numbering is hidden.', 'fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/hideQuestionNumbering.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Display category', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Display category', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="showCategory" <?php echo(($showCategory != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, category will be displayed in the question.', 'fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/showCategory.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e('Result-Options', 'fcc');?></h3>
                                            <div class="wrap">
                                                <table class="form-table">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Show average points', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Show average points', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" value="1" name="showAverageResult" <?php echo(($showAverageResult != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('Statistics-function must be enabled.', 'fcc');?>
                                                                    </p>
                                                                    <div class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/averagePoints.png', dirname(dirname(__FILE__))); ?>">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Show category score', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Show category score', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="showCategoryScore" value="1" <?php echo(($showCategoryScore != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, the results of each category is displayed on the results page.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/catOverview.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Hide correct questions - display', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Hide correct questions - display', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="hideResultCorrectQuestion" value="1" <?php echo(($hideResultCorrectQuestion != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you select this option, no longer the number of correctly answered questions are displayed on the results page.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/hideCorrectQuestion.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php echo sprintf(__('Hide %s time - display','fcc'),LearnDash_Custom_Label::get_label('quiz')); ?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php echo sprintf(__('Hide %s  time - display','fcc'),LearnDash_Custom_Label::get_label('quiz'));?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="hideResultQuizTime" value="1" <?php echo(($hideResultQuizTime != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php echo sprintf(__('If you enable this option, the time for finishing the %s won\'t be displayed on the results page anymore.','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                    </p>
                                                                </fieldset>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/hideQuizTime.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Hide score - display', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Hide score - display', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" name="hideResultPoints" value="1" <?php echo(($hideResultPoints != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, final score won\'t be displayed on the results page anymore.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/hideQuizPoints.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e(LearnDash_Custom_Label::get_label('quiz').'-Mode (required)', 'fcc');?></h3>
                                            <div class="inside">
                                                <table style="width: 100%; border-collapse: collapse; border: 1px solid #A0A0A0;" class="wpProQuiz_quizModus">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 25%;"><?php _e('Normal', 'fcc');?></th>
                                                            <th style="width: 25%;"><?php _e('Normal + Back-Button', 'fcc');?></th>
                                                            <th style="width: 25%;"><?php _e('Check -&gt; continue', 'fcc');?></th>
                                                            <th style="width: 25%;"><?php _e('Questions below each other', 'fcc');?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><label><input type="radio" name="quizModus" value="0" <?php echo(($quizModus == 0) ? 'checked' : ''); ?>> <?php _e('Activate', 'fcc');?></label></td>
                                                            <td><label><input type="radio" name="quizModus" value="1" <?php echo(($quizModus == 1) ? 'checked' : ''); ?>> <?php _e('Activate', 'fcc');?></label></td>
                                                            <td><label><input type="radio" name="quizModus" value="2" <?php echo(($quizModus == 2) ? 'checked' : ''); ?>> <?php _e('Activate', 'fcc');?></label></td>
                                                            <td><label><input type="radio" name="quizModus" value="3" <?php echo(($quizModus == 3) ? 'checked' : ''); ?>> <?php _e('Activate', 'fcc');?></label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?php echo sprintf(__('Displays all questions sequentially, "right" or "false" will be displayed at the end of the %s','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                            </td>
                                                            <td>
                                                                <?php _e('Allows to use the back button in a question.', 'fcc');?>
                                                            </td>
                                                            <td>
                                                                <?php _e('Shows "right or wrong" after each question.', 'fcc');?>
                                                            </td>
                                                            <td>
                                                                <?php _e('If this option is activated, all answers are displayed below each other, i.e. all questions are on a single page.', 'fcc');?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/normal.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/backButton.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/checkCcontinue.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="wpProQuiz_demoBox">
                                                                    <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                    <div style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                        <img alt="" src="<?php echo plugins_url('images/singlePage.png', dirname(dirname(__FILE__))); ?>">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                <?php _e('How many questions to be displayed on a page:', 'fcc');?><br>
                                                                <input type="number" name="questionsPerPage" value="<?php echo $questionsPerPage; ?>" min="0">
                                                                <span class="description">
                                                                <?php _e('(0 = All on one page)', 'fcc');?>                                 </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e('Leaderboard (optional)', 'fcc');?></h3>
                                            <div class="inside">
                                                <p>
                                                    <?php _e('The leaderboard allows users to enter results in public list and to share the result this way.', 'fcc');?>
                                                </p>
                                                <p>
                                                    <?php _e('The leaderboard works independent from internal statistics function.', 'fcc');?>
                                                </p>
                                                <table class="form-table">
                                                    <tbody id="toplistBox">
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Leaderboard', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <label>
                                                                    <input type="checkbox" name="toplistActivated" value="1" <?php echo(($toplistActivated != 0) ? 'checked' : ''); ?>>
                                                                <?php _e('Activate', 'fcc');?>                              </label>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('Who can sign up to the list', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <label>
                                                                    <input name="toplistDataAddPermissions" type="radio" value="1" checked="checked" <?php echo(($toplistDataAddPermissions == 1) ? 'checked' : ''); ?>>
                                                                <?php _e('all users', 'fcc');?>                             </label>
                                                                <br/>
                                                                <label>
                                                                    <input name="toplistDataAddPermissions" type="radio" value="2" <?php echo(($toplistDataAddPermissions == 2) ? 'checked' : ''); ?>>
                                                                <?php _e('registered users only', 'fcc');?>                             </label>
                                                                <br/>
                                                                <label>
                                                                    <input name="toplistDataAddPermissions" type="radio" value="3" <?php echo(($toplistDataAddPermissions == 3) ? 'checked' : ''); ?>>
                                                                <?php _e('anonymous users only', 'fcc');?>                              </label>
                                                                <p class="description">
                                                                    <?php _e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)', 'fcc');?>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('insert automatically', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <label>
                                                                    <input name="toplistDataAddAutomatic" type="checkbox" value="1" <?php echo(($toplistDataAddAutomatic != 0) ? 'checked' : ''); ?>>
                                                                <?php _e('Activate', 'fcc');?>                              </label>
                                                                <p class="description">
                                                                    <?php _e('If you enable this option, logged in users will be automatically entered into leaderboard', 'fcc');?>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('display captcha', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <label>
                                                                    <input type="checkbox" name="toplistDataCaptcha" value="1" disabled="disabled" >
                                                                <?php _e('Activate', 'fcc');?>                              </label>
                                                                <p class="description">
                                                                    <?php _e('If you enable this option, additional captcha will be displayed for users who are not registered.', 'fcc');?>
                                                                </p>
                                                                <p class="description" style="color: red;">
                                                                    <?php _e('This option requires additional plugin:', 'fcc');?>                                    <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really Simple CAPTCHA'</a>
                                                                </p>
                                                                <p class="description" style="color: red;">
                                                                    <?php _e('Plugin is not installed.', 'fcc');?>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('Sort list by', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <label>
                                                                    <input name="toplistDataSort" type="radio" value="1" checked="checked" <?php echo(($toplistDataSort == 1) ? 'checked' : ''); ?>>
                                                                <?php _e('best user', 'fcc');?>                             </label>
                                                                <br/>
                                                                <label>
                                                                    <input name="toplistDataSort" type="radio" value="2" <?php echo(($toplistDataSort == 2) ? 'checked' : ''); ?>>
                                                                <?php _e('newest entry', 'fcc');?>                              </label>
                                                                <br/>
                                                                <label>
                                                                    <input name="toplistDataSort" type="radio" value="3" <?php echo(($toplistDataSort == 3) ? 'checked' : ''); ?>>
                                                                <?php _e('oldest entry', 'fcc');?>                              </label>
                                                                <br/>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('Users can apply multiple times', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <div>
                                                                    <label>
                                                                        <input type="checkbox" name="toplistDataAddMultiple" value="1" <?php echo(($toplistDataAddMultiple != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>                                  </label>
                                                                </div>
                                                                <div id="toplistDataAddBlockBox" style="display: none;">
                                                                    <label>
                                                                        <?php _e('User can apply after:', 'fcc');?>                                     <input type="number" min="0" class="small-text" name="toplistDataAddBlock" value="<?php echo $toplistDataAddBlock; ?>">
                                                                    <?php _e('minute', 'fcc');?>                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <th scope="row">
                                                                <?php _e('How many entries should be displayed', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <div>
                                                                    <label>
                                                                        <input type="number" min="0" class="small-text" name="toplistDataShowLimit" value="<?php echo $toplistDataShowLimit; ?>">
                                                                    <?php _e('Entries', 'fcc');?>                                   </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="AutomaticallyDisplayLeaderboard" style="display: none;">
                                                            <th scope="row">
                                                                <?php echo sprintf(__('Automatically display leaderboard in %s result','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                            </th>
                                                            <td>
                                                                <div style="margin-top: 6px;">
                                                                    <?php _e('Where should leaderboard be displayed:', 'fcc');?><br>
                                                                    <label style="margin-right: 5px; margin-left: 5px;">
                                                                        <input type="radio" name="toplistDataShowIn" value="0" <?php echo(($toplistDataShowIn == 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('don\'t display', 'fcc');?></label>
                                                                    <label>
                                                                        <input type="radio" name="toplistDataShowIn" value="1" <?php echo(($toplistDataShowIn == 1) ? 'checked' : ''); ?>>
                                                                    <?php _e('below the "result text"', 'fcc');?>                                   </label>
                                                                    <span class="wpProQuiz_demoBox" style="margin-right: 5px;">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/leaderboardInResultText.png', dirname(dirname(__FILE__))); ?>">
                                                                        </span>
                                                                    </span>
                                                                    <label>
                                                                        <input type="radio" name="toplistDataShowIn" value="2" <?php echo(($toplistDataShowIn == 2) ? 'checked' : ''); ?>>
                                                                    <?php _e('in a button', 'fcc');?>                                   </label>
                                                                    <span class="wpProQuiz_demoBox">
                                                                        <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                        <span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                            <img alt="" src="<?php echo plugins_url('images/leaderboardInButton.png', dirname(dirname(__FILE__))); ?>">
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e('Custom fields', 'fcc');?></h3>
                                            <div class="inside">
                                                <p class="description">
                                                    <?php _e('You can create custom fields, e.g. to request the name or the e-mail address of the users.', 'fcc');?>
                                                </p>
                                                <p class="description">
                                                    <?php _e('The statistic function have to be enabled.', 'fcc');?>
                                                </p>
                                                <table class="form-table">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Custom fields enable', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Custom fields enable', 'fcc');?></span>
                                                                    </legend>
                                                                    <label>
                                                                        <input type="checkbox" id="formActivated" value="1" name="formActivated" <?php echo(($formActivated != 0) ? 'checked' : ''); ?>>
                                                                    <?php _e('Activate', 'fcc');?>      </label>
                                                                    <p class="description">
                                                                        <?php _e('If you enable this option, custom fields are enabled.', 'fcc');?>
                                                                    </p>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Display position', 'fcc');?>
                                                            </th>
                                                            <td>
                                                                <fieldset>
                                                                    <legend class="screen-reader-text">
                                                                        <span><?php _e('Display position', 'fcc');?></span>
                                                                    </legend>
                                                                    <?php _e('Where should the fields be displayed:', 'fcc');?><br>
                                                                    <label>
                                                                        <input type="radio" value="0" name="formShowPosition" checked="checked">
                                                                        <?php echo sprintf(__('On the %s startpage','fcc'), LearnDash_Custom_Label::get_label('quiz'));?>
                                                                        <div style="display:block;" class="wpProQuiz_demoBox">
                                                                            <a style="color:#0073aa;"href="#"><?php _e('Demo', 'fcc');?></a>
                                                                            <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                                <img alt="" src="<?php echo plugins_url('images/customFieldsFront.png', dirname(dirname(__FILE__))); ?>">
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                    <label>
                                                                        <input type="radio" value="1" name="formShowPosition" <?php echo(($formShowPosition == 1) ? 'checked' : ''); ?>>
                                                                        <?php echo sprintf(__('At the end of the %s  (before the %s  result)','fcc'),LearnDash_Custom_Label::get_label('quiz'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                                        <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                                                            <a style="color:#0073aa;"href="#"><?php _e('Demo', 'fcc');?></a>
                                                                            <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                                <img alt="" src="<?php echo plugins_url('images/customFieldsEnd1.png', dirname(dirname(__FILE__))); ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                                                            <a style="color:#0073aa;" href="#"><?php _e('Demo', 'fcc');?></a>
                                                                            <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                                                <img alt="" src="<?php echo plugins_url('images/customFieldsEnd2.png', dirname(dirname(__FILE__))); ?>">
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div style="margin-top: 10px; padding: 10px; border: 1px solid #C2C2C2;">
                                                    <table style=" width: 100%; text-align: left; " id="form_table">
                                                        <thead>
                                                            <tr>
                                                                <th><?php _e('Field name', 'fcc');?></th>
                                                                <th><?php _e('Type', 'fcc');?></th>
                                                                <th><?php _e('Required?', 'fcc');?></th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="ui-sortable">
                                                            <tr style="display:none">
                                                                <td>
                                                                    <input type="text" name="form[][fieldname]" value="" class="regular-text">
                                                                </td>
                                                                <td style="position: relative;">
                                                                    <select name="form[][type]">
                                                                        <option value="0" selected="selected"><?php _e('Text', 'fcc');?></option>
                                                                        <option value="1"><?php _e('Textarea', 'fcc');?></option></option>
                                                                        <option value="3"><?php _e('Checkbox', 'fcc');?></option>
                                                                        <option value="7"><?php _e('Drop-Down menu', 'fcc');?></option>
                                                                        <option value="8"><?php _e('Radio', 'fcc');?></option>
                                                                        <option value="2"><?php _e('Number', 'fcc');?></option>
                                                                        <option value="4"><?php _e('Email', 'fcc');?></option>
                                                                        <option value="5"><?php _e('Yes/No', 'fcc');?></option>
                                                                        <option value="6"><?php _e('Date', 'fcc');?></option>
                                                                    </select>
                                                                    <a href="#" class="editDropDown" style="display: none;">Edit list</a>
                                                                    <div class="dropDownEditBox" style="position: absolute; border: 1px solid rgb(175, 175, 175); padding: 5px; bottom: 0px; right: -45px; box-shadow: rgb(175, 175, 175) 1px 1px 1px 1px; display: none; background: rgb(235, 235, 235);">
                                                                        <h4>One entry per line</h4>
                                                                        <div>
                                                                            <textarea rows="5" cols="50" name="form[][data]"></textarea>
                                                                        </div>
                                                                        <input type="button" value="OK" class="button-primary">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" name="form[][required]" value="1">
                                                                </td>
                                                                <td>
                                                                    <input type="button" name="form_delete" value="<?php _e('Delete', 'fcc');?>" class="button-secondary">
                                                                    <a class="form_move button-secondary" href="#" style="cursor:move;"><?php _e('Move', 'fcc');?></a>
                                                                    <input type="hidden" name="form[][form_id]" value="0">
                                                                    <input type="hidden" name="form[][form_delete]" value="0">
                                                                </td>
                                                            </tr>
                                                            <?php if (empty($custom_field_data)) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="text" name="form[][fieldname]" value="" class="regular-text">
                                                                </td>
                                                                <td style="position: relative;">
                                                                    <select name="form[][type]">
                                                                        <option value="0" selected="selected"><?php _e('Text', 'fcc');
                                                                        ?></option>
                                                                        <option value="1"><?php _e('Textarea', 'fcc');
                                                                        ?></option>
                                                                        <option value="3"><?php _e('Checkbox', 'fcc');
                                                                        ?></option>
                                                                        <option value="7"><?php _e('Drop-Down menu', 'fcc');
                                                                        ?></option>
                                                                        <option value="8"><?php _e('Radio', 'fcc');
                                                                        ?></option>
                                                                        <option value="2"><?php _e('Number', 'fcc');
                                                                        ?></option>
                                                                        <option value="4"><?php _e('Email', 'fcc');
                                                                        ?></option>
                                                                        <option value="5"><?php _e('Yes/No', 'fcc');
                                                                        ?></option>
                                                                        <option value="6"><?php _e('Date', 'fcc');
                                                                        ?></option>
                                                                    </select>
                                                                    <a href="#" class="editDropDown" style="display: none;">Edit list</a>
                                                                    <div class="dropDownEditBox" style="position: absolute; border: 1px solid rgb(175, 175, 175); padding: 5px; bottom: 0px; right: -45px; box-shadow: rgb(175, 175, 175) 1px 1px 1px 1px; display: none; background: rgb(235, 235, 235);">
                                                                        <h4>One entry per line</h4>
                                                                        <div>
                                                                            <textarea rows="5" cols="50" name="form[][data]"></textarea>
                                                                        </div>
                                                                        <input type="button" value="OK" class="button-primary">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" name="form[][required]" value="1">
                                                                </td>
                                                                <td>
                                                                    <input type="button" name="form_delete" value="<?php _e('Delete', 'fcc');
                                                                    ?>" class="button-secondary">
                                                                    <a class="form_move button-secondary" href="#" style="cursor:move;"><?php _e('Move', 'fcc');
                                                                    ?></a>
                                                                    <input type="hidden" name="form[][form_id]" value="0">
                                                                    <input type="hidden" name="form[][form_delete]" value="0">
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            } else {
                                                            ?>
                                                            <?php foreach ($custom_field_data as $k => $v) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="text" name="form[][fieldname]" value="<?php echo $v[ 'fieldname' ];
                                                                    ?>" class="regular-text">
                                                                </td>
                                                                <td style="position: relative;">
                                                                    <select name="form[][type]">
                                                                        <option value="0" <?php echo(($v[ 'type' ] == 0) ? 'selected' : '');
                                                                        ?>>Text</option>
                                                                        <option value="1" <?php echo(($v[ 'type' ] == 1) ? 'selected' : '');
                                                                        ?>>Textarea</option>
                                                                        <option value="3" <?php echo(($v[ 'type' ] == 3) ? 'selected' : '');
                                                                        ?>>Checkbox</option>
                                                                        <option value="7" <?php echo(($v[ 'type' ] == 7) ? 'selected' : '');
                                                                        ?>>Drop-Down menu</option>
                                                                        <option value="8" <?php echo(($v[ 'type' ] == 8) ? 'selected' : '');
                                                                        ?>>Radio</option>
                                                                        <option value="2" <?php echo(($v[ 'type' ] == 2) ? 'selected' : '');
                                                                        ?>>Number</option>
                                                                        <option value="4" <?php echo(($v[ 'type' ] == 4) ? 'selected' : '');
                                                                        ?>>Email</option>
                                                                        <option value="5" <?php echo(($v[ 'type' ] == 5) ? 'selected' : '');
                                                                        ?>>Yes/No</option>
                                                                        <option value="6" <?php echo(($v[ 'type' ] == 6) ? 'selected' : '');
                                                                        ?>>Date</option>
                                                                    </select>
                                                                    <a href="#" class="editDropDown" style="display: none;">Edit list</a>
                                                                    <div class="dropDownEditBox" style="position: absolute; border: 1px solid rgb(175, 175, 175); padding: 5px; bottom: 0px; right: -45px; box-shadow: rgb(175, 175, 175) 1px 1px 1px 1px; display: none; background: rgb(235, 235, 235);">
                                                                        <h4>One entry per line</h4>
                                                                        <div>
                                                                            <textarea rows="5" cols="50" name="form[][data]"><?php echo $v[ 'data' ];
                                                                            ?></textarea>
                                                                        </div>
                                                                        <input type="button" value="OK" class="button-primary">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" name="form[][required]" value="1" <?php echo(($v[ 'required' ] != 0) ? 'checked' : '');
                                                                    ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="button" name="form_delete" value="Delete" class="button-secondary">
                                                                    <a class="form_move button-secondary" href="#" style="cursor:move;">Move</a>
                                                                    <input type="hidden" name="form[][form_id]" value="0">
                                                                    <input type="hidden" name="form[][form_delete]" value="0">
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                            <?php
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                    <div style="margin-top: 10px;">
                                                        <input type="button" name="form_add" id="form_add" value="<?php _e('Add field', 'fcc');?>" class="button-secondary">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input name="text" type="hidden" value="AAZZAAZZ">
                                        <div class="postbox">
                                            <h3 class="hndle ui-sortable-handle"><?php _e('Results text (optional)', 'fcc');?></h3>
                                            <div class="inside">
                                                <p class="description">
                                                    <?php echo sprintf(__('This text will be displayed at the end of the %s (in results). (this text is optional)','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                </p>
                                                <div style="padding-top: 10px; padding-bottom: 10px;">
                                                    <label for="wpProQuiz_resultGradeEnabled">
                                                        <?php _e('Activate graduation', 'fcc');?>
                                                        <input type="checkbox" name="resultGradeEnabled" id="wpProQuiz_resultGradeEnabled" value="1" <?php echo(($resultGradeEnabled == 1) ? 'checked' : ''); ?>>
                                                    </label>
                                                </div>
                                                <div style="display: none;" id="resultGrade">
                                                    <div>
                                                        <strong><?php _e('Hint:', 'fcc');?></strong>
                                                        <ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
                                                            <li><?php _e('Maximal 15 levels', 'fcc');?></li>
                                                            <li>
                                                                <?php echo sprintf(__('Percentages refer to the total score of the %s . (Current total 0 points in 0 questions.','fcc'),LearnDash_Custom_Label::get_label('quiz'));?>
                                                            </li>
                                                            <li><?php _e('Values can also be mixed up', 'fcc');?></li>
                                                            <li><?php _e('10,15% or 10.15% allowed (max. two digits after the decimal point)', 'fcc');?></li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <ul id="resultList">
                                                            <?php if ($resultGradeEnabled == 1) {
                                                            foreach ($resultText['text'] as $key => $value) {
                                                            ?>
                                                            <li style="padding: 5px; border: 1; border: 1px dotted;" >
                                                                <div style="margin-bottom: 5px;">
                                                                    <?php wp_editor($value, 'resultText_'.$resultText_count, array('textarea_name' => 'resultTextGrade[text][]'));
                                                                ?></div>
                                                                <div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;"><?php _e('from:', 'fcc');
                                                                    ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="<?php echo $resultText['prozent'][$key];
                                                                    ?>"> <?php _e('percent (Will be displayed, when result-percent is &gt;= ', 'fcc');
                                                                    ?><span class="resultProzent">0</span>%)                                        <input type="button" style="float: right;" class="button-primary deleteResult" value="<?php _e('Delete graduation', 'fcc');
                                                                    ?>">
                                                                    <div style="clear: right;"></div>
                                                                    <input type="hidden" value="1" name="resultTextGrade[activ][]">
                                                                </div>
                                                            </li>
                                                            <?php
                                                            ++$resultText_count;
                                                            }
                                                            }
                                                            ?>
                                                            
                                                            <?php
                                                            for ($result_count = $resultText_count;$result_count < 16;++$result_count) {
                                                            ?>
                                                            <li style="padding: 5px; border: 1; border: 1px dotted;display:none;" >
                                                                <div style="margin-bottom: 5px;">
                                                                    <?php wp_editor('', 'resultText_'.$result_count, array('textarea_name' => 'resultTextGrade[text][]'));
                                                                ?></div>
                                                                <div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
                                                                    <?php _e('from:', 'fcc');
                                                                    ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="0"> <?php _e('percent (Will be displayed, when result-percent is &gt;= ', 'fcc');
                                                                    ?><span class="resultProzent">0</span>%)                                        <input type="button" style="float: right;" class="button-primary deleteResult" value="<?php _e('Delete graduation', 'fcc');
                                                                    ?>">
                                                                    <div style="clear: right;"></div>
                                                                    <input type="hidden" value="0" name="resultTextGrade[activ][]">
                                                                </div>
                                                                <?php
                                                                }
                                                                ?>
                                                                
                                                            </li>
                                                            
                                                            
                                                        </ul>
                                                        <input type="button" class="button-primary addResult" value="<?php _e('Add graduation', 'fcc');?>">
                                                    </div>
                                                </div>
                                                <div id="resultNormal">
                                                    <?php
                                                    if ($resultGradeEnabled == 1) {
                                                    $resultText = '';
                                                    }
                                                    wp_editor($resultText, 'resultText');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div style="float: left;">
                                            <input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="Save">
                                        </div>-->
                                        <!--                  <div style="float: right;">
                                                                <input type="text" placeholder="template name" class="regular-text" name="templateName" style="border: 1px solid rgb(255, 134, 134);">
                                                                <select name="templateSaveList">
                                                                        <option value="0">=== Create new template === </option>
                                                                </select>
                                                                <input type="submit" name="template" class="button-primary" id="wpProQuiz_saveTemplate" value="Save as template">
                                        </div>-->
                                        <div style="clear: both;"></div>
                                    </div>
                                </div>
                            </div>
                        </span>
                        <p style="clear:left"></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'enabled') == 'yes') {
                ?>
                <h3><?php echo sprintf( __('%s Builder', 'fcc'), LearnDash_Custom_Label::get_label('quiz') ); ?></h3>
                <div>
                    <div id="sfwd-header" style="display: none !important;"></div>
                    <input type="hidden" name="quizid" value="<?php echo $this->quiz_id; ?>">
                    <?php $this->quiz_builder->show_builder_box(get_post($this->quiz_id)); ?>
                    <hr>
                    <span class="fcc-builder-labels"><?php _e('Navigation', 'fcc'); ?></span>
                    <div id ="quiz_navigation_app"></div>
                    <span class="fcc-builder-labels"><?php _e(LearnDash_Custom_Label::get_label('questions')); ?></span>
                    <div id ="sfwd-questions-app"></div>
                </div>
                <?php
            }else if(isset($_GET['quizid'])){?>
            <h3><?php echo __('Questions', 'fcc'); ?></h3>
            <div>
                <?php
                $param=(isset($_GET['quizid']) ? ' '.$_GET['quizid'] : '');
                echo do_shortcode('[wdm_question_list'.$param.']');
                ?>
            </div>
            <?php } ?>
            <?php if(isset($_GET['quizid'])){ ?>
            <h3><?php echo __('Associated contents','fcc'); ?></h3>
            <div>
                <?php
                $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : learndash_get_course_id( @$_GET['quizid'] );
                
                if ( !empty( $course_id ) ) {
                $course = get_post( $course_id );
                $course_settings = learndash_get_setting( $course );
                $lessons = learndash_get_course_lessons_list( $course );
                if (( isset( $course_id ) ) && ( !empty( $course_id ) )) {
                // Normally this will be called on a Course/Lesson/Topic/Quiz admin page or front-end where the post var is available.
                if ( isset( $_GET['quizid'] ) ) {
                $post_id = intval( $_GET['quizid'] );
                $post = get_post( $post_id );
                if ( $post->post_type == 'sfwd-topic' || $post->post_type == 'sfwd-quiz' ) {
                $lesson_id = learndash_get_setting( $post, 'lesson' );
                } else {
                $lesson_id = $post->ID;
                }
                } else {
                $post_id = 0;
                $lesson_id = 0;
                }
                include_once trailingslashit(dirname(dirname(__FILE__))) . 'templates/associated-contents.php';
                }
                }
                if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
                // learndash_course_switcher_admin( $course_id );
                $sfwd_post=isset($_GET['quizid']) ? $_GET['quizid'] : '0';
                include trailingslashit(dirname(dirname(__FILE__))) . 'templates/course_navigation_switcher_admin.php';
                }
                ?>
            </div>
            <?php } ?>
        </div>
        <br><br>
        <input type ="hidden" name="wdm_quiz_action" value="<?php echo(isset($_GET[ 'quizid' ]) ? 'edit' : 'add'); ?>" />
        <input type ="hidden" name="fcc-post-type" value="sfwd-quiz" />
        <?php if (isset($_GET[ 'quizid' ])) {
        ?>
        <input type ="hidden" name ="quizid" value ="<?php echo $_GET[ 'quizid' ];
        ?>">
        <input type="hidden" name='sfwd-quiz_quiz_pro' value='<?php echo $sfwd_quiz_quiz_pro;
        ?>'>
        <?php } ?>
        <input type="submit" value="<?php _e('Speichern', 'fcc');?>" id="wdm_quiz_submit">
    </form>
</div>