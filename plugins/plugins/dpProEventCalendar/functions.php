<?php
function dpProEventCalendar_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;

	$path = 'admin.php';

	if ( $query = build_query( $query ) )
		$path .= '?' . $query;

	$url = admin_url( $path );

	return esc_url_raw( $url );
}

function dpProEventCalendar_plugin_url( $path = '', $protocol = '' ) {
	global $wp_version;
	$return_url = "";
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		$return_url = plugins_url( $path );
		if($protocol != "") {
			$return_url = str_replace(array('http://', 'https://'), $protocol.'://', $return_url);
		}
		return $return_url;
	}

	$return_url = plugins_url( $path, __FILE__ );
	if($protocol != "") {
		$return_url = str_replace(array('http://', 'https://'), $protocol.'://', $return_url);
	}
	
	return $return_url;
}

function dpProEventCalendar_parse_date( $date ) {
	
	$date = substr($date,0,10);
	if($date == "0000-00-00" || $date == "")
		return '';
	$date_arr = explode("-", $date);
	$date = $date_arr[1]."/".$date_arr[2]."/".$date_arr[0];
	
	return $date ;
}

function dpProEventCalendar_parse_date_widget( $date, $date_format ) {
	if($date == "0000-00-00" || $date == "")
		return '';
		
	$date_arr = explode("-", substr($date, 0, 10));
	$time_arr = explode(":", substr($date, 11, 5));
	
	switch($date_format) {
		case 0: 
			$date = $date_arr[1]."/".$date_arr[2]."/".$date_arr[0]." ".$time_arr[0].":".$time_arr[1];
			break;
		case 1: 
			$date = $date_arr[2]."/".$date_arr[1]."/".$date_arr[0]." ".$time_arr[0].":".$time_arr[1];
			break;
		case 2: 
			$date = $date_arr[1]."/".$date_arr[2]."/".$date_arr[0];
			break;
		case 3: 
			$date = $date_arr[2]."/".$date_arr[1]."/".$date_arr[0];
			break;
		case 4: 
			$date = substr(dpProEventCalendar_translate_month($date_arr[1]), 0, 3)." ".$date_arr[2].", ".$date_arr[0];
			break;
		case 5: 
			$date = substr(dpProEventCalendar_translate_month($date_arr[1]), 0, 3)." ".$date_arr[2];
			break;
		default: 
			$date = $date_arr[1]."/".$date_arr[2]."/".$date_arr[0]." ".$time_arr[0].":".$time_arr[1];
			break;	
	}
	
	return $date ;
}

function dpProEventCalendar_translate_month($month) {
	global $dpProEventCalendar;
	
	switch($month) {
		case "01":
			$month_name = $dpProEventCalendar['lang_january'];
			break;
		case "02":
			$month_name = $dpProEventCalendar['lang_february'];
			break;
		case "03":
			$month_name = $dpProEventCalendar['lang_march'];
			break;
		case "04":
			$month_name = $dpProEventCalendar['lang_april'];
			break;
		case "05":
			$month_name = $dpProEventCalendar['lang_may'];
			break;
		case "06":
			$month_name = $dpProEventCalendar['lang_june'];
			break;
		case "07":
			$month_name = $dpProEventCalendar['lang_july'];
			break;
		case "08":
			$month_name = $dpProEventCalendar['lang_august'];
			break;
		case "09":
			$month_name = $dpProEventCalendar['lang_september'];
			break;
		case "10":
			$month_name = $dpProEventCalendar['lang_october'];
			break;
		case "11":
			$month_name = $dpProEventCalendar['lang_november'];
			break;
		case "12":
			$month_name = $dpProEventCalendar['lang_december'];
			break;
		default:
			$month_name = $dpProEventCalendar['lang_january'];
			break;
	}
	
	return $month_name;
}

function dpProEventCalendar_reslash_multi(&$val,$key) 
{
   if (is_array($val)) array_walk($val,'dpProEventCalendar_reslash_multi');
   else {
      $val = dpProEventCalendar_reslash($val);
   }
}


function dpProEventCalendar_reslash($string)
{
   if (!get_magic_quotes_gpc())$string = addslashes($string);
   return $string;
}

function dpProEventCalendar_CutString ($texto, $longitud = 180) { 
	$str_len = function_exists('mb_strlen') ? mb_strlen($texto) : strlen($texto);
	if($str_len > $longitud) { 
		$strpos = function_exists('mb_strpos') ? mb_strpos($texto, ' ', $longitud) : strpos($texto, ' ', $longitud);
		$pos_espacios = $strpos - 1; 
		if($pos_espacios > 0) { 
			$substr1 = function_exists('mb_substr') ? mb_substr($texto, 0, ($pos_espacios + 1)) : substr($texto, 0, ($pos_espacios + 1));
			$caracteres = count_chars($substr1, 1); 
			if ($caracteres[ord('<')] > $caracteres[ord('>')]) { 
				$strpos2 = function_exists('mb_strpos') ? mb_strpos($texto, ">", $pos_espacios) : strpos($texto, ">", $pos_espacios);
				$pos_espacios = $strpos2 - 1; 
			} 
			$substr2 = function_exists('mb_substr') ? mb_substr($texto, 0, ($pos_espacios + 1)) : substr($texto, 0, ($pos_espacios + 1));
			$texto = $substr2.'...'; 
		} 
		if(preg_match_all("|(<([\w]+)[^>]*>)|", $texto, $buffer)) { 
			if(!empty($buffer[1])) { 
				preg_match_all("|</([a-zA-Z]+)>|", $texto, $buffer2); 
				if(count($buffer[2]) != count($buffer2[1])) { 
					$cierrotags = array_diff($buffer[2], $buffer2[1]); 
					$cierrotags = array_reverse($cierrotags); 
					foreach($cierrotags as $tag) { 
							$texto .= '</'.$tag.'>'; 
					} 
				} 
			} 
		} 
 
	} 
	return $texto; 
}

add_action( 'wp_ajax_nopriv_getDate', 'dpProEventCalendar_getDate' );
add_action( 'wp_ajax_getDate', 'dpProEventCalendar_getDate' );
 
function dpProEventCalendar_getDate() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!is_numeric($_POST['date'])) { die(); }
	
	$timestamp = $_POST['date'];
	$calendar = $_POST['calendar'];
	$category = $_POST['category'];
	
	$location = (isset($_POST['location']) ? $_POST['location'] : "");
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$type = $_POST['type'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];
	$is_admin = $_POST['is_admin'];
	if ($is_admin && strtolower($is_admin) !== "false") {
      $is_admin = true;
   } else {
      $is_admin = false;
   }
   //die(__FILE__.'/../classes/base.class.php');
	require_once(dirname(__FILE__).'/classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( $is_admin, $calendar, $timestamp, null, '', $category, $event_id, $author, '', '', '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ));
	
	die($dpProEventCalendar->monthlyCalendarLayout(($type == 'compact' ? true : false)));
}

add_action( 'wp_ajax_nopriv_getDaily', 'dpProEventCalendar_getDaily' );
add_action( 'wp_ajax_getDaily', 'dpProEventCalendar_getDaily' );
 
function dpProEventCalendar_getDaily() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!is_numeric($_POST['date'])) { die(); }
	
	$timestamp = $_POST['date'];
	$currDate = date("Y-m-d", $timestamp);
	
	$calendar = $_POST['calendar'];
	$is_admin = $_POST['is_admin'];
	$category = $_POST['category'];
	$columns = $_POST['columns'];
	$location = $_POST['location'];
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];

	if ($is_admin && strtolower($is_admin) !== "false") {
      $is_admin = true;
   } else {
      $is_admin = false;
   }
   
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( $is_admin, $calendar, $timestamp, null, '', $category, $event_id, $author, '', $columns, '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ));
	
	echo "<!--".dpProEventCalendar_date_i18n(get_option('date_format'), $timestamp).">!]-->";
	
	die($dpProEventCalendar->dailyCalendarLayout());
}

add_action( 'wp_ajax_nopriv_getWeekly', 'dpProEventCalendar_getWeekly' );
add_action( 'wp_ajax_getWeekly', 'dpProEventCalendar_getWeekly' );
 
function dpProEventCalendar_getWeekly() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!is_numeric($_POST['date'])) { die(); }
	
	$timestamp = $_POST['date'];
	$currDate = date("Y-m-d", $timestamp);
	
	$calendar = $_POST['calendar'];
	$is_admin = $_POST['is_admin'];
	$category = $_POST['category'];
	$location = $_POST['location'];
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];

	if ($is_admin && strtolower($is_admin) !== "false") {
      $is_admin = true;
   } else {
      $is_admin = false;
   }
   
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( $is_admin, $calendar, $timestamp, null, '', $category, $event_id, $author, '', '', '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ) );
	
	if($dpProEventCalendar->calendar_obj->first_day == 1) {
		$weekly_first_date = strtotime('last monday', ($timestamp + (24* 60 * 60)));
		$weekly_last_date = strtotime('next sunday', ($timestamp - (24* 60 * 60)));
	} else {
		$weekly_first_date = strtotime('last sunday', ($timestamp + (24* 60 * 60)));
		$weekly_last_date = strtotime('next saturday', ($timestamp - (24* 60 * 60)));
	}

	$weekly_txt = dpProEventCalendar_date_i18n('d F', $weekly_first_date).' - '.dpProEventCalendar_date_i18n('d F, Y', $weekly_last_date);
	
	if(date('m', $weekly_first_date) == dpProEventCalendar_date_i18n('m', $weekly_last_date)) {
	
		$weekly_txt = dpProEventCalendar_date_i18n('d', $weekly_first_date) . ' - ' . dpProEventCalendar_date_i18n('d F, Y', $weekly_last_date);
		
	}
	
	if(date('Y', $weekly_first_date) != date('Y', $weekly_last_date)) {
			
		$weekly_txt = dpProEventCalendar_date_i18n(get_option('date_format'), $weekly_first_date).' - '.dpProEventCalendar_date_i18n(get_option('date_format'), $weekly_last_date);
		
	}

	echo "<!--".$weekly_txt.">!]-->";
	
	die($dpProEventCalendar->weeklyCalendarLayout());
}

add_action( 'wp_ajax_nopriv_getEvents', 'dpProEventCalendar_getEvents' );
add_action( 'wp_ajax_getEvents', 'dpProEventCalendar_getEvents' );
 
function dpProEventCalendar_getEvents() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //   die ( 'Busted!');
		
	if(!isset($_POST['date'])) { die(); }
	
	$date = $_POST['date'];
	$calendar = $_POST['calendar'];
	$category = $_POST['category'];
	$location = $_POST['location'];
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$type = $_POST['type'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];

	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar, null, null, '', $category, $event_id, $author, '', '', '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ) );
	$dpProEventCalendar_class->switchCalendarTo($type, 5, 0, $category, $author, $event_id, $location);
	
	die($dpProEventCalendar_class->eventsListLayout( $date ));
}

add_action( 'wp_ajax_nopriv_getEvent', 'dpProEventCalendar_getEvent' );
add_action( 'wp_ajax_getEvent', 'dpProEventCalendar_getEvent' );
 
function dpProEventCalendar_getEvent() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //   die ( 'Busted!');
		
	if(!isset($_POST['event'])) { die(); }
	
	$event = $_POST['event'];
	$calendar = $_POST['calendar'];
	$date = $_POST['date'];
	
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( false, $calendar );
	
	$start_date = $dpProEventCalendar->parseMysqlDate($date);
			
	$start_date_year = date('Y', strtotime($date));
	$start_date_formatted = str_replace(array(','), '', $start_date);
	$start_date_formatted = trim(str_replace($start_date_year, '', $start_date_formatted), ',./|-');

	echo '
		<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap">
			<span class="fa fa-calendar-o"></span>
			<div class="dp_pec_date_block">'.$start_date_formatted.'<span>'.$start_date_year.'</span></div>
			<a href="#" class="dp_pec_date_event_back pec_action_btn dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.$dpProEventCalendar->translation['TXT_BACK'].'</span></a>
		</div>
		<div class="dp_pec_clear"></div>';
		
	$result = $dpProEventCalendar->getEventData($event);
	
	echo $dpProEventCalendar->singleEventLayout($result, false, $date, true, '', true);
	
	die();
}

add_action( 'wp_ajax_nopriv_getEventsMonth', 'dpProEventCalendar_getEventsMonth' );
add_action( 'wp_ajax_getEventsMonth', 'dpProEventCalendar_getEventsMonth' );
 
function dpProEventCalendar_getEventsMonth() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //   die ( 'Busted!');
		
	if(!isset($_POST['month'])) { die(); }
	
	$month = $_POST['month'];
	$year = $_POST['year'];
	$calendar = $_POST['calendar'];
	$category = $_POST['category'];
	$location = $_POST['location'];
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];

	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( false, $calendar, null, null, '', $category, $event_id, $author, '', '', '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ) );
	
	$next_month_days = cal_days_in_month(CAL_GREGORIAN, str_pad(($month), 2, "0", STR_PAD_LEFT), $year);
	$month_number = str_pad($month, 2, "0", STR_PAD_LEFT);
	
	$start = $year."-".$month_number."-01 00:00:00";

	if($dpProEventCalendar->calendar_obj->hide_old_dates && date("Y-m") == $year."-".$month_number) {
		$start = date("Y-m-d H:i:s");
	}
	
	echo $dpProEventCalendar->upcomingCalendarLayout( false, 20, '', $start, $year."-".$month_number."-".$next_month_days." 23:59:59", true );
	die();
}

add_action( 'wp_ajax_nopriv_getEventsMonthList', 'dpProEventCalendar_getEventsMonthList' );
add_action( 'wp_ajax_getEventsMonthList', 'dpProEventCalendar_getEventsMonthList' );
 
function dpProEventCalendar_getEventsMonthList() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //   die ( 'Busted!');
		
	if(!is_numeric($_POST['month'])) { die(); }
	
	$month = $_POST['month'];
	$year = $_POST['year'];
	$calendar = $_POST['calendar'];
	$category = $_POST['category'];
	$limit = $_POST['limit'];
	$widget = $_POST['widget'];
	$location = $_POST['location'];
	$event_id = $_POST['event_id'];
	$author = $_POST['author'];
	$columns = $_POST['columns'];
	$include_all_events = $_POST['include_all_events'];
	$hide_old_dates = $_POST['hide_old_dates'];
	
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( false, $calendar, null, null, '', $category, $event_id, $author, '', $columns, '', '', 0, array('location' => $location, 'include_all_events' => $include_all_events, 'hide_old_dates' => $hide_old_dates ) );
	
	$next_month_days = cal_days_in_month(CAL_GREGORIAN, str_pad(($month), 2, "0", STR_PAD_LEFT), $year);
	$month_number = str_pad($month, 2, "0", STR_PAD_LEFT);
	$this_month_day = "01";
	
	if($dpProEventCalendar->calendar_obj->hide_old_dates && ($month <= date('m') || $year < date('Y'))) {
		if($month == date('m') && $year == date('Y')) {
			$this_month_day = str_pad($dpProEventCalendar->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		} elseif($year <= date('Y')) {
			$this_month_day = $next_month_days;
			$next_month_days = "01";
		}
	}

	$limit_events = 40;
	if($widget && is_numeric($limit) && $limit > 0) {
		$limit_events = $limit;
	}

	echo $dpProEventCalendar->eventsMonthList( $year."-".$month_number."-".$this_month_day." 00:00:00", $year."-".$month_number."-".$next_month_days." 23:59:59", $limit_events );
	
	die();
}

add_action( 'wp_ajax_nopriv_submitEvent', 'dpProEventCalendar_submitEvent' );
add_action( 'wp_ajax_submitEvent', 'dpProEventCalendar_submitEvent' );
 
