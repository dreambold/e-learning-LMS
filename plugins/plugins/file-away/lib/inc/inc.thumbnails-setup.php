<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$thumbnails = $thumbnails && $type == 'table' && extension_loaded('gd') && function_exists('gd_info') ? $thumbnails : false;
if($thumbnails)
{ 
	$graythumbs = $graythumbs ? ' ssfa-thumb-bw' : '';
	if($thumbsize == 'large')
	{
		$thumbwidth = in_array($thumbstyle, array('widerounded','widesharp','oval')) ? 180 : 120; 
		$thumbheight = 120; 
		$thumbsizeclass = 'ssfa-thumb-lrg';
		$thumbfix = $thumbwidth == 180 ? 'lrg_wd_' : 'lrg_sq_';
		$playoffset = $thumbwidth == 120 ? 45 : 75;
		$playsize = 'border-top: 15px solid transparent; border-left: 30px solid silver; border-bottom: 15px solid transparent;';
		$playbordersize = 'border-top: 17px solid transparent; border-left: 34px solid #555; border-bottom: 17px solid transparent;';
		$playoverlay = '<div class="ssfa-play-overlay-border" style="'.$playbordersize.' left:'.($playoffset-1).'px; top:44px;"></div>'.
			'<div class="ssfa-play-overlay" style="'.$playsize.' left:'.$playoffset.'px; top:45px;"></div>';
	}
	elseif($thumbsize == 'medium')
	{
		$thumbwidth = in_array($thumbstyle, array('widerounded','widesharp','oval')) ? 120 : 80; 
		$thumbheight = 80; 
		$thumbsizeclass = 'ssfa-thumb-med';
		$thumbfix = $thumbwidth == 120 ? 'med_wd_' : 'med_sq_';
		$playoffset = $thumbwidth == 80 ? 30 : 50;
		$playsize = 'border-top: 10px solid transparent; border-left: 20px solid silver; border-bottom: 10px solid transparent;';
		$playbordersize = 'border-top: 12px solid transparent; border-left: 24px solid #555; border-bottom: 12px solid transparent;';
		$playoverlay = '<div class="ssfa-play-overlay-border" style="'.$playbordersize.' left:'.($playoffset-1).'px; top:29px;"></div>'.
			'<div class="ssfa-play-overlay" style="'.$playsize.' left:'.$playoffset.'px; top:30px;"></div>';		
	}
	else
	{
		$thumbwidth = in_array($thumbstyle, array('widerounded','widesharp','oval')) ? 60 : 40; 
		$thumbheight = 40; 
		$thumbsizeclass = 'ssfa-thumb-sm';
		$thumbfix = $thumbwidth == 60 ? 'wd_' : 'sq_';
		$playoffset = $thumbwidth == 40 ? 10 : 20;
		$playsize = 'border-top: 10px solid transparent; border-left: 20px solid silver; border-bottom: 10px solid transparent;';
		$playbordersize = 'border-top: 12px solid transparent; border-left: 24px solid #555; border-bottom: 12px solid transparent;';
		$playoverlay = '<div class="ssfa-play-overlay-border" style="'.$playbordersize.' left:'.($playoffset-1).'px; top:9px;"></div>'.
			'<div class="ssfa-play-overlay" style="'.$playsize.' left:'.$playoffset.'px; top:10px;"></div>';
	}
	if($thumbnails !== 'permanent')
	{
		$maxsrcbytes = preg_replace('/[^\\d.]+/', '', $maxsrcbytes);
		$maxsrcwidth = preg_replace('/[^\\d.]+/', '', $maxsrcwidth);
		$maxsrcheight = preg_replace('/[^\\d.]+/', '', $maxsrcheight);
	}
}