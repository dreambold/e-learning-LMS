<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if($iconcolor) $icocol = " ssfa-$iconcolor"; 
if($color && !$accent)
{ 
	$accent = $color; 
	$colors = " ssfa-$color accent-$accent"; 
}
if($color && $accent) $colors = " ssfa-$color accent-$accent"; 
if(($color) && !($iconcolor))
{ 
	$useIconColor = $randcolor[array_rand($randcolor)]; 
	$icocol = " ssfa-$useIconColor";
}
if(!($color) && ($iconcolor))
{ 
	$useColor = $randcolor[array_rand($randcolor)]; 
	$colors = " ssfa-$useColor accent-$useColor";
}
if(!($color) && !($iconcolor))
{ 
	$useColor = $randcolor[array_rand($randcolor)]; 
	$colors = " ssfa-$useColor accent-$useColor"; 
	$icocol = " ssfa-$useColor";
}