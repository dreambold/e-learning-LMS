<?php
$view_topics = bp_core_get_user_domain(get_current_user_id()).'listing/topic_listing/';
$view_quizzes = bp_core_get_user_domain(get_current_user_id()).'listing/quiz_listing/';
$sql = "SELECT ID,post_modified FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('draft','publish','trash')";
$results = $wpdb->get_results($sql);
$lessons = array();
foreach ($results as $key => $value) {
    array_push($lessons, $value->ID);
}
$lessons=apply_filters('wdm_filter_lessons_listing_page', $lessons, 'sfwd-lessons');
$sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_lesson_creation]%' AND post_status like 'publish'";
$course_result = $wpdb->get_var($sql);
$link = get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') ? true : false;
do_action('wdm_list_lesson_top_section', 'sfwd-lessons');
?>
<button onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_lesson_list">
    <thead>
        <tr>
            <th><?php echo __('Title', 'fcc'); ?></th>
            <?php if (!$sharedCourse) {
            ?>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></th>
            <?php }?>
            <th><?php echo __('Action', 'fcc'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><?php echo __('Title', 'fcc'); ?></th>
            <?php if (!$sharedCourse) {
            ?>
            <th><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></th>
            <?php }?>
            <th><?php echo __('Action', 'fcc'); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach ($lessons as $lesson_id) { ?>
        <tr>
            <td><?php echo get_the_title($lesson_id); ?></td>
            <?php if (!$sharedCourse) {
            ?>
            <td><?php
            $course_id = get_post_meta($lesson_id, 'course_id', true);
            echo (($course_id != 0) ? get_the_title($course_id) : '-' );
            ?></td>
            <?php }?>
            <td>
                <?php
                     $preview_link='';
                if (get_post_status($lesson_id)=='draft') {
                    $preview_link = '&wdm_preview=1';
                }
                if (get_post_status($lesson_id)=='trash') {
                    ?>
                   <div class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $lesson_id; ?>" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                        <a style="display: none;" class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($lesson_id).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Assigned %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></span></a>
                        <a style="display: none;" class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></span></a>
                        <div style="display: none;" class="wdm_tooltip wdm_trash" data-post_id="<?php echo $lesson_id; ?>" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                        <?php
                        if (!$sharedCourse) {
                        ?>
                        <a style="display: none;" class="wdm_tooltip wdm_view_lessons" href = "<?php echo $view_topics.'?lessonid='.$lesson_id;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('topics')); ?></span></a>
                        <a style="display: none;" class="wdm_tooltip wdm_view_quizzes" href = "<?php echo $view_quizzes.'?lessonid='.$lesson_id;?>"><img src="<?php echo plugins_url('images/view_quizzes.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quizzes')); ?></span></a>
                        <?php
                        }
                } else {
                    ?>
                       <div style="display: none;" class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $lesson_id; ?>" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                       <a class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($lesson_id).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></span></a>
                       <a class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></span></a>
                       <div class="wdm_tooltip wdm_trash" data-post_id="<?php echo $lesson_id; ?>" href = "<?php echo add_query_arg(array("lessonid" => $lesson_id), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                        <?php
                        if (!$sharedCourse) {
                        ?>
                       <a class="wdm_tooltip wdm_view_lessons" href = "<?php echo $view_topics.'?lessonid='.$lesson_id;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('topic')); ?></span></a>
                       <a class="wdm_tooltip wdm_view_quizzes" href = "<?php echo $view_quizzes.'?lessonid='.$lesson_id;?>"><img src="<?php echo plugins_url('images/view_quizzes.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quizzes')); ?></span></a>
                        <?php
                        }
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
}
?>
<script>
jQuery(document).ready(function($){
    <?php
    if (!$sharedCourse) {
?>
    var selected="<?php echo isset($_GET['courseid']) ? html_entity_decode(addslashes(get_the_title($_GET['courseid']))) : ''; ?>";
 $('#wdm_lesson_list').dataTable()
          .columnFilter({
            aoColumns: [ { type: "text" },
                     { type: "select",
                     selected: selected
                },
                     null,
                     null,
                     null
                ]
        });
    jQuery(document).find('select').each(function(element){
        jQuery(this).find('option').each(function(option){
            if(jQuery(this).text().indexOf('Assigned')== -1){
                jQuery(this).val(jQuery(this).text());
                if(jQuery(this).val()==selected){
                    jQuery(this).parent().val(selected);
                }
            }
        });
    });
            <?php
    } else {
        ?>
        $('#wdm_lesson_list').dataTable()
      .columnFilter({
        aoColumns: [ { type: "text" },
                 null,
            ]
        });
        <?php
    } ?>

});
</script>