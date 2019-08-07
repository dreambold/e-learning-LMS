<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$thefiles .= 
"<div class='ssfa-clearfix'>".
	"<div id='ssfa-bulk-download-area-$uid' style='text-align:right; float:right'>".
		"<div style='text-align:left; margin-top:5px;'>".
		"<input type='checkbox' id='ssfa-bulk-download-select-all-$uid' style='display:inline-block; margin-top:5px!important; margin-right:5px;' ".
		"data-selectall=\"".__('Select All', 'file-away')."\" data-clearall=\"".__('Clear All', 'file-away')."\"/>".
		"<label for='ssfa-bulk-download-select-all-$uid' id='ssfa-bulkdownload-select-all-$uid' style='display:inline-block; font-size:12px;'> 
			".__('Select All', 'file-away')."</label>".
		"<span id='ssfa-bulk-download-engage-$uid' class='ssfa-bulk-download-engage'>".__('Download', 'file-away')."</span>".
		"</div>".
		"<br><img id='ssfa-engage-ajax-loading-$uid' src='".fileaway_url."/lib/img/ajax.gif' style='width:15px; display:none; margin: 0 5px 0 0!important; box-shadow: none;'>".
	"</div>".
"</div>";