function dpProEventCalendar_submitEvent() {
	header("HTTP/1.1 200 OK");
	global $current_user, $dpProEventCalendar_cache, $dpProEventCalendar;

    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //   die ( 'Error!');
		
	if(!is_numeric($_POST['calendar']) && !is_numeric($_POST['edit_calendar'])) { die(); }

	wp_get_current_user();
	
	$calendar = $_POST['calendar'];
	if(!is_numeric($calendar)) {
		$calendar = $_POST['edit_calendar'];

		if(!is_numeric($_POST['edit_event'])) {
			die();
		}
		
		$calendar_arr = get_post_meta($_POST['edit_event'], 'pec_id_calendar', true);

		$calendar_arr = explode(',', $calendar_arr);

		if(!in_array($calendar, $calendar_arr)) {
			die();
		}
	}

	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar );
	$dpProEventCalendar_class->getCalendarData();
	
	if(!is_user_logged_in() && !$dpProEventCalendar_class->calendar_obj->assign_events_admin) { die(); }
	
	// ReCaptcha Validation
	if($dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
		//set POST variables
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$fields = array(
								'secret' => $dpProEventCalendar['recaptcha_secret_key'],
								'response' => $_POST['grecaptcharesponse'],
								'remoteip' => $_SERVER['REMOTE_ADDR']
						);
		
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		
		//execute post
		$result = curl_exec($ch);
		
		$result = json_decode($result, true);
		
		//close connection
		curl_close($ch);
		
		if($result['success'] != true) { die('0 - captcha'); }
	}


	$category = array();
	foreach ($_POST as $key => $value) {
		if (strpos($key, 'category-') === 0) {
			$category[] = $value;
		}
	}

	$new_event = array(
	  'post_title'    => $_POST['title'],
	  'post_content'  => (isset($_POST['description']) ? $_POST['description'] : ''),
	  'post_category'  => $category,
	  'post_status'   => ($dpProEventCalendar_class->calendar_obj->publish_new_event ? 'publish' : 'pending'),
	  'post_type'	  => 'pec-events'
	);
	
	if(!is_user_logged_in() && $dpProEventCalendar_class->calendar_obj->assign_events_admin > 0) {
		$new_event['post_author'] = $dpProEventCalendar_class->calendar_obj->assign_events_admin;
	} 

	/*else {
		$new_event['post_author'] = $current_user->ID;
	}*/
	
	if(is_numeric($_POST['edit_calendar']) && is_numeric($_POST['edit_event'])) {
		$inserted = $_POST['edit_event'];
		
		$event_edit = get_post($inserted);
		if($event_edit->post_author == $current_user->ID || current_user_can( 'manage_options' )) {
			$new_event['ID'] = $inserted;
			$new_event['post_status'] = $event_edit->post_status;
			
			wp_update_post($new_event);

			do_action('pec_action_edit_event', $inserted);

		} else {
			die();	
		}
		
	} else {
		$inserted = wp_insert_post($new_event);
		
		do_action('pec_action_new_event', $inserted);
	}
	
	if(!is_numeric($inserted)) { die(); }
	
	wp_set_post_terms($inserted, $category, 'pec_events_category');
	
	if(is_array($dpProEventCalendar['custom_fields_counter'])) {
		$counter = 0;
		
		foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
			update_post_meta($inserted, "pec_custom_".$dpProEventCalendar['custom_fields']['id'][$counter], $_POST['pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter]]);
			$counter++;		
		}
	}
	$all_day = $_POST['all_day'];

	if($_POST['time_hours'] == "" && !isset($_POST['edit_event'])) {

		$all_day = 1;
		
	}

	// Set Location
	$location = $_POST['location'];

	if($location == "-1") {
		if($_POST['location_name'] != "") {
			$location = dpProEventCalendar_create_venue($_POST['location_name'], $_POST['location_address'], $_POST['googlemap'], $lnlat = $_POST['map_lnlat']);
		} else {
			$location = "";
		}
	}

	if(isset($_POST['time_hours']) && $_POST['time_hours'] != "") {
		$time_hours = $_POST['time_hours'];
	} else {
		if(is_numeric($_POST['edit_event'])) {
			$pec_date_time = get_post_meta($_POST['edit_event'], 'pec_date', true);
			$time_hours = date('h', strtotime($pec_date_time));
		} else {
			$time_hours = '00';
		}
	}

	if(isset($_POST['time_minutes']) && $_POST['time_minutes'] != "") {
		$time_minutes = $_POST['time_minutes'];
	} else {
		if(is_numeric($_POST['edit_event'])) {
			$pec_date_time = get_post_meta($_POST['edit_event'], 'pec_date', true);
			$time_minutes = date('i', strtotime($pec_date_time));
		} else {
			$time_minutes = '00';
		}
	}

	$end_time_hours = '';
	if(isset($_POST['end_time_hh']) && $_POST['end_time_hh'] != "") {
		$end_time_hours = $_POST['end_time_hh'];
	}

	$end_time_mins = '';
	if(isset($_POST['end_time_mm']) && $_POST['end_time_mm'] != "") {
		$end_time_mins = $_POST['end_time_mm'];
	}

	$end_date = '';
	if(isset($_POST['end_date']) && $_POST['end_date'] != "") {
		$end_date = $_POST['end_date'];
	}

	update_post_meta($inserted, "pec_timezone", $_POST['timezone']);
	update_post_meta($inserted, "pec_extra_dates", $_POST['extra_dates']);
	update_post_meta($inserted, "pec_link", $_POST['link']);
	update_post_meta($inserted, "pec_share", $_POST['share']);
	update_post_meta($inserted, "pec_location", $location);
	update_post_meta($inserted, "pec_phone", $_POST['phone']);
	update_post_meta($inserted, "pec_color", $_POST['color']);	
	/*update_post_meta($inserted, "pec_map", $_POST['googlemap']);
	update_post_meta($inserted, "pec_map_lnlat", $_POST['map_lnlat']);*/
	update_post_meta($inserted, 'pec_id_calendar', $calendar);
	update_post_meta($inserted, 'pec_date', $_POST['date'].' '.$time_hours.':'.$time_minutes.':00');
	update_post_meta($inserted, 'pec_all_day', $all_day);
	update_post_meta($inserted, 'pec_recurring_frecuency', $_POST['recurring_frecuency']);
	update_post_meta($inserted, 'pec_end_date', $end_date);
	update_post_meta($inserted, 'pec_end_time_hh', $end_time_hours);
	update_post_meta($inserted, 'pec_end_time_mm', $end_time_mins);
	update_post_meta($inserted, 'pec_hide_time', $_POST['hide_time']);
	
	update_post_meta( $inserted, 'pec_daily_every', $_POST['pec_daily_every'] );
	update_post_meta( $inserted, 'pec_daily_working_days', $_POST['pec_daily_working_days'] );
	update_post_meta( $inserted, 'pec_weekly_day', $_POST['pec_weekly_day'] );
	update_post_meta( $inserted, 'pec_weekly_every', $_POST['pec_weekly_every'] );
	update_post_meta( $inserted, 'pec_monthly_every', $_POST['pec_monthly_every'] );
	update_post_meta( $inserted, 'pec_monthly_position', $_POST['pec_monthly_position'] );
	update_post_meta( $inserted, 'pec_monthly_day', $_POST['pec_monthly_day'] );
	update_post_meta( $inserted, 'pec_enable_booking', $_POST['booking_enable'] );
	update_post_meta( $inserted, 'pec_booking_limit', $_POST['limit'] );
	update_post_meta( $inserted, 'pec_booking_block_hours', $_POST['block_hours'] );
	update_post_meta( $inserted, 'pec_booking_price', $_POST['price'] );
		
	$image = $_FILES['event_image'];
	$timestamp = time();
	
	$wp_filetype = wp_check_filetype(basename($image['name']), null );
	if(strtolower($wp_filetype['ext']) == "jpeg" || strtolower($wp_filetype['ext']) == "png" || strtolower($wp_filetype['ext']) == "gif" || strtolower($wp_filetype['ext']) == "jpg") {
		$uploads = wp_upload_dir();
		
		$image['name'] = md5($image['name']).'.'.strtolower($wp_filetype['ext']);

		$filesize = (filesize($image['tmp_name']) / 1000);
		$maxFileSize = convertBytes(ini_get('upload_max_filesize')) / 1000;
		$maxFileSize_settings = $dpProEventCalendar['max_file_size'];

		if($maxFileSize_settings < $maxFileSize) {
			$maxFileSize = $maxFileSize_settings;
		}
		
		if($maxFileSize != "" && $filesize > $maxFileSize) {
			return false;
		}
		
		if (!copy($image['tmp_name'], $uploads['path']."/".$current_user->ID."_".$timestamp."_".$image['name'])) {
			//echo "Error copying file...<br>";
		} else {
			
			$attachment = array(
			 'guid' => $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ), 
			 'post_mime_type' => $wp_filetype['type'],
			 'post_title' => '',
			 'post_content' => '',
			 'post_excerpt' => '',
			 'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment( $attachment, $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ) );
			
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploads['path'] . '/'.$current_user->ID."_".$timestamp."_" . basename( $image['name'] ) );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			
			update_post_meta($inserted, "_thumbnail_id", $attach_id);
		}
	}

	if($dpProEventCalendar_class->calendar_obj->email_admin_new_event && !is_numeric($_POST['edit_calendar'])) {
		add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
		add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
		
		$message = __('A new event is waiting for approval:', 'dpProEventCalendar') . ' ' . $_POST['title']." ( ".get_admin_url()."post.php?post=".$inserted."&action=edit )";
		
		$success_email = wp_mail( get_bloginfo('admin_email'), __('New Event', 'dpProEventCalendar'), $message );
		
	}

	if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar])) {
	   $dpProEventCalendar_cache['calendar_id_'.$calendar] = array();
	   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
    }
	
	die();
}

function convertBytes( $value ) {
    if ( is_numeric( $value ) ) {
        return $value;
    } else {
        $value_length = strlen($value);
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        switch ( $unit ) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return $qty;
    }
}

add_action( 'wp_ajax_nopriv_cancelBooking', 'dpProEventCalendar_cancelBooking' );
add_action( 'wp_ajax_cancelBooking', 'dpProEventCalendar_cancelBooking' );
 
function dpProEventCalendar_cancelBooking() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
       die ( 'Error!');
		
	if(!is_numeric($_POST['calendar']) || !is_numeric($_POST['cancel_booking_id']) || !is_numeric($_POST['cancel_booking_event'])) { die(); }
	
	global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;

	wp_get_current_user();
	
	if(!is_user_logged_in()) { die(); }

	$calendar = $_POST['calendar'];
	$booking_id = $_POST['cancel_booking_id'];
	$event_id = $_POST['cancel_booking_event'];

	$table_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;

	$querystr = "
        UPDATE ".$table_booking." SET status = 'canceled_by_user', cancel_date = '".current_time( 'Y-m-d H:i:s' )."' 
		WHERE id_event = %d AND id_user = %d AND id = %d
        ";
    $wpdb->query($wpdb->prepare($querystr, $event_id, $current_user->ID, $booking_id));

    $calendar_data = $wpdb->get_row( 
		$wpdb->prepare("
			SELECT booking_cancel_email_template, booking_cancel_email_enable 
			FROM ".$table_name_calendars. " 
			WHERE id = %d", $calendar
		)
	);

    $booking_cancel_email_template = $calendar_data->booking_cancel_email_template;
	if($booking_cancel_email_template == '') {
		$booking_cancel_email_template = "Hi #USERNAME#,\n\nThe following booking has been canceled:\n\n#EVENT_DETAILS#\n\n#CANCEL_REASON#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
	}

	$booking_cancel_email_template_admin = str_replace(" #USERNAME#", "", $booking_cancel_email_template);
	
	add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
	add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
	$headers = array('Content-Type: text/html; charset=UTF-8');

	// Email to User
	$booking_user_email = $current_user->user_email;
	
	wp_mail( $booking_user_email, get_bloginfo('name'), apply_filters('pec_booking_email_cancel', $booking_cancel_email_template, $booking_id), $headers );

	$event_author_id = get_post_field( 'post_author', $event_id );
	
	wp_mail( get_the_author_meta( 'user_email', $event_author_id ), get_bloginfo('name'), apply_filters('pec_booking_email_cancel', $booking_cancel_email_template, $booking_id), $headers );




	do_action('pec_action_cancel_booking', $inserted);

	die();
}

add_action( 'wp_ajax_nopriv_removeEvent', 'dpProEventCalendar_removeEvent' );
add_action( 'wp_ajax_removeEvent', 'dpProEventCalendar_removeEvent' );
 
function dpProEventCalendar_removeEvent() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
       die ( 'Error!');
		
	if(!is_numeric($_POST['calendar'])) { die(); }
	
	global $current_user, $dpProEventCalendar_cache;
	wp_get_current_user();
	
	if(!is_user_logged_in()) { die(); }

	$calendar = $_POST['calendar'];

	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( false, $calendar );
	$dpProEventCalendar->getCalendarData();
	
	if(is_numeric($_POST['remove_event_calendar']) && is_numeric($_POST['remove_event'])) {
		$event = $_POST['remove_event'];
		
		$event_edit = get_post($event);
		if($event_edit->post_author == $current_user->ID && $event_edit->post_type == 'pec-events') {
						
			wp_delete_post($event);
			do_action('pec_action_remove_event', $event);

			if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar])) {
			   $dpProEventCalendar_cache['calendar_id_'.$calendar] = array();
			   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
		   }
		}
		
	}
	die();
}

function dpProEventCalendar_wp_mail_from_name( $original_email_from )
{
	return get_bloginfo('name');
}

function dpProEventCalendar_wp_mail_from( $original_email_address ) {
	global $dpProEventCalendar;
	//Make sure the email is from the same domain 
	//as your website to avoid being marked as spam.
	return ($dpProEventCalendar['wp_mail_from'] != "" ? $dpProEventCalendar['wp_mail_from'] : $original_email_address);
}

add_action( 'wp_ajax_nopriv_getSearchResults', 'dpProEventCalendar_getSearchResults' );
add_action( 'wp_ajax_getSearchResults', 'dpProEventCalendar_getSearchResults' );
 
function dpProEventCalendar_getSearchResults() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!isset($_POST['calendar']) || !isset($_POST['key'])) { die(); }
	
	$calendar = $_POST['calendar'];
	$key = $_POST['key'];
	$type = $_POST['type'];
	$author = $_POST['author'];
	$columns = $_POST['columns'];
	
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( false, $calendar, null, null, '', '', '', $author, '', $columns );
	
	die($dpProEventCalendar->getSearchResults( $key, $type ));
}

add_action( 'wp_ajax_nopriv_bookEvent', 'dpProEventCalendar_bookEvent' );
add_action( 'wp_ajax_bookEvent', 'dpProEventCalendar_bookEvent' );
 
