<?php
$sql = "SELECT ID,post_modified FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-quiz' AND post_status IN ('draft','publish','trash')";
$results = $wpdb->get_results($sql);
$quizzes = array();
foreach ($results as $key => $value) {
    array_push($quizzes, $value->ID);
}
$quizzes=apply_filters('wdm_filter_lessons_listing_page', $quizzes, 'sfwd-quiz');
$sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_quiz_creation]%' AND post_status IN ('draft','publish','trash')";
$course_result = $wpdb->get_var($sql);
$link = get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') ? true : false;
do_action('wdm_list_quiz_top_section', 'sfwd-quiz');
?>
<button onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_quiz_list">
    <thead>
        <tr>
            <th><?php echo __('Title', 'fcc'); ?></th>
            <?php if (!$sharedCourse) { ?>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></th>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></th>
            <?php } ?>
            <th><?php echo __('Actions', 'fcc'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><?php echo __('Title', 'fcc'); ?></th>
            <?php if (!$sharedCourse) { ?>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></th>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></th>
            <?php } ?>
            <th><?php echo __('Actions', 'fcc'); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach ($quizzes as $quiz_id) { ?>
        <tr>
            <td><?php echo get_the_title($quiz_id); ?></td>
            <?php if (!$sharedCourse) { ?>
            <td><?php
            $course_id = get_post_meta($quiz_id, 'course_id', true);
            echo (($course_id != 0) ? get_the_title($course_id) : '-' );
            
            ?></td>
            <td><?php $lesson_id = get_post_meta($quiz_id, 'lesson_id', true);
            echo (($lesson_id != 0) ? get_the_title($lesson_id) : '-' );
            
            ?></td>
            <?php } ?>
            <td>
                <?php
                    $preview_link='';
                if (get_post_status($quiz_id)=='draft') {
                    $preview_link = '&wdm_preview=1';
                }
                if (get_post_status($quiz_id)=='trash') {
                    ?>
                    <div class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $quiz_id; ?>" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                        <a style="display: none;" class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($quiz_id).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span></a>
                        <a style="display: none;" class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span></a>
                        <div style="display: none;" class="wdm_tooltip wdm_trash" data-post_id="<?php echo $quiz_id; ?>" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                        <a style="display: none;" class="wdm_tooltip wdm_view_lessons" href = "<?php echo get_permalink(get_option('wdm_question_list_page')).'?quiz_id='.$quiz_id;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('View Questions', 'fcc'); ?></span></a>
                        <?php
                } else {
                    ?>
                       <div style="display: none;" class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $quiz_id; ?>" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                       <a class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($quiz_id).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span></a>
                       <a class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></span></a>
                       <div class="wdm_tooltip wdm_trash" data-post_id="<?php echo $quiz_id; ?>" href = "<?php echo add_query_arg(array("quizid" => $quiz_id), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                       <a class="wdm_tooltip wdm_view_lessons" href = "<?php echo get_permalink(get_option('wdm_question_list_page')).'?quiz_id='.$quiz_id;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('View Questions', 'fcc'); ?></span></a>
                        <?php
                } ?>
            </td>
        </tr>   
            
        <?php } ?>
        
    </tbody>
</table>
<?php
if (!$sharedCourse) {
    $course_list = array();
    $sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('draft','publish')";
    $results = $wpdb->get_col($sql);
//echo '<pre>';print_R($results);echo '</pre>';
    if (!empty($results)) {
        foreach ($results as $k => $v) {
            if (!in_array($v, $course_list)) {
                $course_list[] = $v;
            }
        }
    }

    $course_list[] = '-';
    $temp = implode("','", $course_list);
    $course_names = "['".$temp."']";
    $lesson_list = array();
    $sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('draft','publish')";
    $results = $wpdb->get_col($sql);
//echo '<pre>';print_R($results);echo '</pre>';
    if (!empty($results)) {
        foreach ($results as $k => $v) {
            if (!in_array($v, $lesson_list)) {
                $lesson_list[] = $v;
            }
        }
    }

    $lesson_list[] = '-';
    $temp = implode("','", $lesson_list);
    $lesson_names = "['".$temp."']";
}
//echo $course_names;
?>
<script>
jQuery(document).ready(function($){
    <?php if (!$sharedCourse) { ?>
         var selected_lesson="<?php echo isset($_GET['lessonid']) ? html_entity_decode(addslashes(get_the_title($_GET['lessonid']))) : ''; ?>";
         var selected_course="<?php echo isset($_GET['courseid']) ? html_entity_decode(addslashes(get_the_title($_GET['courseid']))) : ''; ?>";
         $('#wdm_quiz_list').dataTable()
                  .columnFilter({
                    aoColumns: [ { type: "text" },
                             { type: "select",
                             selected: selected_course
                        },
                             { type: "select",
                             selected: selected_lesson
                         },
                             null,
                             null
                        ]

                });

            jQuery(document).find('select').each(function(element){
                jQuery(this).find('option').each(function(option){
                    if(jQuery(this).text().indexOf('Assigned')== -1){
                        jQuery(this).val(jQuery(this).text());
                        if(jQuery(this).val()==selected_course){
                            jQuery(this).parent().val(selected_course);
                        }else if(jQuery(this).val()==selected_lesson){
                            jQuery(this).parent().val(selected_lesson);
                        }
                    }
                });
            });
    <?php } else { ?>
        $('#wdm_quiz_list').dataTable()
                  .columnFilter({
                    aoColumns: [ { type: "text" },
                             null
                        ]

                });
    <?php } ?>
});
</script>