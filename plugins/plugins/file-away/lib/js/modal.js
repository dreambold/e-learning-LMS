(function()
{
   if(fileaway_mce_config.version === 'new')
   {
		tinymce.PluginManager.add('fileawaymodal', function(editor, url)
		{
			editor.addButton('fileawaymodal', 
			{
				title: fileaway_mce_config.tb_title,
				icon: 'icon fileaway-icon',
				onclick: function()
				{
					var width = jQuery(window).width(), H = jQuery(window).height(), W = (640 < width) ? 640 : width; W = W; H = H;
					tb_show('File Away', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=fileawaymodal-form');
            }
        });
    });
   }
   else
   {
		tinymce.create('tinymce.plugins.fileawaymodal', 
		{
			createControl : function(id, controlManager)
			{
				if(id == 'fileawaymodal')
				{
					var button = controlManager.createButton('fileawaymodal', 
					{
						title: fileaway_mce_config.tb_title, 
						image: fileaway_mce_config.button_img,
						onclick : function()
						{
							var width = jQuery(window).width(), H = jQuery(window).height(), W = (640 < width) ? 640 : width; W = W; H = H;
							tb_show('File Away', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=fileawaymodal-form');
						}
					});
					return button;
				}
				return null;
			}
		});
		tinymce.PluginManager.add('fileawaymodal', tinymce.plugins.fileawaymodal);
	}
})();
jQuery(function($)
{
    var table;
    var datat = {
		action: 'fileaway_tinymce',
		security: fileaway_mce_config.ajax_nonce
	};
    $.post( 
        fileaway_mce_config.ajax_url, 
        datat,                   
        function(response)
		{
			if('error' == response )
			{
				$('<div id="fileawaymodal-form"><h1 style="color:#c00;padding:100px 0;width:100%;text-align:center">Ajax error</h1></div>')
					.appendTo('body').hide();
            }
			else
			{
				form = $(response);
				table = form.find('table');
				form.appendTo('body').hide();
				form.find('#fileaway-shortcode-submit').click(fileaway_submit_shortcode);
			}
		}
	);
	function fileaway_submit_shortcode()
	{
		var stype = ''; var ttype = '';
		$('div[id^="options-container-"]').each(function()
		{
			if($(this).is(':visible'))
			{ 
				stype = $(this).data('sc');
				ttype = $(this).data('type');
				return false;
			}
		});
		if(stype == '') return false;
		var type = ttype !== 'table' ? '' : ' type="'+ttype+'"';
		var	shortcode = '['+stype+type;
		$('[id^="'+stype+'_'+ttype+'_"]').each(function()
		{
			var value = $(this).val();
			if(value != '' && value != ' ' && value != null)
			{
				var attr = $(this).data('attribute');
				shortcode += ' '+attr+'="'+value+'"';
			}
		});
		shortcode += ']';
		if(stype == 'formaway_row' || stype == 'formaway_cell')
		{ 
			var selected_content = tinyMCE.activeEditor.selection.getContent();
			if(!selected_content) selected_content = '<br /><br />';
			else 
			{
				selected_content = selected_content.replace(/\[[ ]*formaway_row[^\]]*\]/gi,'');
				selected_content = selected_content.replace(/\[[ ]*\/formaway_row\]/gi,'');	
				selected_content = selected_content.replace(/\[[ ]*formaway_cell[^\]]*\]/gi,'');
				selected_content = selected_content.replace(/\[[ ]*\/formaway_cell\]/gi,'');
			}
			shortcode += selected_content+"[/"+stype+"]";
		}
		tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		$('div[id^="options-container-"]').each(function()
		{
			$(this).hide();
			$(this).find('input').val('');    
			$('select.select', this).find('option:first').attr('selected','selected').trigger('chozed:updated');
			$('select.select option:selected', this).removeAttr("selected").trigger('chozed:updated');
		});
		$('select#fileaway_shortcode_select').find('option:first').attr('selected','selected').trigger('chozed:updated');
		$('select#fileaway_type_select').find('option:first').attr('selected','selected').trigger('chozed:updated');
		$('div#fileaway_type_select').hide();
		tb_remove();
    }
});	