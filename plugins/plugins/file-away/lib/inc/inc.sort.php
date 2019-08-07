<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$limit = $limit && is_numeric($limit) ? round($limit, 0) : false;
if(!$limit)
{
	if(phpversion() < '5.4') array_multisort($rawnames, SORT_ASC, SORT_STRING, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
	else array_multisort($rawnames, SORT_ASC, SORT_NATURAL, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
}
elseif($limitby == 'random')
{
	$i = 0;
	$keys = array_keys($rawnames); 
	shuffle($keys); 
	$rawrandom = array(); 
	foreach($keys as $key)
	{
		if($i > $limit) break;
		$rawrandom[$key] = $rawnames[$key]; 
		$i++;
	}
	$rawnames = array_intersect_key($rawrandom, $rawnames);	
}
elseif($limitby == 'mostrecent')
{
	array_multisort($times, SORT_DESC, SORT_NUMERIC, $rawnames, $links, $locs, $exts, $fulls, $dirs, $dynamics);
	array_splice($rawnames, $limit);
}
elseif($limitby == 'oldest')
{
	array_multisort($times, SORT_ASC, SORT_NUMERIC, $rawnames, $links, $locs, $exts, $fulls, $dirs, $dynamics);
	array_splice($rawnames, $limit);
}
elseif($limitby == 'alpha')
{
	if(phpversion() < '5.4') array_multisort($rawnames, SORT_ASC, SORT_STRING, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
	else array_multisort($rawnames, SORT_ASC, SORT_NATURAL, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
	array_splice($rawnames, $limit);
}
elseif($limitby == 'alpha-desc')
{
	if(phpversion() < '5.4') array_multisort($rawnames, SORT_DESC, SORT_STRING, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
	else array_multisort($rawnames, SORT_DESC, SORT_NATURAL, $links, $locs, $exts, $fulls, $dirs, $times, $dynamics); 
	array_splice($rawnames, $limit);
}