function dpProEventCalendar_bookEvent() {
	header("HTTP/1.1 200 OK");
	global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!isset($_POST['calendar']) || !is_numeric($_POST['event_id']) || !isset($_POST['event_date'])) { die(); }

	$calendar = $_POST['calendar'];
	$comment = $_POST['comment'];
	$quantity = $_POST['quantity'];
	$id_event = $_POST['event_id'];
	$id_coupon = $_POST['pec_payment_discount_id'];
	$coupon = $_POST['pec_payment_discount_coupon'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$event_date = $_POST['event_date'];
	$return_url = $_POST['return_url'];
	$ticket = $_POST['ticket'];
	$extra_fields = serialize($_POST['extra_fields']);
	$price = get_post_meta($id_event, 'pec_booking_price', true);
	if($price == '.00' || $price == '0') {
		$price = "";
	}

	if(!is_numeric($quantity) || $quantity < 1) {
		$quantity = 1;	
	}
	
	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar );
	
	if(!is_user_logged_in() && !$dpProEventCalendar_class->calendar_obj->booking_non_logged) {
		die();	
	}
	
	// Check Coupon
	$coupon_discount = '';
	if(is_numeric($id_coupon)) {
		if(strtolower(get_the_title($id_coupon)) == strtolower($coupon)) {
			$coupon_discount = get_post_meta($id_coupon, 'pec_coupon_amount', true);
		} else {
			$id_coupon = "";
		}
	}
	
	$wpdb->insert( 
		$table_name_booking, 
		array( 
			'id_calendar' 	=> $calendar, 
			'booking_date' 	=> date('Y-m-d H:i:s'),
			'event_date'	=> $event_date,
			'id_event'		=> $id_event,
			'id_user'		=> $current_user->ID,
			'id_coupon'		=> $id_coupon,
			'coupon_discount'=> $coupon_discount,
			'comment'		=> $comment,
			'quantity'		=> $quantity,
			'name'			=> $name,
			'phone'			=> $phone,
			'email'			=> $email,
			'extra_fields'	=> $extra_fields,
			'status'		=> (($price != '' || $ticket != "") ? 'pending' : '')
		), 
		array( 
			'%d', 
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		) 
	);
	
	$id_booking = $wpdb->insert_id;

	do_action('pec_action_book_event', $id_booking, $id_event, $current_user->ID);

	if($price == "" && $ticket == '') {
	
		// Send emails for free bookings
		
		if(is_user_logged_in()) {
			
			$userdata = get_userdata($current_user->ID);
			
		} else {
			
			$userdata = new stdClass();
			$userdata->display_name = $name;
			$userdata->user_email = $email;
				
		}
		
		if($dpProEventCalendar_class->calendar_obj->booking_email_template_user == '') {
			$dpProEventCalendar_class->calendar_obj->booking_email_template_user = "Hi #USERNAME#,\n\nThanks for booking the event:\n\n#EVENT_DETAILS#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		}
		
		if($dpProEventCalendar_class->calendar_obj->booking_email_template_admin == '') {
			$dpProEventCalendar_class->calendar_obj->booking_email_template_admin = "The user #USERNAME# (#USEREMAIL#) booked the event:\n\n#EVENT_DETAILS#\n\n#COMMENT#\n\n#SITE_NAME#";
		}
		
		add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
		add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
		$headers = 'Content-Type: text/html; charset=UTF-8';

		// Email to User

		wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters('pec_booking_email', $dpProEventCalendar_class->calendar_obj->booking_email_template_user, $id_event, $userdata->display_name, $userdata->user_email, $event_date, $comment, $quantity, $phone, $extra_fields), $headers );
		
		// Email to Author
		$event_author_id = get_post_field( 'post_author', $id_event );
		
		wp_mail( get_the_author_meta( 'user_email', $event_author_id ), get_bloginfo('name'), apply_filters('pec_booking_email', $dpProEventCalendar_class->calendar_obj->booking_email_template_admin, $id_event, $userdata->display_name, $userdata->user_email, $event_date, $comment, $quantity, $phone, $extra_fields), $headers );
	}
	
	$return = array(
		array(
			"book_btn" => $dpProEventCalendar_class->translation['TXT_BOOK_EVENT_REMOVE'], 
			"notification" => '<p>'.$dpProEventCalendar_class->translation['TXT_BOOK_EVENT_SAVED'].'</p>',
			"gateway_screen" => apply_filters('pec_receipt_gateways', (is_numeric($ticket) ? $ticket : ''), $id_event, $event_date, $id_booking, $return_url, $quantity)
		),
		array(
			"book_btn" => $dpProEventCalendar_class->translation['TXT_BOOK_EVENT'], 
			"notification" => '<p>'.$dpProEventCalendar_class->translation['TXT_BOOK_EVENT_REMOVED'].'</p>'
		)
	);
	
	//die(!$id_booking ? json_encode($return[0]) : json_encode($return[1]));
	die(json_encode($return[0]));
}

add_filter('pec_booking_email', 'dpProEventCalendar_bookingEmail', 10, 9);

function dpProEventCalendar_bookingEmail($template, $event_id, $user_name, $user_email, $event_date, $comment, $quantity, $user_phone = '', $extra_fields = '') {
	global $dpProEventCalendar;
	
	$template = str_replace("#USERNAME#", $user_name, $template);
	
	$template = str_replace("#COMMENT#", $comment, $template);
	
	$template = str_replace("#USEREMAIL#", $user_email, $template);
	
	$template = str_replace("#USERPHONE#", $user_phone, $template);
	
	$location_id = get_post_meta($event_id, 'pec_location', true);
	$location = get_the_title($location_id);
	$address = get_post_meta($location_id, 'pec_venue_address', true);
	if($address != "") {
		$location .= " (".$address.")";
	}

	$extra_fields = unserialize($extra_fields);
	if(!is_array($extra_fields)) 
		$extra_fields = array();

	$custom_fields = '';
	
	foreach($extra_fields as $key=>$value) {

		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
		
		if(is_array($field_index)) {
    		$field_index = $field_index[0];
    	} else {
    		$field_index = '';
    	}

		if($value != "" && is_numeric($field_index)) {
			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') {
				$value = __('Yes', 'dpProEventCalendar');
			}
			$custom_fields .= $dpProEventCalendar['booking_custom_fields']['name'][$field_index].": ".$value."\n\r";
		}

	}


	$template = str_replace("#EVENT_DETAILS#", "---------------------------\n\r".get_the_title($event_id)."\n\r".dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_date)).' - '.date(get_option('time_format'), strtotime(get_post_meta($event_id, 'pec_date', true)))."\n\r".__("Quantity", "dpProEventCalendar").": ".$quantity."\n\r".$location."\n\r".$custom_fields."---------------------------\n\r", $template);

	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return nl2br($template);
	
}

add_filter('pec_booking_email_cancel', 'dpProEventCalendar_bookingEmailCancel', 10, 2);

function dpProEventCalendar_bookingEmailCancel($template, $booking_id) {
	global $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;

	$booking_count = 0;
    $querystr = "
    SELECT *
    FROM $table_name_booking
	WHERE id = ".$booking_id;

    $booking = $wpdb->get_row($querystr, OBJECT);

    if(is_numeric($booking->id_user) && $booking->id_user > 0) {
		//$userdata = FALSE;
		//$userdata = WP_User::get_data_by( 'id', $booking->id_user );

		$userdata = get_userdata($booking->id_user);
	} else {
		$userdata = new stdClass();
		$userdata->display_name = $booking->name;
		$userdata->user_email = $booking->email;	
	}

	$event_id = $booking->id_event;
	$event_date = $booking->event_date;
	$quantity = $booking->quantity;
	$cancel_reason = $booking->cancel_reason;

	$template = str_replace("#USERNAME#", $userdata->display_name, $template);
	
	$template = str_replace("#COMMENT#", $booking->comment, $template);
	
	$template = str_replace("#USEREMAIL#", $userdata->user_email, $template);
	
	$template = str_replace("#USERPHONE#", $booking->phone, $template);
	
	$location_id = get_post_meta($event_id, 'pec_location', true);
	$location = get_the_title($location_id);
	$address = get_post_meta($location_id, 'pec_venue_address', true);
	if($address != "") {
		$location .= " (".$address.")";
	}

	$template = str_replace("#EVENT_DETAILS#", "---------------------------\n\r".get_the_title($event_id)."\n\r".dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_date)).' - '.date(get_option('time_format'), strtotime(get_post_meta($event_id, 'pec_date', true)))."\n\r".__("Quantity", "dpProEventCalendar").": ".$quantity."\n\r".$location."\n\r---------------------------\n\r", $template);
	
	$template = str_replace("#CANCEL_REASON#", $cancel_reason, $template);

	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return nl2br($template);
	
}

add_filter('pec_new_event_published', 'dpProEventCalendar_eventPublished', 10, 6);

function dpProEventCalendar_eventPublished($template, $event_title, $user_name) {
	
	$template = str_replace("#USERNAME#", $user_name, $template);
	
	$template = str_replace("#EVENT_TITLE#", $event_title, $template);
		
	$template = str_replace("#SITE_NAME#", get_bloginfo('name'), $template);
	
	return html_entity_decode ($template);
	
}

//add_action( 'wp_ajax_nopriv_removeBooking', 'dpProEventCalendar_removeBooking' );
add_action( 'wp_ajax_removeBooking', 'dpProEventCalendar_removeBooking' );
 
function dpProEventCalendar_removeBooking() {
	header("HTTP/1.1 200 OK");
	global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
	
	if(!is_user_logged_in()) {
		die();	
	}
	
	if(!is_numeric($_POST['booking_id'])) { die(); }
	
	$booking_id = $_POST['booking_id'];

	$wpdb->delete( $table_name_booking, array( 'id' => $booking_id ) );
		
}

add_action( 'wp_ajax_setSpecialDates', 'dpProEventCalendar_setSpecialDates' );
 
function dpProEventCalendar_setSpecialDates() {
	header("HTTP/1.1 200 OK");
    $nonce = $_POST['postEventsNonce'];
	//if ( ! wp_verify_nonce( $nonce, 'ajax-get-events-nonce' ) )
    //    die ( 'Busted!');
		
	if(!isset($_POST['calendar']) || !isset($_POST['date'])) { die(); }
	
	$calendar = $_POST['calendar'];
	$sp = $_POST['sp'];
	$date = $_POST['date'];
	
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( true, $calendar );
	
	$dpProEventCalendar->setSpecialDates( $sp, $date );
	
	die();
}

function dpProEventCalendar_updateNotice(){
    echo '<div class="updated">
       <p>Updated Succesfully.</p>
    </div>';
}

if(@$_GET['settings-updated'] && 
($_GET['page'] == 'dpProEventCalendar-admin' 
|| $_GET['page'] == 'dpProEventCalendar-events' 
|| $_GET['page'] == 'dpProEventCalendar-special'
|| $_GET['page'] == 'dpProEventCalendar-payments')) {
	add_action('admin_notices', 'dpProEventCalendar_updateNotice');
}

/*
function pec_title_filter( $title, $id = null ) {

    if ( get_post_type($id) == 'pec-events' && is_single() ) {
  
	    $date = get_post_meta($id, 'pec_date', true);
	    return '('.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($date)).') '.$title;
	    
    }

    return $title;
}
add_filter( 'the_title', 'pec_title_filter', 10, 2 );
*/
function dpProEventCalendar_pro_event_calendar_init() {
  global $dpProEventCalendar;
  

  if(!isset($dpProEventCalendar['events_slug'])) {
  	$dpProEventCalendar['events_slug'] = '';
  }

  $events_slug = ( $dpProEventCalendar['events_slug'] != "" ? $dpProEventCalendar['events_slug'] : _x('pec-events', 'events slug', 'dpProEventCalendar'));

  add_rewrite_rule(
        '^'.$events_slug.'/([^/]*)/?',
        'index.php?pec-events=$matches[1]',
        'top' );

  add_rewrite_rule(
        '^'.$events_slug.'/([^/]*)/([^/]*)/?',
        'index.php?pec-events=$matches[1]&event_date=$matches[2]',
        'top' );
    add_rewrite_tag('%event_date%','([^&]+)');


  $labels = array(
    'name' => __('Pro Event Calendar', 'dpProEventCalendar'),
    'singular_name' => __('Events', 'dpProEventCalendar'),
    'add_new' => __('Add New', 'dpProEventCalendar'),
    'add_new_item' => __('Add New Event', 'dpProEventCalendar'),
    'edit_item' => __('Edit Event', 'dpProEventCalendar'),
    'new_item' => __('New Event', 'dpProEventCalendar'),
    'all_items' => __('All Events', 'dpProEventCalendar'),
    'view_item' => __('View Event', 'dpProEventCalendar'),
    'search_items' => __('Search Events', 'dpProEventCalendar'),
    'not_found' =>  __('No Events Found', 'dpProEventCalendar'),
    'not_found_in_trash' => __('No Events Found in Trash', 'dpProEventCalendar'), 
    'parent_item_colon' => '',
    'menu_name' => __('Events', 'dpProEventCalendar')
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
	'exclude_from_search' => false,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => $events_slug, 'with_front' => false),
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
	'show_in_menu' => 'dpProEventCalendar-admin',
    'menu_position' => null,
    'show_in_rest'       => true,
    'menu_icon' => 'dashicons-calendar-alt',
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'publicize' ),
    'taxonomies' => array('pec_events_category', 'post_tag')
  ); 

  register_post_type( 'pec-events', $args );

  $labels = array(
    'name' => __('Venues', 'dpProEventCalendar'),
    'singular_name' => __('Venue', 'dpProEventCalendar'),
    'add_new' => __('Add New', 'dpProEventCalendar'),
    'add_new_item' => __('Add New Venue', 'dpProEventCalendar'),
    'edit_item' => __('Edit Venue', 'dpProEventCalendar'),
    'new_item' => __('New Venue', 'dpProEventCalendar'),
    'all_items' => __('Venues', 'dpProEventCalendar'),
    'view_item' => __('View Venue', 'dpProEventCalendar'),
    'search_items' => __('Search Venues', 'dpProEventCalendar'),
    'not_found' =>  __('No Venues Found', 'dpProEventCalendar'),
    'not_found_in_trash' => __('No Venues Found in Trash', 'dpProEventCalendar'), 
    'parent_item_colon' => '',
    'menu_name' => __('Venues', 'dpProEventCalendar')
  );

  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
	'exclude_from_search' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => _x('pec-venues', 'venues slug', 'dpProEventCalendar'), 'with_front' => false ),
    'capability_type' => 'post',
    'has_archive' => false, 
    'hierarchical' => false,
	'show_in_menu' => 'dpProEventCalendar-admin',
    'menu_position' => null,
    'show_in_rest'       => true,
    'menu_icon' => 'dashicons-calendar-alt',
    'supports' => array( 'title', 'thumbnail' ),
    'taxonomies' => array()
  ); 

  register_post_type( 'pec-venues', $args );

  $labels = array(
    'name' => __('Organizers', 'dpProEventCalendar'),
    'singular_name' => __('Organizer', 'dpProEventCalendar'),
    'add_new' => __('Add New', 'dpProEventCalendar'),
    'add_new_item' => __('Add New Organizer', 'dpProEventCalendar'),
    'edit_item' => __('Edit Organizer', 'dpProEventCalendar'),
    'new_item' => __('New Organizer', 'dpProEventCalendar'),
    'all_items' => __('Organizers', 'dpProEventCalendar'),
    'view_item' => __('View Organizer', 'dpProEventCalendar'),
    'search_items' => __('Search Organizers', 'dpProEventCalendar'),
    'not_found' =>  __('No Organizer Found', 'dpProEventCalendar'),
    'not_found_in_trash' => __('No Organizers Found in Trash', 'dpProEventCalendar'), 
    'parent_item_colon' => '',
    'menu_name' => __('Organizers', 'dpProEventCalendar')
  );

  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
	'exclude_from_search' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => _x('pec-organizers', 'organizers slug', 'dpProEventCalendar'), 'with_front' => false ),
    'capability_type' => 'post',
    'has_archive' => false, 
    'hierarchical' => false,
	'show_in_menu' => 'dpProEventCalendar-admin',
    'menu_position' => null,
    'show_in_rest'       => true,
    'menu_icon' => 'dashicons-calendar-alt',
    'supports' => array( 'title', 'thumbnail' ),
    'taxonomies' => array()
  ); 

  register_post_type( 'pec-organizers', $args );
  //flush_rewrite_rules();
  
}
add_action( 'init', 'dpProEventCalendar_pro_event_calendar_init' );

add_action( 'init', 'dpProEventCalendar_pro_event_calendar_taxonomies', 0 );



function dpProEventCalendar_change_title_text( $title ){

     $screen = get_current_screen();

     if  ( 'pec-organizers' == $screen->post_type ) {

          $title = __('Organizer Name', 'dpProEventCalendar');

     }

     return $title;
}
add_filter( 'enter_title_here', 'dpProEventCalendar_change_title_text' );

function dpProEventCalendar_pro_event_calendar_taxonomies() 
{
	global $dpProEventCalendar;
  
  if(!isset($dpProEventCalendar['categories_slug'])) {
  	$dpProEventCalendar['categories_slug'] = '';
  }
  
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Event Categories', 'taxonomy general name' ),
    'singular_name'       => _x( 'Category', 'taxonomy singular name' ),
    'search_items'        => __( 'Search Categories' ),
    'all_items'           => __( 'All Categories' ),
    'parent_item'         => __( 'Parent Category' ),
    'parent_item_colon'   => __( 'Parent Category:' ),
    'edit_item'           => __( 'Edit Category' ), 
    'update_item'         => __( 'Update Category' ),
    'add_new_item'        => __( 'Add New Category' ),
    'new_item_name'       => __( 'New Category Name' ),
    'menu_name'           => __( 'Category' )
  ); 	

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true,
    'show_in_rest'       => true,
    'rewrite'             => array( 'with_front' => false, 'slug' => ( $dpProEventCalendar['categories_slug'] != "" ? $dpProEventCalendar['categories_slug'] : _x('pec_events_category', 'event category slug', 'dpProEventCalendar')) )
  );

  register_taxonomy( 'pec_events_category', array( 'pec-events' ), $args );
}


add_action('admin_footer-edit.php', 'pec_custom_bulk_admin_footer');

function pec_custom_bulk_admin_footer() {

	global $post_type;
	if($post_type == 'pec-events') {
	  echo '
	  <script type="text/javascript">
	  	jQuery(document).ready(function() {
			jQuery("select[name=\'action\']").append("<option value=\'duplicate\'>Duplicate</option>");

		});
	  </script>';
	}
  
}

