jQuery(document).ready(function(){
	// console.log(wdm_topic_object.select_lesson_text);
    
    jQuery('[name="sfwd-topic_lesson_assignment_upload"]').change(function(){
    checked = jQuery("[name=sfwd-topic_lesson_assignment_upload]:checked").length;
    if(checked) {
      jQuery("#sfwd-topic_auto_approve_assignment").slideDown();
      jQuery("#sfwd-topic_lesson_assignment_points_enabled").slideDown();

      jQuery("#sfwd-topic_assignment_upload_limit_extensions").slideDown();
      jQuery("#sfwd-topic_assignment_upload_limit_size").slideDown();

      // We uncheck the Video Progression option because we don't support Assignments and Videos
      if ( jQuery("input[name='sfwd-topic_lesson_video_enabled']").length ) {
        jQuery("input[name='sfwd-topic_lesson_video_enabled']").attr('checked', false);
        jQuery("#sfwd-topic_lesson_video_enabled").hide();
      }
    }
    else {
      jQuery("#sfwd-topic_auto_approve_assignment").slideUp();
      jQuery("#sfwd-topic_assignment_upload_limit_count").slideUp();
      
      jQuery("#sfwd-topic_lesson_assignment_points_enabled").slideUp();
      jQuery("#sfwd-topic_assignment_upload_limit_extensions").slideUp();
      jQuery("#sfwd-topic_assignment_upload_limit_size").slideUp();

      jQuery("[name='sfwd-topic_lesson_assignment_points_enabled']").prop('checked', false); 
      jQuery("[name='sfwd-topic_lesson_assignment_points_enabled']").change();

      if ( jQuery("input[name='sfwd-topic_lesson_video_enabled']").length ) {
        jQuery("#sfwd-topic_lesson_video_enabled").slideDown();
      }
    }
    
    if( jQuery("[name='sfwd-topic_auto_approve_assignment']").length ) {
      jQuery("[name='sfwd-topic_auto_approve_assignment']").change();
    }
    
  });
  
  if(jQuery("[name='sfwd-topic_lesson_assignment_upload']").length) {
    jQuery("[name='sfwd-topic_lesson_assignment_upload']").change();
  }
  
  jQuery('[name="sfwd-topic_lesson_assignment_points_enabled"]').change(function(){
    checked = jQuery("[name=sfwd-topic_lesson_assignment_points_enabled]:checked").length;
    if(checked) {
      jQuery("#sfwd-topic_lesson_assignment_points_amount").slideDown();
    } else {
      jQuery("#sfwd-topic_lesson_assignment_points_amount").slideUp();
      
      // Clear out the Points amount value
      jQuery("[name='sfwd-topic_lesson_assignment_points_amount']").val('0'); 
      
    }
  });
  
  if(jQuery("[name='sfwd-topic_lesson_assignment_points_enabled']").length) {
    jQuery("[name='sfwd-topic_lesson_assignment_points_enabled']").change();
  }


  jQuery('[name="sfwd-topic_auto_approve_assignment"]').change(function(){
    checked = jQuery("[name=sfwd-topic_lesson_assignment_upload]:checked").length;
    if ( checked ) {
      checked = jQuery("[name=sfwd-topic_auto_approve_assignment]:checked").length;
      if(checked) {
        jQuery("#sfwd-topic_assignment_upload_limit_count").slideUp();
        jQuery("#sfwd-topic_lesson_assignment_deletion_enabled").slideUp();
      } else {
        jQuery("#sfwd-topic_assignment_upload_limit_count").slideDown();
        jQuery("#sfwd-topic_lesson_assignment_deletion_enabled").slideDown();
      }
    } else {
      jQuery("#sfwd-topic_assignment_upload_limit_count").slideUp();
      jQuery("#sfwd-topic_lesson_assignment_deletion_enabled").slideUp();
    }
  });
  
  if(jQuery("[name='sfwd-topic_auto_approve_assignment']").length) {
    jQuery("[name='sfwd-topic_auto_approve_assignment']").change();
  }

var wdm_topic_video_handler = function() {
  checked = jQuery("[name=sfwd-topic_lesson_video_enabled]:checked").length;
    if(checked) {
      jQuery('#sfwd-topic_lesson_assignment_upload').hide();
      jQuery('#sfwd-topic_lesson_video_url').show();
      jQuery('#sfwd-topic_lesson_video_auto_start').show();
      jQuery('#sfwd-topic_lesson_video_show_controls').show();
      jQuery('#sfwd-topic_lesson_video_shown').show();
      if (jQuery('#sfwd-topic_lesson_video_shown select').val() == 'AFTER') {
        jQuery('#sfwd-topic_lesson_video_auto_complete').show();
        if (jQuery('input[name=sfwd-topic_lesson_video_auto_complete]:checked').length) {
          jQuery('#sfwd-topic_lesson_video_auto_complete_delay').show();
          jQuery('#sfwd-topic_lesson_video_hide_complete_button').show();
        }
      }
    }
    else {
      jQuery('#sfwd-topic_lesson_assignment_upload').show();
      jQuery('#sfwd-topic_lesson_video_url').hide();
      jQuery('#sfwd-topic_lesson_video_auto_start').hide();
      jQuery('#sfwd-topic_lesson_video_show_controls').hide();
      jQuery('#sfwd-topic_lesson_video_shown').hide();
      if (jQuery('#sfwd-topic_lesson_video_shown select').val() == 'AFTER') {
        jQuery('#sfwd-topic_lesson_video_auto_complete').hide();
        if (jQuery('input[name=sfwd-topic_lesson_video_auto_complete]:checked').length) {
          jQuery('#sfwd-topic_lesson_video_auto_complete_delay').hide();
          jQuery('#sfwd-topic_lesson_video_hide_complete_button').hide();
        }
      }
    }
  };

  // if (jQuery("[name=sfwd-topic_lesson_video_enabled]:checked").length) {
    wdm_topic_video_handler();
  // }
	jQuery("select[name=sfwd-topic_lesson_video_shown]").change(function(){
    if (jQuery(this).val() == 'AFTER') {
      jQuery('#sfwd-topic_lesson_video_auto_complete').show();
      if (jQuery('input[name=sfwd-topic_lesson_video_auto_complete]:checked').length) {
        jQuery('#sfwd-topic_lesson_video_auto_complete_delay').show();
        jQuery('#sfwd-topic_lesson_video_hide_complete_button').show();
      }
    } else {
      jQuery('#sfwd-topic_lesson_video_auto_complete').hide();
      if (jQuery('input[name=sfwd-topic_lesson_video_auto_complete]:checked').length) {
        jQuery('#sfwd-topic_lesson_video_auto_complete_delay').hide();
        jQuery('#sfwd-topic_lesson_video_hide_complete_button').hide();
      }
    }
   });
   jQuery("[name=sfwd-topic_lesson_video_auto_complete]").on('change', function(){
      if (jQuery("[name=sfwd-topic_lesson_video_auto_complete]:checked").length) {
        jQuery('#sfwd-topic_lesson_video_auto_complete_delay').show();
        jQuery('#sfwd-topic_lesson_video_hide_complete_button').show();
      } else {
        jQuery('#sfwd-topic_lesson_video_auto_complete_delay').hide();
        jQuery('#sfwd-topic_lesson_video_hide_complete_button').hide();
      }
   });
   jQuery("[name='sfwd-topic_lesson_video_enabled']").change(function(){
    wdm_topic_video_handler();
  });
	// jQuery("[name='sfwd-topic_assignment_points_enabled']").change(function(){
 //    assgn_points_checked = jQuery("[name='sfwd-topic_assignment_points_enabled']:checked").length;
 //    if (assgn_points_checked) {
 //      jQuery('#sfwd-topic_assignment_points_amount').show();
 //    } else {
 //      jQuery('#sfwd-topic_assignment_points_amount').hide();
 //    }
 //   });

 //   if (jQuery("[name='sfwd-topic_assignment_points_enabled']:checked").length > 0) {
 //    jQuery('#sfwd-topic_assignment_points_amount').show();
 //   };


		jQuery("select[name=sfwd-topic_course]").change(function() {
				if(window['sfwd_topic_lesson'] == undefined)
				window['sfwd_topic_lesson'] = jQuery("select[name=sfwd-topic_lesson]").val();

				jQuery("select[name=sfwd-topic_lesson]").html('<option>Loading...</option>');

				var data = {
					'action': 'wdm_select_a_lesson',
					'course_id': jQuery(this).val()
				};

				$course_id = jQuery(this).val();

				// console.log(wdm_topic_data.admin_url);
				// console.log(data);
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(wdm_topic_data.admin_url, data, function(json) {
					window['response'] = json;
					html  = '<option value="0">'+wdm_topic_object.select_lesson_text+'</option>';
					jQuery.each(json, function(key, value) {
						if(key != '' && key != '0')
						{
							selected = (key == window['sfwd_topic_lesson'])? 'selected=selected': '';
							html += "<option value='" + key + "' "+ selected +">" + value + "</option>";				
						}
					});
					jQuery("select[name=sfwd-topic_lesson]").html(html);
					//jQuery("select[name=sfwd-topic_lesson]").val(window['sfwd_topic_lesson']);
				}, "json");
		});
                jQuery("select[name=sfwd-topic_course]").change();
                jQuery('*').on('hover',function(){
           // console.log('asdasd');
        jQuery('.media-menu a:nth-child(5)').remove();

        });
    
});

function toggleVisibility(id) {
	var e = document.getElementById(id);
	if (e.style.display == 'block')
		e.style.display = 'none';
	else
		e.style.display = 'block';
}