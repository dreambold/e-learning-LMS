<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$end = $now;
$number = $number && is_numeric($number) ? round($number, 0) : false;
switch($scope)
{
	case '24hrs':	
		$begin = date('Y-m-d H:i:s', strtotime($now.' - 24 hours'));
		break;
	case 'yesterday':
		$begin = date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00').' -1 day'));
		$end = date('Y-m-d H:i:s', strtotime(date('Y-m-d 23:59:59').' -1 day'));
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
$records = $wpdb->get_results($wpdb->prepare("SELECT file FROM ".fileaway_stats::$db." WHERE timestamp >= %s AND timestamp <= %s ORDER BY timestamp DESC", $begin, $end), ARRAY_A); 
if(!$records) return;
$files = array();
foreach($records as $k => $record)
{
	$files[$k] = $record['file'];
}
if($show == 'top')
{
	$i = 0;
	$filevals = array_count_values($files);
	natsort($filevals);
	$array = array_reverse($filevals);
	foreach($array as $key => $value)
	{
		if(!is_file($rootpath.$key)) continue;
		if($number && $i >= $number) break;
		$link = $url.'/'.$key;
		$slices = fileaway_utility::pathinfo($link);
		$extension = isset($slices['extension']) ? $slices['extension'] : false;
		$dir = str_replace($slices['basename'], '', $key);
		$exts[$i] = $extension;
		$locs[$i] = $slices['dirname']; 
		$fulls[$i] = $slices['basename']; 
		$rawnames[$i] = $slices['filename'];
		$links[$i] = fileaway_utility::urlesc($link);
		$dirs[$i] = $dir;
		$times[$i] = $mod != 'no' ? filemtime($dir.'/'.$slices['basename']) : time();
		$totals[$i] = 'x'.$value;
		$i++;
	}
}
elseif($show == 'recent')
{
	$i = 0;
	$array = array_values(array_unique($files));
	foreach($array as $key => $value)
	{
		if(!is_file($rootpath.$value)) continue;
		if($number && $i >= $number) break;
		$link = $url.'/'.$value;
		$slices = fileaway_utility::pathinfo($link);
		$extension = isset($slices['extension']) ? $slices['extension'] : false;
		$dir = str_replace($slices['basename'], '', $value);
		$exts[$i] = $extension;
		$locs[$i] = $slices['dirname']; 
		$fulls[$i] = $slices['basename']; 
		$rawnames[$i] = $slices['filename'];
		$links[$i] = fileaway_utility::urlesc($link);
		$dirs[$i] = $dir;
		$times[$i] = $mod != 'no' ? filemtime($dir.'/'.$slices['basename']) : time();
		$totals[$i] = false;
		$i++;
	}					
}
/*elseif($show == 'downloaders'){$uidvals = array_count_values($uids);natsort($uidvals);$array = array_reverse($uidvals);}*/
$count = 0;
if(!is_array($rawnames)) return;
foreach($rawnames as $k => $rawname)
{
	$link = $links[$k];
	$loc = $locs[$k]; 
	$ext = $exts[$k]; 
	$oext = $ext; 
	$extension = strtolower($ext); 
	$full = $fulls[$k]; 
	$dir = trim($dirs[$k], '/');
	$thetime = $times[$k];
	$file = $full;
	$total = $totals[$k];
	$iss2 = false;
	include fileaway_dir.'/lib/inc/inc.prettify.php';
	if($thename == '') continue;
	include fileaway_dir.'/lib/inc/inc.colors.php';
	$datemodified = $mod == 'yes' 
		? "<div class='ssfa-datemodified'>".sprintf(_x('Last modified %s at %s', 'For List Types: *Date* at *Time*', 'file-away'), $date, $time)."</div>" 
		: null;
	$listfilesize = $size != 'no' 
		? ($theme == 'ssfa-minimal-list' 
			? "<span class='ssfa-listfilesize'>($fsize)</span>" 
			: "<span class='ssfa-listfilesize'>$fsize</span>") 
		: null;
	if($s2mem && strpos($dir, 's2member-files') !== false)
	{
		$iss2 = true;
		$s2skip = $s2skipconfirm ? '&s2member_skip_confirmation' : '';	
		$sub = fileaway_utility::replacefirst($rootpath.$dir, WP_PLUGIN_DIR.'/s2member-files/', '');
		$link = $s2mem && strpos($dir, 's2member-files') !== false ? $url.'/?s2member_file_download='.$sub.$file.$s2skip : $link;
	}
	$link = $encryption && !$iss2 ? $encrypt->url($rootpath.$dir.'/'.$file) : $link;
	$fulllink = 'href="'.$link.'"';
	$statstatus = $stats ? 'true' : 'false';
	$fulllink = $redirect ? 'href="'.$this->op['redirect'].'"' : $fulllink;
	include fileaway_dir.'/lib/inc/inc.icons.php'; 
	$linktype = $iss2 || $encryption ? '' : $linktype;
	$linktype = $redirect ? 'target="_blank"' : $linktype;
	if($flightbox && !fileaway_utility::startswith($file, '_thumb_')) 
	include fileaway_dir.'/lib/inc/inc.flightbox.php';
	$thename = "<span class='ssfa-filename'>".fileaway_utility::strtotitle($thename)."</span>"; 
	$count++;
	$thefiles .= 
		"<a id='ssfa-$uid-$count' class='$display$noicons$colors' $fulllink $linktype data-stat='$statstatus'>".
			"<div class='ssfa-listitem $ellipsis'>".
				"<span class='ssfa-topline'>$icon $thename $listfilesize</span> ".
				"$total".
				"$datemodified".
			"</div>".
		"</a>"; 										
}