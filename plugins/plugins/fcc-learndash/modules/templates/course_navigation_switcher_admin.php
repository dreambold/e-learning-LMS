<?php
if ($sfwd_post!='0') {
    $post = get_post($sfwd_post);
    if (is_a($post, 'WP_Post') && ( in_array($post->post_type, array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' )) )) {
        $cb_courses = learndash_get_courses_for_step($post->ID);
        $count_primary = 0;
        $count_secondary = 0;

        if (isset($cb_courses['primary'])) {
            $count_primary = count($cb_courses['primary']);
        }

        if (isset($cb_courses['secondary'])) {
            $count_secondary = count($cb_courses['secondary']);
        }

        if (( count($count_primary) > 0 ) || ( ( count($count_primary) == 0 ) && ( $count_secondary > 1 ) )) {
            $default_course_id = learndash_get_course_id($post->ID, true);

            $course_post_id = 0;
            if (isset($_GET['course_id'])) {
                $course_post_id = intval($_GET['course_id']);
            }
    
            ?><p class="widget_course_switcher"><?php echo sprintf(_x('%s switcher', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label('Course')); ?><br />
            <span class="ld-course-message" style="display:none"><?php echo sprintf(_x('Switch to the Primary %s to edit this setting', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label('Course')) ?></span>
            <input type="hidden" id="ld-course-primary" name="ld-course-primary" value="<?php echo $default_course_id; ?>" />

            <?php
                $item_url = get_edit_post_link($post->ID);
            ?>
            <select name="ld-course-switcher" id="ld-course-switcher">
                <option value=""><?php echo sprintf(_x('Select a %s', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label('Course')); ?></option>
                <?php
                if (( $post->post_type == 'sfwd-quiz' ) && ( empty($count_primary) ) && ( empty($count_secondary) )) {
                    $selected = ' selected="selected" ';
                    ?><option <?php echo $selected ?> data-course_id="0" value="<?php echo remove_query_arg('course_id', $item_url); ?>"><?php echo sprintf(_x('Standalone %s', 'placeholder: Quiz', 'learndash'), LearnDash_Custom_Label::get_label('Quiz')); ?></option><?php
                }
                ?>
                <?php
                $use_select_opt_groups = false;
                if (( isset($cb_courses['primary']) ) && ( !empty($cb_courses['primary']) ) && ( isset($cb_courses['secondary']) ) && ( !empty($cb_courses['secondary']) )) {
                    $use_select_opt_groups = true;
                }

                foreach ($cb_courses as $course_key => $course_set) {
                    if ($use_select_opt_groups === true) {
                        if ($course_key == 'primary') {
                            ?><optgroup label="<?php echo sprintf(_x('Primary %s', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label('Course')) ?>"><?php
                        } elseif ($course_key == 'secondary') {
                            ?><optgroup label="<?php echo sprintf(_x('Other %s', 'placeholder: Courses', 'learndash'), LearnDash_Custom_Label::get_label('Courses')) ?>"><?php
                        }
                    }
            
                    foreach ($course_set as $course_id => $course_title) {
                        //if ( intval( $course_id ) != intval( $default_course_id ) ) {
                            $item_url = add_query_arg('course_id', $course_id, $item_url);
                        //}
                        
                        $selected = '';
                        if ($post->post_type == 'sfwd-quiz') {
                            if ($course_id == $course_post_id) {
                                $selected = ' selected="selected" ';
                            }
                        } else {
                            if (( $course_id == $course_post_id ) || ( ( empty($course_post_id) ) && ( $course_id == $default_course_id ) )) {
                                $selected = ' selected="selected" ';
                            }
                        }
                        ?><option <?php echo $selected ?> data-course_id="<?php echo $course_id ?>" value="<?php echo $item_url; ?>"><?php echo get_the_title($course_id);  ?></option><?php
                    }
            
                    if ($use_select_opt_groups === true) {
                        ?></optgroup><?php
                    }
                }
            ?></select></p><?php
        }
    }
}