<?php
/*
File: inc/controller.php
Description: Plugin Controller
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/

// SAVE POST

add_action('save_post', 'mv_save_post');
function mv_save_post($post_id, $post = null) {
	global $post;
	
	// Multisite fix
	if(is_multisite()) {
		switch_to_blog(SITE_ID_CURRENT_SITE);
	 }
		
	/* --- SECURITY VERIFICATION ---  */ 

	// Auto Save
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;  

    // if invalid $post object or post type is not 'multiverso', return
    if(!$post || get_post_type($post->ID) != 'multiverso') return;
	
	// id trash or untrash
	if( (isset($_GET['action']) && $_GET['action'] == 'trash') || (isset($_GET['action']) &&  $_GET['action'] == 'untrash') ) {return;}
	
	/* --- START SAVING --- */
	
	// Remove Meta Field
	if (!empty($_POST['mv_remove_file'])) {
		delete_post_meta($post_id, 'mv_file');
	}
	
	//Add Meta Field
	$user_info = get_userdata($_POST['mv_user']);
	add_post_meta($post_id, 'mv_user', $user_info->user_login);
	update_post_meta($post_id, 'mv_user', $user_info->user_login);
	
	add_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	update_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	
	if($_POST['mv_download_limit'] >= 0 &&  is_numeric($_POST['mv_download_limit'])){
		add_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
		update_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
	}else{
		add_post_meta($post_id, 'mv_download_limit', '-1');
		update_post_meta($post_id, 'mv_download_limit', '-1');
	}
	
	
	// Make sure the file array isn't empty
	if(!empty($_FILES['mv_file']['name'])) {
		
		$limit_option = get_option('mv_upload_size');
		
		if( !empty($limit_option) && $_FILES['mv_file']['size'] > $limit_option ) {
			wp_die('The file size ('.$_FILES['mv_file']['size'].' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes)');
		}
		
		
		// Check Supported Types
		$supported_types = mv_supported_types();
	
		
		// Get the file type of the upload
		$arr_file_type = wp_check_filetype(basename($_FILES['mv_file']['name']));
		
		$uploaded_type = $arr_file_type['type'];
	
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($uploaded_type, $supported_types)) {
			
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			
			if ($mv_file) {
				$mv_file_path = WP_CONTENT_DIR.'/multiverso/'.$mv_file['file'];
				if (file_exists($mv_file_path)) unlink($mv_file_path);
			}
			
			// Use the WordPress API to upload the file
			$upload = wp_handle_upload( $_FILES['mv_file'], array( 'test_form' => false ) );
		
			

			if(isset($upload['error']) && !empty($upload['error'])) {
				wp_die(__('There was an error uploading your file. The error is: ' . $upload['error'], 'mvafsp'));
			} else {
			
				// Update custom field
				$upload['file'] = substr($upload['file'],stripos($upload['file'],'wp-content/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/')+28);
				
				add_post_meta($post_id, 'mv_file', $upload);
				update_post_meta($post_id, 'mv_file', $upload); 
				
			} // end if/else
			
			
		} else {
			wp_die(__("The file type that you've uploaded is not allowed.", 'mvafsp'));
		} // end if/else
		
	}elseif (!empty($_POST['mv_file_r'])){ // else if empty upload files

		// Setup vars
		$uploaddir = WP_CONTENT_DIR.'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.mv_get_user_dir($_POST['mv_user']);
		
		$remoteurl = $_POST['mv_file_r'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$fileunipath = mv_get_user_dir($_POST['mv_user']).'/'.$filename;
		
		$fileurl = content_url().'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.$fileunipath;
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];		
		
		$uploadfile = $uploaddir . '/' . $filename;
		
		// Check for MIME TYPE
		$supported_types = mv_supported_types();
		
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($filemime, $supported_types)) {
			
			if(url_exists($remoteurl)) {
			$contents = file_get_contents($remoteurl);
			}else{
				wp_die(__("Remote url is wrong, there isn't any file to upload.", 'mvafsp'));
			}
			
			$limit_option = get_option('mv_upload_size');
			
			if( !empty($limit_option) && strlen($contents) > $limit_option ){
				wp_die('The file size ('.strlen($contents).' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes)', 'mvafsp');
			}
			
			$savefile = fopen($uploadfile, 'w');
			fwrite($savefile, $contents);
			fclose($savefile);
			
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			if ($mv_file) {				
				update_post_meta( $post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl, 'type'=>$filemime ) ); 
			}else{
				add_post_meta($post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl ) );
			}
				
		}else{
			wp_die(__("The file type that you've uploaded is not allowed.", 'mvafsp'));
		}
	
	}elseif (!empty($_POST['mv_file_d'])){ // else if empty remote upload too 
		
		// Get vars
		$remoteurl = $_POST['mv_file_d'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];	
	
		$mv_file = get_post_meta($post_id, 'mv_file', true);
		if ($mv_file) {				
			update_post_meta( $post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) ); 
		}else{
			add_post_meta($post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) );
		}
	
	}

	// Multisite fix
	if(is_multisite()) {
		restore_current_blog();	
	}
	
}


