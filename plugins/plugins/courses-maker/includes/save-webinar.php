<?php

require_once( '../../../../wp-config.php' );



global $wpdb;
global $post;

if( current_user_can('publish_posts')) {

  // editor

  $table_name  = $wpdb->prefix."posts";
  $post_name = $_POST['post_title'];



  $new_post_name = str_replace(" ","-",mb_strtolower($post_name));

  if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'", 'ARRAY_A')) {

      $new_post_name = wp_unique_post_slug( $new_post_name, $wpdb->get_var("SELECT post_name FROM wp_posts WHERE post_name = '" . $new_post_name . "'"), $_POST['add_status'], $_POST['add_post_type'], $_POST['add_parent']);

  } 


  $user_id   = esc_sql($_POST["user_id"]);
  $post_date   = esc_sql($_POST["post_date"]);
  $post_date_gmt   = esc_sql($_POST["post_date_gmt"]);
  $post_content   = esc_sql($_POST["post_content"]);
  $post_title   = esc_sql($_POST["post_title"]);
  $post_status   = esc_sql($_POST["post_status"]);
  $comment_status   = esc_sql($_POST["comment_status"]);
  $ping_status   = esc_sql($_POST["ping_status"]);
  $post_password   = esc_sql($_POST["post_password"]);
  $post_name   = esc_sql($_POST["post_name"]);
  $post_modified   = esc_sql($_POST["modified_date"]);
  $post_modified_gmt   = esc_sql($_POST["modified_date_gmt"]);
  $post_parent   = esc_sql($_POST["post_parent"]);
  $guid   = get_site_url('','webinars/'.$new_post_name);
  $menu_order   = esc_sql($_POST["menu_order"]);
  $post_type   = esc_sql($_POST["post_type"]);
  $comment_count   = esc_sql($_POST["comment_count"]);

  $values = array(

    'post_author' => $user_id,

    'post_date' => $post_date,

    'post_date_gmt' => $post_date_gmt,

    'post_content' => stripcslashes($post_content),

    'post_title' => $post_title,

    'post_status' => $post_status,

    'comment_status' => $comment_status,

    'ping_status' => $post_password,

    'post_password' => $post_password,

    'post_name' => $new_post_name,

    'post_modified' => $post_modified,

    'post_modified_gmt' => $post_modified_gmt,

    'post_parent' => $post_parent,

    'guid' => $guid,

    'menu_order' => $menu_order,

    'post_type' => $post_type,

    'comment_count' => $comment_count,

  );





  if (intval($_POST['id'])) {

    $filter = array('id' => $_POST['id']);

    $wpdb->update($table_name, $values, $filter);    
  }

  else {

    if (!$wpdb->insert($table_name, $values)) {

      wp_redirect(get_site_url().'/create-webinar&err='.urlencode($wpdb->last_error));

      exit;

    }

    $post_id = $wpdb->insert_id;
    $meta_value_1 = $_POST['webinar_date'].' '.$_POST['webinar_start_hour'];

    $meta_value_2 = $_POST['webinar_date_end'].' '.$_POST['webinar_end_hour'];

    $meta_value_3 = $_POST['free_places'];

    set_post_thumbnail( $post_id, $_POST['attachment_id'] );
    update_post_meta( $post_id, '_webinar_date', $meta_value_1);
    update_post_meta( $post_id, '_webinar_date_end', $meta_value_2);
    update_post_meta( $post_id, '_free_places', $meta_value_3);

// var_dump($post_id, $meta_value);
//    var_dump($values);
//    var_dump($table_name);

  }
  // var_dump($wpdb->insert_id);

  wp_redirect($guid);

  exit;

}

?>