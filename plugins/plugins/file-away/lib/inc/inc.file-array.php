<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(is_array($files))
{
	$now = time();
	foreach($files as $file)
	{
		$file = str_replace('\\','/',$file);
		$link = $recursive ? $url.'/'.$file : $url.'/'.$dir.'/'.$file; 
		$slices = fileaway_utility::pathinfo($link); 
		$extension = isset($slices['extension']) ? $slices['extension'] : false;
		include fileaway_dir.'/lib/inc/inc.filters.php'; 
		$dir = $recursive ? str_replace($slices['basename'], '', $file) : $dir;
		$dir = rtrim($dir, '/');
		if(!$excluded && is_readable($dir.'/'.$slices['basename']))
		{
			$exts[] = $extension;
			$locs[] = $slices['dirname']; 
			$fulls[] = $slices['basename']; 
			$rawnames[] = $slices['filename'];
			$links[] = fileaway_utility::urlesc($link);
			$dirs[] = $dir;
			$times[] = $mod != 'no' ? filemtime($dir.'/'.$slices['basename']) : $now;
			$dynamics[] = false;
			$bannerads[] = false;
		}
	}
}