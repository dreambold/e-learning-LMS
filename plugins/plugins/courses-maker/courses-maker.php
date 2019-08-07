<?php
/**
 * Plugin Name:       Courses Maker
 * Plugin URI:        http://...
 * Description:       Create a Courses.
 * Version:           1.0
 * Author:            Roman M.
 * Author URI:        https://...
 * Requires at least: 5.1.1
 * Tested up to:      5.1.1
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       courses-maker
 * Domain Path:       /languages/
 * Copyright (c)      2019-2019 StoreApps, All right reserved
 *
 * @package Courses Maker
 */

 
global $wpdb;
global $post;


$result = add_role( 'Teacher', __('Teacher' ),
 
array(
 
'read' => true, // true allows this capability
'edit_posts' => true, // Allows user to edit their own posts
'edit_pages' => true, // Allows user to edit pages
'edit_others_posts' => true, // Allows user to edit others posts not just their own
'create_posts' => true, // Allows user to create new posts
'manage_categories' => true, // Allows user to manage post categories
'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
 
)
 
);

$result2 = add_role( 'Student', __('Student' ),
 
array(
 
'read' => true, // true allows this capability
'edit_posts' => true, // Allows user to edit their own posts
'edit_pages' => true, // Allows user to edit pages
'edit_others_posts' => true, // Allows user to edit others posts not just their own
'create_posts' => true, // Allows user to create new posts
'manage_categories' => true, // Allows user to manage post categories
'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
 
)
 
);

$result3 = add_role( 'Account Manager', __('Account Manager' ),
 
array(
 
'read' => true, // true allows this capability
'edit_posts' => true, // Allows user to edit their own posts
'edit_pages' => true, // Allows user to edit pages
'edit_others_posts' => true, // Allows user to edit others posts not just their own
'create_posts' => true, // Allows user to create new posts
'manage_categories' => true, // Allows user to manage post categories
'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
 
)
 
);


$result4 = add_role( 'Super Admin', __('Super Admin' ),
 
array(
 
'read' => true, // true allows this capability
'edit_posts' => true, // Allows user to edit their own posts
'edit_pages' => true, // Allows user to edit pages
'edit_others_posts' => true, // Allows user to edit others posts not just their own
'create_posts' => true, // Allows user to create new posts
'manage_categories' => true, // Allows user to manage post categories
'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
 
)
 
);

// function courses_plugin_activate() {
//   $new_page_title_1 = 'Add Course';
//   $new_page_title_2 = 'Add Lesson';
//   $new_page_title_3 = 'Add Question';
//   $new_page_title_4 = 'Add Quizz';  
//   $new_page_title_5 = 'Add Topic';  

//   $page_check_1 = get_page_by_title($new_page_title_1);
//   $new_page_1 = array(
//       'post_type' => 'page',
//       'post_title' => $new_page_title_1,
//       'post_content' => '',
//       'post_status' => 'publish',
//       'post_author' => 1,
//   );
//   $page_check_2 = get_page_by_title($new_page_title_2);
//   $new_page_2 = array(
//       'post_type' => 'page',
//       'post_title' => $new_page_title_2,
//       'post_content' => '',
//       'post_status' => 'publish',
//       'post_author' => 1,
//   );
//   $page_check_3 = get_page_by_title($new_page_title_3);
//   $new_page_3 = array(
//       'post_type' => 'page',
//       'post_title' => $new_page_title_3,
//       'post_content' => '',
//       'post_status' => 'publish',
//       'post_author' => 1,
//   );
//   $page_check_4 = get_page_by_title($new_page_title_4);
//   $new_page_4 = array(
//       'post_type' => 'page',
//       'post_title' => $new_page_title_4,
//       'post_content' => '',
//       'post_status' => 'publish',
//       'post_author' => 1,
//   );
//   $page_check_5 = get_page_by_title($new_page_title_5);
//   $new_page_5 = array(
//       'post_type' => 'page',
//       'post_title' => $new_page_title_5,
//       'post_content' => '',
//       'post_status' => 'publish',
//       'post_author' => 1,
//   );
//   if(!isset($page_check_1->ID)){
//     $new_page_id = wp_insert_post($new_page_1);    
//   }
//   if(!isset($page_check_2->ID)){
//     $new_page_id = wp_insert_post($new_page_2);
//   }
//   if(!isset($page_check_3->ID)){
//     $new_page_id = wp_insert_post($new_page_3);
//   }
//   if(!isset($page_check_4->ID)){
//     $new_page_id = wp_insert_post($new_page_4);
//   }
//   if(!isset($page_check_5->ID)){
//     $new_page_id = wp_insert_post($new_page_5);
//   }

