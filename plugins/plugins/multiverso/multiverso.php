<?php
/*
Plugin Name: Multiverso - Advanced File Sharing Plugin
Plugin URI: http://www.webself.it
Description: Multipurpose and advanced file sharing pulign
Author: Alessio Marzo & Andrea Onori
Version: 2.6
Author URI: http://www.webself.it
*/

// Basic plugin definitions 
define( 'WP_MULTIVERSO_VERSION', '2.6' );
define( 'WP_MULTIVERSO_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
define( 'WP_MULTIVERSO_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
define('WP_MULTIVERSO_UPLOAD_FOLDER', 'multiverso-files');

// File inclusion
require_once(WP_MULTIVERSO_DIR.'inc/functions.php');
require_once(WP_MULTIVERSO_DIR.'inc/functions-html.php');
require_once(WP_MULTIVERSO_DIR.'inc/install.php');
require_once(WP_MULTIVERSO_DIR.'inc/options.php');
require_once(WP_MULTIVERSO_DIR.'inc/filters.php');
require_once(WP_MULTIVERSO_DIR.'inc/download.php');
require_once(WP_MULTIVERSO_DIR.'inc/controller.php');
require_once(WP_MULTIVERSO_DIR.'inc/update-notifier.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-login-register.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-recent-files.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-personal-recent-files.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-registered-recent-files.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-category-files.php');
require_once(WP_MULTIVERSO_DIR.'widgets/mv-search.php');


//*********** install/uninstall actions ********************//
register_activation_hook(__FILE__,'mv_install');
register_deactivation_hook(__FILE__, 'mv_uninstall');

function mv_install(){
	
    mv_uninstall(); //force to uninstall past options
	
	// File Types
	add_option('mv_mime_pdf','application/pdf');
	add_option('mv_mime_txt','text/plain');
	add_option('mv_mime_zip','application/zip');
	add_option('mv_mime_rar','application/rar');
	add_option('mv_mime_doc','application/msword');
	add_option('mv_mime_xls','application/vnd.ms-excel');
	add_option('mv_mime_ppt','application/vnd.ms-powerpoint');
	add_option('mv_mime_gif','image/gif');
	add_option('mv_mime_png','image/png');
	add_option('mv_mime_jpeg','image/jpeg');
	add_option('mv_mime_others','');
	
	// Other Options
	add_option('mv_user_backend','');
	
	add_option('mv_upload_size','');
	add_option('mv_pt_slug','');
	add_option('mv_tax_slug','');
	
	add_option('mv_single_theme','');
	add_option('mv_custom_css','');
	add_option('mv_before_tpl', '');
	add_option('mv_after_tpl', '');
	
}

function mv_uninstall(){ //function to uninstall opt

	delete_option('mv_mime_pdf');
	delete_option('mv_mime_txt');
	delete_option('mv_mime_zip');
	delete_option('mv_mime_rar');
	delete_option('mv_mime_doc');
	delete_option('mv_mime_xls');
	delete_option('mv_mime_ppt');
	delete_option('mv_mime_gif');
	delete_option('mv_mime_png');
	delete_option('mv_mime_jpeg'); 
	
	// Other Options
	delete_option('mv_user_backend');
	
	delete_option('mv_upload_size');
	delete_option('mv_pt_slug');
	delete_option('mv_tax_slug');
	
	delete_option('mv_single_theme');
	delete_option('mv_custom_css');
	delete_option('mv_before_tpl');
	delete_option('mv_after_tpl');
}
//*********** end of install/uninstall actions ********************//

// Enable MV Boxes in Menu
add_filter( 'get_user_option_metaboxhidden_nav-menus', 'mv_enable_menu_boxes', 10, 3 );

function mv_enable_menu_boxes( $result, $option, $user ){ 
    
	if( in_array( 'add-multiverso', $result ) ){
       
	   if (($key = array_search('add-multiverso', $result)) !== false) unset($result[$key]);
	   
	}
	
	if( in_array( 'add-multiverso-categories', $result ) ){
        
		if (($key = array_search('add-multiverso-categories', $result)) !== false) unset($result[$key]);
		
	}

    return $result;
	
}