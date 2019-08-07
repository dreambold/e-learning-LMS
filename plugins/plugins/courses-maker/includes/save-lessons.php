<?php

require_once( '../../../wp-config.php' );



global $wpdb;
global $post;

if( current_user_can('publish_posts')) {

  // editor

  $table_name  = $wpdb->prefix."posts";
  $post_name = $_POST['add_title'];



  $new_post_name = str_replace(" ","-",mb_strtolower($post_name));

  if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'", 'ARRAY_A')) {

      $new_post_name = wp_unique_post_slug( $new_post_name, $wpdb->get_var("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'"), $_POST['add_status'], $_POST['add_post_type'], $_POST['add_parent']);

  } 

  $guid = get_site_url('','lessons/'.$new_post_name);

  // if (isset ($_POST['add_title'])) {
  //     $title =  $_POST['add_title'];
  // } else {
  //     echo 'Please enter a  title';
  // }
  // if (isset ($_POST['add_content'])) {
  //     $description = $_POST['add_content'];
  // } else {
  //     echo 'Please enter the content';
  // }

  $values = array(

    'post_author' => $_POST['add_user_id'],

    'post_date' => $_POST['add_date'],

    'post_date_gmt' => $_POST['add_date_gmt'],

    'post_content' => $_POST['add_content'],

    'post_title' => $_POST['add_title'],

    'post_status' => $_POST['add_status'],

    'comment_status' => $_POST['add_comment'],

    'ping_status' => $_POST['add_ping'],

    'post_password' => $_POST['add_password'],

    'post_name' => $new_post_name,

    'post_modified' => $_POST['add_modified'],

    'post_modified_gmt' => $_POST['add_modified_gmt'],

    'post_parent' => $_POST['add_parent'],

    'guid' => $guid,

    'menu_order' => $_POST['add_menu_order'],

    'post_type' => $_POST['add_post_type'],

    'comment_count' => $_POST['add_comment_count'],

  );





  if (intval($_POST['id'])) {

    $filter = array('id' => $_POST['id']);

    $wpdb->update($table_name, $values, $filter);

    
  }

  else {

    if (!$wpdb->insert($table_name, $values)) {

      wp_redirect(get_site_url().'/add-lesson&err='.urlencode($wpdb->last_error));

      exit;

    }

    $post_id = $wpdb->insert_id;
    $meta_value = array(

    'sfwd-lessons_lesson_materials' => $_POST['sfwd-lessons_lesson_materials'],

    'sfwd-lessons_course' => $_POST['sfwd-lessons_course'],

    'sfwd-lessons_forced_lesson_time' => $_POST['sfwd-lessons_forced_lesson_time'],

    'sfwd-lessons_lesson_assignment_upload' => $_POST['sfwd-lessons_lesson_assignment_upload'],

    'sfwd-lessons_auto_approve_assignment' => $_POST['sfwd-lessons_auto_approve_assignment'],

    'sfwd-lessons_assignment_upload_limit_count' => $_POST['sfwd-lessons_assignment_upload_limit_count'],

    'sfwd-lessons_lesson_assignment_deletion_enabled' => $_POST['sfwd-lessons_lesson_assignment_deletion_enabled'],

    'sfwd-lessons_lesson_assignment_points_enabled' => $_POST['sfwd-lessons_lesson_assignment_points_enabled'],

    'sfwd-lessons_lesson_assignment_points_amount' => $_POST['sfwd-lessons_lesson_assignment_points_amount'],

    'sfwd-lessons_assignment_upload_limit_extensions' => $_POST['sfwd-lessons_assignment_upload_limit_extensions'],

    'sfwd-lessons_assignment_upload_limit_size' => $_POST['lessons_assignment_upload_limit_size'],

    'sfwd-lessons_sample_lesson' => $_POST['sfwd-lessons_sample_lesson'],

    'sfwd-lessons_visible_after' => $_POST['sfwd-lessons_visible_after'],

    'sfwd-lessons_visible_after_specific_date[mm]' => $_POST['sfwd-lessons_visible_after_specific_date[mm]'],

    'sfwd-lessons_visible_after_specific_date[jj]' => $_POST['sfwd-lessons_visible_after_specific_date[jj]'],

    'sfwd-lessons_visible_after_specific_date[aa]' => $_POST['sfwd-lessons_visible_after_specific_date[aa]'],

    'sfwd-lessons_visible_after_specific_date[hh]' => $_POST['sfwd-lessons_visible_after_specific_date[hh]'],

    'sfwd-lessons_visible_after_specific_date[mn]' => $_POST['sfwd-lessons_visible_after_specific_date[mn]'],

    'sfwd-lessons_lesson_video_enabled' => $_POST['sfwd-lessons_lesson_video_enabled'],

    'sfwd-lessons_lesson_video_url' => $_POST['sfwd-lessons_lesson_video_url'],

    'sfwd-lessons_lesson_video_auto_start' => $_POST['sfwd-lessons_lesson_video_auto_start'],

    'sfwd-lessons_lesson_video_show_controls' => $_POST['sfwd-lessons_lesson_video_show_controls'],

    'sfwd-lessons_lesson_video_shown' => $_POST['sfwd-lessons_lesson_video_shown'],

    'sfwd-lessons_lesson_video_auto_complete' => $_POST['sfwd-lessons_lesson_video_auto_complete'],

    'sfwd-lessons_lesson_video_auto_complete_delay' => $_POST['sfwd-lessons_lesson_video_auto_complete_delay'],

    'sfwd-lessons_lesson_video_hide_complete_button' => $_POST['sfwd-lessons_lesson_video_hide_complete_button'],

  );

    set_post_thumbnail( $post_id, $_POST['attachment_id'] );
    update_post_meta( $post_id, '_sfwd-lessons', $meta_value );
    update_post_meta( $post_id, 'course_id', $_POST['sfwd-lessons_course'] );

// var_dump($post_id, $meta_value);
//    var_dump($values);
//    var_dump($table_name);

  }
  // var_dump($wpdb->insert_id);

  wp_redirect($guid);

  exit;

}

?>