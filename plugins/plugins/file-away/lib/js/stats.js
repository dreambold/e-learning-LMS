jQuery(document).ready(function($)
{
	$('body').on('contextmenu', 'div.ssfa-meta-container a[data-stat="true"], div#ssfa-flightbox a[data-stat="true"]', function()
	{
		return false;
	});
	$('body').on('click', 'div.ssfa-meta-container a[data-stat="true"], div#ssfa-flightbox a[data-stat="true"]', function(ev)
	{
		if($(this).data('stat'))
		{
			ev.preventDefault();
			var href = $(this).attr('href');
			var type = 'default';
			if(href.indexOf('?s2member') >= 0) type = 's2member';
			else if(href.indexOf('?fileaway') >= 0) type = 'encrypted';
			var shadow = $('<div id="ssfa-stats-shadow" style="display:none;" />');
			$('body').append(shadow);
			shadow.fadeIn(1000);
			$("body").css("cursor", "progress");			
			$.post(
				fileaway_stats.ajaxurl,
				{
					action : 'fileaway-stats',
					dataType : 'html',	
					act : 'insert',
					file : href,
					type : type,
					nonce : fileaway_stats.nonce
				},
				function(response)
				{
					$("body").css("cursor", "auto");
					shadow.fadeOut(500).queue(function(){
						$(this).remove();
					});
					if(response != 'error') window.location = response;
				}
			);
			return false;
		}
	});
	$('span[id^="stataway-refresh-"]').on('click', function()
	{
		$b = $('input#stataway-fsb').val();
		$e = $('input#stataway-fse').val();	
		if($b == '' || $e == '') filertify.alert('Please select a beginning and an end date.');
		else
		{
			var shadow = $('<div id="ssfa-stats-shadow" style="display:none;" />');
			$('body').append(shadow);
			shadow.fadeIn(1000);
			$("body").css("cursor", "progress");
			$q = $(this).data('url').indexOf('?') >= 0 ? '&' : '?';
			window.location = $(this).data('url')+$q+"fsb="+$b+"&fse="+$e;	
		}
	});
});