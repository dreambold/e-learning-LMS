<?php 
// This function displays the admin page content
function dpProEventCalendar_calendars_page() {
	global $dpProEventCalendar, $dpProEventCalendar_cache, $wpdb, $table_prefix;
	$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	$table_name_events = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
	$table_name_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
	$table_name_subscribers_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;
	
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));
	$memory_limit = (int)(ini_get('memory_limit'));
	$upload_mb = min($max_upload, $max_post, $memory_limit);

	if ($_POST['submit']) {
	   
	   foreach($_POST as $key=>$value) { $$key = stripslashes_deep($value); }
	   
	   if($format_ampm != 1) { $format_ampm = 0; }
	   if($show_time != 1) { $show_time = 0; }
	   if($show_timezone != 1) { $show_timezone = 0; }
	   if($show_search != 1) { $show_search = 0; }
	   if($show_category_filter != 1) { $show_category_filter = 0; }
	   if($show_location_filter != 1) { $show_location_filter = 0; }
	   if($show_x != 1) { $show_x = 0; }
	   if($allow_user_add_event != 1) { $allow_user_add_event = 0; }
	   if($allow_user_edit_event != 1) { $allow_user_edit_event = 0; }
	   if($allow_user_remove_event != 1) { $allow_user_remove_event = 0; }
	   if($publish_new_event != 1) { $publish_new_event = 0; }
	   if($new_event_email_enable != 1) { $new_event_email_enable = 0; }
	   if($booking_cancel_email_enable != 1) { $booking_cancel_email_enable = 0; }
	   if($show_view_buttons != 1) { $show_view_buttons = 0; }
	   if($show_preview != 1) { $show_preview = 0; }
	   if($show_titles_monthly != 1) { $show_titles_monthly = 0; }
	   if($show_references != 1) { $show_references = 0; }
	   if($show_author != 1) { $show_author = 0; }
	   if($cache_active != 1) { $cache_active = 0; }
	   if($ical_active != 1) { $ical_active = 0; }
	   if($rss_active != 1) { $rss_active = 0; }
	   if($subscribe_active != 1) { $subscribe_active = 0; }
       if($link_post != 1) { $link_post = 0; }
	   if($email_admin_new_event != 1) { $email_admin_new_event = 0; }
	   if($article_share != 1) { $article_share = 0; }
	   if($hide_old_dates != 1) { $hide_old_dates = 0; }
	   if(!is_numeric($limit_time_start)) { $limit_time_start = 0; }
	   if(!is_numeric($limit_time_end)) { $limit_time_end = 23; }
	   if(!is_numeric($booking_max_quantity) || $booking_max_quantity <= 0) { $booking_max_quantity = 3; }
	   if(!is_numeric($booking_max_upcoming_dates) || $booking_max_upcoming_dates <= 0) { $booking_max_upcoming_dates = 10; }
	   if($assign_events_admin == "") { $assign_events_admin = 0; }
	   if($form_show_end_date != 1) { $form_show_end_date = 0; }
	   if($form_show_start_time != 1) { $form_show_start_time = 0; }
	   if($form_show_end_time != 1) { $form_show_end_time = 0; }
	   if($form_show_extra_dates != 1) { $form_show_extra_dates = 0; }
	   if($form_show_description != 1) { $form_show_description = 0; }
	   if($form_show_category != 1) { $form_show_category = 0; }
	   if($form_show_hide_time != 1) { $form_show_hide_time = 0; }
	   if($form_show_frequency != 1) { $form_show_frequency = 0; }
	   if($form_show_all_day != 1) { $form_show_all_day = 0; }
	   if($form_show_image != 1) { $form_show_image = 0; }
	   if($form_show_link != 1) { $form_show_link = 0; }
	   if($form_show_location != 1) { $form_show_location = 0; }
	   if($form_show_location_options != 1) { $form_show_location_options = 0; }
	   if($form_show_phone != 1) { $form_show_phone = 0; }
	   if($form_show_map != 1) { $form_show_map = 0; }
	   if($form_show_color != 1) { $form_show_color = 0; }
	   if($form_show_timezone != 1) { $form_show_timezone = 0; }
	   if($booking_enable != 1) { $booking_enable = 0; }
	   if($booking_comment != 1) { $booking_comment = 0; }
	   if($booking_non_logged != 1) { $booking_non_logged = 0; }
	   if($booking_cancel != 1) { $booking_cancel = 0; }
	   if($enable_wpml != 1) { $enable_wpml = 0; }
	   if($booking_display_attendees != 1) { $booking_display_attendees = 0; }
	   if($booking_display_attendees_names != 1) { $booking_display_attendees_names = 0; }
	   if($booking_display_fully_booked != 1) { $booking_display_fully_booked = 0; }
	   
	   if($booking_show_phone != 1) { $booking_show_phone = 0; }
	   if($booking_show_remaining != 1) { $booking_show_remaining = 0; }
	   if($form_text_editor != 1) { $form_text_editor = 0; }
	   if($form_show_booking_enable != 1) { $form_show_booking_enable = 0; }
	   if($sync_ical_enable != 1) { $sync_ical_enable = 0; }
	   if($form_show_booking_limit != 1) { $form_show_booking_limit = 0; }
	   if($form_show_booking_price != 1) { $form_show_booking_price = 0; }
	   if($form_show_booking_block_hours != 1) { $form_show_booking_block_hours = 0; }
	   if(!is_numeric($sync_ical_category)) { $sync_ical_category = 0; }
	   $translation_fields = serialize($translation_fields);

	   if(is_array($category_filter_include)) {
	   	$category_filter_include = implode(",", $category_filter_include);
	   } else {
		$category_filter_include =  "";
	   }

	   if(is_array($venue_filter_include)) {
	   	$venue_filter_include = implode(",", $venue_filter_include);
	   } else {
		$venue_filter_include =  "";
	   }
	   
	   if(is_array($allow_user_add_event_roles)) {
	   	$allow_user_add_event_roles = implode(",", $allow_user_add_event_roles);
	   } else {
		$allow_user_add_event_roles =  "";
	   }

	   if(is_array($booking_custom_fields)) {
	   	$booking_custom_fields = implode(",", $booking_custom_fields);
	   } else {
		$booking_custom_fields =  "";
	   }

	   if(is_array($form_custom_fields)) {
	   	$form_custom_fields = implode(",", $form_custom_fields);
	   } else {
		$form_custom_fields =  "";
	   }

	   
	   $data = array();
	   $format = array();
	   
	   $data['title'] = $title;
	   $format[] = '%s';
	   $data['description'] = $description;
	   $format[] = '%s';
	   $data['width'] = $width;
	   $format[] = '%s';
	   $data['width_unity'] = $width_unity;
	   $format[] = '%s';
	   if(!is_null($default_date)) {
		   $data['default_date'] = $default_date;
		   $format[] = '%s';
	   }
	   if(!is_null($date_range_start)) {
		   $data['date_range_start'] = $date_range_start;
		   $format[] = '%s';
	   }
	   if(!is_null($date_range_end)) {
		   $data['date_range_end'] = $date_range_end;
		   $format[] = '%s';
	   }
	   $data['current_date_color'] = $current_date_color;
	   $format[] = '%s';
	   $data['active'] = 1;
	   $format[] = '%d';
	   $data['hide_old_dates'] = $hide_old_dates;
	   $format[] = '%d';
	   $data['limit_time_start'] = $limit_time_start;
	   $format[] = '%d';
	   $data['limit_time_end'] = $limit_time_end;
	   $format[] = '%d';
	   $data['assign_events_admin'] = $assign_events_admin;
	   $format[] = '%d';
	   $data['cache_active'] = $cache_active;
	   $format[] = '%d';
	   $data['ical_active'] = $ical_active;
	   $format[] = '%d';
	   $data['ical_limit'] = $ical_limit;
	   $format[] = '%s';
	   $data['rss_active'] = $rss_active;
	   $format[] = '%d';
	   $data['booking_display_attendees'] = $booking_display_attendees;
	   $format[] = '%d';
	   $data['booking_display_attendees_names'] = $booking_display_attendees_names;
	   $format[] = '%d';
	   $data['booking_display_fully_booked'] = $booking_display_fully_booked;
	   $format[] = '%d';
	   $data['booking_show_phone'] = $booking_show_phone;
	   $format[] = '%d';
	   $data['booking_show_remaining'] = $booking_show_remaining;
	   $format[] = '%d';
	   $data['booking_enable'] = $booking_enable;
	   $format[] = '%d';
	   $data['booking_non_logged'] = $booking_non_logged;
	   $format[] = '%d';
	   $data['booking_cancel'] = $booking_cancel;
	   $format[] = '%d';
	   $data['booking_email_template_user'] = $booking_email_template_user;
	   $format[] = '%s';
	   $data['booking_cancel_email_template'] = $booking_cancel_email_template;
	   $format[] = '%s';
	   $data['booking_email_template_admin'] = $booking_email_template_admin;
	   $format[] = '%s';
	   $data['booking_email_template_reminder_user'] = $booking_email_template_reminder_user;
	   $format[] = '%s';
	   $data['new_event_email_template_published'] = $new_event_email_template_published;
	   $format[] = '%s';
	   $data['booking_comment'] = $booking_comment;
	   $format[] = '%d';
	   $data['booking_event_color'] = '#fff';
	   $format[] = '%s';
	   $data['subscribe_active'] = $subscribe_active;
	   $format[] = '%d';
	   $data['mailchimp_api'] = $mailchimp_api;
	   $format[] = '%s';
	   $data['mailchimp_list'] = $mailchimp_list;
	   $format[] = '%s';
	   $data['rss_limit'] = $rss_limit;
	   $format[] = '%s';
	   $data['link_post'] = $link_post;
	   $format[] = '%d';
	   $data['link_post_target'] = $link_post_target;
	   $format[] = '%s';
	   $data['article_share'] = $article_share;
	   $format[] = '%d';
	   $data['email_admin_new_event'] = $email_admin_new_event;
	   $format[] = '%d';
	   $data['view'] = $view;
	   $format[] = '%s';
	   $data['format_ampm'] = $format_ampm;
	   $format[] = '%d';
	   $data['show_time'] = $show_time;
	   $format[] = '%d';
	   $data['show_timezone'] = $show_timezone;
	   $format[] = '%d';
	   $data['enable_wpml'] = $enable_wpml;
	   $format[] = '%d';
	   $data['show_category_filter'] = $show_category_filter;
	   $format[] = '%d';
	   $data['category_filter_include'] = $category_filter_include;
	   $format[] = '%s';
	   $data['venue_filter_include'] = $venue_filter_include;
	   $format[] = '%s';
	   $data['show_location_filter'] = $show_location_filter;
	   $format[] = '%d';
	   $data['allow_user_add_event_roles'] = $allow_user_add_event_roles;
	   $format[] = '%s';
	   $data['booking_custom_fields'] = $booking_custom_fields;
	   $format[] = '%s';
	   $data['form_custom_fields'] = $form_custom_fields;
	   $format[] = '%s';
	   $data['show_search'] = $show_search;
	   $format[] = '%d';
	   $data['show_x'] = $show_x;
	   $format[] = '%d';
	   $data['allow_user_add_event'] = $allow_user_add_event;
	   $format[] = '%d';
	   $data['allow_user_edit_event'] = $allow_user_edit_event;
	   $format[] = '%d';
	   $data['allow_user_remove_event'] = $allow_user_remove_event;
	   $format[] = '%d';
	   $data['publish_new_event'] = $publish_new_event;
	   $format[] = '%d';
	   $data['new_event_email_enable'] = $new_event_email_enable;
	   $format[] = '%d';
	   $data['booking_cancel_email_enable'] = $booking_cancel_email_enable;
	   $format[] = '%d';
	   $data['show_view_buttons'] = $show_view_buttons;
	   $format[] = '%d';
	   $data['show_preview'] = $show_preview;
	   $format[] = '%d';
	   $data['show_titles_monthly'] = $show_titles_monthly;
	   $format[] = '%d';
	   $data['show_references'] = $show_references;
	   $format[] = '%d';
	   $data['show_author'] = $show_author;
	   $format[] = '%d';
	   $data['form_text_editor'] = $form_text_editor;
	   $format[] = '%d';
	   $data['form_show_end_date'] = $form_show_end_date;
	   $format[] = '%d';
	   $data['form_show_start_time'] = $form_show_start_time;
	   $format[] = '%d';
	   $data['form_show_end_time'] = $form_show_end_time;
	   $format[] = '%d';
	   $data['form_show_extra_dates'] = $form_show_extra_dates;
	   $format[] = '%d';
	   $data['form_show_description'] = $form_show_description;
	   $format[] = '%d';
	   $data['form_show_category'] = $form_show_category;
	   $format[] = '%d';
	   $data['form_show_hide_time'] = $form_show_hide_time;
	   $format[] = '%d';
	   $data['form_show_frequency'] = $form_show_frequency;
	   $format[] = '%d';
	   $data['form_show_all_day'] = $form_show_all_day;
	   $format[] = '%d';
	   $data['form_show_image'] = $form_show_image;
	   $format[] = '%d';
	   $data['form_show_link'] = $form_show_link;
	   $format[] = '%d';
	   $data['form_show_location'] = $form_show_location;
	   $format[] = '%d';
	   $data['form_show_location_options'] = $form_show_location_options;
	   $format[] = '%d';
	   $data['form_show_phone'] = $form_show_phone;
	   $format[] = '%d';
	   $data['form_show_map'] = $form_show_map;
	   $format[] = '%d';
	   $data['form_show_color'] = $form_show_color;
	   $format[] = '%d';
	   $data['form_show_timezone'] = $form_show_timezone;
	   $format[] = '%d';
	   $data['first_day'] = $first_day;
	   $format[] = '%d';
	   $data['skin'] = $skin;
	   $format[] = '%s';
	   $data['sync_ical_enable'] = $sync_ical_enable;
	   $format[] = '%d';
	   $data['sync_ical_url'] = str_replace("webcal://", "http://", $sync_ical_url);
	   $format[] = '%s';
	   $data['sync_fb_page'] = $sync_fb_page;
	   $format[] = '%s';
	   $data['sync_ical_frequency'] = $sync_ical_frequency;
	   $format[] = '%s';
	   $data['sync_ical_category'] = $sync_ical_category;
	   $format[] = '%d';
	   $data['daily_weekly_layout'] = $daily_weekly_layout;
	   $format[] = '%s';
	   $data['booking_max_quantity'] = $booking_max_quantity;
	   $format[] = '%d';
	   $data['form_show_booking_enable'] = $form_show_booking_enable;
	   $format[] = '%d';
	   $data['form_show_booking_limit'] = $form_show_booking_limit;
	   $format[] = '%d';
	   $data['form_show_booking_price'] = $form_show_booking_price;
	   $format[] = '%d';
	   $data['form_show_booking_block_hours'] = $form_show_booking_block_hours;
	   $format[] = '%d';
	   $data['booking_max_upcoming_dates'] = $booking_max_upcoming_dates;
	   $format[] = '%d';
	   $data['translation_fields'] = $translation_fields;
	   $format[] = '%s';
	   
	   if (is_numeric($_POST['calendar_id']) && $_POST['calendar_id'] > 0) {
	   	   $wpdb->query("SET NAMES utf8");
		   
	   	   $sql = "UPDATE $table_name SET ";
		   $sql .= "WHERE id = $calendar_id ";
		   
		   $wpdb->update( 
				$table_name, 
				$data, 
				array( 'id' => $calendar_id ), 
				$format, 
				array( '%d' ) 
			);

	   } else {

		   $wpdb->insert( 
				$table_name, 
				$data, 
				$format 
			);

		   $calendar_id = $wpdb->insert_id;
	   }
	   
   	   if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar_id])) {
		   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	   }
	   
	   wp_redirect( admin_url('admin.php?page=dpProEventCalendar-admin&settings-updated=1') );
	   exit;
	}
	
	if(!empty($_FILES['pec_ical_file']['name']) || !empty($_POST['pec_fb_event_url'])) {
		$calendar_id = $_POST['pec_id_calendar_ics'];
		$category_ics = $_POST['pec_category_ics'];
		
		
		if(!empty($_POST['pec_fb_event_url'])) {
			
			$fb_page_arr = explode(",", $_POST['pec_fb_event_url']);
	
			foreach($fb_page_arr as $url) {
				
				if($url != "") {
					$event_url = rtrim($url);

					dpProEventCalendar_importFB($calendar_id, $event_url, $category_ics, $_POST['pec_fb_event_option'], $_POST['pec_offset_hour']);
				}
				
			}
			
			/*
			echo '<pre>';
			print_r($graph_arr);
			echo '</pre>';
			die();*/
		}
		
		
		if(!empty($_FILES['pec_ical_file']['name'])) {
			$filename= $_FILES['pec_ical_file']['tmp_name'];
			
			dpProEventCalendar_importICS($calendar_id, $filename, $_FILES['pec_ical_file']['name'], $category_ics, $_POST['pec_offset_hour']);
			
		}
		
		if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar_id])) {
		   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	   }
	   
		wp_redirect( admin_url('admin.php?page=dpProEventCalendar-admin&settings-updated=1') );
	   exit;
   }
	
	if ($_GET['delete_calendar']) {
	   $calendar_id = $_GET['delete_calendar'];
	   
	   $querystr = "
		SELECT sync_ical_url, sync_fb_page
		FROM ".$table_name."
		WHERE id = ".$calendar_id."
		LIMIT 1
		";
		$calendars_obj = $wpdb->get_results($querystr, OBJECT);
		
	   $args = array( 
			'posts_per_page' => -1, 
			'post_type' => 'pec-events', 
			'meta_key' => 'pec_id_calendar',
			'meta_value' => $calendar_id
		);
					
	   $delete_posts = get_posts( $args );
	   if(!empty($delete_posts)) {
		   foreach($delete_posts as $key) {
	   			wp_delete_post($key->ID);
		   }
	   }
	   	
	   $sql = "DELETE FROM $table_name WHERE id = $calendar_id;";
	   $result = $wpdb->query($sql);
	   
	   $sql = "DELETE FROM $table_name_special_dates_calendar WHERE calendar = $calendar_id;";
	   $result = $wpdb->query($sql);
	   	
		wp_clear_scheduled_hook( 'pecsyncical'.$calendar_id, array($calendar_id, $calendars_obj[0]->sync_ical_url, $calendars_obj[0]->sync_fb_page) );
			   
	   wp_redirect( admin_url('admin.php?page=dpProEventCalendar-admin&settings-updated=1') );
	   exit;
	}
	
	if ($_GET['delete_calendar_events']) {
	   $calendar_id = $_GET['delete_calendar_events'];
	   	
	   $args = array( 
			'posts_per_page' => -1, 
			'post_type' => 'pec-events', 
			'meta_key' => 'pec_id_calendar',
			'meta_value' => $calendar_id
		);
					
	   $delete_posts = get_posts( $args );
	   if(!empty($delete_posts)) {
		   foreach($delete_posts as $key) {
	   			wp_trash_post($key->ID);
		   }
	   }
			   
	   wp_redirect( admin_url('admin.php?page=dpProEventCalendar-admin&settings-updated=1') );
	   exit;
	}
	
	if ($_GET['delete_subscriber']) {
	   $subscriber_id = $_GET['delete_subscriber'];
	   $calendar_id = $_GET['edit'];
	   	
	   $sql = "DELETE FROM $table_name_subscribers_calendar WHERE calendar = ".$calendar_id." AND id = ".$subscriber_id.";";
	   $result = $wpdb->query($sql);
	   	   
	   wp_redirect( admin_url('admin.php?page=dpProEventCalendar-admin&edit='.$calendar_id.'&settings-updated=1') );
	   exit;
	}
	
	
	require_once (dirname (__FILE__) . '/../classes/base.class.php');
	
	
	?>
    <script type="text/javascript">
	function MailChimp_getList() {
		jQuery('#div_mailchimp_list').hide();
		
		if(jQuery('#mailchimp_api_key').val() != "") {
			jQuery.post("<?php echo dpProEventCalendar_plugin_url('ajax/MailChimp_getLists.php')?>", { mailchimp_api: jQuery('#mailchimp_api_key').val() },
			   function(data) {
				 jQuery('#mailchimp_list').html(data);
				 jQuery('#div_mailchimp_list').show();
			   }
			);
			
		}
	}
	</script>

	<div class="wrap" style="clear:both;" id="dp_options">
    <h2></h2>
	<div style="clear:both;"></div>
 	<!--end of poststuff --> 
 	<div id="dp_ui_content">
    	
        <div id="leftSide">
        	<div id="dp_logo"></div>
            <p>
                Version: <?php echo DP_PRO_EVENT_CALENDAR_VER?><br />
            </p>
            <ul id="menu" class="nav">
            	<li><a href="admin.php?page=dpProEventCalendar-settings" title=""><span><?php _e('General Settings','dpProEventCalendar'); ?></span></a></li>
                <li><a href="javascript:void(0);" class="active" title=""><span><?php _e('Calendars','dpProEventCalendar'); ?></span></a></li>
                <li><a href="edit.php?post_type=pec-events" title=""><span><?php _e('Events','dpProEventCalendar'); ?></span></a></li>
                <li><a href="edit.php?post_type=pec-venues" title=""><span><?php _e('Venues','dpProEventCalendar'); ?></span></a></li>
                <li><a href="admin.php?page=dpProEventCalendar-special" title=""><span><?php _e('Special Dates / Event Color','dpProEventCalendar'); ?></span></a></li>
                <li><a href="admin.php?page=dpProEventCalendar-custom-shortcodes" title=""><span><?php _e('Custom Shortcodes','dpProEventCalendar'); ?></span></a></li>
                <?php
				if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
				?>
				<li><a href="admin.php?page=dpProEventCalendar-payments" title=""><span><?php _e('Payments Options','dpProEventCalendar'); ?></span></a></li>
				<?php }?>
                <li><a href="http://wpsleek.com/pro-event-calendar-documentation/" target="_blank" title=""><span><?php _e('Documentation','dpProEventCalendar'); ?></span></a></li>
            </ul>

            <a href="http://codecanyon.net/downloads" target="_blank" class="rate_plugin">
                <?php _e('Rate this plugin!','dpProEventCalendar'); ?>
                <br>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
            </a>
            
            <div class="clear"></div>
		</div>     
		<?php if(!is_numeric($_GET['add']) && !is_numeric($_GET['edit'])) {	?>
 
        
        <div id="rightSide">
        	<div id="menu_general_settings">
                <div class="titleArea">
                    <div class="wrapper">
                        <div class="pageTitle">
                            <h2><?php _e('Calendars List','dpProEventCalendar'); ?></h2>
                            <span><?php _e('Use the shortcode in your posts or pages.','dpProEventCalendar'); ?></span>
                        </div>
                        
                        <div class="submit" style="float:right; margin:0; margin-top:66px;">
                    
	                    	<input type="button" class="button-primary" value="<?php echo __( 'Add new calendar', 'dpProEventCalendar' )?>" name="add_calendar" onclick="location.href='<?php echo dpProEventCalendar_admin_url( array( 'add' => '1' ) )?>';" />
	                    
	                    </div>
                        <div class="clear"></div>
                    </div>
                </div>
                
                <div class="wrapper">

                <form action="<?php echo admin_url('admin.php?page=dpProEventCalendar-admin&noheader=true'); ?>" id="dpProEventCalendar_events_meta" class="dpProEventCalendar_calendar_form" method="post" enctype="multipart/form-data">
					<?php settings_fields('dpProEventCalendar-group'); ?>
                    
                    <input type="hidden" name="remove_posts_calendar" value="1" />
                    
                	
                	<?php 

                	$counter = 0;
					$limit = 10;
					$pag = 0;
					if(is_numeric($_GET['pag']) && $_GET['pag'] > 0) {
						$pag = $_GET['pag'];
					}
					
					$cal_output = "";
					$querystr = "
                    SELECT COUNT(id) as counter
                    FROM $table_name calendars;
                    ";

                    $count = $wpdb->get_row($querystr, OBJECT);
                    
                    
                    if($count->counter > $limit) {

                    	if($pag > 0) {
                    	?>
						<a href="<?php echo admin_url('admin.php?page=dpProEventCalendar-admin&pag='.($pag - 1))?>"><?php echo __('Prev', 'DpProEventCalendar')?></a> | 
                    	
                    	<?php }?>
                    	<?php if((($pag + 1) * $limit) < $count->counter) {?>
                    	<a href="<?php echo admin_url('admin.php?page=dpProEventCalendar-admin&pag='.($pag + 1))?>"><?php echo __('Next', 'DpProEventCalendar')?></a>
                    	<?php
                    	}

                    }?>

                    <table class="widefat" cellpadding="0" cellspacing="0" id="sort-table">
                    	<thead>
                    		<tr style="cursor:default !important;">
                                <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                <th><?php _e('Default Shortcode','dpProEventCalendar'); ?></th>
                                <th><?php _e('Title','dpProEventCalendar'); ?></th>
                                <th><?php _e('Events','dpProEventCalendar'); ?></th>
                                <th><?php _e('Actions','dpProEventCalendar'); ?></th>
                             </tr>
                        </thead>
                        <tbody>
                    <?php 
					

                    $querystr = "
                    SELECT calendars.*
                    FROM $table_name calendars
                    ORDER BY calendars.title ASC
                    LIMIT ".($pag * $limit)." ,".$limit;
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar) {
						$dpProEventCalendar_class = new DpProEventCalendar( true, (is_numeric($calendar->id) ? $calendar->id : null) );
						
						$dpProEventCalendar_class->addScripts(true);
						
						$calendar_nonce = $dpProEventCalendar_class->getNonce();
						$args = array( 'numberposts' => 500, 'fields' => 'ids', 'post_status' => 'publish', 'post_type' => 'pec-events' );
						$args['meta_query'] = array( 
							'relation' => 'OR',
							array('key' => "pec_id_calendar", "value" => '(,'.$calendar->id.',)', 'compare' => 'REGEXP'),
							array('key' => "pec_id_calendar", "value" => '(,'.$calendar->id.')$', 'compare' => 'REGEXP'),
							array('key' => "pec_id_calendar", "value" => '^('.$calendar->id.',)', 'compare' => 'REGEXP'),
							array('key' => "pec_id_calendar", "value" => $calendar->id)
						);

						$events_cal = get_posts( $args );
						$events_count = count($events_cal);
						
                        echo '<tr id="'.$calendar->id.'">
								<td width="5%">'.$calendar->id.'</td>
								<td width="25%"><div class="pec_calendar_shortcode">[dpProEventCalendar id='.$calendar->id.']</div></td>
								<td width="20%">'.$calendar->title.'</td>
								<td width="5%"><a href="'.admin_url('edit.php?s&post_status=all&post_type=pec-events&action=-1&m=0&pec_id_calendar='.$calendar->id.'&paged=1').'">'.($events_count == 500 ? "500+" : $events_count).'</a></td>
								<td width="45%">
									<input type="button" value="'.__( 'Edit', 'dpProEventCalendar' ).'" name="edit_calendar" class="button-secondary" onclick="location.href=\''.admin_url('admin.php?page=dpProEventCalendar-admin&edit='.$calendar->id).'\';" />
									<input type="button" value="'.__( 'Special Dates', 'dpProEventCalendar' ).'" name="sp_calendar" data-calendar-id="'.$calendar->id.'" data-calendar-nonce="'.$calendar_nonce.'" class="btn_manage_special_dates button-secondary" />
									<input type="button" value="'.__( 'Delete', 'dpProEventCalendar' ).'" name="delete_calendar" class="button-secondary" onclick="if(confirmCalendarDelete()) { location.href=\''.admin_url('admin.php?page=dpProEventCalendar-admin&delete_calendar='.$calendar->id.'&noheader=true').'\'; }" />
									<input type="button" value="'.__( 'Delete All Events', 'dpProEventCalendar' ).'" name="delete_calendar" class="button-secondary" onclick="if(confirmCalendarEventsDelete()) { location.href=\''.admin_url('admin.php?page=dpProEventCalendar-admin&delete_calendar_events='.$calendar->id.'&noheader=true').'\'; }" />
								</td>
							</tr>'; 
						$counter++;
						$cal_output .= $dpProEventCalendar_class->output();
                    }
                    ?>
                    
                		</tbody>
                        <tfoot>
                        	<tr style="cursor:default !important;">
                        		<th><?php _e('ID','dpProEventCalendar'); ?></th>
                                <th><?php _e('Default Shortcode','dpProEventCalendar'); ?></th>
                                <th><?php _e('Title','dpProEventCalendar'); ?></th>
                                <th><?php _e('Events','dpProEventCalendar'); ?></th>
                                <th><?php _e('Actions','dpProEventCalendar'); ?></th>
                             </tr>
                        </tfoot>
                    </table>

                    <div class="clear"></div>
                    
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_import_events', this);"><?php _e('Import Events','dpProEventCalendar'); ?></h2>
                    
                    <div id="div_import_events" style="display:none;">
                        <select name="pec_id_calendar_ics" id="pec_id_calendar_ics">
                            <option value=""><?php _e('Select a Calendar','dpProEventCalendar'); ?></option>
                            <?php
                            $querystr = "
                            SELECT *
                            FROM $table_name
                            ORDER BY title ASC
                            ";
                            $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                            if(is_array($calendars_obj)) {
                                foreach($calendars_obj as $calendar) {
                            ?>
                                <option value="<?php echo $calendar->id?>"><?php echo $calendar->title?></option>
                            <?php }
                            }?>
                        </select>
                        &nbsp;&nbsp;
                        <select name="pec_category_ics" id="pec_category_ics">
                            <option value=""><?php _e('Select a Category (optional)','dpProEventCalendar'); ?></option>
                            <?php
                           $categories = get_categories('taxonomy=pec_events_category&hide_empty=0'); 
						   if(is_array($categories)) {
							  foreach ($categories as $category) {
								$option = '<option value="'.$category->term_id.'">';
								$option .= $category->cat_name;
								$option .= ' ('.$category->category_count.')';
								$option .= '</option>';
								echo $option;
							  }
						   }?>
                        </select>
                        &nbsp;&nbsp;
                        <select name="pec_offset_hour" id="pec_offset_hour">
                            <option value=""><?php _e('Hours offset (optional)','dpProEventCalendar'); ?></option>
                            <?php
                           
							for ($i = -13; $i <= 13; $i++) {
								$val = $i;
								if($i == 0) {continue;}
								if($i > 0) { $val = '+'.$i; }

								$option = '<option value="'.$val.'">';
								$option .= $val;
								$option .= '</option>';
								echo $option;
							  
						    }?>
                        </select>
                        &nbsp;&nbsp;
                        <h4><?php _e('From ICS:','dpProEventCalendar'); ?></h4>
                        <?php _e('Select the .ics file. ','dpProEventCalendar'); ?>(<?php _e('Max', 'dpProEventCalendar')?> <?php echo $upload_mb?>mb)
                        <input type="file" name="pec_ical_file" id="pec_ical_file" />
						
                        <h4><?php _e('From Facebook:','dpProEventCalendar'); ?></h4>
                        
                        <select name="pec_fb_event_option">
                        	<option value="1"><?php _e('Facebook Event URL', 'dpProEventCalendar')?></option>
                            <option value="2"><?php _e('Facebook Page URL', 'dpProEventCalendar')?></option>
                        </select>
                        &nbsp;&nbsp;
                        <input type="text" name="pec_fb_event_url" size="50" placeholder="Introduce the Facebook Event / Page URL or ID" id="pec_fb_event_url" <?php echo (empty($dpProEventCalendar['facebook_app_id'] ) ? 'disabled="disabled"' : '')?> />
                        <div class="clear"></div>
                        <?php if ( empty($dpProEventCalendar['facebook_app_id'] ) ) {?>
                            <div class="errorCustom" style="float:left;"><p><?php _e('Notice: This feature requires the FB API keys in the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-settings' ).'" target="_blank">
General settings</a>.','dpProEventCalendar'); ?></p></div>
                        <?php }?>
                        <div class="clear"></div>
                        <div class="submit">
                        
                        <input type="submit" class="button-primary" value="<?php echo __( 'Import Events', 'dpProEventCalendar' )?>" name="import_events"  />
                        
                        </div>
                        <div class="clear"></div>
                    </div>
                 </form>
                 <?php echo $cal_output?>
             	</div>
            </div> 
        </div>
        <?php } elseif(is_numeric($_GET['add']) || is_numeric($_GET['edit'])) {
		
		if(is_numeric($_GET['edit'])){
			$calendar_id = $_GET['edit'];
			$querystr = "
			SELECT *
			FROM $table_name 
			WHERE id = $calendar_id
			";
			$calendar_obj = $wpdb->get_results($querystr, OBJECT);
			$calendar_obj = $calendar_obj[0];	
			foreach($calendar_obj as $key=>$value) { $$key = $value; }
			
			$category_filter_include = explode(',', $category_filter_include);
			$venue_filter_include = explode(',', $venue_filter_include);
			$allow_user_add_event_roles = explode(',', $allow_user_add_event_roles);
			$booking_custom_fields_arr = explode(',', $booking_custom_fields);
			$form_custom_fields_arr = explode(',', $form_custom_fields);
			
		} else {
			$width_unity = '%';
			$width = 100;	
			$booking_event_color = '#e14d43';
			$show_time = 1;
			$show_preview = 1;
			$show_titles_monthly = 1;
			$new_event_email_enable = 1;
		}
		
		if($booking_email_template_user == '') {
			$booking_email_template_user = "Hi #USERNAME#,\n\nThanks for booking the event:\n\n#EVENT_DETAILS#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		}

		if($booking_cancel_email_template == '') {
			$booking_cancel_email_template = "Hi #USERNAME#,\n\nThe following booking has been canceled:\n\n#EVENT_DETAILS#\n\n#CANCEL_REASON#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		}
		
		if($booking_email_template_admin == '') {
			$booking_email_template_admin = "The user #USERNAME# (#USEREMAIL#) booked the event:\n\n#EVENT_DETAILS#\n\n#COMMENT#\n\n#SITE_NAME#";
		}
		
		if($booking_email_template_reminder_user == '') {
			$booking_email_template_reminder_user = "Hi #USERNAME#,\n\nWe would like to remind you the booking of the event:\n\n#EVENT_DETAILS#\n\nKind Regards.\n#SITE_NAME#";
		}
		
		if($new_event_email_template_published == '') {
			$new_event_email_template_published = "Hi #USERNAME#,\n\nThe event #EVENT_TITLE# has been approved.\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		}
		
		$dpProEventCalendar_class = new DpProEventCalendar( true, (is_numeric($calendar_id) ? $calendar_id : null) );
		
		$dpProEventCalendar_class->addScripts(true);
		?>
        <div id="rightSide">
        	
        	<div id="menu_general_settings">

        		<form method="post" id="dpProEventCalendar_events_meta" class="dpProEventCalendar_calendar_form" action="<?php echo admin_url('admin.php?page=dpProEventCalendar-admin&noheader=true'); ?>" onsubmit="return calendar_checkform();" enctype="multipart/form-data">

	                <div class="titleArea">
	                    <div class="wrapper">
	                        <div class="pageTitle">
	                            <h2><?php _e('Calendar','dpProEventCalendar'); ?></h2>
	                            <span><?php _e('Customize the Calendar.','dpProEventCalendar'); ?></span>

	                        </div>

	                        <p class="submit" style="float: right; margin: 0; margin-top: 66px;">

				                <input type="submit" class="button-primary" value="<?php _e('Save') ?>" />

				                <input type="button" class="button" value="<?php _e('Back') ?>" onclick="history.back();" />

				            </p>
	                        
	                        <div class="clear"></div>
	                    </div>
	                </div>
	                
	                <div class="wrapper">
	        
       		
	            <input type="hidden" name="submit" value="1" />
	            <?php if(is_numeric($id) && $id > 0) {?>
	            	<input type="hidden" name="calendar_id" value="<?php echo $id?>" />
	            <?php }?>
	            <?php settings_fields('dpProEventCalendar-group'); ?>
	            <div style="clear:both;"></div>
             	<!--end of poststuff --> 
             	
             	<div style="clear:both;"></div>

                <h2 class="subtitle accordion_title dp_ui_on" onclick="showAccordion('div_general_settings', this);">General Settings</h2>
                <div id="div_general_settings">
        
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Title','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" name="title" maxlength="80" id="dpProEventCalendar_title" class="large-text" value="<?php echo $title?>" placeholder="<?php _e('Introduce the title (80 chars max.)','dpProEventCalendar'); ?>" />
                                    <br>
                                </div>
                                <div class="desc"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Description','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" name="description" id="dpProEventCalendar_description" class="large-text" value="<?php echo $description?>" placeholder="<?php _e('Introduce the description','dpProEventCalendar'); ?>" />
                                    <br>
                                </div>
                                <div class="desc"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Preselected Date','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" readonly="readonly" maxlength="10" class="large-text"  name="default_date" id="dpProEventCalendar_default_date" value="<?php echo $default_date != '0000-00-00' ? $default_date : '' ?>" style="width:100px;" />
                                    <button type="button" class="dpProEventCalendar_btn_getDate">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ); ?>" alt="Calendar" title="Calendar">
                                    </button>
                                    <button type="button" onclick="jQuery('#dpProEventCalendar_default_date').val('');">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/clear.png' ); ?>" alt="Clear" title="Clear">
                                    </button>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the preselected date.(optional)<br />Leave blank to NOT preselect any date.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable iCal Feed','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="ical_active" id="dpProEventCalendar_ical_active" class="checkbox" <?php if($ical_active) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('On/Off the ical feed for this calendar','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('iCal Limit','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="0" max="999" name="ical_limit" id="dpProEventCalendar_ical_limit" value="<?php echo $ical_limit?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Limits the number of future events shown (0 = unlimited).','dpProEventCalendar'); ?> <br /><?php if(is_numeric($calendar_id)) { _e('iCal feed URL: ', 'dpProEventCalendar'); echo '<br>'.dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$calendar_id); }?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable RSS Feed','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="rss_active" id="dpProEventCalendar_rss_active" class="checkbox" <?php if($rss_active) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('On/Off the rss feed for this calendar','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('RSS Limit','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="0" max="999" name="rss_limit" id="dpProEventCalendar_rss_limit" value="<?php echo $rss_limit?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Limits the number of future events shown (0 = unlimited).','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                                        
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Link Events to Single Post','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="link_post" id="dpProEventCalendar_link_post" class="checkbox" <?php if($link_post) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Adds a link in the event title to the post type single page.','dpProEventCalendar'); ?></div>

                                <select name="link_post_target" id="dpProEventCalendar_link_post_target" class="large-text">
                                	<option value="_self" <?php if($link_post_target == "_self") { echo 'selected="selected"'; }?>><?php _e('Open in the same tab','dpProEventCalendar'); ?></option>
                                    <option value="_blank" <?php if($link_post_target == "_blank") { echo 'selected="selected"'; }?>><?php _e('Open in a new tab','dpProEventCalendar'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Include share buttons','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="article_share" id="dpProEventCalendar_article_share" class="checkbox" <?php if($article_share) {?> checked="checked" <?php } if ( !is_plugin_active( 'dpArticleShare/dpArticleShare.php' ) ) {?> disabled="disabled" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Adds a share bar in the events content using the Wordpress Article Social Share plugin','dpProEventCalendar'); ?></div>
                                <?php if ( !is_plugin_active( 'dpArticleShare/dpArticleShare.php' ) ) {?>
                                	<div class="errorCustom"><p><?php _e('Notice: This feature requires the <a href="http://codecanyon.net/item/wordpress-article-social-share/6247263" target="_blank">
Wordpress Article Social Share plugin</a>.','dpProEventCalendar'); ?></p></div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                   
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Date Range','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" readonly="readonly" maxlength="10" class="large-text"  name="date_range_start" id="dpProEventCalendar_date_range_start" value="<?php echo $date_range_start != '0000-00-00' ? $date_range_start : '' ?>" style="width:100px;" />
                                    <button type="button" class="dpProEventCalendar_btn_getDateRangeStart">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ); ?>" alt="Calendar" title="Calendar">
                                    </button>
                                    <button type="button" onclick="jQuery('#dpProEventCalendar_date_range_start').val('');">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/clear.png' ); ?>" alt="Clear" title="Clear">
                                    </button>
                                    
                                    &nbsp;&nbsp;to&nbsp;&nbsp;
                                    
                                    <input type="text" readonly="readonly" maxlength="10" class="large-text"  name="date_range_end" id="dpProEventCalendar_date_range_end" value="<?php echo $date_range_end != '0000-00-00' ? $date_range_end : '' ?>" style="width:100px;" />
                                    <button type="button" class="dpProEventCalendar_btn_getDateRangeEnd">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ); ?>" alt="Calendar" title="Calendar">
                                    </button>
                                    <button type="button" onclick="jQuery('#dpProEventCalendar_date_range_end').val('');">
                                        <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/clear.png' ); ?>" alt="Clear" title="Clear">
                                    </button>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('This option will limit the dates displayed in the calendar. (optional)','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Hide Old Dates','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="hide_old_dates" id="dpProEventCalendar_hide_old_dates" class="checkbox" <?php if($hide_old_dates) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Hide old dates in calendar view.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('First Day','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="first_day" id="dpProEventCalendar_first_day" class="large-text">
                                    	<option value="0" <?php if($first_day == "0") { echo 'selected="selected"'; }?>><?php _e('Sunday','dpProEventCalendar'); ?></option>
                                        <option value="1" <?php if($first_day == "1") { echo 'selected="selected"'; }?>><?php _e('Monday','dpProEventCalendar'); ?></option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the first day to display in the calendar','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Default View','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="view" id="dpProEventCalendar_view" class="large-text">
                                    	<option value="monthly" <?php if($view == "monthly") { echo 'selected="selected"'; }?>><?php _e('Calendar','dpProEventCalendar'); ?></option>
                                        <option value="monthly-all-events" <?php if($view == "monthly-all-events") { echo 'selected="selected"'; }?>><?php _e('Monthly Events List','dpProEventCalendar'); ?></option>
                                        <option value="weekly" <?php if($view == "weekly") { echo 'selected="selected"'; }?>><?php _e('Weekly','dpProEventCalendar'); ?></option>
                                        <option value="daily" <?php if($view == "daily") { echo 'selected="selected"'; }?>><?php _e('Daily','dpProEventCalendar'); ?></option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the default view.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Monthly / Weekly / Daily Buttons','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_view_buttons" class="checkbox" id="dpProEventCalendar_show_view_buttons" value="1" <?php if($show_view_buttons) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show/Hide the Monthly / Weekly / Daily Buttons.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Daily / Weekly layout','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="daily_weekly_layout" id="dpProEventCalendar_daily_weekly_layout" class="large-text">
                                    	<option value="list" <?php if($daily_weekly_layout == "list") { echo 'selected="selected"'; }?>><?php _e('List','dpProEventCalendar'); ?></option>
                                        <option value="schedule" <?php if($daily_weekly_layout == "schedule") { echo 'selected="selected"'; }?>><?php _e('Schedule','dpProEventCalendar'); ?></option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the events layout for the daily and weekly view.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox no_border">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Limit Time in daily / weekly schedule view','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" name="limit_time_start" id="dpProEventCalendar_limit_time_start" style="width: 60px;" maxlength="2" min="0" max="23" value="<?php echo $limit_time_start?>" />:00 hs /
                                    &nbsp;
                                    <input type="number" name="limit_time_end" id="dpProEventCalendar_limit_time_end" style="width: 60px;" maxlength="2" min="0" max="23" value="<?php echo $limit_time_end?>" />:00 hs
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Set a range of time to display in the daily / weekly schedule layout.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                </div>
                
                <h2 class="subtitle accordion_title" onclick="showAccordion('div_display_settings', this);"><?php _e('Display Settings','dpProEventCalendar'); ?></h2>
                
                <div id="div_display_settings" style="display: none;">
                	<div class="option option-select">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Skin','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="skin" id="dpProEventCalendar_skin" class="large-text">
                                    	<option value="light" <?php if($skin == 'light') { echo 'selected="selected"'; }?>><?php _e('Light','dpProEventCalendar'); ?></option>
                                        <option value="dark" <?php if($skin == 'dark') { echo 'selected="selected"'; }?>><?php _e('Dark','dpProEventCalendar'); ?></option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the skin theme','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Time','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_time" class="checkbox" id="dpProEventCalendar_show_time" value="1" <?php if($show_time) {?>checked="checked" <?php }?> onclick="toggleFormat();" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show/Hide the events time.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div id="div_time_extended" style="display:none;">
	                    <div class="option option-checkbox">
	                        <div class="option-inner">
	                            <label class="titledesc"><?php _e('Show Timezone','dpProEventCalendar'); ?></label>
	                            <div class="formcontainer">
	                                <div class="forminp">
	                                    <input type="checkbox" name="show_timezone" id="dpProEventCalendar_show_timezone" class="checkbox" <?php if($show_timezone) {?> checked="checked" <?php }?> value="1" />
	                                    <br>
	                                </div>
	                                <div class="desc"><?php _e('Display Timezone in the frontend.','dpProEventCalendar'); ?></div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="clear"></div>
					</div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Search','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_search" class="checkbox" id="dpProEventCalendar_show_search" value="1" <?php if($show_search) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show/Hide the search input.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Category Filter','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_category_filter" class="checkbox" id="dpProEventCalendar_show_category_filter" value="1" <?php if($show_category_filter) {?>checked="checked" <?php }?>  onclick="toggleFormatCategories();" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show/Hide the categories dropdown.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox" id="div_category_filter" style="display:none;">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Categories to display','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="category_filter_include[]" id="dpProEventCalendar_category_filter_include" multiple="multiple">
                                    	<option value="" <?php if(empty($category_filter_include)) {?>selected="selected"<?php }?>><?php _e('All','dpProEventCalendar'); ?></option>
										<?php 
                                          $categories = get_categories('taxonomy=pec_events_category&hide_empty=0'); 
										  if(!is_array($category_filter_include)) {
											$category_filter_include = array();  
										  }
                                          foreach ($categories as $category) {
                                            $option = '<option value="'.$category->term_id.'" ';
											if(in_array($category->term_id, $category_filter_include)) {
												$option .= 'selected="selected"';
											}
											$option .= '>';
                                            $option .= $category->cat_name;
                                            $option .= '</option>';
                                            echo $option;
                                          }
                                         ?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select specific categories to display. To select multiple categories, keep pressing ctrl.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Location / Venue Filter','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_location_filter" class="checkbox" id="dpProEventCalendar_show_location_filter" value="1" <?php if($show_location_filter) {?>checked="checked" <?php }?>  onclick="toggleFormatVenues();" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show/Hide the locations dropdown.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox" id="div_venue_filter" style="display:none;">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Venues to display','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="venue_filter_include[]" id="dpProEventCalendar_venue_filter_include" multiple="multiple">
                                    	<option value="" <?php if(empty($venue_filter_include)) {?>selected="selected"<?php }?>><?php _e('All','dpProEventCalendar'); ?></option>
										<?php 
										$args = array(
										'posts_per_page'   => -1,
										'post_type'        => 'pec-venues',
										'post_status'      => 'publish',
										'order'			   => 'ASC', 
										'orderby' 		   => 'title' 
										);

										$venues_list = get_posts($args);
										if(!is_array($venue_filter_include)) {
											$venue_filter_include = array();  
										}

										$option = "";
										foreach($venues_list as $venue) {
										
							            
							            	$option .= '<option value="'.$venue->ID.'"';
							            	if(in_array($venue->ID, $venue_filter_include)) {
												$option .= ' selected="selected"';
											}
											
											$option .= ' >'.$venue->post_title.'</option>';
							            
							            }
							            echo $option;?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select specific venues to display. To select multiple venues, keep pressing ctrl key.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show X in dates with events?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_x" class="checkbox" id="dpProEventCalendar_show_x" value="1" <?php if($show_x) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show an "X" instead of the number of events in a date.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Events Title?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_titles_monthly" class="checkbox" id="dpProEventCalendar_show_titles_monthly" value="1" <?php if($show_titles_monthly) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Display the event titles in the calendar layout','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show Events Preview?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_preview" class="checkbox" id="dpProEventCalendar_show_preview" value="1" <?php if($show_preview) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Display a list of event in a day on mouse over','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show References Button?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_references" class="checkbox" id="dpProEventCalendar_show_references" value="1" <?php if($show_references) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Display the references button','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Display the Event Author','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="show_author" class="checkbox" id="dpProEventCalendar_show_author" value="1" <?php if($show_author) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Display the event author','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Current Date Color','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <div id="currentDate_colorSelector" class="colorSelector"><div style="background-color: <?php echo $current_date_color?>"></div></div>
                                    <input type="hidden" name="current_date_color" id="dpProEventCalendar_current_date_color" value="<?php echo $current_date_color?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Set the Current date color.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select no_border">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Width','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" name="width" id="dpProEventCalendar_width" maxlength="4" style="width:50px; float: left;" class="large-text" value="<?php echo $width?>" /> 
                                    <select name="width_unity" id="dpProEventCalendar_width_unity" style="width:60px;" class="large-text">
                                        <option value="px" <?php if($width_unity == 'px') {?> selected="selected" <?php }?>>px (<?php _e('pixels','dpProEventCalendar'); ?>)</option>
                                        <option value="%" <?php if($width_unity == '%') {?> selected="selected" <?php }?>>% (<?php _e('percentage','dpProEventCalendar'); ?>)</option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc" style="width: 400px;"><?php _e('Set the width of the calendar','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <h2 class="subtitle accordion_title" onclick="showAccordion('div_user_events', this);"><?php _e('User\'s Events','dpProEventCalendar'); ?></h2>

                <div id="div_user_events" style="display: none;">
                	<div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Allow users to add events?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="allow_user_add_event" onclick="toggleNewEventRoles();" class="checkbox" id="dpProEventCalendar_allow_user_add_event" value="1" <?php if($allow_user_add_event) {?>checked="checked" <?php }?> />
                                    <br>
                                    <?php if(!is_array($allow_user_add_event_roles)) { $allow_user_add_event_roles = array(); }?>
                                    <select name='allow_user_add_event_roles[]' id="allow_user_add_event_roles" multiple="multiple" class="multiple">
                                    	<option value="all" <?php if(empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "" || in_array('all', $allow_user_add_event_roles)) { echo 'selected="selected"'; }?>><?php _e('All','dpProEventCalendar'); ?></option>
                                       <?php 
									   $user_roles = '';
                                       $editable_roles = get_editable_roles();

								       foreach ( $editable_roles as $role => $details ) {
								           $name = translate_user_role($details['name'] );
										   
										   if ( in_array($role, $allow_user_add_event_roles) ) // preselect specified role
								               $user_roles .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
								           else
								               $user_roles .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
								       }
									   echo $user_roles;
									   ?>
                                    </select>
                                </div>
                                <div class="desc"><?php _e('Adds the possibility for registered users to add events. Select multiple user roles using the ctrl key.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Assign new events from a non-logged in user to an admin','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="assign_events_admin" id="dpProEventCalendar_assign_events_admin">
                                    	<option value="" <?php if(empty($assign_events_admin)) {?>selected="selected"<?php }?>><?php _e('None','dpProEventCalendar'); ?></option>
										<?php 
                                          $users = get_users('role=administrator'); 
                                          foreach ($users as $user) {
                                            $option = '<option value="'.$user->ID.'" ';
											if($user->ID == $assign_events_admin) {
												$option .= 'selected="selected"';
											}
											$option .= '>';
                                            $option .= $user->display_name;
                                            $option .= '</option>';
                                            echo $option;
                                          }
                                         ?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('This will allow non-logged in users to submit new events.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Allow users to edit their events?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="allow_user_edit_event" class="checkbox" id="dpProEventCalendar_allow_user_edit_event" value="1" <?php if($allow_user_edit_event) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Adds the possibility for logged in users to edit their events.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Allow users to remove their events?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="allow_user_remove_event" class="checkbox" id="dpProEventCalendar_allow_user_remove_event" value="1" <?php if($allow_user_remove_event) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Adds the possibility for logged in users to remove their events.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Publish automatically?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="publish_new_event" class="checkbox" id="dpProEventCalendar_publish_new_event" value="1" <?php if($publish_new_event) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Publish events submitted by users automatically?','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email that will receive the user after publishing the event','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                	<input type="checkbox" name="new_event_email_enable" class="checkbox" value="1" <?php if($new_event_email_enable) {?>checked="checked" <?php }?> /> <?php _e('Enable?','dpProEventCalendar'); ?>
                                	<br>
                                    <textarea cols="20" rows="5" name='new_event_email_template_published'><?php echo $new_event_email_template_published?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Template of the email that will receive the user. Use the reserved tags to display dynamic data.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Send Email to Admin when a user submits a new event','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="email_admin_new_event" class="checkbox" id="dpProEventCalendar_email_admin_new_event" value="1" <?php if($email_admin_new_event) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Email will be sent to ('.get_bloginfo('admin_email').')','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable Text Editor','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="form_text_editor" class="checkbox" id="dpProEventCalendar_form_text_editor" value="1" <?php if($form_text_editor) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Enables the text editor in the frontend form.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox no_border">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Form Customization','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="form_show_end_date" class="checkbox" id="dpProEventCalendar_form_show_end_date" value="1" <?php if($form_show_end_date) {?>checked="checked" <?php }?> /> <?php _e('Show End date','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_start_time" class="checkbox" id="dpProEventCalendar_form_show_start_time" value="1" <?php if($form_show_start_time) {?>checked="checked" <?php }?> /> <?php _e('Show Start Time','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_end_time" class="checkbox" id="dpProEventCalendar_form_show_end_time" value="1" <?php if($form_show_end_time) {?>checked="checked" <?php }?> /> <?php _e('Show End Time','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_extra_dates" class="checkbox" id="dpProEventCalendar_form_show_extra_dates" value="1" <?php if($form_show_extra_dates) {?>checked="checked" <?php }?> /> <?php _e('Show Extra Dates','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_description" class="checkbox" id="dpProEventCalendar_form_show_description" value="1" <?php if($form_show_description) {?>checked="checked" <?php }?> /> <?php _e('Show Event Decription','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_category" class="checkbox" id="dpProEventCalendar_form_show_category" value="1" <?php if($form_show_category) {?>checked="checked" <?php }?> /> <?php _e('Show Category Dropdown','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_hide_time" class="checkbox" id="dpProEventCalendar_form_show_hide_time" value="1" <?php if($form_show_hide_time) {?>checked="checked" <?php }?> /> <?php _e('Show \'Hide Time\' option','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_frequency" class="checkbox" id="dpProEventCalendar_form_show_frequency" value="1" <?php if($form_show_frequency) {?>checked="checked" <?php }?> /> <?php _e('Show Frequency','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_all_day" class="checkbox" id="dpProEventCalendar_form_show_all_day" value="1" <?php if($form_show_all_day) {?>checked="checked" <?php }?> /> <?php _e('Show All Day option','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_image" class="checkbox" id="dpProEventCalendar_form_show_image" value="1" <?php if($form_show_image) {?>checked="checked" <?php }?> /> <?php _e('Allow to upload an image','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_link" class="checkbox" id="dpProEventCalendar_form_show_link" value="1" <?php if($form_show_link) {?>checked="checked" <?php }?> /> <?php _e('Show Link field','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_location" class="checkbox" id="dpProEventCalendar_form_show_location" value="1" <?php if($form_show_location) {?>checked="checked" <?php }?> /> <?php _e('Show Location / Venue dropdown','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_location_options" class="checkbox" id="dpProEventCalendar_form_show_location_options" value="1" <?php if($form_show_location_options) {?>checked="checked" <?php }?> /> <?php _e('Allow users to add new locations / venues','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_phone" class="checkbox" id="dpProEventCalendar_form_show_phone" value="1" <?php if($form_show_phone) {?>checked="checked" <?php }?> /> <?php _e('Show Phone option','dpProEventCalendar'); ?>
                                    <br>
                                    <!--<input type="checkbox" name="form_show_map" class="checkbox" id="dpProEventCalendar_form_show_map" value="1" <?php if($form_show_map) {?>checked="checked" <?php }?> /> <?php _e('Show Map option','dpProEventCalendar'); ?>
                                    <br>-->
                                    <input type="checkbox" name="form_show_color" class="checkbox" id="dpProEventCalendar_form_show_color" value="1" <?php if($form_show_color) {?>checked="checked" <?php }?> /> <?php _e('Show \'Color\' option','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_timezone" class="checkbox" id="dpProEventCalendar_form_show_timezone" value="1" <?php if($form_show_timezone) {?>checked="checked" <?php }?> /> <?php _e('Show Timezone','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_booking_enable" class="checkbox" id="dpProEventCalendar_form_show_booking_enable" value="1" <?php if($form_show_booking_enable) {?>checked="checked" <?php }?> /> <?php _e('Show Booking Enable checkbox','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_booking_limit" class="checkbox" id="dpProEventCalendar_form_show_booking_limit" value="1" <?php if($form_show_booking_limit) {?>checked="checked" <?php }?> /> <?php _e('Show Booking Limit','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_booking_price" class="checkbox" id="dpProEventCalendar_form_show_booking_price" value="1" <?php if($form_show_booking_price) {?>checked="checked" <?php }?> /> <?php _e('Show Booking Price','dpProEventCalendar'); ?>
                                    <br>
                                    <input type="checkbox" name="form_show_booking_block_hours" class="checkbox" id="dpProEventCalendar_form_show_booking_block_hours" value="1" <?php if($form_show_booking_block_hours) {?>checked="checked" <?php }?> /> <?php _e('Show Booking Block Hours','dpProEventCalendar'); ?>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Customize the frontend form','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Custom Fields','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name='form_custom_fields[]' id="form_custom_fields" multiple="multiple" class="multiple">
                                    	<option value="all" <?php if(empty($form_custom_fields) || $form_custom_fields == "" || in_array('all', $form_custom_fields_arr)) { echo 'selected="selected"'; }?>><?php _e('All','dpProEventCalendar'); ?></option>
                                       <?php 
                                       $custom_fields_option = "";
                                       print_r($form_custom_fields_arr);
									   if(is_array($dpProEventCalendar['custom_fields_counter'])) {
		    								$counter = 0;
		    								foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
									           $name = $dpProEventCalendar['custom_fields']['name'][$counter];
									           $field_id = $dpProEventCalendar['custom_fields']['id'][$counter];
											   
											   if ( in_array($field_id, $form_custom_fields_arr) ) // preselect specified role
									               $custom_fields_option .= "\n\t<option selected='selected' value='" . esc_attr($field_id) . "'>$name</option>";
									           else
									               $custom_fields_option .= "\n\t<option value='" . esc_attr($field_id) . "'>$name</option>";

									           $counter++;
									       }
								       }
									   echo $custom_fields_option;
									   ?>
                                    </select>
                                </div>
                                <div class="desc"><?php _e('Select multiple custom fields using the ctrl key.','dpProEventCalendar'); ?></div>
                                <br>
                                    <p><?php _e('Add custom fields from the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-settings' ).'" target="_blank">
General settings</a>.','dpProEventCalendar'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                
                </div>
                
                <h2 class="subtitle accordion_title" onclick="showAccordion('div_booking', this);"><?php _e('Booking','dpProEventCalendar'); ?></h2>

                <div id="div_booking" style="display: none;">
                
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable booking for all the events','dpProEventCalendar'); ?>
                            	<span class="pec_info dashicons dashicons-info"><span><?php _e('If you only want bookings in specific events, uncheck this option and use the event\'s booking setting instead.','dpProEventCalendar'); ?></span></span>
                            </label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_enable" class="checkbox" id="dpProEventCalendar_booking_enable" value="1" <?php if($booking_enable) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('A "Book Event" button will be displayed.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Allow not logged in users to book an event','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_non_logged" class="checkbox" id="dpProEventCalendar_booking_non_logged" value="1" <?php if($booking_non_logged) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('A Full name and email field will be required in the booking form.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Allow logged in users to cancel a booking','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_cancel" class="checkbox" id="dpProEventCalendar_booking_cancel" value="1" <?php if($booking_cancel) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Logged in users will see a button to cancel a booking.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Display attendees counter?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_display_attendees" class="checkbox" id="dpProEventCalendar_booking_display_attendees" value="1" <?php if($booking_display_attendees) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('An attendees counter will be displayed in the frontend.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Display attendees names?','dpProEventCalendar'); ?>
                            	<span class="pec_info dashicons dashicons-info"><span><?php _e('The list of attendees will be displayed on mouse hover over the attendees counter.','dpProEventCalendar'); ?></span></span>
                            </label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_display_attendees_names" class="checkbox" id="dpProEventCalendar_booking_display_attendees_names" value="1" <?php if($booking_display_attendees_names) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('A list of attendees will be displayed in the frontend.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Display Fully Booked label?','dpProEventCalendar'); ?>
                            </label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_display_fully_booked" class="checkbox" id="dpProEventCalendar_booking_display_fully_booked" value="1" <?php if($booking_display_fully_booked) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('A fully Booked label will be displayed in some layouts.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Display Phone field?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_show_phone" class="checkbox" id="dpProEventCalendar_booking_show_phone" value="1" <?php if($booking_show_phone) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Display a phone field for users in the booking form.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable comment option in booking form','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_comment" class="checkbox" id="dpProEventCalendar_booking_comment" value="1" <?php if($booking_comment) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Enables a comment text field.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Custom Fields','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name='booking_custom_fields[]' id="booking_custom_fields" multiple="multiple" class="multiple">
                                    	<option value="all" <?php if(empty($booking_custom_fields) || $booking_custom_fields == "" || in_array('all', $booking_custom_fields_arr)) { echo 'selected="selected"'; }?>><?php _e('All','dpProEventCalendar'); ?></option>
                                       <?php 
                                       $custom_fields_option = "";
									   if(is_array($dpProEventCalendar['booking_custom_fields_counter'])) {
		    								$counter = 0;
		    								foreach($dpProEventCalendar['booking_custom_fields_counter'] as $key) {
									           $name = $dpProEventCalendar['booking_custom_fields']['name'][$counter];
									           $field_id = $dpProEventCalendar['booking_custom_fields']['id'][$counter];
											   
											   if ( in_array($field_id, $booking_custom_fields_arr) ) // preselect specified role
									               $custom_fields_option .= "\n\t<option selected='selected' value='" . esc_attr($field_id) . "'>$name</option>";
									           else
									               $custom_fields_option .= "\n\t<option value='" . esc_attr($field_id) . "'>$name</option>";

									           $counter++;
									       }
								       }
									   echo $custom_fields_option;
									   ?>
                                    </select>
                                </div>
                                <div class="desc"><?php _e('Select multiple custom fields using the ctrl key.','dpProEventCalendar'); ?></div>
                                <br>
                                    <p><?php _e('Add custom fields from the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-settings' ).'" target="_blank">
General settings</a>.','dpProEventCalendar'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Show remaining tickets?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="booking_show_remaining" class="checkbox" id="dpProEventCalendar_booking_show_remaining" value="1" <?php if($booking_show_remaining) {?>checked="checked" <?php }?> />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Show the number of remaining tickets in the booking form.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Max number of bookings by user per event','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="1" max="99" name="booking_max_quantity" id="dpProEventCalendar_booking_max_quantity" value="<?php echo $booking_max_quantity?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('The max quantity of bookings by user in a single event / date.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Max number of upcoming dates','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="1" max="99" name="booking_max_upcoming_dates" id="dpProEventCalendar_booking_max_upcoming_dates" value="<?php echo $booking_max_upcoming_dates?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('The max number of upcoming dates on each event\'s date dropdown.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email that will receive the user after booking','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <textarea cols="20" rows="5" name='booking_email_template_user'><?php echo $booking_email_template_user?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Template of the email that will receive the user. Use the reserved tags to display dynamic data. (#USERNAME#, #USEREMAIL, #USERPHONE#, #COMMENT#, #EVENT_DETAILS#, #SITE_NAME#','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email that will receive the user if the booking is canceled','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                	<input type="checkbox" name="booking_cancel_email_enable" class="checkbox" value="1" <?php if($booking_cancel_email_enable) {?>checked="checked" <?php }?> /> <?php _e('Enable?','dpProEventCalendar'); ?>
                                	<br>
                                    <textarea cols="20" rows="5" name='booking_cancel_email_template'><?php echo $booking_cancel_email_template?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Template of the email that will receive the user. Use the reserved tags to display dynamic data. (#USERNAME#, #USEREMAIL, #USERPHONE#, #COMMENT#, #EVENT_DETAILS#, #CANCEL_REASON#, #SITE_NAME#','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email that will receive the event author when a user purchases a booking','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <textarea cols="20" rows="5" name='booking_email_template_admin'><?php echo $booking_email_template_admin?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Template of the email that will receive the event author. Use the reserved tags to display dynamic data. (#USERNAME#, #USEREMAIL, #USERPHONE#, #COMMENT#, #EVENT_DETAILS#, #SITE_NAME#)','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email that will receive the user as a reminder before the event','dpProEventCalendar'); ?>
                            	<span class="pec_info dashicons dashicons-info"><span><?php _e('Reminders are sent 3 days before the event starts.','dpProEventCalendar'); ?></span></span>
                            </label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <textarea cols="20" rows="5" name='booking_email_template_reminder_user'><?php echo $booking_email_template_reminder_user?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Template of the email reminder that will receive the user before the event. Use the reserved tags to display dynamic data. (#USERNAME#, #USEREMAIL, #COMMENT#, #EVENT_DETAILS#, #SITE_NAME#','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <!--
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Booked Event Color','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <div id="bookedEvent_colorSelector" class="colorSelector"><div style="background-color: <?php echo $booking_event_color?>"></div></div>
                                    <input type="hidden" name="booking_event_color" id="dpProEventCalendar_booking_event_color" value="<?php echo $booking_event_color?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Set the booked event color.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                	-->
                </div>
                
                <h2 class="subtitle accordion_title" onclick="showAccordion('div_translations', this);"><?php _e('Translations / Multi Language','dpProEventCalendar'); ?></h2>
                
                <div id="div_translations" style="display: none;">
                	
                    <div id="div_translations_ml">
                    	<div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Enable Multi language / use .PO file','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" name="enable_wpml" id="dpProEventCalendar_enable_wpml" onclick="toggleTranslations();" class="checkbox" value="1" <?php if($enable_wpml) {?>checked="checked" <?php }?> />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Use PO files in the /languages/ folder to translate the plugin texts.','dpProEventCalendar'); ?></div>
                                    
                                	<div class="errorCustom"><p><?php _e('Notice: The Multi language feature requires the <a href="https://wpml.org/?aid=86607&affiliate_key=pCq9y9jsvJMt" target="_blank">
Wordpress Multi Language plugin</a>.','dpProEventCalendar'); ?></p></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <div id="div_translations_fields">
                
                		<?php foreach($dpProEventCalendar_class->translation as $key => $value) { ?>
                		
                		<?php if(is_array($dpProEventCalendar_class->translation[$key])) {
                			foreach($dpProEventCalendar_class->translation[$key] as $key2 => $value2) {
                				?>
                				<div class="option option-select">
		                            <div class="option-inner">
		                                <label class="titledesc"><?php echo $value2 ?></label>
		                                <div class="formcontainer">
		                                    <div class="forminp">
		                                        <input type="text" name="translation_fields[<?php echo strtolower($key)?>][<?php echo strtolower($key2)?>]" class="large-text" value="<?php echo htmlentities($dpProEventCalendar_class->translation[$key][$key2])?>" />
		                                        <br>
		                                    </div>
		                                    <div class="desc"></div>
		                                </div>
		                            </div>
		                        </div>
		                        <div class="clear"></div>
                				<?php
                			}
                			continue;
                		}
                		?>
                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php echo $dpProEventCalendar_class->translation_orig[$key] ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" name="translation_fields[<?php echo strtolower($key)?>]" class="large-text" value="<?php echo htmlentities($dpProEventCalendar_class->translation[$key])?>" />
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <?php }?>
                        
	               	</div>
               </div>
               
               <h2 class="subtitle accordion_title" onclick="showAccordion('div_sync', this);"><?php _e('Sync iCal Feed / Facebook Events','dpProEventCalendar'); ?></h2>
                
                <div id="div_sync" style="display: none;">
                
                	<div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable Sync','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="sync_ical_enable" id="dpProEventCalendar_sync_ical_enable" class="checkbox" <?php if($sync_ical_enable) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('On/Off the sync feature for this calendar.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('iCal URL','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" name="sync_ical_url" id="dpProEventCalendar_sync_ical_url" class="large-text" value="<?php echo $sync_ical_url?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('The events will be synced with this iCal Feed. For multiple feeds, separate them with commas.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Facebook Page','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" name="sync_fb_page" id="dpProEventCalendar_sync_fb_page" class="large-text" value="<?php echo $sync_fb_page?>" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Introduce the Facebook Page ID or URL. For multiple Facebook pages, separate them with commas.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                	
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Category','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="sync_ical_category" id="dpProEventCalendar_sync_ical_category">
			                            <option value="0"><?php _e('None','dpProEventCalendar'); ?></option>
			                            <?php
			                           $categories = get_categories('taxonomy=pec_events_category&hide_empty=0'); 
									   if(is_array($categories)) {
										  foreach ($categories as $category) {
											$option = '<option value="'.$category->term_id.'" '.($sync_ical_category == $category->term_id ? 'selected="selected"' : '').'>';
											$option .= $category->cat_name;
											$option .= ' ('.$category->category_count.')';
											$option .= '</option>';
											echo $option;
										  }
									   }?>
			                        </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select a category to import events (optional)','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                     
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Sync Frequency','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="sync_ical_frequency" id="dpProEventCalendar_sync_ical_frequency">
                                        <option value="hourly" <?php if($sync_ical_frequency == 'hourly') { ?> selected="selected" <?php }?>><?php _e('Hourly','dpProEventCalendar'); ?></option>
                                        <option value="twicedaily" <?php if($sync_ical_frequency == 'twicedaily') { ?> selected="selected" <?php }?>><?php _e('Twice Daily','dpProEventCalendar'); ?></option>
                                        <option value="daily" <?php if($sync_ical_frequency == 'daily') { ?> selected="selected" <?php }?>><?php _e('Daily','dpProEventCalendar'); ?></option>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the sync recurrence','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                
                </div>
                
                <h2 class="subtitle accordion_title" onclick="showAccordion('div_cache', this);"><?php _e('Cache','dpProEventCalendar'); ?></h2>
                
                <div id="div_cache" style="display: none;">
                    <div class="option option-checkbox">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable Cache','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="cache_active" id="dpProEventCalendar_cache_active" class="checkbox" <?php if($cache_active) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('On/Off the cache feature for this calendar. The cache will be cleared every time you edit the calendar settings and when you add / edit an event.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
               
               <?php if(is_numeric($_GET['edit'])) {?>
               <h2 class="subtitle accordion_title" onclick="showAccordion('div_subscribers', this);"><?php _e('Subscribers','dpProEventCalendar'); ?></h2>
                
                <div id="div_subscribers" style="display: none;">
                    
                    <div class="option option-checkbox no_border">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Enable Subscribe Button','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" name="subscribe_active" id="dpProEventCalendar_subscribe_active" class="checkbox" <?php if($subscribe_active) {?>checked="checked" <?php }?> value="1" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('On/Off the "subscribe" button for this calendar','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>            

	                <h2><?php _e('List of Subscribers', 'dpProEventCalendar'); ?> 
	                    <a class="button" style="float: right;" href="<?php echo dpProEventCalendar_plugin_url('includes/export_subscribers.php?calendar_id='.$_GET['edit'])?>"><?php _e('Export to CSV', 'dpProEventCalendar'); ?></a>
                    </h2>

                    <table class="widefat" cellpadding="0" cellspacing="0" id="sort-table">
                    	<thead>
                    		<tr style="cursor:default !important;">
                            	<th><?php _e('Name','dpProEventCalendar'); ?></th>
                                <th><?php _e('Email','dpProEventCalendar'); ?></th>
                                <th><?php _e('Subscription Date','dpProEventCalendar'); ?></th>
                                <th><?php _e('Actions','dpProEventCalendar'); ?></th>
                             </tr>
                        </thead>
                        <tbody>
                        	<?php
                            $querystr = "
                            SELECT *
                            FROM $table_name_subscribers_calendar 
                            WHERE calendar = %d
                            ORDER BY subscription_date ASC
                            ";
                            $subscribers_obj = $wpdb->get_results($wpdb->prepare($querystr, $_GET['edit']), OBJECT);
                            foreach($subscribers_obj as $subscriber) {
								?>
                    		<tr>
                            	<td><?php echo $subscriber->name?></td>
                                <td><?php echo $subscriber->email?></td>
                                <td><?php echo $subscriber->subscription_date?></td>
                                <td><input type="button" value="<?php echo __( 'Delete', 'dpProEventCalendar' )?>" name="delete_calendar" class="button-secondary" onclick="if(confirm('<?php echo __( 'Are you sure that you want to remove this subscriber?', 'dpProEventCalendar' )?>')) { location.href='<?php echo admin_url('admin.php?page=dpProEventCalendar-admin&edit='.$_GET['edit'].'&delete_subscriber='.$subscriber->id.'&noheader=true')?>'; }" /></td>
                            </tr>
                            <?php }?>
                		</tbody>
                        <tfoot>
                        	<tr style="cursor:default !important;">
                            	<th><?php _e('Name','dpProEventCalendar'); ?></th>
                                <th><?php _e('Email','dpProEventCalendar'); ?></th>
                                <th><?php _e('Subscription Date','dpProEventCalendar'); ?></th>
                                <th><?php _e('Actions','dpProEventCalendar'); ?></th>
                             </tr>
                        </tfoot>
                    </table>

                    <h2 class="dp_subsection"><?php _e('MailChimp Subscription','dpProEventCalendar'); ?></h2>
                    
                    <div class="option option-select option_w no_border">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('API Key','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type='text' name='mailchimp_api' id="mailchimp_api_key" value="<?php echo $mailchimp_api?>" placeholder="<?php _e('Introduce your MailChimp API key.','dpProEventCalendar'); ?>" />&nbsp;&nbsp;
                                    <input type="button" onclick="MailChimp_getList(); return false;" style="width: auto;padding: 0 10px;margin: 0 !important;height: 34px;" class="button" value="<?php _e('Get Lists') ?>" />
                                    <br>
                                </div>
                                <div class="desc"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w no_border" id="div_mailchimp_list" style="display: <?php echo ($mailchimp_api != "") ? 'block' : 'none'?>;">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('List:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp" id="mailchimp_list">
                                    <select name='mailchimp_list'>
                                        <?php 
                                        if($mailchimp_api != "") {
                                            $return = '';
											//$mailchimp_class = new mailchimpSF_MCAPI($_POST['mailchimp_api']);
																					
											//$retval = $mailchimp_class->lists();
											
											// Query String Perameters are here
											// for more reference please vizit http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
											$data = array(
												'fields' => 'lists' // total_items, _links
											);
											 
											$url = 'https://' . substr($mailchimp_api,strpos($mailchimp_api,'-')+1) . '.api.mailchimp.com/3.0/lists/';
											
											$result = json_decode( dpProEventCalendar_mailchimp_curl_connect( $url, 'GET', $mailchimp_api, $data) );
											
											if( !empty($result->lists) ) {
												foreach( $result->lists as $list ){
											
													?><option value="<?php echo $list->id;?>" <?php if( $list->id == $mailchimp_list ) {?>selected="selected"<?php }?>><?php echo $list->name?></option><?php
											
												}	
											}
                                        }
                                        ?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select a list to add the new suscribers.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                	</div>
                    <div class="clear"></div>
                    
                    <div class="clear"></div>
                </div>
                <?php }?>
               
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
                        <input type="button" class="button" value="<?php _e('Back') ?>" onclick="history.back();" />
                    </p>
                </div>
                <script type="text/javascript">
					toggleFormat();
					toggleTranslations();
					toggleNewEventRoles();
					toggleFormatVenues();
					toggleFormatCategories();
				</script>
            </form>
        </div>
    </div>
        <?php $dpProEventCalendar_class->output(true);?>
        <?php }?>
	 <!--end of poststuff --> 
	
	
	</div> <!--end of float wrap -->
    <div class="clear"></div>
	

	<?php	
}
?>