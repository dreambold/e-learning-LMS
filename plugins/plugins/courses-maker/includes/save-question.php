<?php

require_once( '../../../wp-config.php' );



global $wpdb;
global $post;

if( current_user_can('publish_posts')) {

  // editor

  $table_name  = $wpdb->prefix."posts";
  $table_name_2  = $wpdb->prefix."wp_pro_quiz_question";
  $post_name = $_POST['add_title'];



  $new_post_name = str_replace(" ","-",mb_strtolower($post_name));

  if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'", 'ARRAY_A')) {

      $new_post_name = wp_unique_post_slug( $new_post_name, $wpdb->get_var("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'"), $_POST['add_status'], $_POST['add_post_type'], $_POST['add_parent']);

  } 

  $guid = get_site_url('','?post_type=sfwd-question&#038;p='.$new_post_name);

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

    'post_excerpt' => '',

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

  $values_2 = array(

    'id' => $_POST['add_user_id'],

    'quiz_id' => $_POST['add_date'],

    'online' => $_POST['add_date_gmt'],

    'sort' => $_POST['add_content'],

    'title' => $_POST['add_title'],

    'points' => $_POST['add_excerpt'],

    'question' => $_POST['add_status'],

    'correct_msg' => $_POST['add_comment'],

    'incorrect_msg' => $_POST['add_ping'],

    'correct_same_text' => $_POST['add_password'],

    'tip_enabled' => $new_post_name,

    'tip_msg' => $_POST['add_modified'],

    'answer_type' => $_POST['add_modified_gmt'],

    'show_points_in_box' => $_POST['add_parent'],

    'answer_points_activated' => $_POST['answer_points_activated'],

    'answer_data' => $_POST['add_menu_order'],

    'category_id' => $_POST['add_post_type'],

    'answer_points_diff_modus_activated' => $_POST['add_comment_count'],

    'answer_points_diff_modus_activated' => $_POST['disable_correct'],

    'answer_points_diff_modus_activated' => $_POST['matrix_sort_answer_criteria_width'],

  );





  if (intval($_POST['id'])) {

    $filter = array('id' => $_POST['id']);

    $wpdb->update($table_name, $values, $filter);

    
  }

  else {

    if (!$wpdb->insert($table_name, $values)) {

      wp_redirect(get_site_url().'/add-question&err='.urlencode($wpdb->last_error));

      exit;

    }

    $post_id = $wpdb->insert_id;
    $meta_value = array(

    'sfwd-question_quiz' => $_POST['sfwd-question_quiz'],

  );


    update_post_meta( $post_id, 'question_points', $_POST['points']);
    update_post_meta( $post_id, 'question_type', 'single');
    // update_post_meta( $post_id, '_edit_lock', '1559279925:1');
    update_post_meta( $post_id, 'quiz_id', $_POST['sfwd-question_quiz']);
    update_post_meta( $post_id, 'question_pro_category', '0');
    update_post_meta( $post_id, 'slide_template', 'default');
    update_post_meta( $post_id, '_edit_last', '1');
    update_post_meta( $post_id, '_sfwd-question', $meta_value);
    // update_post_meta( $post_id, 'question_pro_id', $meta_value);

// var_dump($post_id, $meta_value);
    var_dump($values);
    var_dump($table_name);

  }
  // var_dump($wpdb->insert_id);

  wp_redirect($guid);

  exit;

}

?>