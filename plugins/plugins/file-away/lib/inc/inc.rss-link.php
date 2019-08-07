<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if($type == 'table' && !$recursive && isset($this->op['feeds']) && $this->op['feeds'] && trim($this->op['feeds']) != '')
{
	$getrss = true;
	if(is_file($rootpath.$dir.'/_fa.feed.id.ini'))
	{
		$ini = parse_ini_file($rootpath.$dir.'/_fa.feed.id.ini');
		$feedid = $ini['id'];
		$feedfile = $rootpath.trim($this->op['feeds'], '/').'/_feed_'.$feedid.'.xml';
		if(is_file($feedfile))
		{
			$feedurl = fileaway_utility::replacefirst($feedfile, $rootpath, rtrim($this->op['baseurl'], '/').'/');
			$rsslink = '<a href="'.$feedurl.'" target="_blank" class="ssfa-rsslink"><span class="ssfa-icon-feed ssfa-rsslink"></span></a>';
		}
	}
}