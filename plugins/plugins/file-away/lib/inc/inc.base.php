<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$url = str_replace('\\','/',$this->op['baseurl']); 
$s2mem = fileaway_definitions::$s2member && ($base == 's2member-files' || stripos($this->op['base'.$base], 'plugins/s2member-files') !== false) ? true : false;
$base = $s2mem ? str_replace('\\','/',fileaway_utility::replacefirst(WP_PLUGIN_DIR.'/s2member-files', $chosenpath, '')) : str_replace('\\','/',$this->op['base'.$base]);
$base = trim(str_replace('\\','/',$base), '/'); 
$base = trim(str_replace('\\','/',$base), '/');
if($base == '' || $base == null) 
{
	echo 'Your Base Directory is not set.'; 
	return 2;
}
$sub = $sub ? trim(str_replace('\\','/',$sub), '/') : false;
$dir = $sub ? $base.'/'.$sub : $base;
$dir = str_replace('//', '/', "$dir");
$dir = $problemchild ? $install.$dir : $dir;
$plabackpath = $playback ? str_replace('\\','/',$playbackpath) : false;
if($s2mem)
{
	$iss2 = true;
	$s2skip = $s2skipconfirm ? '&s2member_skip_confirmation' : '';	
}