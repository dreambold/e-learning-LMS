<style>
    ul.tabs {
        margin: 0px;
        padding: 0px;
        list-style: none;
    }
    
    ul.tabs li {
        background: none;
        color: #007cba;
        display: inline-block;
        padding: 10px 15px;
        cursor: pointer;
    }
    
    ul.tabs li.current {
        background: #ffffff;
        color: #007cba;
        border-radius: 5px 5px 0 0;
        font-weight: 700;
    }
    
    .tab-content {
        display: none;
        background: #ffffff;
        padding: 15px;
    }
    
    .tab-content.current {
        display: inherit;
    }
</style>

<form method="POST" action="<?php echo plugins_url('save-courses.php', __FILE__); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
    <div class="add-header">
        <h1><input id="post_title" name="post_title" title="Title" type="text" autocomplete="on"  placeholder="Title"></h1>
    </div>
    <input type="hidden" id="id" name="id" value="<?php // echo get_the_ID();?>">
    <input type="hidden" id="attachment_id" name="attachment_id" value="<?php if (!is_wp_error($attachment_id)) {
        echo $attachment_id;
    } ?>">
    <input type="hidden" id="user_id" name="user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="post_date" name="post_date" value="<?php echo current_time(" Y-m-d H:i:s "); ?>">
    <input type="hidden" id="post_date_gmt" name="post_date_gmt" value="<?php echo get_gmt_from_date(current_time(" Y-m-d H:i:s ")); ?>">
    <input type="hidden" id="post_status" name="post_status" value="publish">
    <input type="hidden" id="comment_status" name="comment_status" value="closed">
    <input type="hidden" id="ping_status" name="ping_status" value="closed">
    <input type="hidden" id="post_password" name="post_password" value="">
    <input type="hidden" id="modified_date" name="modified_date" value="<?php echo current_time(" Y-m-d H:i:s "); ?>">
    <input type="hidden" id="modified_date_gmt" name="modified_date_gmt" value="<?php echo get_gmt_from_date(current_time(" Y-m-d H:i:s ")); ?>">
    <input type="hidden" id="post_parent" name="post_parent" value="">
    <input type="hidden" id="site_url" name="site_url" value="<?php echo get_site_url() ?>">
    <input type="hidden" id="menu_order" name="menu_order" value="0">
    <input type="hidden" id="post_type" name="post_type" value="sfwd-courses">
    <input type="hidden" id="comment_count" name="comment_count" value="0">
    <div id="primary" class="content-area">

        <ul class="tabs">
            <li class="tab-link current" data-tab="tab-1">Content</li>
            <li class="tab-link" data-tab="tab-2">Builder</li>
            <li class="tab-link" data-tab="tab-3">Settings</li>
        </ul>

        <div id="tab-1" class="tab-content current">

            <h3>Content</h3>
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
            'quicktags' => 0,
            'drag_drop_upload' => true);
            wp_editor('', 'post_content', $args);
            ?>