add_action('load-edit.php', 'pec_custom_bulk_action');

function pec_custom_bulk_action() {
	
	$action = (isset($_GET['action']) ? $_GET['action'] : '');
	
	// 2. security check
	if ($action == "duplicate" && $_GET['post_type'] == 'pec-events') {
		
		
		$post_ids = (isset($_GET['post']) ? $_GET['post'] : '');
		
		switch($action) {
			
			// 3. Perform the action
			
			case 'duplicate':
							
				$duplicated = 0;
				
				foreach( $post_ids as $post_id ) {
					$my_post = get_post($post_id, "ARRAY_A" );
					unset($my_post['ID']);
					$my_post['post_category'] = array();
					$my_post['post_date'] = date('Y-m-d H:i:s');
					$category = get_the_terms( $post_id, 'pec_events_category' ); 
					if(!empty($category)) {
						foreach ( $category as $cat){
							$my_post['post_category'][] =  $cat->term_id;
						}
					}

					if ( !$inserted = wp_insert_post( $my_post, false ) )
					
					wp_die( __('Error duplicating post.') );
					
					$meta_values = get_post_meta($post_id);
					
					foreach($meta_values as $key => $value) {
						foreach($value as $val) {
							add_post_meta($inserted, $key, $val);
						}
					}
					wp_set_post_terms( $inserted, $my_post['post_category'], 'pec_events_category' );
					$duplicated++;
				
				}
				
				// build the redirect url
				
				$sendback = esc_url_raw(add_query_arg( array( 'post_type' => 'pec-events', 'duplicated' => $duplicated, 'ids' => join(',', $post_ids) ), $sendback ));
				
			break;
			
			default: return;
			
		}
		
		// 4. Redirect client
		
		wp_redirect($sendback);
		
		exit();
	}

}

add_action('admin_notices', 'pec_custom_bulk_admin_notices');

function pec_custom_bulk_admin_notices() {

	global $post_type, $pagenow;
	
	if($pagenow == 'edit.php' && $post_type == 'pec-events' &&
	
		isset($_REQUEST['duplicated']) && (int) $_REQUEST['duplicated']) {
		
		$message = sprintf( _n( 'Post duplicated.', '%s posts duplicated.', $_REQUEST['duplicated'] ), number_format_i18n( $_REQUEST['duplicated'] ) );
		
		echo "
		<div class='updated'><p>{$message}</p></div>
		";
	
	}

}

function pec_truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
	if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}

add_action( 'wp_ajax_nopriv_ProEventCalendar_NewSubscriber', 'dpProEventCalendar_ProEventCalendar_NewSubscriber' );
add_action( 'wp_ajax_ProEventCalendar_NewSubscriber', 'dpProEventCalendar_ProEventCalendar_NewSubscriber' );

function dpProEventCalendar_ProEventCalendar_NewSubscriber() {
	global $dpProEventCalendar;
	
	$your_name = stripslashes($_POST['your_name']);
	$your_email = stripslashes($_POST['your_email']);
	$calendar = stripslashes($_POST['calendar']);
	
	if($dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
		//set POST variables
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$fields = array(
								'secret' => $dpProEventCalendar['recaptcha_secret_key'],
								'response' => $_POST['grecaptcharesponse'],
								'remoteip' => $_SERVER['REMOTE_ADDR']
						);
		
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		
		//execute post
		$result = curl_exec($ch);
		
		$result = json_decode($result, true);
		
		//close connection
		curl_close($ch);
		
		if($result['success'] != true) { die(__("Failed Captcha", "dpProEventCalendar")); }
	}
	
	require_once('classes/base.class.php');

	$dpProEventCalendar = new DpProEventCalendar( true, $calendar );
	
	$dpProEventCalendar->calendarSubscription($your_email, $your_name);
	
	die();	
}

add_action( 'wp_ajax_nopriv_ProEventCalendar_RateEvent', 'dpProEventCalendar_ProEventCalendar_RateEvent' );
add_action( 'wp_ajax_ProEventCalendar_RateEvent', 'dpProEventCalendar_ProEventCalendar_RateEvent' );

function dpProEventCalendar_ProEventCalendar_RateEvent() {
	global $dpProEventCalendar;
	
	if(!is_user_logged_in()) {
		die();	
	}
	
	$event_id = stripslashes($_POST['event_id']);
	$rate = stripslashes($_POST['rate']);
	$calendar = stripslashes($_POST['calendar']);
	
	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( true, $calendar );
	
	echo $dpProEventCalendar_class->rateEvent($event_id, $rate);
	
	die();	
}

add_action( 'wp_ajax_nopriv_getBookEventForm', 'dpProEventCalendar_getBookEventForm' );
add_action( 'wp_ajax_getBookEventForm', 'dpProEventCalendar_getBookEventForm' );

function dpProEventCalendar_getBookEventForm() {
	header("HTTP/1.1 200 OK");
	global $dpProEventCalendar;
	
	if(!is_numeric($_POST['event_id'])) {
		die();	
	}
	
	$event_id = stripslashes($_POST['event_id']);
	$calendar = stripslashes($_POST['calendar']);
	$date = stripslashes($_POST['date']);

	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar );
	
	echo $dpProEventCalendar_class->getBookingForm($event_id, $date);
	
	die();	
}

add_action( 'wp_ajax_nopriv_getEditEventForm', 'dpProEventCalendar_getEditEventForm' );
add_action( 'wp_ajax_getEditEventForm', 'dpProEventCalendar_getEditEventForm' );

function dpProEventCalendar_getEditEventForm() {
	header("HTTP/1.1 200 OK");
	global $dpProEventCalendar;
	
	if(!is_user_logged_in() || !is_numeric($_POST['event_id'])) {
		die();	
	}
	
	$event_id = stripslashes($_POST['event_id']);
	//$id_calendar = stripslashes($_POST['calendar']);
	$id_calendar = get_post_meta($event_id, 'pec_id_calendar', true);
	$id_calendar = explode(',', $id_calendar);
	$id_calendar = $id_calendar[0];
	
	require_once('classes/base.class.php');

	$dpProEventCalendar_class = new DpProEventCalendar( false, $id_calendar );
	
	echo $dpProEventCalendar_class->getAddForm($event_id);
	
	die();	
}

add_action('edit_post', 'dpProEventCalendar_editEvent');
add_action('publish_post', 'dpProEventCalendar_editEvent');
add_action('wp_trash_post', 'dpProEventCalendar_editEvent');
add_action('untrash_post', 'dpProEventCalendar_editEvent');
add_action('delete_post', 'dpProEventCalendar_editEvent');

function dpProEventCalendar_editEvent($post_ID) {
	@header("HTTP/1.1 200 OK");
	global $current_user, $wpdb, $dpProEventCalendar_cache;
	
	$post_type = get_post_type( (isset($_GET['post']) ? $_GET['post'] : '') );

	if((isset($_POST['post_type']) && 'pec-events' != $_POST['post_type']) && 'pec-events' != $post_type) return;

	if(isset($_POST['pec_id_calendar'])) {
		$calendar_id = explode(",", $_POST['pec_id_calendar']); 
		if(is_array($calendar_id)) {
			$calendar_id = $calendar_id[0];	
		}
	} else {
		$calendar_id = explode(",", get_post_meta($post_ID, 'pec_id_calendar', true)); 
		if(is_array($calendar_id)) {
			$calendar_id = $calendar_id[0];	
		}
	}

	if(isset($_POST['hidden_post_status']) && $_POST['hidden_post_status'] == 'pending' && $_POST['post_status'] == 'publish') {
		// Send email to event author
		
		require_once('classes/base.class.php');
		
		$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar_id );
		
		$userdata = get_userdata($_POST['post_author']);
		
		if($dpProEventCalendar_class->calendar_obj->new_event_email_template_published == '') {
			@$dpProEventCalendar_class->calendar_obj->new_event_email_template_published = "Hi #USERNAME#,\n\nThe event #EVENT_TITLE# has been approved.\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
		}
		
		add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
		add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
		
		// Email to User
		
		if($dpProEventCalendar_class->calendar_obj->new_event_email_enable) {
			wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters('pec_new_event_published', $dpProEventCalendar_class->calendar_obj->new_event_email_template_published, get_the_title($post_ID), $userdata->display_name) );
		}
				
	}
	
	if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar_id])) {
	   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
	   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
   }
}

function dpProEventCalendar_contentFilter($content) {
	global $dpProEventCalendar, $wp_query;

	if(is_array($GLOBALS) && null !== $GLOBALS['post']) {
		
		if ($GLOBALS['post']->post_type == 'pec-events' && $wp_query->is_single && !post_password_required($GLOBALS['post']->ID)) {

			if($dpProEventCalendar['rtl_support'] || get_post_meta($GLOBALS['post']->ID, 'pec_rtl', true) || is_rtl()) {
				wp_enqueue_style( 'dpProEventCalendar_rtlcss', dpProEventCalendar_plugin_url( 'css/rtl.css' ),
					false, DP_PRO_EVENT_CALENDAR_VER, 'all');
			}
			
			$calendar_id = explode(",", get_post_meta($GLOBALS['post']->ID, 'pec_id_calendar', true)); 
			
			$calendar_id = $calendar_id[0];	
			
			$content = '[dpProEventCalendar get="author"]'.
						'<div class="dp_pec_event_page_nav">'.
						'[dpProEventCalendar id="'.$calendar_id.'" get="actions"]'.
						'[dpProEventCalendar id="'.$calendar_id.'" get="book_event"]'.
						'<div class="dp_pec_clear"></div>'.
						'</div>'.
						'<div class="dp_pec_row">'.
						'<div class="dp_pec_col6">'.
						'[dpProEventCalendar id="'.$calendar_id.'" get="date"]'.
						'[dpProEventCalendar get="location"]'.
						'[dpProEventCalendar get="organizer"]'.
						'</div>'.
						'<div class="dp_pec_col6">'.
						'[dpProEventCalendar get="phone"]'.
						'[dpProEventCalendar get="link"]'.
						'[dpProEventCalendar get="age_range"]'.
						'[dpProEventCalendar id="'.$calendar_id.'" get="attendees"]'.
						'[dpProEventCalendar get="facebook_url"]'.
						'[dpProEventCalendar get="custom_fields"]'.
						'</div>'.
						'<div class="dp_pec_clear"></div>'.
						'</div>'.
						'[dpProEventCalendar get="video"]'.
						'<div class="dp_pec_clear"></div>'.
						$content.
						'[dpProEventCalendar get="map"]';
						
			if($dpProEventCalendar['custom_css'] != "") {
				$content .= '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';
			}
		}
	}
	// otherwise returns the database content
	return $content;
}

add_filter( 'the_content', 'dpProEventCalendar_contentFilter' );


//add_filter( 'get_the_excerpt', 'dpProEventCalendar_excerptFilter' );

/*
function dpProEventCalendar_loadTemplate( $template ) {
	global $dpProEventCalendar;
	
	// assuming you have created a page/post entitled 'debug'
	if ($GLOBALS['post']->post_type == 'pec-events' && $dpProEventCalendar['event_single_enable']) {
		
		remove_filter( 'the_content', 'dpProEventCalendar_contentFilter' );
		remove_all_actions('wp_enqueue_scripts');
		return dirname( __FILE__ ) . '/templates/default/template.php';
		
	}
	
	return $template;
	
}

add_filter( 'template_include', 'dpProEventCalendar_loadTemplate', 100 );*/

if(!function_exists('cal_days_in_month')) {
	
	function cal_days_in_month($month, $year) { 
		return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
	}
	
}

function dpProEventCalendar_str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

