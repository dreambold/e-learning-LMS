<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(preg_match('/\[([^\]]+)\]/', $rawname))
{
	$file_plus_custom = $rawname;
	list($salvaged_filename, $customvalue) = preg_split("/[\[\]]/", $file_plus_custom);
	if($customvalue != '' && !$prettify)
	{
		$customvalue = str_replace(array('~', '--', '_', '.', '*'), ' ', $customvalue);
		$customvalue = preg_replace('/(?<=\D)-(?=\D)/', ' ', "$customvalue");
		$customvalue = preg_replace('/(?<=\d)-(?=\D)/', ' ', "$customvalue");
		$customvalue = preg_replace('/(?<=\D)-(?=\d)/', ' ', "$customvalue");	
	}
	$thename = $prettify ? $salvaged_filename : str_replace(array('~', '--', '_', '.', '*'), ' ', $salvaged_filename); 
}	
else
{ 
	$file_plus_custom = null; 
	$customvalue = null; 
	$thename = $prettify ? $rawname : str_replace(array('~', '--', '_', '.', '*'), ' ', $rawname); 
	$salvaged_filename = $rawname;
}
if(!$prettify)
{
	$thename = preg_replace('/(?<=\D)-(?=\D)/', ' ', "$thename"); 
	$thename = preg_replace('/(?<=\d)-(?=\D)/', ' ', "$thename"); 
	$thename = preg_replace('/(?<=\D)-(?=\d)/', ' ', "$thename"); 
}
$ext = !$ext ? '?' : $ext; 
$ext = substr($ext,0,4);
$bytes = $dynamiclink || $size == 'no' ? 1 : filesize($dir.'/'.$file); 
if($size != 'no')
{ 
	$fsize = fileaway_utility::formatBytes($bytes, 1); 
	$fsize = (!preg_match('/[a-z]/i', $fsize) ? '1k' : ($fsize === 'NAN' ? '0' : $fsize));
}
$sortdatekey = date("YmdHis", $thetime); 
$sortdate = $this->op['daymonth'] === 'dm' ? date("d/m/Y ".$time_format, $thetime) : date($time_format." m/d/Y", $thetime);
$date = date($date_format, $thetime); 
$time = date($time_format, $thetime);