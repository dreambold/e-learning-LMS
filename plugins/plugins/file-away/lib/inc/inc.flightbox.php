<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$boxquery = false;
$s2 = $iss2 ? ($s2skipconfirm ? 'skip' : 'true') : 'false'; 
if($attachaway)
{
	$boxlink = $link;
	$boximage = $link;
}
else
{
	$boxlink = $links[$k];
	$boximage = $links[$k];
}
if(($flightbox == 'multi' || $flightbox == 'images') && in_array(strtolower($extension), array('jpg', 'jpeg', 'png', 'gif')))
{
	$imgsz = $attachaway 
		? getimagesize(fileaway_utility::urlesc($link))
		: getimagesize($rootpath.$dir.'/'.$file);
	$imgwdth = $imgsz[0]; 
	$imghght = $imgsz[1];
	$dimension = $imgwdth >= $imghght ? 'width' : 'height';
	$boxquery = '?boxtype=image&g='.$guest.'&s='.$statstatus.'&s2='.$s2.'&d='.$dimension.'&w='.$imgwdth.'&h='.$imghght.'&mw='.$maximgwidth.'&mh='.$maximgheight;
	$boximages[] = 'jQuery(\'<img src="'.$boximage.'"/>\'); ';
}
elseif(($flightbox == 'multi' || $flightbox == 'videos') && in_array(strtolower($extension), array('flv', 'mp4', 'm4v', 'webm', 'ogv', 'tube', 'vmeo')))
	$boxquery = '?boxtype=video&g='.$guest.'&s='.$statstatus.'&s2='.$s2.'&e='.$extension.'&w='.$videowidth;
elseif(($flightbox == 'multi' || $flightbox == 'pdfs') && strtolower($extension) == 'pdf' && !$get->is_opera)
	$boxquery = '?boxtype=pdf&g='.$guest.'&s='.$statstatus.'&s2='.$s2.'&r=wide';
if($boxquery) 
{
	$statstatus = 'false';
	$fulllink = $manager 
		? 'href="'.$boxlink.$boxquery.'" onclick="return flightbox(this.href, \''.$uid.'-'.($fb+1).'\', \'ssfa-'.$boxtheme.'\', \''.$icocol.'\', \''.$nolinksbox.'\');" '.
			'data-flightbox="'.($fb+1).'"'
		: 'href="javascript:" onclick="flightbox(\''.$boxlink.$boxquery.'\', \''.$uid.'-'.($fb+1).'\', \'ssfa-'.$boxtheme.'\', \''.$icocol.'\', \''.$nolinksbox.'\');" '.
			'data-flightbox="'.($fb+1).'"';
	$linktype = '';
	$fb++;
}