function dpProEventCalendar_booking_reminder() {
	global $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	$days_reminder_setting = $dpProEventCalendar['days_reminders'];
	if(!is_numeric($days_reminder_setting) || $days_reminder_setting == 0) {
		$days_reminder_setting = 3;
	}
	$booking_search_date = date('Y-m-d', strtotime('+'.$days_reminder_setting.' days'));
	$booking_search_id = array();
	
	//Search events with continuous bookings
	$args = array( 
			'posts_per_page' => -1, 
			'post_type'=> 'pec-events', 
			'meta_query' => array(
		array(
			'key' => 'pec_booking_continuous',
			'value' => 1,
		))
	);

	$continuous_events = get_posts( $args );

	if(!empty($continuous_events)) {
		
		require_once('classes/base.class.php');

		foreach ($continuous_events as $event) {
			$id_calendar = get_post_meta($event->ID, 'pec_id_calendar', true);
			$id_calendar = explode(',', $id_calendar);
			$id_calendar = $id_calendar[0];

			$dpProEventCalendar_class = new DpProEventCalendar( false, $id_calendar, null, null, '', '', $event->ID );

			$event_dates = $dpProEventCalendar_class->upcomingCalendarLayout( true, 1, '', $booking_search_date." 00:00:00", $booking_search_date." 23:59:59" );
			
			if(is_array($event_dates) && !empty($event_dates)) {
				$booking_search_id[] = $event->ID;
			}
		}
	}

	$querystr = "
		SELECT *
		FROM $table_name_booking
		WHERE (event_date = '".$booking_search_date."' ";
		if(is_array($booking_search_id) && !empty($booking_search_id)) {
			$querystr .= "OR id_event IN (".implode(",", $booking_search_id).")";
		}
		$querystr .= ") AND status <> 'pending' AND status <> 'canceled' AND status <> 'canceled_by_user'
		ORDER BY id DESC
		";
		
		$bookings_obj = $wpdb->get_results($querystr, OBJECT);
		$bookings_count = 0;
		if(!empty($bookings_obj)) {
			
			require_once('classes/base.class.php');
			
			foreach($bookings_obj as $booking) {

				if(is_numeric($booking->id_user) && $booking->id_user > 0) {
					$userdata = get_userdata($booking->id_user);
				} else {
					$userdata = new stdClass();
					$userdata->display_name = $booking->name;
					$userdata->user_email = $booking->email;	
				}
				
				$dpProEventCalendar_class = new DpProEventCalendar( false, $booking->id_calendar );
				
				$booking_email_template_reminder_user = $dpProEventCalendar_class->calendar_obj->booking_email_template_reminder_user;
				if($booking_email_template_reminder_user == '') {
					$booking_email_template_reminder_user = "Hi #USERNAME#,\n\nWe would like to remind you the booking of the event:\n\n#EVENT_DETAILS#\n\nKind Regards.\n#SITE_NAME#";
				}
				
				add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
				add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
				$headers = 'Content-Type: text/html; charset=UTF-8';

				// Email to User
				wp_mail( $userdata->user_email, get_bloginfo('name'), apply_filters('pec_booking_email', $booking_email_template_reminder_user, $booking->id_event, $userdata->display_name, $userdata->user_email, $booking_search_date, $booking->comment, $booking->quantity, $booking->phone, $booking->extra_fields), $headers );

				$bookings_count++;
			
			}


			/*$booking_email_template_reminder_admin = "Hi #USERNAME#,\n\nWe would like to remind you the booking of the event:\n\n#EVENT_DETAILS#\n\nKind Regards.\n#SITE_NAME#";
			
			// Email to Author
			$event_author_id = get_post_field( 'post_author', $booking->id_event );
			
			wp_mail( get_the_author_meta( 'user_email', $event_author_id ), get_bloginfo('name'), apply_filters('pec_booking_email', $booking_email_template_reminder_user, $booking->id_event, get_the_author_meta( 'display_name', $event_author_id ), get_the_author_meta( 'user_email', $event_author_id ), $booking_search_date, "", '', '', ''), $headers );*/

		}
}


function dpProEventCalendar_setup_booking_reminder() {
	
	global $dpProEventCalendar;

	if(!isset($dpProEventCalendar['disable_reminders']) || !$dpProEventCalendar['disable_reminders']) {
		if ( ! wp_next_scheduled( 'pecbookingreminder' ) ) {
		
			$scheduled = wp_schedule_event( time(), 'daily', 'pecbookingreminder');
		
		}
		
		add_action( 'pecbookingreminder', 'dpProEventCalendar_booking_reminder', 10 );
	} else {
		wp_clear_scheduled_hook( 'pecbookingreminder' );
	}

}

add_action( 'init', 'dpProEventCalendar_setup_booking_reminder', 10 );

function dpProEventCalendar_setup_ical_sync() {
	global $wpdb, $table_prefix;
	$table_name_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;

	$querystr = "
		SELECT id as calendar_id, sync_ical_url, sync_fb_page, sync_ical_frequency, sync_ical_enable, sync_ical_category
		FROM ".$table_name_calendar."
		";
	$calendars_obj = $wpdb->get_results($querystr, OBJECT);
	foreach($calendars_obj as $key) {
		
		if($key->sync_ical_enable && ($key->sync_ical_url != "" || $key->sync_fb_page != "")) {

			//Schedule

			if ( ! wp_next_scheduled( 'pecsyncical'.$key->calendar_id, array($key->calendar_id) ) ) {
				$scheduled = wp_schedule_event( time(), $key->sync_ical_frequency, 'pecsyncical'.$key->calendar_id, array($key->calendar_id));
				//die($scheduled.'<br>'.$key->calendar_id);
			}
			
			add_action( 'pecsyncical'.$key->calendar_id, 'dpProEventCalendar_ical_sync', 10 ,1 );
					/*if($_GET['pec_debug']) {
						echo $key->calendar_id . ' - '.$key->sync_fb_page.'<br>';
						dpProEventCalendar_ical_sync($key->calendar_id);
					}
						/*
						dpProEventCalendar_ical_sync($key->calendar_id);
					}*/
		} else {

			// Unschedule
			
			// Get the timestamp for the next event.
		
			wp_clear_scheduled_hook( 'pecsyncical'.$key->calendar_id, array($key->calendar_id) );

		}

	}
}
add_action( 'init', 'dpProEventCalendar_setup_ical_sync', 10 );


/**
 * On the scheduled action hook, run a function.
 */
function dpProEventCalendar_ical_sync($calendar_id) {
	
	global $wpdb, $table_prefix;
	$table_name_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;

	$querystr = "
		SELECT id as calendar_id, sync_ical_url, sync_fb_page, sync_ical_frequency, sync_ical_enable, sync_ical_category
		FROM ".$table_name_calendar."
		WHERE id = ".$calendar_id;
	$calendar_obj = $wpdb->get_row($querystr, OBJECT);
	
	if(empty($calendar_obj)) return;

	$filename_ical = $calendar_obj->sync_ical_url;
	$fb_page = $calendar_obj->sync_fb_page;
	$category = $calendar_obj->sync_ical_category;

	if($category == 0) {
		$category = '';
	}

	$filename_ical_arr = explode(",", $filename_ical);
	
	foreach($filename_ical_arr as $url) {

		if($url != "") {
			dpProEventCalendar_importICS($calendar_id, $url, '', $category);
		}
		
	}

	$fb_page_arr = explode(",", $fb_page);
	
	foreach($fb_page_arr as $url) {
		
		if($url != "") {
			dpProEventCalendar_importFB($calendar_id, $url, $category);
		}
		
	}

}

function dpProEventCalendar_create_venue($name, $address = '', $map = '', $lnlat = '', $link = '') {
	global $wpdb;

	$name = trim(stripslashes($name));

	if($name == "") {
		return '';
	}

	$search_query = 'SELECT ID FROM '.$wpdb->posts.'
                         WHERE post_type = "pec-venues" 
                         AND post_status = "publish"
                         AND post_title = %s';

	$result = $wpdb->get_row($wpdb->prepare($search_query, str_replace("&", "&amp;", $name)));

	if(count($result) > 0) {
		return $result->ID;
	} else {
		$args = array( 
			'posts_per_page' => 1, 
			'post_type'=> 'pec-venues', 
			"meta_query" => array(
				array(
				   'key' => 'pec_venue_name_id',
				   'value' => $name
				)
			)
		);
		
		$result = get_posts( $args );

		if(!empty($result)) {
			return $result[0]->ID;
		}
	}


	$venue_args = array(
	  'post_title'    => $name,
	  'post_status'   => 'publish',
	  'post_type'	  => 'pec-venues'
	);

	if(!is_user_logged_in()) {
		$users = get_users('role=administrator&number=1'); 
		foreach ($users as $user) {
			$venue_args['post_author'] = $user->ID;
		}
	}

	$venue_id = wp_insert_post( $venue_args );

	
	update_post_meta($venue_id, 'pec_venue_address', $address);

	update_post_meta($venue_id, 'pec_venue_map_lnlat', $lnlat);
	
	update_post_meta($venue_id, 'pec_venue_map', $map);

	update_post_meta($venue_id, 'pec_venue_link', $link);

	update_post_meta($venue_id, 'pec_venue_name_id', $name);

	return $venue_id;

}

function dpProEventCalendar_importFB($calendar_id, $event_url, $category_ics = '', $event_option = 2, $offset = '') {
	global $dpProEventCalendar, $table_prefix, $wpdb;

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if($expire_after == '' || !is_numeric($expire_after) || $expire_after < 0) {
		$expire_after = 10;
	}

	$event_list = array();
			

	if(!is_numeric($event_url)) {
		
		$event_url = str_replace("/?fref=nf", "", $event_url);
		$event_url = str_replace("/?ti=cl", "", $event_url);
		$event_url = str_replace("/timeline", "", $event_url);
		$event_url = substr($event_url, strrpos(rtrim($event_url, '/ '), '/') + 1);	
		
		$event_list[] = $event_url;
		
	} else {
		if($event_option == 1) {
			$event_list[] = $event_url;
		}

	}
	
	require_once( dirname (__FILE__) . '/includes/Facebook/facebook.php' );

	$facebook = new FacebookGraphV2(array(
	  'appId'  => $dpProEventCalendar['facebook_app_id'],
	  'secret' => $dpProEventCalendar['facebook_app_secret'],
	));

	FacebookGraphV2::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
	FacebookGraphV2::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
	
	if($event_option == 2) {
		@$last_chars = rtrim(substr($event_url, strrpos($event_url, '-') + 1), '/');
		if(strlen($last_chars) > 10 && is_numeric($last_chars) && !is_numeric($event_url)) {
			$event_url = $last_chars;
		}

		if(!is_numeric($event_url)) {

			
			try {
			$response = $facebook->api('/'.$event_url, 'get');
			
			} catch (Exception $e) {
				//die($e->getMessage());
				return;
			}

			$graph_arr = $response;
			
			$event_url = $graph_arr['id'];
				
		}

		try {
			$response = $facebook->api('/'.$event_url.'/events?limit=200', 'get');
		
		} catch (Exception $e) {
			return;
		}
		$graph_arr = $response;
		
		$event_list = array();
		foreach($graph_arr['data'] as $key) {
			$event_list[] = $key['id'];
		}
	
	}

	// Might be needed in some servers
	//ini_set('memory_limit','512M');
	@set_time_limit(0);
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	/*echo '<pre>';
	print_r($event_list);
	echo '</pre>';
	die();*/
	foreach($event_list as $key) {
		$key = rtrim($key, '/');
		
		if(!is_numeric($key)) {
			continue;
		}

		$event_url = $key;
		
		try {
		$response = $facebook->api('/'.$event_url, 'get', array('fields' => array('cover','event_times', 'description', 'end_time', 'name', 'place', 'start_time', 'ticket_uri')));
		
		} catch (Exception $e) {
			continue;
		}

		$graph_arr = $response;
		/*echo '<pre>';
		print_r($graph_arr);
		echo '</pre>';
		die();
*/
		$extra_times = array();
		if(isset($graph_arr['event_times'])) {
			$event_times = $graph_arr['event_times'];
			$event_times_arr = array();
			
			foreach($event_times as $key) {
				$event_times_arr[] = date('Y-m-d', strtotime($key['start_time']));
			}

			$extra_times = implode(',',$event_times_arr);
		}
		

		//$picture = $facebook->api('/'.$event_url.'/picture', 'get', array('redirect' => false, 'type' => 'large'));
		$picture = $graph_arr['cover'];
		if(is_array($picture)) {
			$picture = $picture['source'];
		}
		$ticket_uri = $graph_arr['ticket_uri'];
		if(empty($graph_arr['start_time'])) { continue; }

		// XX 
		$args = array( 
			'posts_per_page' => 1, 
			'post_type'=> 'pec-events', 
			"meta_query" => array(
				'relation' => 'AND',
				array(
				   'key' => 'pec_id_calendar',
				   'value' => $calendar_id,
				),
				array(
				   'key' => 'pec_fb_uid',
				   'value' => $graph_arr['id'].'@pec-no-uid',
				)
			)
		);
		/*
		,
				array(
				   'key' => 'pec_ics_uid_title',
				   'value' => $key['SUMMARY'],
				)
				*/
		
		$imported_posts = get_posts( $args );

		$fb_event = array(
		  'post_title'    => $graph_arr['name'],
		  'post_content'  => (isset($graph_arr['description']) ? $graph_arr['description'] : ''),
		  'post_status'   => 'publish',
		  'post_category'  => array($category_ics),
		  'tax_input' 	  => array( 'pec_events_category' => $category_ics ),
		  'post_type'	  => 'pec-events'
		);


		if(!is_user_logged_in()) {
			$blogadmin = get_users( 'role=administrator' );

			$fb_event['post_author'] =  $blogadmin[0]->ID;
		}

		if(!empty($imported_posts)) {
			$disable_sync = get_post_meta($imported_posts[0]->ID, 'pec_disable_sync', true);
			if($disable_sync) {
				continue;
			}

			$fb_event['ID'] = $imported_posts[0]->ID;
			$fb_event['post_author'] = $imported_posts[0]->post_author;
		}
		
		$tzid = "UTC";
		$setTimeZone = get_option('timezone_string');

		if($setTimeZone == "") {

			$setTimeZone = timezone_name_from_abbr("", get_option('gmt_offset') * 3600, false);
			//$setTimeZone = dpProEventCalendar_tz_offset_to_name(get_option('gmt_offset'));
		}


		$start_date = new DateTime(date("Y-m-d H:i:s O", strtotime($graph_arr['start_time'])), new DateTimeZone($tzid));
		
		$start_date->setTimeZone(new DateTimeZone($setTimeZone));
		
		$pec_date = $start_date->format('Y-m-d H:i:s');
		
		if($offset != "") {

			$end_date_hh = date('H', strtotime($offset.' hours', strtotime($end_date.' '.$end_date_hh.':'.$end_date_mm.':00')));
			$pec_date = date('Y-m-d H:i:s', strtotime($offset.' hours', strtotime($pec_date)));

		}

		$end_date = "";
		$end_date_hh = "";
		$end_date_mm = "";
		if(isset($graph_arr['end_time'])) {
			$end_date = new DateTime(date("Y-m-d H:i:s O", strtotime($graph_arr['end_time'])), new DateTimeZone($tzid));
			$end_date->setTimeZone(new DateTimeZone($setTimeZone));
			$end_date_hh = $end_date->format('H');
			$end_date_mm = $end_date->format('i');
			$end_date = $end_date->format('Y-m-d');
		}

		$location = $graph_arr['place']['name'];
		$venue_id = '';
		// Create Venue
		if($location != "") {

			$venue_address = "";
			$venue_map = "";
			if(isset($graph_arr['place']['location']['street'])) {
				$venue_address = $graph_arr['place']['location']['street'];
				$venue_map .= $venue_address;
			}
			
			if(isset($graph_arr['place']['location']['city'])) {
				$venue_map .= ', '.$graph_arr['place']['location']['city'];
			}
			
			if(isset($graph_arr['place']['location']['country'])) {
				$venue_map .= ', '.$graph_arr['place']['location']['country'];
			}

			$venue_lnlat = "";
			if(isset($graph_arr['place']['location']['latitude'])) {
				$venue_lnlat .= $graph_arr['place']['location']['latitude'];
				$venue_lnlat .= ', '.$graph_arr['place']['location']['longitude'];
			}

			if($venue_map == "") {
				$venue_map = $venue_address;
			}

			if($venue_map == "") {
				$venue_map = $location;
			}

			$venue_id = dpProEventCalendar_create_venue($location, $venue_address, $venue_map, $venue_lnlat);
		}

		$recurring_frecuency = '';
		
		if($end_date != "" && substr($pec_date, 0, 10) != $end_date) {
	
			$recurring_frecuency = 1;

		}

		if(isset($dpProEventCalendar['remove_expired_enable']) && $dpProEventCalendar['remove_expired_enable'] && 
			(
				(
					$end_date != "" && 
					strtotime($end_date) < strtotime(current_time( 'Y-m-d' ) . ' -'.$expire_after.' days')
				) ||
				(
					$end_date == "" &&
					strtotime($pec_date) < strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days') &&
					$recurring_frecuency == ''
				)
			)
		) {
			continue;
		}

		$post_id = wp_insert_post( $fb_event, true );
		if(is_wp_error($post_id)) {
			continue;
		}
		
		if($recurring_frecuency == 1 && $end_date != "" && date("H", $pec_date) > $end_date_hh) {
			$recurring_frecuency = 0;
		}

		if(!empty($extra_times)) {
			$recurring_frecuency = 0;
			$end_date = '';
			update_post_meta($post_id, 'pec_extra_dates', $extra_times);
		}

		wp_set_post_terms($post_id, array($category_ics), 'pec_events_category');

		update_post_meta($post_id, 'pec_id_calendar', $calendar_id);
		update_post_meta($post_id, 'pec_date', $pec_date);
		update_post_meta($post_id, 'pec_all_day', ($graph_arr['is_date_only'] ? '1' : '0'));
		update_post_meta($post_id, 'pec_location', $venue_id);
		update_post_meta($post_id, 'pec_end_date', $end_date);
		update_post_meta($post_id, 'pec_link', $ticket_uri);
		update_post_meta($post_id, 'pec_share', '');
		update_post_meta($post_id, 'pec_map', '');
		update_post_meta($post_id, 'pec_map_lnlat', '');
		update_post_meta($post_id, 'pec_recurring_frecuency', $recurring_frecuency);
		update_post_meta($post_id, 'pec_end_time_hh', $end_date_hh);
		update_post_meta($post_id, 'pec_end_time_mm', $end_date_mm);
		update_post_meta($post_id, 'pec_hide_time', '');
		update_post_meta($post_id, 'pec_fb_event', 'https://www.facebook.com/events/'.$graph_arr['id']);
		
		update_post_meta($post_id, 'pec_fb_uid', $graph_arr['id'].'@pec-no-uid');	
		update_post_meta($post_id, 'pec_fb_uid_title', $graph_arr['name']);

		if($picture != "" && $picture != get_post_meta($post_id, 'pec_fb_image', true)) {

			dpProEventCalendar_fetch_media($picture, $post_id);
		}
		update_post_meta($post_id, 'pec_fb_image',$picture);	
		
	
	}
}

function dpProEventCalendar_importICS($calendar_id, $filename, $tmp_filename = '', $category_ics = '', $offset = '') {
	global $dpProEventCalendar_cache, $dpProEventCalendar, $table_prefix, $wpdb;

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if($expire_after == '' || !is_numeric($expire_after) || $expire_after < 0) {
		$expire_after = 10;
	}

	if($tmp_filename == "") {
		$tmp_filename = $filename;	
	}
	$extension = strrchr($tmp_filename, '.'); 
	$extensions = array('.ics');
	//if(in_array($extension, $extensions)) {
		require_once(dirname(__FILE__) . '/includes/ical_parser.php');
		$ical = new ICal($filename);
		$feed = $ical->cal;
		/*echo '<pre>';
		echo $filename;
			print_r($feed);
			echo '</pre>';
			die();*/
		if(!empty($feed)) {

			
			
			set_time_limit(0);
			
			$all_uid = array();
			$count = 0;

			/*
			echo '<pre>';
			print_r($feed['VTIMEZONE']);
			print_r($feed['STANDARD']);
			echo '</pre>';*/

			if(is_array($feed['VEVENT'])) {
				foreach($feed['VEVENT'] as $key) {
					/*echo '<pre>';
					print_r($key);
					echo '</pre>';*/
					if(!isset($key['SUMMARY']) || $key['SUMMARY'] == "") {
						$summary_arr = preg_grep('/^SUMMARY/', array_keys($key));
						$summary_arr = reset($summary_arr);
						
						$key['SUMMARY'] = $key[$summary_arr];
						
					}
		
					if($key['SUMMARY'] == "") { continue; }

					$count++;
					
					if(preg_match("/\p{Hebrew}/u", $key['DESCRIPTION']) || preg_match("/\p{Cyrillic}/u", $key['DESCRIPTION'])) {
						$key['DESCRIPTION'] = str_replace("​", "", ltrim($key['DESCRIPTION'], '<br>'));
					} else {
						$key['DESCRIPTION'] = str_replace("―", " - ", $key['DESCRIPTION']);
						$key['DESCRIPTION'] = utf8_encode(utf8_decode(str_replace("​", "", ltrim($key['DESCRIPTION'], '<br>'))));
					}
					
					foreach($key as $k => $v) {
						$key[substr($k, 0, strpos($k, ';'))] = $v;	
					}

					//$key['UID'] = "";

					if($key['UID'] == "") {
						$key['UID'] = $key['DTSTART'].$key['SUMMARY'].'@pec-no-uid';
					}
					
					// XX 
					$args = array( 
						'posts_per_page' => 1, 
						'post_type'=> 'pec-events', 
						"meta_query" => array(
							'relation' => 'AND',
							array(
							   'key' => 'pec_id_calendar',
							   'value' => $calendar_id,
							),
							array(
							   'key' => 'pec_ics_uid',
							   'value' => $key['UID'],
							)
						)
					);
					/*
					,
							array(
							   'key' => 'pec_ics_uid_title',
							   'value' => $key['SUMMARY'],
							)
							*/
					$imported_posts = get_posts( $args );
					
					// Create post object
					$ics_event = array(
					  'post_title'    => $key['SUMMARY'],
					  'post_content'  => $key['DESCRIPTION'],
					  'post_status'   => 'publish',
		  			  'post_category' => array($category_ics),
					  'tax_input' 	  => array( 'pec_events_category' => $category_ics ),
					  'post_type'	  => 'pec-events'
					);
					
					if(!empty($imported_posts)) {
						//continue;
						$disable_sync = get_post_meta($imported_posts[0]->ID, 'pec_disable_sync', true);
						if($disable_sync) {
							continue;
						}
						$ics_event['ID'] = $imported_posts[0]->ID;
					}

					$rrule_arr = "";
					if($key['RRULE'] != "") {
						$rrule = explode(';', $key['RRULE']);
						
						if(is_array($rrule)) {
							$rrule_arr = array();
							foreach($rrule as $rule) {
								$rrule_arr[substr($rule, 0, strpos($rule, '='))] = substr($rule, strrpos($rule, '=') + 1);
							}
						}
					}

					$tzid = "UTC";

					$setTimeZone = get_option('timezone_string');
					if($setTimeZone == "") {
						$setTimeZone = timezone_name_from_abbr("", get_option('gmt_offset') * 3600, false);
					}
					
					if($feed['VTIMEZONE'][0]['TZID;X-RICAL-TZSOURCE=TZINFO'] != "") {
						$feed['VTIMEZONE'][0]['TZID'] = $feed['VTIMEZONE'][0]['TZID;X-RICAL-TZSOURCE=TZINFO'];
					}

					if($feed['VTIMEZONE'][0]['TZID'] != "" || $feed['VCALENDAR']['TZID'] != "") {
						$tzid = ($feed['VTIMEZONE'][0]['TZID'] != "" ? $feed['VTIMEZONE'][0]['TZID'] : $feed['VCALENDAR']['TZID']);
					}
					if(isset($key['TZID']) && $key['TZID'] != "") {
						$tzid = $key['TZID'];
					}
					
					if(isset($feed['STANDARD'][0]['TZOFFSETTO'])) {
						$offset_to = substr_replace($feed['STANDARD'][0]['TZOFFSETTO'], ':', -2, 0);
						list($hours, $minutes) = explode(':', $offset_to);
						$seconds = ($hours * 60 * 60) + ($minutes * 60);
						// Get timezone name from seconds
						$tzid_tmp = timezone_name_from_abbr('', $seconds, false);

						if($tzid_tmp != "") {
							$tzid = $tzid_tmp;
						
						}
					}

					if($tzid == "Mountain Standard Time") {
						$tzid = "America/Denver";
					}
					if($tzid == "Eastern Standard Time") {
						$tzid = "America/New_York";
					}
					if($tzid == "Pacific Standard Time") {
						$tzid = "America/Los_Angeles";
					}
					if($tzid == "SE Asia Standard Time") {
						$tzid = "Asia/Vientiane";
					}
					if($tzid == "UTC+0") {
						$tzid = "UTC";
					}
					
					$tzid = str_replace("America-", "America/", $tzid);
					$tzid = str_replace(";VALUE=DATE-TIME", "", $tzid);
					$tzid = str_replace(";VALUE=DATE", "", $tzid);

					$start_date = new DateTime($key['DTSTART'], new DateTimeZone($tzid));
					$start_date->setTimeZone(new DateTimeZone($setTimeZone));
					
					
					if(strlen($key['DTEND']) == 8) {
						$end_date = date("Y-m-d", strtotime($key['DTEND']));
						$end_date_hh = date("H", strtotime($key['DTEND']));
						$end_date_mm = date("i", strtotime($key['DTEND']));
					} else {
						$end_date = new DateTime($key['DTEND'], new DateTimeZone($tzid));
						$end_date->setTimeZone(new DateTimeZone($setTimeZone));
						$end_date_hh = $end_date->format('H');
						$end_date_mm = $end_date->format('i');
						$end_date = $end_date->format('Y-m-d');
						
					}

					$all_day = false;
					$start_date_formatted = "";
					$set_until = false;
					
					if(strlen($key['DTSTART']) == 8) {
						$pec_date = date("Y-m-d", strtotime($key['DTSTART'])).' 00:00:00';
						
						$start_date_formatted = date("Y-m-d", strtotime($key['DTSTART']));
						$all_day = true;
						
						if(strlen($key['DTEND']) == 8) {
							$end_date = date("Y-m-d", strtotime($key['DTEND']) - 86400);
							if(strtotime($key['DTEND']) - strtotime($key['DTSTART']) <= 86400) {
								$end_date = '';
								$end_date_hh = '';
								$end_date_mm = '';
								$key['DTEND'] = '';
							}
						}
					} else {
						$pec_date = $start_date->format('Y-m-d H:i:s');
						
						if($offset != "") {

							$pec_date = date('Y-m-d H:i:s', strtotime($offset.' hours', strtotime($pec_date)));

						}

						$start_date_formatted = $start_date->format('Y-m-d');
					}

					$recurring_frecuency = '';
					$pec_daily_every = '';
					$pec_daily_working_days = '';
					$pec_weekly_every = '';
					$pec_weekly_day = '';
					$pec_monthly_every = '';
					$pec_monthly_position = '';
					$pec_monthly_day;

					if(is_array($rrule_arr)) {
						
						foreach($rrule_arr as $key2 => $value) {
							
							if($key2 == 'FREQ') {
								
								switch($value) {
									case 'DAILY':
										$recurring_frecuency = '1';
										break;
									case 'WEEKLY':
										$recurring_frecuency = '2';
										break;
									case 'MONTHLY':
										$recurring_frecuency = '3';
										break;
									case 'YEARLY':
										$recurring_frecuency = '4';
										break;
								}

							}
							
							if($key2 == 'FREQ' && $value == 'DAILY') {
								$pec_daily_every = $rrule_arr['INTERVAL'];
	
								$pec_daily_working_days = '';
							}
							
							if($key2 == 'UNTIL' && $value != "") {
								
								if(strlen($value) == 8) {
									$end_date = date("Y-m-d", strtotime($value));
									//$end_date_hh = date("H", strtotime($value));
									//$end_date_mm = date("i", strtotime($value));
									
								} else {
									$end_date = new DateTime($value, new DateTimeZone($tzid));
									$end_date->setTimeZone(new DateTimeZone($setTimeZone));
									
									//$end_date_hh = $end_date->format('H');
									//$end_date_mm = $end_date->format('i');
									$end_date = $end_date->format('Y-m-d');
								}
								
								$set_until = true;
								
							}


							
							if($key2 == 'COUNT' && $value != "") {
								
								switch($recurring_frecuency) {
									case 1:
										$end_date = date("Y-m-d", strtotime("+".($value - 1)." days", strtotime($start_date_formatted)));
										break;
									case 2:
										$end_date = date("Y-m-d", strtotime("+".$value." weeks", strtotime($start_date_formatted)));
										break;
									case 3:
										$end_date = date("Y-m-d", strtotime("+".$value." months", strtotime($start_date_formatted)));
										break;
									case 4:
										$end_date = date("Y-m-d", strtotime("+".$value." years", strtotime($start_date_formatted)));
										break;	
								}
								
								$set_until = true;
							}
							
							if($key2 == 'FREQ' && $value == 'WEEKLY') {
								$day_arr = array();
								foreach(explode(',', $rrule_arr['BYDAY']) as $day) {
									switch($day) {
										case 'MO':
											$day_arr[] = 1;
											break;
										case 'TU':
											$day_arr[] = 2;
											break;
										case 'WE':
											$day_arr[] = 3;
											break;
										case 'TH':
											$day_arr[] = 4;
											break;
										case 'FR':
											$day_arr[] = 5;
											break;
										case 'SA':
											$day_arr[] = 6;
											break;
										case 'SU':
											$day_arr[] = 7;
											break;
									}

									$pec_weekly_day = $day_arr;
								}

								$pec_weekly_every = $rrule_arr['INTERVAL'];
							}
							
							if($key2 == 'FREQ' && $value == 'MONTHLY') {
								
								$pec_monthly_every = $rrule_arr['INTERVAL']; 

								$setpos = "";
								switch($rrule_arr['BYSETPOS']) {
									case '1':
										$setpos = 'first';
										break;
									case '2':
										$setpos = 'second';
										break;
									case '3':
										$setpos = 'third';
										break;
	
									case '4':
										$setpos = 'fourth';
										break;
									case '-1':
										$setpos = 'last';
										break;
								}
								
								
								$day_arr = '';
								foreach(explode(',', $rrule_arr['BYDAY']) as $day) {
									switch($day) {
										case 'MO':
											$day_arr = 'monday';
											break;
										case 'TU':
											$day_arr = 'tuesday';
											break;
										case 'WE':
											$day_arr = 'wednesday';
											break;
										case 'TH':
											$day_arr = 'thursday';
											break;
										case 'FR':
											$day_arr = 'friday';
											break;
										case 'SA':
											$day_arr = 'saturday';
											break;
										case 'SU':
											$day_arr = 'sunday';
											break;
										case '1MO':
											$day_arr = 'monday';
											$setpos = 'first';
											break;
										case '1TU':
											$day_arr = 'tuesday';
											$setpos = 'first';
											break;
										case '1WE':
											$day_arr = 'wednesday';
											$setpos = 'first';
											break;
										case '1TH':
											$day_arr = 'thursday';
											$setpos = 'first';
											break;
										case '1FR':
											$day_arr = 'friday';
											$setpos = 'first';
											break;
										case '1SA':
											$day_arr = 'saturday';
											$setpos = 'first';
											break;
										case '1SU':
											$day_arr = 'sunday';
											$setpos = 'first';
											break;
										case '2MO':
											$day_arr = 'monday';
											$setpos = 'second';
											break;
										case '2TU':
											$day_arr = 'tuesday';
											$setpos = 'second';
											break;
										case '2WE':
											$day_arr = 'wednesday';
											$setpos = 'second';
											break;
										case '2TH':
											$day_arr = 'thursday';
											$setpos = 'second';
											break;
										case '2FR':
											$day_arr = 'friday';
											$setpos = 'second';
											break;
										case '2SA':
											$day_arr = 'saturday';
											$setpos = 'second';
											break;
										case '2SU':
											$day_arr = 'sunday';
											$setpos = 'second';
											break;
										case '3MO':
											$day_arr = 'monday';
											$setpos = 'third';
											break;
										case '3TU':
											$day_arr = 'tuesday';
											$setpos = 'third';
											break;
										case '3WE':
											$day_arr = 'wednesday';
											$setpos = 'third';
											break;
										case '3TH':
											$day_arr = 'thursday';
											$setpos = 'third';
											break;
										case '3FR':
											$day_arr = 'friday';
											$setpos = 'third';
											break;
										case '3SA':
											$day_arr = 'saturday';
											$setpos = 'third';
											break;
										case '3SU':
											$day_arr = 'sunday';
											$setpos = 'third';
											break;
										case '4MO':
											$day_arr = 'monday';
											$setpos = 'fourth';
											break;
										case '4TU':
											$day_arr = 'tuesday';
											$setpos = 'fourth';
											break;
										case '4WE':
											$day_arr = 'wednesday';
											$setpos = 'fourth';
											break;
										case '4TH':
											$day_arr = 'thursday';
											$setpos = 'fourth';
											break;
										case '4FR':
											$day_arr = 'friday';
											$setpos = 'fourth';
											break;
										case '4SA':
											$day_arr = 'saturday';
											$setpos = 'fourth';
											break;
										case '4SU':
											$day_arr = 'sunday';
											$setpos = 'fourth';
											break;
									}
								}
								
								$pec_monthly_position = $setpos;
								
								$pec_monthly_day = $day_arr;
							}
						}
					} else if($key['DTEND'] != "" && $end_date != $start_date_formatted && substr($key['DTSTART'], 0, 10) != substr($key['DTEND'], 0, 10)) {
	
						$recurring_frecuency = 1;
					}

					if(is_array($rrule_arr) && !$set_until) {
						$end_date = "";
					}

					if($recurring_frecuency == '' || ($end_date == $start_date_formatted && !$set_until)) {
						$end_date = "";
					}
					
					// Avoid expired events
					if(isset($dpProEventCalendar['remove_expired_enable']) && $dpProEventCalendar['remove_expired_enable'] && 
						(
							(
								$end_date != "" && 
								strtotime($end_date) < strtotime(current_time( 'Y-m-d' ) . ' -'.$expire_after.' days')
							) ||
							(
								$end_date == "" &&
								strtotime($pec_date) < strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days') &&
								$recurring_frecuency == ''
							)
						)
					) {
						continue;
					}

					// Insert the post into the database
					$post_id = wp_insert_post( $ics_event );
					
					wp_set_post_terms($post_id, array($category_ics), 'pec_events_category');

					if($key['EXDATE'] != "") {
						$exdate = wordwrap($key['EXDATE'], 15, ",", true);
						$exdate_string = array();
						foreach(explode(',', $exdate) as $exception) {
							$exdate_string[] = date("Y-m-d", strtotime($exception));
						}
						$exdate = implode(',', $exdate_string);
						
						update_post_meta($post_id, 'pec_exceptions', $exdate);	
						
					}
					
					/*
					if($key['UID'] == '7dmgqughp8dl2oarvtb96bq5j4@google.com') {
						echo $post_id;
						print_r($key);	
						die();
					}*/
					
					update_post_meta($post_id, 'pec_id_calendar', $calendar_id);

					if($all_day) {
						update_post_meta($post_id, 'pec_all_day', 1);
					} else {
						update_post_meta($post_id, 'pec_all_day', 0);
					}

					update_post_meta($post_id, 'pec_date', $pec_date);

					if($offset != "") {

						$end_date_hh = date('H', strtotime($offset.' hours', strtotime('1970-01-01 '.$end_date_hh.':'.$end_date_mm.':00')));

					}

					if($recurring_frecuency == 1 && $end_date != "" && !$all_day && date("H", $pec_date) > $end_date_hh) {
						$recurring_frecuency = 0;
					}

					update_post_meta($post_id, 'pec_recurring_frecuency', $recurring_frecuency);

					
					$location = $key['LOCATION'];
					$venue_id = "";
					// Create Venue
					if($location != "") {

						$venue_id = dpProEventCalendar_create_venue($location, '', $location);

					}
					
					update_post_meta($post_id, 'pec_daily_every', $pec_daily_every);
					update_post_meta($post_id, 'pec_daily_working_days', $pec_daily_working_days);
					update_post_meta($post_id, 'pec_weekly_every', $pec_weekly_every);
					update_post_meta($post_id, 'pec_weekly_day', $pec_weekly_day);
					update_post_meta($post_id, 'pec_monthly_every', $pec_monthly_every);
					update_post_meta($post_id, 'pec_monthly_position', $pec_monthly_position);
					update_post_meta($post_id, 'pec_monthly_day', $pec_monthly_day);

					update_post_meta($post_id, 'pec_end_date', $end_date);
					update_post_meta($post_id, 'pec_link', $key['URL']);
					update_post_meta($post_id, 'pec_share', '');
					update_post_meta($post_id, 'pec_end_time_hh', ($all_day ? '' : $end_date_hh));
					update_post_meta($post_id, 'pec_end_time_mm', ($all_day ? '' : $end_date_mm));
					update_post_meta($post_id, 'pec_hide_time', '');
					update_post_meta($post_id, 'pec_location', $venue_id);	
					update_post_meta($post_id, 'pec_map', '');	
					update_post_meta($post_id, 'pec_map_lnlat', '');	
					update_post_meta($post_id, 'pec_ics_uid', $key['UID']);		
					update_post_meta($post_id, 'pec_ics_uid_title', $key['SUMMARY']);
					update_post_meta($post_id, 'pec_ics_filename', sha1($filename));	

					/*
					global $wpdb, $table_prefix;
					$table_name_sd = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;

					$event_color = $wpdb->get_row( 
						$wpdb->prepare("
							SELECT *
							FROM ".$table_name_sd. " 
							WHERE title = %s", $filename
						)
					);

					if(isset($event_color) && is_numeric($event_color->id)) {
						update_post_meta($post_id, 'pec_color', $event_color->id);
					}*/

					$all_uid[] = $key['UID'];

				}
			}
			
			// Sync?
			
			if(filter_var($filename, FILTER_VALIDATE_URL) == $filename) {
				
				
				// Remove Not found events
				
				$args = array( 
					'posts_per_page' => -1, 
					'post_type'=> 'pec-events', 
					"meta_query" => array(
						'relation' => 'AND',
						array(
						   'key' => 'pec_ics_filename',
						   'value' => sha1($filename)
						),
						array(
						   'key' => 'pec_ics_uid',
						   'value' => $all_uid,
						   'compare' => 'NOT IN'
						)
					)
				);
				
				$not_found_posts = get_posts( $args );
				
				foreach($not_found_posts as $key) {
					wp_delete_post( $key->ID );
				}
				
			}
			
			if(isset($dpProEventCalendar_cache['calendar_id_'.$calendar_id])) {
			   $dpProEventCalendar_cache['calendar_id_'.$calendar_id] = array();
			   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
		   }

		}
	//}	

}

function dpProEventCalendar_addFeaturedImageSupport()
{
    $supportedTypes = get_theme_support( 'post-thumbnails' );

    if( $supportedTypes === false )
        add_theme_support( 'post-thumbnails', array( 'pec-events' ) );               
    elseif( is_array( $supportedTypes ) )
    {
        $supportedTypes[0][] = 'pec-events';
        add_theme_support( 'post-thumbnails', $supportedTypes[0] );
    }
}

add_action( 'after_setup_theme', 'dpProEventCalendar_addFeaturedImageSupport', 11 );

if(!function_exists('mb_substr')) {
	function mb_substr($string, $offset, $length, $encoding = '') {
		$arr = preg_split("//u", $string);
		$slice = array_slice($arr, $offset + 1, $length);
	  	return implode("", $slice);	
	}
}

function dpProEventCalendar_datediffInWeeks($date1, $date2)
{
    if($date1 > $date2) return dpProEventCalendar_datediffInWeeks($date2, $date1);

	if(method_exists('DateTime','createFromFormat')) {
		
		$first = DateTime::createFromFormat('Y-m-d H:i:s', $date1);
		$second = DateTime::createFromFormat('Y-m-d H:i:s', $date2);
	} else {
		$first = dpProEventCalendar_create_from_format('Y-m-d H:i:s', $date1);
		$second = dpProEventCalendar_create_from_format('Y-m-d H:i:s', $date2);
		
	}
    //return floor($first->diff($second)->days/7);
	/*echo "First: ".floor($first->diff($second)->days/7)."<br>";
	echo "Second: ".floor(round(($second->format('U') - $first->format('U')) / (60*60*24) / 7) )."<br>";
	die();*/
	if(!is_object($first) || !is_object($second)) {
		return 1;
	}
	
	return floor(($second->format('U') - $first->format('U')) / (60*60*24) / 7);
}

function dpProEventCalendar_get_date_diff($time1, $time2, $precision = 2, $intervals = array(
                                                                            'year' => array('year', 'years'),
                                                                            'month' => array('month', 'months'),
                                                                            'day' => array('day', 'days'),
                                                                            'hour' => array('hour', 'hours'),
                                                                            'minute' => array('minute', 'minutes')
                                                                        ))
{

	// If not numeric then convert timestamps
	if (!is_int($time1)) {
	    //$time1 = strtotime($time1);
	}
	if (!is_int($time2)) {
	    //$time2 = strtotime($time2);
	}
	
	// If time1 > time2 then swap the 2 values
	if ($time1 > $time2) {
	    list($time1, $time2) = array($time2, $time1);
	}
	// Set up intervals and diffs arrays
	$diffs = array();

	foreach ($intervals as $interval => $interval_label) {
	    // Create temp time from time1 and interval
	    $ttime = strtotime('+1 ' . $interval, $time1);
	    // Set initial values
	    $add = 1;
	    $looped = 0;
	    // Loop until temp time is smaller than time2
	    while ($time2 >= $ttime) {
	        // Create new temp time from time1 and interval
	        $add++;
	        $ttime = strtotime("+" . $add . " " . $interval, $time1);
	        $looped++;
	    }
	    $time1 = strtotime("+" . $looped . " " . $interval, $time1);
	    $diffs[$interval] = $looped;
	}
	$count = 0;
	$times = array();
	foreach ($diffs as $interval => $value) {
	    // Break if we have needed precission
	    if ($count >= $precision) {
	        break;
	    }
	    // Add value and interval if value is bigger than 0
	    if ($value > 0) {
	        // Add value and interval to times array
	        $times[] = $value . " " . $intervals[$interval][$value == 1 ? 0 : 1];
	        $count++;
	    }
	}
	// Return string with times
	return implode(", ", $times);
}

function dpProEventCalendar_create_from_format( $dformat, $dvalue )
{

	$ymd = sprintf(
		// This is a format string that takes six total decimal
		// arguments, then left-pads them with zeros to either
		// 4 or 2 characters, as needed
		'%04d-%02d-%02d %02d:%02d:%02d',
		date('Y', strtotime($dvalue)),  // This will be "111", so we need to add 1900.
		date('m', strtotime($dvalue)),      // This will be the month minus one, so we add one.
		date('d', strtotime($dvalue)), 
		date('H', strtotime($dvalue)), 
		date('i', strtotime($dvalue)), 
		date('s', strtotime($dvalue))
	);
	
	$new_schedule = new DateTime($ymd);
	
	return $new_schedule;

}

add_filter( 'the_excerpt_rss', 'dpProEventCalendar_rss_feed' );
add_filter( 'the_content_feed', 'dpProEventCalendar_rss_feed' );

function dpProEventCalendar_rss_feed($content)
{
	global $wp_query;
 
    $post_id = $wp_query->post->ID;
    $post_type = get_post_type( $post_id );
 
    if( $post_type == 'pec-events' ) {

		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			
		$content = '<img src="'.$image_attributes[0].'" alt="" />'.$content;
	}
	return $content;
}

function dpProEventCalendar_fetch_media($file_url, $post_id) {
	//require_once(ABSPATH . 'wp-load.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	global $wpdb;

	if(!$post_id) {
		return false;
	}

	//directory to import to	
	$artDir = '/importedmedia/';

	$uploads = wp_upload_dir();
	$upload_basedir = $uploads['basedir'];
	$upload_baseurl = $uploads['baseurl'];


	//if the directory doesn't exist, create it	
	if(!file_exists($upload_basedir.$artDir)) {
		mkdir($upload_basedir.$artDir);
	}

	//rename the file... alternatively, you could explode on "/" and keep the original file name
	$ext = preg_replace('/\?.*/', '', array_pop(explode(".", $file_url)));
	
	if($ext != "jpg" && $ext != "png" && $ext != "gif") {
		return false;
	}
	$new_filename = 'event-'.$post_id.".".$ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

	$opts = array(
	    "ssl"=>array(
	        "cafile" => dirname(__FILE__)."/includes/Facebook/fb_ca_chain_bundle.crt",
	        "verify_peer"=> true,
	        "verify_peer_name"=> true,
	    ),
	);

	$context = stream_context_create($opts);

	//if (@fclose(@fopen($file_url, "r", false, $context))) { //make sure the file actually exists

	if( ini_get('allow_url_fopen') ) {
		if(!copy($file_url, $upload_basedir.$artDir.$new_filename, $context))
		{
			/*$errors= error_get_last();
			echo "COPY ERROR: ".$errors['type'];
			echo "<br />\n".$errors['message'];*/

		} else {
			//echo "File copied from remote!";
		}
	} else {
		$ch = curl_init();
		$fp = fopen ($upload_basedir.$artDir.$new_filename, 'w+');
		$ch = curl_init($file_url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

		$siteurl = get_option('siteurl');
		$file_info = getimagesize($upload_basedir.$artDir.$new_filename);

		//create an array of attachment data to insert into wp_posts table
		$artdata = array();
		$artdata = array(
			'post_date' => current_time('mysql'),
			'post_date_gmt' => current_time('mysql'),
			'post_title' => $new_filename, 
			'post_status' => 'inherit',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),											
			'post_modified' => current_time('mysql'),
			'post_modified_gmt' => current_time('mysql'),
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'guid' => $upload_baseurl.$artDir.$new_filename,
			'post_mime_type' => $file_info['mime'],
			'post_excerpt' => '',
			'post_content' => ''
		);

		$save_path = $uploads['basedir'].'/importedmedia/'.$new_filename;

		//insert the database record
		$attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );

		//generate metadata and thumbnails
		if(function_exists("wp_generate_attachment_metadata")) {
			if ($attach_data = wp_generate_attachment_metadata( $attach_id, $save_path)) {
				wp_update_attachment_metadata($attach_id, $attach_data);
			}
		}
		
		//optional make it the featured image of the post it's attached to
		$rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));
	/*}
	else {
		return false;
	}*/

	return true;
}

