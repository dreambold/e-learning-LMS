 jQuery(document).ready(function(){
   jQuery("input[name='sfwd-lessons_visible_after_specific_date']").datetimepicker();
   jQuery("input[name='sfwd-lessons_lesson_assignment_upload']").change(function(){
    checked = jQuery("input[name=sfwd-lessons_lesson_assignment_upload]:checked").length;
    if(checked) {
      jQuery("#sfwd-lessons_auto_approve_assignment").slideDown();
      jQuery("#sfwd-lessons_lesson_assignment_points_enabled").slideDown();
      
      jQuery("#sfwd-lessons_assignment_upload_limit_extensions").slideDown();
      jQuery("#sfwd-lessons_assignment_upload_limit_size").slideDown();
      
      //jQuery("#sfwd-lessons_lesson_assignment_points_amount").show();
      jQuery("input[name='sfwd-lessons_lesson_assignment_points_enabled']").change();
      
      // We uncheck the Video Progression option because we don't support Assignments and Videos
      if ( jQuery("input[name='sfwd-lessons_lesson_video_enabled']").length ) {
        jQuery("input[name='sfwd-lessons_lesson_video_enabled']").attr('checked', false);
        jQuery("#sfwd-lessons_lesson_video_enabled").hide();
      }
      jQuery("#sfwd-lessons_lesson_assignment_deletion_enabled").slideDown();
      
    }
    else {
      jQuery("#sfwd-lessons_auto_approve_assignment").slideUp();
      jQuery("#sfwd-lessons_lesson_assignment_points_enabled").slideUp();
      jQuery("#sfwd-lessons_assignment_upload_limit_extensions").slideUp();
      jQuery("#sfwd-lessons_assignment_upload_limit_size").slideUp();
      jQuery("#sfwd-lessons_lesson_assignment_deletion_enabled").slideUp();
      
      // We force the checkbox for 'Award Points for Assignment' to false then trigger the logic to hide the sub-input element(s)
      jQuery("input[name='sfwd-lessons_lesson_assignment_points_enabled']").attr('checked', false); 
      
      //jQuery("#sfwd-lessons_lesson_assignment_points_amount").hide();
      jQuery("input[name='sfwd-lessons_lesson_assignment_points_enabled']").change();
      
      if ( jQuery("input[name='sfwd-lessons_lesson_video_enabled']").length ) {
        jQuery("#sfwd-lessons_lesson_video_enabled").slideDown();
      }
    }
    
    if( jQuery("[name='sfwd-lessons_auto_approve_assignment']").length ) {
      jQuery("[name='sfwd-lessons_auto_approve_assignment']").change();
    }
    
  });
  if(jQuery("input[name='sfwd-lessons_lesson_assignment_upload']").length) {
    jQuery("input[name='sfwd-lessons_lesson_assignment_upload']").change();
  }


  jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']").change(function(){
    checked = jQuery("[name=sfwd-lessons_lesson_assignment_points_enabled]:checked").length;
    if(checked) {
      jQuery("#sfwd-lessons_lesson_assignment_points_amount").slideDown();
    }
    else {
      jQuery("#sfwd-lessons_lesson_assignment_points_amount").slideUp();
      
      // Clear out the Points amount value
      jQuery("[name='sfwd-lessons_lesson_assignment_points_amount']").val('0'); 
    }
  });
  if(jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']").length) {
    jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']").change();
  }


  jQuery('[name="sfwd-lessons_auto_approve_assignment"]').change(function(){
    checked = jQuery("[name=sfwd-lessons_lesson_assignment_upload]:checked").length;
    if ( checked ) {
      checked = jQuery("[name=sfwd-lessons_auto_approve_assignment]:checked").length;
      if(checked) {
        jQuery("#sfwd-lessons_assignment_upload_limit_count").slideUp();
        jQuery("#sfwd-lessons_lesson_assignment_deletion_enabled").slideUp();
      } else {
        jQuery("#sfwd-lessons_assignment_upload_limit_count").slideDown();
        jQuery("#sfwd-lessons_lesson_assignment_deletion_enabled").slideDown();
      }
    } else {
      jQuery("#sfwd-lessons_assignment_upload_limit_count").slideUp();
      jQuery("#sfwd-lessons_lesson_assignment_deletion_enabled").slideUp();
    }
  });
  if(jQuery("[name='sfwd-lessons_auto_approve_assignment']").length) {
    jQuery("[name='sfwd-lessons_auto_approve_assignment']").change();
  }
   var wdm_lesson_video_handler = function() {
    checked = jQuery("[name=sfwd-lessons_lesson_video_enabled]:checked").length;
    if(checked) {
      jQuery('#sfwd-lessons_lesson_assignment_upload').hide();
      jQuery('#sfwd-lessons_lesson_video_url').show();
      jQuery('#sfwd-lessons_lesson_video_auto_start').show();
      jQuery('#sfwd-lessons_lesson_video_show_controls').show();
      jQuery('#sfwd-lessons_lesson_video_shown').show();
      if (jQuery('#sfwd-lessons_lesson_video_shown select').val() == 'AFTER') {
        jQuery('#sfwd-lessons_lesson_video_auto_complete').show();
        if (jQuery('input[name=sfwd-lessons_lesson_video_auto_complete]:checked').length) {
          jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').show();
          jQuery('#sfwd-lessons_lesson_video_hide_complete_button').show();
        }
      }
    }
    else {
      jQuery('#sfwd-lessons_lesson_assignment_upload').show();
      jQuery('#sfwd-lessons_lesson_video_url').hide();
      jQuery('#sfwd-lessons_lesson_video_auto_start').hide();
      jQuery('#sfwd-lessons_lesson_video_show_controls').hide();
      jQuery('#sfwd-lessons_lesson_video_shown').hide();
      if (jQuery('#sfwd-lessons_lesson_video_shown select').val() == 'AFTER') {
        jQuery('#sfwd-lessons_lesson_video_auto_complete').hide();
        if (jQuery('input[name=sfwd-lessons_lesson_video_auto_complete]:checked').length) {
          jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').hide();
          jQuery('#sfwd-lessons_lesson_video_hide_complete_button').hide();
        }
      }
    }
  };

  jQuery('#wdm_lesson_submit').click(function(){
    var selected_dated=new Date(jQuery('input[name="sfwd-lessons_visible_after_specific_date"]').val()).getTime();
    if(parseInt(Date.now()) > parseInt(selected_dated)){
      alert(wdm_lesson_data.visible_date_validation);
      return false;
    }
  });
  wdm_lesson_video_handler();
   jQuery("select[name=sfwd-lessons_lesson_video_shown]").on('change', function(){
    if (jQuery(this).val() == 'AFTER') {
      jQuery('#sfwd-lessons_lesson_video_auto_complete').show();
      if (jQuery('input[name=sfwd-lessons_lesson_video_auto_complete]:checked').length) {
        jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').show();
        jQuery('#sfwd-lessons_lesson_video_hide_complete_button').show();
      }
    } else {
      jQuery('#sfwd-lessons_lesson_video_auto_complete').hide();
      if (jQuery('input[name=sfwd-lessons_lesson_video_auto_complete]:checked').length) {
        jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').hide();
        jQuery('#sfwd-lessons_lesson_video_hide_complete_button').hide();
      }
    }
   });
   jQuery("[name=sfwd-lessons_lesson_video_auto_complete]").on('change', function(){
      if (jQuery("[name=sfwd-lessons_lesson_video_auto_complete]:checked").length) {
        jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').show();
        jQuery('#sfwd-lessons_lesson_video_hide_complete_button').show();
      } else {
        jQuery('#sfwd-lessons_lesson_video_auto_complete_delay').hide();
        jQuery('#sfwd-lessons_lesson_video_hide_complete_button').hide();
      }
   });
   jQuery("[name='sfwd-lessons_lesson_video_enabled']").change(function(){
    wdm_lesson_video_handler();
  });

   

   jQuery("[name='sfwd-lessons_visible_after']").click(function(){
    if(jQuery("input[name='sfwd-lessons_visible_after_specific_date']").val().length != 0){
      jQuery(this).attr('disabled','disabled');
    }else{
      jQuery(this).removeAttr('disabled');
    }
   });

   jQuery(document).on('click','form', function(){
    if(jQuery("[name='sfwd-lessons_visible_after']").val().length != 0){
      jQuery("[name='sfwd-lessons_visible_after_specific_date']").attr('disabled','disabled');
      jQuery('.input-group-addon').hide();
    }
   });

   jQuery("[name='sfwd-lessons_visible_after']").focusout(function(){
    if(jQuery(this).val().length==0){
      jQuery("[name='sfwd-lessons_visible_after_specific_date']").removeAttr('disabled');
      jQuery('.input-group-addon').show();
    }else{
      jQuery("[name='sfwd-lessons_visible_after_specific_date']").attr('disabled','disabled');
      jQuery('.input-group-addon').hide();
    }
   });
   jQuery("[name='sfwd-lessons_visible_after_specific_date']").focusout(function(event){
    if(jQuery(this).val().length == 0){
      jQuery("[name='sfwd-lessons_visible_after']").removeAttr('disabled');
    }else{
      jQuery("[name='sfwd-lessons_visible_after']").attr('disabled','disabled');
    }
   });

  //  if(wdm_lesson_data.is_new=='1'){
  //     if(jQuery("[name='sfwd-lessons_lesson_assignment_upload']"))
  //     jQuery("[name='sfwd-lessons_lesson_assignment_upload']").change();

  //   if(jQuery("[name='sfwd-lessons_auto_approve_assignment']"))
  //     jQuery("[name='sfwd-lessons_auto_approve_assignment']").change();
  // }else{
  //   if(jQuery("[name='sfwd-lessons_auto_approve_assignment']"))
  //     jQuery("[name='sfwd-lessons_auto_approve_assignment']").change();

  //     if(jQuery("[name='sfwd-lessons_lesson_assignment_upload']"))
  //     jQuery("[name='sfwd-lessons_lesson_assignment_upload']").change();
  // }

    // load_datepicker();	
        // var date_picker = jQuery( "#wdm_datetimepicker" ).datetimepicker({
        //       weekStart: 1,
        //       todayBtn:  1,
        //       autoclose: 1,
        //       todayHighlight: 1,
        //       startView: 2,
        //       forceParse: 0,
        // });
        // var firstOpen=true;
        // date_picker.on("dp.show", function(){
        //   if (firstOpen==true){
        //       jQuery(this).data('DateTimePicker').date(jQuery("#dtp_input").val());
        //       firstOpen=false;
        //   }
        // });
    function load_datepicker(){
        
        // jQuery("input[name='sfwd-lessons_visible_after_specific_date']").blur(function() {
        //     var specific_data = jQuery("input[name='sfwd-lessons_visible_after_specific_date']").val();
        //     if( specific_data != '') {
        //     jQuery("input[name='sfwd-lessons_visible_after']").val('0');
        //    jQuery("input[name='sfwd-lessons_visible_after']").attr("disabled", "disabled");
        //    }else {
        //      jQuery("input[name='sfwd-lessons_visible_after']").removeAttr("disabled");
        //    }
        // });
        // jQuery("input[name='sfwd-lessons_visible_after']").click(function() {
        //      var specific_data = jQuery("input[name='sfwd-lessons_visible_after_specific_date']").val();
        //     if( specific_data != '') {
        //     jQuery("input[name='sfwd-lessons_visible_after']").val('0');
        //    jQuery("input[name='sfwd-lessons_visible_after']").attr("disabled", "disabled");
        //    }else {
        //      jQuery("input[name='sfwd-lessons_visible_after']").removeAttr("disabled");
        //    }
        //     });
        
    }
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