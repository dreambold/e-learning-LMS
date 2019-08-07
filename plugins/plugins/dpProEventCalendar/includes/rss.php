<?php

//Include Configuration
require_once (dirname (__FILE__) . '/../../../../wp-load.php');
require_once(dirname (__FILE__) . '/../classes/base.class.php');

global $dpProEventCalendar, $wpdb, $table_prefix;

if(!is_numeric($_GET['calendar_id']) || $_GET['calendar_id'] <= 0) { 
	die(); 
}

$calendar_id = $_GET['calendar_id'];
if( isset($_GET['all_events']) && $_GET['all_events'] == 1) {
	$all_events = 1;
}


$dpProEventCalendar_class = new DpProEventCalendar( false, $calendar_id, null, null, '', '', '', '', "", "", "", "", 0, array("include_all_events" => $all_events)  );

if(!$dpProEventCalendar_class->calendar_obj->rss_active) 
	die();
	
$limit = $dpProEventCalendar_class->calendar_obj->rss_limit;
if( !is_numeric($limit) || $limit <= 0 ) {
	$limit = 99;	
}
$cal_events = $dpProEventCalendar_class->upcomingCalendarLayout( true, $limit, '', null, null, true, false, true, false, false, '', false );
$blog_desc = ent2ncr(convert_chars(strip_tags(get_bloginfo()))) . " - " . __('Calendar','dpProEventCalendar');

//timezone
$tz = get_option('timezone_string'); // get current PHP timezone
$gmt_offset = get_option('gmt_offset');
$minutes_offset = "0";
if($gmt_offset != "") {
	$minutes_offset = floor($gmt_offset * 60);
	if($minutes_offset < 0) {
		$minutes_offset = "+".str_replace("-", "", $minutes_offset);
	} else {
		$minutes_offset = "-".$minutes_offset;
	}
}

if($tz == "") {
	$tz = date_default_timezone_get();	
}
function timezoneDoesDST($tzId, $time = "") {
	if(class_exists('DateTimeZone') && $tzId != "") {
		$tz = new DateTimeZone($tzId);
		$date = new DateTime($time != "" ? $time : "now",$tz);  
		$trans = $tz->getTransitions();
		foreach ($trans as $k => $t) 
		{
			if ($t["ts"] > $date->format('U')) {
				  return $trans[$k-1]['isdst'];    
			}
		}
	} else {
		return false;	
	}
}

date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress

$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>'.$blog_desc.'</title>
<link>'.home_url().'</link>
<atom:link type="application/rss+xml" href="https://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'" rel="self"/>
<description>'.$blog_desc.'</description>
<language>en-us</language>
<ttl>40</ttl>';

if(is_array($cal_events)) {
	foreach ( $cal_events as $event ) {
		if($event->id == "") 
			$event->id = $event->ID;
		
		$event = (object)array_merge((array)$dpProEventCalendar_class->getEventData($event->id), (array)$event);
		
		
								
		if ( get_option('permalink_structure') ) {
			$link = rtrim(get_permalink($event->id), '/').'/'.strtotime($event->date);
		} else {
			$link = get_permalink($event->id).(strpos(get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date);
		}

		if(get_post_meta($event->id, 'pec_use_link', true) && get_post_meta($event->id, 'pec_link', true) != "") {
			$link = get_post_meta($event->id, 'pec_link', true);
		}

		$post_thumbnail_id = get_post_thumbnail_id( $event->id );
		if(is_numeric($post_thumbnail_id)) {
			$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				
			$event->description = '<img src="'.$image_attributes[0].'" alt="" />'.$event->description;
		}
		$rssfeed .= '
		<item>
		<title><![CDATA[' . $event->title . ']]></title>
		<description><![CDATA[' . $event->description . ']]></description>
		<link>'.$link .'</link>
		<guid>'.$link .'</guid>
		<pubDate>' . date("D, d M Y H:i:s O", strtotime($event->date)) . '</pubDate>
		</item>';
	}
}
$rssfeed .= '
</channel>
</rss>';

//date_default_timezone_set($tz); // set the PHP timezone back the way it was
header("Content-Type: application/rss+xml; charset=UTF-8");
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $rssfeed;