add_action( 'wp_ajax_bookEventAdmin', 'dpProEventCalendar_bookEventAdmin' );
 
function dpProEventCalendar_bookEventAdmin() {
	global $wpdb, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	if(!is_numeric($_POST['eventid'])) { die(); }

	$eventid = $_POST['eventid'];
	$userid = $_POST['userid'];
	$phone = $_POST['phone'];
	$quantity = $_POST['quantity'];
	$status = $_POST['status'];
	$event_date = $_POST['date'];
	$comment = '';

	$wpdb->insert( 
		$table_name_booking, 
		array( 
			//'id_calendar' 	=> $calendar, 
			'booking_date' 	=> date('Y-m-d H:i:s'),
			'event_date'	=> $event_date,
			'id_event'		=> $eventid,
			'id_user'		=> $userid,
			//'id_coupon'		=> $id_coupon,
			//'coupon_discount'=> $coupon_discount,
			'comment'		=> $comment,
			'quantity'		=> $quantity,
			//'name'			=> $name,
			'phone'			=> $phone,
			//'email'			=> $email,
			//'extra_fields'	=> $extra_fields,
			'status'		=> $status
		), 
		array( 
			//'%d', 
			'%s',
			'%s',
			'%d',
			'%d',
			//'%d',
			//'%s',
			'%s',
			'%d',
			//'%s',
			'%s',
			//'%s',
			//'%s',
			'%s'
		) 
	);

	$id_booking = $wpdb->insert_id;

	dpProEventCalendar_getMoreBookings(1, 0, $eventid);
	die();
}

