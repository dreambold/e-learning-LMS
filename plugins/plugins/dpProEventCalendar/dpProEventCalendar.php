<?php
/*
Plugin Name: DP Pro Event Calendar
Description: The Pro Event Calendar plugin adds a professional and sleek calendar to your posts or pages. 100% Responsive, also you can use it inside a widget.
Version: 3.0.4
Author: Diego Pereyra
Author URI: http://www.dpereyra.com/
Wordpress version supported: 4.1 and above
*/
@error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);

//on activation
//defined global variables and constants here
global $dpProEventCalendar, $dpProEventCalendar_cache, $table_prefix, $wpdb;
$dpProEventCalendar = get_option('dpProEventCalendar_options');
$dpProEventCalendar_cache = get_option( 'dpProEventCalendar_cache');
define('DP_PRO_EVENT_CALENDAR_TABLE_EVENTS','dpProEventCalendar_events'); //events TABLE NAME
define('DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS','dpProEventCalendar_calendars'); //calendar TABLE NAME
define('DP_PRO_EVENT_CALENDAR_TABLE_BOOKING','dpProEventCalendar_booking'); //booking TABLE NAME
define('DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES','dpProEventCalendar_special_dates'); //special dates TABLE NAME
define('DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR','dpProEventCalendar_special_dates_calendar'); //special dates TABLE NAME
define('DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR','dpProEventCalendar_subscribers_calendar'); //special dates TABLE NAME

define('DP_PRO_EVENT_CALENDAR_PAYMENTS_URL', 'http://codecanyon.net/item/wordpress-pro-event-calendar-payment-extension/9492899');

define("DP_PRO_EVENT_CALENDAR_VER","3.0.4",false);//Current Version of this plugin
if ( ! defined( 'DP_PRO_EVENT_CALENDAR_PLUGIN_BASENAME' ) )
	define( 'DP_PRO_EVENT_CALENDAR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'DP_PRO_EVENT_CALENDAR_CSS_DIR' ) ){
	define( 'DP_PRO_EVENT_CALENDAR_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/' );
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('dpliteeventcalendar/dpliteeventcalendar.php')) {
	trigger_error('Please deactivate the Lite version of this plugin first, and try again.', E_USER_ERROR);
}

function dpProEventCalendar_load_textdomain() {
// Create Text Domain For Translations

	//load_plugin_textdomain('dpProEventCalendar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	
	$domain = 'dpProEventCalendar';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	//load_plugin_textdomain( $domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	load_plugin_textdomain('dpProEventCalendar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	
}

add_action( 'plugins_loaded', 'dpProEventCalendar_load_textdomain' );

function checkMU_install_dpProEventCalendar($network_wide) {
	global $wpdb;
	if ( $network_wide ) {
		$blog_list = get_blog_list( 0, 'all' );
		foreach ($blog_list as $blog) {
			switch_to_blog($blog['blog_id']);
			install_dpProEventCalendar();
		}
		switch_to_blog($wpdb->blogid);
	} else {
		install_dpProEventCalendar();
	}
}

