<?php
/**
* Plugin Name: Exclude or include Pages, Tags, Posts & Categories (integrate with WiziApp)
* Description: This plugin adds a checkbox, "Display on your web site", for pages, tags & categories. Uncheck it to exclude content from your web site. Use Tags to uncheck Posts too.
* Author: mayerz.
* Version: 1.0.10
*/

class EIContent_Route {

	/**
	* @var object EIContent_Backend or EIContent_Frontend instance
	*/
	private $_controller;

	/**
	* Set _db and _model properties.
	* Activate activation and unactivation hooks.
	* Choose to trigger Site Front End or Back End hooks
	*/
	public function __construct() {
		if ( is_admin() ) {
			$this->_controller = new EIContent_Backend;

            require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

            add_action( 'tgmpa_register', array( $this->_controller, 'required_plugins' ) );

			register_activation_hook( __FILE__, array( $this->_controller, 'activate') );
			register_deactivation_hook( __FILE__, array( $this->_controller, 'deactivate') );

			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		} else {
			$this->_controller = new EIContent_Frontend;

			add_action( 'init', array( &$this, 'site_init' ) );
		}
	}

	/**
	* Trigger of the hooks on the Site Front End.
	*
	* @return void
	*/
	public function site_init() {
		/*************	Exclude Pages and Posts unchecked to be shown	**********/
		add_filter( 'get_pages', 	 array( $this->_controller, 'exclude_pages' ) );
		add_action( 'pre_get_posts', array( $this->_controller, 'exclude_posts' ) );

		/*************	Exclude Categories, Tags and Links, appurtenant to Links Category unchecked, to be shown	**********/
		// Exclude unchecked Categories to be shown on Site and Application
		add_filter( 'get_terms_args',				array( $this->_controller, 'exclude_categories' ), 10, 2 );
		add_filter( 'widget_categories_args',     	array( $this->_controller, 'exclude_categories' ) );
		add_filter( 'wiziapp_exclude_categories',	array( $this->_controller, 'exclude_categories' ) );
		// Exclude unchecked Tags to be shown on Site and Application
		add_filter( 'get_terms_args', 			  	array( $this->_controller, 'exclude_tags' ), 10, 2 );
		add_filter( 'widget_tag_cloud_args',      	array( $this->_controller, 'exclude_tags' ) );
		add_filter( 'wiziapp_exclude_tags',  	  	array( $this->_controller, 'exclude_tags' ) );
		// Exclude unchecked Links to be shown on Site and Application
		add_filter( 'get_terms_args', 				array( $this->_controller, 'exclude_links' ), 10, 2 );
		add_filter( 'widget_links_args', 		  	array( $this->_controller, 'exclude_links' ) );
		add_filter( 'wiziapp_mobile_get_bookmarks', array( $this->_controller, 'exclude_links' ) );
		// Exclude Links, appurtenant to Links Category unchecked, to be shown on the Application
		add_filter( 'get_terms_args', 			  	array( $this->_controller, 'exclude_mobile_links_categories' ), 10, 2 );
		// Exclude Links, appurtenant to Links Category unchecked, to be shown on the Site
		add_filter( 'widget_links_args', 		  	array( $this->_controller, 'exclude_desktop_links_categories' ) );

		/*************	Fix amount error in Categories and Tags, shown on the Application   **********/
		add_filter( 'get_term',  array( $this->_controller, 'fix_amount_error' ) );
		add_filter( 'get_terms', array( $this->_controller, 'fix_amount_errors' ) );

		/*************	Exclude Media from hiddens Posts, to be shown on the Application		**********/
		add_filter( 'wiziapp_albums_exclude', array( $this->_controller, 'exclude_albums' ) );
		add_filter( 'wiziapp_audio_request',  array( $this->_controller, 'exclude_media' ) );
		add_filter( 'wiziapp_video_request',  array( $this->_controller, 'exclude_media' ) );
	}

	/**
	* Trigger of the hooks on the Site Back End.
	*
	* @return void
	*/
	public function admin_init() {
		/*************	"Add Checkboxes" Part	**********/
		// Add checkboxes to Page page
		add_action( 'post_submitbox_start', array( $this->_controller, 'add_page_checkboxes' ) );
		// Add checkboxes to Category page
		add_action( 'edit_category_form', array( $this->_controller, 'add_category_checkboxes' ) );
		// Add checkboxes to Link page and to Link Category page
		add_action( 'submitlink_box', 		   array( $this->_controller, 'add_link_checkboxes' ) );
		add_action( 'edit_link_category_form', array( $this->_controller, 'add_link_category_checkboxes' ) );
		// Add checkboxes to Tag pages
		add_action( 'edit_tag_form',	   array( $this->_controller, 'add_tag_checkboxes' ) );
		add_action( 'add_tag_form_fields', array( $this->_controller, 'add_tag_checkboxes' ) );
		add_action( 'tag_add_form_fields', array( $this->_controller, 'add_tag_checkboxes' ) );

		/*************	"Update Checkboxes value" in appropriate DB table Part	**********/
		// Update checkboxes after Edit Page page changes
		add_action( 'save_post', array( $this->_controller, 'update_page_exclusion' ), 10, 2 );
		// Update checkboxes after Edit Category, Link Category and Tag page changes
		add_action( 'edit_terms',  array( $this->_controller, 'update_term_exclusion' ) );
		// Update checkboxes after Create Category, Link Category and Tag page changes
		add_action( 'create_term', array( $this->_controller, 'update_term_exclusion' ) );
		// Update checkboxes after Add New Link or Edit Link changes
		add_action( 'add_link',  array( $this->_controller, 'update_link_exclusion' ) );
		add_action( 'edit_link', array( $this->_controller, 'update_link_exclusion' ) );

		/*************	Avoid "Wiziapp Push", in element unchecked case	**********/
		// add_filter( 'exclude_wiziapp_push', array( $this->_controller, 'exclude_wiziapp_push' ) );
	}
}

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	exit( "Can not be called directly." );
}
// Define Exclude Include Content plugin root directory path
if ( ! defined( 'EICONTENT_EXCLUDE_PATH' ) ) {
	define( 'EICONTENT_EXCLUDE_PATH', 	  plugin_dir_path( __FILE__ ) );
	define( 'EICONTENT_EXCLUDE_BASENAME', plugin_basename( __FILE__ ) );
	/** @define "EICONTENT_EXCLUDE_PATH" "D:/localhost/wiziapp/apptelecom.com/public/blogtest1/wp-content/plugins/exclude-or-include-pages-tags-posts-categories-integrate-with-wiziapp/" */
	require EICONTENT_EXCLUDE_PATH . 'eicontent-controllers' . DIRECTORY_SEPARATOR . 'frontend.php';
	require EICONTENT_EXCLUDE_PATH . 'eicontent-controllers' . DIRECTORY_SEPARATOR . 'backend.php';
	require EICONTENT_EXCLUDE_PATH . 'eicontent-model.php';
	require EICONTENT_EXCLUDE_PATH . 'eicontent-view.php';
}
// Start of the Plugin work
global $eicontent_rout;
$eicontent_rout = new EIContent_Route;