add_action( 'wp_ajax_getMoreBookings', 'dpProEventCalendar_getMoreBookings' );
 
function dpProEventCalendar_getMoreBookings($limit = 30, $offset = '', $eventid = '', $event_date = '') {
	global $wpdb, $table_prefix, $dp_pec_payments, $dpProEventCalendar;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	if(isset($_POST['eventid'])) {
		$eventid = $_POST['eventid'];
	}
	if(isset($_POST['offset'])) {
		$offset = $_POST['offset'];
	}
	if(!is_numeric($limit)) {
		$limit = 30;
	}
	if(empty($event_date)) {
		$event_date = $_POST['event_date'];
	}
	
	$id_list = $eventid;
    if(function_exists('icl_object_id')) {
        global $sitepress;

        $id_list_arr = array();
		$trid = $sitepress->get_element_trid($eventid, 'post_pec-events');
		$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

		foreach($translation as $key) {
			$id_list_arr[] = $key->element_id;
		}

		if(!empty($id_list_arr)) {
			$id_list = implode(",", $id_list_arr);
		}
	}

	$querystr = "
    SELECT COUNT(*) as count
    FROM $table_name_booking
	WHERE id_event IN (".$id_list.")
	";
	if($event_date != "") {
		$querystr .= "
		AND event_date = '".$event_date."'";
	}
    $counter = $wpdb->get_row($querystr, OBJECT);

	$querystr = "
	SELECT *
	FROM $table_name_booking
	WHERE id_event IN (".$id_list.")
	";
	if($event_date != "") {
		$querystr .= "
		AND event_date = '".$event_date."'";
	}
	$querystr .= "
	ORDER BY id DESC
	LIMIT ".$offset.", ".$limit."
	";
	$bookings_obj = $wpdb->get_results($querystr, OBJECT);

	ob_start();
	foreach($bookings_obj as $booking) {
		if(is_numeric($booking->id_user) && $booking->id_user > 0) {
			$userdata = get_userdata($booking->id_user);
		} else {
			$userdata = new stdClass();
			$userdata->display_name = $booking->name;
			$userdata->user_email = $booking->email;	
		}
		?>
	<tr>
		<td width="200"><?php echo $userdata->display_name?> <br>
        	<?php if($userdata->user_email != "") {?>
        	<span class="dashicons dashicons-email-alt"></span><input type="text" readonly="readonly" name="pec_booking_email[<?php echo $booking->id?>]" class="pec_booking_text" value="<?php echo $userdata->user_email?>" /><br>
        	<?php }?>

        	<?php if($booking->phone != "") {?>
        	<span class="dashicons dashicons-phone"></span><input type="text" readonly="readonly" name="pec_booking_phone[<?php echo $booking->id?>]"  class="pec_booking_text" value="<?php echo $booking->phone?>" />
        	<?php }?>
        </td>
        <td><?php echo date_i18n(get_option('date_format') . ' '. get_option('time_format'), strtotime($booking->booking_date))?></td>
        <td><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></td>
        <td><?php echo $booking->quantity?></td>
        <td>
        	<?php if($booking->comment != "") {?>
        	<span class="dashicons dashicons-admin-comments"></span> <?php echo nl2br($booking->comment)?> <hr>
        	<?php }?>

        	<?php
        	$extra_fields = unserialize($booking->extra_fields);
        	if(!is_array($extra_fields)) 
        		$extra_fields = array();

        	$html = '';

        	foreach($extra_fields as $key=>$value) {

        		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
        		
        		if(is_array($field_index)) {
            		$field_index = $field_index[0];
            	} else {
            		$field_index = '';
            	}

        		if($value != "" && is_numeric($field_index)) {
        			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') {
        				$value = __('Yes', 'dpProEventCalendar');
        			}
					$html .= '<div class="pec_event_page_custom_fields">
								<strong>'.$dpProEventCalendar['booking_custom_fields']['name'][$field_index].': </strong>'.$value;
					$html .= '</div>';
				}

        	}
        	
        	echo $html;
			?>
		</td>
        <td>
        	<select name="pec_booking_status[<?php echo $booking->id?>]">
				<option value=""><?php echo __( 'Completed', 'dpProEventCalendar' )?></option>
                <option value="pending" <?php if($booking->status == "pending") {?> selected="selected" <?php }?>><?php echo __( 'Pending', 'dpProEventCalendar' )?></option>
                <option value="canceled_by_user" <?php if($booking->status == "canceled_by_user") {?> selected="selected" <?php }?>><?php echo __( 'Canceled By User', 'dpProEventCalendar' )?></option>
                <option value="canceled" <?php if($booking->status == "canceled") {?> selected="selected" <?php }?>><?php echo __( 'Canceled', 'dpProEventCalendar' )?></option>
            </select>
        </td>

		<td><input type="button" value="<?php echo __( 'Delete', 'dpProEventCalendar' )?>" name="delete_booking" class="button-primary" onclick="if(confirm('<?php echo __( 'Are you sure that you want to remove this booking?', 'dpProEventCalendar' )?>')) { pec_removeBooking(<?php echo $booking->id?>, this); }" /></td>
	</tr>
	<?php 
	}

	$page = ob_get_contents();
	ob_end_clean();
	$page = preg_replace('/\s+/', ' ', trim($page));


	$encode = json_encode(array("html" => $page, "counter" => $counter->count));

	print_r($encode);
	die();
			
}