function install_dpProEventCalendar() {
	global $wpdb, $table_prefix;
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	$table_name_special_dates = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
	$table_name_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
	$table_name_subscribers_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;
	
	if($wpdb->get_var("show tables like '$table_name_booking'") != $table_name_booking) {
		$sql = "CREATE TABLE $table_name_booking (
					id int(11) NOT NULL AUTO_INCREMENT,
					id_calendar int(11) NOT NULL,
					id_event int(11) NOT NULL,
					id_coupon int(11) NULL,
					coupon_discount int(11) NULL,
					booking_date datetime NOT NULL,
					cancel_date datetime NULL,
					event_date date NOT NULL,
					id_user int(11) NOT NULL,
					quantity int(11) NOT NULL DEFAULT 1,
					comment text NULL,
					cancel_reason text NULL,
					status varchar(255) NOT NULL,
					name varchar(255) NOT NULL,
					email varchar(255) NOT NULL,
					phone varchar(80) NULL,
					extra_fields TEXT NULL DEFAULT '',
					UNIQUE KEY id(id)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
	}
	
	if($wpdb->get_var("show tables like '$table_name_calendars'") != $table_name_calendars) {
		$sql = "CREATE TABLE $table_name_calendars (
					id int(11) NOT NULL AUTO_INCREMENT,
					active tinyint(1) NOT NULL,
					title varchar(80) NOT NULL,
					description varchar(255) NOT NULL,
					width char(5) NOT NULL,
					width_unity char(2) NOT NULL DEFAULT 'px',
					default_date date NULL,
					date_range_start date NULL,
					date_range_end date NULL,
					ical_active tinyint(1) NOT NULL,
					ical_limit varchar(80) NOT NULL,
					rss_active tinyint(1) NOT NULL,
					rss_limit varchar(80) NOT NULL,
					link_post tinyint(1) NOT NULL,
					link_post_target varchar(80) NULL DEFAULT '_self',
					booking_display_attendees tinyint(1) NOT NULL DEFAULT 0,
					booking_display_attendees_names tinyint(1) NOT NULL DEFAULT 0,
					booking_display_fully_booked tinyint(1) NOT NULL DEFAULT 0,
					email_admin_new_event tinyint(1) NOT NULL,
					hide_old_dates TINYINT(1) NOT NULL DEFAULT 0,
					limit_time_start TINYINT(2) NOT NULL DEFAULT 0,
					limit_time_end TINYINT(2) NOT NULL DEFAULT 0,
					view VARCHAR(80) NOT NULL DEFAULT 'monthly',
					format_ampm TINYINT(1) NOT NULL DEFAULT 0,
					show_time TINYINT(1) NOT NULL DEFAULT 1,
					show_timezone TINYINT(1) NOT NULL DEFAULT 0,
					show_preview TINYINT(1) NOT NULL DEFAULT 0,
					show_titles_monthly TINYINT(1) NOT NULL DEFAULT 0,
					show_references TINYINT(1) NOT NULL DEFAULT 1,
					show_author TINYINT(1) NOT NULL DEFAULT 0,
					show_search TINYINT(1) NOT NULL DEFAULT 0,
					show_category_filter TINYINT(1) NOT NULL DEFAULT 0,
					show_location_filter TINYINT(1) NOT NULL DEFAULT 0,
					booking_enable TINYINT(1) NOT NULL DEFAULT 0,
					booking_non_logged TINYINT(1) NOT NULL DEFAULT 0,
					booking_cancel TINYINT(1) NOT NULL DEFAULT 0,
					booking_email_template_user TEXT NOT NULL,
					booking_email_template_admin TEXT NOT NULL,
					booking_email_template_reminder_user TEXT NOT NULL,
					booking_cancel_email_enable TINYINT(1) NOT NULL DEFAULT 0,
					booking_cancel_email_template TEXT NOT NULL,
					new_event_email_template_published TEXT NOT NULL,
					booking_comment TINYINT(1) NULL DEFAULT 0,
					booking_event_color VARCHAR(80) NOT NULL DEFAULT '#e14d43',
					category_filter_include text NULL,
					venue_filter_include text NULL,
					allow_user_add_event_roles text NULL,
					booking_custom_fields text NULL,
					form_custom_fields text NULL,
					article_share TINYINT(1) NOT NULL DEFAULT 0,
					cache_active TINYINT(1) NOT NULL DEFAULT 0,
					allow_user_add_event TINYINT(1) NOT NULL DEFAULT 0,
					publish_new_event TINYINT(1) NOT NULL DEFAULT 0,
					new_event_email_enable TINYINT(1) NOT NULL DEFAULT 1,
					form_text_editor TINYINT(1) NOT NULL DEFAULT 1,
					form_show_end_date TINYINT(1) NOT NULL DEFAULT 1,
					form_show_start_time TINYINT(1) NOT NULL DEFAULT 1,
					form_show_end_time TINYINT(1) NOT NULL DEFAULT 1,
					form_show_extra_dates TINYINT(1) NOT NULL DEFAULT 0,
					form_show_description TINYINT(1) NOT NULL DEFAULT 1,
					form_show_category TINYINT(1) NOT NULL DEFAULT 1,
					form_show_hide_time TINYINT(1) NOT NULL DEFAULT 1,
					form_show_frequency TINYINT(1) NOT NULL DEFAULT 1,
					form_show_all_day TINYINT(1) NOT NULL DEFAULT 1,
					form_show_image TINYINT(1) NOT NULL DEFAULT 1,
					form_show_link TINYINT(1) NOT NULL DEFAULT 1,
					form_show_location TINYINT(1) NOT NULL DEFAULT 1,
					form_show_location_options TINYINT(1) NOT NULL DEFAULT 0,					
					form_show_phone TINYINT(1) NOT NULL DEFAULT 1,
					form_show_map TINYINT(1) NOT NULL DEFAULT 1,
					form_show_color TINYINT(1) NOT NULL DEFAULT 0,
					form_show_timezone TINYINT(1) NOT NULL DEFAULT 0,
					form_show_booking_enable TINYINT(1) NOT NULL DEFAULT 0,
					form_show_booking_limit TINYINT(1) NOT NULL DEFAULT 0,
					form_show_booking_price TINYINT(1) NOT NULL DEFAULT 0,
					form_show_booking_block_hours TINYINT(1) NOT NULL DEFAULT 0,
					show_x TINYINT(1) NOT NULL DEFAULT 1,
					allow_user_edit_event TINYINT(1) NOT NULL DEFAULT 0,
					allow_user_remove_event TINYINT(1) NOT NULL DEFAULT 0,
					show_view_buttons TINYINT(1) NOT NULL DEFAULT 1,
					assign_events_admin INT(11) NOT NULL DEFAULT 0,
					first_day tinyint(1) NOT NULL DEFAULT '0',
					current_date_color VARCHAR(10) NOT NULL DEFAULT '#C4C5D1',
					subscribe_active tinyint(1) NOT NULL DEFAULT 0,
					mailchimp_api varchar(80) NULL,
					mailchimp_list varchar(80) NULL,
					translation_fields TEXT NULL DEFAULT '',
					skin varchar(80) NOT NULL,
					enable_wpml TINYINT(1) NOT NULL DEFAULT 0,
					sync_ical_enable TINYINT(1) NOT NULL DEFAULT 0,
					sync_ical_url TEXT NOT NULL DEFAULT '',
					sync_ical_frequency VARCHAR(80) NOT NULL DEFAULT '',
					sync_ical_category INT(11) NOT NULL DEFAULT 0,
					sync_fb_page TEXT NOT NULL DEFAULT '',
					daily_weekly_layout VARCHAR(80) NOT NULL DEFAULT 'list',
					booking_max_quantity INT(11) NOT NULL DEFAULT 3,
					booking_max_upcoming_dates INT(11) NOT NULL DEFAULT 10,
					booking_show_phone TINYINT(1) NOT NULL DEFAULT 0,
					booking_show_remaining TINYINT(1) NOT NULL DEFAULT 1,
					UNIQUE KEY id(id)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
	}
	
	if($wpdb->get_var("show tables like '$table_name_special_dates'") != $table_name_special_dates) {
		$sql = "CREATE TABLE $table_name_special_dates (
					id int(11) NOT NULL AUTO_INCREMENT,
					title varchar(80) NOT NULL,
					color varchar(10) NOT NULL,
					UNIQUE KEY id(id)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
	}
	
	if($wpdb->get_var("show tables like '$table_name_special_dates_calendar'") != $table_name_special_dates_calendar) {
		$sql = "CREATE TABLE $table_name_special_dates_calendar (
					special_date int(11) NOT NULL,
					calendar int(11) NOT NULL,
					date date NOT NULL,
					PRIMARY KEY (special_date,calendar,date)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
	}
	
	if($wpdb->get_var("show tables like '$table_name_subscribers_calendar'") != $table_name_subscribers_calendar) {
		$sql = "CREATE TABLE $table_name_subscribers_calendar (
					id int(11) NOT NULL AUTO_INCREMENT,
					calendar int(11) NOT NULL,
					name varchar(80) NOT NULL,
					email varchar(80) NOT NULL,
					subscription_date datetime NOT NULL,
					UNIQUE KEY id(id)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
	}

   $default_events = array();
   $default_events = array(

   						   'version' 				=> 		DP_PRO_EVENT_CALENDAR_VER,
   						   'disable_rewrite_rules'  => 		0,
						   'user_roles'				=>		array(),						   
						   'article_share'			=>		true,
						   'category_filter_include'=>		true,
						   'assign_events_admin'	=>		true,
						   'all_working_days'		=>		true,
						   'hide_old_dates'			=>		true,
						   'limit_time_start'		=>		true,
						   'form_show_fields'		=>		true,
						   'allow_users_edit_event'	=>		true,
						   'show_author'			=>		true,
						   'remove_events'			=>		true,
						   'cache_active'			=>		true,
						   'booking'				=>		true,
						   'booking_lang'			=>		true,
						   'enable_wpml'			=>		true,
						   'booking_status'			=>		true,
						   'booking_non_logged'		=>		true,
						   'booking_non_logged_options' => 	true,
						   'weekly_view'			=>		true,
						   'tickets_remaining'		=>		true,
						   'sync_ical'				=>		true,
						   'updatebookingtable'		=>      true,
						   'show_titles_monthly'	=>		true,
						   'daily_weekly_layout'	=> 		true,
						   'allow_user_add_event_roles' =>  true,
						   'sync_ical_url_text'		=>		true,
						   'display_attendees'		=> 		true,
						   'new_event_template_published' => true,
						   'booking_quantity'		=>		true,
						   'booking_max_quantity' 	=> 		true,
						   'form_text_editor'		=>		true,
						   'form_bookings'			=>		true,
						   'booking_max_upcoming_dates' => true,
						   'booking_phone'			=> 		true,
						   'form_show_color'		=> true,
						   'booking_email_template_reminder_user'	=> true,
						   'booking_extra_fields'	=> true,
						   'sync_fb_page'		=>		true,
						   'translation_fields'	=>	true,
						   'sync_ical_category'	=>	true,
						   'show_timezone_update'=> true,
						   'form_show_end_time'	=> true,
						   'display_attendees_names' => true,
						   'show_location_filter'	=>		true,
						   'form_show_location_options'	=>	true,
						   'link_post_target'			=> true,
						   'booking_cancel'		=>		true,
						   'booking_cancel_date'	=> true,
						   'new_event_email_enable' => true,
						   'booking_cancel_reason' => true,
						   'booking_cancel_email_enable' => true,
						   'booking_coupon'			=> true,
						   'booking_remaining'		=> true,
						   'form_show_timezone'		=> true,
						   'form_show_extra_dates'	=> true,
						   'booking_custom_fields_calendar'	=> true,
						   'venue_filter_include'	=> true,
						   'display_fully_booked'	=> true,
						   'form_show_booking_block_hours' => true,
						   'update_sync_ical_type_'	=> true
			              );
   
	$dpProEventCalendar = get_option('dpProEventCalendar_options');
	
	if(!$dpProEventCalendar) {
	 $dpProEventCalendar = array();
	}
	
	foreach($default_events as $key=>$value) {
	  if(!isset($dpProEventCalendar[$key])) {
		 $dpProEventCalendar[$key] = $value;
	  }
	}
	
	delete_option('dpProEventCalendar_options');	  
	update_option('dpProEventCalendar_options',$dpProEventCalendar);
}
register_activation_hook( __FILE__, 'checkMU_install_dpProEventCalendar' );

