jQuery('document').ready(function(){
    var course_update = false;
    var quiz_update = false;

    // Ajax Course Builder Update
    jQuery('form#wdm_course_form').submit(function(event){
        // Close Builder accordion
        jQuery('#learndash_builder_box_wrap').parents('.ui-accordion-content').slideUp();
        // Show Loader
        jQuery('.fcc-ajax-overlay').show();
        // Save before unmounting
        if (! course_update) {
            event.preventDefault();
            if (wdm_builder_data.ld_data.rest.root.length) {
                var builder_data = jQuery('#learndash_builder_data').val();

                jQuery.ajax({
                    url : wdm_builder_data.ajax_url,
                    type : 'POST',
                    data : {
                        'action'    : 'update_course_builder',
                        'course_id' : wdm_builder_data.ld_data.post_data.builder_post_id,
                        'builder_data' : builder_data,
                        'learndash_builder_nonce' : jQuery('#learndash_builder_nonce').val()
                    },
                    success: function(response) {
                        course_update = true;
                        jQuery('.fcc-ajax-overlay').hide();
                        jQuery('form#wdm_course_form').submit();
                    }
                });
            }
        } else {
            // Unmount
		    ReactDOM.unmountComponentAtNode(document.getElementById('learndash_builder_box_wrap'));
        }
    });

    // Ajax Quiz Builder Update
    jQuery('form#wdm_quiz_form').submit(function(event){
        // Close Builder accordion
        jQuery('#learndash_builder_box_wrap').parents('.ui-accordion-content').slideUp();
        // Show ajax loader
        jQuery('.fcc-ajax-overlay').show();
        // Save before unmounting
        if (! quiz_update) {
            event.preventDefault();
            if (wdm_builder_data.ld_data.rest.root.length) {
                var builder_data = jQuery('#learndash_builder_data').val();

                jQuery.ajax({
                    url : wdm_builder_data.ajax_url,
                    type : 'POST',
                    data : {
                        'action'    : 'update_quiz_builder',
                        'quiz_id' : wdm_builder_data.ld_data.post_data.builder_post_id,
                        'builder_data' : builder_data,
                        'learndash_builder_nonce' : jQuery('#learndash_builder_nonce').val()
                    },
                    success: function(response) {
                        quiz_update = true;
                        jQuery('.fcc-ajax-overlay').hide();
                        jQuery('form#wdm_quiz_form').submit();
                    }
                });
            }
        } else {
            // Unmount
		    ReactDOM.unmountComponentAtNode(document.getElementById('learndash_builder_box_wrap'));
        }
    });
});