jQuery(document).ready(function($)
{
	// FLASH BODY WIDTH FOR WEB FONTS
    setTimeout(function(){
        $('body').width($('body').width()+1).width('auto');
    }, 500);	
	// TABS
	$('a[id^="fileaway-tab-"]').on('click', function(ev)
	{
		ev.preventDefault();
		var slug = $(this).attr('data-tab');
		var panel = $('div#fileaway-panel-'+slug);
		if(panel.is(':visible')){}
		else
		{
			$('li.'+slug).addClass('state-active').siblings('li').removeClass('state-active');
			$('div[id^="fileaway-panel-"]').fadeOut(500);
			panel.delay(500).fadeIn(500);
			if(slug == 'customcss' && CodeMirror)
			{
				setTimeout(function()
				{
					CodeMirror.refresh();
				}, 500);
			}
		}		
	});
	// CHOSEN
	$('select#baseurl, select#manager_role_access, select#manager_user_access, select#ignore_roles, select#ignore_users').chozed({width: '450px'});
	$('select.chozed-select').chozed({
		allow_single_deselect:true, 
		width: '200px', 
		inherit_select_classes:true,
		no_results_text: "Say what?",
		search_contains: true, 
	});
	// MANAGER MODE STUFF
	$('select#manager_role_access').on('change', function()
	{
		var roleaccess_selected = []; 
		$(this).each(function(i, selected)
		{ 
			roleaccess_selected[i] = $(selected).val(); 
		});
		$('input#manager_role_access').val(roleaccess_selected);
	});
	$('select#manager_user_access').on('change', function()
	{
		var useraccess_selected = []; 
		$(this).each(function(i, selected)
		{ 
			useraccess_selected[i] = $(selected).val(); 
		});
		$('input#manager_user_access').val(useraccess_selected);
	});	
	// STATISTICS STUFF
	$('select#ignore_roles').on('change', function()
	{
		var roleaccess_selected = []; 
		$(this).each(function(i, selected)
		{ 
			roleaccess_selected[i] = $(selected).val(); 
		});
		$('input#ignore_roles').val(roleaccess_selected);
	});
	$('select#ignore_users').on('change', function()
	{
		var useraccess_selected = []; 
		$(this).each(function(i, selected)
		{ 
			useraccess_selected[i] = $(selected).val(); 
		});
		$('input#ignore_users').val(useraccess_selected);
	});
	// BASE DIRECTORY STUFF
	$('input[id^=base]').each(function() 
	{
    	var idSuffix = this.id,
	       	i = $(this),
	        s = $('#fileaway-abspath-' + idSuffix),
	        w = $('#fileaway-wrap-' + idSuffix),		
	        e = $('#fileaway-error-' + idSuffix),
	        rx = /^(wp-admin|\/wp-admin|wp-includes|\/wp-includes)/i;
		i.on('focus', function() 
		{
			w.addClass('fileaway-focus');
		});
		i.on('blur', function() 
		{
			w.removeClass('fileaway-focus');
		});
		s.on('click', function() 
		{
			i.focus();
		});
		w.on('click', function() 
		{
			i.focus();
		});
	    i.on('keyup', function() 
		{
	        var test = rx.test(i.val());
	        w.toggleClass('fileaway-error', test);
	        if(test) e.show(600);
	        else e.hide(600);
	    });
	});	
	// PLACEHOLDER STUFF
	$("input[type=text], textarea").each(function()
	{
		if ($(this).val() === $(this).attr("placeholder") || $(this).val() === "") $(this).css({color:"#BBBBBB"});
	});
	$("input[type=text], textarea").on('focus', function()
	{
		if($(this).val() == $(this).attr("placeholder") || $(this).val() === "")
		{
			$(this).val("");
			$(this).css("color", "#666666");
		}
	}).blur(function()
	{
		if($(this).val() == "" || $(this).val() == $(this).attr("placeholder"))
		{
			$(this).val($(this).attr("placeholder"));
			$(this).css("color", "#BBBBBB");
		}
	});
	// WARNING STYLE
	$("select#reset_options").on('change', function()
	{
		$info = $("span.link-fileaway-help-reset_options");
		if($(this).val() === 'reset') $info.css({color:'orange','font-size':'22px'});
		else $info.css({'color':'#A6A29E','font-size':'15px'});
	});
	// ADD ANOTHER BASEFEED
	$('span#fileaway_add_new_basefeeds, span#fileaway_add_new_excluded_feeds').on('click', function()
	{
		var feedtype = $(this).attr('id').replace('fileaway_add_new_', '');
		$feednumber = 1;
		$('div[id^="fileaway-wrap-'+feedtype+'_"]').each(function()
		{
			$current_id = parseInt($(this).data('feed'));
			if($current_id >= $feednumber) $feednumber = ($current_id+1);
		});
		$rootpath = $('span#fileaway-abspath-'+feedtype+'_'+$current_id).text();
		$x = $feednumber;
		$newinput = $('<div id="fileaway-wrap-'+feedtype+'_'+$x+'" data-feed="'+$x+'" class="fileaway-wrap-base fileaway-subsequent">'+
						'<span id="fileaway-abspath-'+feedtype+'_'+$x+'" class="fileaway-abspath">'+$rootpath+'</span> '+
						'<input class="regular-text fileaway-basedir fileaway-feeds fileaway-inline" type="text" id="'+feedtype+'_'+$x+'" name="fileaway_options['+feedtype+'][]" value="" />'+
					'</div>');
		$('div#fileaway-container-'+feedtype+'').append($newinput);
	});
	// SAVE SETTINGS
	$('span.fileaway-save-settings').on('click', function()
	{
		var settings = {}; 
		settings['basefeeds'] = {};
		settings['excluded_feeds'] = {};
		var frm = $("#fileaway-form"),
			svn = $("#fileaway-saving"),
			bck = $("#fileaway-saving-backdrop"),
			img = $("#fileaway-saving-img"),
			svd = $("#fileaway-settings-saved");
		img.css({'bottom' : '-100px'});
		svn.fadeIn('slow');
		bck.fadeIn('fast');
		img.fadeIn('slow').css({'bottom' : '50px', 'transition' : 'all 1s ease-out'});
		if(CodeMirror) CodeMirror.save();
		$('div#fileaway-options-container [placeholder]').each(function()
		{
			var input = $(this);
			if(input.val() == input.attr('placeholder')) input.val('');
		})
		$('input[id^=base]').each(function()
		{
			var i = $(this);
			var rx = /^(wp-admin|\/wp-admin|wp-includes|\/wp-includes)/i;
			var check = rx.test(i.val());
	        if(check) i.val('');
		});
		$("input#custom_list_classes").val(function(i, val)
		{
		  return val.replace(/ssfa-/g,"");
		});
		$("input#custom_table_classes").val(function(i, val)
		{
		  return val.replace(/ssfa-/g, '');
		});
		$("input#custom_color_classes").val(function(i, val)
		{
		  return val.replace(/ssfa-/g, '');
		});
		$("input#custom_accent_classes").val(function(i, val)
		{
		  return val.replace(/accent-/g, '');
		});									
		$("input#manager_user_access").val(function(i, val)
		{
			return val.replace(/\s/g, '');
		});
		var basefeeds = 0;
		var excluded_feeds = 0;
		$('div#fileaway-options-container [name^="fileaway_options"]').each(function(i)
		{
			if(this.id.indexOf('basefeeds') >= 0)
			{ 
				$feeddir = $(this).val();
				if($.trim($feeddir) !== '')
				{
					settings['basefeeds'][basefeeds] = $(this).val();
					basefeeds++;
				}
			}
			if(this.id.indexOf('excluded_feeds') >= 0)
			{ 
				$feeddir = $(this).val();
				if($.trim($feeddir) !== '')
				{
					settings['excluded_feeds'][excluded_feeds] = $(this).val();
					excluded_feeds++;
				}
			}
			else settings[this.id] = $(this).val();
		});
		var data = { action : 'fileaway_save', settings : settings, nonce : fileaway_admin_ajax.nonce };
		$.post(fileaway_admin_ajax.ajaxurl, data, function(response)
		{
			svn.fadeOut('slow');
			img.delay(2000).queue(function(next)
			{
				$(this).css({'bottom' : '2400px', 'transition' : 'all 4.5s ease-in'}); next();
			});
			svd.delay(1000).fadeIn('slow').delay(2500).fadeOut('slow');
			bck.delay(4500).fadeOut('slow'); 
			if(response == 'reload')
			{
				setTimeout(function(){ location.reload(true); }, 4000);
			}
		}); 
	});
	// Tutorials Sections
	$('select#fileaway-tutorials').on('change', function(){
		$selection = $(this).val();
		$allcontent = $('div[id^="fileaway-tutorials-"]');
		if($selection == '') $allcontent.fadeOut(500);
		else 
		{
			$content = $('div#fileaway-tutorials-'+$selection);	
			if(false == $content.is(':visible'))
			{
				$allcontent.fadeOut(500);	
				$content.delay(500).fadeIn(500);
			}
		}
	});
	// ACCORDION STUFF
	$(".fileaway-accordion > dt").on('click', function()
	{
    	$('.fileaway-accordion-active').removeClass('fileaway-accordion-active');
	    if(false == $(this).next().is(':visible')) 
		{
	        $(this).addClass('fileaway-accordion-active');
	        $('.fileaway-accordion > dd').slideUp(600);
	    }
	    $(this).next().slideToggle(600);
	});
	// Info Links
	var	con = $('.fileaway-help-content');
	$('div[id^=fileaway-help-]').each(function() {
		var sfx = this.id,
			mdl = $(this),
			cls = $('.fileaway-help-close'),			
			lnk = $('.link-' + sfx);
		lnk.click(function(){
			mdl.fadeIn('fast');
		});
		mdl.click(function() {
			mdl.fadeOut('fast');
		});
		cls.click(function(){
			mdl.fadeOut('fast');
		});
	});
	con.click(function() {
		return false;
	});
	// Remove Update Notice after 10 Seconds
	setTimeout(function() {	$("div.updated, div.update-nag").fadeOut(600);	}, 10000);	
});