//   if ( ! current_user_can( 'manage_options' ) ) {
//     show_admin_bar( false );
//   }
  
// }    
// register_activation_hook( __FILE__, 'courses_plugin_activate' );

// add_filter('template_include', 'course_template');
// function course_template( $template ) {
// if( is_page('add-course') || is_page('add-lesson') || is_page('add-question') || is_page('add-quizz') || is_page('add-certificate') || is_page('add-topic')){
//   return untrailingslashit( plugin_dir_path( __FILE__ ) ).'/create.php';
// }
// return $template;
// }

function courses_plugin_activate() {

  $page_check_1 = get_page_by_title('Create Webinar');
  $new_page_1 = array(
      'post_type' => 'page',
      'post_title' => 'Create Webinar',
      'post_content' => '',
      'post_status' => 'publish',
      'post_author' => 1, 
  ); 

  if(!isset($page_check_1->ID)){
    $new_page_id = wp_insert_post($new_page_1);    
  }

  $page_check_2 = get_page_by_title('Create Offline Course');
  $new_page_2 = array(
      'post_type' => 'page',
      'post_title' => 'Create Offline Course',
      'post_content' => '',
      'post_status' => 'publish',
      'post_author' => 1, 
  ); 

  if(!isset($page_check_2->ID)){
    $new_page_id = wp_insert_post($new_page_2);    
  }
}
register_activation_hook( __FILE__, 'courses_plugin_activate' );

add_filter('template_include', 'course_template');
function course_template( $template ) {
if( is_page('create-webinar') || is_page('create-offline-course')){
  return untrailingslashit( plugin_dir_path( __FILE__ ) ).'/create.php';
}
return $template;
}


function wptp_create_webinars() {
  $labels = array(
    'name' => __( 'Webinars' ),
    'singular_name' => __( 'Webinars' ),
    'add_new' => __( 'New Webinar' ),
    'add_new_item' => __( 'Add New Webinar' ),
    'edit_item' => __( 'Edit Webinar' ),
    'new_item' => __( 'New Webinar' ),
    'view_item' => __( 'View Webinar' ),
    'search_items' => __( 'Search Webinars' ),
    'not_found' =>  __( 'No Webinars Found' ),
    'not_found_in_trash' => __( 'No Webinars found in Trash' ),
    );
  $args = array(
    'labels' => $labels,
    'has_archive' => true,
    'public' => true,
    'hierarchical' => false,
    'menu_position' => 5,
    'show_in_rest' => true,
    'supports' => array(
      'title',
      'editor',
      'excerpt',
      'custom-fields',
      'thumbnail',
      'page-attributes'
      ),
    );
  register_post_type( 'webinars', $args );
}
add_action( 'init', 'wptp_create_webinars' );

function wptp_register_category_webinars() {
  register_taxonomy( 'webinars_category', 'webinars',
    array(
      'labels' => array(
        'name'              => 'Webinar Categories',
        'singular_name'     => 'Webinar Category',
        'search_items'      => 'Search Webinar Categories',
        'all_items'         => 'All Webinar Categories',
        'edit_item'         => 'Edit Webinar Categories',
        'update_item'       => 'Update Webinar Category',
        'add_new_item'      => 'Add New Webinar Category',
        'new_item_name'     => 'New Webinar Category Name',
        'menu_name'         => 'Webinar Category',
        ),
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'show_admin_column' => true
      )
    );
}
add_action( 'init', 'wptp_register_category_webinars' );
add_post_type_support( 'webinars', 'thumbnail' );

