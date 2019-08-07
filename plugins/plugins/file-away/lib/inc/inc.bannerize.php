<?php 
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$bannerfile = 'fileaway-banner-parser.csv';
if(isset($this->op['banner_directory'])) $bannerpath = rtrim($this->op['banner_directory'], '/');
else return;
if(!is_dir($rootpath.$bannerpath) || !is_file($rootpath.$bannerpath.'/'.$bannerfile) || !is_readable($rootpath.$bannerpath.'/'.$bannerfile)) return;
ini_set('auto_detect_line_endings', TRUE);
$banner_csv = $rootpath.$bannerpath.'/'.$bannerfile;
$header = NULL;
$banners = array();
$now = time();
if(($handle = fopen($banner_csv, 'r')) !== FALSE)
{
	while(($row = fgetcsv($handle, 0, ',')) !== FALSE)
	{
		if(!$header) $header = $row;
		else
		{
			if(count($header) > count($row))
			{
				$difference = count($header)-count($row);
				for($i = 1; $i <= $difference; $i++)
				{
					$row[count($row) + 1] = ',';
				}
			}
			$banners[] = array_combine($header, $row);
		}	
	}
	fclose($handle);
}
foreach($banners as $banner)
{
	if(isset($banner['URL']) && (preg_match('/[a-z]/i', $banner['URL']) || preg_match('/\d/', $banner['URL']))
	&& isset($banner['FILENAME']) && is_file($rootpath.$bannerpath.'/'.$banner['FILENAME']))
	{	
		$image = $url.'/'.$bannerpath.'/'.$banner['FILENAME'];
		$slices = fileaway_utility::pathinfo($image); 
		$extension = isset($slices['extension']) ? $slices['extension'] : false;
		$allbanners['exts'][] = $extension;
		$allbanners['locs'][] = $slices['dirname']; 
		$allbanners['fulls'][] = $slices['basename']; 
		$allbanners['rawnames'][] = $slices['filename'];
		$allbanners['links'][] = fileaway_utility::urlesc($banner['URL']);
		$allbanners['dirs'][] = $bannerpath;
		$allbanners['times'][] = $now;
		$allbanners['dynamics'][] = false;
		$allbanners['bannerads'][] = true;
	}	
}
if(isset($allbanners['rawnames']) && count($allbanners['rawnames'] > 0))
{
	$numbanners = $fcount < $bannerize ? 1 : (int)round($fcount / $bannerize, 0);
	if($numbanners > count($allbanners['rawnames'])) $numbanners = count($allbanners['rawnames']);
	$getbanners = array_rand($allbanners['rawnames'], $numbanners);
	$inc = $bannerize; 
	$rement = $inc;
	if(is_array($getbanners))
	{
		foreach($getbanners as $b)
		{
			array_splice($exts, $inc, 0, $allbanners['exts'][$b]);
			array_splice($locs, $inc, 0, $allbanners['locs'][$b]);
			array_splice($fulls, $inc, 0, $allbanners['fulls'][$b]);
			array_splice($rawnames, $inc, 0, $allbanners['rawnames'][$b]);
			array_splice($links, $inc, 0, $allbanners['links'][$b]);
			array_splice($dirs, $inc, 0, $allbanners['dirs'][$b]);
			array_splice($times, $inc, 0, $allbanners['times'][$b]);
			array_splice($dynamics, $inc, 0, $allbanners['dynamics'][$b]);
			array_splice($bannerads, $inc, 0, $allbanners['bannerads'][$b]);	
			$inc = $inc+$rement+1;
		}
	}
}