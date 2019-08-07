<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$thefiles = '';
$uid = rand(0, 9999); 
$crumbies = null;
$rsslink = null;
$directories = false;
$type = 'table';
$name = "ssfa-meta-container-$uid";
$randcolor = array("red","green","blue","brown","black","orange","silver","purple","pink");
$paginated = $paginate ? " data-page-navigation='.ssfa-pagination'" : null;
$pagearea = $paginate ? "<div class='ssfa-pagination ssfa-pagination-centered hide-if-no-paging'></div>" : null;
$pagesized = $paginate ? " data-page-size='$pagesize'" : null;
$page = $paginate ? $paginated.$pagesized : "$paginated data-page-size='100000'";
$disablesort = $sort == 'no' ? 'data-sort="false"' : null;
$initsort = $sort == 'desc' ? 'data-sort-initial="descending"' : 'data-sort-initial="true"';
$theme = "ssfa-$theme";
$textalign = $textalign ? ' ssfa-'.$textalign : null;
$width = preg_replace('[\D]', '', $width);
$width = $width ? "width:$width$perpx;" : null;
$float = " float:$align;";
$margin = $width !== 'width:100%;' ? ($align === 'right' ? ' margin-left:15px;' : ' margin-right:15px;') : null;
$howshouldiputit = $width.$float.$margin;
if($width == '100' && $perpx == '%') $align = 'none';
$mobileclass = $get->is_mobile ? 'ssfa-mobile' : null;
$clearfix = $align == 'none' ? "<div class='ssfa-clearfix'></div>" : null;
$fadeit = $fadein ? ($fadein == 'opacity' ? 'opacity:0;' : 'display:none;') : null;
if($fadein)
{
	$fadescript = $fadein == 'opacity' ? '.animate({opacity:"1"}, '.$fadetime.');' : '.fadeIn('.$fadetime.');';
	$thefiles .= '<script> jQuery(document).ready(function($){ setTimeout(function(){ $("div#'.$name.'")'.$fadescript.' }, 1000); }); </script>';
}