add_action('add_meta_boxes', 'webinar_details_box');

function webinar_details_box() {
  $screens = ['webinars'];
  foreach ($screens as $screen) {
    add_meta_box(
      'webinar_details_box',
      __('Webinar Details', 'course-maker'),
      'webinar_details_box_content',
      $screen
    );
  }
}

function webinar_details_box_content($post) {
  $value_date = get_post_meta($post->ID, '_webinar_date', true);
  $value_date_end = get_post_meta($post->ID, '_webinar_date_end', true);
  $webinar_users_reg = get_post_meta($post->ID, '_webinar_users_reg', true);
  $value_place = get_post_meta($post->ID, '_free_places', true);
?>
<label for="webinar_date">Webinare Date: </label>
<input type="text" id="webinar_date" name="webinar_date" placeholder="Enter webinar start date" value="<?php echo $value_date; ?>">
<input type="text" id="webinar_date_end" name="webinar_date_end" placeholder="Enter webinar end date" value="<?php echo $value_date_end; ?>">
<textarea type="text" id="webinar_users_reg" name="webinar_users_reg" placeholder="Enter User ID for invate to webinare"><?php echo $webinar_users_reg; ?></textarea>
<label for="free_places">Vacancies: </label>
<input type="text" id="free_places" name="free_places" placeholder="Enter the number of vacancies" value="<?php echo $value_place;?>">
<?php 
}

function webinar_save_postdata($post_id)
{
    if (array_key_exists('webinar_date', $_POST)) {
        update_post_meta(
            $post_id,
            '_webinar_date',
            $_POST['webinar_date']
        );
    }

    if (array_key_exists('webinar_date_end', $_POST)) {
      update_post_meta(
          $post_id,
          '_webinar_date_end',
          $_POST['webinar_date_end']
      );
    }

    if (array_key_exists('webinar_users_reg', $_POST)) {
      update_post_meta(
          $post_id,
          '_webinar_users_reg',
          $_POST['webinar_users_reg']
      );
    }

    if (array_key_exists('free_places', $_POST)) {
      update_post_meta(
          $post_id,
          '_free_places',
          $_POST['free_places']
      );
    }
}
add_action('save_post', 'webinar_save_postdata');



function wptp_create_offline() {
  $labels = array(
    'name' => __( 'Offline Course' ),
    'singular_name' => __( 'Offline Course' ),
    'add_new' => __( 'New Offline Course' ),
    'add_new_item' => __( 'Add New Offline Course' ),
    'edit_item' => __( 'Edit Offline Course' ),
    'new_item' => __( 'New Offline Course' ),
    'view_item' => __( 'View Offline Course' ),
    'search_items' => __( 'Search Offline Course' ),
    'not_found' =>  __( 'No offline Found' ),
    'not_found_in_trash' => __( 'No offline found in Trash' ),
    );
  $args = array(
    'labels' => $labels,
    'has_archive' => true,
    'public' => true,
    'hierarchical' => false,
    'menu_position' => 5,
    'show_in_rest' => true,
    'supports' => array(
      'title',
      'editor',
      'excerpt',
      'custom-fields',
      'thumbnail',
      'page-attributes'
      ),
    );
  register_post_type( 'offline-courses', $args );
}
add_action( 'init', 'wptp_create_offline' );

