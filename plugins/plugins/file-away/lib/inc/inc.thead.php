<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if($type === 'table')
{
	$typesort = null; $filenamesort = null; $customsort = null; $modsort = null; $sizesort = null;
	if($sortfirst === 'type') $typesort = " data-sort-initial='true'"; 
	elseif($sortfirst === 'type-desc') $typesort = " data-sort-initial='descending'"; 
	elseif($sortfirst === 'filename') $filenamesort = " data-sort-initial='true'"; 
	elseif($sortfirst === 'filename-desc') $filenamesort = " data-sort-initial='descending'";
	elseif($sortfirst === 'custom') $customsort = " data-sort-initial='true'"; 
	elseif($sortfirst === 'custom-desc') $customsort = " data-sort-initial='descending'";
	elseif($sortfirst === 'mod') $modsort = " data-sort-initial='true'"; 
	elseif($sortfirst === 'mod-desc') $modsort = " data-sort-initial='descending'";
	elseif($sortfirst === 'size') $sizesort = " data-sort-initial='true'"; 
	elseif($sortfirst === 'size-desc') $sizesort = " data-sort-initial='descending'";
	else $filenamesort = " data-sort-initial='true' "; 
	if($directories) $filename = $drawerlabel ? $drawerlabel : ($filenamelabel ? $filenamelabel : _x('Drawer/File', 'File and Directory Name Column', 'file-away')); 
	else $filename = $filenamelabel ? $filenamelabel : _x('File&nbsp;Name', 'File Name Column', 'file-away');
	$datelabel = $datelabel ? $datelabel : _x('Date&nbsp;Modified', 'Date Modified Column', 'file-away');
	if($manager)
	{	
		$baseparts = explode('/', $start); 
		$basename = end($baseparts);
		$pathparts = explode('/', $dir);
		$basedir = end($pathparts);
		$fafl = $fa_firstlast_used ? "$fa_firstlast" : '0';
		$faui = $fa_userid_used ? "$fa_userid" : '0';
		$faun = $fa_username_used ? "$fa_username" : '0';
		$faur = $fa_userrole_used ? "$fa_userrole" : '0';
		$faum = $fa_usermeta_used ? implode(',', $fa_metavalues) : '0';
		$path = '<input type="hidden" id="ssfa-actionpath-'.$uid.'" value="" />';
		$mdata = $metadata ? 'true' : 'false';
	}
	$typelabel = $playback && $playbacklabel ? $playbacklabel : _x('Type', 'File Type Column', 'file-away');
	$typesorter = $playback ? "data-sort-ignore='true'" : "title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\" $typesort";
	$data_stat = $stats ? 'data-stats="true"' : 'data-stats="false"';
	$data_atts = $manager ? 'data-uid="'.$uid.'" data-pg="'.$GLOBALS['post']->ID.'" data-drw="'.$drawericon.'" data-cls="'.$theme.'" data-metadata="'.$mdata.'" '.
		'data-basename="'.$basename.'" data-start="'.$start.'" data-dir="'.trim("$dir",'/').'" data-base="'.$basebase.'" data-basedir="'.$basedir.'"'.
		'data-fafl="'.$fafl.'" data-faui="'.$faui.'" data-faun="'.$faun.'" data-faur="'.$faur.'" data-faum="'.$faum.'"' : null;
	$data_drawer = $directories ? 'data-drawer="drawer'.$drawerid.'"' : null;
	$treeclass = $directories || $manager ? 'dirtree-table' : null;
	$disablesort = $bannerize || $sortfirst == 'disabled' ? "data-sort='false'" : false;
	$filenamesort = $disablesort ? null : $filenamesort;
	$manager_nonce = $manager ? 'data-mn="'.wp_create_nonce('fileaway-manager-nonce').'"' : '';
	$bulkdownload_nonce = $bulkdownload || $manager ? 'data-bd="'.wp_create_nonce('fileaway-bulk-download-nonce').'"' : '';
	$thefiles .= "<script type='text/javascript'>jQuery(function(){ jQuery('.footable').footable();});</script>".
		"<table id='ssfa-table-$uid' data-filter='#filter-$uid' $disablesort $page class='footable ssfa-sortable $theme $textalign $treeclass $bulkclass' ".
			"$data_drawer $data_atts $data_stat $manager_nonce $bulkdownload_nonce>".
			"<thead><tr>".
				"<th class='ssfa-sorttype $theme-first-column' $typesorter>".$typelabel."</th>".
				"<th class='ssfa-sortname' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$filenamesort.">$filename$path</th>";
	$cells = array();
	if($mod !== 'no') $cells[] = 1; 
	if($size !== 'no') $cells[] = 1;
	if($manager) $cells[] = 1; 
	if($customdata)
	{
		$custom_sort = true;
		$customarray = preg_split('/(, |,)/', trim($customdata), -1, PREG_SPLIT_NO_EMPTY); 
		if(!is_array($customarray)) $customarray = array();
		foreach($customarray as $customdatum)
		{
			if(preg_match('/[*]/', $customdatum)) $custom_sort = false;
		}
		foreach($customarray as $customdatum)
		{
			if($customdatum !== ' ')
			{
				$cells[] = 1;
				if(preg_match('/[*]/', $customdatum))
				{
					$customdatum = str_replace('*', '', $customdatum); 
					$custom_sort = true; 
				}
				if($custom_sort == true) $custom_sort = $customsort;
				$customdatum = trim($customdatum);
				$thefiles .= "<th class='ssfa-sortcustomdata' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$custom_sort.">$customdatum</th>";
			}
		}
	}
	$thefiles .= $mod !== 'no' 
		? "<th class='ssfa-sortdate' data-type='numeric' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$modsort.">".$datelabel."</th>" 
		: null;
	$thefiles .= $size !== 'no' 
		? "<th class='ssfa-sortsize' data-type='numeric' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$sizesort.">".
			_x('Size', 'File Size Column', 'file-away')."</th>" 
		: null;
	if($manager) $thefiles .= "<th style='width:90px!important;' class='ssfa-manager' data-sort-ignore='true'>"._x('Manage', 'Manager Column', 'file-away')."</th>";
	$thefiles .= "</tr></thead><tfoot><tr><td colspan='100'>$pagearea</td></tr></tfoot><tbody>"; 
}