<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(isset($_GET['fsb']) && isset($_GET['fse']))
{
	$begin = $_GET['fsb'].' 00:00:00';
	$end = $_GET['fse'].' 23:59:59';
}
else
{
	$begin = date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00').' -1 week'));
	$end = $now;
}
wp_enqueue_script('jquery-ui-datepicker');
$thefiles .= '<link href="'.fileaway_url.'/lib/css/datepicker.css" rel="stylesheet">';
$thefiles .= 
	'<script>jQuery(document).ready(function($){ '.
		'$("#stataway-fsb, #stataway-fse")'.
			'.datepicker({dateFormat:"yy-mm-dd", autoSize:true, maxDate:0}).datepicker("widget").wrap("<div class=\'stataway-datepicker\'/>"); '.
	'}); </script>';
$window = fileaway_utility::querystring(get_permalink(), $_SERVER["QUERY_STRING"], array('fsb','fse'), true);
$begin_input = '<input type="text" id="stataway-fsb" name="stataway-fsb" class="stataway-datepicker" value="'.date('Y-m-d', strtotime($begin)).'"/>';
$end_input = '<input type="text" id="stataway-fse" name="stataway-fse" class="stataway-datepicker" value="'.date('Y-m-d', strtotime($end)).'"/>';
$heading = 
	"<div class='stataway-datepicker-area'>".
		$begin_input." &#8674; ".$end_input." ".
		"<span id='stataway-refresh-$uid' style='cursor:pointer' data-url=\"".$window."\">&#8635;</span>".
	"</div>";