function wptp_register_category_offline() {
  register_taxonomy( 'offline_category', 'offline-courses',
    array(
      'labels' => array(
        'name'              => 'Offline Course Categories',
        'singular_name'     => 'Offline Course Category',
        'search_items'      => 'Search Offline Course Categories',
        'all_items'         => 'All Offline Course Categories',
        'edit_item'         => 'Edit Offline Course Categories',
        'update_item'       => 'Update Offline Course Category',
        'add_new_item'      => 'Add New Offline Course Category',
        'new_item_name'     => 'New Offline Course Category Name',
        'menu_name'         => 'Offline Course Category',
        ),
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'show_admin_column' => true
      )
    );
}
add_action( 'init', 'wptp_register_category_offline' );
add_post_type_support( 'offline-courses', 'thumbnail' );


add_action('add_meta_boxes', 'offline_details_box');

function offline_details_box() {
  $screens = ['offline-courses'];
  foreach ($screens as $screen) {
    add_meta_box(
      'offline_details_box',
      __('Offline Course Details', 'course-maker'),
      'offline_details_box_content',
      $screen
    );
  }
}

function offline_details_box_content($post) {
  $value_date = get_post_meta($post->ID, '_offline_date', true);
  $value_date_end = get_post_meta($post->ID, '_offline_date_end', true);
  $value_location = get_post_meta($post->ID, '_location_value', true);
  $value_adress = get_post_meta($post->ID, '_location_adress', true);
  $value_place = get_post_meta($post->ID, '_course_free_places', true);
?>
<label for="offline_date">Date: </label>
<input type="text" id="offline_date" name="offline_date" placeholder="Enter course start date" value="<?php echo $value_date; ?>">
<input type="text" id="offline_date_end" name="offline_date_end" placeholder="Enter course end date" value="<?php echo $value_date_end; ?>">
<label for="location_value">Location: </label>
<input type="text" id="location_value" name="location_value" placeholder="Enter the location" value="<?php echo $value_location;?>">
<input type="text" id="location_adress" name="location_adress" placeholder="Enter the location" value="<?php echo $value_adress;?>">
<label for="course_free_places">Vacancies: </label>
<input type="text" id="course_free_places" name="course_free_places" placeholder="Enter the number of vacancies" value="<?php echo $value_place;?>">
<?php 
}

function offline_save_postdata($post_id)
{
    if (array_key_exists('offline_date', $_POST)) {
        update_post_meta(
            $post_id,
            '_offline_date',
            $_POST['offline_date']
        );
    }

    if (array_key_exists('offline_date_end', $_POST)) {
      update_post_meta(
          $post_id,
          '_offline_date_end',
          $_POST['offline_date_end']
      );
    }

    if (array_key_exists('course_free_places', $_POST)) {
      update_post_meta(
          $post_id,
          '_course_free_places',
          $_POST['course_free_places']
      );
    }

    if (array_key_exists('location_adress', $_POST)) {
      update_post_meta(
          $post_id,
          '_location_adress',
          $_POST['location_adress']
      );
    }

    if (array_key_exists('location_value', $_POST)) {
      update_post_meta(
          $post_id,
          '_location_value',
          $_POST['location_value']
      );
    }
}
add_action('save_post', 'offline_save_postdata');

add_action( 'vc_before_init', 'theme_vc_shortcodes' );
function theme_vc_shortcodes() {
    $shortcode_file = untrailingslashit( plugin_dir_path( __FILE__ ) ).'/shortcodes/example.php';
    

    require_once $shortcode_file;
}



function add_option_field_to_general_admin_page(){
	$option_name = 'runnig_type_option';

	register_setting( 'general', $option_name );

	add_settings_field( 
		'running_type_setting-id', 
		'Running Type Text', 
		'running_type_setting_callback_function', 
		'general', 
		'default', 
		array( 
			'id' => 'running_type_setting-id', 
			'option_name' => 'runnig_type_option' 
		)
	);
}
add_action('admin_menu', 'add_option_field_to_general_admin_page');

function running_type_setting_callback_function( $val ){
	$id = $val['id'];
	$option_name = $val['option_name'];
	?>
	<input 
		type="text" 
		name="<? echo $option_name ?>" 
		id="<? echo $id ?>" 
		value="<? echo esc_attr( get_option($option_name) ) ?>" 
	/> 
	<?
}