function dpProEventCalendar_date_i18n($date, $timestamp = "") {
	if($timestamp == "") {
		$timestamp = time();	
	}
	$i18n = date_i18n($date, $timestamp);
	
	$i18n = str_replace("January", __('January', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("February", __('February', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("March", __('March', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("April", __('April', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("May", __('May', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("June", __('June', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("July", __('July', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("August", __('August', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("September", __('September', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("October", __('October', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("November", __('November', 'dpProEventCalendar'), $i18n);
	$i18n = str_replace("December", __('December', 'dpProEventCalendar'), $i18n);
	
	return $i18n;
}

function dpProEventCalendar_archive_template( $archive_template ) {
     global $post, $dpProEventCalendar;

     if ( is_post_type_archive ( 'pec-events' ) ) {
          //$archive_template = dirname( __FILE__ ) . '/post-type-template.php';
		  if($dpProEventCalendar['redirect_archive'] != "") {
			  wp_redirect($dpProEventCalendar['redirect_archive']);
			  die();
		  }
     }
     return $archive_template;
}

add_filter( 'archive_template', 'dpProEventCalendar_archive_template' ) ;

/*
Dashboard Widgets
*/

// Function that outputs the contents of the dashboard widget
function dpProEventCalendar_dashboard_widget_function( $post, $callback_args ) {
	global $wpdb, $table_prefix, $dpProEventCalendar;
	
	echo '<ul>';
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	$booking_count = 0;
	$querystr = "
	SELECT *
	FROM $table_name_booking
	ORDER BY id DESC
	LIMIT 10
	";
	$bookings_obj = $wpdb->get_results($querystr, OBJECT);
	foreach($bookings_obj as $booking) {
		if(is_numeric($booking->id_user) && $booking->id_user > 0) {
			$userdata = get_userdata($booking->id_user);
		} else {
			$userdata = new stdClass();
			$userdata->display_name = $booking->name;
			$userdata->user_email = $booking->email;	
		}
		
		if(get_the_title($booking->id_event) == '') 
			continue;
			
		echo '<li>The user <strong>' . $userdata->display_name . '</strong> has booked the event <a href="'.get_edit_post_link($booking->id_event).'">'.get_the_title($booking->id_event).'</a> ('.date(get_option('date_format'), strtotime($booking->event_date)).')</li>';
	}

	echo '</ul>';
}

// Function used in the action hook
function dpProEventCalendar_add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', __('Latest Event Bookings', 'dpProEventCalendar'), 'dpProEventCalendar_dashboard_widget_function');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'dpProEventCalendar_add_dashboard_widgets' );

function dpProEventCalendar_get_permalink($id) {

	if(function_exists('icl_object_id')) {
		$id = icl_object_id($id, get_post_type($id), true);
	}

	$use_link = get_post_meta($id, 'pec_use_link', true);
	$link = get_post_meta($id, 'pec_link', true);

	if($use_link && $link != "") {

		if(substr($link, 0, 4) != "http" && substr($link, 0, 4) != "mail") {
			$link = 'http://'.trim($link);
		}
		
		return trim($link);

	} else {

		return get_permalink($id);

	}
}

add_action('wp_footer', 'dpProEventCalendar_footerSingle');

function dpProEventCalendar_footerSingle() {
	global $wp_query;

	if(get_post_type() != 'pec-events') {
		return;
	}

	$id = get_the_ID();
	$url = get_post_meta($id, 'pec_link', true);
	$calendar = get_post_meta($id, 'pec_id_calendar', true);
	$title = get_the_title();
	$startDate = get_post_meta($id, 'pec_date', true);
	
	if(isset($wp_query->query_vars['event_date']) && $wp_query->query_vars['event_date'] != "") {
		$startDate = date('Y-m-d H:i:s', (int)$wp_query->query_vars['event_date']);
	} elseif(strtotime($startDate) < time()) {
		require_once('classes/base.class.php');

		$dpProEventCalendar = new DpProEventCalendar( false, $calendar, null, null, '', '', $id );

		$event_dates = $dpProEventCalendar->upcomingCalendarLayout( true, 1 );
		if(is_array($event_dates) && !empty($event_dates)) {
			$startDate = $event_dates[0]->date;
		}
	}
	$location = get_post_meta($id, 'pec_location', true);
	$age_range = get_post_meta($id, 'pec_age_range', true);
	$objDateTime = new DateTime($startDate);
	$isoDate = $objDateTime->format(DateTime::ISO8601);

	echo '<script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "Event",
	  "name": "'.addSlashes($title).'",
	  "startDate" : "'.$isoDate.'",
	  "url" : "'.$url.'",
	  "typicalAgeRange" : "'.addSlashes($age_range).'"';
	if($location != "") {
		$address = $location;
		$venue_name = $location;
		$venue_link = '';

		if(is_numeric($location)) {
			$address = get_post_meta($location, 'pec_venue_address', true);
			$venue_name = get_the_title($location);
			$venue_link = get_post_meta($location, 'pec_venue_link', true);
		}
	echo ',
	  "location" : {
	    "@type" : "Place",
	    "address" : "'.addSlashes($address).'",
	    "sameAs" : "'.addSlashes($venue_link).'",
	    "name" : "'.addSlashes($venue_name).'"
	  }';
	}
	echo '
	}
	</script>';
}

function dpProEventCalendar_getEventTimezone($event_id) {

	$event_timezone = get_post_meta($event_id, 'pec_timezone', true);
		
	if($event_timezone == "") {
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

		$event_timezone = $tzstring;
	}
	
	return $event_timezone;
}

function dpProEventCalendar_is_ampm() {

	if(strpos(get_option('time_format'), "A")  !== false || strpos(get_option('time_format'), "a")  !== false) {
		return true;
	} else {
		return false;
	}
}

function dpProEventCalendar_removeExpiredEvents() {
	global $dpProEventCalendar, $dpProEventCalendar_cache;

	$post_status = $dpProEventCalendar['remove_expired_status'];
	if($post_status == '') {
		$post_status = 'publish';
	}

	$expire_after = $dpProEventCalendar['remove_expired_days'];
	if($expire_after == '' || !is_numeric($expire_after) || $expire_after < 0) {
		$expire_after = 10;
	}

	$force_removal = false;
	if($dpProEventCalendar['remove_expired_completly'] == 'remove') {
		$force_removal = true;
	}

	$expired_events = array();

	// Get Events which end date is set and it is in the past
	$args = array( 
		'posts_per_page' => -1, 
		'post_type'		 => 'pec-events', 
		'post_status'    => $post_status,
		"meta_query" 	 => 
			array(
				array(
					'relation' => 'AND',
					array(
						'key'     => 'pec_end_date',
						'value'   => '',
						'compare' => '!='
					),
					array(
						'key'     => 'pec_end_date',
						'value'   => date('Y-m-d', strtotime(current_time( 'Y-m-d' ) . ' -'.$expire_after.' days')),
						'compare' => '<',
						'type'    => 'DATETIME'
					)
				)
			)
		);
	
	$expired_events = array_merge(get_posts( $args ), $expired_events);

	// Get events that end date is not set and the date is in the past and is not recurrent
	$args = array( 
		'posts_per_page' => -1, 
		'post_type'		 => 'pec-events', 
		'post_status'    => $post_status,
		"meta_query" 	 => 
			array(
				array(
					'relation' => 'AND',
					array(
						'key'     => 'pec_end_date',
						'value'   => '',
						'compare' => '='
					),
					array(
						'key'     => 'pec_date',
						'value'   => date('Y-m-d H:i:s', strtotime(current_time( 'Y-m-d H:i:s' ) . ' -'.$expire_after.' days')),
						'compare' => '<',
						'type'    => 'DATETIME'
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'pec_recurring_frecuency',
							'value'   => '0',
							'compare' => '='
						),
						array(
							'key'     => 'pec_recurring_frecuency',
							'value'   => '',
							'compare' => '='
						)
					)
				)
			)
		);
	
	$expired_events = array_merge(get_posts( $args ), $expired_events);

	//print_r($args);
	//echo count($expired_events).$post_status;
	if(is_array($expired_events)) {
		foreach($expired_events as $key) {
			//echo $key->ID . ' - ' . $key->post_title.' <strong>End Date: '.get_post_meta($key->ID, 'pec_end_date', true).'</strong> 
			//<strong>Frequency: '.get_post_meta($key->ID, 'pec_recurring_frecuency', true).'</strong><br>';
			
			wp_delete_post( $key->ID, $force_removal );
			
		}

		if(isset($dpProEventCalendar_cache)) {
		   $dpProEventCalendar_cache = array();
		   update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
	   }
	}

}

function dpProEventCalendar_setup_expired_events_cron() {
	global $dpProEventCalendar;

	if(isset($dpProEventCalendar['remove_expired_enable']) && $dpProEventCalendar['remove_expired_enable']) {
		
		if ( ! wp_next_scheduled( 'pecexpiredevents' ) ) {
		
			$scheduled = wp_schedule_event( time(), 'daily', 'pecexpiredevents');
		
		}
		
		add_action( 'pecexpiredevents', 'dpProEventCalendar_removeExpiredEvents', 10 );

	} else {

		// Unschedule
		
		// Get the timestamp for the next event.
	
		wp_clear_scheduled_hook( 'pecexpiredevents' );

	}
}

add_action( 'init', 'dpProEventCalendar_setup_expired_events_cron', 10 );

function dpProEventCalendar_getBookingInfo($booking_id) {
	global $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;

	$booking_count = 0;
    $querystr = "
    SELECT *
    FROM $table_name_booking
	WHERE id = ".$booking_id;

    $booking = $wpdb->get_row($querystr, OBJECT);

    return $booking;
	
}

function dpProEventCalendar_convertYoutube($string) {
    return preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<iframe src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
        $string
    );
}

function dpProEventCalendar_detectEmail($str)
{
    //Detect and create email
    $mail_pattern = "/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/";
    $str = preg_replace($mail_pattern, '<a href="mailto:$1$2">$1$2</a>', $str);

    return $str;
}

function dpProEventCalendar_trim_words($text, $limit) {
	$text = preg_replace('/\<[\/]{0,1}div[^\>]*\>|\<[\/]{0,1}span[^\>]*\>/i', '', $text);
	
	//$text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);

	$text = htmlentities($text);
  if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]) . '...';

      $start_tag = strrpos($text, "&lt;a href=&quot;");
      $end_tag = strrpos($text, "&gt;");
      //$text.= $start_tag."----".$end_tag;
      if($start_tag > $end_tag) {
      	$text .= "&quot;&gt;";
      }

      $start_tag = strrpos($text, "&lt;p style=&quot;");
      $end_tag = strrpos($text, "&gt;");
      //$text.= $start_tag."----".$end_tag;
      if($start_tag > $end_tag) {
      	$text .= "&quot;&gt;";
      }
  }
  return $text;
}

add_action( 'rest_api_init', 'dpProEventCalendar_register_rest' );
function dpProEventCalendar_register_rest() {
	if(function_exists('register_rest_field')) {
		
		register_rest_field( 'pec-venues',
	        'pec_venue_map_lnlat',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_date',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_venue_map_lnlat',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_featured_image',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_image_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_id_calendar',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_featured_event',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_all_day',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_tbc',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_daily_working_days',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_daily_every',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_weekly_every',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_weekly_day',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_monthly_every',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_monthly_position',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_monthly_day',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_exceptions',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_extra_dates',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_end_date',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_link',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_age_range',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_map',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_end_time_hh',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_end_time_mm',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_hide_time',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_organizer',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_phone',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_recurring_frecuency',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'pec-events',
	        'pec_location',
	        array(
	            'get_callback'    => 'dpProevEntCalendar_get_field_rest',
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	}
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function dpProevEntCalendar_get_field_rest( $object, $field_name, $request ) {
	if($field_name == "pec_venue_map_lnlat" && $object['type'] == 'pec-events') {
		$location_id = get_post_meta( $object[ 'id' ], 'pec_location', true );
		if(is_numeric($location_id)) {
			return get_post_meta( $location_id, $field_name, true );
		}
	}
    return get_post_meta( $object[ 'id' ], $field_name, true );
}

function dpProevEntCalendar_get_image_rest( $object, $field_name, $request ) {
	$post_thumbnail_id = get_post_thumbnail_id( $object[ 'id' ] );
	$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

	if(!empty($image_attributes)) {
	    return $image_attributes[0];
	} else {
		return '';
	}
}

function dpProEventCalendar_tz_offset_to_name($offset)
{
        $offset *= 3600; // convert hour offset to seconds
        $abbrarray = timezone_abbreviations_list();
        foreach ($abbrarray as $abbr)
        {
                foreach ($abbr as $city)
                {
                        if ($city['offset'] == $offset)
                        {
                                return $city['timezone_id'];
                        }
                }
        }

        return FALSE;
}

function pec_category_order( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( isset($query->query_vars['pec_events_category']) ) {
    	 
    	$query->set( 'orderby', 'date' );
    	$query->set( 'order', 'desc' );
    	print_r($query);
        return;
    }


}
//add_action( 'pre_get_posts', 'pec_category_order', 1 );

add_action('pec_events_category_edit_form_fields','pec_events_category_edit_form_fields');
add_action('edited_pec_events_category', 'pec_events_category_edit_save');
add_action('create_pec_events_category', 'pec_events_category_edit_save');
add_action('pec_events_category_add_form_fields','pec_events_category_edit_form_fields');


function pec_events_category_edit_save($term_id) {

	if ( isset( $_POST['color'] ) ) {
        update_term_meta($term_id, 'color', $_POST['color']);
    }
}

function dpProEventCalendar_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
	if( $request_type == 'GET' )
		$url .= '?' . http_build_query($data);
 
	$mch = curl_init();
	$headers = array(
		'Content-Type: application/json',
		'Authorization: Basic '.base64_encode( 'user:'. $api_key )
	);
	curl_setopt($mch, CURLOPT_URL, $url );
	curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
	curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
	curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
	curl_setopt($mch, CURLOPT_TIMEOUT, 10);
	curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
 
	if( $request_type != 'GET' ) {
		curl_setopt($mch, CURLOPT_POST, true);
		curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
	}
 
	return curl_exec($mch);
}

function pec_events_category_edit_form_fields () {
	global $wpdb, $table_prefix;

	$table_name_sd = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;

	$pec_color = "";

	if(isset($_GET['tag_ID']) && is_numeric($_GET['tag_ID'])) {
		$pec_color = get_term_meta($_GET['tag_ID'], 'color', true);
	}
?>
    <tr class="form-field">
            <th valign="top" scope="row">
                <label for="color"><?php _e('Color Code', 'dpProEventCalendar'); ?></label>
            </th>
            <td>
            	<select name="color">
		        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
		             <?php 
					$counter = 0;
					$querystr = "
					SELECT *
					FROM $table_name_sd 
					ORDER BY title ASC
					";
					$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
					foreach($sp_dates_obj as $sp_dates) {
					?>
		            
		            	<option value="<?php echo $sp_dates->id?>" <?php echo ($pec_color == $sp_dates->id ? 'selected="selected"' : '')?>><?php echo $sp_dates->title?></option>
		            
		            <?php }?>
		        </select>
		        <label class="dp_ui_pec_content_desc"><?php _e('Select a color. To create a new one, go to the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-special' ).'" target="_blank">special dates</a> section','dpProEventCalendar'); ?></label>
            </td>
        </tr>
    <?php 
}
/*
// Add to admin_init function   
add_filter("manage_pec_events_category_custom_column", 'pec_events_categories_columns', 10, 5);
 
function pec_events_categories_column($out, $column_name, $term_id) {
    //$theme = get_term($term_id, '');
    echo $column_name.'<br>';
    die();
    switch ($column_name) {
        case 'header_icon': 
            // get header image url
            $data = maybe_unserialize($theme->description);
            $out .= "<img src=\"{$data['HEADER_image']}\" width=\"250\" height=\"83\"/>"; 
            break;
 
        default:
            break;
    }
    return $out;    
}*/
?>