/* Update checker */
if($dpProEventCalendar['purchase_code'] != "" || esc_attr( get_site_option('pec-purchase-code')) != "") {
	require 'includes/plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = PucFactoryCustom::buildUpdateChecker(
	    'http://wpsleek.com/proeventcalendar.json',
	    __FILE__
	);
}

/* Uninstall */
function checkMU_uninstall_dpProEventCalendar($network_wide) {
	global $wpdb;
	if ( $network_wide ) {
		$blog_list = get_blog_list( 0, 'all' );
		foreach ($blog_list as $blog) {
			switch_to_blog($blog['blog_id']);
			uninstall_dpProEventCalendar();
		}
		switch_to_blog($wpdb->blogid);
	} else {
		uninstall_dpProEventCalendar();
	}
}

function uninstall_dpProEventCalendar() {
	global $wpdb, $table_prefix;
	delete_option('dpProEventCalendar_options'); 
	
	$events_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
	$sql = "DROP TABLE $events_table;";
	$wpdb->query($sql);
	
	$calendars_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	$sql = "DROP TABLE $calendars_table;";
	$wpdb->query($sql);
	
	$booking_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	$sql = "DROP TABLE $booking_table;";
	$wpdb->query($sql);
	
	$special_dates_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
	$sql = "DROP TABLE $special_dates_table;";
	$wpdb->query($sql);
	
	$special_dates_calendar_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
	$sql = "DROP TABLE $special_dates_calendar_table;";
	$wpdb->query($sql);
	
	$subscribers_calendar_table = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;
	$sql = "DROP TABLE $subscribers_calendar_table;";
	$wpdb->query($sql);
}
register_uninstall_hook( __FILE__, 'checkMU_uninstall_dpProEventCalendar' );

