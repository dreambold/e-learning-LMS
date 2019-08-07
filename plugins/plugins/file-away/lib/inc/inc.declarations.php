<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$time_format = get_option('time_format');
$date_format = get_option('date_format');
$thefiles = null; 
$included = null; 
$excluded = null; 
$rawnames = null; 
$iconstyle = null; 
$icocol = null; 
$path = null; 
$start = null; 
$basename = null; 
$dir = null; 
$basebase = null; 
$fafl = null; 
$faui = null; 
$faun = null; 
$faur = null; 
$direxcluded = 0; 
$getthumb = false; 
$playaway = false;
$attachaway = false;
$boximages = array();
$crumbies = null;
$iss2 = false;
$fb = 0;
global $is_IE, $is_safari;
$flightbox = $get->is_mobile ? false : $flightbox;
$nolinksbox = $nolinksbox ? 'true' : 'false';
$allbanners = array();
if($encryption)
{
	$s2mem = false;
	$encrypt = new fileaway_encrypted;
}
if($width == '100' && $perpx == '%') $align = 'none';
$clearfix = $align == 'none' ? "<div class='ssfa-clearfix'></div>" : null;
$rsslink = null;
$getrss = false;