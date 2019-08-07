jQuery(document).ready(function(){
    jQuery('#wdm_fcc_report_tbl').dataTable();
    jQuery('.update_commission').click(function(e){
       jQuery(this).parent().find('.wdm_ajax_loader').show();
        e.preventDefault();
        var update_commission = jQuery(this);
        var anchor_tag_name = jQuery( this ).attr( 'name' );
        var arr = anchor_tag_name.split( '_' );
        var course_author_id = arr[1];
        var value = jQuery( '#input_' + course_author_id ).val();
//        If Update link's name is not valid, course_author id is less than 0 or greater than 100 then return with message
        if ( jQuery.trim( anchor_tag_name ).length == 0 || jQuery.trim( course_author_id ).length == 0 || jQuery.trim( value ).length == 0 || value > 100 || value < 0 ) {
            alert(wdm_commission_data.invalid_percentage);
            jQuery(this).parent().find('.wdm_ajax_loader').hide();
            return false;
        }
        // If everything is proper then involve ajax call to update commission
        jQuery.ajax( {
            type: 'post',
            url: wdm_commission_data.ajax_url,
            async : false,
            data: {
                action: 'wdm_update_course_author_commission',
                commission: value,
                course_author_id: course_author_id
            },
            success: function ( response ) {
                jQuery( '#input_' + course_author_id ).attr('value',value);
                update_commission.parent().find('.wdm_ajax_loader').hide();
                alert(response);
                

            }
        } );
   });
});