<?php
$view_lessons = bp_core_get_user_domain(get_current_user_id()).'listing/lesson_listing/';
$view_quizzes = bp_core_get_user_domain(get_current_user_id()).'listing/quiz_listing/';
$sql = "SELECT ID,post_modified FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('draft','publish','trash')";
$results = $wpdb->get_results($sql);
$sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_course_creation]%' AND post_status like 'publish'";
$course_result = $wpdb->get_var($sql);
$link = get_permalink($course_result);
$is_default_cat = false;
$is_default_tag = false;
//echo "<pre>";print_r($results);echo "</pre>";
$is_ver_greater = version_compare(LEARNDASH_VERSION, "2.3.3", ">");
if (class_exists('LearnDash_Settings_Section')) {
    $is_default_cat=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_category') == 'yes') ? true : false;
    $is_default_tag=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_tag') == 'yes') ? true : false;
}
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') ? true : false;
?>
<button onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_course_list">
    <thead>
        <tr>
            <th><?php echo __('Title', 'fcc'); ?></th>
            <?php
            if (( $is_ver_greater && $is_default_cat) || version_compare(LEARNDASH_VERSION, "2.4.0", "<")) {
            ?>
            <th><?php echo __('Categories', 'fcc'); ?></th>
            <?php } ?>
            <?php
            if ($is_ver_greater && $is_default_tag  || version_compare(LEARNDASH_VERSION, "2.4.0", "<")) {
            ?>
            <th><?php echo __('Tags', 'fcc'); ?></th>
            <?php } ?>
            <th><?php echo __('Actions', 'fcc'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $k => $v) { ?>
        <tr>
            <td><?php echo get_the_title($v->ID); ?></td>
            <?php
            if ($is_ver_greater && $is_default_cat || !$is_ver_greater) {
            ?>
            <td><?php $post_categories = wp_get_post_categories($v->ID);
            if (count($post_categories) > 0) {
                $cats = array();
                foreach ($post_categories as $c) {
                    $cat = get_category($c);
                    $cats[] = $cat->name;
                }
                if (count($cats) > 0) {
                    echo implode(', ', $cats);
                }
            } else {
                echo '-';
            }
            ?></td>
            <?php
            }
            if ($is_ver_greater && $is_default_tag || !$is_ver_greater) {
            ?>
            <td><?php $post_tags = wp_get_post_tags($v->ID);
            //echo "<pre>";print_R($post_tags);echo "</pre>";
            if (count($post_tags) > 0) {
                $tags = array();
                foreach ($post_tags as $c) {
                    $tags[] = $c->name;
                }
                if (count($tags) > 0) {
                    echo implode(', ', $tags);
                }
            } else {
                echo '-';
            }
            
            ?></td>
            <?php } ?>
            <td>
                <?php
                    $preview_link='';
                if (get_post_status($v->ID)=='draft') {
                    $preview_link = '&wdm_preview=1';
                }
                if (get_post_status($v->ID)=='trash') {
                    ?>
                   <div class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $v->ID; ?>" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                        <a style="display: none;" class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($v->ID).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></span></a>
                        <a style="display: none;" class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></span></a>
                        <div style="display: none;" class="wdm_tooltip wdm_trash" data-post_id="<?php echo $v->ID; ?>" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                        <?php
                        if (!$sharedCourse) {
                        ?>
                        <a style="display: none;" class="wdm_tooltip wdm_view_lessons" href = "<?php echo $view_lessons.'?courseid='.$v->ID;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('lessons')); ?></span></a>
                        <a style="display: none;" class="wdm_tooltip wdm_view_quizzes" href = "<?php echo $view_quizzes.'?courseid='.$v->ID;?>"><img src="<?php echo plugins_url('images/view_quizzes.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quizzes')); ?></span></a>
                        <?php
                        }
                } else {
                    ?>
                       <div style="display: none;" class="wdm_tooltip wdm_remove_trash" data-post_id="<?php echo $v->ID; ?>" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/undo_trash.png', dirname(dirname(__FILE__))); ?>" width="50" height="50"><span class="wdm_tooltiptext"><?php echo __('Restore', 'fcc'); ?></span></div>
                       <a class="wdm_tooltip wdm_view" href = "<?php echo get_permalink($v->ID).$preview_link; ?>"><img src="<?php echo plugins_url('images/view-quiz.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></span></a>
                       <a class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('Edit %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></span></a>
                       <div class="wdm_tooltip wdm_trash" data-post_id="<?php echo $v->ID; ?>" href = "<?php echo add_query_arg(array("courseid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Move to Trash', 'fcc'); ?></span></div>
                        <?php
                        if (!$sharedCourse) {
                        ?>
                       <a class="wdm_tooltip wdm_view_lessons" href = "<?php echo $view_lessons.'?courseid='.$v->ID;?>"><img src="<?php echo plugins_url('images/questions.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('lessons')); ?></span></a>
                       <a class="wdm_tooltip wdm_view_quizzes" href = "<?php echo $view_quizzes.'?courseid='.$v->ID;?>"><img src="<?php echo plugins_url('images/view_quizzes.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo sprintf(__('View %s', 'fcc'), LearnDash_Custom_Label::get_label('quizzes')); ?></span></a>
                        <?php
                        }
                } ?>
            </td>
        </tr>   
            
        <?php } ?>
        
    </tbody>
</table>

<script>
jQuery(document).ready(function(){
jQuery('#wdm_course_list').dataTable();
    
});
</script>