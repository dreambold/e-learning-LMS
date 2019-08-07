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

<form method="POST" action="<?php echo plugins_url('save-offline.php', __FILE__); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
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
    <input type="hidden" id="post_type" name="post_type" value="offline-courses">
    <input type="hidden" id="comment_count" name="comment_count" value="0">
    <div id="primary" class="content-area">



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

        <div class="inside">
        <label for="offline_date"><br><b>Date: </b></label>
        <input type="text" id="offline_date" name="offline_date" placeholder="Enter course start date" value="">
        <input type="text" id="offline_date_end" name="offline_date_end" placeholder="Enter course end date" value="">
        <label for="location_value"><br><b>Location: </b></label>
        <input type="text" id="location_value" name="location_value" placeholder="Enter the location Town" value="">
        <input type="text" id="location_adress" name="location_adress" placeholder="Enter the location" value="">
        <label for="course_free_places"><br><b>Vacancies: </b></label>
        <input type="text" id="course_free_places" name="course_free_places" placeholder="Enter the number of vacancies" value="">
        </div>

    </div>

    <div>
        <input class="button" type="submit" value="Save" />
    </div>

</form>

