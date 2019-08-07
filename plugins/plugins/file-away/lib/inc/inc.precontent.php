<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$thefiles .= $type === 'table' 
	? "<div id='ssfa-table-wrap-$uid' class='$theme'>" 
	: "<div id='ssfa-list-wrap-$uid' class='$theme$corners$bordercolor'>";
if($searchlabel) $searchlabel = " $searchlabel";
$searchclass = $searchlabel ? 'ssfa-search-label-active' : '';
$thefiles .= '<div class="ssfa-clearfix" style="margin-bottom:10px!important;">';
if($heading)
{
	$hcolor = $hcolor ? $hcolor : $randcolor[array_rand($randcolor)];
	$hfloat = $directories ? 'none' : 'left';
	$thefiles .= strpos($heading, '<select') !== false || strpos($heading, '<input') !== false ? $heading 
		: "<h3 class='ssfa-heading ssfa-$hcolor' style='float:$hfloat'>$rsslink$heading</h3>"; 
	$rsslink = null;
}
$thefiles .= $crumbies;
if($rsslink != null) $thefiles .= '<span style="float:left;">'.$rsslink.'</span>';
if($type === 'table' && $search !== 'no') $thefiles .=
	"<div class='ssfa-search-wrap $searchclass'><span data-ssfa-icon='&#xe047;' class='ssfa-searchicon $searchclass' aria-hidden='true'>$searchlabel</span>".
		"<input id='filter-$uid' class='ssfa-searchfield' placeholder='".__('SEARCH', 'file-away')."' value='' name='search' id='search' type='text' />".
	"</div>";
$thefiles .= '</div>';