// INCLUDE TEMPLATES

add_action( 'template_redirect', 'mv_multiverso_template' );

function mv_multiverso_template() {
    global $wp, $wp_query;
	
	// Single
    if ( isset( $wp->query_vars['post_type'] ) && $wp->query_vars['post_type'] == 'multiverso' ) {
        if ( have_posts() ) {
			if (get_option('mv_single_theme') == '1') {
            	add_filter('the_content', 'mv_single_template_theme');
			}else{
				$wp_query->is_404 = true;
			}
        }
        else {
            $wp_query->is_404 = true;
        }
    }
	
	
	// Category
	if ( is_tax( 'multiverso-categories') ) {
		require_once(WP_MULTIVERSO_DIR.'shortcodes/multiverso-category.php');
	}
	
}

function mv_single_template_theme() {
	
	return require_once(WP_MULTIVERSO_DIR.'shortcodes/multiverso-file-theme.php');
	
}


//:::::::::::::::::::::::://
//       SHORTCODES       //
//:::::::::::::::::::::::://

// Single File
add_shortcode( 'mv_file', 'mv_file' );

function mv_file( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'id' => 0
		), $atts )
	);

	// Code
	if($id == 0) {
		
		_e('You need to enter an ID for the file', 'mvafsp');
		
	}else{
			
		if ( mv_is_file_existing($id) && mv_user_can_view_file($id) ) {
			
			return mv_file_details_html ($id);			
			
		}else{
			
			if ( !mv_is_file_existing($id) ) {
				
				return __('File doesn\'t exist', 'mvafsp');
				
			}elseif ( !mv_user_can_view_file($id) ) {
				
				return __('You haven\'t the rights to access', 'mvafsp');
				
			}
		}
		
	}
}


// All Files
add_shortcode( 'mv_allfiles', 'mv_allfiles' );


function mv_allfiles() {

	// Include allfiles.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/allfiles.php');
	
}

// Personal Files
add_shortcode( 'mv_personalfiles', 'mv_personalfiles' );

function mv_personalfiles() {

	// Include personalfiles.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/personalfiles.php');
	
}

// Registered Files
add_shortcode( 'mv_registeredfiles', 'mv_registeredfiles' );

function mv_registeredfiles() {

	// Include registeredfiles.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/registeredfiles.php');
	
}


// Categories
add_shortcode( 'mv_categories', 'mv_categories' );

function mv_categories() {

	// Include multiverso-category.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/multiverso-category.php');
	
}

// Single Category
add_shortcode( 'mv_single_category', 'mv_single_category' );

function mv_single_category($atts) {
	
	// Attributes
	extract( shortcode_atts(
		array(
			'id' => 0
		), $atts )
	);
	
	if (empty($id)) { 
	
		return __('You need to enter an ID for the category', 'mvafsp');
	
	}else{
		
	$mv_single_cat_id = $id;
	// Include multiverso-category.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/multiverso-category.php');
	
	}
	
}

// Search
add_shortcode( 'mv_search', 'mv_search' );

function mv_search() {

	// Include multiverso-search-files.php template
	return include(WP_MULTIVERSO_DIR.'shortcodes/multiverso-search-files.php');
	
}

// Manage Files
add_shortcode( 'mv_managefiles', 'mv_managefiles' );

function mv_managefiles() {

	// Include manage-files.php template
	include(WP_MULTIVERSO_DIR.'shortcodes/manage-files.php');
	
}


//:::::::::::::::::::::::://
//        ENQUEUE         //
//:::::::::::::::::::::::://

// Load Basic Script
add_action('wp_enqueue_scripts', 'mv_add_scripts');

function mv_add_scripts() {
wp_enqueue_script('jquery'); 	 
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('multiverso', WP_MULTIVERSO_URL . 'js/multiverso.js', array(), '', true);
}


// Load IcoMoon
add_action( 'wp_enqueue_scripts', 'mv_add_mvicomoon' );

function mv_add_mvicomoon() {
		wp_register_style( 'mv-icomoon',  WP_MULTIVERSO_URL . 'css/mvicomoon.css' );
        wp_enqueue_style( 'mv-icomoon' );
}

// Load Basic Style
add_action( 'wp_enqueue_scripts', 'mv_add_stylesheet' );
add_action( 'admin_enqueue_scripts', 'mv_add_stylesheet' );

function mv_add_stylesheet() {
        wp_register_style( 'mv-style',  WP_MULTIVERSO_URL . 'css/style.css' );
        wp_enqueue_style( 'mv-style' );
}

// Load WP jQuery
add_action('wp_enqueue_scripts','mv_insert_jquery');

function mv_insert_jquery(){
   wp_enqueue_script('jquery');
}

// Load Custom Dynamic Style
add_action('wp_enqueue_scripts','mv_add_custom_css');

function mv_add_custom_css(){	
wp_add_inline_style( 'mv-style', get_option('mv_custom_css') );
}

 
?>