<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$thefiles .= 
"<div class='ssfa-clearfix'>".
	"<div id='ssfa-bulk-action-toggle-$uid' style='text-align:right; float:right'>".
		__('Bulk Action Mode:', 'file-away')." ".
		"<a href='javascript:' data-disabled=\"".__('Disabled', 'file-away')."\" data-enabled=\"".__('Enabled', 'file-away')."\" ".
			"id='ssfa-bulk-action-toggle-$uid'>".__('Disabled', 'file-away')."</a><br>".
		"<div id='ssfa-bulk-action-select-area-$uid' style='display:none;'>".
			"<div style='text-align:left; margin-top:5px;'>".
				"<select style='display:none;' class='chozed-select ssfa-bulk-action-select' id='ssfa-bulk-action-select-$uid' ".
					"data-placeholder=\"".__('Select Action', 'file-away')."\">".
					"<option></option>".
					"<option value='download'>".__('Download', 'file-away')."</option>".
					"<option value='copy'>".__('Copy', 'file-away')."</option>".
					"<option value='move'>".__('Move', 'file-away')."</option>".
					"<option value='delete'>".__('Delete', 'file-away')."</option>".
				"</select>".
				"<span id='ssfa-bulk-action-engage-$uid' class='ssfa-bulk-action-engage'>"._x('File Away', 'Bulk Action Submit Button', 'file-away')."</span>".
			"</div>".
			"<br>".
			"<label for='ssfa-bulk-action-select-all-$uid' id='ssfa-bulkaction-select-all-$uid' style='font-size:12px;'>".
				"<input type='checkbox' id='ssfa-bulk-action-select-all-$uid' style='margin-top:5px!important; margin-right:5px;' ".
				"data-selectall=\"".__('Select All', 'file-away')."\" data-clearall=\"".__('Clear All', 'file-away')."\" /> ".
				"<span>".__('Select All', 'file-away')."</span>".
			"</label>".
			"<img id='ssfa-engage-ajax-loading-$uid' src='".fileaway_url."/lib/img/ajax.gif' style='width:15px; display:none; margin:0 0 0 5px!important; box-shadow:none!important;'>".
		"</div>".
	"</div>".
	"<div id='ssfa-path-container-$uid' style='display:none; float:left;'>".
		"<div id='ssfa-directories-select-container-$uid' class='frm_form_field form-field frm_required_field frm_top_container frm_full'>".
			"<label for='ssfa-directories-select-$uid' class='frm_primary_label' style='display:block!important; margin-bottom:5px!important;'>".
				__('Destination Directory', 'file-away')."<span class='frm_required'> <span style='color:red'>*</span></span>".
			"</label>".
			"<select name='ssfa-directories-select-$uid' id='ssfa-directories-select-$uid' class='chozed-select ssfa-directories-select' data-placeholder='&nbsp;'>".
				"<option></option>".
				"<option value=\"$start\">$basename</option>".
			"</select>".
			"<br>".
			"<div id='ssfa-action-path-$uid' style='margin-top:5px; min-height:25px;'>".
				"<img id='ssfa-path-ajax-loading-$uid' src='".fileaway_url."/lib/img/ajax.gif' style='width:15px; margin:0 0 0 5px!important; box-shadow:none!important; display:none;'>".
			"</div>".
		"</div>".
	"</div>".
"</div>";