<div class="toolset-shortcodes-gui-dialog-group"><h4 data-id="ee11cbb19052e40b07aac0ca060c23ee" class="group-title  editor-addon-link-ee11cbb19052e40b07aac0ca060c23ee-target">User data</h4>
<ul class="wpv-shortcode-gui-group-list js-wpv-shortcode-gui-group-list">
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User ID', params: {attributes:{field:'ID'}} }); return false;">User ID</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User Email', params: {attributes:{field:'user_email'}} }); return false;">User Email</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User Login', params: {attributes:{field:'user_login'}} }); return false;">User Login</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'First Name', params: {attributes:{field:'user_firstname'}} }); return false;">First Name</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Last Name', params: {attributes:{field:'user_lastname'}} }); return false;">Last Name</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Nickname', params: {attributes:{field:'nickname'}} }); return false;">Nickname</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Display Name', params: {attributes:{field:'display_name'}} }); return false;">Display Name</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Profile Picture', params: {attributes:{field:'profile_picture'}} }); return false;">Profile Picture</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Nicename', params: {attributes:{field:'user_nicename'}} }); return false;">Nicename</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Description', params: {attributes:{field:'description'}} }); return false;">Description</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Yahoo IM', params: {attributes:{field:'yim'}} }); return false;">Yahoo IM</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Jabber', params: {attributes:{field:'jabber'}} }); return false;">Jabber</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'AIM', params: {attributes:{field:'aim'}} }); return false;">AIM</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User URL', params: {attributes:{field:'user_url'}} }); return false;">User URL</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'Registration Date', params: {attributes:{field:'user_registered'}} }); return false;">Registration Date</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User Status', params: {attributes:{field:'user_status'}} }); return false;">User Status</button></li>
<li class="item" style="display: inline-block;"><button class="button button-secondary button-small" onclick="WPViews.shortcodes_gui.wpv_insert_shortcode_dialog_open({ shortcode: 'wpv-user', title: 'User Spam Status', params: {attributes:{field:'spam'}} }); return false;">User Spam Status</button></li>
</ul>
</div>
        </div>

        <div id="tab-2" class="tab-content">

            <h3>Builder</h3>
            <p>Some text</p>
        </div>

        <div id="tab-3" class="tab-content">

            <h3>Settings</h3>
            <div class="edit-post-layout__metaboxes">
                <div class="edit-post-meta-boxes-area is-normal">
                    <div class="edit-post-meta-boxes-area__container">
                        <div id="poststuff" class="sidebar-open">
                            <div id="postbox-container-2" class="postbox-container">
                                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                    <div id="sfwd-courses" class="postbox">
                                        <div class="inside">
                                            <div class="sfwd sfwd_options sfwd-courses_settings">
                                                <div class="sfwd_input " id="sfwd-courses_course_materials">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Options for Course materials"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Materials</a></span>
                                                    <textarea name="sfwd-courses_course_materials" rows="2" cols="57"></textarea>
                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_price_type">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Is it open to all, free join, one time purchase, or a recurring subscription?"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Price Type</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_price_type">
                                                    <option selected="" value="open">Open</option>
                                                    <option value="closed">Closed</option>
                                                    <option value="free">Free</option>
                                                    <option value="paynow">Buy Now</option>
                                                    <option value="subscribe">Recurring</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_custom_button_url" style="display: block;">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Entering a URL in this field will enable the 'Take this Course' button. The button will not display if this field is left empty. Relative URL beginning with a slash is acceptable." onclick="toggleVisibility('sfwd-courses_custom_button_url_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Custom Button URL</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_custom_button_url" type="text" size="57" placeholder="Optional" value=""></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_price" style="display: block;">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Enter Course price here. Leave empty if the Course is free." onclick="toggleVisibility('sfwd-courses_course_price_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Price</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_price" type="text" size="57" value=""></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_price_billing_cycle">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Billing Cycle for the recurring payments in case of a subscription." onclick="toggleVisibility('sfwd-courses_course_price_billing_cycle_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Billing Cycle</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <input name="course_price_billing_p3" type="text" value="" size="2"> 
                                                    <select class="select_course_price_billing_p3" name="course_price_billing_t3">
                                                    <option value="D">day(s)</option>
                                                    <option value="W">week(s)</option>
                                                    <option value="M">month(s)</option>
                                                    <option value="Y">year(s)</option>
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_access_list">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="This field is auto-populated with the UserIDs of those who have access to this course." onclick="toggleVisibility('sfwd-courses_course_access_list_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Access List</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><textarea name="sfwd-courses_course_access_list" rows="2" cols="57"></textarea></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_lesson_orderby">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Choose the sort order of lessons in this course." onclick="toggleVisibility('sfwd-courses_course_lesson_orderby_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Sort Lesson By</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_lesson_orderby">
                                                    <option selected="" value="">Use Default ( Date )</option>
                                                    <option value="title">Title</option>
                                                    <option value="date">Date</option>
                                                    <option value="menu_order">Menu Order</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_lesson_order">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Choose the sort order of lessons in this course." onclick="toggleVisibility('sfwd-courses_course_lesson_order_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Sort Lesson Direction</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_lesson_order">
                                                    <option selected="" value="">Use Default ( Descending )</option>
                                                    <option value="ASC">Ascending</option>
                                                    <option value="DESC">Descending</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_lesson_per_page">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Choose the per page of lessons in this course." onclick="toggleVisibility('sfwd-courses_course_lesson_per_page_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Lessons Per Page</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_lesson_per_page">
                                                    <option selected="" value="">Use Default ( 25 )</option>
                                                    <option value="CUSTOM">Custom</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_lesson_per_page_custom" style="display: block;">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Enter Lesson per page value. Set to zero for no paging" onclick="toggleVisibility('sfwd-courses_course_lesson_per_page_custom_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Custom Lessons Per Page</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_lesson_per_page_custom" type="number" min="0" step="1" value="0"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_prerequisite_enabled">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Leave this field unchecked if prerequisite not used." onclick="toggleVisibility('sfwd-courses_course_prerequisite_enabled_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Enable Course Prerequisites</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_prerequisite_enabled" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_prerequisite" style="display: block;">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Select one or more course as prerequisites to view this course" onclick="toggleVisibility('sfwd-courses_course_prerequisite_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Prerequisites</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_prerequisite[]" multiple="">
                                                    <option value="0">-- Select a Course --</option>
                                                    <?php
                                                    $query = new WP_Query(array('post_type' => 'sfwd-courses', 'orderby' => 'author',));
                                                    if ($query->have_posts()) {
                                                    while ($query->have_posts()):
                                                    $query->the_post();
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

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_prerequisite_compare" style="display: block;">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Select how to compare the selected prerequisite course." onclick="toggleVisibility('sfwd-courses_course_prerequisite_compare_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Prerequisites Compare</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_course_prerequisite_compare">
                                                    <option value="ANY">ANY (default) - The student must complete at least one of the prerequisites</option>
                                                    <option selected="" value="ALL">ALL - The student must complete all the prerequisites</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_points_enabled">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Leave this field unchecked if points not used." onclick="toggleVisibility('sfwd-courses_course_points_enabled_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Enable Course Points</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_points_enabled" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_points">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Enter the number of points a user will receive for this Course." onclick="toggleVisibility('sfwd-courses_course_points_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Points</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_points" type="number" min="0" step="any" value="0"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_points_access">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Enter the number of points a user must have to access this Course." onclick="toggleVisibility('sfwd-courses_course_points_access_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Course Points Access</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_points_access" type="number" min="0" step="any" value="0"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_disable_lesson_progression">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Disable the feature that allows attempting lessons only in allowed order." onclick="toggleVisibility('sfwd-courses_course_disable_lesson_progression_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Disable Lesson Progression</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_disable_lesson_progression" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_expire_access">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Leave this field unchecked if access never expires." onclick="toggleVisibility('sfwd-courses_expire_access_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Expire Access</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_expire_access" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_expire_access_days">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Enter the number of days a user has access to this Course." onclick="toggleVisibility('sfwd-courses_expire_access_days_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Expire Access After (days)</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_expire_access_days" type="number" min="0" step="1" value=""></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_expire_access_delete_progress">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Select this option if you want the user's Course progress to be deleted when their access expires." onclick="toggleVisibility('sfwd-courses_expire_access_delete_progress_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Delete Course and Quiz Data After Expiration</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_expire_access_delete_progress" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_course_disable_content_table">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Hide Course Content table when user is not enrolled." onclick="toggleVisibility('sfwd-courses_course_disable_content_table_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Hide Course Content Table</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div"><input name="sfwd-courses_course_disable_content_table" type="checkbox"></div>
                                                    </span>

                                                </div>
                                                <div class="sfwd_input " id="sfwd-courses_certificate">
                                                    <span class="sfwd_option_label"><a class="sfwd_help_text_link" style="display: flex;align-items: center;" style="cursor:pointer;" title="Select a certificate to be awarded upon course completion (optional)." onclick="toggleVisibility('sfwd-courses_certificate_tip');"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/question.png'; ?>">Associated Certificate</a></span>
                                                    <span class="sfwd_option_input">
                                                    <div class="sfwd_option_div">
                                                    <select name="sfwd-courses_certificate">
                                                    <option value="0">-- Select a Certificate --</option>
                                                    \n
                                                    </select>
                                                    </div>
                                                    </span>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>

        </div>

    </div>

    <div>
        <input class="button" type="submit" value="Save" />
    </div>

</form>

