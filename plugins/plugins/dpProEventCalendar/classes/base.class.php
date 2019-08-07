<?php
/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Base Class
 */

require_once('dates.class.php');

class DpProEventCalendar {
	
	var $nonce;
	var $is_admin = false;
	var $type = 'calendar';
	var $limit = 0;
	var $limit_description = 0;
	var $category = "";
	var $event_id = "";
	var $event = "";
	var $author = "";
	var $columns = 3;
	var $from = "";
	var $view = "";
	var $id_calendar = null;
	var $default_date = null;
	var $calendar_obj;
	var $wpdb = null;
	var $eventsByCurrDate = null;
	var $opts = array();
	var $hidden_editor_added = false;
	var $loaded_event = array();
	var $datesObj;
	var $widget;
	var $time_format;
	
	var $translation;
	var $translation_orig;
	
	
	var $table_calendar;
	var $table_subscribers_calendar;
	
	function __construct( 
		$is_admin = false, 
		$id_calendar = null, 
		$defaultDate = null, 
		$translation = null, 
		$widget = '', 
		$category = '', 
		$event_id = '', 
		$author = '', 
		$event = "", 
		$columns = "", 
		$from = "", 
		$view = "", 
		$limit_description = 0, 
		$opts = array() 
		) 
	{
		global $table_prefix;
		
		$this->table_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		$this->table_events = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		$this->table_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
		$this->table_special_dates = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
		$this->table_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
		$this->table_subscribers_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;

		$this->translation = array( 
			'TXT_NO_EVENTS_FOUND' 		=> __('No Events were found.','dpProEventCalendar'),
			'TXT_ALL_DAY' 				=> __('All Day','dpProEventCalendar'),
			'TXT_ALL_EVENT_DATES' 		=> __('All Event Dates','dpProEventCalendar'),
			'TXT_COLOR_CODE' 			=> __('Color Code','dpProEventCalendar'),
			'TXT_LIST_VIEW'				=> __('List View','dpProEventCalendar'),
			'TXT_CALENDAR_VIEW'			=> __('Calendar View','dpProEventCalendar'),
			'TXT_ALL_CATEGORIES'		=> __('All Categories','dpProEventCalendar'),
			'TXT_ALL_LOCATIONS'			=> __('All Locations','dpProEventCalendar'),
			'TXT_MONTHLY'				=> __('Monthly','dpProEventCalendar'),
			'TXT_DAILY'					=> __('Daily','dpProEventCalendar'),
			'TXT_WEEKLY'				=> __('Weekly','dpProEventCalendar'),
			'TXT_ALL_WORKING_DAYS'		=> __('All working days','dpProEventCalendar'),
			'TXT_SEARCH' 				=> __('Search...','dpProEventCalendar'),
			'TXT_RESULTS_FOR' 			=> __('Results: ','dpProEventCalendar'),
			'TXT_VISIT_WEBSITE'			=> __('Visit Website', 'dpProEventCalendar'),
			'TXT_TO_BE_CONFIRMED'		=> __('To Be Confirmed','dpProEventCalendar'),
			'TXT_FULLY_BOOKED'			=> __('Fully Booked','dpProEventCalendar'),
			'TXT_AGE_RANGE'				=> __('Age Range', 'dpProEventCalendar'),
			'TXT_MORE_DATES'			=> __('More Dates','dpProEventCalendar'),
			'TXT_STARTS_IN'				=> __('Starts in','dpProEventCalendar'),
			'TXT_ADD_TO_PERSONAL_CALENDAR'=> __('Add to personal calendar','dpProEventCalendar'),
			'TXT_ORGANIZED_BY' 			=> __('Organized By','dpProEventCalendar'),
			'TXT_BY' 					=> __('By','dpProEventCalendar'),
			'TXT_VISIT_FB_EVENT'		=> __('Visit Facebook Event','dpProEventCalendar'),
			'TXT_YEAR' 					=> __('Year','dpProEventCalendar'),
			'TXT_YEARS'					=> __('Years','dpProEventCalendar'),
			'TXT_MONTH' 				=> __('Month','dpProEventCalendar'),
			'TXT_MONTHS' 				=> __('Months','dpProEventCalendar'),
			'TXT_PRINT'					=> __('Print','dpProEventCalendar'),
			'TXT_DAYS' 					=> __('Days','dpProEventCalendar'),
			'TXT_DAY' 					=> __('Day','dpProEventCalendar'),
			'TXT_HOURS' 				=> __('Hours','dpProEventCalendar'),
			'TXT_HOUR' 					=> __('Hour','dpProEventCalendar'),
			'TXT_MINUTES' 				=> __('Minutes','dpProEventCalendar'),
			'TXT_MINUTE' 				=> __('Minute','dpProEventCalendar'),
			'TXT_SECONDS' 				=> __('Seconds','dpProEventCalendar'),
			'TXT_FEATURED' 				=> __('Featured','dpProEventCalendar'),
			'TXT_CURRENT_DATE'			=> __('Current Date','dpProEventCalendar'),
			'TXT_SELECT_TIMEZONE'		=> __('Select Timezone', 'dpProEventCalendar'),
			'TXT_BOOK_EVENT'			=> __('Book Event','dpProEventCalendar'),
			'TXT_BOOKED'				=> __('Booked','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVE'		=> __('Remove Booking','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SAVED'		=> __('Booking saved successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVED'	=> __('Booking removed successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SELECT_DATE'=> __('Select Date:','dpProEventCalendar'),
			'TXT_BOOK_EVENT_PICK_DATE'	=> __('Click to book on this date.','dpProEventCalendar'),
			'TXT_BOOK_TICKETS_REMAINING'=> __('Tickets Remaining','dpProEventCalendar'),
			'TXT_BOOK_ALREADY_BOOKED'	=> __('You have already booked this event date.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_COMMENT'	=> __('Leave a comment (optional)','dpProEventCalendar'),
			'TXT_CATEGORY'				=> __('Category','dpProEventCalendar'),
			'TXT_SUBSCRIBE'				=> __('Subscribe','dpProEventCalendar'),
			'TXT_SUBSCRIBE_SUBTITLE'	=> __('Receive new events notifications in your email.','dpProEventCalendar'),
			'TXT_YOUR_NAME'				=> __('Your Name','dpProEventCalendar'),
			'TXT_YOUR_EMAIL'			=> __('Your Email','dpProEventCalendar'),
			'TXT_FIELDS_REQUIRED'		=> __('All fields are required.','dpProEventCalendar'),
			'TXT_INVALID_EMAIL'			=> __('The Email is invalid.','dpProEventCalendar'),
			'TXT_SUBSCRIBE_THANKS'		=> __('Thanks for subscribing.','dpProEventCalendar'),
			'TXT_SENDING'				=> __('Sending...','dpProEventCalendar'),
			'TXT_SEND'					=> __('Send','dpProEventCalendar'),
			'TXT_ADD_EVENT'				=> __('Add Event','dpProEventCalendar'),
			'TXT_EDIT_EVENT'			=> __('Edit Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT'			=> __('Remove Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT_CONFIRM'	=> __('Are you sure that you want to delete this event?','dpProEventCalendar'),
			'TXT_CANCEL_BOOKING_CONFIRM'=> __('Are you sure that you want to cancel this booking?','dpProEventCalendar'),
			'TXT_CANCEL'				=> __('Cancel','dpProEventCalendar'),
			'TXT_CANCEL_BOOKING'		=> __('Cancel Booking','dpProEventCalendar'),
			'TXT_COMPLETED'				=> __('Completed','dpProEventCalendar'),
			'TXT_PENDING'				=> __('Pending','dpProEventCalendar'),
			'TXT_CANCELED_BY_USER'		=> __('Canceled by user','dpProEventCalendar'),
			'TXT_CANCELED'				=> __('Canceled','dpProEventCalendar'),
			'TXT_YES'					=> __('Yes','dpProEventCalendar'),
			'TXT_NO'					=> __('No','dpProEventCalendar'),
			'TXT_EVENT_LOGIN'			=> __('You must be logged in to submit an event.','dpProEventCalendar'),
			'TXT_EVENT_THANKS'			=> __('Thanks for your event submission. It will be reviewed soon.','dpProEventCalendar'),
			'TXT_EVENT_TITLE'			=> __('Title','dpProEventCalendar'),
			'TXT_EVENT_DESCRIPTION'		=> __('Event Description','dpProEventCalendar'),
			'TXT_EVENT_IMAGE'			=> __('Upload an Image (optional)','dpProEventCalendar'),
			'TXT_EVENT_LINK'			=> __('Link (optional)','dpProEventCalendar'),
			'TXT_EVENT_SHARE'			=> __('Text to share in social networks (optional)','dpProEventCalendar'),
			'TXT_EVENT_LOCATION'		=> __('Location (optional)','dpProEventCalendar'),
			'TXT_EXTRA_DATES'			=> __('Extra Dates (optional)','dpProEventCalendar'),
			'TXT_OTHER'					=> __('Other','dpProEventCalendar'),
			'TXT_EVENT_LOCATION_NAME'	=> __('Location Name','dpProEventCalendar'),
			'TXT_EVENT_ADDRESS'			=> __('Address','dpProEventCalendar'),
			'TXT_EVENT_PHONE'			=> __('Phone (optional)','dpProEventCalendar'),
			'TXT_EVENT_GOOGLEMAP'		=> __('Google Map (optional)','dpProEventCalendar'),
			'TXT_EVENT_START_DATE'		=> __('Start Date','dpProEventCalendar'),
			'TXT_EVENT_ALL_DAY'			=> __('Set if the event is all day.','dpProEventCalendar'),
			'TXT_EVENT_START_TIME'		=> __('Start Time','dpProEventCalendar'),
			'TXT_EVENT_HIDE_TIME'		=> __('Hide Time','dpProEventCalendar'),
			'TXT_EVENT_END_TIME'		=> __('End Time','dpProEventCalendar'),
			'TXT_EVENT_FREQUENCY'		=> __('Frequency','dpProEventCalendar'),
			'TXT_NONE'					=> __('None','dpProEventCalendar'),
			'TXT_EVENT_DAILY'			=> __('Daily','dpProEventCalendar'),
			'TXT_EVENT_WEEKLY'			=> __('Weekly','dpProEventCalendar'),
			'TXT_EVENT_MONTHLY'			=> __('Monthly','dpProEventCalendar'),
			'TXT_EVENT_YEARLY'			=> __('Yearly','dpProEventCalendar'),
			'TXT_EVENT_END_DATE'		=> __('End Date','dpProEventCalendar'),
			'TXT_MORE'					=> __('More', 'dpProEventCalendar'),
			'TXT_BACK'					=> __('Back', 'dpProEventCalendar'),
			'TXT_TO'					=> __('to', 'dpProEventCalendar'),
			'TXT_EVERY'					=> __('Every','dpProEventCalendar'),
			'TXT_REPEAT_EVERY'			=> __('Repeat every','dpProEventCalendar'),
			'TXT_SUBMIT_FOR_REVIEW'		=> __('Submit for Review','dpProEventCalendar'),
			'TXT_SUBMIT'				=> __('Submit','dpProEventCalendar'),
			'TXT_WEEKS_ON'				=> __('week(s) on:','dpProEventCalendar'),
			'TXT_MONTHS_ON'				=> __('month(s) on:','dpProEventCalendar'),
			'TXT_RECURRING_OPTION'		=> __('Recurring Option','dpProEventCalendar'),
			'TXT_FIRST'					=> __('First','dpProEventCalendar'),
			'TXT_SECOND'				=> __('Second','dpProEventCalendar'),
			'TXT_THIRD'					=> __('Third','dpProEventCalendar'),
			'TXT_FOURTH'				=> __('Fourth','dpProEventCalendar'),
			'TXT_LAST'					=> __('Last','dpProEventCalendar'),
			'TXT_ALLOW_BOOKINGS'		=> __('Allow Bookings?', 'dpProEventCalendar'),
			'TXT_PRICE'					=> __('Price', 'dpProEventCalendar'),
			'TXT_BOOKING_LIMIT'			=> __('Booking Limit', 'dpProEventCalendar'),
			'TXT_BOOKING_BLOCK_HOURS'	=> __('Block Hours', 'dpProEventCalendar'),
			'TXT_SELECT_COLOR'			=> __('Select a color', 'dpProEventCalendar'),
			'TXT_QUANTITY'				=> __('Quantity', 'dpProEventCalendar'),
			'TXT_ATTENDEE'				=> __('Attendee', 'dpProEventCalendar'),
			'TXT_ATTENDEES'				=> __('Attendees', 'dpProEventCalendar'),
			'TXT_YOUR_PHONE'			=> __('Your Phone', 'dpProEventCalendar'),
			'TXT_DRAG_MARKER'			=> __('Drag the marker to set a specific position', 'dpProEventCalendar'),
			'TXT_MON'					=> __('Mon','dpProEventCalendar'),
			'TXT_TUE'					=> __('Tue','dpProEventCalendar'),
			'TXT_WED'					=> __('Wed','dpProEventCalendar'),
			'TXT_THU'					=> __('Thu','dpProEventCalendar'),
			'TXT_FRI'					=> __('Fri','dpProEventCalendar'),
			'TXT_SAT'					=> __('Sat','dpProEventCalendar'),
			'TXT_SUN'					=> __('Sun','dpProEventCalendar'),
			'PREV_MONTH' 				=> __('Prev Month','dpProEventCalendar'),
			'NEXT_MONTH'				=> __('Next Month','dpProEventCalendar'),
			'PREV_DAY' 					=> __('Prev Day','dpProEventCalendar'),
			'NEXT_DAY'					=> __('Next Day','dpProEventCalendar'),
			'PREV_WEEK'					=> __('Prev Week','dpProEventCalendar'),
			'NEXT_WEEK'					=> __('Next Week','dpProEventCalendar'),
			'DAY_SUNDAY' 				=> __('Sunday','dpProEventCalendar'),
			'DAY_MONDAY' 				=> __('Monday','dpProEventCalendar'),
			'DAY_TUESDAY' 				=> __('Tuesday','dpProEventCalendar'),
			'DAY_WEDNESDAY' 			=> __('Wednesday','dpProEventCalendar'),
			'DAY_THURSDAY' 				=> __('Thursday','dpProEventCalendar'),
			'DAY_FRIDAY' 				=> __('Friday','dpProEventCalendar'),
			'DAY_SATURDAY' 				=> __('Saturday','dpProEventCalendar'),
			'MONTHS' 					=> array(
											__('January','dpProEventCalendar'),
											__('February','dpProEventCalendar'),
											__('March','dpProEventCalendar'),
											__('April','dpProEventCalendar'),
											__('May','dpProEventCalendar'),
											__('June','dpProEventCalendar'),
											__('July','dpProEventCalendar'),
											__('August','dpProEventCalendar'),
											__('September','dpProEventCalendar'),
											__('October','dpProEventCalendar'),
											__('November','dpProEventCalendar'),
											__('December','dpProEventCalendar')
										)
	   );
	   
	   $this->translation_orig = $this->translation;


		$this->widget = $widget;
		if($is_admin) { $this->is_admin = true; }
		if($view != "") { $this->view = $view; }
		if(is_numeric($id_calendar)) { $this->setCalendar($id_calendar); }
		if(!isset($defaultDate)) { $defaultDate = $this->getDefaultDate(); }
		$this->defaultDate = $defaultDate;
		if(isset($translation)) { $this->translation = $translation; }
		if(isset($category)) { $this->category = $category; }
		if(isset($event_id)) { $this->event_id = $event_id; }
		if(isset($event)) { $this->event = $event; }
		if(isset($columns)) { $this->columns = $columns; }
		if(isset($from)) { $this->from = $from; }
		if(isset($author)) { $this->author = $author; }
		if(isset($limit_description)) { $this->limit_description = $limit_description; }
		$time_format = get_option('time_format');
		if($time_format == "") {
			$time_format = "H:i:s";
		}
		$this->time_format = $time_format;
		$this->opts = $opts;
		
		$this->nonce = rand();
		
		$this->datesObj = new DPPEC_Dates($defaultDate);
		
		//die(print_r($this->datesObj));
    }
	
	function setCalendar($id) {
		$this->id_calendar = $id;	
		
		$this->getCalendarData();
		
		if(!$this->calendar_obj->enable_wpml) {
			
			$translation_fields = unserialize($this->calendar_obj->translation_fields);
			if(is_array($translation_fields)) {
				
				if(!is_array($this->translation)) {
					$this->translation = array();
				}

				foreach ($translation_fields as $key => $value) {

					$this->translation[strtoupper($key)] = $value;

				}
			}
			
	   } else {
		   
			//echo get_locale().__('View all events','dpProEventCalendar');
			//die();   
	   }
	}
	
	function getNonce() {
		if(!is_numeric($this->id_calendar)) { return false; }
		
		return $this->nonce;
	}
	
	function getDefaultDate() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$default_date;
		$querystr = "
		SELECT default_date
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		if(!empty($calendar_obj)) {
			foreach($calendar_obj as $key=>$value) { $$key = $value; }
		}

		if($default_date == "" || $default_date == "0000-00-00") { $default_date = current_time('timestamp'); } else { $default_date = strtotime($default_date); }
		return $default_date;
	}
	
	function getCalendarName() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return ""; }
		
		$default_date;
		$querystr = "
		SELECT title
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		
		if(!empty($calendar_obj)) {
			foreach($calendar_obj as $key=>$value) { $$key = $value; }
		}
		return $title;
	}
	
	function getCalendarData() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$querystr = "
		SELECT *
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	

		$this->calendar_obj = $calendar_obj;
		
		if($this->view != "") {
			$this->calendar_obj->view = $this->view;
		}

		if(isset($this->calendar_obj->link) && $this->calendar_obj->link != "") {
			if(substr($this->calendar_obj->link, 0, 4) != "http" && substr($this->calendar_obj->link, 0, 4) != "mail") {
				$this->calendar_obj->link = 'http://'.$this->calendar_obj->link;
			}
		}
			
	}
	
	function getCalendarByEvent($event_id) {

		$id_calendar = get_post_meta($event_id, 'pec_id_calendar', true);

		$id_calendar = explode(',', $id_calendar);
		$id_calendar = $id_calendar[0];

		if($id_calendar == "") { $calendar_id = false; } else { $calendar_id = $id_calendar; }
		return $calendar_id;
	}
	
	function getEventData($event_id, $filter = 'none') {

		if(isset($this->loaded_event[$event_id])) {
			return $this->loaded_event[$event_id];
		} else {

			$event_obj = new stdClass;
			
			$event_obj->id = $event_id;
			$event_obj->title = get_the_title($event_id);
			$event_obj->description = get_post_field('post_content', $event_id);
			$event_obj->id_calendar = get_post_meta($event_id, 'pec_id_calendar', true);
			$event_obj->date = get_post_meta($event_id, 'pec_date', true);
			$event_obj->orig_date = $event_obj->date;
			$event_obj->featured_event = get_post_meta($event_id, 'pec_featured_event', true);
			$event_obj->all_day = get_post_meta($event_id, 'pec_all_day', true);
			$event_obj->tbc = get_post_meta($event_id, 'pec_tbc', true);
			$event_obj->pec_daily_working_days = get_post_meta($event_id, 'pec_daily_working_days', true);
			$event_obj->pec_daily_every = get_post_meta($event_id, 'pec_daily_every', true);
			$event_obj->pec_weekly_every = get_post_meta($event_id, 'pec_weekly_every', true);
			$event_obj->pec_weekly_day = get_post_meta($event_id, 'pec_weekly_day', true);
			$event_obj->pec_monthly_every = get_post_meta($event_id, 'pec_monthly_every', true);
			$event_obj->pec_monthly_position = get_post_meta($event_id, 'pec_monthly_position', true);
			$event_obj->pec_monthly_day = get_post_meta($event_id, 'pec_monthly_day', true);
			$event_obj->pec_exceptions = get_post_meta($event_id, 'pec_exceptions', true);
			$event_obj->pec_extra_dates = get_post_meta($event_id, 'pec_extra_dates', true);
			$event_obj->end_date = get_post_meta($event_id, 'pec_end_date', true);
			$event_obj->link = get_post_meta($event_id, 'pec_link', true);
			$event_obj->age_range = get_post_meta($event_id, 'pec_age_range', true);
			$event_obj->map = get_post_meta($event_id, 'pec_map', true);
			$event_obj->end_time_hh = get_post_meta($event_id, 'pec_end_time_hh', true);
			$event_obj->end_time_mm = get_post_meta($event_id, 'pec_end_time_mm', true);
			$event_obj->hide_time = get_post_meta($event_id, 'pec_hide_time', true);
			$event_obj->organizer = get_post_meta($event_id, 'pec_organizer', true);
			$event_obj->location = get_post_meta($event_id, 'pec_location', true);
			$event_obj->location_id = get_post_meta($event_id, 'pec_location', true);
			if(is_numeric($event_obj->location)) {
				$event_obj->map = get_post_meta($event_obj->location, 'pec_venue_map', true);
				$event_obj->location_address = get_post_meta($event_obj->location, 'pec_venue_address', true);
				$event_obj->location = get_the_title($event_obj->location);
				
			}
			$event_obj->phone = get_post_meta($event_id, 'pec_phone', true);
			$event_obj->recurring_frecuency = get_post_meta($event_id, 'pec_recurring_frecuency', true);

			$this->loaded_event[$event_id] = $event_obj;

			return $event_obj;
		}
	}
	
	function getFormattedEventData($get = "", $post_id = "") {
		global $wpdb, $post, $dp_pec_payments, $dpProEventCalendar, $wp_query;

		if($post_id == "") {
			$post_id = $post->ID;
		}

		$return = "";
		$event_data = $this->getEventData($post_id);
		
		if($get == 'date' || $get == 'book_event' || $get == 'actions' || $get == 'attendees') {
			$this->event_id = $event_data->id;
						
			$max_upcoming_dates = $this->calendar_obj->booking_max_upcoming_dates;
			
			$start = substr($event_data->date, 0, 10)." 00:00:00";

			//$event_dates = $this->upcomingCalendarLayout( true, 999, '', null, null, true, false, true, false, false , '', true, $start );
			$event_dates = $this->upcomingCalendarLayout( true, 60 );
			/*echo '<pre>';
			print_r($event_dates);
			echo '</pre>';
			*/
			$valid_dates = array();

			if(is_array($event_dates)) {
				foreach($event_dates as $ev_date) {
					$curDate = substr($ev_date->date, 0, 10);
					
					if($event_data->pec_exceptions != "") {
						$exceptions = explode(',', $event_data->pec_exceptions);
						
						if($event_data->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
							continue;
						}
					}

					if($event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
						continue;
					}
					
					if(!$event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1 && $event_data->pec_daily_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($event_data->orig_date,0,11))) / (60 * 60 * 24)) % $event_data->pec_daily_every != 0 )
					) {

						continue;
					}
					
					if($event_data->recurring_frecuency == 2 && $event_data->pec_weekly_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($event_data->orig_date,0,11))) / (60 * 60 * 24)) % ($event_data->pec_weekly_every * 7) != 0 )
					) {
						//continue;
					}
					
					if($event_data->recurring_frecuency == 3 && $event_data->pec_monthly_every > 1 && 
						( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event_data->orig_date,0,11))))) / ($event_data->pec_monthly_every)) )
					) {
						continue;
					}

					$valid_dates[] = $ev_date->date;
				}
			}
			//print_r($wp_query->query_vars);
			//echo date('Y-m-d H:i:s', $wp_query->query_vars['event_date']);
			if(is_numeric($wp_query->query_vars['event_date']) && in_array(date('Y-m-d H:i:s', $wp_query->query_vars['event_date']), $valid_dates)) {
				$event_data->date = date('Y-m-d H:i:s', $wp_query->query_vars['event_date']);
			} 

			if(strtotime($event_data->date) < time()) {
			//if(strtotime($event_data->date) < strtotime($start)) {
			
				if(is_array($valid_dates) && !empty($valid_dates)) {
					$event_data->date = $valid_dates[0];
				}
			}

		}
		
		switch($get) {
			case 'location':
				if($event_data->location != "") {
					$address = "";
					$phone = "";
					if(is_numeric($event_data->location_id)) {
						$address = get_post_meta($event_data->location_id, 'pec_venue_address', true);
						$phone = get_post_meta($event_data->location_id, 'pec_venue_phone', true);
					}
					$return = '<div class="pec_event_page_location"><i class="fa fa-map-marker"></i>';
					$return .= '<p>'.$event_data->location.'</p>';

					if($address != "") {
						$return .= '<p class="pec_event_page_sub_p">'.$address.'</p>';
					}
					if($phone != "") {
						$return .= '<p class="pec_event_page_sub_p">'.$phone.'</p>';
					}

					$return .= '</div>';
				}
				break;
			case 'phone':
				if($event_data->phone != "") {
					$return = '<div class="pec_event_page_phone"><p><i class="fa fa-phone"></i>'.$event_data->phone.'</p></div>';
				}
				break;
			case 'video':
				$video = get_post_meta($post_id, 'pec_video', true);
				if($video != "") {
					$return = dpProEventCalendar_convertYoutube($video);
				}
				break;
			case 'facebook_url':
				$pec_fb_event = get_post_meta($post_id, 'pec_fb_event', true);
				
				if($pec_fb_event != "") {
					$return = '<div class="dp_pec_clear"></div>';
					$return .= '<div class="pec_event_page_facebook_url"><i class="fa fa-facebook"></i><p><a href="'.$pec_fb_event.'" target="_blank">'.$this->translation['TXT_VISIT_FB_EVENT'].'</a></p></div>';
					$return .= '<div class="dp_pec_clear"></div>';
				}
				break;
			case 'custom_fields':
				if(is_array($dpProEventCalendar['custom_fields_counter'])) {
					$counter = 0;
					
					$return = '<div class="dp_pec_clear"></div>';
					$return .= '<ul class="pec_event_page_custom_fields_list">';

					foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
						$field_value = get_post_meta($post_id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
						if($field_value != "") {
							$field_value = dpProEventCalendar_detectEmail($field_value);
							$return .= '<li class="pec_event_page_custom_fields pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'">';
							if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
								$return .= '<i class="fa fa-check"></i>';
							}
							$return .= '<p>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</p>';

							if($dpProEventCalendar['custom_fields']['type'][$counter] != 'checkbox') { 
								$return .= '<p class="pec_event_page_sub_p">'.$field_value.'</p>';
							}

							$return .= '</li>';
							$return .= '<div class="dp_pec_clear"></div>';
						}
						$counter++;		
					}
					$return .= '</ul>';
				}
				break;
			case 'link':
				if($event_data->link != "") {
					$formated_link = str_replace(array('http://', 'https://'), '', $event_data->link);
					if(strlen($formated_link) > 25) {
						$formated_link = substr(str_replace(array('http://', 'https://'), '', $event_data->link), 0, 25).'...';	
					}
					if(substr($event_data->link, 0, 4) != "http" && substr($event_data->link, 0, 4) != "mail") {
						$event_data->link = 'http://'.$event_data->link;
					}
					$return = '<div class="dp_pec_clear"></div>';
					$return .= '<div class="pec_event_page_link"><i class="fa fa-link"></i><p><a href="'.$event_data->link.'" target="_blank" rel="nofollow">'.$formated_link.'</a></p></div>';
					$return .= '<div class="dp_pec_clear"></div>';
				}
				break;
			case 'organizer':
				if($event_data->organizer != "") {
					$return = '<div class="dp_pec_clear"></div>';
					if(is_numeric($event_data->organizer)) {
						$organizer = get_the_title($event_data->organizer);

						$organizer_image_id = get_post_thumbnail_id( $event_data->organizer );
						if(is_numeric($organizer_image_id)) {
							$organizer_image = wp_get_attachment_image_src( $organizer_image_id, 'thumbnail' );
						}

						$return .= '<div class="pec_event_page_organizer">'.
									(is_array($organizer_image) ? '<div class="pec_event_page_organizer_image" style="background-image:url(\''.$organizer_image[0].'\')"></div>' : '').
									'<p>'.$this->translation['TXT_ORGANIZED_BY'].'</p>'.
									'<p class="pec_event_page_sub_p">'.$organizer.'</p>'.
									'</div>';
					}
					$return .= '<div class="dp_pec_clear"></div>';
				}
				break;
			case 'age_range':
				if($event_data->age_range != "") {
					$return = '<div class="dp_pec_clear"></div>';
					$return .= '<div class="pec_event_page_age_range"><i class="fa fa-users"></i>'.
								'<p>'.$this->translation['TXT_AGE_RANGE'].'</p>'.
								'<p class="pec_event_page_sub_p">'.$event_data->age_range.'</p>'.
								'</div>';
					$return .= '<div class="dp_pec_clear"></div>';
				}
				break;
			case 'categories':
				$category = get_the_terms( $post_id, 'pec_events_category' ); 
				$html = "";
				if(!empty($category)) {
					$category_count = 0;
					$html .= '
					<div class="pec_event_page_categories">
						<p>';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$html .= " / ";	
						}
						$html .= $cat->name;
						$category_count++;
					}
					$html .= '
						</p>
					</div>';
				}
				$return = $html;
				break;
			case 'frequency':
				if($event_data->recurring_frecuency != "") {
					switch($event_data->recurring_frecuency) {
						case 1:
							$return = $this->translation['TXT_EVENT_DAILY'];
							break;	
						case 2:
							$return = $this->translation['TXT_EVENT_WEEKLY'];
							break;	
						case 3:
							$return = $this->translation['TXT_EVENT_MONTHLY'];
							break;	
						case 4:
							$return = $this->translation['TXT_EVENT_YEARLY'];
							break;	
					}
				}
				break;
			case 'map':

				if($event_data->map != "" || is_numeric($event_data->location_id)) {
					
					if(is_numeric($event_data->location_id)) {
						$event_data->map = get_post_meta($event_data->location_id, 'pec_venue_map_lnlat', true);
					} else {
						$event_data->map = get_post_meta($event_data->id, 'pec_map_lnlat', true);
					}

					$geocode = false;
					if($event_data->map != "") {
						$event_data->map = str_replace(" ", "", $event_data->map);
					} else {
						$geocode = true;
						if(is_numeric($event_data->location_id)) {
							$venue_address = get_post_meta($event_data->location_id, 'pec_venue_address', true);
							if($venue_address != "") {
								$event_data->map = $venue_address;
							} else {
								$event_data->map = get_post_meta($event_data->location_id, 'pec_venue_map', true);
							}
						} else {
							$event_data->map = get_post_meta($event_data->id, 'pec_map', true);
						}
					}

					$map_id = $event_data->id.'_'.$this->nonce.'_'.rand();
					$return = $this->getMap($map_id, $event_data->map, $event_data->location_id, $geocode);
				}
				break;
			case 'rating':
				$rate = get_post_meta($post_id, 'pec_rate', true);
				if($rate != '') {
					$return .= '
					<ul class="dp_pec_rate">
						<li><a href="#" '.($rate >= 1 ? 'class="dp_pec_rate_full"' : '').'></a></li>
						<li><a href="#" '.($rate >= 2 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 1 && $rate < 2 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 3 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 2 && $rate < 3 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 4 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 3 && $rate < 4 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="#" '.($rate >= 5 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 4 && $rate < 5 ? 'class="dp_pec_rate_h"' : '').'></a></li>
					</ul>
					<div class="dp_pec_clear"></div>';
				}
				break;
			case 'date':
				
				$return = "";

				$return .= '<div class="pec_event_page_date">';
				$return .= '<i class="fa fa-calendar-o"></i>';
				// To Be Confirmed ?
				if(!$event_data->tbc) {

					$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event_data->date));

					$end_date = '';
					$end_year = '';
					if($event_data->end_date != "" && $event_data->end_date != "0000-00-00" && $event_data->recurring_frecuency == 1) {
						$end_day = date('d', strtotime($event_data->end_date));
						$end_month = date('n', strtotime($event_data->end_date));
						$end_year = date('Y', strtotime($event_data->end_date));
						
						//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
						$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_data->end_date));
					}
										
					$end_time = "";
					if($event_data->end_time_hh != "" && $event_data->end_time_mm != "") { $end_time = str_pad($event_data->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event_data->end_time_mm, 2, "0", STR_PAD_LEFT); }
					
					if($end_time != "") {
						
						$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

						$end_time = " - ".$end_time_tmp;
						if($end_time_tmp == $time) {
							$end_time = "";	
						}
					}

					if($event_data->all_day) {
						$time = $this->translation['TXT_ALL_DAY'];
						$end_time = "";
					}
					
					$all_working_days = '';
					if($event_data->pec_daily_working_days && $event_data->recurring_frecuency == 1) {
						$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
					}

					$event_timezone = dpProEventCalendar_getEventTimezone($event_data->id);
					$event_time_line = $all_working_days.' '.((($this->calendar_obj->show_time && !$event_data->hide_time) || $event_data->all_day) ? $time.$end_time.($this->calendar_obj->show_timezone && !$event_data->all_day ? ' '.$event_timezone : '') : '');

					$show_more_dates = false;

					if(is_array($event_dates) && count($event_dates) > 0 && count($event_dates) !== 1) {
						$show_more_dates = true;
					}

						
					if(strtotime($event_data->date) > current_time('timestamp')) {
						$time_translate = array(
                            'year' => array($this->translation['TXT_YEAR'], $this->translation['TXT_YEARS']),
                            'month' => array($this->translation['TXT_MONTH'], $this->translation['TXT_MONTHS']),
                            'day' => array($this->translation['TXT_DAY'], $this->translation['TXT_DAYS']),
                            'hour' => array($this->translation['TXT_HOUR'], $this->translation['TXT_HOURS']),
                            'minute' => array($this->translation['TXT_MINUTE'], $this->translation['TXT_MINUTES'])
                        );
						$return .= '<p>'.$this->translation['TXT_STARTS_IN'].' '.dpProEventCalendar_get_date_diff(strtotime($event_data->date), current_time('timestamp'), 2, $time_translate).'</p>';
					}

					$return .= '<p class="pec_event_page_sub_p">'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_data->date)).$end_date.' '.$event_time_line.'</p>';

					$return .= '<div class="dp_pec_clear"></div>';
					if($show_more_dates) {
						
						$return .= '<div class="pec_event_page_action_wrap">';

						$return .= '<p class="pec_event_page_action">'.$this->translation['TXT_MORE_DATES'].'</p>';

						$return .= 			"<div class='pec_event_page_action_menu'>";

						

							$return .= 			"<ul>";
							$counter_valid_dates = 0;
							foreach($valid_dates as $key) {
								if($counter_valid_dates >= 10) {
									break;
								}
								$return .= 			"<li".(substr($key, 0, 10) == substr($event_data->date, 0, 10) ? ' class="pec_event_page_action_menu_active"' : '').">".dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($key))."</li>";
								$counter_valid_dates++;
							}

							$return .= 			"</ul>";
						
						$return .= 			"</div>";

						$return .= '</div> ';

					}

					$return .= '</div>';

				} else {
					
					$return .= '<p class="pec_event_page_date_time"><i class="fa fa-clock-o"></i>'.$this->translation['TXT_TO_BE_CONFIRMED'].'</p>';

					$return .= '</div>';

				}

				//$return .= $this->getEditRemoveButtons($event);
				
				$return .= '<div class="dp_pec_clear"></div>';

				break;
			case 'book_event':
				$booking_booked = $this->getBookingBookedLabel($post_id, $event_data->date);
				if($booking_booked == "") {
					$return = $this->getBookingButton($post_id, date('Y-m-d', strtotime($event_data->date)), false, false);
				} else {
					$return = $booking_booked;
				}
					
				break;
			case 'author':

				if($this->calendar_obj->show_author) {
					
					$author = get_userdata(get_post_field( 'post_author', $post_id ));
					$return = '<div class="pec_event_page_author">'.
					$return .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
					$return .= '</div>';
				}
				break;
			case 'attendees':
				if(get_post_meta($post_id, 'pec_enable_booking', true) || $this->calendar_obj->booking_enable) {

					if($this->calendar_obj->booking_display_attendees) {
						$attendees_counter = $this->getEventBookings(true, date('Y-m-d', strtotime($event_data->date)), $post_id);

						$return = '<div class="dp_pec_clear"></div>';
						$return .= "<div class='dp_pec_tooltip_list_wrap dp_pec_attendees_counter_".$post_id."'>";
						$return .= '<div class="pec_event_page_attendees"><i class="fa fa-user"></i>'.
									'<p>'.$attendees_counter.' '.($attendees_counter == 1 ? $this->translation['TXT_ATTENDEE'] : $this->translation['TXT_ATTENDEES']).'</p>';

						if ($this->calendar_obj->booking_display_attendees_names && $attendees_counter > 0) {

							$attendees_list = $this->getEventBookings(false, date('Y-m-d', strtotime($event_data->date)), $post_id);

							$return .= "<div class='dp_pec_tooltip_list'>";

							$return .= "	<ul class='dp_pec_tooltip_list_ul'>";

							foreach($attendees_list as $booking) {
								if(is_numeric($booking->id_user) && $booking->id_user > 0) {

									$userdata = get_userdata($booking->id_user);
								} else {
									$userdata = new stdClass();
									$userdata->display_name = $booking->name;
									$userdata->user_email = $booking->email;	
								}

							
									$return .= "<li>".$userdata->display_name."</li>";
							}
							
							$return .= "	</ul>";

							$return .= "</div>";

						}

						$return .= "</div>";
						$return .= "</div>";
						$return .= '<div class="dp_pec_clear"></div>';
					}
				}

				break;
			case 'actions':
				$return .= '<div class="pec_event_page_action_wrap">';

				$return .= 		'<p class="pec_event_page_action">'.$this->translation['TXT_MORE'].' <i class="fa fa-chevron-down"></i></p>';
				$return .= 		"<div class='pec_event_page_action_menu'>";

				$return .= 			"<ul>";

				if(is_user_logged_in()) {
					global $current_user;
					wp_get_current_user();

					if(($current_user->ID == get_post_field( 'post_author', $post_id) || current_user_can('switch_themes') ) && ($this->calendar_obj->allow_user_edit_event && $this->opts['allow_user_edit_remove'])) {
						
						$return .= 			"<li><a href='#' title='".$this->translation['TXT_EDIT_EVENT']."' data-event-id='".$post_id."' class='pec_edit_event'><i class='fa fa-pencil'></i>".$this->translation['TXT_EDIT_EVENT']."</a></li>";
					}
				}

				if($this->calendar_obj->ical_active && !$event_data->tbc) {
				
					$ical_date = strtotime($event_data->date);	
					
					$return .= 			"<li><a href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$post_id.'&date='.$ical_date ) . "'><i class='fa fa-calendar-plus-o'></i>".$this->translation['TXT_ADD_TO_PERSONAL_CALENDAR']."</a></li>";
				}

				$return .= 				"<li><a href='javascript:window.print();'><i class='fa fa-print'></i>".$this->translation['TXT_PRINT']."</a></li>";

				$return .= 			"</ul>";
				
				$return .= 		"</div>";

				$return .= 	"</div>";

				break;
			case 'time':
				
				$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event_data->date));
													
				$end_time = "";
				if($event_data->end_time_hh != "" && $event_data->end_time_mm != "") { $end_time = str_pad($event_data->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event_data->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

					$end_time = " / ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if($event_data->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$event_timezone = dpProEventCalendar_getEventTimezone($event_data->id);

				$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event_data->hide_time) || $event_data->all_day) ? $time.$end_time.($this->calendar_obj->show_timezone && !$event_data->all_day ? ' '.$event_timezone : '') : '');
				if($pec_time != "") {
					$return .= '<div class="pec_event_page_date">'.
								'<p>'.$pec_time.'</p>'.
						   '</div>
						   <div class="dp_pec_clear"></div>';
				}
				break;
		}
		
		return $return;
	}
	
	function getBookingsByUser($user_id) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		$querystr = "
            SELECT *
            FROM ".$this->table_booking."
			WHERE id_user = ".$user_id." AND event_date >= CURRENT_DATE AND status <> 'pending'
			ORDER BY event_date ASC
            ";
		if(is_numeric($this->limit) && $this->limit > 0) {
			$querystr .= "LIMIT ".$this->limit;
		}
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return  $bookings_obj;
	}
	
	function getBookingsCount($post_id, $date) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		if(!is_numeric($post_id)) {
			return 0;	
		}

		$id_list = $post_id;
        if(function_exists('icl_object_id')) {
            global $sitepress;

            if(is_object($sitepress) ) {
	            $id_list_arr = array();
				$trid = $sitepress->get_element_trid($post_id, 'post_pec-events');
				$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

				foreach($translation as $key) {
					$id_list_arr[] = $key->element_id;
				}

				if(!empty($id_list_arr)) {
					$id_list = implode(",", $id_list_arr);
				}
			}
		}
		
		$pec_booking_continuous = get_post_meta($post_id, 'pec_booking_continuous', true);

		$querystr = "
            SELECT SUM(quantity) as counter
            FROM ".$this->table_booking."
			WHERE id_event IN(".$id_list.") ";
		if(!$pec_booking_continuous) {
			$querystr .= "	AND event_date = '".$date."'";
		}

		$querystr .= "
			AND status <> 'pending' AND status <> 'canceled_by_user' AND status <> 'canceled'";
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return $bookings_obj[0]->counter;
	}
	
	function getBookingForm($post_id, $date = '') {
		global $dp_pec_payments, $dpProEventCalendar;
		
		$hide_buttons = false;
		$return = '';

		$calendar = $this->id_calendar;

		if(!is_numeric($calendar) || $calendar == 0) {
			$calendar = explode(",", get_post_meta($post_id, 'pec_id_calendar', true));
			$calendar = $calendar[0];
		}

		$event_single = "false";

		$this->event_id = $post_id;
		
		$max_upcoming_dates = $this->calendar_obj->booking_max_upcoming_dates;
		
		$pec_booking_block_hours = get_post_meta($post_id, 'pec_booking_block_hours', true);
		$pec_booking_continuous = get_post_meta($post_id, 'pec_booking_continuous', true);
		$start_date_from  = '';

		if(is_numeric($pec_booking_block_hours) && $pec_booking_block_hours > 0) {
			$start_date_from  = date('Y-m-d H:i:s', strtotime('+ '.$pec_booking_block_hours.' hours', current_time('timestamp')));
		}

		$event_dates = $this->upcomingCalendarLayout( true, (is_numeric($max_upcoming_dates) && $max_upcoming_dates > 0 ? $max_upcoming_dates : 10), '', null, null, true, false, true, false, false, '', false, $start_date_from );

		$autoselected_date = "";
		
		$default_date = null;
		
		if(count($event_dates) == 1) {
			
			if(!is_array($event_dates)) {
				$autoselected_date = substr(get_post_meta($post_id, 'pec_date', true), 0, 10);
				
			} else {
				$autoselected_date = substr($event_dates[0]->date, 0, 10);
			}
			
			$event_single = "true";
			
		}
		
		if(count($event_dates) > 0 || $date != '') {
			
			if(!is_array($event_dates) || $date != '') {
				if($date != "") {
					$default_date = $date;
				} else {
					$default_date = substr(get_post_meta($post_id, 'pec_date', true), 0, 10);
				}
			} else {
				$default_date = substr($event_dates[0]->date, 0, 10);
			}
			
		}

		$return .= '
		<div class="pec_book_select_date">
			<div class="pec_modal_wrap_content">	
		';

		if($event_single == "true") {
			
			if($this->userHasBooking($autoselected_date, $post_id)) {
				
				$return .= '<p>'.$this->translation['TXT_BOOK_ALREADY_BOOKED'].'</p>';
				
				$hide_buttons = true;
				
			} else {
				
				//$return .= '<p>'.sprintf( __( 'Booking of %s' , 'dpProEventCalendar'), get_the_title($post_id) . ' <strong>('.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($autoselected_date)).')</strong>' ).'</p>';
				
			}
		
		} elseif($pec_booking_continuous) {
			
			if($this->userHasBooking("", $post_id)) {
				
				$return .= '<p>'.$this->translation['TXT_BOOK_ALREADY_BOOKED'].'</p>';
				
				$hide_buttons = true;
				
			}
		}

		if(!is_user_logged_in() && !$hide_buttons) {
			$return .= '<input type="text" value="" class="dpProEventCalendar_input dpProEventCalendar_from_name" id="pec_event_page_book_name" placeholder="'.$this->translation['TXT_YOUR_NAME'].'" />';	
			$return .= '<input type="email" value="" class="dpProEventCalendar_input dpProEventCalendar_from_email" id="pec_event_page_book_email" placeholder="'.$this->translation['TXT_YOUR_EMAIL'].'" />';	
		}
		
		if($this->calendar_obj->booking_show_phone && !$hide_buttons) {
			$return .= '<input type="tel" value="" class="dpProEventCalendar_input dpProEventCalendar_from_phone" id="pec_event_page_book_phone" placeholder="'.$this->translation['TXT_YOUR_PHONE'].'" />';		
		}

		$cal_booking_custom_fields = $this->calendar_obj->booking_custom_fields;
		$cal_booking_custom_fields_arr = explode(',', $cal_booking_custom_fields);

		
		if(is_array($dpProEventCalendar['booking_custom_fields_counter']) && !$hide_buttons) {
			$counter = 0;
			
			foreach($dpProEventCalendar['booking_custom_fields_counter'] as $key) {
				
				if(!empty($cal_booking_custom_fields) && $cal_booking_custom_fields != "all" && $cal_booking_custom_fields != "" && !in_array($dpProEventCalendar['booking_custom_fields']['id'][$counter], $cal_booking_custom_fields_arr)) {
					$counter++;
					continue;
				}

				if($dpProEventCalendar['booking_custom_fields']['type'][$counter] == "checkbox") {

					$return .= '
					<div class="dp_pec_wrap_checkbox">
					<input type="checkbox" class="checkbox pec_event_page_book_extra_fields '.(!$dpProEventCalendar['booking_custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="1" id="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'
					</div>';
		
				} else {

					$return .= '
					<input type="text" class="dpProEventCalendar_input pec_event_page_book_extra_fields '.(!$dpProEventCalendar['booking_custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="" placeholder="'.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'" id="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" />';
					
				}
				$counter++;		
			}
		}
		
		$return .= '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';
		
		$return .= '

			<input type="hidden" name="pec_event_page_book_event_id" id="pec_event_page_book_event_id" value="'.$post_id.'" />
			<input type="hidden" name="pec_event_page_book_calendar" id="pec_event_page_book_calendar" value="'.$calendar.'" />
			';
		
		$this->event_id = "";
		
		
		$return .= '<div class="dp_pec_clear"></div>';
		
		$return .= '</div>';
			
		if(!$hide_buttons) {
			
			$return .= '<div class="dp_pec_clear"></div>

				<p class="pec_booking_date">
					<!--'.$this->translation['TXT_BOOK_EVENT_SELECT_DATE'].'-->
					<select autocomplete="off" name="pec_event_page_book_date" id="pec_event_page_book_date"> 
					';
			
			$booking_limit = get_post_meta($post_id, 'pec_booking_limit', true);
			$booking_available_first = 0;
			
			$booking_max_quantity = $this->calendar_obj->booking_max_quantity;

			$count_event_dates = 0;
			if(!is_array($event_dates) || count($event_dates) == 0) {
				
				$return .= '<option value="">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</option>';	
				
			} else {

				foreach($event_dates as $upcoming_date) {
					if($upcoming_date->id == "") {
						$upcoming_date->id = $upcoming_date->ID;
					}

					$upcoming_date = (object)array_merge((array)$this->getEventData($upcoming_date->id), (array)$upcoming_date);

					$curDate = substr($upcoming_date->date, 0, 10);

					if($upcoming_date->pec_exceptions != "") {
						$exceptions = explode(',', $upcoming_date->pec_exceptions);
						
						if($upcoming_date->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
							continue;
						}
					}

					if($upcoming_date->pec_daily_working_days && $upcoming_date->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
						continue;
					}
					
					if(!$upcoming_date->pec_daily_working_days && $upcoming_date->recurring_frecuency == 1 && $upcoming_date->pec_daily_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($upcoming_date->orig_date,0,11))) / (60 * 60 * 24)) % $upcoming_date->pec_daily_every != 0 )
					) {
						continue;
					}
					
					if($upcoming_date->recurring_frecuency == 2 && $upcoming_date->pec_weekly_every > 1 && 
						( ((strtotime($curDate) - strtotime(substr($upcoming_date->date,0,11))) / (60 * 60 * 24)) % ($upcoming_date->pec_weekly_every * 7) != 0 )
					) {
						continue;
					}
					
					if($upcoming_date->recurring_frecuency == 3 && $upcoming_date->pec_monthly_every > 1 && 
						( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($upcoming_date->orig_date,0,11))))) / ($upcoming_date->pec_monthly_every)) )
					) {
						continue;
					}

					$booking_count = $this->getBookingsCount($upcoming_date->id, substr($upcoming_date->date, 0, 10));
					
					$booking_available = $booking_limit - $booking_count;
					
					if($booking_available < 0 || !is_numeric($booking_available)) {
						$booking_available = 0;	
					}
					
					if($count_event_dates == 0) {
						$booking_available_first = $booking_available;	
					}
					
					$option_value = ($booking_limit > 0 && $booking_available == 0 ? '0' : substr($upcoming_date->date, 0, 10));
					
					if($upcoming_date->end_time_hh != "" && $upcoming_date->end_time_mm != "") {
						$end_time = str_pad($upcoming_date->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($upcoming_date->end_time_mm, 2, "0", STR_PAD_LEFT);

						$end_time = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

						if($end_time == dpProEventCalendar_date_i18n($this->time_format, strtotime($upcoming_date->date))) {
							$end_time = "";	
						}
					}

					$end_time = "";						

					$option_text = dpProEventCalendar_date_i18n(

						get_option('date_format') . ($this->calendar_obj->show_time && !$upcoming_date->hide_time ? ' ' . $this->time_format : ''), 
						strtotime($upcoming_date->date)

					).($this->calendar_obj->show_time && !$upcoming_date->hide_time && $end_time != "" ? '-' . $end_time : '');

					if($pec_booking_continuous) {
						$option_text = $this->translation['TXT_ALL_EVENT_DATES'];
					}

					if($this->calendar_obj->booking_max_quantity == 1 && $this->userHasBooking($option_value, $upcoming_date->id)) {
						//continue;
						$option_value = 0;
						$option_text .= " (".$this->translation['TXT_BOOK_ALREADY_BOOKED'].")";
					}

					$return .= '<option data-available="'.$booking_available.'" value="'.$option_value.'" '.($default_date == $option_value ? 'selected="selected"' : '').'>
					'.$option_text.' '.($booking_limit > 0 && $this->calendar_obj->booking_show_remaining ? '('.$booking_available.' '.$this->translation['TXT_BOOK_TICKETS_REMAINING'].')' : '').'</option>';	
					
					$count_event_dates++;

					if($pec_booking_continuous) {
						break;
					}
				
				}
			}
			$return .= '</select></p><div class="dp_pec_clear"></div>';
			

			if ( is_plugin_active( 'woocommerce-dp-pec/woocommerce-dp-pec.php' ) ) {
				$pec_booking_ticket = get_post_meta($post_id, 'pec_booking_ticket', true);
				$pec_booking_ticket = $pec_booking_ticket_arr = ($pec_booking_ticket != "" ? explode(",", $pec_booking_ticket) : '');
				
				if(is_array($pec_booking_ticket) && !empty($pec_booking_ticket)) {
					$return .= '<p class="pec_booking_ticket">
							<!--'.$this->translation['TXT_BOOK_EVENT_SELECT_DATE'].'-->
							<select autocomplete="off" name="pec_event_page_book_ticket" id="pec_event_page_book_ticket"> 
							';
							foreach($pec_booking_ticket as $ticket) {
								if($ticket == "") { continue;}
								$return .= '<option value="'.$ticket.'">'.get_the_title($ticket).'</option>';	
							}

					$return .= '</select></p><div class="dp_pec_clear"></div>';
				}
			}

			$return .= '<div class="pec_booking_quantity">';

			$return .= '<select autocomplete="off" name="pec_event_page_book_quantity" id="pec_event_page_book_quantity">';

			

			if($this->calendar_obj->booking_max_quantity > $booking_available && is_numeric($booking_available) && $booking_available > 0) {
				//$booking_max_quantity = $booking_available;
			}
			for($i = 1; $i <= $booking_max_quantity; $i++) {
				$return .= '<option value="'.$i.'">'.$i.'</option>';	
			}
			$return .= '</select>';

			$return .= '<span>'.$this->translation['TXT_QUANTITY'].'</span>';
			
			$price = get_post_meta($post_id, 'pec_booking_price', true);
			
			if(is_numeric($price) && $price > 0) {
				$return .= '<p class="dp_pec_payment_price">'.__('Total Price', 'dpProEventCalendar').' <span class="dp_pec_payment_price_wrapper"><span class="dp_pec_payment_price_value" data-price="'.$price. '" data-price-updated="'.$price. '">'.$price. '</span> ' .$dp_pec_payments['currency'].'</span></p>';
			}

			$return .= '</div>';
				
			$return .= '<div class="dp_pec_clear"></div>';

			$form_bottom = apply_filters('pec_booking_form_bottom', $post_id);

			if($form_bottom != $post_id && $form_bottom != "") {
				$return .= $form_bottom;
			}

			if($this->calendar_obj->booking_comment && !$hide_buttons) {
				$return .= '
				<textarea name="pec_event_page_book_comment" id="pec_event_page_book_comment" placeholder="'.$this->translation['TXT_BOOK_EVENT_COMMENT'].'" class="dpProEventCalendar_textarea"></textarea>';
			}

			$return .= '
				<div class="pec-add-footer">
					<button class="pec_event_page_send_booking" '.($booking_limit > 0 && $booking_available_first == 0 ? 'disabled="disabled"' : '').'>'.(get_post_meta($post_id, 'pec_booking_price', true) ? apply_filters('pec_payments_send', $this->translation['TXT_SEND']) : $this->translation['TXT_SEND'] ).'</button>
					';
			if(is_numeric($dpProEventCalendar['terms_conditions'])) {
				$return .= '
					<p><input type="checkbox" name="pec_event_page_book_terms_conditions" id="pec_event_page_book_terms_conditions" value="1" /> '.sprintf(__('I\'ve read and accept the %s terms & conditions %s', 'dpProEventCalendar'), '<a href="'.dpProEventCalendar_get_permalink($dpProEventCalendar['terms_conditions']).'" target="_blank">', '</a>').'</p>
					
				';
			}	
			$return .= '
					<div class="dp_pec_clear"></div>
				</div>';
		}
		$return .= '
			</div>
		</div>';

		return $return;

	}

	function getBookingBookedLabel ($post_id, $date) {
		$return = "";
		if($this->calendar_obj->booking_display_fully_booked) {

			if(
				get_post_meta($post_id, 'pec_enable_booking', true) || $this->calendar_obj->booking_enable
				
			) {
				//Booking Available For Date
				$booking_limit = get_post_meta($post_id, 'pec_booking_limit', true);
				if(is_numeric($booking_limit) && $booking_limit > 0) {
					$booking_count = $this->getBookingsCount($post_id, substr($date, 0, 10));
						
					$booking_available = (int)$booking_limit - (int)$booking_count;
						
					if($booking_available <= 0 || !is_numeric($booking_available)) {
						
						$return = '<span class="dp_pec_fully_booked">'.$this->translation['TXT_FULLY_BOOKED'].'</span>';
					}
				}
			}
		}

		return $return;

	}
	function getBookingButton($post_id, $date = '', $clear = true, $show_attendees = true) {
		global $dp_pec_payments, $dpProEventCalendar, $wp_query;

		$return = '';
		
		if(post_password_required($post_id)) {
			return '';
		}

		$extra_dates = array();
		$has_extra_dates = false;
		$pec_extra_dates = get_post_meta($post_id, 'pec_extra_dates', true);
		if($pec_extra_dates != "") {
			$extra_dates = explode(",", $pec_extra_dates);
			foreach($extra_dates as $extra_date) {
				if(strtotime($extra_date) > (int)current_time( 'timestamp' )) {
					$has_extra_dates = true;
				}
			}
		}
		
		if(
			(is_user_logged_in() || $this->calendar_obj->booking_non_logged) 
			&& 
			(
				(get_post_meta($post_id, 'pec_enable_booking', true) || $this->calendar_obj->booking_enable) 
				&& 
				( 
					get_post_meta($post_id, 'pec_recurring_frecuency', true) > 0 
					|| 
					strtotime(get_post_meta($post_id, 'pec_date', true)) > (int)current_time( 'timestamp' ) 
					|| 
					$has_extra_dates
				)
			) 
			&&
			(strtotime($date. ' '.date('H:i:s', strtotime(get_post_meta($post_id, 'pec_date', true)))) >= (int)current_time( 'timestamp' ) )
		) {

			$pec_booking_block_hours = get_post_meta($post_id, 'pec_booking_block_hours', true);
			
			if(is_numeric($pec_booking_block_hours) && $pec_booking_block_hours > 0) {
				$start_date_from  = date('Y-m-d H:i:s', strtotime('+ '.$pec_booking_block_hours.' hours', current_time('timestamp')));

				$event_dates = $this->upcomingCalendarLayout( true, 3, '', null, null, true, false, true, false, false, '', false, $start_date_from );

				if(empty($event_dates)) {
					return '';
				}
				
			}

			$calendar = $this->id_calendar;

			if(!is_numeric($calendar) || $calendar == 0) {
				$calendar = explode(",", get_post_meta($post_id, 'pec_id_calendar', true));
				$calendar = $calendar[0];
			}

			$return .= '<div class="pec_event_page_book_wrapper">
				<a href="#" class="pec_event_page_book" data-event-id="'.$post_id.'" data-calendar="'.$calendar.'" data-date="'.$date.'"><i class="fa fa-calendar"></i><strong>'.$this->translation['TXT_BOOK_EVENT'].'</strong>' . (get_post_meta($post_id, 'pec_booking_price', true) > 0 && $dp_pec_payments['currency'] != "" ? ' <div class="pec_booking_price">'.get_post_meta($post_id, 'pec_booking_price', true). ' ' .$dp_pec_payments['currency'].'</div>' : '').'</a>';

			//$return .= 	$this->getBookingForm($post_id, $date);	
			
			if($clear)
				$return .= '<div class="dp_pec_clear"></div>';
			
			// Attendees Counter
						
			if($this->calendar_obj->booking_display_attendees && $show_attendees) {

				/*if(is_numeric($wp_query->query_vars['event_date'])) {
					$event_data->date = date('Y-m-d', $wp_query->query_vars['event_date']);
				}*/
				
				$attendees_counter = $this->getEventBookings(true, $date, $post_id);
				
				//print_r($booking_obj);
				
				$return .= "<div class='dp_pec_attendees_counter dp_pec_tooltip_list_wrap dp_pec_attendees_counter_".$post_id."'>";

				$return .= "<span>" . $attendees_counter . '</span> ' . $this->translation['TXT_ATTENDEES'];

				if ($this->calendar_obj->booking_display_attendees_names && $attendees_counter > 0) {

					$attendees_list = $this->getEventBookings(false, $date, $post_id);

					$return .= "<div class='dp_pec_tooltip_list'>";

					$return .= "	<ul class='dp_pec_tooltip_list_ul'>";

					foreach($attendees_list as $booking) {
						if(is_numeric($booking->id_user) && $booking->id_user > 0) {

							$userdata = get_userdata($booking->id_user);
						} else {
							$userdata = new stdClass();
							$userdata->display_name = $booking->name;
							$userdata->user_email = $booking->email;	
						}

					
							$return .= "<li>".$userdata->display_name."</li>";
					}
					
					$return .= "	</ul>";

					$return .= "</div>";

				}
				
				$return .= "</div>";
				
				if($clear)
					$return .= '<div class="dp_pec_clear"></div>';

			}
			
			$return .= "</div>";

		}	
		
		return $return;
	}
	
	// ************************************* //
	// ****** Monthly Calendar Layout ****** //
	//************************************** //
	
	function monthlyCalendarLayout($compact = false) 
	{
		global $dpProEventCalendar_cache;

		$month_search = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT);

		$layoutCache = 'monthlyLayout';
		if($compact) {
			$layoutCache = 'monthlyLayoutCompact';
		}

		/*
		if(!$this->is_admin &&
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]) && 
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search]) && 
			$this->calendar_obj->cache_active &&
			empty($this->category) &&
			!$this->opts['include_all_events'] &&
			empty($this->opts['location']) &&
			empty($this->event_id) &&
			empty($this->author) &&
			$this->limit_description == 0) {
				
			$html = $dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search]['html'];
			//echo '<pre>';
			//print_r($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]);
			//echo '</pre>';
		} else {
		*/
		//die();
			$html = '';
			
			$html .= '<div class="dp_pec_monthly_row">';

			if($this->calendar_obj->first_day == 1) {
				if($this->datesObj->firstDayNum == 0) { $this->datesObj->firstDayNum == 7;  }
				$this->datesObj->firstDayNum--;
				
				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_monday">
					 	<div class="dp_pec_dayname_item">
							<span>'.$this->translation['DAY_MONDAY'].'</span>
					 	</div>
					 </div>';
			} else {
				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_sunday">
						<div class="dp_pec_dayname_item">
							<span>'.$this->translation['DAY_SUNDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_monday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_MONDAY'].'</span>
					 	</div>
					 </div>';
			}
			$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_tuesday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_TUESDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_wednesday">
						<div class="dp_pec_dayname_item">
							<span>'.$this->translation['DAY_WEDNESDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_thursday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_THURSDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_friday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_FRIDAY'].'</span>
					 	</div>
					 </div>
					 <div class="dp_pec_dayname dp_pec_dayname_saturday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_SATURDAY'].'</span>
					 	</div>
					 </div>
					 ';
			if($this->calendar_obj->first_day == 1) {
				$html .= '
					 <div class="dp_pec_dayname dp_pec_dayname_sunday">
						<div class="dp_pec_dayname_item">	
							<span>'.$this->translation['DAY_SUNDAY'].'</span>
						</div> 
					 </div>';
			}

			$html .= '</div>';
			
			$general_count = 0;
			
			if($general_count == 0) {
				$html .= '<div class="dp_pec_monthly_row">';		
			}
			
			if( $this->datesObj->firstDayNum != 6 ) {
				
				for($i = ($this->datesObj->daysInPrevMonth - $this->datesObj->firstDayNum); $i <= $this->datesObj->daysInPrevMonth; $i++) 
				{
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.$i.'</span></div></div>
							</div>';
					
					$general_count++;
				}
				
			}

			
			$month_number = str_pad(($this->datesObj->currentMonth), 2, "0", STR_PAD_LEFT);
			$year = $this->datesObj->currentYear;
			
			$start = $year."-".$month_number."-01 00:00:00";

			if(($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) && date("Y-m") == $year."-".$month_number) {
				$start = date("Y-m-d H:i:s");
			}
			
			if(!$this->is_admin) {
				$list = $this->upcomingCalendarLayout( true, 20, '', $start, $year."-".$month_number."-".$this->datesObj->daysInCurrentMonth." 23:59:59", true );
			}

			
			for($i = 1; $i <= $this->datesObj->daysInCurrentMonth; $i++) 
			{
				$result = array();

				$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($i, 2, "0", STR_PAD_LEFT);
				$countEvents = 0;
				$eventsCurrDate = array();
				
				if(!$this->is_admin) {

					if(is_array($list)) {
						foreach ($list as $key) {
							if(substr($key->date, 0, 10) == $curDate) {
								$result[] = $key;
							}		
						}
					}

					//$result = $this->getEventsByDate($curDate);
					
					if(is_array($result)) {
						foreach($result as $event) {
							
							if($event->id == "") 
								$event->id = $event->ID;

							// Reset featured option
							unset($event->featured_event);
							
							$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

							if($event->pec_exceptions != "") {
								$exceptions = explode(',', $event->pec_exceptions);
								
								if($event->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
									continue;
								}
							}

							if($event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
								continue;
							}
							
							if(!$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
								( ((strtotime($curDate) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
								( ((strtotime(substr($event->date,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
								( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event->orig_date,0,11))))) / ($event->pec_monthly_every)) )
							) {
								continue;
							}
							
							if($event->featured_event) {
								array_unshift($eventsCurrDate, $event);
							} else {
								$eventsCurrDate[] = $event;
							}

							$countEvents++;
						}
					}
				}
				
				//$countEvents = $this->getCountEventsByDate($curDate);
				if(($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) || !empty($this->event_id)) {
					@$this->calendar_obj->date_range_start = date('Y-m-d');	
				}
				if(($this->calendar_obj->date_range_start != '0000-00-00' && $this->calendar_obj->date_range_start != NULL && (strtotime($curDate) < strtotime($this->calendar_obj->date_range_start)) || ( $this->calendar_obj->date_range_end != '0000-00-00' && $this->calendar_obj->date_range_end != NULL && strtotime($curDate) > strtotime($this->calendar_obj->date_range_end))) && !$this->is_admin) {
					
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
						<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
							<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.$i.'</span></div></div>
						</div>';
				} else {
					$special_date = "";
					$special_date_obj = $this->getSpecialDates($curDate);
					$booked_date = false;
					$booking_remain = true;					
					
					$special_date_title = "";

					if(!empty($this->event_id)) {
						
						$booking_limit = get_post_meta($this->event_id, 'pec_booking_limit', true);
						$booking_count = $this->getBookingsCount($this->event_id, $curDate);
						
						if($booking_limit > 0 && ($booking_limit - $booking_count) <= 0) {
							$booking_remain = false;
						}

						if($this->userHasBooking($curDate, $this->event_id)) {
							$special_date = "style='background-color: ".$this->calendar_obj->booking_event_color.";' ";
							$booked_date = true;
						}
						
					} else {
						
						if($special_date_obj->color) {
							$special_date = "style='background-color: ".$special_date_obj->color.";' ";
						}
						if($special_date_obj->title) {
							$special_date_title = $special_date_obj->title;
						}
						
						if($curDate == date("Y-m-d", current_time('timestamp'))) {
							$special_date_title = $this->translation['TXT_CURRENT_DATE'];
							//$special_date = "style='background-color: ".$this->calendar_obj->current_date_color.";' ";
						}
						
					}
					
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
						<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.($countEvents > 0 && !$booked_date && $booking_remain ? 'pec_has_events' : '').' '.($general_count % 7 == 0 ? 'first-child' : '').' '.($special_date != "" ? 'dp_pec_special_date' : '').'" data-dppec-date="'.$curDate.'">
							<div class="dp_pec_date_item" '.$special_date.'><div class="dp_special_date_dot" '.$special_date.'>'.($special_date_title != "" ? '<div>'.$special_date_title.'</div>' : "").'</div><div class="dp_date_head"><span>'.$i.'</span></div>
							';

					if($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact) {
						$html .= '<div class="pec_monthlyDisplayEvents">';
						$count_monthly_title = 0;

						$calendar_per_date = (is_numeric($this->opts['calendar_per_date']) && $this->opts['calendar_per_date'] > 0 ? $this->opts['calendar_per_date'] : 3);

						foreach($eventsCurrDate as $event) {
			
							if($event->id == "") 
								$event->id = $event->ID;
							
							$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

							$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));
							
							$end_time = "";
							if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
							
							if($end_time != "") {
								
								$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

								$end_time = " - ".$end_time_tmp;
								if($end_time_tmp == $time) {
									$end_time = "";	
								}
							}
													
							if(isset($event->all_day) && $event->all_day) {
								$time = $this->translation['TXT_ALL_DAY'];
								$end_time = "";
							}
							
							$category_list = get_the_terms( $event->id, 'pec_events_category' ); 
							$category_color = "";
							if(!empty($category_list)) {
								foreach ( $category_list as $cat){
									
									$cat_color = get_term_meta($cat->term_id, 'color', true);

									if(isset($cat_color) && is_numeric($cat_color)) {
										$category_color = $cat_color;
										break;
									}
								}
							}

							$event_color_id = get_post_meta($event->id, 'pec_color', true);
							if($event_color_id == "" && $category_color != "") {
								$event_color_id = $category_color;
							}

							$event_color = $this->getSpecialDatesById($event_color_id);
							
							$title = wp_trim_words($event->title, 5);

							$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

							if($this->calendar_obj->show_time && !$event->hide_time) {
								//$html .= '<span>'.$time.'</span>';
								$title = '<strong>'.$time.$end_time.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '').'</strong><br>'.$title;
							}
							
							if($this->calendar_obj->link_post) {
								$permalink = dpProEventCalendar_get_permalink($event->id);
								$use_link = get_post_meta($event->id, 'pec_use_link', true);
								$href = $permalink;

								if(!$use_link) {
									if ( get_option('permalink_structure') ) {
										$permalink_format = rtrim($permalink, '/');
										if(strpos($permalink, "?") !== false) {
											$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
										} else {
											$permalink_query = "";
										}

										$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
										$href = $permalink_format.'/'.strtotime($event->date).$permalink_query;
									} else {
										$href = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
									}
								}

							} else {
								$href = '#';
							}

							$html .= '<a class="dp_daily_event" style="'.($event_color != "" ? 'background-color:'.$event_color.';' : '').($count_monthly_title >= $calendar_per_date ? 'display:none;' : '').'" data-dppec-event="'.$event->id.'" href="'.$href.'" '.($href != '#' ? 'target="'.$this->calendar_obj->link_post_target.'"' : '').' data-dppec-date="'.$curDate.'">'.$title;
							$html .= $this->getBookingBookedLabel($event->id, $curDate);
							$html .= '</a>';

							$count_monthly_title++;
							
						}

						

						if($count_monthly_title > $calendar_per_date) {

							$html .= '<span class="dp_daily_event dp_daily_event_show_more"> + '.($count_monthly_title - $calendar_per_date).' '.$this->translation['TXT_MORE'].'</span>';

						}

						$html .= '</div>';
						
					}
					
					$html .= '
							'.($countEvents > 0 && !$booked_date && $booking_remain ? ($this->calendar_obj->show_x || !empty($this->event_id) ? (!empty($this->event_id) ? '<span class="dp_book_event_radio"></span>' : '<span class="dp_count_events" '.($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact ? 'style="display:none"' : '').'>X</span>') : '<span class="dp_count_events" '.($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact ? 'style="display:none"' : '').'>'.$countEvents.'</span>') : '').'
							';
						
					
					if($this->is_admin) {
						$html .= '
							<div class="dp_manage_special_dates" style="display: none;">
								<div class="dp_manage_sd_head">Special Date</div>
								<select autocomplete="off">
									<option value="">None</option>';
									foreach($this->getSpecialDatesList() as $key) {
										$html .= '<option value="'.$key->id.','.$key->color.'" '.($key->id == $special_date_obj->id ? 'selected' : '').'>'.$key->title.'</option>';
									}
						$html .= '
								</select>	
							</div>';
					}
					if($countEvents > 0 && ($this->calendar_obj->show_preview || !empty($this->event_id))) {
						$html .= '
							<div class="eventsPreview">
								<ul>
							';
							if(!empty($this->event_id)) {
								if($booked_date) {
									$html .= '<li>'.$this->translation['TXT_BOOK_ALREADY_BOOKED'].'</li>';
								} else {
									$html .= '<li>';
									
									if($booking_remain) {
									
										$html .= $this->translation['TXT_BOOK_EVENT_PICK_DATE'];
									
									}
									
									if($booking_limit > 0 && $this->calendar_obj->booking_show_remaining) {
										
										if($booking_remain) {
									
											$html .= '<br>';
										
										}
									
										$html .= '<strong>'.($booking_limit - $booking_count).' '.$this->translation['TXT_BOOK_TICKETS_REMAINING'].'.</strong>';
										
									}
									$html .= '</li>';
								}
								
							} else {
								//$result = $this->getEventsByDate($curDate);
								foreach($eventsCurrDate as $event) {
									
									if($event->id == "") 
										$event->id = $event->ID;
									
									$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

									$category_list = get_the_terms( $event->id, 'pec_events_category' ); 
									$category_color = "";
									if(!empty($category_list)) {
										foreach ( $category_list as $cat){
											
											$cat_color = get_term_meta($cat->term_id, 'color', true);

											if(isset($cat_color) && is_numeric($cat_color)) {
												$category_color = $cat_color;
												break;
											}
										}
									}

									$event_color_id = get_post_meta($event->id, 'pec_color', true);
									if($event_color_id == "" && $category_color != "") {
										$event_color_id = $category_color;
									}

									$event_color = $this->getSpecialDatesById($event_color_id);
									

									$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));
															
									$html .= '<li data-dppec-event="'.$event->id.'">';
									if(isset($event->all_day) && $event->all_day) {
										$time = $this->translation['TXT_ALL_DAY'];
										$end_time = "";
									}
									if($this->calendar_obj->show_time && !$event->hide_time) {
										$html .= '<span>'.$time.'</span>';
									}
			
									$html .= '<i '.($event_color != "" ? 'style="background-color:'.$event_color.';padding: 8px;margin-top: 5px;color: #333;"' : '').'>'.$event->title.'</i>';
									$html .= '<div class="dp_pec_clear"></div>';
									if(is_numeric($event->organizer)) {
										$organizer = get_the_title($event->organizer);
										$html .= '<i>'.$this->translation['TXT_ORGANIZED_BY'].'</i> <strong>'.$organizer.'</strong>';
									}
									$html .= '<div class="dp_pec_clear"></div>';
									if($event->location != '') {
				
										if($event->location_address != "") {
											$event->location .= ' <br><span>'.$event->location_address.'</span>';
										}

										$html .= '
										<i><i class="fa fa-map-marker"></i> '.$event->location.'</i>';
									}
									$html .= '<div class="dp_pec_clear"></div>';
									$html .= get_the_post_thumbnail($event->id, 'medium');
									
									$html .= '</li>';
								}
							}
						$html .= '
								</ul>
							</div>';
					}
					
					$html .= '</div>
						</div>';
				}
				
				$general_count++;
			}
			
			if( $this->datesObj->lastDayNum != ($this->calendar_obj->first_day == 1 ? 0 : 6) ) {
				
				for($i = 1; $i <= ( ($this->calendar_obj->first_day == 1 ? 7 : 6) - $this->datesObj->lastDayNum ); $i++) 
				{
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.$i.'</span></div></div>
							</div>';
					
					$general_count++;
					
				}
				
			}
			$html .= '</div>
				<div class="dp_pec_monthly_row_space"></div>
				<div class="dp_pec_clear"></div>';
		
		/*}
		
		if(!$this->is_admin &&
			empty($this->category) &&
			!$this->opts['include_all_events'] &&
			empty($this->opts['location']) &&
			empty($this->event_id) &&
			empty($this->author) &&
			$this->limit_description == 0) {

			$cache = array(
				'calendar_id_'.$this->id_calendar => array(
					$layoutCache => array(
						$month_search => array(
							'html'		  => $html,
							'lastUpdate'  => time()	
						)
					)
				)
			);
			
			if(!$dpProEventCalendar_cache) {
				update_option( 'dpProEventCalendar_cache', $cache);
			} else if($html != "") {
					
				//$dpProEventCalendar_cache[] = $cache;
				$dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search] =  array(
						'html'		  => $html,
						'lastUpdate'  => time()	
					);
					//print_r($dpProEventCalendar_cache);
				update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
			}
		}
		*/
		return $html;
	}
	
	// ************************************* //
	// ****** Daily Calendar Layout ****** //
	//************************************** //
	
	function dailyCalendarLayout($curDate = null) 
	{
		$html = "";
		if(is_null($curDate)) {
			$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($this->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		}
		
		
		if($this->calendar_obj->daily_weekly_layout == 'schedule') {

			$result = $this->getEventsByDate($curDate);
			
			if(!is_array($result)) {
				$result = array();				
			}
			$counter_i = 0;
			for($i = $this->calendar_obj->limit_time_start; $i <= $this->calendar_obj->limit_time_end; $i++) {
				$counter_i++;
				$min = '00';
				$hour = $i;
				if(dpProEventCalendar_is_ampm()) {
					$min = ($i >= 12 ? __('PM', 'dpProEventCalendar') : __('AM', 'dpProEventCalendar'));
					$hour = ($i > 12 ? $i - 12 : $i);
				}
				if($counter_i > 1) {
					$html .= '<div class="dp_pec_monthly_row_space"></div>';
				}
				$html .= '<div class="dp_pec_monthly_row">';

				$html .= '<div class="dp_pec_date first-child">
							<div class="dp_pec_date_item"><div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>';
				
				foreach($result as $event) {
					
					if($event->id == "") 
						$event->id = $event->ID;
					
					$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

					$event_hour = date('G', strtotime($event->date));
					$event_hour_end = ltrim($event->end_time_hh, "0");
	
					if($event_hour_end <= $event_hour) {
						$event_hour_end = $event_hour + 1;
					}
					
					if($event_hour > $i || $event_hour_end <= $i) { continue; }
					
					$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));

					if($this->calendar_obj->show_time && !$event->hide_time && !$event->all_day) {
						//$html .= '<span>'.$time.'</span>';
					}
					$title = $event->title;
					

					$category_list = get_the_terms( $event->id, 'pec_events_category' ); 
					$category_color = "";
					if(!empty($category_list)) {
						foreach ( $category_list as $cat){
							
							$cat_color = get_term_meta($cat->term_id, 'color', true);

							if(isset($cat_color) && is_numeric($cat_color)) {
								$category_color = $cat_color;
								break;
							}
						}
					}

					$event_color_id = get_post_meta($event->id, 'pec_color', true);
					if($event_color_id == "" && $category_color != "") {
						$event_color_id = $category_color;
					}

					$event_color = $this->getSpecialDatesById($event_color_id);
					
					if($this->calendar_obj->link_post) {
								
						$permalink = dpProEventCalendar_get_permalink($event->id);
						$use_link = get_post_meta($event->id, 'pec_use_link', true);
						$href = $permalink;

						if(!$use_link) {
							
							if ( get_option('permalink_structure') ) {
								$permalink_format = rtrim($permalink, '/');
								if(strpos($permalink, "?") !== false) {
									$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
								} else {
									$permalink_query = "";
								}

								$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
								$href = $permalink_format.'/'.strtotime($event->date).$permalink_query;
							} else {
								$href = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
							}
						}

					} else {
						$href = '#';
					}

					$html .= '<a class="dp_daily_event" '.($event_color != "" ? 'style="background-color:'.$event_color.'"' : '').' data-dppec-event="'.$event->id.'" href="'.$href.'" '.($href != '#' ? 'target="'.$this->calendar_obj->link_post_target.'"' : '').' data-dppec-date="'.$curDate.'">'.$title;
					$html .= $this->getBookingBookedLabel($event->id, $event->date);
					$html .= '</a>';
				}
				$html .= '</div></div>
					</div>';
			}
		} else {
			
			$html .= $this->eventsListLayout($curDate, false);
				
		}
		
		$html .= '<div class="dp_pec_clear"></div>';
		return $html;
	}
	
	// ************************************* //
	// ****** Weekly Calendar Layout ****** //
	//************************************** //
	
	function weeklyCalendarLayout($curDate = null) 
	{
		$html = "";
		if(is_null($curDate)) {
			$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($this->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		}
		
		if($this->calendar_obj->first_day == 1) {
			$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
		} else {
			$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
		}
		
		$week_days = array();
		
		$week_days[0] = $weekly_first_date;
		$weekly_one = dpProEventCalendar_date_i18n('d', $weekly_first_date);
		$week_days[1] = strtotime('+1 day', $weekly_first_date);
		$weekly_two = dpProEventCalendar_date_i18n('d', $week_days[1]);
		$week_days[2] = strtotime('+2 day', $weekly_first_date);
		$weekly_three = dpProEventCalendar_date_i18n('d', $week_days[2]);
		$week_days[3] = strtotime('+3 day', $weekly_first_date);
		$weekly_four = dpProEventCalendar_date_i18n('d', $week_days[3]);
		$week_days[4] = strtotime('+4 day', $weekly_first_date);
		$weekly_five = dpProEventCalendar_date_i18n('d', $week_days[4]);
		$week_days[5] = strtotime('+5 day', $weekly_first_date);
		$weekly_six = dpProEventCalendar_date_i18n('d', $week_days[5]);
		$week_days[6] = strtotime('+6 day', $weekly_first_date);
		$weekly_seven = dpProEventCalendar_date_i18n('d', $week_days[6]);
		
		
		$html .= '<div class="dp_pec_monthly_row">';

		if($this->calendar_obj->first_day == 1) {
			if($this->datesObj->firstDayNum == 0) { $this->datesObj->firstDayNum == 7;  }
			$this->datesObj->firstDayNum--;
			
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_monday">
				 	<div class="dp_pec_dayname_item">
						<span>'.$weekly_one.' '.mb_substr($this->translation['DAY_MONDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>';
		} else {
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_sunday">
				 	<div class="dp_pec_dayname_item">
						<span>'.$weekly_one.' '.mb_substr($this->translation['DAY_SUNDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_monday">
					<div class="dp_pec_dayname_item">
						<span>'.$weekly_two.' '.mb_substr($this->translation['DAY_MONDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>';
		}
		$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_tuesday">
					<div class="dp_pec_dayname_item">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_two : $weekly_three).' '.mb_substr($this->translation['DAY_TUESDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_wednesday">
					<div class="dp_pec_dayname_item">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_three : $weekly_four).' '.mb_substr($this->translation['DAY_WEDNESDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_thursday">
					<div class="dp_pec_dayname_item">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_four : $weekly_five).' '.mb_substr($this->translation['DAY_THURSDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_friday">
					<div class="dp_pec_dayname_item">	
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_five : $weekly_six).' '.mb_substr($this->translation['DAY_FRIDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 <div class="dp_pec_dayname dp_pec_dayname_saturday">
					<div class="dp_pec_dayname_item">	
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_six : $weekly_seven).' '.mb_substr($this->translation['DAY_SATURDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>
				 ';
		if($this->calendar_obj->first_day == 1) {
			$html .= '
				 <div class="dp_pec_dayname dp_pec_dayname_sunday">
				 	<div class="dp_pec_dayname_item">
						<span>'.$weekly_seven.' '.mb_substr($this->translation['DAY_SUNDAY'], 0,3, 'UTF-8').'</span>
				 	</div>
				 </div>';
		}
		
		$html .= '</div>';

		$weekly_events_view = array();
		
		if($this->calendar_obj->daily_weekly_layout == 'list') {
			$html .= '<div class="dp_pec_monthly_row">';
		}
		

		for($x = 1; $x <= 7; $x++) {
			
			$html_tmp = "";
			$html_list = "";
			$disabled = "";
			$curDate = date('Y-m-d', $week_days[$x - 1]);
			
			
				if(!($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) || $curDate >= current_time('Y-m-d')) {
					$result = $this->getEventsByDate($curDate);
				}

				if(($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) && $curDate < current_time('Y-m-d')) {
					$disabled = "disabled";
				}

				if(!is_array($result)) {
					$result = array();	
				}
				
				foreach($result as $event) {
	
					if($event->id == "") 
						$event->id = $event->ID;
					
					$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

					if($event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
						continue;
					}
					
					//if(date('G', strtotime($event->date)) != $i) { continue; }
					$event_hour = date('G', strtotime($event->date));
					$event_hour_end = ltrim($event->end_time_hh, "0");
	
					if($event_hour_end <= $event_hour) {
						$event_hour_end = $event_hour + 1;
					}
					
					$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));
					
					$end_time = "";
					if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
					
					if($end_time != "") {
						
						$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

						$end_time = " - ".$end_time_tmp;
						if($end_time_tmp == $time) {
							$end_time = "";	
						}
					}

					$title = $event->title;
					
					$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

					if($this->calendar_obj->daily_weekly_layout == 'list' && $this->calendar_obj->show_time && !$event->hide_time) {
						$title = '<strong>'.$time.$end_time.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '').'</strong><br>'.$title;
					}
					
					$category_list = get_the_terms( $event->id, 'pec_events_category' ); 
					$category_color = "";
					if(!empty($category_list)) {
						foreach ( $category_list as $cat){
							
							$cat_color = get_term_meta($cat->term_id, 'color', true);

							if(isset($cat_color) && is_numeric($cat_color)) {
								$category_color = $cat_color;
								break;
							}
						}
					}

					$event_color_id = get_post_meta($event->id, 'pec_color', true);
					if($event_color_id == "" && $category_color != "") {
						$event_color_id = $category_color;
					}

					$event_color = $this->getSpecialDatesById($event_color_id);
					
					$href = '';
					if($this->calendar_obj->link_post) {
						
						$permalink = dpProEventCalendar_get_permalink($event->id);
						$use_link = get_post_meta($event->id, 'pec_use_link', true);
						$href = $permalink;

						if(!$use_link) {
							if ( get_option('permalink_structure') ) {
								$permalink_format = rtrim($permalink, '/');
								if(strpos($permalink, "?") !== false) {
									$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
								} else {
									$permalink_query = "";
								}

								$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
								$href = $permalink_format.'/'.strtotime($event->date).$permalink_query;
							} else {
								$href = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
							}
						}

					} else {
						$href = '#';
					}

					$html_tmp = '<a class="dp_daily_event" '.($event_color != "" ? 'style="background-color:'.$event_color.'"' : '').' data-dppec-event="'.$event->id.'" href="'.$href.'" target="'.$this->calendar_obj->link_post_target.'" data-dppec-date="'.$curDate.'">'.$title;
					
					$html_tmp .= $this->getBookingBookedLabel($event->id, $event->date);

					$html_tmp .= '</a>';

					if($this->calendar_obj->daily_weekly_layout == 'schedule') {
						
						for($z = $event_hour; $z < $event_hour_end; $z++) {
						
							$weekly_events_view[$x][$z][] = $html_tmp;

						}

						
					}	
					
					$html_list .= $html_tmp;
					
				}
				
			
			// Responsive Layout
			$html .= '<div class="dp_pec_responsive_weekly">';
			$html .= '<div class="dp_pec_clear"></div>
						<div class="dp_pec_dayname">
							<div class="dp_pec_dayname_item">
								<span>'.date('d', strtotime($curDate)).'</span>
							</div>
					 </div>
					 <div class="dp_pec_clear"></div>';
			 $html .= '<div class="dp_pec_date '.$disabled.'"><div class="dp_pec_date_item">';
			if($html_list != "") {
				
					$html .= $html_list;
			
			} else {
				
				$html .= '<span class="pec_no_events_found">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span>';
				
			}
			$html .= '</div></div>';
			$html .= '</div>';
				
			if($this->calendar_obj->daily_weekly_layout == 'list') {
				
				$html .= '<div class="dp_pec_date '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0; margin-right: 0;"' : '').'>';
				$html .= '<div class="dp_pec_date_item">';
				
				if($html_list != "") {
				
					$html .= $html_list;
				
				} else {
					
					$html .= '<span class="pec_no_events_found"></span>';
					
				}
				$html .= '</div>';
				$html .= '</div>';
			}
				
		
		}
		
		if($this->calendar_obj->daily_weekly_layout == 'list') {
			$html .= '</div>';
		}
		
		if($this->calendar_obj->daily_weekly_layout == 'schedule') {
			for($i = $this->calendar_obj->limit_time_start; $i <= $this->calendar_obj->limit_time_end; $i++) {
				$min = '00';
				$hour = $i;
				if(dpProEventCalendar_is_ampm()) {
					$min = ($i >= 12 ? __('PM', 'dpProEventCalendar') : __('AM', 'dpProEventCalendar'));
					$hour = ($i > 12 ? $i - 12 : $i);
				}
				$html .= '
				

				<div class="dp_pec_monthly_row_space">
					<div class="dp_pec_date_weekly_time">
						<div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>
					</div>
				</div>

				<div class="dp_pec_monthly_row">
					
					';
					for($x = 1; $x <= 7; $x++) {
						$disabled = "";
						$curDate = date('Y-m-d', $week_days[$x - 1]);
						
						if(($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) && $curDate < current_time('Y-m-d')) {
							$disabled = "disabled";
						}

						if(isset($weekly_events_view[$x][$i])) {
							$html .= '<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'>
							<div class="dp_pec_date_item">';
							foreach($weekly_events_view[$x][$i] as $z) {
								$html .= $z;
							}
							$html .= '</div></div>';
						} else {
		
							$html .= '
							<div class="dp_pec_date dp_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'><div class="dp_pec_date_item"></div></div>';
			
						}
					
					}
				$html .= '</div>';
			}
		}
		$html .= '<div class="dp_pec_clear"></div>';
		return $html;
	}
	
	function getSpecialDates($date) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "
		SELECT sp.id, sp.color, sp.title
		FROM ". $this->table_special_dates ." sp
		INNER JOIN ". $this->table_special_dates_calendar ." spc ON spc.special_date = sp.`id`
		WHERE spc.calendar = ".$this->id_calendar." AND spc.`date` = '".$date."' ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result[0];
	}
	
	function setSpecialDates( $sp, $date ) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "DELETE FROM ". $this->table_special_dates_calendar ." WHERE calendar = ".$this->id_calendar." AND date = '".$date."'; ";
		$result = $wpdb->query($querystr, OBJECT);
		
		if(is_numeric($sp)) {
			$querystr = "INSERT INTO ". $this->table_special_dates_calendar ." (special_date, calendar, date) VALUES ( ".$sp.", ".$this->id_calendar.", '".$date."' );";
			$result = $wpdb->query($querystr, OBJECT);
		}

		
		return;
	}
	
	function getSpecialDatesList() {
		global $wpdb;
		
		$querystr = "
		SELECT * 
		FROM ". $this->table_special_dates ." sp ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result;
	}
	
	function getSpecialDatesById($id) {
		global $wpdb;
		
		if(!is_numeric($id)) { return ""; }
		
		$querystr = "
		SELECT * 
		FROM ". $this->table_special_dates ." sp 
		WHERE sp.id = ".$id."
		LIMIT 1";
		$result = $wpdb->get_row($querystr, OBJECT);
		
		if(empty($result)) { return ""; }
		
		return $result->color;
	}
	
	function getEventsByDate($date, $count = false) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$this->limit = 0;

		$event_list = $this->upcomingCalendarLayout( true, 5, '', $date . ' 00:00:00', $date . ' 23:59:59' );
		
		return $event_list;
		
		$events = array();
		
		foreach($events_obj as $event) {
			
			if($event->id == "") 
				$event->id = $event->ID;
				
			$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

			$pec_weekly_day = @unserialize($event->pec_weekly_day);
			
			if($event->recurring_frecuency == 2 && is_array($pec_weekly_day)) {
				$original_date = $event->date;
				foreach($pec_weekly_day as $week_day) {
					$day = "";
					
					switch($week_day) {
						case 1:
							$day = "Monday";
							break;	
						case 2:
							$day = "Tuesday";
							break;	
						case 3:
							$day = "Wednesday";
							break;	
						case 4:
							$day = "Thursday";
							break;	
						case 5:
							$day = "Friday";
							break;	
						case 6:
							$day = "Saturday";
							break;	
						case 7:
							$day = "Sunday";
							break;	
					}
					
					if(date('l', strtotime($date)) == $day) {
						$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
						$event->date = date("Y-m-d", strtotime("next ".$day, strtotime($original_date))). ' '.date("H:i:s", strtotime($original_date));
						$events[] = $event;
						
					}
					
				}
			} elseif($event->recurring_frecuency == 3 && $event->pec_monthly_day != "" && $event->pec_monthly_position != "") {
				$original_date = $event->date;
				//echo date('l', strtotime($date));

				if(strtolower(date('Y-m-d', strtotime($date))) == date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date))))) {
					//$event->date = date("Y-m", strtotime($original_date)). '-'.date("d", strtotime($date)). ' '.date("H:i:s", strtotime($original_date));
					//die("OKKK");
					//$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
					//$event->date = date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date));
					//echo date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date))." ".$event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date));
					$events[] = $event;
					
				}
			} else {
				$events[] = $event;
			}
		}
		
		return $events;
	}
	
	function eventsListLayout($date, $return_btn = true) {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = " SET time_zone = '+00:00'";
		$wpdb->query($querystr);
		
		$start_date = $this->parseMysqlDate($date);
			
		$start_date_year = date('Y', strtotime($date));
		$start_date_formatted = str_replace(array(','), '', $start_date);
		$start_date_formatted = trim(str_replace($start_date_year, '', $start_date_formatted), ',./|-');
		
		if(is_numeric($this->columns) && $this->columns > 1) {
			$html .= '
			<div class="'.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"></div>';
		}
		
		$html .= '
		<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap">
			<span class="fa fa-calendar-o"></span>
			<div class="dp_pec_date_block">'.$start_date_formatted.'<span>'.$start_date_year.'</span></div>
			';
			if($return_btn) {
				$html .= '<a href="#" class="dp_pec_date_event_back pec_action_btn dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.$this->translation['TXT_BACK'].'</span></a>';
			}
		$html .= '
		</div>
		<div class="dp_pec_clear"></div>';	
	
		
		$result = $this->getEventsByDate($date);

		if(count($result) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			$html .= $this->singleEventLayout($result, false, $date, true, '', true);
			
		}
		
		return $html;
	}
	
	function getSearchResults($key, $type = '') {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($key)) { return false; }
		
		if($type == '') {
			$html = '
			<div class="dp_pec_date_block_wrap dp_pec_date_event_search dp_pec_isotope">
				<i class="fa fa-search"></i>
				<div class="dp_pec_date_block">'.$this->translation['TXT_RESULTS_FOR'].'</div>
				
				<a href="#" class="dp_pec_date_event_back pec_action_btn dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.$this->translation['TXT_BACK'].'</span></a>
			</div>
			<div class="dp_pec_clear"></div>';
		}

		if($type == 'accordion') {

			$html .= $this->eventsMonthList(null, null, 10, $key);

		} else {

			$result = $this->upcomingCalendarLayout( true, 10, '', null, null, true, false, true, false, false, $key );
			
			if(count($result) == 0) {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			} else {

				$html .= $this->singleEventLayout($result, true, null, true, $type);

			}
		}

		return $html;
	}
	
	function singleEventLayout($result, $search = false, $selected_date = null, $show_end_date = true, $type = '', $force = false) {
		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();

		$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
		if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
			$pagination = $this->opts['pagination'];
		}
		$i = 0;
		
		if(is_object($result)) {
			$result = array($result);
		}
		if(!is_array($result)) {
			$result = array();	
		}
		
		foreach($result as $event) {
			
			if($event->id == "") 
				$event->id = $event->ID;
			
			$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

			if($event->recurring_frecuency == 1){
				
				if(in_array($event->id, $daily_events)) {
					continue;	
				}
				
				$daily_events[] = $event->id;
			}

			$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

			if($selected_date != "" && $event->pec_exceptions != "") {
				$exceptions = explode(',', $event->pec_exceptions);
				
				if($event->recurring_frecuency != "" && in_array($selected_date, $exceptions)) {
					continue;
				}
			}
			
			if($selected_date != "" && $event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($selected_date)) == "0" || date('w', strtotime($selected_date)) == "6")) {
				continue;
			}
			
			if($selected_date != "" && !$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
			) {
				continue;
			}
			
			if($selected_date != "" && !$force && $event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0)) {
				continue;
			}
			
			if($selected_date != "" && $event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
				( !is_int (((date('m', strtotime($selected_date)) - date('m', strtotime(substr($event->orig_date,0,11))))) / ($event->pec_monthly_every)) )
			) {
				continue;
			}

			$i++;
			
			if($this->limit < $i && $this->limit > 0) { break;}
			
			$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));

			$start_day = date('d', strtotime($event->date));
			$start_month = date('n', strtotime($event->date));
			$start_year = date('Y', strtotime($event->date));
			
			$end_date = '';
			$end_year = '';
			$end_month = '';
			$end_day = '';
			if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
				$end_day = date('d', strtotime($event->end_date));
				$end_month = date('n', strtotime($event->end_date));
				$end_year = date('Y', strtotime($event->end_date));
				
				//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
				$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
			}

			if(date("Y-m-d", strtotime($event->date)) == date("Y-m-d", strtotime($event->end_date))) {
				$end_date = '';
			}
			
			//$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
			$start_date = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date));
			
			$end_time = "";
			if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
			
			if($end_time != "" && $show_end_date) {
				
				$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));
				
				$end_time = " - ".$end_time_tmp;
				if($end_time_tmp == $time) {
					$end_time = "";	
				}
			}
			
			
			if($start_year != $end_year && $end_year != "") {
				$start_date .= ', '.$start_year;
			}
			
			if(isset($event->all_day) && $event->all_day) {
				$time = $this->translation['TXT_ALL_DAY'];
				$end_time = "";
			}
			
			$post_thumbnail_id = get_post_thumbnail_id( $event->id );
			$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			
			$title = '<span class="dp_pec_event_title_sp">'.$event->title.'</span>';
			$permalink = "";
			if($this->calendar_obj->link_post) {

				$permalink = dpProEventCalendar_get_permalink($event->id);
				$use_link = get_post_meta($event->id, 'pec_use_link', true);

				if(!$use_link) {
					if ( get_option('permalink_structure') ) {
						$permalink_format = rtrim($permalink, '/');
						if(strpos($permalink, "?") !== false) {
							$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
						} else {
							$permalink_query = "";
						}

						$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
						$permalink = $permalink_format.'/'.strtotime($event->date).$permalink_query;
					} else {
						$permalink = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
					}
				}

				if($selected_date != "") {

					$title = '<a href="'.$permalink.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	

					
				} else {
					$title = '<a href="'.$permalink.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.$selected_date.'</a>';					
				}
			}
			
			$all_working_days = '';
			if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
				$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
			}
			
		
			$html .= '
			<div class="dp_pec_date_event '.($search ? 'dp_pec_date_eventsearch' : '').' dp_pec_isotope '.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'" data-event-number="'.$i.'" '.($i > $pagination ? 'style="display:none;"' : '').'>';

			if($this->type == 'compact') {
				if($post_thumbnail_id && !$this->widget) {
					$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';
				}

				$html .= '
					<div class="dp_pec_content_left">';
				
			}

			if($event->featured_event) {
				$html .= '
				<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>
				<div class="dp_pec_clear"></div>';
			}

			if($post_thumbnail_id && $this->type != 'compact') {
				$html .= '<div class="dp_pec_event_photo">';
				$html .= '<img src="'.$image_attributes[0].'" alt="" />';
				$html .= '</div>';
			}
			
			$edit_button = $this->getEditRemoveButtons($event);
			
			if($search || !empty($this->event)) {
				// To Be Confirmed ?
				if($event->tbc) { 
					$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$this->translation['TXT_TO_BE_CONFIRMED'].'</span>';
					$end_date = "";
				} else {
					$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$start_date.$end_date.'</span>';
					$end_date = "";
				}
			}
				
			$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ? $time.$end_time.$end_date.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');

			if(!post_password_required($event->id)) {
		
				if($pec_time != "" && !$event->tbc) {
					$html .= '
						<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$pec_time.'</span>';
				}

				if(false && $this->calendar_obj->ical_active) {
					$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime(($selected_date != null ? $selected_date : $event->date)) ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
				}

				if($event->link != '' && $this->type == 'compact') {
					$event->link = trim($event->link);
					if(substr($event->link, 0, 4) != "http" && substr($event->link, 0, 4) != "mail") {
						$event->link = 'http://'.$event->link;
					}

					$html .= '
					<a class="dpProEventCalendar_feed" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i>'.$event->link.'</a>';
				}
			}

			$html .= $edit_button.'<div class="dp_pec_clear"></div>';
			
			$html .= '<h2 class="dp_pec_event_title">'.$title.'</h2><div class="dp_pec_clear"></div><div class="dp_pec_clear"></div>';
			
			/*if($this->userHasBooking($event->date, $event->id)) {
				
				$html .= '<p class="dp_pec_fully_booked">'.$this->translation['TXT_BOOKED'].'</p>';
				
			}*/

			if($this->calendar_obj->show_author && $this->type != 'compact') {
				$author = get_userdata(get_post_field( 'post_author', $event->id ));
				$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
			}
			
				
			if($event->location != '' && $this->type != 'compact') {
				
				if($event->location_address != "") {
					$event->location .= ' <br><span>'.$event->location_address.'</span>';
				}

				$html .= '
				<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
			}
			
			if($event->phone != '' && $this->type != 'compact') {
				$html .= '
				<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
			}
			
			$category = get_the_terms( $event->id, 'pec_events_category' ); 
			if(!empty($category) && $this->type != 'compact') {
				$category_count = 0;
				$html .= '
					<span class="dp_pec_event_categories"><i class="fa fa-folder"></i>';
				foreach ( $category as $cat){
					if($category_count > 0) {
						$html .= " / ";	
					}
					$html .= $cat->name;
					$category_count++;
				}
				$html .= '
					</span>';
			}
			
			$cal_form_custom_fields = $this->calendar_obj->form_custom_fields;
			$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

			if(is_array($dpProEventCalendar['custom_fields_counter']) && $this->type != 'compact') {
				$counter = 0;
				
				$html .= '<ul class="pec_event_page_custom_fields_list">';

				foreach($dpProEventCalendar['custom_fields_counter'] as $key) {

					if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
						$counter++;
						continue;
					}

					$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
					if($field_value != "") {
						$html .= '<li class="pec_event_page_custom_fields pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
						if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
							$html .= '<i class="fa fa-check"></i>';
						} else {
							$html .= '<p>'.$field_value.'</p>';
						}
						$html .= '</li>';
					}
					$counter++;		
				}
				$html .= '</ul>';
			}
			
			if($event->link != '' && $this->type != 'compact') {
				$event->link = trim($event->link);
				if(substr($event->link, 0, 4) != "http" && substr($event->link, 0, 4) != "mail") {
					$event->link = 'http://'.$event->link;
				}

				$html .= '
				<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i><span>'.$event->link.'</span></a>
				<div class="dp_pec_clear"></div>';
			}

			if($this->type != 'compact') {
				$booking_booked = $this->getBookingBookedLabel($event->id, ($selected_date != null ? $selected_date : date('Y-m-d', strtotime($event->date))));
				if($booking_booked == "") {
					$html .= $this->getBookingButton($event->id, ($selected_date != null ? $selected_date : date('Y-m-d', strtotime($event->date))));
				} else {
					$html .= $booking_booked;
				}

				$excerpt = get_post_meta($event->id, 'pec_excerpt', true);
				$event_desc = $event->description;
				
			//if($this->limit_description > 0) {
				if($this->limit_description == 0) {
					if($this->widget) {
						$this->limit_description = 60;
					} else {
						$this->limit_description = 150;	
					}
				}

				if($excerpt != "") {
					$event_desc_short = $excerpt;
				} else {
					$event_desc_short = force_balance_tags(html_entity_decode(dpProEventCalendar_trim_words($event_desc, $this->limit_description)));
				}
				//}
				
				$map_id = "";
				if($event->map != '' || is_numeric($event->location_id)) {
					if(is_numeric($event->location_id)) {
						$event->map = get_post_meta($event->location_id, 'pec_venue_map_lnlat', true);
					} else {
						$event->map = get_post_meta($event->id, 'pec_map_lnlat', true);
					}

					$geocode = false;
					if($event->map != "") {
						$event->map = str_replace(" ", "", $event->map);
					} else {
						$geocode = true;
						if(is_numeric($event->location_id)) {
							$venue_address = get_post_meta($event->location_id, 'pec_venue_address', true);
							if($venue_address != "") {
								$event->map = $venue_address;
							} else {
								$event->map = get_post_meta($event->location_id, 'pec_venue_map', true);
							}
						} else {
							$event->map = get_post_meta($event->id, 'pec_map', true);
						}
					}

					$map_id = $event->id.'_'.$this->nonce.'_'.rand();

				}

				$html .= '
					<div class="dp_pec_event_description">';
					if(post_password_required($event->id)) {
						$html .= get_the_password_form();
					} else {
						$html .= '
						<div class="dp_pec_event_description_short" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:block"' : '').'>
							<p>'.do_shortcode(nl2br($event_desc_short)).'</p>
							<a href="'.($permalink == "" ? '#' : $permalink).'" '.($permalink == "" ? '' : 'target="'.$this->calendar_obj->link_post_target.'"').' class="dp_pec_event_description_more" '.($map_id != "" ? 'onclick="setTimeout(initialize_'.$map_id.', 100);"' : '').'>'.$this->translation['TXT_MORE'].'</a>
						</div>
						<div class="dp_pec_event_description_full" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:none"' : '').'>
							'.do_shortcode(nl2br($event_desc));
							
						if($map_id != "") {
							$html .= '<div class="dp_pec_clear dp_map_margin"></div>';
							$html .= $this->getMap($map_id, $event->map, $event->location_id, $geocode);
						}
						$html .= '</div>';
					}

					$html .= '
							</div>';
				
				$html .= $this->getRating($event->id);

				//$html .= '<div class="dp_pec_date_event_icons">';

				//$html .= $this->getEventShare($event);

				//$html .= '</div>';
			} else {
				$html .= '</div>';
				
			}

			$html .= '</div>';
		
		}
		
		if($i > $pagination) {
			$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.$i.'" data-pagination="'.$pagination.'">'.$this->translation['TXT_MORE'].'</a>';
		}
		
		return $html;
	}
	
	function upcomingCalendarLayout( 
		$return_data = false, 
		$limit = '', 
		$limit_description = '', 
		$events_month = null, 
		$events_month_end = null, 
		$show_end_date = true, 
		$filter_author = false, 
		$auto_limit = true, 
		$filter_map = false, 
		$past = false, 
		$keyword = '',
		$use_featured = true,
		$start_date_from  = ''
	) {
		global $wpdb, $dpProEventCalendar, $dpProEventCalendar_cache;
		
		$pec_cache_id = "";
		$html = "";
		
		$list_limit = $this->limit;
		if(is_numeric($limit)) {
			$list_limit = $limit;	
		}
		
		if(is_numeric($limit_description)) {
			$this->trim_words = $limit_description;	
		}

		if($start_date_from == '') {
			$current_time_from = current_time('mysql');
			$current_time_from_all_day = date('Y-m-d'). " 00:00:00";
		} else {
			$current_time_from = $start_date_from;
			$current_time_from_all_day = $start_date_from;
		}

		//$current_time_from = $current_time_from_all_day;
		$querystr = "SET SQL_BIG_SELECTS = 1";
		$wpdb->query($querystr);
		
		$args = array( 
			'posts_per_page' 	=> -1, 
			'post_type'			=> 'pec-events',
			'post_status'		=> array('publish', 'private'),
			'meta_key'			=> 'pec_date',
			'order'				=> 'ASC',
			'lang'				=> (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
			'orderby'			=> 'meta_value',
			'suppress_filters'  => false
		);
		
		if(!empty($this->category)) {

			$cat_arr = explode(",",$this->category);
			$cat_slug = array();
			foreach($cat_arr as $key) {

				if(is_numeric($key)) {
					$category_slug = get_term_by('term_id', (int)$key, 'pec_events_category');
					$cat_slug[] = $category_slug->slug;
				}
			}

			$args['pec_events_category'] = implode(",", $cat_slug);

			//$args['taxonomy'] = "pec_events_category";	
			//$args['term'] = $category_slug->slug;
			
			/*$args['tax_query'] = array(
				array(
					'taxonomy' => 'pec_events_category',
					'field' => 'term_id',
					'term' => $this->category
				)
			);*/
		}
		
		if(!is_null($events_month)) {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => array($events_month, $events_month_end),
					'compare' => 'BETWEEN',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
				
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr($events_month, 0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
			);

			// To Be Confirmed?
			$args['meta_query'][] = array(
				array(
					'key'     => 'pec_tbc',
					'compare' => 'NOT EXISTS'
				)
			);
			
		} elseif($past) {

			$args['meta_query'][] = array(
				'key'     => 'pec_date',
				'value'   => current_time('mysql'),
				'compare' => '<=',
				'type'    => 'DATETIME'
			);
			
		} else {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => $current_time_from,
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '>'
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				)
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr(current_time('mysql'),0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '=',
					'type'    => 'NUMERIC'
				)
			);
		}
		
		
		if(!empty($this->event_id)) {
			
			$args['p'] = $this->event_id;
		}
		
		if(!empty($this->author)) {
			
			$args['author'] = $this->author;
		}
		
		if($filter_author) {
			
			if(is_author()) {
				global $author_name, $author;
				$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
			} else {
				$curauth = get_userdata(intval($author));
			}
			
			if(is_numeric($this->author) && $this->author > 0) {			

				$args['author'] = $this->author;

			} else {

				$args['author'] = $curauth->ID;

			}
		}

		if($keyword != "") {

			$args['s'] = $keyword;

		}

		if(!empty($this->opts['location'])) {
			$args['meta_query'][] = array(
				'key'     => 'pec_location',
				'value'   => $this->opts['location']
			);
		}

		if($filter_map) {

			$args['meta_query'][] = array(
				array(
					'key'     => 'pec_location',
					'value'   => 0,
					'compare' => '>',
					'type'	  => 'numeric'
				)
			);
			
		}
		
		// Check Calendar ID
		
		// XXXXXXXXXX
		if($this->opts['include_all_events'] != 1) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_id_calendar',
					'value'   => $this->id_calendar,
					'compare' => 'LIKE'
				)
			);
		}

		$pec_cache_id = serialize($args);
		$pec_cache_id = md5($pec_cache_id);
		$loaded_from_cache = false;
		$order_events = array();
		$events_obj = array();


		if(!$this->is_admin &&
			!is_null($events_month) &&
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]) && 
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$pec_cache_id]) && 
			$this->calendar_obj->cache_active) {
			
			$loaded_from_cache = true;
			if($return_data) {
				return $dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$pec_cache_id];
			} else {
				$order_events = $dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$pec_cache_id];
				$events_obj = $order_events;
			}

		}

		if(empty($events_obj)) {

			//$events_obj = $wpdb->get_results($querystr);
			$events_obj = get_posts( $args );
		}


		//echo '<!--';
		//echo '<pre>';
		//print_r($args);
		//echo '</pre>';
		//print_r($events_obj);
		//echo $GLOBALS['wp_query']->request;
		//echo '-->';
		if(count($events_obj) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
			
		} else {

			if(empty($order_events)) {
				$daily_events_total = 0;


				foreach($events_obj as $event) {


					$event = $this->getEventData($event->ID);

					$event->ID = $event->id;
					$is_featured = false;

					if ( get_post_status ( $event->ID ) == 'private' ) {
						if(is_user_logged_in()) {
						    $current_user = wp_get_current_user();
						    $is_author = false;
						    if($current_user->ID == get_post_field( 'post_author', $event->ID)) { $is_author = true; }

						    if( ! current_user_can('administrator') || ! $is_author ) {
						        continue;
						    }
						} else {
							continue;
						}


					}	

					$pec_calendars = explode(',', $event->id_calendar);	

					if(!in_array($this->id_calendar, $pec_calendars) && $this->opts['include_all_events'] != 1) {
						continue;
					}
					
					$pec_extra_dates = explode(',', $event->pec_extra_dates);	
					if(!is_array($pec_extra_dates)) {
						$pec_extra_dates = array();
					}

					if($event->recurring_frecuency > 0) {
						
						$enddate_orig = $event->end_date." 23:59:59";
						if(isset($event->all_day) && $event->all_day) {
							$startdate_orig = $current_time_from_all_day;
						} else {
							$startdate_orig = $current_time_from;	
						}
						if(!is_null($events_month)) {
							$startdate_orig = $events_month;
							$enddate_orig = $events_month_end;
						}
						if($past) {
							$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
							$enddate_orig =  current_time('mysql');
							//$startdate_orig = $enddate_orig;
						}

						switch($event->recurring_frecuency) {
							case 1:
								$k = 1;
								
								$startdate = $event->date;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$startdate = date('Y-m-d', strtotime($startdate_orig)). ' ' .date('H:i:s', strtotime($event->date));
										
								}
								
								
								
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)) - 1 +$k, date("y", strtotime($startdate))));

								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")
										) 
										|| $event->end_date == '0000-00-00'
										|| $event->end_date == '') 
										&& (strtotime($eventdate) <= strtotime($enddate_orig))) {	
									
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}
								//echo "whie 1<br>";
								
								//for($i = 1; $i <= $list_limit; $i++) {
														
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)) - 1 +$k, date("y", strtotime($startdate))));
									//echo "DONE DAILY";
									
									if(!$event->pec_daily_working_days && $event->pec_daily_every > 1 && 
										( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
									) {
										$i--;
											$k++;
										continue;
									}
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										$exceptions = explode(',', $event->pec_exceptions);
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											$i--;
											$k++;
											continue;
										}
									}

									if(
										(
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")
										) 
										|| $event->end_date == '0000-00-00'
										|| $event->end_date == '') 
										&& (strtotime($eventdate) >= strtotime($startdate_orig) && strtotime($eventdate) <= strtotime($enddate_orig))
									) {
										//&& (strtotime(substr($eventdate,0, 10)) >= strtotime(substr($startdate_orig,0, 10)) && strtotime($eventdate) <= strtotime($enddate_orig))
										$order_events[strtotime($eventdate).$event->ID] = new stdClass;
										//$order_events[strtotime($eventdate).$event->ID] = $event;
										$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
										$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

										$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
										if(!$is_featured && $use_featured) {
											$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
											$is_featured = true;
										}

										$daily_events_total++;
									} elseif(strtotime($eventdate) < strtotime($startdate_orig)) {
										$i--;
									}
									$k++;
								}
								break;
							case 2:
								
								$k = 1;
								$startdate = $event->date;
								$weeksdiff = 0;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$weeksdiff = dpProEventCalendar_datediffInWeeks($startdate, $startdate_orig);
									//echo "weeksdiff : ".$weeksdiff;
									$startdate = date("Y-m-d H:i:s", strtotime('+'.($weeksdiff - 1).' weeks', strtotime($startdate)));
											
								}
								
								$pec_weekly_day = $event->pec_weekly_day;
								
								if(is_array($pec_weekly_day)) {
									
									$event_date = $startdate;
									$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($event_date)), date("i", strtotime($event_date)), 0, date("m", strtotime($event_date)), date("d", strtotime($event_date)), date("y", strtotime($event_date))) - (86400 * 7));
									$original_date = $event->date;


									$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
								
									//echo "DONE WEEKLY 1";
									
									$eventdate = 0;
									$i = 0;
									while((
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
										) {	
										
										//echo "whie 2<br>";
										$i++;

										if(is_null($events_month) && $i >= $list_limit) {
											break;
										}


									//for ($i = 1; $i <= $list_limit; $i++) {
										foreach($pec_weekly_day as $week_day) {
											$day = "";
											switch($week_day) {
												case 1:
													$day = "Monday";
													break;	
												case 2:
													$day = "Tuesday";
													break;	
												case 3:
													$day = "Wednesday";
													break;	
												case 4:
													$day = "Thursday";
													break;	
												case 5:
													$day = "Friday";
													break;	
												case 6:
													$day = "Saturday";
													break;	
												case 7:
													$day = "Sunday";
													break;	
											}
											
											if($weeksdiff == 0 && $week_day > 1 && $week_day < date('N', strtotime($original_date))) {
												
												//continue;	
											}
											
											$event_date = date("Y-m-d H:i:s", strtotime("next ".$day, strtotime($original_date)));
											
											$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($event_date))));
											
											$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
											$last_day = $eventdate;										
											if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
												break(2);	
											}
	
											if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
												$i--;
												continue;	
											}

											if(strtotime(($eventdate)) < strtotime($event->date)) {
												$i--;
												continue;	
											}
											
											if($eventdate != "" && $event->pec_exceptions != "") {
												$exceptions = explode(',', $event->pec_exceptions);
												if(in_array(substr($eventdate, 0, 10), $exceptions)) {
													continue;
												}
											}
											

											if($event->pec_weekly_every > 1) {

												$weeksdiff2 = dpProEventCalendar_datediffInWeeks($eventdate, $event->date);

												if( $weeksdiff2 % ($event->pec_weekly_every) != 0 ) {
												//$i--;
												continue;
												}

											}
											
											
											if(
												(
													(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
													|| $event->end_date == '0000-00-00' 
													|| $event->end_date == ''
												) 
												&& (strtotime($eventdate) >= strtotime($startdate_orig))
											) {
												$order_events[strtotime($eventdate).$event->ID] = new stdClass;
												//$order_events[strtotime($eventdate).$event->ID] = $event;
												$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
												$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
												
												$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
												if(!$is_featured && $use_featured) {
													$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
													$is_featured = true;
												}

												
											}
											
										}

										$k++;
									}

								} else {
									$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)), date("y", strtotime($startdate))) - 86400);

									$eventdate = 0;
									$i = 0;
									while((
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
										) {	
										
										//echo "whie 3<br>";
										$i++;

										if(is_null($events_month) && $i >= $list_limit) {
											break;
										}

									//for($i = 1; $i <= $list_limit; $i++) {
									//echo "DONE WEEKLY 2";
										$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($startdate))));
										$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
										$last_day = $eventdate;
										
												
										if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
											break;	
										}
										
										if($eventdate != "" && $event->pec_exceptions != "") {
											$exceptions = explode(',', $event->pec_exceptions);
											
											if(in_array(substr($eventdate, 0, 10), $exceptions)) {
												$i--;
												continue;
											}
										}
		
										if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
											$i--;
											continue;	
										}
										
										if($event->pec_weekly_every > 1 && 
											( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
										) {
											//echo "DATEDIFF: ".(strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24);
											//$i--;
											continue;
										}
		
										if(
											(
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
											&& (strtotime($eventdate) >= strtotime($startdate_orig))
										) {

											$order_events[strtotime($eventdate).$event->ID] = new stdClass;
											//$order_events[strtotime($eventdate).$event->ID] = $event;
											$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
											$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

											$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
											if(!$is_featured && $use_featured) {
												$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
												$is_featured = true;
											}
										}
									}
									$k++;

								}

								break;
							case 3:
								$k = 1;
								$startdate = $event->date;
								
								if(strtotime($startdate) < strtotime($startdate_orig)) {
									
									$startdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($startdate_orig)), date("d", strtotime($event->date)), date("y", strtotime($startdate_orig))));
										
								}
								
								$counter_m = 1;
								if(isset($events_month) || ($event->pec_monthly_day != "" && $event->pec_monthly_position != "")) {
									$counter_m = 0;	
								}

								$eventdate = 0;
								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
									) {	
									
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}

								//for($i = 1; $i <= $list_limit; $i++) {
									//echo "DONE MONTHLY";
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate))+((strtotime($startdate) < time() && !isset($events_month)) || $k > 1 ? $counter_m : 0), date("d", strtotime($startdate)), date("y", strtotime($startdate))));
									
									//echo "whie 4 ".$eventdate." - ".$events_month."<br>";

									
									//$html .= $event->pec_monthly_day. " - " .$event->pec_monthly_position;
									if($event->pec_monthly_day != "" && $event->pec_monthly_position != "") {
										
										$eventdate = str_replace(substr($eventdate, 5, 5), date('m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($eventdate)))), $eventdate);
										
										/*if($eventdate != "" && $event->pec_exceptions != "") {
											
											if(in_array(substr($eventdate, 0, 10), $exceptions)) {
												// X NO $i--;
												$counter_m++;
												continue;
											}
											
										}*/
										
										if(strtotime(($eventdate)) > strtotime($enddate_orig) && ($event->end_date != '0000-00-00' && $event->end_date != '')) {
											break;	
										}
										//$html .= $eventdate."XXX";

									}
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										
										$exceptions = explode(',', $event->pec_exceptions);
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											//X NO $i--;
											
											if(isset($events_month)) {
												break;
											}
											
											$counter_m++;
											continue;
										}
										
									}

									if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
										break;	
									}
									
									if($event->pec_monthly_every > 1) {

										if(( !is_int (((date('m', strtotime($eventdate)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)))) {

											if(isset($events_month)) {
												break;
											}
											//No
											//$i--;
											$counter_m++;
											continue;
										}
									}
									
									if(strtotime($startdate) < current_time('timestamp') || $i > 1) {
										$counter_m++;
									}
									if(
										(
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59"))
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
										&& (strtotime($eventdate) >= strtotime($startdate_orig))
									) {

										if(!isset($order_events[strtotime($eventdate).$event->ID])) {
											$order_events[strtotime($eventdate).$event->ID] = new stdClass;
											//$order_events[strtotime($eventdate).$event->ID] = $event;
											$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
											$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

											
											$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
											
											if(!$is_featured && $use_featured) {
												
												$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
												$is_featured = true;
											}
										}
									}
									$k++;
								}
								break;	
							case 4:
								$k = 1;
								$counter_y = 1;
								if(isset($events_month)) {
									$counter_y = 0;	
								}

								$eventdate = 0;
								$i = 0;
								while((
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
									) {	
									
									//echo "whie 5 - ".$eventdate."<br>";
									$i++;

									if(is_null($events_month) && $i >= $list_limit) {
										break;
									}

								//for($i = 1; $i <= $list_limit; $i++) {
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($event->date)), date("d", strtotime($event->date)), date("Y", strtotime($event->date))+((strtotime($event->date) < time() && !isset($events_month)) || $k > 1 ? $counter_y : 0)));
									//echo "K: ".$k."<br>";
									//echo "Event date : ".(date("Y", strtotime($event->date))+((strtotime($event->date) < time() && !isset($events_month)) || $k > 1 ? $counter_y : 0))."<br>";
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										$exceptions = explode(',', $event->pec_exceptions);
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											$i--;
											continue;
										}
									}
									
									if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
										break;	
									}
								
									if(strtotime($event->date) < time() || $i > 1) {
										$counter_y++;
										//echo "Counter ".$counter_y." <br>";
										//echo $event->date. ' - '.$eventdate.'<br><br>';

									}
									if(((strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) || $event->end_date == '0000-00-00' || $event->end_date == '') && (strtotime($eventdate) >= strtotime($startdate_orig))) {
										$order_events[strtotime($eventdate).$event->ID] = new stdClass;
										//$order_events[strtotime($eventdate).$event->ID] = $event;
										$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
										$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;

										$order_events[strtotime($eventdate).$event->ID]->featured_event = "";
										if(!$is_featured && $use_featured) {
											$order_events[strtotime($eventdate).$event->ID]->featured_event = $event->featured_event;
											$is_featured = true;
										}
									}
									$k++;
								}
								
								break;
						}
						
					} else {

						$enddate_orig = $event->end_date." 23:59:59";
						if(isset($event->all_day) && $event->all_day) {
							$startdate_orig = $current_time_from_all_day;
						} else {
							$startdate_orig = $current_time_from;	
						}
						if(!is_null($events_month)) {
							$startdate_orig = $events_month;
							$enddate_orig = $events_month_end;
						}
						
						$continue = 0;
						
						if($past) {
							//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
							$enddate_orig =  current_time('mysql');
							$startdate_orig = $enddate_orig;
							
							if(strtotime(($event->date)) > strtotime($startdate_orig)) {
								$continue = 1;	
							}

						} else {
							
							if(!is_null($events_month)) {
								if(substr($event->date,0,10) < substr($startdate_orig,0,10) || substr($event->date,0,10) > substr($enddate_orig,0,10)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									$continue = 1;	
								}
							} else {
								if(strtotime(($event->date)) < strtotime($startdate_orig)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									$continue = 1;	
								}
							}

						}
						
						if(!$continue) {
							/*if($use_featured) {
								if($event->featured_event) {
									$is_featured = true;
								}
							} else {
								$event->featured_event = "";	
							}*/

							//$order_events[strtotime($event->date).$event->ID] = $event;
							$order_events[strtotime($event->date).$event->ID] = new stdClass;
							//$order_events[strtotime($event->date).$event->ID] = $event;
							$order_events[strtotime($event->date).$event->ID]->id = $event->ID;
							$order_events[strtotime($event->date).$event->ID]->date = $event->date;

							$order_events[strtotime($event->date).$event->ID]->featured_event = "";
							if($use_featured) {
								$order_events[strtotime($event->date).$event->ID]->featured_event = $event->featured_event;
								$is_featured = true;
							}
						}
					}
					

					if(!empty($pec_extra_dates)) {
						foreach($pec_extra_dates as $extra_date) {
							$extra_date = trim($extra_date);
							if($extra_date == "") {
								continue;
							}
							if(strlen(trim($extra_date)) <= 12) {
								$extra_date = $extra_date . ' ' . date('H:i:s', strtotime($event->date));
							}
							
							$enddate_orig = $event->end_date." 23:59:59";
							if(isset($event->all_day) && $event->all_day) {
								$startdate_orig = $current_time_from_all_day;
							} else {
								$startdate_orig = $current_time_from;	
							}
							if(!is_null($events_month)) {
								$startdate_orig = $events_month;
								$enddate_orig = $events_month_end;
							}
							
							if($past) {
								//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
								$enddate_orig =  current_time('mysql');
								$startdate_orig = $enddate_orig;
								
								if(strtotime(($extra_date)) > strtotime($startdate_orig)) {
									continue;	
								}
		
							} else {
								
								if(!is_null($events_month)) {
									
									if(substr($extra_date,0,10) < substr($startdate_orig,0,10) || substr($extra_date,0,10) > substr($enddate_orig,0,10)) {
										//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
										continue;	
									}
									
								} else {
									if(strtotime(($extra_date)) < strtotime($startdate_orig)) {
										//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
										continue;	
									}	
								}
		
							}
							
							$order_events[strtotime($extra_date).$event->ID] = new stdClass;
							//$order_events[strtotime($extra_date).$event->ID] = $event;
							$order_events[strtotime($extra_date).$event->ID]->id = $event->ID;
							$order_events[strtotime($extra_date).$event->ID]->date = $extra_date;

							$order_events[strtotime($extra_date).$event->ID]->featured_event = "";
							if(!$is_featured && $use_featured) {
								$order_events[strtotime($extra_date).$event->ID]->featured_event = $event->featured_event;
								$is_featured = true;
							}
						}
					}
				}
			}
			

			if(!function_exists('dp_pec_cmp')) {
				function dp_pec_cmp($a, $b) {

					if ($a->featured_event) {
						if ($b->featured_event) {
							$a = strtotime($a->date);
							$b = strtotime($b->date);

							if ($a == $b) {
								return 0;
							}
							return ($a < $b) ? -1 : 1;
						}
						return -1;
					}
					if ($b->featured_event) {

						return 1;
					}

					$a = strtotime($a->date);
					$b = strtotime($b->date);

					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? -1 : 1;
				}
			}
			
			if(!function_exists('dp_pec_cmp_reverse')) {
				function dp_pec_cmp_reverse($a, $b) {
					$a = strtotime($a->date);
					$b = strtotime($b->date);
					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? 1 : -1;
				}
			}
			
			if($past) {
				usort($order_events, "dp_pec_cmp_reverse");
			} else {
				usort($order_events, "dp_pec_cmp");
			}
			
			//ksort($order_events, SORT_NUMERIC);

			if(!$this->is_admin &&
				!$loaded_from_cache &&
				!is_null($events_month) &&
				isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]) && 
				!isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$pec_cache_id]) && 
				$this->calendar_obj->cache_active) {

				$cache = array(
					'calendar_id_'.$this->id_calendar => array(
						$pec_cache_id => $order_events
					)
				);
				
				if(!$dpProEventCalendar_cache) {
					update_option( 'dpProEventCalendar_cache', $cache);
				} else {
				//} else if(!empty($order_events)) {
						
					//$dpProEventCalendar_cache[] = $cache;
					$dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$pec_cache_id] = $order_events;
						//print_r($dpProEventCalendar_cache);
					update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
				}
			}


			if($return_data) {
				if($limit != '' && $auto_limit && is_null($events_month)) { $order_events = array_slice($order_events, 0, ($limit + $daily_events_total)); }

				return $order_events;
			}
			
			$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
			if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
				$pagination = $this->opts['pagination'];
			}

			$event_counter = 1;
			$event_columns_counter = 0;
			
			if(empty($order_events)) {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			} else {
				$event_reg = array();
				
				$html .= "<div class='dp_pec_clear'></div>
				<div class='".(is_numeric($this->columns) && $this->columns > 1 ? 'pec_upcoming_layout' : '')."'>";
				
				$last_date = "";
				$daily_events = array();
				foreach($order_events as $event) {
					
					if($event->id == "") 
						$event->id = $event->ID;

					$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

					if($event_counter > $list_limit && is_null($events_month)) { break; }

					if($event->recurring_frecuency == 1){
						
						if(in_array($event->id, $daily_events)) {
							continue;	
						}
						
						$daily_events[] = $event->id;
					}

					$all_working_days = '';
					if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
						$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
						$event->date = $event->orig_date;
					}
					
					if($this->columns == "")  {
						
						$html .= "<div class='clear'></div>";
						
						$event_columns_counter = 0;
						
					}
					
					$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));
					
					$start_day = date('d', strtotime($event->date));
					$start_month = date('n', strtotime($event->date));
					
					$end_date = '';
					if($event->end_date != "" && $event->end_date != "0000-00-00" && $show_end_date) {
						$end_day = date('d', strtotime($event->end_date));
						$end_month = date('n', strtotime($event->end_date));
						
						//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3);
						$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));

						if(date("Y-m-d", strtotime($event->date)) == date("Y-m-d", strtotime($event->end_date))) {
							$end_date = '';
						}
					}
					
					

					//$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
					$start_date = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date));
					
					if($start_date == $end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3)) { $end_date = ""; }
					if($event->recurring_frecuency != 1) {
						$end_date = "";
					} elseif(in_array($event->id, $event_reg) && ($this->columns > 1)) {
						continue;	
					}
					
					$end_time = "";
					if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
					
					if($end_time != "") {
						
						$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

						$end_time = " / ".$end_time_tmp;
						if($end_time_tmp == $time) {
							$end_time = "";	
						}
					}
					
					if(isset($event->all_day) && $event->all_day) {
						$time = $this->translation['TXT_ALL_DAY'];
						$end_time = "";
					}
					
					if(date('Y-m-d', strtotime($event->date)) != $last_date) {
						$last_date = date('Y-m-d', strtotime($event->date));
						$start_date_year = date('Y', strtotime($event->date));
						$start_date_formatted = str_replace(array(','), '', $start_date);
						$start_date_formatted = trim(str_replace($start_date_year, '', $start_date_formatted), ',./|-');
						
						if(is_numeric($this->columns) && $this->columns > 1 && $event_counter == 1 && false) {
							$html .= '
							<div class="'.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"></div>';
						}
						//<span class="dpProEventCalendar_feed"><i class="fa fa-calendar-o"></i>'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)) .'</span>
						if(!is_numeric($this->columns) || $this->columns <= 1) {
							$html .= '
							<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
								<span class="fa fa-calendar-o"></span>
								<div class="dp_pec_date_block">'.$start_date_formatted.'<span>'.$start_date_year.'</span></div>
							</div>
							<div class="dp_pec_clear"></div>';	
						}
					}
					
					$html .= '
					<div class="dp_pec_isotope '.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'" data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
						<div class="dp_pec_date_event dp_pec_upcoming">';
					
					if($event->featured_event) {
						$html .= '
						<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>
						<div class="dp_pec_clear"></div>';
					}

					$post_thumbnail_id = get_post_thumbnail_id( $event->id );
					$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, (is_numeric($this->columns) && $this->columns > 2 ? 'medium' : 'large') );
					if($post_thumbnail_id) {
						$html .= '<div class="dp_pec_event_photo">';
						$html .= '<img src="'.$image_attributes[0].'" alt="" />';
						$html .= '</div>';
					}
					
					$edit_button = $this->getEditRemoveButtons($event);
					
					//<a href="'.dpProEventCalendar_get_permalink($event->id).'"></a>
					if($end_date == ' - '.$start_date) {
						$end_date = '';	
					}
					
					if((is_numeric($this->columns) && $this->columns > 1) || $event->tbc || $this->opts['force_dates'] || ($event->recurring_frecuency == 1	)) {
						if($event->tbc) {
							$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$this->translation['TXT_TO_BE_CONFIRMED'].'</span>';
						} else {
							$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$start_date.$end_date.'</span>';
						}
					}

					$event_timezone = dpProEventCalendar_getEventTimezone($event->id);
					

					$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');
					if($pec_time != "" && !$event->tbc) {
						$html .= '<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$pec_time.'</span>';
					}

					$html .= $edit_button.'<div class="dp_pec_clear"></div>';
					
					$title = '<span class="dp_pec_event_title_sp">'.$event->title.'</span>';
					$permalink = "";
					if($this->calendar_obj->link_post) {
						$permalink = dpProEventCalendar_get_permalink($event->id);
						$title = '<a href="'.$permalink.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	
					}
					
					$html .= $this->getRating($event->id);
					
					$html .= '
						<h2 class="dp_pec_event_title">'.$title.'</h2><div class="dp_pec_clear"></div>';
				
					if($this->calendar_obj->show_author) {
						$author = get_userdata(get_post_field( 'post_author', $event->id ));
						$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
					}
					
					if($event->location != '') {
						if($event->location_address != "") {
							$event->location .= ' <br><span>'.$event->location_address.'</span>';
						}

						$html .= '
						<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
					}
					
					if($event->phone != '') {
						$html .= '
						<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
					}
					
					$category = get_the_terms( $event->id, 'pec_events_category' ); 
					if(!empty($category)) {
						$category_count = 0;
						$html .= '
							<span class="dp_pec_event_categories"><i class="fa fa-folder"></i>';
							
						foreach ( $category as $cat){
							if($category_count > 0) {
								$html .= " / ";	
							}
							$html .= $cat->name;
							$category_count++;
						}
						$html .= '
							</span>';
					}
					
					$cal_form_custom_fields = $this->calendar_obj->form_custom_fields;
					$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

					if(is_array($dpProEventCalendar['custom_fields_counter'])) {
						$counter = 0;
						
						$html .= '<ul class="pec_event_page_custom_fields_list">';

						foreach($dpProEventCalendar['custom_fields_counter'] as $key) {

							if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
								$counter++;
								continue;
							}

							$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
							if($field_value != "") {
								$html .= '<li class="pec_event_page_custom_fields pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
								if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
									$html .= '<i class="fa fa-check"></i>';
								} else {
									$html .= '<p>'.$field_value.'</p>';
								}
								$html .= '</li>';
							}
							$counter++;		
						}
						$html .= '</ul>';
					}

					if($event->link != '') {
						$event->link = trim($event->link);
						if(substr($event->link, 0, 4) != "http" && substr($event->link, 0, 4) != "mail") {
							$event->link = 'http://'.$event->link;
						}

						$html .= '
						<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i><span>'.$event->link.'</span></a>
						<div class="dp_pec_clear"></div>';
					}
					
					$excerpt = get_post_meta($event->id, 'pec_excerpt', true);
					$event_desc = $event->description;
					
					$booking_booked = $this->getBookingBookedLabel($event->id, $event->date);
					if($booking_booked == "") {
						$html .= $this->getBookingButton($event->id, date('Y-m-d', strtotime($event->date)));
					} else {
						$html .= $booking_booked;
					}

					//if($this->limit_description > 0) {
					if($this->limit_description == 0) {
						$this->limit_description = 30;
					}

					if($excerpt != "") {
						$event_desc_short = $excerpt;
					} else {
						$event_desc_short = force_balance_tags(html_entity_decode(dpProEventCalendar_trim_words($event_desc, $this->limit_description)));
					}
						
					//}
					
					$map_id = "";
					if($event->map != '' || is_numeric($event->location_id)) {
						if(is_numeric($event->location_id)) {
							$event->map = get_post_meta($event->location_id, 'pec_venue_map_lnlat', true);
						} else {
							$event->map = get_post_meta($event->id, 'pec_map_lnlat', true);
						}

						$geocode = false;
						if($event->map != "") {
							$event->map = str_replace(" ", "", $event->map);
						} else {
							$geocode = true;
							if(is_numeric($event->location_id)) {
								$venue_address = get_post_meta($event->location_id, 'pec_venue_address', true);
								if($venue_address != "") {
									$event->map = $venue_address;
								} else {
									$event->map = get_post_meta($event->location_id, 'pec_venue_map', true);
								}
							} else {
								$event->map = get_post_meta($event->id, 'pec_map', true);
							}
						}

						$map_id = $event->id.'_'.$this->nonce.'_'.rand();

					}

					$html .= '
						<div class="dp_pec_event_description">';

						if(post_password_required($event->id)) {
							$html .= get_the_password_form();
						} else {

						$html .= '
							<div class="dp_pec_event_description_short" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:block"' : '').'>
								<p>'.do_shortcode(nl2br($event_desc_short)).'</p>
								<a href="'.($permalink == "" ? '#' : $permalink).'" '.($permalink == "" ? '' : 'target="'.$this->calendar_obj->link_post_target.'"').' class="dp_pec_event_description_more" '.($map_id != "" ? 'onclick="setTimeout(initialize_'.$map_id.', 100);"' : '').'>'.$this->translation['TXT_MORE'].'</a>
							</div>
							<div class="dp_pec_event_description_full" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:none"' : '').'>
								'.do_shortcode(nl2br($event_desc));
							if($map_id != "") {
								$html .= '<div class="dp_pec_clear dp_map_margin"></div>';
								$html .= $this->getMap($map_id, $event->map, $event->location_id, $geocode);
							}
							$html .= '
									</div>';
						}

					$html .= '
						</div>';
					
					$html .= '
						<div class="dp_pec_date_event_icons">';
					
					//$html .= $this->getEventShare($event);
					
					
					$html .= '
							</div>';
							
					if($this->calendar_obj->ical_active && !post_password_required($event->id)) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a><br class='clear' />";
					}
					$html .= '
						</div>
					</div>';
					$event_reg[] = $event->id;
					$event_counter++;
				}
				
				$html .= "</div>";	
				
				if(($event_counter - 1) > $pagination) {
					$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.$this->translation['TXT_MORE'].'</a>
						<div class="dp_pec_clear"></div>
					';
				}
			}
		}

		return $html;
	}
	
	function parseMysqlDate($date) {
		
		$dateArr = explode("-", $date);
		//$newDate = $dateArr[2] . " " . $this->translation['MONTHS'][($dateArr[1] - 1)] . ", " . $dateArr[0];
		$newDate = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($date));
		return $newDate;
	}
	
	function addScripts( $print = false, $commented = false, $hidden = false ) 
	{
		global $dpProEventCalendar;
		
		$script = '';
		if($commented) {
			$script .= $this->addScripts(false, false, true);	
		}
		$script .= '<script type="text/javascript">
		// <![CDATA[
		';
		if($commented) {
			$script .= ' /* PEC Commented Script';	
		}
		$map_lat = 0;
		$map_lng = 0;

		if(isset($dpProEventCalendar['map_default_latlng']) && $dpProEventCalendar['map_default_latlng'] != "") {
			
			$map_lnlat = explode(",", $dpProEventCalendar['map_default_latlng']);
			if(is_numeric($map_lnlat[0]) && is_numeric($map_lnlat[1])) {
				$map_lat = $map_lnlat[0];
				$map_lng = $map_lnlat[1];
			}
		}

		$script .= '
		jQuery(document).ready(function() {
			
			function startProEventCalendar() {
				
				jQuery("#dp_pec_id'.$this->nonce.'").dpProEventCalendar({
					nonce: "dp_pec_id'.$this->nonce.'", 
					draggable: false,
					map_lat: '.$map_lat.',
					map_lng: '.$map_lng.',
					columns: "'.$this->columns.'",
					monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"), ';
				if($this->is_admin) {
					$script .= '
					draggable: false,
					isAdmin: true,
					';
				}
				if(is_numeric($this->id_calendar)) {
					$script .= '
					calendar: '.$this->id_calendar.',
					';	
				}
				if(isset($this->calendar_obj->date_range_start) && $this->calendar_obj->date_range_start != NULL && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					dateRangeStart: "'.$this->calendar_obj->date_range_start.'",
					';	
				}
				if(isset($this->calendar_obj->date_range_end) && $this->calendar_obj->date_range_end != NULL && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					dateRangeEnd: "'.$this->calendar_obj->date_range_end.'",
					';	
				}
				if(isset($this->calendar_obj->skin) && $this->calendar_obj->skin != "" && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					skin: "'.$this->calendar_obj->skin.'",
					';	
				}
				if(isset($this->type)) {
					$script .= '
					type: "'.$this->type.'",
					';	
				}
				
				if($hidden) {
					$script .= '
					selectric: false,
					';	
				}

				if($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) {
					$script .= '
					hide_old_dates: true,
					';	
				}

				if($this->opts['include_all_events']) {
					$script .= '
					include_all_events: 1,
					';	
				}

				if(is_numeric($this->limit)) {
					$script .= '
					limit: '.$this->limit.',
					';	
				}

				if($this->widget) {
					$script .= '
					widget: '.$this->widget.',
					';	
				}
				
				if($commented || $hidden) {
					$script .= '
					show_current_date: false,
					';	
				}

				$isRTL = 0;

				if($dpProEventCalendar['rtl_support'] || $this->opts['rtl'] || is_rtl()) { 
					$isRTL = 1;
				}
				
				$script .= '
					isRTL: '.$isRTL.',
					calendar_per_date: '.(is_numeric($this->opts['calendar_per_date']) && $this->opts['calendar_per_date'] > 0 ? $this->opts['calendar_per_date'] : 3).',
					allow_user_add_event: "'.$this->calendar_obj->allow_user_add_event.'",
					actualMonth: '.$this->datesObj->currentMonth.',
					actualYear: '.$this->datesObj->currentYear.',
					actualDay: '.$this->datesObj->currentDate.',
					defaultDate: "'.$this->defaultDate.'",
					defaultDateFormat: "'.date('Y-m-d', $this->defaultDate).'",
					current_date_color: "'.$this->calendar_obj->current_date_color.'",
					category: "'.($this->category != "" ? $this->category : '').'",
					location: "'.($this->opts['location'] != "" ? $this->opts['location'] : '').'",
					event_id: "'.($this->event_id != "" ? $this->event_id : '').'",
					author: "'.($this->author != "" ? $this->author : '').'",
					lang_sending: "'.addslashes($this->translation['TXT_SENDING']).'",
					lang_subscribe: "'.addslashes($this->translation['TXT_SUBSCRIBE']).'",
					lang_subscribe_subtitle: "'.addslashes($this->translation['TXT_SUBSCRIBE_SUBTITLE']).'",
					lang_remove_event: "'.addslashes($this->translation['TXT_REMOVE_EVENT']).'",
					lang_your_name: "'.addslashes($this->translation['TXT_YOUR_NAME']).'",
					lang_your_email: "'.addslashes($this->translation['TXT_YOUR_EMAIL']).'",
					lang_fields_required: "'.addslashes($this->translation['TXT_FIELDS_REQUIRED']).'",
					lang_invalid_email: "'.addslashes($this->translation['TXT_INVALID_EMAIL']).'",
					lang_txt_subscribe_thanks: "'.addslashes($this->translation['TXT_SUBSCRIBE_THANKS']).'",
					lang_book_event: "'.addslashes($this->translation['TXT_BOOK_EVENT']).'",
					view: "'.($this->is_admin || $this->type == "upcoming" || !empty($this->event_id) ? 'monthly' : $this->calendar_obj->view).'"
				});

				jQuery( document ).on("click", ".pec_edit_event", function() {


						setTimeout(function() {
							jQuery(".dp_pec_date_input_modal, .dp_pec_end_date_input_modal", ".dpProEventCalendarModalEditEvent").datepicker({
								beforeShow: function(input, inst) {
								   jQuery("#ui-datepicker-div").removeClass("dp_pec_datepicker");
								   jQuery("#ui-datepicker-div").addClass("dp_pec_datepicker");
							   },
								showOn: "button",
								isRTL: '.$isRTL.',
								buttonImage: "'.dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ).'",
								buttonImageOnly: false,
								minDate: 0,
								dateFormat: "yy-mm-dd",
								'.($this->calendar_obj->first_day == 1 ? "firstDay: 1," : "").'
								monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"),
								dayNamesMin: new Array("'.substr($this->translation['DAY_SUNDAY'], 0, 2).'", "'.substr($this->translation['DAY_MONDAY'], 0, 2).'", "'.substr($this->translation['DAY_TUESDAY'], 0, 2).'", "'.substr($this->translation['DAY_WEDNESDAY'], 0, 2).'", "'.substr($this->translation['DAY_THURSDAY'], 0, 2).'", "'.substr($this->translation['DAY_FRIDAY'], 0, 2).'", "'.substr($this->translation['DAY_SATURDAY'], 0, 2).'")
							});
						}, 2000);
						
					});
				';
				if(($this->calendar_obj->allow_user_add_event || $this->type == 'add-event') && !$this->is_admin && empty($this->event_id)) {
					
					$min_sunday = dpProEventCalendar_str_split_unicode($this->translation['DAY_SUNDAY'], 3);
					$min_monday = dpProEventCalendar_str_split_unicode($this->translation['DAY_MONDAY'], 3);
					$min_tuesday = dpProEventCalendar_str_split_unicode($this->translation['DAY_TUESDAY'], 3);
					$min_wednesday = dpProEventCalendar_str_split_unicode($this->translation['DAY_WEDNESDAY'], 3);
					$min_thursday = dpProEventCalendar_str_split_unicode($this->translation['DAY_THURSDAY'], 3);
					$min_friday = dpProEventCalendar_str_split_unicode($this->translation['DAY_FRIDAY'], 3);
					$min_saturday = dpProEventCalendar_str_split_unicode($this->translation['DAY_SATURDAY'], 3);
					
					

					$script .= '
					var multiple_dates = new Array();

					jQuery( ".dp_pec_date_input, .dp_pec_end_date_input, .dp_pec_extra_dates", "#dp_pec_id'.$this->nonce.'" ).datepicker({
						beforeShow: function(input, inst) {
						   jQuery("#ui-datepicker-div").removeClass("dp_pec_datepicker");
						   jQuery("#ui-datepicker-div").addClass("dp_pec_datepicker");
					   },
					   onSelect: function (dateText, inst) {
					        if(jQuery(inst.input).hasClass("dp_pec_extra_dates")) {
					        	var dates = jQuery(inst.input).val();
					        	var index = jQuery.inArray(dateText, multiple_dates);
							    if (index >= 0) 
							        multiple_dates.splice(index, 1);
							    else 
							        multiple_dates.push(dateText);

							    var printExtraDates = new String;
							    multiple_dates.forEach(function (val) {
							        printExtraDates += val + ", ";
							    });

							    jQuery(inst.input).val(printExtraDates.slice(0, -2));

					        }
					    },
						showOn: "button",
						isRTL: '.$isRTL.',
						buttonImage: "'.dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ).'",
						buttonImageOnly: false,
						dateFormat: "yy-mm-dd",
						'.($this->calendar_obj->first_day == 1 ? "firstDay: 1," : "").'
						monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"),
						dayNamesMin: new Array("'.$min_sunday[0].'", "'.$min_monday[0].'", "'.$min_tuesday[0].'", "'.$min_wednesday[0].'", "'.$min_thursday[0].'", "'.$min_friday[0].'", "'.$min_saturday[0].'")
					});
					
					
					';
				}
				if(!$this->is_admin && empty($this->event_id)) {
					$script .= '
					jQuery("input, textarea", "#dp_pec_id'.$this->nonce.'").placeholder();';
				}
				$script .= '
			}
			
			';
			if(!$hidden) {
				$script .= '
				if(jQuery("#dp_pec_id'.$this->nonce.'").parent().css("display") == "none") {
					jQuery("#dp_pec_id'.$this->nonce.'").parent().onShowProCalendar(function(){
						startProEventCalendar();
					});
					return;
				}';
			}
			
			$script .= '
			startProEventCalendar();
		});
		
		jQuery(window).resize(function(){
			if(jQuery(".dp_pec_layout", "#dp_pec_id'.$this->nonce.'").width() != null) {
	
				var instance = jQuery("#dp_pec_id'.$this->nonce.'");
				
				if(instance.width() < 500) {
					jQuery(instance).addClass("dp_pec_400");
	
					jQuery(".dp_pec_dayname span", instance).each(function(i) {
						jQuery(this).html(jQuery(this).html().substr(0,3));
					});
					
					jQuery(".prev_month strong", instance).hide();
					jQuery(".next_month strong", instance).hide();
					jQuery(".prev_day strong", instance).hide();
					jQuery(".next_day strong", instance).hide();
					
				} else {
					jQuery(instance).removeClass("dp_pec_400");
					jQuery(".prev_month strong", instance).show();
					jQuery(".next_month strong", instance).show();
					jQuery(".prev_day strong", instance).show();
					jQuery(".next_day strong", instance).show();
					
				}
			}
		});
		';

		if(!empty($this->event_id)) {
			$script .= '
			jQuery(".dp_pec_layout", "#dp_pec_id'.$this->nonce.'").hide();
			jQuery(".dp_pec_options_nav", "#dp_pec_id'.$this->nonce.'").hide();
			jQuery(".dp_pec_add_nav", "#dp_pec_id'.$this->nonce.'").hide();
			';
		}
		
		if($commented) {
			$script .= ' PEC Commented Script */';	
		}
		$script .= '
		
		//]]>
		</script>';
		
		if($print)
			echo $script;	
		else
			return $script;
		
	}
	
	function outputEvent($event) {
		
		$result = $this->getEventData($event);	
		
		$html = '';
		
		$html .= '
				<div class="'.$this->calendar_obj->skin.' dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'">
					<div class="dp_pec_content">';
		
		$html .= $this->singleEventLayout($result);
		
		$html .= '		
					</div>
				</div>';
		
		return $html;
		
	}
	
	function output( $print = false ) 
	{
		global $dpProEventCalendar, $dp_pec_payments, $wpdb;
		
		$width = "";
		$html = "";

		$skin = "pec_no_skin";
			
		if($this->opts['skin'] != "") {
			
			$skin = 'pec_skin_'.$this->opts['skin'];
		}

		if($this->type == 'calendar') {
			
			if(isset($this->calendar_obj->width) && !$this->is_admin && empty($this->event_id) && !$this->widget) { $width = 'style="width: '.$this->calendar_obj->width.$this->calendar_obj->width_unity.' " '; }
			
			if($this->is_admin) {
				$html .= '
				<div class="dpProEventCalendar_ModalCalendar">';
			}
			
				$html .= '
				<div class="dp_pec_wrapper dp_pec_dw_layout_'.$this->calendar_obj->daily_weekly_layout.' dp_pec_calendar_'.$this->calendar_obj->id.' dp_pec_'.($this->is_admin || !empty($this->event_id) ? 'monthly' : $this->calendar_obj->view).' '.$skin.' '.$this->calendar_obj->skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>';

			if(!$this->is_admin && ($this->calendar_obj->ical_active || $this->calendar_obj->rss_active || $this->calendar_obj->subscribe_active || $this->calendar_obj->show_view_buttons)) {
				$html .= '<div class="dp_pec_options_nav">';
					if($this->calendar_obj->show_view_buttons) {

						$html .= '<a href="#" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "monthly" || $this->calendar_obj->view == "monthly-all-events" ? "active" : "").'" data-pec-view="monthly">'.$this->translation['TXT_MONTHLY'].'</a>';

						$html .= '<a href="#" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "weekly" ? "active" : "").'" data-pec-view="weekly">'.$this->translation['TXT_WEEKLY'].'</a>';

						$html .= '<a href="#" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "daily" ? "active" : "").'" data-pec-view="daily">'.$this->translation['TXT_DAILY'].'</a>';
						
					}
					$html .= '<div class="dp_pec_options_nav_divider"></div>';
					if($this->calendar_obj->ical_active) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar, 'webcal' ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
					}
					if($this->calendar_obj->rss_active) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'><i class='fa fa-rss'></i>RSS</a>";
					}
					if($this->calendar_obj->subscribe_active) {
						$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='#'>".$this->translation['TXT_SUBSCRIBE']."</a>";
					}
					$html .= '<div class="dp_pec_clear"></div>';

				$html .= '</div>';
			}
			
			$allow_user_add_event_roles = explode(',', $this->calendar_obj->allow_user_add_event_roles);
			$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);
			
			if(!is_array($allow_user_add_event_roles) || empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "") {
				$allow_user_add_event_roles = array('all');	
			}
			
			if($this->calendar_obj->allow_user_add_event && 
				!$this->is_admin && 
					(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
					 in_array('all', $allow_user_add_event_roles) || 
					 (!is_user_logged_in() && !$this->calendar_obj->assign_events_admin)
				    )
			) {
				
				$html .= '
					<div class="dp_pec_add_nav">';
					$html .= '
						<a href="#" class="dp_pec_view dp_pec_add_event pec_action_btn dp_pec_btnright"><span class="fa fa-plus-circle"></span>'.str_replace('+', '', $this->translation['TXT_ADD_EVENT']).'</a>
						<a href="#" class="dp_pec_view dp_pec_cancel_event pec_action_btn dp_pec_btnright">'.$this->translation['TXT_CANCEL'].'</a>
						<div class="dp_pec_clear"></div>
						';
					$html .= '
						<div class="dp_pec_add_form">';
					if(!is_user_logged_in() && !$this->calendar_obj->assign_events_admin) {
						$html .= '
							<div class="dp_pec_notification_box dp_pec_visible">
							'.$this->translation['TXT_EVENT_LOGIN'].'
							</div>';
					} else {
						$html .= '
							<div class="dp_pec_notification_box dp_pec_notification_event_succesfull">
							'.$this->translation['TXT_EVENT_THANKS'].'
							</div>';
						$html .= '
							<form name="dp_pec_event_form" class="add_new_event_form" enctype="multipart/form-data" method="post">
								<div class="pec-add-body">
									<div class="">
										<div class="dp_pec_row">
											<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" id="" class="dp_pec_form_title pec_required" name="title" />
										</div>
										';
										if($this->calendar_obj->form_show_description) {
											$html .= '
										<div class="dp_pec_row">';
											if($this->calendar_obj->form_text_editor) {
												// Turn on the output buffer
												ob_start();
												
												// Echo the editor to the buffer
												wp_editor('', $this->nonce.'_event_description', array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'textarea_rows' => 5, 'teeny' => true));
												
												// Store the contents of the buffer in a variable
												$editor_contents = ob_get_clean();
												
												$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
													
											} else {
												$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" id="" name="description" cols="50" rows="5"></textarea>';	
											}
											
											$html .= '
										</div>
										';
										}
										$html .= '
										
										<div class="dp_pec_row dp_pec_cal_new_sub">
											<div class="dp_pec_col6">
												';
												if($this->calendar_obj->form_show_category) {
													$cat_arr = array();
													if(!empty($this->category)) {
														$cat_arr = explode(",", $this->category);
													}

													$cat_args = array(
															'taxonomy' => 'pec_events_category',
															'hide_empty' => 0
														);
													if($this->calendar_obj->category_filter_include != "") {
														$cat_args['include'] = $this->calendar_obj->category_filter_include;
													}
													$categories = get_categories($cat_args); 
													if(count($categories) > 0) {
														$html .= '
														<div class="dp_pec_row">
															<div class="dp_pec_col12">
																<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
																';
																foreach ($categories as $category) {
																	if(!empty($cat_arr) && !in_array($category->term_id, $cat_arr)) {
																		continue;
																	}
																	$html .= '<div class="pec_checkbox_list">';
																	$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" />';
																	$html .= $category->cat_name;
																	$html .= '</div>';
																  }
																$html .= '	
																<div class="dp_pec_clear"></div>
															</div>
														</div>
														';
													}
												}
												$html .= '
												<div class="dp_pec_row">
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
														<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="large-text dp_pec_date_input" value="'.date('Y-m-d').'" style="width:80px;" />
													</div>
													
													<div class="dp_pec_col6 dp_pec_end_date_form">';
													if($this->calendar_obj->form_show_end_date) {
													$html .= '
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
														<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="large-text dp_pec_end_date_input" value="" style="width:80px;" />
														<button type="button" class="dp_pec_clear_end_date">
															<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
														</button>';
													}
													$html .= '
													</div>
													<div class="dp_pec_clear"></div>
												</div>';
												if($this->calendar_obj->form_show_extra_dates) {
													$html .= '<div class="dp_pec_row">';
													$html .= '<div class="dp_pec_col12">';
													$html .= '<input type="text" value="" placeholder="'.$this->translation['TXT_EXTRA_DATES'].'" id="" class="dp_pec_extra_dates" readonly="readonly" style="max-width: 300px;" name="extra_dates" />';
													$html .= '</div>';
													$html .= '<div class="dp_pec_clear"></div>
													</div>';
												}

												$html .= '<div class="dp_pec_row">';
													if($this->calendar_obj->form_show_start_time) {
														$html .= '
														<div class="dp_pec_col6">
															<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
															<select autocomplete="off" name="time_hours" class="dp_pec_new_event_time" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">';
															
																for($i = 0; $i <= 23; $i++) {
																	$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
																	if(dpProEventCalendar_is_ampm()) {
																		$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
																	}
																	$html .= '
																	<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
																}
															$html .= '
															</select>
															<select autocomplete="off" name="time_minutes" class="dp_pec_new_event_time" id="pec_time_minutes" style="width:50px;">';
																for($i = 0; $i <= 59; $i += 5) {
																	$html .= '
																	<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
																}
															$html .= '
															</select>
														</div>';
													}
													if($this->calendar_obj->form_show_end_time) {
														$html .= '
														<div class="dp_pec_col6">
															<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
															<select autocomplete="off" name="end_time_hh" class="dp_pec_new_event_time" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">
																<option value="">--</option>';
																for($i = 0; $i <= 23; $i++) {
																	$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
																	if(dpProEventCalendar_is_ampm()) {
																		$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
																	}
																	$html .= '
																	<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
																}
															$html .= '
															</select>
															<select autocomplete="off" name="end_time_mm" class="dp_pec_new_event_time" id="" style="width:50px;">
																<option value="">--</option>';
																for($i = 0; $i <= 59; $i += 5) {
																	$html .= '
																	<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
																}
															$html .= '
															</select>
														</div>';
													}
													$html .= '
													<div class="dp_pec_clear"></div>
												</div>';
												
												$html .= '<div class="dp_pec_row">';
												
												if($this->calendar_obj->form_show_hide_time) {
													$html .= '
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
														<select autocomplete="off" name="hide_time">
															<option value="0">'.$this->translation['TXT_NO'].'</option>
															<option value="1">'.$this->translation['TXT_YES'].'</option>
														</select>
														
														<div class="dp_pec_clear"></div>
														';
														
														if($this->calendar_obj->form_show_all_day) {
															
															$html .= '
															<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
															<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
																
														}
														
													$html .= '
													</div>';
												} elseif($this->calendar_obj->form_show_all_day) {
													
													$html .= '
													<div class="dp_pec_col6">
														<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
														<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
													</div>';	
					
												}
												
												if($this->calendar_obj->form_show_frequency) {
													$html .= '
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
														<select autocomplete="off" name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
															<option value="0">'.$this->translation['TXT_NONE'].'</option>
															<option value="1">'.$this->translation['TXT_EVENT_DAILY'].'</option>
															<option value="2">'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
															<option value="3">'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
															<option value="4">'.$this->translation['TXT_EVENT_YEARLY'].'</option>
														</select>
													';
								
														$html .= '
														<div class="pec_daily_frequency" style="display:none;">
														
															<div class="dp_pec_clear"></div>
														
															<div id="pec_daily_every_div">' . $this->translation['TXT_EVERY'] . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="1" /> '.$this->translation['TXT_DAYS'] . ' </div>
															
															<div class="dp_pec_clear"></div>
															
															<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" />'. $this->translation['TXT_ALL_WORKING_DAYS'] . '</div>
														</div>';
														
														$html .= '
														<div class="pec_weekly_frequency" style="display:none;">
															
															<div class="dp_pec_clear"></div>
															
															'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="1" /> '. $this->translation['TXT_WEEKS_ON'].'
															
															<div class="dp_pec_clear"></div>
															
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="1" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_MON'] . '
															</div>
															<div class="pec_checkbox_list">	
																<input type="checkbox" class="checkbox" value="2" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_TUE'] . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="3" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_WED'] . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="4" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_THU'] . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="5" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_FRI'] . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="6" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SAT'] . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="7" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SUN'] . '
															</div>
															
														</div>';
														
														$html .= '
														<div class="pec_monthly_frequency" style="display:none;">
															
															<div class="dp_pec_clear"></div>
															
															'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="1" /> ' . $this->translation['TXT_MONTHS_ON'] . '
															
															<div class="dp_pec_clear"></div>
															
															<select autocomplete="off" name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
																<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
																<option value="first"> ' . $this->translation['TXT_FIRST'] . '</option>
																<option value="second"> ' . $this->translation['TXT_SECOND'] . '</option>
																<option value="third"> ' . $this->translation['TXT_THIRD'] . '</option>
																<option value="fourth"> ' . $this->translation['TXT_FOURTH'] . '</option>
																<option value="last"> ' . $this->translation['TXT_LAST'] . '</option>
															</select>
															
															<select autocomplete="off" name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
															<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
																<option value="monday"> ' . $this->translation['DAY_MONDAY'] . '</option>
																<option value="tuesday"> ' . $this->translation['DAY_TUESDAY'] . '</option>
																<option value="wednesday"> ' . $this->translation['DAY_WEDNESDAY'] . '</option>
																<option value="thursday"> ' . $this->translation['DAY_THURSDAY'] . '</option>
																<option value="friday"> ' . $this->translation['DAY_FRIDAY'] . '</option>
																<option value="saturday"> ' . $this->translation['DAY_SATURDAY'] . '</option>
																<option value="sunday"> ' . $this->translation['DAY_SUNDAY'] . '</option>
															</select>
														</div>
													</div>';
													
												}
												
												if($this->calendar_obj->form_show_booking_enable ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="checkbox" class="checkbox" name="booking_enable" id="" value="1" />
														<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_ALLOW_BOOKINGS'].'</span>
													</div>';
												}

												$html .= '
												</div>';
												
												$html .= '
												<div class="dp_pec_row">';
												if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="number" min="0" value="" style="width: 120px;" placeholder="'.$this->translation['TXT_PRICE'].'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
													</div>';
												}
												
												if($this->calendar_obj->form_show_booking_block_hours ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="number" min="0" value="" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_BLOCK_HOURS'].'" id="" name="block_hours" />
													</div>';
												}

												if($this->calendar_obj->form_show_booking_limit ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="number" min="0" value="" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_LIMIT'].'" id="" name="limit" />
													</div>';
												}
												
												$html .= '
													<div class="dp_pec_clear"></div>
												</div>
												
												';

												if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "" ) {
													$html .= '
													<div class="dp_pec_row">';
													
													$html .= '
														<div class="dp_pec_col12">
															<div id="pec_new_event_captcha"></div>
														</div>';
													

													$html .= '
														<div class="dp_pec_clear"></div>
													</div>
													';
												}
												$html .= '
												<div class="dp_pec_clear"></div>
											</div>
											<div class="dp_pec_col6">
												';
												if($this->calendar_obj->form_show_image) {
													$rand_image = rand();
													$html .= '
													<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
													<div class="dp_pec_add_image_wrap">
														<label for="event_image_'.$this->nonce.'_'.$rand_image.'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
													</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.'" class="event_image" style="visibility:hidden; position: absolute;" />							
													';
												}
												if($this->calendar_obj->form_show_color) {
													$html .= '
													<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_COLOR'].'</span>
													<select autocomplete="off" name="color" class="pec_color_form">
														<option value="">'.$this->translation['TXT_NONE'].'</option>
														 ';
														 
														$counter = 0;
														$querystr = "
														SELECT *
														FROM ". $this->table_special_dates ." 
														ORDER BY title ASC
														";
														$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
														foreach($sp_dates_obj as $sp_dates) {
														
														$html .= '<option value="'.$sp_dates->id.'">'.$sp_dates->title.'</option>';
														
														}
                                                    $html .= ' 
													</select>
													<div class="dp_pec_clear"></div>';
												}

												if($this->calendar_obj->form_show_timezone) {
													$html .= '
													<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_TIMEZONE'].'</span>
													<select autocomplete="off" name="timezone" class="pec_timezone_form">';
													$current_offset = get_option('gmt_offset');
													$tzstring = get_option('timezone_string');
													if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
														$check_zone_info = false;
														if ( 0 == $current_offset )
															$tzstring = 'UTC+0';
														elseif ($current_offset < 0)
															$tzstring = 'UTC' . $current_offset;
														else
															$tzstring = 'UTC+' . $current_offset;
													}

													$pec_timezone = $tzstring;

									                $html .= wp_timezone_choice($pec_timezone); 
													$html .= ' 
													</select>
													<div class="dp_pec_clear"></div>';
												}

												if($this->calendar_obj->form_show_location) {
													$html .= '
													<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_LOCATION'].'</span>
													<select autocomplete="off" name="location" class="pec_location_form">
														<option value="">'.$this->translation['TXT_NONE'].'</option>
														 ';

														$args = array(
														'posts_per_page'   => -1,
														'post_type'        => 'pec-venues',
														'post_status'      => 'publish',
														'order'			   => 'ASC', 
														'orderby' 		   => 'title' );

														if($this->calendar_obj->venue_filter_include != "") {
															$args['include'] = $this->calendar_obj->venue_filter_include;
														}

														$venues_list = get_posts($args);
														foreach($venues_list as $venue) {

															$html .= '<option value="'.$venue->ID.'">'.$venue->post_title.'</option>';
														
														}

														if($this->calendar_obj->form_show_location_options) {
															$html .= '<option value="-1">'.$this->translation['TXT_OTHER'].'</option>';
														}
														
													$html .= ' 
													</select>
													<div class="dp_pec_clear"></div>';
													
													if($this->calendar_obj->form_show_location_options) {
														$html .= '
														<div class="pec_location_options" style="display:none;">
															<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION_NAME'].'" id="" name="location_name" />';

															$html .= '
															<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_ADDRESS'].'" id="" name="location_address" />';

															$html .= '
															<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
															<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
															<div class="map_lnlat_wrap" style="display:none;">
																<span class="dp_pec_form_desc">'.$this->translation['TXT_DRAG_MARKER'].'</span>
																<div id="pec_mapCanvas" style="height: 400px;"></div>
															</div>
														</div>
														';
													}

													/*$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';*/
												}

												if($this->calendar_obj->form_show_link) {
													$html .= '
													<input type="url" value="" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
												}
												
												if($this->calendar_obj->form_show_phone) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
												}

												$cal_form_custom_fields = $this->calendar_obj->form_custom_fields;
												$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

												if(is_array($dpProEventCalendar['custom_fields_counter'])) {
													$counter = 0;
													
													foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
														
														if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
															$counter++;
															continue;
														}

														if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {
				
															$html .= '
															<div class="dp_pec_wrap_checkbox">
															<input type="checkbox" class="checkbox '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="1" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
															</div>';
												
														} else {

															$html .= '
															<input type="text" class="dp_pec_new_event_text '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
															
														}
														$counter++;		
													}
												}
													$html .= '
												
											</div>
											<div class="dp_pec_clear"></div>
										</div>
									</div>
									<div class="dp_pec_clear"></div>
								</div>
								<div class="pec-add-footer">
									<a href="#" data-lang-sending="'.$this->translation['TXT_SENDING'].'" class="dp_pec_view dp_pec_submit_event pec_action_btn dp_pec_btnright">'.($this->calendar_obj->publish_new_event ? $this->translation['TXT_SUBMIT'] : $this->translation['TXT_SUBMIT_FOR_REVIEW']).'</a>
								</div>
							</form>';
					}
				$html .= '
						</div>';
				$html .= '
						<div class="dp_pec_clear"></div>
					</div>
				';
			}

			$html .= '
				<div class="dp_pec_nav dp_pec_nav_monthly" '.($this->calendar_obj->view == "monthly" || $this->is_admin || !empty($this->event_id) ? "" : "style='display:none;'").'>';

					
					$html .= '<span class="next_month"><i class="fa fa-angle-right"></i></span>';
					$html .= '<span class="prev_month"><i class="fa fa-angle-left"></i></span>';

					$html .= '<select autocomplete="off" class="pec_switch_month">
						';
						foreach($this->translation['MONTHS'] as $key) {
							$html .= '<option value="'.$key.'" '.($key == $this->translation['MONTHS'][($this->datesObj->currentMonth - 1)] ? 'selected="selected"':'').'>'.$key.'</option>';
						}
			$html .= '</select>';
			$html .= '<select autocomplete="off" class="pec_switch_year">
						';
						if(!isset($dpProEventCalendar['year_from'])) {
							$dpProEventCalendar['year_from'] = 2;
						}
						if(!isset($dpProEventCalendar['year_until'])) {
							$dpProEventCalendar['year_until'] = 3;
						}

						for($i = date('Y') - $dpProEventCalendar['year_from']; $i <= date('Y') + $dpProEventCalendar['year_until']; $i++) {
							$html .= '<option value="'.$i.'" '.($i == $this->datesObj->currentYear ? 'selected="selected"':'').'>'.$i.'</option>';
						}
			$html .= '</select>';
			$html .= '<div class="dp_pec_clear"></div></div>';
			
			$html .= '<div class="dp_pec_nav dp_pec_nav_daily" '.($this->calendar_obj->view == "daily" && !$this->is_admin && empty($this->event_id) ? "" : "style='display:none;'").'>';
			$html .= '<span class="next_day"><i class="fa fa-angle-right"></i></span>';
			$html .= '<span class="prev_day"><i class="fa fa-angle-left"></i></span>';

			$html .= '<span class="actual_day">'.dpProEventCalendar_date_i18n(get_option('date_format'), $this->defaultDate).'</span>';
			$html .= '<div class="dp_pec_clear"></div></div>';
			
			if($this->calendar_obj->first_day == 1) {
				$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
			} else {
				$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
			}
			
			$weekly_format = get_option('date_format');
			$weekly_format = 'd F, Y';
			
			$weekly_txt = dpProEventCalendar_date_i18n('d F', $weekly_first_date).' - '.dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
	
			if(date('m', $weekly_first_date) == date('m', $weekly_last_date)) {
			
				$weekly_txt = date('d', $weekly_first_date) . ' - ' . dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
				
			}
			
			if(date('Y', $weekly_first_date) != date('Y', $weekly_last_date)) {
					
				$weekly_txt = dpProEventCalendar_date_i18n($weekly_format, $weekly_first_date).' - '.dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
				
			}
			
			$html .= '<div class="dp_pec_nav dp_pec_nav_weekly" '.($this->calendar_obj->view == "weekly" && !$this->is_admin && empty($this->event_id) ? "" : "style='display:none;'").'>';
			$html .= '<span class="next_week"><i class="fa fa-angle-right"></i></span>';
			$html .= '<span class="prev_week"><i class="fa fa-angle-left"></i></span>';
			$html .= '<span class="actual_week">'.$weekly_txt.'</span>';
			$html .= '<div class="dp_pec_clear"></div></div>';
			
			if(!$this->is_admin) {
				$specialDatesList = $this->getSpecialDatesList();
				$html .= '<div class="dp_pec_layout">';
				

				if($this->calendar_obj->show_category_filter && empty($this->category)) {

					$html .= '<select autocomplete="off" name="pec_categories" class="pec_categories_list">';
					$html .= '<option value="">'.$this->translation['TXT_ALL_CATEGORIES'].'</option>';
							$cat_args = array(
									'taxonomy' => 'pec_events_category',
									'hide_empty' => 0
								);
							if($this->calendar_obj->category_filter_include != "") {
								$cat_args['include'] = $this->calendar_obj->category_filter_include;
							}
							$categories = get_categories($cat_args); 
						  foreach ($categories as $category) {
							$html .= '<option value="'.$category->term_id.'">';
							$html .= $category->cat_name;
							$html .= '</option>';
						  }
					$html .= '</select>';
				}

				if($this->calendar_obj->show_location_filter && empty($this->opts['location'])) {
					$html .= '<select autocomplete="off" name="pec_location" class="pec_location_list">';
					$html .= '<option value="">'.$this->translation['TXT_ALL_LOCATIONS'].'</option>';
							$args = array(
							'posts_per_page'   => -1,
							'post_type'        => 'pec-venues',
							'post_status'      => 'publish',
							'order'			   => 'ASC', 
							'lang'			   => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
							'suppress_filters'  => false,
							'orderby' 		   => 'title' );

					if($this->calendar_obj->venue_filter_include != "") {
						$args['include'] = $this->calendar_obj->venue_filter_include;
					}

							$venues_list = get_posts($args);
							foreach($venues_list as $venue) {

								$html .= '<option value="'.$venue->ID.'">'.$venue->post_title.'</option>';
							
							}
					$html .= '</select>';
				}

				$html .= '<div class="dp_pec_layout_right">';

				if($this->calendar_obj->show_references) {
					$html .= '<a href="#" class="dp_pec_references dp_pec_btnright"><i class="fa fa-dot-circle-o"></i>'.$this->translation['TXT_COLOR_CODE'].'</a>';
					$html .= '<div class="dp_pec_references_div">';
					$html .= '<a href="#" class="dp_pec_references_close"><i class="fa fa-times"></i></a>';
						$html .= '<div class="dp_pec_references_div_sp">';
						$html .= '<div class="dp_pec_references_color" style="background-color: '.$this->calendar_obj->current_date_color.'"></div>';
						$html .= '<div class="dp_pec_references_title">'.$this->translation['TXT_CURRENT_DATE'].'</div>';
						$html .= '<div style="clear:both;"></div></div>';
				
					if(count($specialDatesList) > 0) {
						foreach($specialDatesList as $key) {
							$html .= '<div class="dp_pec_references_div_sp">';
							$html .= '<div class="dp_pec_references_color" style="background-color: '.$key->color.'"></div>';
							$html .= '<div class="dp_pec_references_title">'.$key->title.'</div>';
							$html .= '<div style="clear:both;"></div></div>';
						}
					}
					$html .= '</div>';
				}
				$html .= '<a href="#" class="dp_pec_view_all dp_pec_btnright" data-translation-list="'.$this->translation['TXT_LIST_VIEW'].'" data-translation-calendar="'.$this->translation['TXT_CALENDAR_VIEW'].'">';
				if($this->calendar_obj->view == "monthly-all-events") {
					$html .= '<i class="fa fa-calendar-o"></i>'.$this->translation['TXT_CALENDAR_VIEW'];
				} else {
					$html .= '<i class="fa fa-list"></i>'.$this->translation['TXT_LIST_VIEW'];	
				}
				$html .= '	</a>';

				if($this->calendar_obj->show_search) {

					$html .= '<a href="#" class="dp_pec_search_btn dp_pec_btnright">';
					$html .= '<i class="fa fa-search"></i>';
					$html .= '</a>';

				}

				$html .= '
					</div>';
				if($this->calendar_obj->show_search) {

					$html .= '<form method="post" class="dp_pec_search_form">';
						$html .= '<input type="search" class="dp_pec_search" value="" placeholder="'.$this->translation['TXT_SEARCH'].'">';
						$html .= '<input type="submit" class="no-replace dp_pec_search_go" value="">';
					$html .= '</form>';

				}
				
				$html .= '
				</div>
				';
			}
			$html .= '<div style="clear:both;"></div>';
				
			$html .= '<div class="dp_pec_content">';
					
			if($this->calendar_obj->view == "monthly" || $this->is_admin || !empty($this->event_id)) {
				$html .= $this->monthlyCalendarLayout();
			}
			
			if($this->calendar_obj->view == "daily" && !$this->is_admin && empty($this->event_id)) {
				$html .= $this->dailyCalendarLayout();
			}
			
			if($this->calendar_obj->view == "weekly" && !$this->is_admin && empty($this->event_id)) {
				$html .= $this->weeklyCalendarLayout();
			}
			
			$html .= '</div>';
			$html .= '</div>';
			
			if($this->is_admin) {
				$html .= '
				</div>';
			}
		} elseif($this->type == 'upcoming') {
			
			$html .= '<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>';
			$html .= '<div class="dp_pec_options_nav">';
				
			if($this->calendar_obj->ical_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar, 'webcal' ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
			}
			if($this->calendar_obj->rss_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'><i class='fa fa-rss'></i>RSS</a>";
			}
			if($this->calendar_obj->subscribe_active) {
				$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='#'>".$this->translation['TXT_SUBSCRIBE']."</a>";
			}
			
			$html .= '<div class="dp_pec_clear"></div>';
			$html .= '</div>';

			$html .= '<div style="clear:both;"></div>';
				
			$html .= '<div class="dp_pec_content">';
						
			$html .= $this->upcomingCalendarLayout();
			
			$html .= '</div>';
			$html .= '</div>';
			
		} elseif($this->type == 'past') {
			
			$html .= '<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>';
			$html .= '<div style="clear:both;"></div>';
				
			$html .= '<div class="dp_pec_content">';
					
			if(empty($this->from)) {
				$this->from = "1970-01-01";
			}
			
			$html .= $this->upcomingCalendarLayout(false, $this->limit, '', null, null, true, false, true, false, true);
			
			$html .= '</div>';
			$html .= '</div>';
			
		} elseif($this->type == 'accordion') {
			
			$html .= '
			<div class="dp_pec_accordion_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			if($this->calendar_obj->ical_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar, 'webcal' ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
			}
			if($this->calendar_obj->rss_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'><i class='fa fa-rss'></i>RSS</a>";
			}
			if($this->calendar_obj->subscribe_active) {
				$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='#'>".$this->translation['TXT_SUBSCRIBE']."</a>";
			}
			
			$html .= '
				<div class="dp_pec_clear"></div>
				
				<div class="dp_pec_content_header">
					<span class="events_loading"><i class="fa fa-cog fa-spin"></i></span>
					<h2 class="actual_month">'.$this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].' '.$this->datesObj->currentYear.'</h2>
					<div class="month_arrows">
						<span class="prev_month"><i class="fa fa-angle-left"></i></span>
						<span class="next_month"><i class="fa fa-angle-right"></i></span>
					</div>
					<span class="return_layout"><i class="fa fa-angle-double-left"></i></span>
				</div>
				';

			$html .= "<div class='dp_pec_nav'>";
			if($this->calendar_obj->show_category_filter && empty($this->category)) {

				$html .= '<select autocomplete="off" name="pec_categories" class="pec_categories_list">
						<option value="">'.$this->translation['TXT_ALL_CATEGORIES'].'</option>';
						$cat_args = array(
								'taxonomy' => 'pec_events_category',
								'hide_empty' => 0
							);
						if($this->calendar_obj->category_filter_include != "") {
							$cat_args['include'] = $this->calendar_obj->category_filter_include;
						}
						$categories = get_categories($cat_args); 
					  foreach ($categories as $category) {
						$html .= '<option value="'.$category->term_id.'">';
						$html .= $category->cat_name;
						$html .= '</option>';
					  }
				$html .= '
					</select>';

			}

			if($this->calendar_obj->show_location_filter && empty($this->opts['location'])) {
				$html .= '<select autocomplete="off" name="pec_location" class="pec_location_list">
						<option value="">'.$this->translation['TXT_ALL_LOCATIONS'].'</option>';
						$args = array(
						'posts_per_page'   => -1,
						'post_type'        => 'pec-venues',
						'post_status'      => 'publish',
						'order'			   => 'ASC', 
						'lang'			   => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
						'suppress_filters'  => false,
						'orderby' 		   => 'title' );
					if($this->calendar_obj->venue_filter_include != "") {
						$args['include'] = $this->calendar_obj->venue_filter_include;
					}
						$venues_list = get_posts($args);
						foreach($venues_list as $venue) {

							$html .= '<option value="'.$venue->ID.'">'.$venue->post_title.'</option>';
						
						}
				$html .= '
					</select>';
			}

			if($this->calendar_obj->show_search) {

				$html .= '
				<a href="#" class="dp_pec_search_btn dp_pec_btnright">
					<i class="fa fa-search"></i>
				</a>';

			}
			$html .= "<div class='dp_pec_clear'></div>
				</div>";

				if($this->calendar_obj->show_search) {
					$html .= '
					<div class="dp_pec_content_search dp_pec_search_form">
						<input type="search" class="dp_pec_content_search_input" placeholder="'.$this->translation['TXT_SEARCH'].'" />
						<a href="#" class="dp_pec_icon_search" data-results_lang="'.addslashes($this->translation['TXT_RESULTS_FOR']).'"><i class="fa fa-search"></i></a>
					</div>';
				}
				$html .= '
				
				<div class="dp_pec_content">
					<div class="dp_pec_content_ajax '.(is_numeric($this->columns) && $this->columns > 1 ? 'pec_upcoming_layout' : '').'">';

				$year = $this->datesObj->currentYear;
				$next_month_days = cal_days_in_month(CAL_GREGORIAN, str_pad(($this->datesObj->currentMonth), 2, "0", STR_PAD_LEFT), $year);
				$month_number = str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT);
				$this_month_day = "01";

				if(($this->calendar_obj->hide_old_dates || $this->opts['hide_old_dates']) && $this->datesObj->currentMonth == date('m') && $this->datesObj->currentYear == date('Y')) {
					$this_month_day = str_pad($this->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
				}

				$html_month_list = "";

				$limit = 40;
				if($this->widget && is_numeric($this->limit) && $this->limit > 0) {
					$limit = $this->limit;
				}

				$html_month_list = $this->eventsMonthList($year."-".$month_number."-".$this_month_day." 00:00:00", $year."-".$month_number."-".$next_month_days." 23:59:59", $limit);
				$html .= $html_month_list;
				
			$html .= '
					</div>
				</div>
			</div>';
			
		} elseif($this->type == 'modern') {
			$this->calendar_obj->show_titles_monthly = 1;
			$this->calendar_obj->link_post = 1;

			$html .= '
			<div class="dp_pec_modern_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			if($this->calendar_obj->ical_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar, 'webcal' ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
			}
			if($this->calendar_obj->rss_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'><i class='fa fa-rss'></i>RSS</a>";
			}
			if($this->calendar_obj->subscribe_active) {
				$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='#'>".$this->translation['TXT_SUBSCRIBE']."</a>";
			}
			
			$html .= '
				<div class="dp_pec_clear"></div>
				
				<div class="dp_pec_content_header">
					<span class="events_loading"><i class="fa fa-cog fa-spin"></i></span>
					<h2 class="actual_month">'.$this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].' '.$this->datesObj->currentYear.'</h2>
					<div class="month_arrows">
						<span class="prev_month"><i class="fa fa-angle-left"></i></span>
						<span class="next_month"><i class="fa fa-angle-right"></i></span>
					</div>
				</div>
				';

			$html .= "<div class='dp_pec_nav'>";
			if($this->calendar_obj->show_category_filter && empty($this->category)) {

				$html .= '<select autocomplete="off" name="pec_categories" class="pec_categories_list">
						<option value="">'.$this->translation['TXT_ALL_CATEGORIES'].'</option>';
						$cat_args = array(
								'taxonomy' => 'pec_events_category',
								'hide_empty' => 0
							);
						if($this->calendar_obj->category_filter_include != "") {
							$cat_args['include'] = $this->calendar_obj->category_filter_include;
						}
						$categories = get_categories($cat_args); 
					  foreach ($categories as $category) {
						$html .= '<option value="'.$category->term_id.'">';
						$html .= $category->cat_name;
						$html .= '</option>';
					  }
				$html .= '
					</select>';

			}

			if($this->calendar_obj->show_location_filter && empty($this->opts['location'])) {
				$html .= '<select autocomplete="off" name="pec_location" class="pec_location_list">
						<option value="">'.$this->translation['TXT_ALL_LOCATIONS'].'</option>';
						$args = array(
						'posts_per_page'   => -1,
						'post_type'        => 'pec-venues',
						'post_status'      => 'publish',
						'order'			   => 'ASC', 
						'lang'			   => (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : strtolower(substr(get_locale(),3,2))),
						'suppress_filters'  => false,
						'orderby' 		   => 'title' );
					if($this->calendar_obj->venue_filter_include != "") {
						$args['include'] = $this->calendar_obj->venue_filter_include;
					}
						$venues_list = get_posts($args);
						foreach($venues_list as $venue) {

							$html .= '<option value="'.$venue->ID.'">'.$venue->post_title.'</option>';
						
						}
				$html .= '
					</select>';
			}

			
			$html .= "<div class='dp_pec_clear'></div>
				</div>";

				
				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '<div class="dp_pec_content">';
					$html .= $this->monthlyCalendarLayout();
				$html .= '</div>';
			$html .= '
			</div>';
			
		} elseif($this->type == 'accordion-upcoming') {
			
			$html .= '
			<div class="dp_pec_accordion_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					<div class="dp_pec_content_ajax '.(is_numeric($this->columns) && $this->columns > 1 ? 'pec_upcoming_layout' : '').'">
				';
				
				$html .= $this->eventsMonthList(null, null, $this->limit);
			$html .= '
					</div>
				</div>
			</div>';
		
		} elseif($this->type == 'compact-upcoming' || $this->type == 'list-upcoming') {
			/*if(empty($this->from)) {
				$this->from = "1970-01-01";
			}*/
			$past = false;
			if($this->opts['scope'] == 'past') {
				$past = true;
			}
			$event_list = $this->upcomingCalendarLayout( true, ($this->limit + 1), '', null, null, true, false, false, false, $past );
			

			$html .= '<div class="dp_pec_wrapper dp_pec_compact_wrapper dp_pec_compact_upcoming_wrapper '.($this->type == 'list-upcoming' ? 'dp_pec_list_upcoming' : '').' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>';
			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_content">
				';
				$event_count = 0;
				$daily_events = array();
				if(is_array($event_list)) {
					foreach ($event_list as $event) {

						if($event->id == "") 
							$event->id = $event->ID;
						
						$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);
			
						if($event_count >= $this->limit) {
							break;
						}
						
						if($event->recurring_frecuency == 1 && $this->opts['group']){
					
							if(in_array($event->id, $daily_events)) {
								continue;	
							}
							
							$daily_events[] = $event->id;
						}

						$title = $event->title;
						$permalink = "";
						if($this->calendar_obj->link_post) {
							$permalink = dpProEventCalendar_get_permalink($event->id);
							$title = '<a href="'.$permalink.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	
						}
						
						//$edit_button = $this->getEditRemoveButtons($event);
						$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));

						$start_day = date('d', strtotime($event->date));
						$start_month = date('n', strtotime($event->date));
						$start_year = date('Y', strtotime($event->date));
						
						$end_date = '';
						$end_year = '';
						if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
							$end_day = date('d', strtotime($event->end_date));
							$end_month = date('n', strtotime($event->end_date));
							$end_year = date('Y', strtotime($event->end_date));
							
							//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
							$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
						}
						
						//$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
						$start_date = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date));
						
						$end_time = "";
						if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
						
						if($end_time != "") {
							
							$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));
							
							$end_time = " - ".$end_time_tmp;
							if($end_time_tmp == $time) {
								$end_time = "";	
							}
						}
						
						
						/*if($start_year != $end_year && $end_year != "") {
							$start_date .= ', '.$start_year;
						}*/
						
						if(isset($event->all_day) && $event->all_day) {
							$time = $this->translation['TXT_ALL_DAY'];
							$end_time = "";
						}

						$day_number = date("d", strtotime($event->date));
						$month = dpProEventCalendar_date_i18n("M", strtotime($event->date));

						/*if($event->all_day && $time <= current_time('timestamp')) {
							continue;
						}*/

						$html .= '<div class="dp_pec_date_event" data-event-number="1">
									<div class="dp_pec_date_left">
										<div class="dp_pec_date_left_number">'.$day_number.'
											<div class="dp_pec_date_left_month">'.$month.'
											';
						if($event->recurring_frecuency == 1 && $this->opts['group'] && $this->type == 'list-upcoming' && $end_date != ""){
							$html .= $end_date;
						}
											$html .='</div>
										</div>
										
									</div>';
						$post_thumbnail_id = get_post_thumbnail_id( $event->id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'small' );

						if(!empty($post_thumbnail_id) && !$this->widget) {
						
							$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';

						}

						$html .= '
									<div class="dp_pec_content_left">';
						if($event->featured_event) {
							$html .= '<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>';
						}
						$html .= '
										<div class="dp_pec_clear"></div>';

						$all_working_days = '';
						if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
							$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
						}

						$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ? $time.$end_time.$end_date.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');
					
						if($pec_time != "" && !$event->tbc) {
							$html .= '
								<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$pec_time.'</span>';
						}

						if(false && $this->calendar_obj->ical_active) {
							$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ). "'><i class='fa fa-calendar-plus-o'></i>iCal</a>";
						}

						if($event->link != '') {
							$event->link = trim($event->link);
							if(substr($event->link, 0, 4) != "http" && substr($event->link, 0, 4) != "mail") {
								$event->link = 'http://'.$event->link;
							}

							$html .= '
							<a class="dpProEventCalendar_feed" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i>';

							if(!$this->widget) {
								$html .= $event->link;
							}

							$html .= '</a>';
						}
						
						$html .= '
										<div class="dp_pec_clear"></div>

										<h2 class="dp_pec_event_title">
											<span class="dp_pec_event_title_sp">'.$title.'</span>
										</h2>
										';
						$location = get_the_title($event->location_id);
						
						if($location != "") {
							$html .= '
										<div class="dp_pec_compact_meta">
											<span>'.$location.'</span>
										</div>';
						}

						$category = get_the_terms( $event->id, 'pec_events_category' ); 
						$category_list_html = '';
						if(!empty($category)) {
							$category_count = 0;
							foreach ( $category as $cat){
								if($category_count > 0) {
									$category_list_html .= " / ";	
								}
								$category_list_html .= $cat->name;
								$category_count++;
							}
						}
						
						if($category_list_html != "") {
							$html .= '
										<div class="dp_pec_compact_meta_category">
											<span>'.$category_list_html.'</span>
										</div>';
						}

						$html .= '
									</div>
									<div class="dp_pec_clear"></div>
								</div>';


						$event_count++;
					}
				}
			$html .= '
				</div>
				<div style="clear:both;"></div>
			</div>';
		
		} elseif($this->type == 'grid-upcoming') {
			
			$html .= '
			<div class="dp_pec_grid_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					<ul>
				';
				
				$html .= $this->gridMonthList($this->opts['start_date'], $this->opts['end_date'], $this->limit);
			$html .= '
					</ul>
				</div>
			</div>';

		} elseif($this->type == 'countdown') {

			$event_list = $this->upcomingCalendarLayout( true, ($this->limit + 1), '', null, null, true, false, true, false, false, '', false );

			$html .= '
			<div class="dp_pec_countdown_wrapper '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_content">
				';
				$event_count = 0;
				$daily_events = array();
				if(is_array($event_list)) {
					foreach ($event_list as $event) {

						if($event->id == "") 
							$event->id = $event->ID;
						
						$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

						if($event_count >= $this->limit) {
							break;
						}

						if($event->recurring_frecuency == 1 && $this->opts['group']){
					
							if(in_array($event->id, $daily_events)) {
								continue;	
							}
							
							$daily_events[] = $event->id;
						}

						$title = $event->title;
						$permalink = "";
						if($this->calendar_obj->link_post) {
							$permalink = dpProEventCalendar_get_permalink($event->id);
							$title = '<a href="'.$permalink.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	
						}
						
						//$edit_button = $this->getEditRemoveButtons($event);
						$time = strtotime($event->date);

						if($event->all_day && $time <= current_time('timestamp')) {
							continue;
						}

						$tzo = get_option('gmt_offset') * 60;
						if(substr($tzo, 0, 1) == "-") {
							$tzo = str_replace("-", "", $tzo);
						} else {
							$tzo = str_replace("+", "-", $tzo);
						}

						$html .= '<div class="dp_pec_countdown_event" data-countdown-tzo="'.$tzo.'" data-current-year="'.current_time("Y").'" data-current-month="'.current_time("m").'" data-current-day="'.current_time("d").'" data-current-hour="'.current_time("H").'" data-current-minute="'.current_time("i").'" data-current-second="'.current_time("s").'" data-countdown-year="'.date("Y",$time).'" data-countdown-month="'.date("m",$time).'" data-countdown-day="'.date("d",$time).'" data-countdown-hour="'.date("H",$time).'" data-countdown-minute="'.date("i",$time).'">';

						$post_thumbnail_id = get_post_thumbnail_id( $event->id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
						
						$html .= '<div class="dp_pec_event_photo_wrap">';
						$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';

						$html .= '	<div class="dp_pec_countdown_event_center_text">';
						$html .= '		<h2>'.$title.'</h2>';
						//$html .= '		<p>'.$event->date.'</p>';

						$html .= '		<ul class="dp_pec_countdown">';
						$html .= '			<li class="dp_pec_countdown_days_wrap"><span class="dp_pec_countdown_days">--</span><p class="dp_pec_countdown_days_txt" data-day="'.$this->translation['TXT_DAY'].'" data-day="'.$this->translation['TXT_DAYS'].'">'.$this->translation['TXT_DAYS'].'</p></li>';
						$html .= '			<li class="dp_pec_countdown_hours_wrap"><span class="dp_pec_countdown_hours">--</span><p class="dp_pec_countdown_hours_txt" data-hour="'.$this->translation['TXT_HOUR'].'" data-hours="'.$this->translation['TXT_HOURS'].'">'.$this->translation['TXT_HOURS'].'</p></li>';
						$html .= '			<li class="dp_pec_countdown_minutes_wrap"><span class="dp_pec_countdown_minutes">--</span><p class="dp_pec_countdown_minutes_txt">'.$this->translation['TXT_MINUTES'].'</p></li>';
						$html .= '			<li class="dp_pec_countdown_seconds_wrap"><span class="dp_pec_countdown_seconds">--</span><p class="dp_pec_countdown_seconds_txt">'.$this->translation['TXT_SECONDS'].'</p></li>';
						$html .= '		</ul>';

						$html .= '	</div>';
						$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
						$html .= '</div>';
						$html .= '<div style="clear:both;"></div>';

						$html .= '</div>';

						$event_count++;
					}
				}
			$html .= '
				</div>
				<div style="clear:both;"></div>
			</div>';

		} elseif($this->type == 'compact') {

			if($this->is_admin) {
				$html .= '
				<div class="dpProEventCalendar_ModalCalendar">';
			}

			$html .= '
			<div class="dp_pec_compact_wrapper dp_pec_wrapper '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			if(!isset($dpProEventCalendar['year_from'])) {
				$dpProEventCalendar['year_from'] = 2;
			}
			if(!isset($dpProEventCalendar['year_until'])) {
				$dpProEventCalendar['year_until'] = 3;
			}
			$html .= '
				<div class="dp_pec_nav">
					<span class="next_month"><i class="pec-default-arrow fa fa-chevron-right"></i><i style="display:none;" class="pec-rtl-arrow fa fa-chevron-left"></i></span>
					<span class="prev_month"><i class="pec-default-arrow fa fa-chevron-left"></i><i style="display:none;" class="pec-rtl-arrow fa fa-chevron-right"></i></span>
					<div class="dp_pec_wrap_month_year">
						<select autocomplete="off" class="pec_switch_month">
							';
							for($i = date('Y') - $dpProEventCalendar['year_from']; $i <= date('Y') + $dpProEventCalendar['year_until']; $i++) {
								foreach($this->translation['MONTHS'] as $key) {
									$html .= '
										<option value="'.$key.'-'.$i.'" '.($key.'-'.$i == $this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].'-'.$this->datesObj->currentYear ? 'selected="selected"':'').'>'.$key.' '.$i.'</option>';
								}
							}
				$html .= '
						</select>
						<select autocomplete="off" class="pec_switch_year">
							';
							for($i = date('Y') - $dpProEventCalendar['year_from']; $i <= date('Y') + $dpProEventCalendar['year_until']; $i++) {
								$html .= '
									<option value="'.$i.'" '.($i == $this->datesObj->currentYear ? 'selected="selected"':'').'>'.$i.'</option>';
							}
				$html .= '
						</select>
					</div>
					<div class="dp_pec_clear"></div>
				</div>
			';
				$html .= '<div class="dp_pec_clear"></div>';
				$html .= '<div class="dp_pec_content">';
					$html .= $this->monthlyCalendarLayout(true);
				$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="dp_pec_clear"></div>';

			if($this->is_admin) {
				$html .= '
				</div>';
			}
			
		} elseif($this->type == 'gmap-upcoming') {
			$html .= '
			<div class="dp_pec_gmap_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			
			$event_list = $this->upcomingCalendarLayout( true, $this->limit, '', null, null, true, false, true, true );
			$unique_events = array();
			$event_marker = "";
			$first_loc = "";
			
			if(is_array($event_list)) {
				
			
				foreach ($event_list as $obj) {
					if($obj->id == "") {
						$obj->id = $obj->ID;
					}

					$obj = (object)array_merge((array)$this->getEventData($obj->id), (array)$obj);

					if(is_numeric($obj->location_id)) {
						$map_lnlat = get_post_meta($obj->location_id, 'pec_venue_map_lnlat', true);
						$obj->map = get_post_meta($obj->location_id, 'pec_venue_map', true);
						if($obj->map == "" && $map_lnlat != "") {
							$obj->map = $obj->location;
						}
					} else {
						$map_lnlat = get_post_meta($obj->id, 'pec_map_lnlat', true);
					}

					if($obj->map == "") {
						continue;
					}
					
					if(!isset($unique_events[$obj->id])) {
						$unique_events[$obj->id] = '';
					}
					
					if(!is_object($unique_events[$obj->id])) {
						$unique_events[$obj->id] = $obj;

						$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($obj->date));
		
						$end_date = '';
						$end_year = '';
						if($obj->end_date != "" && $obj->end_date != "0000-00-00" && $obj->recurring_frecuency == 1) {
							$end_day = date('d', strtotime($obj->end_date));
							$end_month = date('n', strtotime($obj->end_date));
							$end_year = date('Y', strtotime($obj->end_date));
							
							//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
							$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($obj->end_date));
						}
											
						$end_time = "";
						if($obj->end_time_hh != "" && $obj->end_time_mm != "") { $end_time = str_pad($obj->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($obj->end_time_mm, 2, "0", STR_PAD_LEFT); }
						
						if($end_time != "") {
							
							
							$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

							$end_time = " / ".$end_time_tmp;
							if($end_time_tmp == $time) {
								$end_time = "";	
							}
						}
		
						if(isset($obj->all_day) && $obj->all_day) {
							$time = $this->translation['TXT_ALL_DAY'];
							$end_time = "";
						}
						
						$title = $obj->title;
						if($this->calendar_obj->link_post) {
							$title = '<a href="'.dpProEventCalendar_get_permalink($obj->id).'" target="'.$this->calendar_obj->link_post_target.'">'.addslashes($title).'</a>';	
						}
						
						$category = get_the_terms( $obj->id, 'pec_events_category' ); 
						$category_list_html = '';
						if(!empty($category)) {
							$category_count = 0;
							foreach ( $category as $cat){
								if($category_count > 0) {
									$category_list_html .= " / ";	
								}
								$category_list_html .= $cat->name;
								$category_count++;
							}
						}
						
						$video = get_post_meta($obj->id, 'pec_video', true);
						if($video != "") {
							$video = dpProEventCalendar_convertYoutube($video);
						}

						$event_timezone = dpProEventCalendar_getEventTimezone($obj->id);

						$event_time = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($obj->date)).$end_date.((($this->calendar_obj->show_time && !$obj->hide_time) || $obj->all_day) ? ' - '.$time.$end_time.($this->calendar_obj->show_timezone && !$obj->all_day ? ' '.$event_timezone : '') : '');

						if($obj->tbc) {
							$event_time = $this->translation['TXT_TO_BE_CONFIRMED'];
						}
						$event_marker .= 'pec_codeAddress("'.$obj->link.'", "'.$category_list_html.'", "'.$obj->phone.'",\''.$obj->map.'\', \''.addslashes($title).'\', \''.get_the_post_thumbnail($obj->id, 'medium').'\', \''.$event_time.'\', "'.$map_lnlat.'", \''.$video.'\'); ';
					}
					if($first_loc == "") {
						$first_loc = $obj->map;
					}
				}
				

			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_map_canvas" id="dp_pec_map_canvas'.$this->nonce.'"></div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					var geocoder, map, oms;

					function initialize'.$this->nonce.'() {
					 geocoder = new google.maps.Geocoder();
					 geocoder.geocode( { "address": "'.$first_loc.'"}, function(results, status) {
						  var latlng = results[0].geometry.location;

						  //center: '.($dpProEventCalendar['map_default_latlng'] != "" ? 'new google.maps.LatLng('.$dpProEventCalendar['map_default_latlng'].')' : 'latlng').'
						  var mapOptions = {
							zoom: '.($dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom']).',
							center: latlng
						  }
						  map = new google.maps.Map(document.getElementById("dp_pec_map_canvas'.$this->nonce.'"), mapOptions);

  						  oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true, keepSpiderfied: true});

  						  oms.addListener("click", function(marker) {
							infoBubble.close();
							  
						    infoBubble.setContent(marker.content);

					    	infoBubble.open(map, marker);
						  });
				';
				$html .= $event_marker;
				$html .= '
					 });
					  ';
				
				$html .= '
					}
					

					var infoBubble = new InfoBubble({
				        maxWidth: 290,
						maxHeight: 320,					
						shadowStyle: 0,
						padding: 0,
						backgroundColor: \'#fff\',
						borderRadius: 5,
						arrowSize: 20,
						borderWidth: 0,
						arrowPosition: 20,
						backgroundClassName: \'pec-infowindow\',
						arrowStyle: 2,
						hideCloseButton: true
				    });

					//var infowindow = new google.maps.InfoWindow();
					
					function getInfoWindowEvent(marker, content) {
						infowindow.close();
						infowindow.setContent(content);
						infowindow.open(map, marker);
					}

					var counter_run = 0;

					function pec_codeAddress(link, category, phone, address, title, image, eventdate, latlng, video) {
						
						var div_class = "dp_pec_map_infowindow";
						if(image == "") {
							div_class += " dp_pec_map_no_img";
						}

						var content = \'<div class="\'+div_class+\'">\'
							+(video != "" ? video : image)
							+\'<span class="dp_pec_map_date"><i class="fa fa-clock-o"></i>\'+eventdate+\'</span><div class="dp_pec_clear"></div>\'
							+\'<span class="dp_pec_map_title">\'+title+\'</span>\';

						if(address != "") {
							content += \'<span class="dp_pec_map_location"><i class="fa fa-map-marker"></i>\'+address+\'</span>\';
						}

						if(phone != "") {
							content += \'<span class="dp_pec_map_phone"><i class="fa fa-phone"></i>\'+phone+\'</span>\';
						}

						if(category != "") {
							content += \'<span class="dp_pec_map_category"><i class="fa fa-folder"></i>\'+category+\'</span>\';
						}

						if(link != "") {
							var link_ellipsy = link;

							if (link_ellipsy.length > 25) {
						        link_ellipsy = (link_ellipsy.substring(0, 25) + "...");
						    }
							content += \'<span class="dp_pec_map_link"><i class="fa fa-link"></i><a href="\'+link+\'" target="_blank" rel="nofollow">\'+link_ellipsy+\'</a></span>\';
						}
						content +=\'<div class="dp_pec_clear"></div>\'
							+\'</div>\';

						setTimeout(function() { 
						  if(latlng != "") {
							  latlng = latlng.split(",");
							  
							  var myLatlng = new google.maps.LatLng(latlng[0],latlng[1]);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: myLatlng,
								  icon: "'.$dpProEventCalendar['map_marker'].'",
								  content: content,
								  animation: google.maps.Animation.DROP
							  });	

							  oms.addMarker(marker);

						  } else {
						  geocoder.geocode( { "address": address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  //map.setCenter(results[0].geometry.location);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: results[0].geometry.location,
								  icon: "'.$dpProEventCalendar['map_marker'].'",
								  content: content,
								  animation: google.maps.Animation.DROP
							  });
							  
							  oms.addMarker(marker);
							} else {
								console.log("Geocode was not successful for the following reason: " + status);
							}
							
						  });
						  

						  }

						  if(!jQuery(".pec_infowindow_close", infoBubble.bubble_).length) {
						  	var close = jQuery(\'<a href="#" id="pec_infowindow_close" class="pec_infowindow_close"><i class="fa fa-close"></i></a>\');

						  	close.click(function(e) {
						  		e.preventDefault();
						  		infoBubble.close();
						  	});
						  	jQuery(infoBubble.bubble_).prepend(close);

						  }

						}, (counter_run < 10 ? 0 : (1000 * counter_run)) );
						  
						  counter_run++;
					}

					google.maps.event.addDomListener(window, "load", initialize'.$this->nonce.');
				});
				</script>';
			} else {
				$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></div>
				<div class="dp_pec_clear"></div>';	
			}
				
			$html .= '
			</div>';
		} elseif($this->type == 'add-event') {
			
			$allow_user_add_event_roles = explode(',', $this->calendar_obj->allow_user_add_event_roles);
			$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);

			if(!is_array($allow_user_add_event_roles) || empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "") {
				$allow_user_add_event_roles = array('all');	
			}
			
			if( 
				(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
				 in_array('all', $allow_user_add_event_roles) || 
				 (!is_user_logged_in() && !$this->calendar_obj->assign_events_admin)
				)
			) {

			
				$html .= '
				<div class="dp_pec_new_event_wrapper '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
				';
				$html .= '
					<div style="clear:both;"></div>
					
					<div class="dp_pec_content_header">
						<span class="events_loading"></span>
						<h2 class="actual_month"><span class="fa fa-plus-circle"></span>'.str_replace('+', '', $this->translation['TXT_ADD_EVENT']).'</h2>
					</div>
					
					<div class="dp_pec_content">
						';
					if(!is_user_logged_in() && !$this->calendar_obj->assign_events_admin) {
						$html .= '<div class="dp_pec_new_event_login"><span>'.$this->translation['TXT_EVENT_LOGIN'].'</span></div>';	
					} else {
						$html .= '
						<div class="dp_pec_new_event_login dp_pec_notification_event_succesfull">
						'.$this->translation['TXT_EVENT_THANKS'].'
						</div>';
						$html .= '
						<form enctype="multipart/form-data" method="post" class="add_new_event_form">
						<input type="text" class="dp_pec_new_event_text dp_pec_form_title pec_required" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" name="title" />
						';
						if($this->calendar_obj->form_show_description) {
							if($this->calendar_obj->form_text_editor) {
								// Turn on the output buffer
								ob_start();
								
								// Echo the editor to the buffer
								wp_editor('', $this->nonce.'_event_description', array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'textarea_rows' => 5, 'teeny' => true));
								
								// Store the contents of the buffer in a variable
								$editor_contents = ob_get_clean();
								
								$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
							} else {
								$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" class="dp_pec_new_event_text" id="" name="description" cols="50" rows="5"></textarea>';
							}
							
						}
						$html .= '
						<div class="dp_pec_row">
							<div class="dp_pec_col6">
								';
								if($this->calendar_obj->form_show_category) {
									$cat_arr = array();
									if(!empty($this->category)) {
										$cat_arr = explode(",", $this->category);
									}

									$cat_args = array(
											'taxonomy' => 'pec_events_category',
											'hide_empty' => 0
										);
									if($this->calendar_obj->category_filter_include != "") {
										$cat_args['include'] = $this->calendar_obj->category_filter_include;
									}
									$categories = get_categories($cat_args); 
									if(count($categories) > 0) {
										$html .= '
										<div class="dp_pec_row">
											<div class="dp_pec_col12">
												<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
												';
												foreach ($categories as $category) {
													if(!empty($cat_arr) && !in_array($category->term_id, $cat_arr)) {
														continue;
													}
													$html .= '<div class="pec_checkbox_list">';
													$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" />';
													$html .= $category->cat_name;
													$html .= '</div>';
												  }
												$html .= '	
												<div class="dp_pec_clear"></div>	
											</div>
										</div>
										';
									}
								}
								$html .= '
								<div class="dp_pec_row">
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
										<div class="dp_pec_clear"></div>
										<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_date_input" value="'.date('Y-m-d').'" />
									</div>
									
									<div class="dp_pec_col6 dp_pec_end_date_form">';
									if($this->calendar_obj->form_show_end_date) {
									$html .= '
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
										<div class="dp_pec_clear"></div>
										<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_end_date_input" value="" />
										<button type="button" class="dp_pec_clear_end_date">
											<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
										</button>';
									}
										$html .='
									</div>
									<div class="dp_pec_clear"></div>
								</div>';
								if($this->calendar_obj->form_show_extra_dates) {
									$html .= '<div class="dp_pec_row">';
									$html .= '<div class="dp_pec_col12">';
									$html .= '<input type="text" value="" placeholder="'.$this->translation['TXT_EXTRA_DATES'].'" id="" class="dp_pec_extra_dates" readonly="readonly" style="max-width: 300px;" name="extra_dates" />';
									$html .= '</div>';
									$html .= '<div class="dp_pec_clear"></div>
									</div>';
								}

								$html .= '<div class="dp_pec_row">';
								if($this->calendar_obj->form_show_start_time) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
										<div class="dp_pec_clear"></div>
										<select autocomplete="off" class="dp_pec_new_event_time" name="time_hours" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">';
											for($i = 0; $i <= 23; $i++) {
												$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
												if(dpProEventCalendar_is_ampm()) {
													$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
												}
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
											}
										$html .= '
										</select>
										<select autocomplete="off" class="dp_pec_new_event_time" name="time_minutes" id="pec_time_minutes" style="width:50px;">';
											for($i = 0; $i <= 59; $i += 5) {
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
											}
										$html .= '
										</select>
									</div>';
								}
								if($this->calendar_obj->form_show_end_time) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
										<div class="dp_pec_clear"></div>
										<select autocomplete="off" class="dp_pec_new_event_time" name="end_time_hh" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">
											<option value="">--</option>';
											for($i = 0; $i <= 23; $i++) {
												$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
												if(dpProEventCalendar_is_ampm()) {
													$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
												}
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
											}
										$html .= '
										</select>
										<select autocomplete="off" class="dp_pec_new_event_time" name="end_time_mm" id="" style="width:50px;">
											<option value="">--</option>';
											for($i = 0; $i <= 59; $i += 5) {
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
											}
										$html .= '
										</select>
									</div>';
								}
								$html .= '
									<div class="dp_pec_clear"></div>
								</div>';
								
								$html .= '<div class="dp_pec_row">';

								if($this->calendar_obj->form_show_hide_time) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
										<select autocomplete="off" name="hide_time">
											<option value="0">'.$this->translation['TXT_NO'].'</option>
											<option value="1">'.$this->translation['TXT_YES'].'</option>
										</select>
										';
										
										if($this->calendar_obj->form_show_all_day) {
											
											$html .= '
											<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
											<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
												
										}
										
									$html .= '
									</div>';
								} elseif($this->calendar_obj->form_show_all_day) {
									
									$html .= '
									<div class="dp_pec_col6">
										<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
										<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
									</div>';	
	
								}
								
								if($this->calendar_obj->form_show_frequency) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
										<select autocomplete="off" name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
											<option value="0">'.$this->translation['TXT_NONE'].'</option>
											<option value="1">'.$this->translation['TXT_EVENT_DAILY'].'</option>
											<option value="2">'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
											<option value="3">'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
											<option value="4">'.$this->translation['TXT_EVENT_YEARLY'].'</option>
										</select>
									';
									
										$html .= '
										<div class="pec_daily_frequency" style="display:none;">
											<div id="pec_daily_every_div">' . $this->translation['TXT_EVERY'] . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="1" /> '.$this->translation['TXT_DAYS'] . ' </div>
											<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" />'. $this->translation['TXT_ALL_WORKING_DAYS'] . '</div>
										</div>';
										
										$html .= '
										<div class="pec_weekly_frequency" style="display:none;">
											
											<div class="dp_pec_clear"></div>
											
											'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="1" /> '. $this->translation['TXT_WEEKS_ON'].'
											
											<div class="dp_pec_clear"></div>
											
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="1" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_MON'] . '
											</div>
											<div class="pec_checkbox_list">	
												<input type="checkbox" class="checkbox" value="2" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_TUE'] . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="3" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_WED'] . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="4" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_THU'] . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="5" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_FRI'] . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="6" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SAT'] . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="7" name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SUN'] . '
											</div>
											
										</div>';
										
										$html .= '
										<div class="pec_monthly_frequency" style="display:none;">
											
											<div class="dp_pec_clear"></div>
											
											'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="1" /> ' . $this->translation['TXT_MONTHS_ON'] . '
											
											<div class="dp_pec_clear"></div>
											
											<select autocomplete="off" name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
												<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
												<option value="first"> ' . $this->translation['TXT_FIRST'] . '</option>
												<option value="second"> ' . $this->translation['TXT_SECOND'] . '</option>
												<option value="third"> ' . $this->translation['TXT_THIRD'] . '</option>
												<option value="fourth"> ' . $this->translation['TXT_FOURTH'] . '</option>
												<option value="last"> ' . $this->translation['TXT_LAST'] . '</option>
											</select>
											
											<select autocomplete="off" name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
											<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
												<option value="monday"> ' . $this->translation['DAY_MONDAY'] . '</option>
												<option value="tuesday"> ' . $this->translation['DAY_TUESDAY'] . '</option>
												<option value="wednesday"> ' . $this->translation['DAY_WEDNESDAY'] . '</option>
												<option value="thursday"> ' . $this->translation['DAY_THURSDAY'] . '</option>
												<option value="friday"> ' . $this->translation['DAY_FRIDAY'] . '</option>
												<option value="saturday"> ' . $this->translation['DAY_SATURDAY'] . '</option>
												<option value="sunday"> ' . $this->translation['DAY_SUNDAY'] . '</option>
											</select>
										</div>
									</div>';
								}
								
								if($this->calendar_obj->form_show_booking_enable ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="checkbox" class="checkbox" name="booking_enable" id="" value="1" />
										<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_ALLOW_BOOKINGS'].'</span>
									</div>';
								}
								
								$html .= '
								</div>';

								$html .= '
								<div class="dp_pec_row">';
								if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="number" min="0" value="" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.$this->translation['TXT_PRICE'].'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
									</div>';
								}
								
								if($this->calendar_obj->form_show_booking_block_hours ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="number" min="0" value="" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_BLOCK_HOURS'].'" id="" name="block_hours" />
									</div>';
								}

								if($this->calendar_obj->form_show_booking_limit ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="number" min="0" value="" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_LIMIT'].'" id="" name="limit" />
									</div>';
								}

								$html .= '
									<div class="dp_pec_clear"></div>
								</div>
								';

								if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "" ) {
									$html .= '
									<div class="dp_pec_row">';
									
									$html .= '
										<div class="dp_pec_col12">
											<div id="pec_new_event_captcha"></div>
										</div>';
									

									$html .= '
										<div class="dp_pec_clear"></div>
									</div>
									';
								}
								
								$html .= '
								<div class="dp_pec_clear"></div>
							</div>
							<div class="dp_pec_col6">
								';
								if($this->calendar_obj->form_show_image) {
									$rand_image = rand();
									$html .= '
									<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
									<div class="dp_pec_add_image_wrap">
										<label for="event_image_'.$this->nonce.'_'.$rand_image.'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
									</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.'" class="event_image" style="visibility:hidden; position: absolute;" />							
									';
								}
								if($this->calendar_obj->form_show_color) {
									$html .= '
									<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_COLOR'].'</span>
									<select autocomplete="off" name="color" class="pec_color_form">
										<option value="">'.$this->translation['TXT_NONE'].'</option>
										 ';
										 
										$counter = 0;
										$querystr = "
										SELECT *
										FROM ". $this->table_special_dates ." 
										ORDER BY title ASC
										";
										$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
										foreach($sp_dates_obj as $sp_dates) {
										
										$html .= '<option value="'.$sp_dates->id.'">'.$sp_dates->title.'</option>';
										
										}
									$html .= ' 
									</select>
									<div class="dp_pec_clear"></div>';
								}

								if($this->calendar_obj->form_show_timezone) {
									$html .= '
									<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_TIMEZONE'].'</span>
									<select autocomplete="off" name="timezone" class="pec_timezone_form">';
									$current_offset = get_option('gmt_offset');
									$tzstring = get_option('timezone_string');
									if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
										$check_zone_info = false;
										if ( 0 == $current_offset )
											$tzstring = 'UTC+0';
										elseif ($current_offset < 0)
											$tzstring = 'UTC' . $current_offset;
										else
											$tzstring = 'UTC+' . $current_offset;
									}

									$pec_timezone = $tzstring;

					                $html .= wp_timezone_choice($pec_timezone); 
									$html .= ' 
									</select>
									<div class="dp_pec_clear"></div>';
								}

								if($this->calendar_obj->form_show_location) {
									$html .= '
									<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_LOCATION'].'</span>
									<select autocomplete="off" name="location" class="pec_location_form">
										<option value="">'.$this->translation['TXT_NONE'].'</option>
										 ';

										$args = array(
										'posts_per_page'   => -1,
										'post_type'        => 'pec-venues',
										'post_status'      => 'publish',
										'order'			   => 'ASC', 
										'orderby' 		   => 'title' );

										$venues_list = get_posts($args);
										foreach($venues_list as $venue) {

											$html .= '<option value="'.$venue->ID.'">'.$venue->post_title.'</option>';
										
										}

										if($this->calendar_obj->form_show_location_options) {
											$html .= '<option value="-1">'.$this->translation['TXT_OTHER'].'</option>';
										}
										
									$html .= ' 
									</select>
									<div class="dp_pec_clear"></div>';

									if($this->calendar_obj->form_show_location_options) {

										$html .= '
										<div class="pec_location_options" style="display:none;">
											<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION_NAME'].'" id="" name="location_name" />';

											$html .= '
											<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_ADDRESS'].'" id="" name="location_address" />';

											$html .= '
											<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
											<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
											<div class="map_lnlat_wrap" style="display:none;">
												<span class="dp_pec_form_desc">'.$this->translation['TXT_DRAG_MARKER'].'</span>
												<div id="pec_mapCanvas" style="height: 400px;"></div>
											</div>
										</div>
										';
									}
									/*$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';*/
								}

								if($this->calendar_obj->form_show_link) {
									$html .= '
									<input type="url" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
								}

								if($this->calendar_obj->form_show_phone) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
								}
								
								$cal_form_custom_fields = $this->calendar_obj->form_custom_fields;
								$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

								if(is_array($dpProEventCalendar['custom_fields_counter'])) {
									$counter = 0;
									
									foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
										
										if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
											$counter++;
											continue;
										}

										if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {

											$html .= '
											<div class="dp_pec_wrap_checkbox">
											<input type="checkbox" class="checkbox '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="1" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
											</div>';
								
										} else {

										$html .= '
											<input type="text" class="dp_pec_new_event_text '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
											
										}
										$counter++;		
									}
								}
									$html .= '
							</div>
						</div>
						<div class="dp_pec_clear"></div>
						<div class="pec-add-footer">
							<button class="dp_pec_submit_event" data-lang-sending="'.$this->translation['TXT_SENDING'].'">'.($this->calendar_obj->publish_new_event ? $this->translation['TXT_SUBMIT'] : $this->translation['TXT_SUBMIT_FOR_REVIEW']).'</button>
							<div class="dp_pec_clear"></div>
						</div>
						</form>';
					}
						$html .= '
					</div>
					<div class="dp_pec_clear"></div>
				</div>';

			}
			
		} elseif($this->type == 'list-author') {
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
						
			$html .= $this->upcomingCalendarLayout(false, 10, '', null, null, true, true);

			$html .= '
								
				</div>
			</div>';
		} elseif($this->type == 'bookings-user') {
			global $current_user;
			
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
			
			$bookings_list = $this->getBookingsByUser($current_user->ID);
			if(!is_array($bookings_list) || empty($bookings_list)) {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			} else {
				foreach($bookings_list as $key) {
					$title = '<span class="dp_pec_event_title_sp">'.get_the_title($key->id_event).'</span>';
					if($this->calendar_obj->link_post) {
						$title = '<a href="'.dpProEventCalendar_get_permalink($key->id_event).'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	
					}

					$status = "";
					switch($key->status){
						case '':
							$status = '';
							break;
						case 'pending':
							$status = $this->translation['TXT_PENDING'];
							break;
						case 'canceled_by_user':
							$status = $this->translation['TXT_CANCELED_BY_USER'];
							break;
						case 'canceled':
							$status = $this->translation['TXT_CANCELED'];
							break;
					}
				
						$html .= '<div class="dp_pec_date_event">';

						$html .= '<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($key->event_date)). ' ' .dpProEventCalendar_date_i18n($this->time_format, strtotime(get_post_meta($key->id_event, 'pec_date', true))).'</span>

						<span class="dp_pec_date_time">'.$status.'</span>';

						if($key->status == 'canceled' && $key->cancel_reason != '') {
							$html .= '<p class="dp_pec_cancel_reason">'.nl2br($key->cancel_reason).'</p>';
						}

						if($this->calendar_obj->booking_cancel && $key->status != 'canceled_by_user' && $key->status != 'canceled') {
							$html .= '<a href="#" class="pec_cancel_booking">'.$this->translation['TXT_CANCEL_BOOKING'].'</a>
							<div style="display:none;">
								<form enctype="multipart/form-data" method="post" class="add_new_event_form remove_event_form">
							
								<input type="hidden" value="'.$key->id.'" name="cancel_booking_id">
								<input type="hidden" value="'.$key->id_event.'" name="cancel_booking_event">
								<p>'.$this->translation['TXT_CANCEL_BOOKING_CONFIRM'].'</p>
								<div class="dp_pec_clear"></div>
								<div class="pec-add-footer">
									<button class="dp_pec_cancel_booking pec_action_btn">'.$this->translation['TXT_YES'].'</button>
									<button class="dp_pec_close pec_action_btn pec_action_btn_secondary">'.$this->translation['TXT_NO'].'</button>
									<div class="dp_pec_clear"></div>
								</div>
								</form>
							</div>';		
						}

						$html .= '
						<div class="dp_pec_clear"></div>
						<h2 class="dp_pec_event_title">
							'.$title.'
						</h2>';

						

					$html .= '
					</div>';
					
				}
			}
			

			$html .= '
								
				</div>
			</div>';
		} elseif($this->type == 'today-events') {
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' dp_pec_today_events" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
			
			//$html .= date('Y-m-d H:i:s', $this->defaultDate).'<br>';
			//date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
			//$html .= date('Y-m-d H:i:s', $this->defaultDate).'<br>';
			
			$html .= $this->eventsListLayout(date('Y-m-d', $this->defaultDate), false);
			
			$html .= '
								
				</div>
			</div>';
		}
		
		
		if($print)
			echo $html;	
		else
			return $html;
		
	}
	
	function eventsMonthList($start_search = null, $end_search = null, $limit = 40, $keyword = '') {
		
		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();
		
		$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
		if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
			$pagination = $this->opts['pagination'];
		}
		$event_counter = 1;
		
		$event_list = $this->upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, true, false, false, $keyword );
		
		if(is_array($event_list) && count($event_list) > 0) {
			
			
			foreach($event_list as $event) {
				
				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1){
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				}
				
				$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));

				$end_date = '';
				$end_year = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->end_date != date('Y-m-d', strtotime($event->date)) && $event->recurring_frecuency == 1) {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					$end_year = date('Y', strtotime($event->end_date));
					
					//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
					$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
				}
									
				$end_time = "";
				if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

					$end_time = " - ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if(isset($event->all_day) && $event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $event->title;

				$permalink = "";
				if($this->calendar_obj->link_post) {
					$permalink = dpProEventCalendar_get_permalink($event->id);
					$use_link = get_post_meta($event->id, 'pec_use_link', true);
					$href = $permalink;

					if(!$use_link) {
						if ( get_option('permalink_structure') ) {
							$permalink_format = rtrim($permalink, '/');
							if(strpos($permalink, "?") !== false) {
								$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
							} else {
								$permalink_query = "";
							}

							$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
							$href = $permalink_format.'/'.strtotime($event->date).$permalink_query;
						} else {
							$href = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
						}
					}

					$title = '<a href="'.$href.'" target="'.$this->calendar_obj->link_post_target.'">'.$title.'</a>';	
				}
				
				$edit_button = $this->getEditRemoveButtons($event);
				
				$category = get_the_terms( $event->id, 'pec_events_category' ); 
				$category_list_html = '';
				$category_slug = '';
				if(!empty($category)) {
					$category_count = 0;
					$category_list_html .= '
						<span class="dp_pec_event_categories"><i class="fa fa-folder"></i>';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$category_list_html .= " / ";	
						}
						$category_list_html .= $cat->name;
						$category_slug .= 'category_'.$cat->slug.' ';
						$category_count++;
					}
					$category_list_html .= '
						</span>';
				}
					
				$html .= '
				<div class="dp_pec_isotope '.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"  data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
				
					<div class="dp_pec_accordion_event '.$category_slug.'">';
					

					$post_thumbnail_id = get_post_thumbnail_id( $event->id );
					$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
					if($post_thumbnail_id) {
						$html .= '<div class="dp_pec_event_photo_wrap">';
						$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$image_attributes[0].');"></div>';
						
						if($event->featured_event) {
							$html .= '<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>';
						}

						$html .= '	<div class="dp_pec_accordion_event_center_text">';
						$html .= '		<h2>'.$title.'</h2>';

						$html .= $this->getRating($event->id);

						if($this->calendar_obj->show_author) {
							$author = get_userdata(get_post_field( 'post_author', $event->id ));
							$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
						}

						$html .= '	</div>';
						$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
						$html .= '</div>';
					} else {
						$html .= '<div class="dp_pec_accordion_event_inner">';
						if($event->featured_event) {
							$html .= '
							<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>
							<div class="dp_pec_clear"></div>';
						}

						$html .= '<div class="dp_pec_accordion_event_head_noimg">';
						$html .= '<h2>'.$title.'</h2>';
						$html .= $this->getRating($event->id);
						$html .= '</div>';
					}

					$html .= '<div class="dp_pec_clear"></div>';

					if($post_thumbnail_id) {
						$html .= '<div class="dp_pec_accordion_event_inner">';
					}
						

						$html .= $edit_button;
							
						
						
						$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

						if($event->tbc) {
							$html .= '<span class="pec_time"><i class="fa fa-calendar-o"></i>'.$this->translation['TXT_TO_BE_CONFIRMED'] .'</span>';
						} else {
							$html .= '<span class="pec_time"><i class="fa fa-calendar-o"></i>'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)).$end_date .'</span>';
							$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');
							if($pec_time != "") {
								$html .= '<span class="pec_time"><i class="fa fa-clock-o"></i>'.$pec_time.'</span>';
							}
						}
						
						$html .= '<div class="dp_pec_clear"></div>';

						if($event->location != '') {
							if($event->location_address != "") {
								$event->location .= ' <br><span>'.$event->location_address.'</span>';
							}

							$html .= '
							<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
						}
						if($event->phone != '') {
							$html .= '
							<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
						}
	
						if($category_list_html != "") {
							$html .= $category_list_html;
						}

						if($event->link != '') {
							$event->link = trim($event->link);
							if(substr($event->link, 0, 4) != "http" && substr($event->link, 0, 4) != "mail") {
								$event->link = 'http://'.$event->link;
							}

							$html .= '
							<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i><span>'.$event->link.'</span></a>
							<div class="dp_pec_clear"></div>';
						}
						
				$html .= '
						<div class="pec_description">';
						$booking_booked = $this->getBookingBookedLabel($event->id, $event->date);
						if($booking_booked == "") {
							$html .= $this->getBookingButton($event->id, date('Y-m-d', strtotime($event->date)));
						} else {
							$html .= $booking_booked;
						}

				$excerpt = get_post_meta($event->id, 'pec_excerpt', true);
				$event_desc = $event->description;
				
				//if($this->limit_description > 0) {
				if($this->limit_description == 0) {
					if($this->widget) {
						$this->limit_description = 60;
					} else {
						$this->limit_description = 150;	
					}
				}

				if($excerpt != "") {
					$event_desc_short = $excerpt;
				} else {
					$event_desc_short = force_balance_tags(html_entity_decode(dpProEventCalendar_trim_words($event_desc, $this->limit_description)));
				}
				
				$html .= '
						<div class="dp_pec_event_description">';
						if(post_password_required($event->id)) {
							$html .= get_the_password_form();
						} else {
							$html .= '
							<div class="dp_pec_event_description_short" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:block"' : '').'>
								<p>'.do_shortcode(nl2br($event_desc_short)).'</p>
								<a href="'.($permalink == "" ? '#' : $permalink).'" '.($permalink == "" ? '' : 'target="'.$this->calendar_obj->link_post_target.'"').' class="dp_pec_event_description_more" '.(isset($map_id) && $map_id != "" ? 'onclick="setTimeout(initialize_'.$map_id.', 100);"' : '').'>'.$this->translation['TXT_MORE'].'</a>
							</div>
							<div class="dp_pec_event_description_full" '.(str_word_count($event_desc) > $this->limit_description || $excerpt != "" ? 'style="display:none"' : '').'>
								'.do_shortcode(nl2br($event_desc));
								$html .= '<div class="dp_pec_clear"></div>';
							$html .= '
							</div>';
						}
						$html .= '
						</div>';
											
						$html .= '
							<div class="dp_pec_date_event_icons">';
						
						//$html .= $this->getEventShare($event);
						
						
						if($this->calendar_obj->ical_active && !post_password_required($event->id)) {
							$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ) . "'><i class='fa fa-calendar-plus-o'></i>iCal</a><br class='clear' />";
						}
						
						$html .= '
							</div>';
					if($event->map != '' || is_numeric($event->location_id)) {

						if(is_numeric($event->location_id)) {
							$map_lnlat = get_post_meta($event->location_id, 'pec_venue_map_lnlat', true);
						} else {
							$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
						}

						if($map_lnlat != "") {
							$event->map = str_replace(" ", "", $map_lnlat);
						}

						$map_id = $event->id.'_'.$this->nonce.'_'.rand();
						$geocode = false;
						$html .= $this->getMap($map_id, $event->map, $event->location_id, $geocode);
					}
				$html .= '
							</div>
						</div>
					</div>
				</div>';
				
				
				$event_counter++;
			}
			
			if(($event_counter - 1) > $pagination) {
				$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.$this->translation['TXT_MORE'].'</a>
					<div class="dp_pec_clear"></div>
				';
			}
		} else {
			$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></div>
			<div class="dp_pec_clear"></div>';	
		}
		
		return $html;
	}
	
	function gridMonthList($start_search = null, $end_search = null, $limit = 20) {
		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();
		$event_counter = 1;
		
		$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
		if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
			$pagination = $this->opts['pagination'];
		}

		$past = false;
		if($this->opts['scope'] == 'past') {
			$past = true;
		}

		$event_list = $this->upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, false, false, $past );

		if(is_array($event_list) && count($event_list) > 0) {

			foreach($event_list as $event) {
				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)$this->getEventData($event->id), (array)$event);

				$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1){
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				}
					
				$time = dpProEventCalendar_date_i18n($this->time_format, strtotime($event->date));

				$end_date = '';
				$end_year = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					$end_year = date('Y', strtotime($event->end_date));
					
					//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
					$end_date = ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
				}
									
				$end_time = "";
				if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					$end_time_tmp = dpProEventCalendar_date_i18n($this->time_format, strtotime("2000-01-01 ".$end_time.":00"));

					$end_time = " / ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if(isset($event->all_day) && $event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $event->title;
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				$no_bg = false;
				
				if($post_thumbnail_id) {
					$event_bg = $image_attributes[0];
				} else {
					$no_bg = true;	
				}
				
				if($end_date == ' '.$this->translation['TXT_TO'].' '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date))) {
					$end_date = '';	
				}
					
				$html .= '<li class="dp_pec_grid_event dp_pec_isotope dp_pec_grid_columns_'.$this->columns.' '.($no_bg ? 'dp_pec_grid_no_img' : '').'" data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>';

					if($event->featured_event) {

						$html .= '<span class="pec_featured"><i class="fa fa-star"></i>'.$this->translation['TXT_FEATURED'].'</span>';

					}

					$permalink = dpProEventCalendar_get_permalink($event->id);

					$use_link = get_post_meta($event->id, 'pec_use_link', true);
					$href = $permalink;

					if(!$use_link) {
						if ( get_option('permalink_structure') ) {
							$permalink_format = rtrim($permalink, '/');
							if(strpos($permalink, "?") !== false) {
								$permalink_query = substr($permalink_format, (strpos($permalink, "?") ));
							} else {
								$permalink_query = "";
							}
							$permalink_format = rtrim(str_replace($permalink_query, "", $permalink_format), '/');
							$href = $permalink_format.'/'.strtotime($event->date).$permalink_query;
						} else {
							$href = $permalink.(strpos($permalink, "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
						}	
					}

					$html .= '<a href="'.$href.'" class="dp_pec_grid_link_image" target="'.$this->calendar_obj->link_post_target.'" title="" style="background-image:url(\''.$event_bg.'\');"></a>';
					
					$html .= '<div class="dp_pec_grid_event_center_text">';
					$html .= '<h2 class="dp_pec_grid_title">'.$title.'</h2>';
					if($event->tbc) {
						$html .= '<span class="pec_date">'.$this->translation['TXT_TO_BE_CONFIRMED'].'</span>';
					} else {
						$html .= '<span class="pec_date">'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)).$end_date.'</span>';
					}
				/*	$category = get_the_terms( $event->id, 'pec_events_category' ); 
						$category_list_html = '';
						if(!empty($category)) {
							$category_count = 0;
							foreach ( $category as $cat){
								if($category_count > 0) {
									$category_list_html .= " / ";	
								}
								$category_list_html .= $cat->name;
								$category_count++;
							}
						}*/
					$html .= '<span class="pec_date">'.$category_list_html.'</span>';
					if(is_numeric($event->organizer)) {
						$organizer = get_the_title($event->organizer);
						$html .= '<span class="pec_organizer">'.$organizer.'</span>';
					}
					$html .= $this->getRating($event->id);
				
					$html .= '</div>';

					$html .= '<div class="dp_pec_grid_text_wrap">';
						$html .= '<div class="dp_pec_grid_meta_data">';
							
						if($this->calendar_obj->show_author) {
							$author = get_userdata(get_post_field( 'post_author', $event->id ));
							$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
							$html .= '<div class="dp_pec_clear"></div>';
						}
						
						$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.($this->calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');
						if($pec_time != "" && !$event->tbc) {
							$html .= '<span class="pec_time">'.$pec_time.'</span>';
						}
						$html .= '<div class="dp_pec_clear"></div>';
						
						if($event->location != '') {
							if($event->location_address != "") {
								$event->location .= ' <br><span>'.$event->location_address.'</span>';
							}

							$html .= '<span class="dp_pec_event_location">'.$event->location.'</span>';
							$html .= '<div class="dp_pec_clear"></div>';
						}
						if($event->phone != '') {
							$html .= '<span class="dp_pec_event_phone">'.$event->phone.'</span>';
							$html .= '<div class="dp_pec_clear"></div>';
						}
						$html .= '</div>';					
					$html .= '</div>';
			$html .= '</li>';
				
				$event_counter++;
			}

			if(($event_counter - 1) > $pagination) {
				$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.$this->translation['TXT_MORE'].'</a>';
			}
		} else {
			$html .= '<li class="dp_pec_grid_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></li>';	
		}
		
		return $html;
	}
	
	function switchCalendarTo($type, $limit = 5, $limit_description = 0, $category = 0, $author = 0, $event_id = 0, $location = 0) {
		if(!is_numeric($limit)) { $limit = 5; }
		$this->type = $type;
		$this->limit = $limit;
		$this->limit_description = $limit_description;
		$this->category = $category;
		$this->opts['location'] = $location;
		$this->event_id = $event_id;
		$this->author = $author;
	}
	
	function calendarSubscription($email, $name) {
		global $wpdb;
		
		require_once (dirname (__FILE__) . '/../mailchimp/miniMCAPI.class.php');

		if(stripslashes($email) != '' ){
			
			$exists = $wpdb->get_row("SELECT COUNT(*) as counter FROM ".$this->table_subscribers_calendar." WHERE calendar = ".$this->id_calendar." AND email = '".$email."'");

			if($exists->counter == 0) {
				$wpdb->insert( 
					$this->table_subscribers_calendar, 
					array( 
						'name' => $name, 
						'email' => $email, 
						'calendar' => $this->id_calendar, 
						'subscription_date' => current_time('mysql')
					), 
					array( 
						'%s', 
						'%s', 
						'%d', 
						'%s' 
					) 
				);
			}
				
			if($this->calendar_obj->mailchimp_api != "" && $this->calendar_obj->subscribe_active) {
				/*$mailchimp_class = new mailchimpSF_MCAPI($this->calendar_obj->mailchimp_api);
				
				$retval = $mailchimp_class->listSubscribe( $this->calendar_obj->mailchimp_list, $email );
		
				if(!$mailchimp_class->errorCode){	
					die('ok');
				}else{
					die('0');
				}*/

				$data = array(
					'email_address' => $email,
					'status' => 'subscribed'
				);
				 
				$url = 'https://' . substr($this->calendar_obj->mailchimp_api,strpos($this->calendar_obj->mailchimp_api,'-')+1) . '.api.mailchimp.com/3.0/'.$this->calendar_obj->mailchimp_list.'/members/';
				
				$result = json_decode( dpProEventCalendar_mailchimp_curl_connect( $url, 'POST', $this->calendar_obj->mailchimp_api, $data) );
				die('ok');
			}
		}
	}
	
	function getRating($eventid) {

		if(post_password_required($eventid)) {
			return '';
		}

		$html = "";
		$rate 		= get_post_meta($eventid, 'pec_rate', true);
		$user_rate 	= get_post_meta($eventid, 'pec_user_rate', true);
		

		if($user_rate != "") {
			$star1 = count(get_post_meta($eventid, 'pec_user_rate_1star'));
			$star2 = count(get_post_meta($eventid, 'pec_user_rate_2star'));
			$star3 = count(get_post_meta($eventid, 'pec_user_rate_3star'));
			$star4 = count(get_post_meta($eventid, 'pec_user_rate_4star'));
			$star5 = count(get_post_meta($eventid, 'pec_user_rate_5star'));
			
			$total_votes = $star1 + $star2 + $star3 + $star4 + $star5;
			
			if($total_votes == 0) {
				$rate = 0;
			} else {
				$rate = ((
					$star1 +
					($star2 * 2) +
					($star3 * 3) +
					($star4 * 4) +
					($star5 * 5)) /
						$total_votes);
			}
		}
		
		if($rate != '' || $rate === 0) {
			
			$s1 = '<i class="fa fa-star-o"></i>';
			if($rate > 0 && $rate < 1) {
				$s1 = '<i class="fa fa-star-half-o"></i>';
			}
			if($rate >= 1) {
				$s1 = '<i class="fa fa-star"></i>';
			}
			$s2 = '<i class="fa fa-star-o"></i>';
			if($rate > 1 && $rate < 2) {
				$s2 = '<i class="fa fa-star-half-o"></i>';
			}
			if($rate >= 2) {
				$s2 = '<i class="fa fa-star"></i>';
			}
			$s3 = '<i class="fa fa-star-o"></i>';
			if($rate > 2 && $rate < 3) {
				$s3 = '<i class="fa fa-star-half-o"></i>';
			}
			if($rate >= 3) {
				$s3 = '<i class="fa fa-star"></i>';
			}
			$s4 = '<i class="fa fa-star-o"></i>';
			if($rate > 3 && $rate < 4) {
				$s4 = '<i class="fa fa-star-half-o"></i>';
			}
			if($rate >= 4) {
				$s4 = '<i class="fa fa-star"></i>';
			}
			$s5 = '<i class="fa fa-star-o"></i>';
			if($rate > 4 && $rate < 5) {
				$s5 = '<i class="fa fa-star-half-o"></i>';
			}
			if($rate >= 5) {
				$s5 = '<i class="fa fa-star"></i>';
			}

			$html = '
			<ul class="dp_pec_rate '.($user_rate != "" && is_user_logged_in() ? 'dp_pec_user_rate' : '').'">
				<li>
					'.($user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">'.$s1.'</a>' : '<span>'.$s1.'</a>').'
				</li>
				<li>
					'.($user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">'.$s2.'</a>' : '<span>'.$s2.'</a>').'
				</li>
				<li>
					'.($user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">'.$s3.'</a>' : '<span>'.$s3.'</a>').'
				</li>
				<li>
					'.($user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">'.$s4.'</a>' : '<span>'.$s4.'</a>').'
				</li>
				<li>
					'.($user_rate != "" && is_user_logged_in() ? '<a href="#" data-rate-val="1" data-event-id="'.$eventid.'">'.$s5.'</a>' : '<span>'.$s5.'</a>').'
				</li>
			</ul>';
		}
		
		return $html;
	}
	
	function rateEvent($eventid, $rate) {
		
		if(is_user_logged_in() && is_numeric($eventid)) {
			global $current_user;
			wp_get_current_user();
			
			delete_post_meta($eventid, 'pec_user_rate_1star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_2star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_3star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_4star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_5star', $current_user->ID);
			
			add_post_meta($eventid, 'pec_user_rate_'.$rate.'star', $current_user->ID);
								
			return $this->getRating($eventid);
		}
		
	}
	
	function getEventShare($event) {
		$html = "";
		
		if($this->calendar_obj->article_share) {
			if(shortcode_exists('dpArticleShare')) {
				global $dpArticleShare;
				
				if($dpArticleShare['support_pro_event_calendar']) {
					$original_skin = $dpArticleShare['skin'];
					$dpArticleShare['skin'] = 'flat';

					require_once (dirname (__FILE__) . '/../../dpArticleShare/classes/base.class.php');
					$dpArticleShare_class = new DpArticleShare( false, '', $event->id );
					$html .= str_replace('dpas-icon dpas-icon-dpShareIcon-more dpas-fa"><i class="fa fa-plus"></i>', 'dpas-icon dpas-icon-dpShareIcon-more dpas-fa"><i class="fa fa-plus"></i><span>'.$dpArticleShare['i18n_share_on'].'</span>', $dpArticleShare_class->output(true));
					$dpArticleShare['skin'] = $original_skin;
				}
			}
		}
		
		return $html;
	}

	function getMap($map_id, $map, $location_id = "", $geocode = false) {
		global $dpProEventCalendar;

		if($map == "") {
			return '';
		}

		$title = "";
		$image = "";
		$address = "";
		$phone = "";
		$link = "";

		if(is_numeric($location_id)) {

			$title = get_the_title($location_id);
			$image = get_the_post_thumbnail($location_id, 'medium');
			$address = get_post_meta($location_id, 'pec_venue_address', true);
			$phone = get_post_meta($location_id, 'pec_venue_phone', true);
			$link = get_post_meta($location_id, 'pec_venue_link', true);
			$map_lnlat = get_post_meta($location_id, 'pec_venue_map_lnlat', true);
			
		}

		$html = '
			<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
			<div id="mapCanvas_'.$map_id.'" class="dp_pec_date_event_map_canvas" style="height: 350px;"></div>

			<script type="text/javascript">

				function initialize_'.$map_id.'() {
					var marker,
						map;
					var image = "'.addslashes($image).'";
					var title = "'.addslashes($title).'";
					var address = "'.addslashes($address).'";
					var phone = "'.addslashes($phone).'";
					var link = "'.addslashes($link).'";

					var infoBubble = new InfoBubble({
				        maxWidth: 290,
						maxHeight: 320,					
						shadowStyle: 0,
						padding: 0,
						backgroundColor: \'#fff\',
						borderRadius: 5,
						arrowSize: 20,
						borderWidth: 0,
						arrowPosition: 20,
						backgroundClassName: \'pec-infowindow\',
						arrowStyle: 2,
						hideCloseButton: true
				    });

					//var infowindow = new google.maps.InfoWindow();
					
					function getInfoWindowEvent(marker, content) {
						infowindow.close();
						infowindow.setContent(content);
						infowindow.open(map, marker);

					}


					var div_class = "dp_pec_map_infowindow";
					if(image == "") {
						div_class += " dp_pec_map_no_img";
					}
					

					var content = \'<div class="\'+div_class+\'">\'
							+image
							+\'<span class="dp_pec_map_title">\'+title+\'<\/span>\';

						if(address != "") {
							content += \'<span class="dp_pec_map_location"><i class="fa fa-map-marker"></i>\'+address+\'<\/span>\';
						}

						if(phone != "") {
							content += \'<span class="dp_pec_map_phone"><i class="fa fa-phone"></i>\'+phone+\'<\/span>\';
						}

						if(link != "") {
							content += \'<span class="dp_pec_map_link"><i class="fa fa-link"></i><a href="\'+link+\'" target="_blank" rel="nofollow">'.$this->translation['TXT_VISIT_WEBSITE'].'<\/a><\/span>\';
						}

					content +=\'<div class="dp_pec_clear"><\/div>\'
						+\'<\/div><\/div>\';

					infoBubble.setContent(content);
						';
				if($geocode || $map_lnlat == "") {
					$html .= '
						var latLng;
						geocoder = new google.maps.Geocoder();
				 		geocoder.geocode( { "address": "'.$map.'"}, function(results, status) {

						   	latLng = results[0].geometry.location;

						   	map = new google.maps.Map(document.getElementById("mapCanvas_'.$map_id.'"), {
								zoom: '.($dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom']).',
								center: latLng,
								disableDefaultUI: false,
								mapTypeId: google.maps.MapTypeId.ROADMAP
							});

							marker = new google.maps.Marker({
								position: latLng,
								map: map,
								icon: "'.$dpProEventCalendar['map_marker'].'"
							});
							

							infoBubble.open(map, marker);

					   });';
				} else {
					$html .= '
						var latLng = new google.maps.LatLng('.$map.');

						map = new google.maps.Map(document.getElementById("mapCanvas_'.$map_id.'"), {
							zoom: '.($dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom']).',
							center: latLng,
							disableDefaultUI: false,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});

						marker = new google.maps.Marker({
							position: latLng,
							map: map,
							icon: "'.$dpProEventCalendar['map_marker'].'"
						});

						infoBubble.open(map, marker);
						';
				}
				$html .= '
					/*google.maps.event.addListenerOnce(map, \'idle\', function() {
					    google.maps.event.trigger(map, \'resize\');
					});*/
				}
				
				if(document.readyState === "complete") {
					initialize_'.$map_id.'();
				} else {
					// Onload handler to fire off the app.
					jQuery(document).ready(function() {
						google.maps.event.addDomListener(window, "load", initialize_'.$map_id.');
					});
				}

			</script>';
		return $html;
	}
	
	function getEditRemoveButtons($event) {
		$edit_button = "";
		if($this->opts['allow_user_edit_remove'] == "") {
			$this->opts['allow_user_edit_remove'] = 1;
		}
		
		if(is_user_logged_in() && (($this->calendar_obj->allow_user_edit_event || $this->calendar_obj->allow_user_remove_event) && $this->opts['allow_user_edit_remove'])) {
			global $current_user;
			wp_get_current_user();
			
			$edit_button .= "<div class='dp_pec_edit_remove_wrap'>";

			if(($current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes') ) && ($this->calendar_obj->allow_user_edit_event && $this->opts['allow_user_edit_remove'])) {

				$edit_button .= '<a href="#" title="'.$this->translation['TXT_EDIT_EVENT'].'" data-event-id="'.$event->id.'" class="pec_edit_event"><i class="fa fa-pencil-square-o"></i></a>';		
			}
			
			if(($current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes' )) && ($this->calendar_obj->allow_user_remove_event && $this->opts['allow_user_edit_remove'])) {
				$edit_button .= '<a href="#" title="'.$this->translation['TXT_REMOVE_EVENT'].'" class="pec_remove_event"><i class="fa fa-trash"></i></a><div style="display:none;">
				<form enctype="multipart/form-data" method="post" class="add_new_event_form remove_event_form">
			
				<input type="hidden" value="'.$this->id_calendar.'" name="remove_event_calendar">
				<input type="hidden" value="'.$event->id.'" name="remove_event">
				<p>'.$this->translation['TXT_REMOVE_EVENT_CONFIRM'].'</p>
				<div class="dp_pec_clear"></div>
				<div class="pec-add-footer">
					<button class="dp_pec_remove_event">'.$this->translation['TXT_YES'].'</button>
					<button class="dp_pec_close pec_action_btn pec_action_btn_secondary">'.$this->translation['TXT_NO'].'</button>
					<div class="dp_pec_clear"></div>
				</div>
				</form>
				</div>';		
			}

			$edit_button .= "</div>";
		}
		
		return $edit_button;
	}
	
	function getAddForm($edit = false) {
		global $dpProEventCalendar, $dp_pec_payments, $wpdb;
		
		$html = '';
		
		$post_category_ids = array();
		$pec_weekly_every = 1;
		$pec_daily_every = 1;
		$pec_monthly_every = 1;
		$pec_monthly_day = '';
		$pec_monthly_position = '';
		$pec_daily_working_days = 0;
		$pec_weekly_day = array();

		if(is_numeric($edit)) {
			$id_calendar = get_post_meta($edit, 'pec_id_calendar', true);
			$id_calendar = explode(',', $id_calendar);
			$id_calendar = $id_calendar[0];
			
			$title = get_the_title($edit);
			$description = get_post_field('post_content', $edit);
			$post_category = get_the_terms($edit, 'pec_events_category');
			
			if(is_array($post_category)) {
				foreach($post_category as $category) {
					$post_category_ids[] = $category->term_id;
				}
			}
			$date = get_post_meta($edit, 'pec_date', true);
			$start_date = substr($date, 0, 11);
			$start_time_hh = substr($date, 11, 2);
			$start_time_mm = substr($date, 14, 2);
			$all_day = get_post_meta($edit, 'pec_all_day', true);
			$recurring_frecuency = get_post_meta($edit, 'pec_recurring_frecuency', true);
			$end_date = get_post_meta($edit, 'pec_end_date', true);
			$link = get_post_meta($edit, 'pec_link', true);
			$share = get_post_meta($edit, 'pec_share', true);
			$color = get_post_meta($edit, 'pec_color', true);
			$pec_daily_every = get_post_meta($edit, 'pec_daily_every', true);
			$pec_daily_working_days = get_post_meta($edit, 'pec_daily_working_days', true);
			$pec_weekly_every = get_post_meta($edit, 'pec_weekly_every', true);
			$pec_monthly_every = get_post_meta($edit, 'pec_monthly_every', true);
			$pec_monthly_day = get_post_meta($edit, 'pec_monthly_day', true);
			$pec_monthly_position = get_post_meta($edit, 'pec_monthly_position', true);
			$pec_weekly_day = get_post_meta($edit, 'pec_weekly_day', true);

			if(!is_array($pec_weekly_day)) {
				$pec_weekly_day = array();
			}

			$map = "";
			$end_time_hh = get_post_meta($edit, 'pec_end_time_hh', true);
			$end_time_mm = get_post_meta($edit, 'pec_end_time_mm', true);
			$hide_time = get_post_meta($edit, 'pec_hide_time', true);
			$location = get_post_meta($edit, 'pec_location', true);
			if(is_numeric($location)) {
				$location_name = get_the_title($location);
				$address = get_post_meta($location, 'pec_venue_address', true);
				$map = get_post_meta($location, 'pec_venue_map', true);
				$map_lnlat = get_post_meta($location, 'pec_venue_map_lnlat', true);
			}

			$phone = get_post_meta($edit, 'pec_phone', true);
			$booking_enable = get_post_meta($edit, 'pec_enable_booking', true);
			$limit = get_post_meta($edit, 'pec_booking_limit', true);
			$block_hours = get_post_meta($edit, 'pec_booking_block_hours', true);
			$price = get_post_meta($edit, 'pec_booking_price', true);
		}
		
		$html .= '
			<form enctype="multipart/form-data" method="post" class="add_new_event_form edit_event_form edit_event_form_calendar_'.$id_calendar.'">
			';
		if(is_numeric($id_calendar)) {
			$html .= '
			<div class="pec_modal_wrap_content">
			<input type="hidden" value="'.$id_calendar.'" name="edit_calendar" />
			<input type="hidden" value="'.$edit.'" name="edit_event" />
			<input type="hidden" value="'.$this->nonce.'_event_description_'.$edit.'" name="editor_id" id="pec_edit_form_editor_id" />';
		}
		$html .= '
			<input type="text" class="dp_pec_new_event_text dp_pec_form_title pec_required" value="'.$title.'" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" name="title" />
			';
			if($this->calendar_obj->form_show_description) {
				
				if($this->calendar_obj->form_text_editor) {
					
					// Turn on the output buffer
					ob_start();
					
					// Echo the editor to the buffer
					wp_editor($description, $this->nonce.'_event_description_'.(is_numeric($edit) ? $edit : ''), array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'tinymce' => true, 'textarea_rows' => 5, 'teeny' => true));
					
					// Store the contents of the buffer in a variable
					$editor_contents = ob_get_contents();
					
					$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
					
				} else {
					$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" class="dp_pec_new_event_text" id="" name="description" cols="50" rows="5">'.$description.'</textarea>';	
				}
				
			}
			$html .= '
			<div class="dp_pec_row">
				<div class="dp_pec_col6">
					';
					if($this->calendar_obj->form_show_category) {
						$cat_arr = array();
						if(!empty($this->category)) {
							$cat_arr = explode(",", $this->category);
						}

						$cat_args = array(
								'taxonomy' => 'pec_events_category',
								'hide_empty' => 0
							);
						if($this->calendar_obj->category_filter_include != "") {
							$cat_args['include'] = $this->calendar_obj->category_filter_include;
						}
						$categories = get_categories($cat_args); 
						if(count($categories) > 0) {
							$html .= '
							<div class="dp_pec_row">
								<div class="dp_pec_col12">
									<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
									';
									foreach ($categories as $category) {
										if(!empty($cat_arr) && !in_array($category->term_id, $cat_arr)) {
											continue;
										}
										$html .= '<div class="pec_checkbox_list">';
										$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" '.(in_array($category->term_id, $post_category_ids) ? 'checked="checked"' : '').' />';
										$html .= $category->cat_name;
										$html .= '</div>';
									  }
									$html .= '	
									<div class="dp_pec_clear"></div>							
								</div>
							</div>
							';
						}
					}
					$html .= '
					<div class="dp_pec_row">
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
							<div class="dp_pec_clear"></div>
							<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_date_input_modal" value="'.$start_date.'" />
						</div>
						
						<div class="dp_pec_col6 dp_pec_end_date_form">
						';
						if($this->calendar_obj->form_show_end_date) {
						$html .= '
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
							<div class="dp_pec_clear"></div>
							<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_end_date_input_modal" value="'.$end_date.'" />
							<button type="button" class="dp_pec_clear_end_date">
								<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
							</button>';
						}
						$html .= '
						</div>
						<div class="dp_pec_clear"></div>
					</div>';
					if($this->calendar_obj->form_show_extra_dates) {
						$html .= '<div class="dp_pec_row">';
						$html .= '<div class="dp_pec_col12">';
						$html .= '<input type="text" value="" placeholder="'.$this->translation['TXT_EXTRA_DATES'].'" id="" class="dp_pec_extra_dates" readonly="readonly" style="max-width: 300px;" name="extra_dates" />';
						$html .= '</div>';
						$html .= '<div class="dp_pec_clear"></div>
						</div>';
					}

					$html .= '<div class="dp_pec_row">';
					if($this->calendar_obj->form_show_start_time) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
							<div class="dp_pec_clear"></div>
							<select autocomplete="off" class="dp_pec_new_event_time" name="time_hours" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">';
								for($i = 0; $i <= 23; $i++) {
									$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
									if(dpProEventCalendar_is_ampm()) {
										$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
									}
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($start_time_hh == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.$hour.'</option>';
								}
							$html .= '
							</select>
							<select autocomplete="off" class="dp_pec_new_event_time" name="time_minutes" id="pec_time_minutes" style="width:50px;">';
								for($i = 0; $i <= 59; $i += 5) {
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($start_time_mm == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
								}
							$html .= '
							</select>
						</div>';
					}
					if($this->calendar_obj->form_show_end_time) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
							<div class="dp_pec_clear"></div>
							<select autocomplete="off" class="dp_pec_new_event_time" name="end_time_hh" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">
								<option value="">--</option>';
								for($i = 0; $i <= 23; $i++) {
									$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
									if(dpProEventCalendar_is_ampm()) {
										$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
									}
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($end_time_hh == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.$hour.'</option>';
								}
							$html .= '
							</select>
							<select autocomplete="off" class="dp_pec_new_event_time" name="end_time_mm" id="" style="width:50px;">
								<option value="">--</option>';
								for($i = 0; $i <= 59; $i += 5) {
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($end_time_mm == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
								}
							$html .= '
							</select>
						</div>';
					}
					$html .= '
						<div class="dp_pec_clear"></div>
					</div>';
					
					$html .= '<div class="dp_pec_row">';
					if($this->calendar_obj->form_show_hide_time) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
							<select autocomplete="off" name="hide_time">
								<option value="0" '.($hide_time == 0 ? 'selected="selected"' : '').'>'.$this->translation['TXT_NO'].'</option>
								<option value="1" '.($hide_time == 1 ? 'selected="selected"' : '').'>'.$this->translation['TXT_YES'].'</option>
							</select>
						';
							
							if($this->calendar_obj->form_show_all_day) {
								
								$html .= '
								<input type="checkbox" class="checkbox" name="all_day" id="" value="1" '.($all_day ? 'checked="checked"' : '').' />
								<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
									
							}
							
						$html .= '
						</div>';
					} elseif($this->calendar_obj->form_show_all_day) {
						
						$html .= '
						<div class="dp_pec_col6">
							<input type="checkbox" class="checkbox" name="all_day" id="" value="1" '.($all_day ? 'checked="checked"' : '').' />
							<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
						</div>';	

					}
					
					if($this->calendar_obj->form_show_frequency) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
							<select autocomplete="off" name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
								<option value="0">'.$this->translation['TXT_NONE'].'</option>
								<option value="1" '.($recurring_frecuency == 1 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_DAILY'].'</option>
								<option value="2" '.($recurring_frecuency == 2 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
								<option value="3" '.($recurring_frecuency == 3 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
								<option value="4" '.($recurring_frecuency == 4 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_YEARLY'].'</option>
							</select>
						';
								
							$html .= '
							<div class="pec_daily_frequency" '.($recurring_frecuency != 1 ? 'style="display:none;"' : '').'>
								<div id="pec_daily_every_div">' . $this->translation['TXT_EVERY'] . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="'.$pec_weekly_every.'" /> '.$this->translation['TXT_DAYS'] . ' </div>
								<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" '.($pec_daily_working_days ? 'checked="checked"' : '').' />'. $this->translation['TXT_ALL_WORKING_DAYS'] . '</div>
							</div>';
							
							$html .= '
							<div class="pec_weekly_frequency" '.($recurring_frecuency != 2 ? 'style="display:none;"' : '').'>
								
								<div class="dp_pec_clear"></div>
								
								'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="'.$pec_weekly_every.'" /> '. $this->translation['TXT_WEEKS_ON'].'
								
								<div class="dp_pec_clear"></div>
								
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="1" '.(in_array(1, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_MON'] . '
								</div>
								<div class="pec_checkbox_list">	
									<input type="checkbox" class="checkbox" value="2" '.(in_array(2, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_TUE'] . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="3" '.(in_array(3, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_WED'] . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="4" '.(in_array(4, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_THU'] . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="5" '.(in_array(5, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_FRI'] . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="6" '.(in_array(6, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SAT'] . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="7" '.(in_array(7, $pec_weekly_day) ? 'checked="checked"' : '').' name="pec_weekly_day[]" /> &nbsp; '. $this->translation['TXT_SUN'] . '
								</div>
								
							</div>';
							
							$html .= '
							<div class="pec_monthly_frequency" '.($recurring_frecuency != 3 ? 'style="display:none;"' : '').'>
								
								<div class="dp_pec_clear"></div>
								
								'. $this->translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="'.$pec_monthly_every.'" /> ' . $this->translation['TXT_MONTHS_ON'] . '
								
								<div class="dp_pec_clear"></div>
								
								<select autocomplete="off" name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
									<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
									<option value="first" '.($pec_monthly_position == 'first' ? 'selected="selected"' : '').'> ' . $this->translation['TXT_FIRST'] . '</option>
									<option value="second" '.($pec_monthly_position == 'second' ? 'selected="selected"' : '').'> ' . $this->translation['TXT_SECOND'] . '</option>
									<option value="third" '.($pec_monthly_position == 'third' ? 'selected="selected"' : '').'> ' . $this->translation['TXT_THIRD'] . '</option>
									<option value="fourth" '.($pec_monthly_position == 'fourth' ? 'selected="selected"' : '').'> ' . $this->translation['TXT_FOURTH'] . '</option>
									<option value="last" '.($pec_monthly_position == 'last' ? 'selected="selected"' : '').'> ' . $this->translation['TXT_LAST'] . '</option>
								</select>
								
								<select autocomplete="off" name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
								<option value=""> ' . $this->translation['TXT_RECURRING_OPTION'] . '</option>
									<option value="monday" '.($pec_monthly_day == 'monday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_MONDAY'] . '</option>
									<option value="tuesday" '.($pec_monthly_day == 'tuesday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_TUESDAY'] . '</option>
									<option value="wednesday" '.($pec_monthly_day == 'wednesday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_WEDNESDAY'] . '</option>
									<option value="thursday" '.($pec_monthly_day == 'thursday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_THURSDAY'] . '</option>
									<option value="friday" '.($pec_monthly_day == 'friday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_FRIDAY'] . '</option>
									<option value="saturday" '.($pec_monthly_day == 'saturday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_SATURDAY'] . '</option>
									<option value="sunday" '.($pec_monthly_day == 'sunday' ? 'selected="selected"' : '').'> ' . $this->translation['DAY_SUNDAY'] . '</option>
								</select>
							</div>
						</div>';
					}
					
					if($this->calendar_obj->form_show_booking_enable ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="checkbox" '.($booking_enable ? 'checked="checked"' : '').' class="checkbox" name="booking_enable" id="" value="1" />
							<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_ALLOW_BOOKINGS'].'</span>
						</div>';
					}
					
					$html .= '
					</div>';

					$html .= '
					<div class="dp_pec_row">';
					if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="number" min="0" value="'.$price.'" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.$this->translation['TXT_PRICE'].'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
						</div>';
					}
					
					if($this->calendar_obj->form_show_booking_block_hours ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="number" min="0" value="'.$block_hours.'" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_BLOCK_HOURS'].'" id="" name="block_hours" />
						</div>';
					}

					if($this->calendar_obj->form_show_booking_limit ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="number" min="0" value="'.$limit.'" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.$this->translation['TXT_BOOKING_LIMIT'].'" id="" name="limit" />
						</div>';
					}

					$html .= '
						<div class="dp_pec_clear"></div>
					</div>
					';

					if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "" ) {
						$html .= '
						<div class="dp_pec_row">';
						
						$html .= '
							<div class="dp_pec_col12">
								<div id="pec_new_event_captcha"></div>
							</div>';
						

						$html .= '
							<div class="dp_pec_clear"></div>
						</div>
						';
					}

					$html .= '
					<div class="dp_pec_clear"></div>
				</div>
				<div class="dp_pec_col6">
					';
					if($this->calendar_obj->form_show_image || false) {
						$rand_image = rand();
						$html .= '
						<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
						<div class="dp_pec_add_image_wrap">
							<label for="event_image_'.$this->nonce.'_'.$rand_image.(is_numeric($edit) ? '_pecremoveedit' : '').'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
						</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.(is_numeric($edit) ? '_pecremoveedit' : '').'" class="event_image" style="visibility:hidden; position: absolute;" />							
						';
					}
					if($this->calendar_obj->form_show_color) {
						$html .= '
						<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_COLOR'].'</span>
						<select autocomplete="off" name="color" class="pec_color_form">
							<option value="">'.$this->translation['TXT_NONE'].'</option>
							 ';
							 
							$counter = 0;
							$querystr = "
							SELECT *
							FROM ". $this->table_special_dates ." 
							ORDER BY title ASC
							";
							$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
							foreach($sp_dates_obj as $sp_dates) {
							
							$html .= '<option value="'.$sp_dates->id.'" '.($color == $sp_dates->id ? 'selected="selected"' : '').'>'.$sp_dates->title.'</option>';
							
							}

						$html .= ' 
						</select>
						<div class="dp_pec_clear"></div>';
					}

					if($this->calendar_obj->form_show_timezone) {
						$html .= '
						<span class="dp_pec_form_desc">'.$this->translation['TXT_SELECT_TIMEZONE'].'</span>
						<select autocomplete="off" name="timezone" class="pec_timezone_form">';
						$current_offset = get_option('gmt_offset');
						$tzstring = get_option('timezone_string');
						if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
							$check_zone_info = false;
							if ( 0 == $current_offset )
								$tzstring = 'UTC+0';
							elseif ($current_offset < 0)
								$tzstring = 'UTC' . $current_offset;
							else
								$tzstring = 'UTC+' . $current_offset;
						}

						$pec_timezone = $tzstring;

		                $html .= wp_timezone_choice($pec_timezone); 
						$html .= ' 
						</select>
						<div class="dp_pec_clear"></div>';
					}

					if($this->calendar_obj->form_show_location) {
						$html .= '
						<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_LOCATION'].'</span>
						<select autocomplete="off" name="location" class="pec_location_form">
							<option value="">'.$this->translation['TXT_NONE'].'</option>
							 ';

							$args = array(
							'posts_per_page'   => -1,
							'post_type'        => 'pec-venues',
							'post_status'      => 'publish',
							'order'			   => 'ASC', 
							'orderby' 		   => 'title' );

						if($this->calendar_obj->venue_filter_include != "") {
							$args['include'] = $this->calendar_obj->venue_filter_include;
						}

							$venues_list = get_posts($args);
							foreach($venues_list as $venue) {

								$html .= '<option value="'.$venue->ID.'" '.($location == $venue->ID ? 'selected="selected"' : '').'>'.$venue->post_title.'</option>';
							
							}

							if($this->calendar_obj->form_show_location_options) {
								$html .= '<option value="-1">'.$this->translation['TXT_OTHER'].'</option>';
							}

						$html .= ' </select>
						<div class="dp_pec_clear"></div>';

						if($this->calendar_obj->form_show_location_options) {
							$html .= '<div class="pec_location_options" style="display:none;">
								<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION_NAME'].'" id="" name="location_name" />';

								$html .= '<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_ADDRESS'].'" id="" name="location_address" />';

								/*$html .= '
								<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
								<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
								<div class="map_lnlat_wrap" style="display:none;">
									<span class="dp_pec_form_desc">'.$this->translation['TXT_DRAG_MARKER'].'</span>
									<div id="pec_mapCanvas" style="height: 400px;"></div>
								</div>*/
							$html .= '</div>';
						}

						/*$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$location.'" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';*/
					}

					if($this->calendar_obj->form_show_link) {
						$html .= '<input type="url" class="dp_pec_new_event_text" value="'.$link.'" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
					}

					if($this->calendar_obj->form_show_phone) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$phone.'" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
					}

					$cal_form_custom_fields = $this->calendar_obj->form_custom_fields;
					$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

					if(is_array($dpProEventCalendar['custom_fields_counter'])) {
						$counter = 0;
						
						foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
							if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
								$counter++;
								continue;
							}

							if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {

								$html .= '
								<div class="dp_pec_wrap_checkbox dp_pec_wrap_checkbox_pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'">
								<input type="checkbox" class="checkbox '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="1" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" '.(is_numeric($edit) && get_post_meta($edit, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true) ? 'checked="checked"' : '').' name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
								</div>';
								
							} else {
								
								$html .= '
								<input type="text" class="dp_pec_new_event_text '.(!$dpProEventCalendar['custom_fields']['optional'][$counter] ? 'pec_required' : '').'" value="'.(is_numeric($edit) ? get_post_meta($edit, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true) : ''). '" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
							}
							
							$counter++;		
						}
					}
						$html .= '
				</div>
				
				<div class="dp_pec_clear"></div>
				
			</div>
			';
			if(is_numeric($id_calendar)) {
				$html .= '
				</div>';
			}
			$html .= '
			<div class="dp_pec_clear"></div>
			<div class="pec-add-footer">
				<button class="dp_pec_submit_event" data-lang-sending="'.$this->translation['TXT_SENDING'].'">'.$this->translation['TXT_SEND'].'</button>
				<div class="dp_pec_clear"></div>
			</div>
			</form>';
		return $html;	
	}
	
	function userHasBooking($date = '', $event_id) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		if(!is_user_logged_in()) {
			return false;	
		}
		
		$id_list = $event_id;
        if(function_exists('icl_object_id')) {
            global $sitepress;

            if(is_object($sitepress) ) {
	            $id_list_arr = array();
				$trid = $sitepress->get_element_trid($event_id, 'post_pec-events');
				$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

				foreach($translation as $key) {
					$id_list_arr[] = $key->element_id;
				}

				if(!empty($id_list_arr)) {
					$id_list = implode(",", $id_list_arr);
				}
			}
		}

		$querystr = "
            SELECT count(*) as counter
            FROM ".$this->table_booking."
			WHERE id_event = (".$id_list.") 
			AND id_user = ".$current_user->ID." 
			";
		if($date != "") {
				$querystr .= "
			AND event_date = '".$date."' ";
		}

		$querystr .= "
			AND status <> 'pending' 
			AND status <> 'canceled_by_user' 
			AND status <> 'canceled'
            ";
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return $bookings_obj[0]->counter;
	}
	
	function getEventBookings($counter = false, $date = "", $event_id) {
		global $wpdb, $dpProEventCalendar, $table_prefix;
		
		$id_list = $event_id;
        if(function_exists('icl_object_id')) {
            global $sitepress;

            if(is_object($sitepress) ) {
	            $id_list_arr = array();

				$trid = $sitepress->get_element_trid($event_id, 'post_pec-events');
				$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

				foreach($translation as $key) {
					$id_list_arr[] = $key->element_id;
				}

				if(!empty($id_list_arr)) {
					$id_list = implode(",", $id_list_arr);
				}
			}
		}

		$pec_booking_continuous = get_post_meta($event_id, 'pec_booking_continuous', true);

		if($counter) {
			$querystr = "
            SELECT quantity";
		} else {
		$querystr = "
            SELECT *";
		}
		$querystr .= "
            FROM ".$this->table_booking."
			WHERE id_event IN(".$id_list.") AND status <> 'pending' AND status <> 'canceled_by_user' AND status <> 'canceled'
            ";
		
		if(!empty($date) && !$pec_booking_continuous) {
			$querystr .= "AND event_date = '".$date."'";	
		}
		
		if($counter) {
			$bookings_obj = $wpdb->get_results($querystr, OBJECT);
			$counter = 0;
			foreach($bookings_obj as $booking) {
				$counter += $booking->quantity;
			}
			return $counter;
		} else {
			$bookings_obj = $wpdb->get_results($querystr, OBJECT);
			
			return $bookings_obj;
		}
	}
	
}
?>