/* Add new Blog */

add_action( 'wpmu_new_blog', 'newBlog_dpProEventCalendar', 10, 6); 		
 
function newBlog_dpProEventCalendar($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $wpdb;
 
	if (is_plugin_active_for_network('dpProEventCalendar/dpProEventCalendar.php')) {
		$old_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		install_dpProEventCalendar();
		switch_to_blog($old_blog);
	}
}

//require_once (dirname (__FILE__) . '/update-notifier.php');
require_once (dirname (__FILE__) . '/functions.php');
require_once (dirname (__FILE__) . '/includes/core.php');
require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/mailchimp/miniMCAPI.class.php');


if(!isset($dpProEventCalendar['disable_rewrite_rules']) || !$dpProEventCalendar['disable_rewrite_rules']) {
	register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
	register_activation_hook( __FILE__, 'dpProEventCalendar_flush_rewrites' );
	function dpProEventCalendar_flush_rewrites() {
		
		dpProEventCalendar_pro_event_calendar_init();
		flush_rewrite_rules();
	}
	
	//add_action( 'admin_init', 'flush_rewrite_rules' );
}

/*******************/
/* UPDATES 
/*******************/

$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
$table_name_events = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
$table_name_special_dates = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
$table_name_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
$table_name_subscribers_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;



	
if(!isset($dpProEventCalendar['update_sync_ical_type_'])) {

	if(!isset($dpProEventCalendar['update_sync_ical_type_'])) {
		$dpProEventCalendar['update_sync_ical_type_'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars MODIFY sync_ical_category int(11) NOT NULL DEFAULT 0;";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_booking_block_hours'])) {
		$dpProEventCalendar['form_show_booking_block_hours'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_booking_block_hours TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_fully_booked'])) {
		$dpProEventCalendar['display_fully_booked'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_display_fully_booked TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
	
	if(!isset($dpProEventCalendar['venue_filter_include'])) {
		$dpProEventCalendar['venue_filter_include'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (venue_filter_include text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_extra_dates'])) {
		$dpProEventCalendar['form_show_extra_dates'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_extra_dates TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_custom_fields_calendar'])) {
		$dpProEventCalendar['booking_custom_fields_calendar'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_custom_fields text NULL);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $table_name_calendars ADD (form_custom_fields text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_timezone'])) {
		$dpProEventCalendar['form_show_timezone'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_timezone TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
	
	if(!isset($dpProEventCalendar['booking_remaining'])) {
		$dpProEventCalendar['booking_remaining'] = true;

		$sql = "ALTER TABLE $table_name_calendars ADD (booking_show_remaining TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_cancel_email_enable'])) {

		$dpProEventCalendar['booking_cancel_email_enable'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_cancel_email_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $table_name_calendars ADD (booking_cancel_email_template TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['new_event_email_enable'])) {
		$dpProEventCalendar['new_event_email_enable'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (new_event_email_enable TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_cancel'])) {
		$dpProEventCalendar['booking_cancel'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_cancel TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['link_post_target'])) {
		$dpProEventCalendar['link_post_target'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (link_post_target varchar(80) NULL DEFAULT '_self');";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);

	}

	if(!isset($dpProEventCalendar['form_show_location_options'])) {
		$dpProEventCalendar['form_show_location_options'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_location_options TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_location_filter'])) {
		$dpProEventCalendar['show_location_filter'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (show_location_filter TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_attendees_names'])) {
		$dpProEventCalendar['display_attendees_names'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_display_attendees_names TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_end_time'])) {
		$dpProEventCalendar['form_show_end_time'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_end_date TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_start_time TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_end_time TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_timezone_update'])) {
		$dpProEventCalendar['show_timezone_update'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (show_timezone TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_ical_category'])) {
		$dpProEventCalendar['sync_ical_category'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (sync_ical_category INT(11) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['translation_fields'])) {
		$dpProEventCalendar['translation_fields'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (translation_fields TEXT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_fb_page'])) {
		$dpProEventCalendar['sync_fb_page'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (sync_fb_page TEXT NOT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_email_template_reminder_user'])) {
		$dpProEventCalendar['booking_email_template_reminder_user'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_email_template_reminder_user TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_color'])) {
		$dpProEventCalendar['form_show_color'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_color TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);

		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_max_upcoming_dates'])) {
		$dpProEventCalendar['booking_max_upcoming_dates'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_max_upcoming_dates INT(11) NOT NULL DEFAULT 10);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_bookings'])) {
		$dpProEventCalendar['form_bookings'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_booking_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_booking_limit TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_booking_price TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
					
	if(!isset($dpProEventCalendar['form_text_editor'])) {
		$dpProEventCalendar['form_text_editor'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_text_editor TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_max_quantity'])) {
		$dpProEventCalendar['booking_max_quantity'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_max_quantity INT(11) NOT NULL DEFAULT 3);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['new_event_template_published'])) {
		$dpProEventCalendar['new_event_template_published'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (new_event_email_template_published TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['display_attendees'])) {
		$dpProEventCalendar['display_attendees'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_display_attendees TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['allow_user_add_event_roles'])) {
		$dpProEventCalendar['allow_user_add_event_roles'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (allow_user_add_event_roles text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['daily_weekly_layout'])) {
		$dpProEventCalendar['daily_weekly_layout'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (daily_weekly_layout VARCHAR(80) NOT NULL DEFAULT 'schedule');";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_titles_monthly'])) {
		$dpProEventCalendar['show_titles_monthly'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (show_titles_monthly TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking'])) {
		$dpProEventCalendar['booking'] = true;
		
		$sql = "CREATE TABLE $table_name_booking (
					id int(11) NOT NULL AUTO_INCREMENT,
					id_calendar int(11) NOT NULL,
					id_event int(11) NOT NULL,
					booking_date datetime NOT NULL,
					event_date date NOT NULL,
					id_user int(11) NOT NULL,
					comment text NULL,
					UNIQUE KEY id(id)
				) DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
		$rs = $wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
	
	if(!isset($dpProEventCalendar['booking_coupon'])) {
		$dpProEventCalendar['booking_coupon'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (id_coupon int(11) NULL);";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $table_name_booking ADD (coupon_discount int(11) NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
					
	if(!isset($dpProEventCalendar['booking_cancel_reason'])) {
		$dpProEventCalendar['booking_cancel_reason'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (cancel_reason text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_extra_fields'])) {
		$dpProEventCalendar['booking_extra_fields'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (extra_fields TEXT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_phone'])) {
		$dpProEventCalendar['booking_phone'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (phone VARCHAR(80) NULL DEFAULT '');";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_show_phone TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_cancel_date'])) {
		$dpProEventCalendar['booking_cancel_date'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (cancel_date datetime NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_quantity'])) {
		$dpProEventCalendar['booking_quantity'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (quantity INT(11) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['updatebookingtable'])) {
		$dpProEventCalendar['updatebookingtable'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (name varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_booking ADD (email varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_booking ADD (status varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_ical'])) {
		$dpProEventCalendar['sync_ical'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (sync_ical_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (sync_ical_url VARCHAR(255) NOT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (sync_ical_frequency VARCHAR(80) NOT NULL DEFAULT '');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['sync_ical_url_text'])) {
		$dpProEventCalendar['sync_ical_url_text'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars MODIFY sync_ical_url TEXT NOT NULL DEFAULT '';";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
			
	if(!isset($dpProEventCalendar['booking_non_logged_options'])) {
		$dpProEventCalendar['booking_non_logged_options'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (name varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_booking ADD (email varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
						
	if(!isset($dpProEventCalendar['booking_non_logged'])) {
		$dpProEventCalendar['booking_non_logged'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_non_logged TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_email_template_user TEXT NOT NULL);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_email_template_admin TEXT NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_status'])) {
		$dpProEventCalendar['booking_status'] = true;
		
		$sql = "ALTER TABLE $table_name_booking ADD (status varchar(255) NOT NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['enable_wpml'])) {
		$dpProEventCalendar['enable_wpml'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (enable_wpml TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['booking_lang'])) {
		$dpProEventCalendar['booking_lang'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_enable TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_comment TINYINT(1) NULL DEFAULT 0);";
		$wpdb->query($sql);
		$sql = "ALTER TABLE $table_name_calendars ADD (booking_event_color VARCHAR(80) NOT NULL DEFAULT '#e14d43');";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}


	if(!isset($dpProEventCalendar['cache_active'])) {
		$dpProEventCalendar['cache_active'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (cache_active TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['remove_events'])) {
		$dpProEventCalendar['remove_events'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (allow_user_remove_event TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['show_author'])) {
		$dpProEventCalendar['show_author'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (show_author TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['allow_users_edit_event'])) {
		$dpProEventCalendar['allow_users_edit_event'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (allow_user_edit_event TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['form_show_fields'])) {
		$dpProEventCalendar['form_show_fields'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_description TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_category TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_hide_time TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_frequency TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_all_day TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_image TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_link TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_location TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_phone TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (form_show_map TINYINT(1) NOT NULL DEFAULT 1);";
		$wpdb->query($sql);

		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
						
	if(!isset($dpProEventCalendar['limit_time_start'])) {
		$dpProEventCalendar['limit_time_start'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (limit_time_start TINYINT(2) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $table_name_calendars ADD (limit_time_end TINYINT(2) NOT NULL DEFAULT 23);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['hide_old_dates'])) {
		$dpProEventCalendar['hide_old_dates'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (hide_old_dates TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['assign_events_admin'])) {
		$dpProEventCalendar['assign_events_admin'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (assign_events_admin INT(11) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['category_filter_include'])) {
		$dpProEventCalendar['category_filter_include'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (category_filter_include text NULL);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}

	if(!isset($dpProEventCalendar['article_share'])) {
		$dpProEventCalendar['article_share'] = true;
		
		$sql = "ALTER TABLE $table_name_calendars ADD (article_share TINYINT(1) NOT NULL DEFAULT 0);";
		$wpdb->query($sql);
		
		update_option('dpProEventCalendar_options',$dpProEventCalendar);
	}
}
?>