<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$baselessdir = fileaway_utility::replacefirst($dir, $basebase, '');
if($basebase !== $basecheck) $crumbs = explode('/', ltrim($baselessdir, '/'));
else $crumbs = explode('/', trim($dir, '/'));		
if(!is_array($crumbs)) $crumbs = array();
$crumblink = array();
$addclass = !$heading ? ' ssfa-crumbs-noheading' : null;
$crumbies = '<div class="ssfa-clearfix ssfa-crumbs'.$addclass.'">'.$rsslink;
$rsslink = null;
foreach($crumbs as $k => $crumb)
{
	$prettycrumb = $crumb;
	$crumblink[$k] = '';
	if(!$prettify)
	{
		$prettycrumb = str_replace(array('~', '--', '_', '.', '*'), ' ', $prettycrumb); 
		$prettycrumb = preg_replace('/(?<=\D)-(?=\D)/', ' ', $prettycrumb);
		$prettycrumb = preg_replace('/(?<=\d)-(?=\D)/', ' ', $prettycrumb);
		$prettycrumb = preg_replace('/(?<=\D)-(?=\d)/', ' ', $prettycrumb);
		$prettycrumb = fileaway_utility::strtotitle($prettycrumb);	
	}
	if($crumb !== '')
	{
		$i = 0; 
		while($i <= $k)
		{ 
			if ($i == 0) $comma = ''; 
			else $comma = "*"; 
			$crumblink[$k] .= $comma.$crumbs[$i]; 
			$i++; 
		}
		if($basebase === $basecheck) $crumblink[$k] = ltrim(fileaway_utility::replacefirst($crumblink[$k], $basebase, ''), '*');
		if(!empty($parentlabel)) $parentlabel = trim($parentlabel);
		if(empty($k) && !empty($parentlabel)) $prettycrumb = $parentlabel;
		$crumbies .= '<a href="'.fileaway_utility::querystring(get_permalink(), $_SERVER["QUERY_STRING"], array("drawer".$drawerid => $crumblink[$k])).'">'.$prettycrumb.'</a> / ';
	}
}
$crumbies .= "</div>";