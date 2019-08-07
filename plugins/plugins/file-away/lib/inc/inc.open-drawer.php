<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$basecheck = trim($dir,'/');
if(strpos($basecheck, '/') !== false)
{
	$subbase = strrchr($basecheck, "/"); 
	$basebase = str_replace($subbase, '', $basecheck); 
}
else
{ 
	$basebase = $basecheck;
	$subbase = $basebase;
}
if(isset($_REQUEST['drawer'.$drawerid]))
{ 
	$rawdrawer = $_GET['drawer'.$drawerid];
	$aposdrawer = fileaway_utility::stripslashes($rawdrawer);
	if($aposdrawer === "/") $aposdrawer = trim($start, '/');
	$dir = $basebase."/".$aposdrawer; 
	$dir = str_replace('*', '/', $dir);
	if($rawdrawer === '') $dir = $start;
	if(!is_dir($dir)) $dir = $start;
	if(strpos($dir, '..') !== false) $dir = $start;
	if(!fileaway_utility::realpath($dir,$rootpath,$chosenpath)) $dir = $start;
	if(strpos($dir, trim($subbase, '/')) === false) $dir = $start; // experimental
}
if($private_content)
{
	if($fa_firstlast_used && stripos($dir, $fa_firstlast) === false) $dir = $start; 
	if($fa_userid_used && strpos($dir, $fa_userid) === false) $dir = $start;
	if($fa_username_used && stripos($dir, $fa_username) === false) $dir = $start; 
	if($fa_userrole_used && stripos($dir, $fa_userrole) === false) $dir = $start; 
	if($fa_usermeta_used && is_array($fa_metavalues))
	{ 
		foreach($fa_metavalues as $mv)
		{
			if(stripos($dir, $mv) === false) $dir = $start; 
		}
	}
}