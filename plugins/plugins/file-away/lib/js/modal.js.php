<?php
/*
** Include JS for TinyMCE
*/
?>
<script>
jQuery(document).ready(function($)
{
	// CHOSEN
	$('select.chozed-select').chozed({
		allow_single_deselect:true, 
		width: '100%',
		disable_search_threshold: 5, 
		inherit_select_classes:true,
		no_results_text: "Say what?",
		search_contains: true, 
	}); 
	$('div.fileaway-wrap').on('hide', function()
	{
		$(this).find('input').val('').trigger('input');    
		$('select.select', this).find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		$('select.select option:selected', this).removeAttr("selected").trigger('chozed:updated').trigger('change');
	});
	$('select#fileaway_shortcode_select').on('change', function()
	{
		$('div.fileaway-wrap').find('input').val('').trigger('input');    
		$type = $('select#fileaway_type_select');
		$num = $('option:selected', this).data('types');
		if($num < 2 || $(this).val() == '')
		{ 
			$('div#fileaway_type_select').fadeOut(500);
			$type.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else $('div#fileaway_type_select').fadeIn(500);
		$sc = $(this).val();
		$st = $type.val();
		$combined = $st != '' ? $sc+'_'+$st : $sc;
		$banner = $sc == '' ? $('img#fileaway_banner_fileaway') : $('img#fileaway_banner_'+$sc)
		$('div.fileaway-wrap').each(function()
		{
			$container = $(this).data('container');
			if($container != $combined) $(this).fadeOut(500).trigger('hide');
			else $(this).delay(500).fadeIn(500);
		});
		if($banner.is(':visible')){}
		else 
		{
			$('img[id^="fileaway_banner_"]').fadeOut(500);
			$banner.delay(500).fadeIn(500);
		}
	});
	$('select#fileaway_type_select').on('change', function()
	{
		$type = $(this);
		$st = $(this).val();
		$sc = $('select#fileaway_shortcode_select').val();
		$combined = $st != '' ? $sc+'_'+$st : $sc;
		$('div.fileaway-wrap').each(function()
		{
			$container = $(this).data('container');
			if($container != $combined) $(this).fadeOut(500).trigger('hide');
			else $(this).delay(500).fadeIn(500);
		});
	});
	// TABS
	$('a[id^="fileaway-tab-"]').on('click', function(ev)
	{
		ev.preventDefault();
		var parent = $(this).parents('div.fileaway-wrap').eq("0").data('container');
		var slug = $(this).attr('data-tab');
		var panel = $('div#options-container-'+parent+' div#fileaway-panel-'+slug);
		if(panel.is(':visible')){}
		else
		{
			$('div#options-container-'+parent+' li.'+slug).addClass('state-active')
				.siblings('div#options-container-'+parent+' li').removeClass('state-active');
			$('div#options-container-'+parent+' div[id^="fileaway-panel-"]').fadeOut(500);
			panel.delay(500).fadeIn(500);
		}		
	});
	// CONDITIONALS
	// Parents
	$list_base = $('select#fileaway_list_base');
	$table_base = $('select#fileaway_table_base');	
	$flist_flightbox = $('select#fileaway_list_flightbox');
	$ftable_flightbox = $('select#fileaway_table_flightbox');
	$alist_flightbox = $('select#attachaway_list_flightbox');
	$atable_flightbox = $('select#attachaway_table_flightbox');
	$list_recursive = $('select#fileaway_list_recursive');
	$table_recursive = $('select#fileaway_table_recursive');
	$list_encryption = $('select#fileaway_list_encryption');
	$table_encryption = $('select#fileaway_table_encryption');
	$directories = $('select#fileaway_table_directories');
	$manager = $('select#fileaway_table_manager');
	$playback = $('select#fileaway_table_playback');
	$list_limit = $('input#fileaway_list_limit');
	$table_limit = $('input#fileaway_table_limit');
	$thumbnails = $('select#fileaway_table_thumbnails');
	// Children
	$list_s2skipconfirm_container = $('div[id^="fileaway-container-fileaway_list_config_s2skipconfirm_"]');
	$list_s2skipconfirm = $('select#fileaway_list_s2skipconfirm');
	$table_s2skipconfirm_container = $('div[id^="fileaway-container-fileaway_table_config_s2skipconfirm_"]');	
	$table_s2skipconfirm = $('select#fileaway_table_s2skipconfirm');
	$flist_boxtheme_container = $('div[id^="fileaway-container-fileaway_list_modes_boxtheme_"]');
	$flist_boxtheme = $('select#fileaway_list_boxtheme');	
	$ftable_boxtheme_container = $('div[id^="fileaway-container-fileaway_table_modes_boxtheme_"]');
	$ftable_boxtheme = $('select#fileaway_table_boxtheme');	
	$alist_boxtheme_container = $('div[id^="fileaway-container-attachaway_list_modes_boxtheme_"]');
	$alist_boxtheme = $('select#attachaway_list_boxtheme');	
	$atable_boxtheme_container = $('div[id^="fileaway-container-attachaway_table_modes_boxtheme_"]');
	$atable_boxtheme = $('select#attachaway_table_boxtheme');
	$flist_nolinksbox_container = $('div[id^="fileaway-container-fileaway_list_modes_nolinksbox_"]');
	$flist_nolinksbox = $('select#fileaway_list_nolinksbox');	
	$ftable_nolinksbox_container = $('div[id^="fileaway-container-fileaway_table_modes_nolinksbox_"]');
	$ftable_nolinksbox = $('select#fileaway_table_nolinksbox');	
	$alist_nolinksbox_container = $('div[id^="fileaway-container-attachaway_list_modes_nolinksbox_"]');
	$alist_nolinksbox = $('select#attachaway_list_nolinksbox');	
	$atable_nolinksbox_container = $('div[id^="fileaway-container-attachaway_table_modes_nolinksbox_"]');
	$atable_nolinksbox = $('select#attachaway_table_nolinksbox');	
	$flist_maximgwidth_container = $('div[id^="fileaway-container-fileaway_list_modes_maximgwidth_"]');
	$flist_maximgwidth = $('input#fileaway_list_maximgwidth');	
	$ftable_maximgwidth_container = $('div[id^="fileaway-container-fileaway_table_modes_maximgwidth_"]');
	$ftable_maximgwidth = $('input#fileaway_table_maximgwidth');	
	$alist_maximgwidth_container = $('div[id^="fileaway-container-attachaway_list_modes_maximgwidth_"]');
	$alist_maximgwidth = $('input#attachaway_list_maximgwidth');	
	$atable_maximgwidth_container = $('div[id^="fileaway-container-attachaway_table_modes_maximgwidth_"]');
	$atable_maximgwidth = $('input#attachaway_table_maximgwidth');
	$flist_maximgheight_container = $('div[id^="fileaway-container-fileaway_list_modes_maximgheight_"]');
	$flist_maximgheight = $('input#fileaway_list_maximgheight');	
	$ftable_maximgheight_container = $('div[id^="fileaway-container-fileaway_table_modes_maximgheight_"]');
	$ftable_maximgheight = $('input#fileaway_table_maximgheight');	
	$alist_maximgheight_container = $('div[id^="fileaway-container-attachaway_list_modes_maximgheight_"]');
	$alist_maximgheight = $('input#attachaway_list_maximgheight');	
	$atable_maximgheight_container = $('div[id^="fileaway-container-attachaway_table_modes_maximgheight_"]');
	$atable_maximgheight = $('input#attachaway_table_maximgheight');
	$flist_videowidth_container = $('div[id^="fileaway-container-fileaway_list_modes_videowidth_"]');
	$flist_videowidth = $('input#fileaway_list_videowidth');	
	$ftable_videowidth_container = $('div[id^="fileaway-container-fileaway_table_modes_videowidth_"]');
	$ftable_videowidth = $('input#fileaway_table_videowidth');	
	$alist_videowidth_container = $('div[id^="fileaway-container-attachaway_list_modes_videowidth_"]');
	$alist_videowidth = $('input#attachaway_list_videowidth');	
	$atable_videowidth_container = $('div[id^="fileaway-container-attachaway_table_modes_videowidth_"]');
	$atable_videowidth = $('input#attachaway_table_videowidth');			
	$drawerid_container = $('div[id^="fileaway-container-fileaway_table_modes_drawerid_"]');
	$drawerid = $('input#fileaway_table_drawerid');
	$list_excludedirs_container = $('div[id^="fileaway-container-fileaway_list_modes_excludedirs_"]');
	$list_excludedirs = $('input#fileaway_list_excludedirs');
	$table_excludedirs_container = $('div[id^="fileaway-container-fileaway_table_modes_excludedirs_"]');
	$table_excludedirs = $('input#fileaway_table_excludedirs');
	$list_onlydirs_container = $('div[id^="fileaway-container-fileaway_list_modes_onlydirs_"]');
	$list_onlydirs = $('input#fileaway_list_onlydirs');
	$table_onlydirs_container = $('div[id^="fileaway-container-fileaway_table_modes_onlydirs_"]');
	$table_onlydirs = $('input#fileaway_table_onlydirs');
	$drawericon_container = $('div[id^="fileaway-container-fileaway_table_modes_drawericon_"]');
	$drawericon = $('select#fileaway_table_drawericon');
	$drawerlabel_container = $('div[id^="fileaway-container-fileaway_table_modes_drawerlabel_"]');
	$drawerlabel = $('input#fileaway_table_drawerlabel');
	$parentlabel_container = $('div[id^="fileaway-container-fileaway_table_modes_parentlabel_"]');
	$parentlabel = $('input#fileaway_table_parentlabel');	
	$dirman_access_container = $('div[id^="fileaway-container-fileaway_table_modes_dirman_access_"]');
	$dirman_access = $('select#fileaway_table_dirman_access');
	$role_override_container = $('div[id^="fileaway-container-fileaway_table_modes_role_override_"]');
	$role_override = $('select#fileaway_table_role_override');
	$user_override_container = $('div[id^="fileaway-container-fileaway_table_modes_user_override_"]');
	$user_override = $('input#fileaway_table_user_override');	
	$password_container = $('div[id^="fileaway-container-fileaway_table_modes_password_"]');
	$password = $('input#fileaway_table_password');
	$playbackpath_container = $('div[id^="fileaway-container-fileaway_table_modes_playbackpath_"]');
	$playbackpath = $('input#fileaway_table_playbackpath');
	$playbacklabel_container = $('div[id^="fileaway-container-fileaway_table_modes_playbacklabel_"]');
	$playbacklabel = $('input#fileaway_table_playbacklabel');
	$onlyaudio_container = $('div[id^="fileaway-container-fileaway_table_modes_onlyaudio_"]');
	$onlyaudio = $('select#fileaway_table_onlyaudio');
	$loopaudio_container = $('div[id^="fileaway-container-fileaway_table_modes_loopaudio_"]');
	$loopaudio = $('select#fileaway_table_loopaudio');
	$list_limitby_container = $('div[id^="fileaway-container-fileaway_list_filters_limitby_"]');
	$list_limitby = $('select#fileaway_list_limitby');
	$table_limitby_container = $('div[id^="fileaway-container-fileaway_table_filters_limitby_"]');
	$table_limitby = $('select#fileaway_table_limitby');
	$maxsrcbytes_container = $('div[id^="fileaway-container-fileaway_table_styles_maxsrcbytes_"]');
	$maxsrcbytes = $('input#fileaway_table_maxsrcbytes');
	$maxsrcwidth_container = $('div[id^="fileaway-container-fileaway_table_styles_maxsrcwidth_"]');
	$maxsrcwidth = $('input#fileaway_table_maxsrcwidth');
	$maxsrcheight_container = $('div[id^="fileaway-container-fileaway_table_styles_maxsrcheight_"]');
	$maxsrcheight = $('input#fileaway_table_maxsrcheight');		
	$thumbstyle_container = $('div[id^="fileaway-container-fileaway_table_styles_thumbstyle_"]');
	$thumbstyle = $('select#fileaway_table_thumbstyle');
	$thumbsize_container = $('div[id^="fileaway-container-fileaway_table_styles_thumbsize_"]');
	$thumbsize = $('select#fileaway_table_thumbsize');
	$graythumbs_container = $('div[id^="fileaway-container-fileaway_table_styles_graythumbs_"]');
	$graythumbs = $('select#fileaway_table_graythumbs');	
	// Functions
	$list_base.on('change', function()
	{
		if($(this).val() == 's2member-files')
		{
			$list_s2skipconfirm_container.fadeIn(500);
			$('input#fileaway_list_sub').val('');
			$list_recursive.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$list_encryption.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$flist_flightbox.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			$list_s2skipconfirm_container.fadeOut(500);
			$list_s2skipconfirm.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});
	$table_base.on('change', function()
	{
		if($(this).val() == 's2member-files')
		{
			$table_s2skipconfirm_container.fadeIn(500);
			$('input#fileaway_table_sub').val('');			
			$directories.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$table_recursive.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$manager.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$table_encryption.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$ftable_flightbox.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			$table_s2skipconfirm_container.fadeOut(500);
			$table_s2skipconfirm.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});
	$flist_flightbox.on('change', function()
	{
		if($(this).val() != '') 
		{
			$flist_boxtheme_container.fadeIn(500);
			$flist_nolinksbox_container.fadeIn(500);
			if($list_base.val() == 's2member-files') $list_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else 
		{
			$flist_boxtheme_container.fadeOut(500);
			$flist_boxtheme.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$flist_nolinksbox_container.fadeOut(500);
			$flist_nolinksbox.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
		if($(this).val() == 'images' || $(this).val() == 'multi')
		{
			$flist_maximgwidth_container.fadeIn(500);
			$flist_maximgheight_container.fadeIn(500);	
		}
		else 
		{
			$flist_maximgwidth_container.fadeOut(500);
			$flist_maximgheight_container.fadeOut(500);
			$flist_maximgwidth.val('');
			$flist_maximgheight.val('');			
		}
		if($(this).val() == 'videos' || $(this).val() == 'multi') $flist_videowidth_container.fadeIn(500);	
		else 
		{
			$flist_videowidth_container.fadeOut(500);
			$flist_videowidth.val('');
		}
	});
	$ftable_flightbox.on('change', function()
	{
		if($(this).val() != '') 
		{
			$ftable_boxtheme_container.fadeIn(500);
			$ftable_nolinksbox_container.fadeIn(500);
			if($table_base.val() == 's2member-files') $table_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else 
		{
			$ftable_boxtheme_container.fadeOut(500);
			$ftable_boxtheme.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$ftable_nolinksbox_container.fadeOut(500);
			$ftable_nolinksbox.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
		if($(this).val() == 'images' || $(this).val() == 'multi')
		{
			$ftable_maximgwidth_container.fadeIn(500);
			$ftable_maximgheight_container.fadeIn(500);	
		}
		else 
		{
			$ftable_maximgwidth_container.fadeOut(500);
			$ftable_maximgheight_container.fadeOut(500);
			$ftable_maximgwidth.val('');
			$ftable_maximgheight.val('');			
		}
		if($(this).val() == 'videos' || $(this).val() == 'multi') $ftable_videowidth_container.fadeIn(500);	
		else 
		{
			$ftable_videowidth_container.fadeOut(500);
			$ftable_videowidth.val('');
		}
	});
	$alist_flightbox.on('change', function()
	{
		if($(this).val() != '') 
		{
			$alist_boxtheme_container.fadeIn(500);
			$alist_nolinksbox_container.fadeIn(500);
		}
		else 
		{
			$alist_boxtheme_container.fadeOut(500);
			$alist_boxtheme.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$alist_nolinksbox_container.fadeOut(500);
			$alist_nolinksbox.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
		if($(this).val() == 'images' || $(this).val() == 'multi')
		{
			$alist_maximgwidth_container.fadeIn(500);
			$alist_maximgheight_container.fadeIn(500);	
		}
		else 
		{
			$alist_maximgwidth_container.fadeOut(500);
			$alist_maximgheight_container.fadeOut(500);
			$alist_maximgwidth.val('');
			$alist_maximgheight.val('');			
		}
		if($(this).val() == 'videos' || $(this).val() == 'multi') $alist_videowidth_container.fadeIn(500);	
		else 
		{
			$alist_videowidth_container.fadeOut(500);
			$alist_videowidth.val('');
		}
	});
	$atable_flightbox.on('change', function()
	{
		if($(this).val() != '') 
		{
			$atable_boxtheme_container.fadeIn(500);
			$atable_nolinksbox_container.fadeIn(500);
		}
		else 
		{
			$atable_boxtheme_container.fadeOut(500);
			$atable_boxtheme.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$atable_nolinksbox_container.fadeOut(500);
			$atable_nolinksbox.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
		if($(this).val() == 'images' || $(this).val() == 'multi')
		{
			$atable_maximgwidth_container.fadeIn(500);
			$atable_maximgheight_container.fadeIn(500);	
		}
		else 
		{
			$atable_maximgwidth_container.fadeOut(500);
			$atable_maximgheight_container.fadeOut(500);
			$atable_maximgwidth.val('');
			$atable_maximgheight.val('');			
		}
		if($(this).val() == 'videos' || $(this).val() == 'multi') $atable_videowidth_container.fadeIn(500);	
		else 
		{
			$atable_videowidth_container.fadeOut(500);
			$atable_videowidth.val('');
		}
	});		
	$list_encryption.on('change', function()
	{
		if($(this).val() != '')
		{
			if($list_base.val() == 's2member-files') $list_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
	});
	$table_encryption.on('change', function()
	{
		if($(this).val() != '')
		{
			$manager.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			if($table_base.val() == 's2member-files') $table_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
	});
	$list_recursive.on('change', function()
	{
		if($(this).val() != '')
		{ 
			$list_excludedirs_container.fadeIn(500);
			$list_onlydirs_container.fadeIn(500);
			if($list_base.val() == 's2member-files') $list_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			$list_excludedirs_container.fadeOut(500);
			$list_onlydirs_container.fadeOut(500);
			$list_excludedirs.val('');
			$list_onlydirs.val('');
		}
	});
	$table_recursive.on('change', function()
	{
		if($(this).val() != '')
		{ 
			$table_excludedirs_container.fadeIn(500);
			$table_onlydirs_container.fadeIn(500);
			if($table_base.val() == 's2member-files') $table_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$directories.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$manager.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else if($directories.val() != '' || $manager.val() != '')
		{
			/* Do Nothing */
		}
		else
		{
			$table_excludedirs_container.fadeOut(500);
			$table_onlydirs_container.fadeOut(500);
			$table_excludedirs.val('');
			$table_onlydirs.val('');
		}
	});
	$directories.on('change', function()
	{
		if($(this).val() != '')
		{ 
			$drawerid_container.fadeIn(500);
			$table_excludedirs_container.fadeIn(500);
			$table_onlydirs_container.fadeIn(500);
			$drawericon_container.fadeIn(500);
			$drawerlabel_container.fadeIn(500);
			$parentlabel_container.fadeIn(500);			
			if($table_base.val() == 's2member-files') $table_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$table_recursive.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			if($table_recursive.val() != '' && $manager.val() == '')
			{
				$drawerid_container.fadeOut(500);
				$drawericon_container.fadeOut(500);
				$drawerlabel_container.fadeOut(500);
				$parentlabel_container.fadeOut(500);				
				$drawerid.val('');
				$drawericon.find('option:first').attr('selected','selected').trigger('chozed:updated');
				$drawerlabel.val('');
				$parentlabel.val('');				
			}
			else if($manager.val() != '')
			{
				/* Do Nothing */
			}
			else
			{
				$drawerid_container.fadeOut(500);
				$drawerid.val('');
				$table_excludedirs_container.fadeOut(500);
				$table_onlydirs_container.fadeOut(500);
				$table_excludedirs.val('');
				$table_onlydirs.val('');
				$drawericon_container.fadeOut(500);
				$drawerlabel_container.fadeOut(500);
				$parentlabel_container.fadeOut(500);				
				$drawericon.find('option:first').attr('selected','selected').trigger('chozed:updated');
				$parentlabel.val('');
			}
		}
	});
	$manager.on('change', function()
	{
		if($(this).val() != '')
		{ 
			$drawerid_container.fadeIn(500);
			$table_excludedirs_container.fadeIn(500);
			$table_onlydirs_container.fadeIn(500);
			$drawericon_container.fadeIn(500);
			$drawerlabel_container.fadeIn(500);
			$parentlabel_container.fadeIn(500);			
			$dirman_access_container.fadeIn(500);
			$role_override_container.fadeIn(500);
			$user_override_container.fadeIn(500);
			$password_container.fadeIn(500);
			$table_recursive.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$playback.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
			$table_encryption.find('option:first').attr('selected','selected').trigger('chozed:updated');
			if($table_base.val() == 's2member-files') $table_base.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			if($table_recursive.val() != '' && $directories.val() == '')
			{
				$drawerid_container.fadeOut(500);
				$drawerid.val('');
				$drawericon_container.fadeOut(500);
				$drawerlabel_container.fadeOut(500);
				$parentlabel_container.fadeOut(500);				
				$drawericon.find('option:first').attr('selected','selected').trigger('chozed:updated');
				$drawerlabel.val('');
				$parentlabel.val('');				
				$dirman_access_container.fadeOut(500);
				$role_override_container.fadeOut(500);
				$user_override_container.fadeOut(500);
				$password_container.fadeOut(500);
				$('option:selected', $dirman_access).removeAttr("selected").trigger('chozed:updated');
				$('option:selected', $role_override).removeAttr("selected").trigger('chozed:updated');
				$user_override.val('');
				$password.val('');
			}
			else if($directories.val() != '')
			{
				$dirman_access_container.fadeOut(500);
				$role_override_container.fadeOut(500);
				$user_override_container.fadeOut(500);
				$password_container.fadeOut(500);
				$('option:selected', $dirman_access).removeAttr("selected").trigger('chozed:updated');
				$('option:selected', $role_override).removeAttr("selected").trigger('chozed:updated');
				$user_override.val('');
				$password.val('');
			}
			else
			{
				$drawerid_container.fadeOut(500);
				$drawerid.val('');
				$table_excludedirs_container.fadeOut(500);
				$table_onlydirs_container.fadeOut(500);
				$table_excludedirs.val('');
				$table_onlydirs.val('');
				$drawericon_container.fadeOut(500);
				$drawerlabel_container.fadeOut(500);
				$parentlabel_container.fadeOut(500);				
				$drawericon.find('option:first').attr('selected','selected').trigger('chozed:updated');
				$drawerlabel.val('');
				$parentlabel.val('');
				$dirman_access_container.fadeOut(500);
				$role_override_container.fadeOut(500);
				$user_override_container.fadeOut(500);
				$password_container.fadeOut(500);
				$('option:selected', $dirman_access).removeAttr("selected").trigger('chozed:updated');
				$('option:selected', $role_override).removeAttr("selected").trigger('chozed:updated');
				$user_override.val('');
				$password.val('');				
			}
		}
	});
	$playback.on('change', function()
	{
		if($(this).val() != '')
		{
			$playbackpath_container.fadeIn(500);
			$playbacklabel_container.fadeIn(500);
			$onlyaudio_container.fadeIn(500);
			$loopaudio_container.fadeIn(500);
			$manager.find('option:first').attr('selected','selected').trigger('chozed:updated').trigger('change');
		}
		else
		{
			$playbackpath_container.fadeOut(500);
			$playbacklabel_container.fadeOut(500);
			$onlyaudio_container.fadeOut(500);
			$loopaudio_container.fadeOut(500);
			$playbackpath.val('');
			$playbacklabel.val('');
			$onlyaudio.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$loopaudio.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});
	$list_limit.on('input', function()
	{
		if($(this).val() != '') $list_limitby_container.fadeIn(500);
		else
		{
			$list_limitby_container.fadeOut(500);
			$list_limitby.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});
	$table_limit.on('input', function()
	{
		if($(this).val() != '') $table_limitby_container.fadeIn(500);
		else
		{
			$table_limitby_container.fadeOut(500);
			$table_limitby.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});	
	$thumbnails.on('change', function()
	{
		if($(this).val() == 'transient')
		{
			$maxsrcbytes_container.fadeIn(500);
			$maxsrcwidth_container.fadeIn(500);
			$maxsrcheight_container.fadeIn(500);
			$thumbstyle_container.fadeIn(500);
			$thumbsize_container.fadeIn(500);
			$graythumbs_container.fadeIn(500);
		}
		else if($(this).val() == 'permanent')
		{
			$thumbstyle_container.fadeIn(500);
			$thumbsize_container.fadeIn(500);
			$graythumbs_container.fadeIn(500);		
			$maxsrcbytes_container.fadeOut(500);
			$maxsrcwidth_container.fadeOut(500);
			$maxsrcheight_container.fadeOut(500);
			$maxsrcbytes.val('');
			$maxsrcwidth.val('');
			$maxsrcheight.val('');
		}
		else
		{
			$thumbstyle_container.fadeOut(500);
			$thumbsize_container.fadeOut(500);
			$graythumbs_container.fadeOut(500);		
			$maxsrcbytes_container.fadeOut(500);
			$maxsrcwidth_container.fadeOut(500);
			$maxsrcheight_container.fadeOut(500);
			$maxsrcbytes.val('');
			$maxsrcwidth.val('');
			$maxsrcheight.val('');
			$thumbstyle.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$thumbsize.find('option:first').attr('selected','selected').trigger('chozed:updated');
			$graythumbs.find('option:first').attr('selected','selected').trigger('chozed:updated');
		}
	});
	// Dynamic Conditionals
	$numcols = $('input#formaway_open_open_numcols');
	var inputdelay = (function()
	{
		var timer = 0;
		return function(callback, ms)
		{
			clearTimeout(timer);
			timer = setTimeout(callback, ms);
		};
	})();
	$numcols.on('input', function(){
		inputdelay(function()
		{
			$num = $numcols.val();
			$num = $num != '' && Math.floor($num) == $num && $.isNumeric($num) ? parseInt($num).toFixed() : 0;
			$('div[id^="fileaway-container-formaway_open_open_columns_col"]').each(function(){
				if(parseInt($(this).data('col')) > $num) $(this).fadeOut(500).queue(function(){ $(this).remove(); });
			});
			$('select#formaway_open_open_initialsort > option').each(function(){
				if(parseInt($(this).data('col')) > $num) $(this).remove();	
			});
			$('select#formaway_open_open_initialsort').trigger("chozed:updated");
			for(var i = 1, l = $num; i <= l; i++)
			{
				$container = $('div#fileaway-container-formaway_open_open_columns_col'+i);
				if(!$container.length)
				{
					if(!$('select#formaway_open_open_initialsort option[value="'+i+'"]').length) 
						$('select#formaway_open_open_initialsort').append('<option value="'+i+'" data-col="'+i+'">Column '+i+'</option>');
					$('div#options-container-formaway_open div#fileaway-panel-columns').append(
						'<div class="fileaway-inline fileaway-half" id="fileaway-container-formaway_open_open_columns_col'+i+'" data-col="'+i+'">'+
							'<div style="width:100%; text-align:right; margin: 2px 0 3px;">'+
								'<label for="formaway_open_open_col'+i+'">'+
									'<span class="link-fileaway-help-col fileaway-helplink fileaway-help-iconinfo2" data-info="col"></span>Col '+i+' Heading'+
								'</label>'+
							'</div>'+
							'<input class="fileaway-text " type="text" '+
								'id="formaway_open_open_col'+i+'" name="formaway_open_open_col'+i+'" placeholder="" value="" data-attribute="col'+i+'">'+
						'</div>'+
						'<div class="fileaway-inline fileaway-half" id="fileaway-container-formaway_open_open_columns_col'+i+'class" data-col="'+i+'">'+
							'<div style="width:100%; text-align:right; margin: 2px 0 3px;">'+
								'<label for="formaway_open_open_col'+i+'class">'+
									'<span class="link-fileaway-help-colclass fileaway-helplink fileaway-help-iconinfo2" data-info="colclass"></span>Col '+i+' Class'+
								'</label>'+
							'</div>'+
							'<input class="fileaway-text " type="text" '+
								'id="formaway_open_open_col'+i+'class" name="formaway_open_open_col'+i+'class" placeholder="" value="" data-attribute="col'+i+'class">'+
						'</div>'+
						'<div class="fileaway-inline fileaway-half" id="fileaway-container-formaway_open_open_columns_col'+i+'type" data-col="'+i+'">'+
							'<div style="width:100%; text-align:right; margin: 2px 0 3px;">'+
								'<label for="formaway_open_open_col'+i+'type">'+
									'<span class="link-fileaway-help-coltype fileaway-helplink fileaway-help-iconinfo2" data-info="coltype"></span>Col '+i+' Type'+
								'</label>'+
							'</div>'+
							'<select id="formaway_open_open_col'+i+'type" class="select chozed-select" '+
								'data-placeholder="&nbsp;" name="formaway_open_open_col'+i+'type" data-attribute="col'+i+'type">'+
									'<option value=""></option>'+
									'<option value="">Alpha</option>'+
									'<option value="numeric">Numeric</option>'+
							'</select>'+
						'</div>'+
						'<div class="fileaway-inline fileaway-half" id="fileaway-container-formaway_open_open_columns_col'+i+'sort" data-col="'+i+'">'+
							'<div style="width:100%; text-align:right; margin: 2px 0 3px;">'+
								'<label for="formaway_open_open_col'+i+'sort">'+
									'<span class="link-fileaway-help-colsort fileaway-helplink fileaway-help-iconinfo2" data-info="colsort"></span>Col '+i+' Sort'+
								'</label>'+
							'</div>'+
							'<select id="formaway_open_open_col'+i+'sort" class="select chozed-select" '+
								'data-placeholder="&nbsp;" name="formaway_open_open_col'+i+'sort" data-attribute="col'+i+'sort">'+
									'<option value=""></option>'+
									'<option value="">Enabled</option>'+
									'<option value="ignore">Disabled</option>'+
							'</select>'+
						'</div>'						
					);
					$('select#formaway_open_open_col'+i+'type, select#formaway_open_open_col'+i+'sort').chozed({
						allow_single_deselect:true, 
						width: '100%', 
						disable_search_threshold: 5, 
						inherit_select_classes:true, 
						no_results_text: "Say what?", 
						search_contains: true, 
					});
				}
			}
			$('select#formaway_open_open_initialsort').trigger("chozed:updated");
		}, 1000 );
	});
	// Help Modals	
	$('body').on('click', 'span[class^="link-fileaway-help-"]', function()
	{
		var id = $(this).data('info');
		$('div#fileaway-help-'+id).fadeIn('fast');
	});
	$('div[id^="fileaway-help-"]').on('click', function()
	{
		$(this).fadeOut('fast');	
	});
	$('.fileaway-help-close').on('click', function()
	{
		$('div[id^="fileaway-help-"]:visible').fadeOut('fast');
	});
	$('.fileaway-help-content').on('click', function()
	{ 
		return false; 
	});
	$('.inner-link').on('click', function(ev)
	{ 
		ev.preventDefault(); 
		var url = $(this).attr('href'); 
		window.open(url, '_blank'); 
	});		
});
</script>