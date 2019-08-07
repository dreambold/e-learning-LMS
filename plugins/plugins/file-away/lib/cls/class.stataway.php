<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('stataway'))
{
	class stataway extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('stataway', array($this, 'sc'));
			add_shortcode('stataway_user', array($this, 'userstats'));
		}
		public function sc($atts)
		{
			$get = new fileaway_definitions;
			extract($get->pathoptions);
			extract($this->correctatts(wp_parse_args($atts, $this->stataway), $this->shortcodes['stataway'], 'stataway'));
			if($devices == 'mobile' && !$get->is_mobile) return;
			elseif($devices == 'desktop' && $get->is_mobile) return;
			if($type == 'table' && !$showto && !$hidefrom && !current_user_can('administrator')) return;
			if(!fileaway_utility::visibility($hidefrom, $showto)) return;
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			include fileaway_dir.'/lib/inc/inc.stataway-declarations.php';
			include fileaway_dir.'/lib/inc/inc.declarations.php';
			include fileaway_dir.'/lib/inc/inc.styles.php'; 
			$fadeit = $fadein ? ($fadein == 'opacity' ? 'opacity:0;' : 'display:none;') : null;
			if($fadein)
			{
				$fadescript = $fadein == 'opacity' ? '.animate({opacity:"1"}, '.$fadetime.');' : '.fadeIn('.$fadetime.');';
				$thefiles .= '<script> jQuery(document).ready(function($){ setTimeout(function(){ $("div#'.$name.'")'.$fadescript.' }, 1000); }); </script>';
			}
			$mobileclass = $get->is_mobile ? 'ssfa-mobile' : null;
			$thefiles .= "$clearfix<div id='$name' class='ssfa-meta-container $mobileclass $class' data-uid='$uid' style='margin: 10px 0 20px; $fadeit $howshouldiputit'>";
			include fileaway_dir.'/lib/inc/inc.stats-redirects.php';
			if($type == 'table') include fileaway_dir.'/lib/inc/inc.stataway-range.php';
			include fileaway_dir.'/lib/inc/inc.precontent.php';			
			if($type != 'table') include fileaway_dir.'/lib/inc/inc.stataway-lists.php';
			else include fileaway_dir.'/lib/inc/inc.stataway-tables.php';
			$thefiles .= "</div></div>$clearfix";	
			if($flightbox && $fb) 
			{
				$thefiles .= '<script>FlightBoxes['.$uid.'] = '.$fb.'; ';
				if(count($boximages) > 0) $thefiles .= implode(' ', $boximages);
				$thefiles .= '</script>';
			}			
			date_default_timezone_set($original_timezone);
			return $thefiles;
		}
		public function userstats($atts)
		{
			if(!is_user_logged_in()) return;
			extract($this->correct(wp_parse_args($atts, $this->stataway_user), $this->shortcodes['stataway_user']));
			global $wpdb;
			$userid = $user && is_numeric($user) ? $user : get_current_user_id();
			if(!get_userdata($userid)) return false;
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			$now = date('Y-m-d H:i:s');
			switch($scope)
			{
				case '24hrs':	
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 24 hours'));
					break;
				case 'week':
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 1 week'));
					break;
				case 'twoweeks':
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 2 weeks'));
					break;
				case 'month':
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 30 days'));
					break;
				case 'year':
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 1 year'));
					break;
				case 'all':
					$begin = '1900-01-01 00:00:00';
					break;
				default:
					$begin = date('Y-m-d H:i:s', strtotime($now.' - 1 week'));
			}
			$end = $now;
			$records = $wpdb->get_results(
				$wpdb->prepare("SELECT file, timestamp FROM ".fileaway_stats::$db." WHERE uid = %d AND timestamp >= %s AND timestamp <= %s ORDER BY timestamp DESC", 
					$userid, $begin, $end
				), ARRAY_A
			);
			if(!$records || count($records) < 1) $count = 0;
			else $count = count($records);
			date_default_timezone_set($original_timezone);
			if($output == 'total') return '<span class="'.$class.'">'.$count.'</span>';
			if($count < 1) return false;
			$datestring = $this->op['daymonth'] == 'md' ? 'm/d/Y' : 'd/m/Y'; 
			$items = array();
			foreach($records as $i => $record)
			{
				$items[$i] = '<li>'.fileaway_utility::basename($record['file']);
				if($timestamp == 'yes') $items[$i] .= ' <span style="display:block;" class="'.$class.'timestamp">'.
					date($datestring.' '.get_option('time_format'), strtotime($record['timestamp'])).'</span>';
				$items[$i] .= '</li>';
			}
			return '<div class="'.$class.'"><'.$output.'>'.implode($items).'</'.$output.'></div>';
		}		
	}
}