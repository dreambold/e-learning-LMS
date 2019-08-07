<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$count = 0;
$data_atts = 'data-uid="'.$uid.'" data-pg="'.$GLOBALS['post']->ID.'" data-cls="'.$theme.'"';
$clicktosort = "title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"";
$thefiles .= "<script type='text/javascript'>jQuery(function(){ jQuery('.footable').footable();});</script>".
	"<table id='ssfa-table-$uid' data-filter='#filter-$uid' $page class='footable ssfa-sortable $theme $textalign' $data_atts>".
		"<thead><tr>".
			"<th class='ssfa-sorttimestamp $theme-first-column' $clicktosort data-sort-initial='descending' data-type='numeric'>".__('Timestamp', 'file-away')."</th>".
			"<th class='ssfa-sortfile' $clicktosort>".ucfirst(__('file', 'file-away'))."</th>".
			"<th class='ssfa-sortuid' $clicktosort data-type='numeric' style='width:120px;'>".str_replace(' ', '&nbsp;', __('User ID', 'file-away'))."</th>";
$thefiles .= $username !== 'no' ? "<th class='ssfa-sortusername' $clicktosort>".__('Username', 'file-away')."</th>" : null;
$thefiles .= $email !== 'no' ? "<th class='ssfa-sortemail' $clicktosort>".__('Email', 'file-away')."</th>" : null;
$thefiles .= $ip !== 'no' ? "<th class='ssfa-sortip' data-type='numeric' $clicktosort>".__('IP Address', 'file-away')."</th>" : null;
$thefiles .= $agent == 'yes' ? "<th class='ssfa-sortagent' $clicktosort>".__('User Agent', 'file-away')."</th>" : null;
$thefiles .= "</tr></thead><tfoot><tr><td colspan='100'>$pagearea</td></tr></tfoot><tbody>"; 
$records = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".fileaway_stats::$db." WHERE timestamp >= %s AND timestamp <= %s ORDER BY timestamp DESC", $begin, $end), ARRAY_A);
if($records && count($records))
{
	$rows = array();
	$download = $is_IE || $is_safari ? 'target="_blank"' : 'download';
	$datastat = $stats == 'true' ? 'data-stat="true"' : 'data-stat="false"';
	foreach($records as $record)
	{
		$datatime = strtotime($record['timestamp']);
		$prettytime = date($datestring.' '.$time_format, strtotime($record['timestamp']));
		$user = $record['uid'] == 0 ? __('Guest', 'file-away') : new WP_User($record['uid']);
		if($record['uid'] != 0) $user = $user->user_login;
		$count++;
		$filecol = $filecolumn == 'file' ? fileaway_utility::basename($record['file']) : str_replace('/', ' > ', $record['file']);
		$thefiles .= 
			"<tr id='ssfa-file-$uid-$count'>".
				"<td id='timestamp-ssfa-file-$uid-$count' class='ssfa-sorttimestamp $theme-first-column' data-value=\"$datatime\" style='min-width:120px;'>".$prettytime."</td>".
				"<td id='file-ssfa-file-$uid-$count' class='ssfa-sortfile'>".
					"<a href=\"".fileaway_utility::urlesc($url."/".$record['file'])."\" $download $datastat>".$filecol."</a>".
				"</td>".
				"<td id='uid-ssfa-file-$uid-$count' class='ssfa-sortuid'>".$record['uid']."</td>";
		if($username != 'no') $thefiles .= "<td id='username-ssfa-file-$uid-$count' class='ssfa-sortusername' style='min-width:120px;'>".$user."</td>";
		if($email != 'no') $thefiles .= "<td id='email-ssfa-file-$uid-$count' class='ssfa-sortemail'><a href=\"mailto:".$record['email']."\" target='_blank'>".$record['email']."</a></td>";
		if($ip != 'no') $thefiles .= "<td id='ip-ssfa-file-$uid-$count' class='ssfa-sortip'>".$record['ip']."</td>";
		if($agent == 'yes') $thefiles .= "<td id='agent-ssfa-file-$uid-$count' class='ssfa-sortagent'>".$record['agent']."</td>";
		$thefiles .= '</tr>'; 
		$rows[] = array(
			'TIMESTAMP'=>$record['timestamp'],
			'FILE'=>$record['file'],
			'UID'=>$record['uid'],
			'USERNAME'=>$user,
			'EMAIL'=>$record['email'],
			'IP'=>$record['ip'],
			'AGENT'=>$record['agent']
		);
	}
	$thefiles .= '</tbody></table>';
	$csv = new fileaway_csv();
	$cols = array();
	$csv->titles = array('TIMESTAMP','FILE','UID','USERNAME','EMAIL','IP','AGENT');
	foreach($rows as $k=> $row)
	{
		foreach($csv->titles as $header) $cols[$header] = $rows[$k][$header];
		$rows[$k] = $cols;
	}
	$csv->data = $rows;
	$filename = 'Download Stats ('.date('Y-m-d', strtotime($begin)).' - '.date('Y-m-d', strtotime($end)).').csv';
	$csv->save(fileaway_dir.'/temp/'.$filename);	
	$csvlink = 	'<a class="stataway-csv" href="'.fileaway_url.'/temp/'.$filename.'" download>Download CSV</a>';
	if(is_file(fileaway_dir.'/temp/'.$filename)) $thefiles .= $csvlink;
}
else $thefiles .= '</tbody></table>';