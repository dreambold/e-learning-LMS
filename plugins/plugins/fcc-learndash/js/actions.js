jQuery(document).ready(function(){
	jQuery(document).on('click', '.wdm_trash', function(){
        var parent_row=jQuery(this).parent().parent().parent();
        var post_title=parent_row.find('td').first().text();
        if(confirm(post_title+' '+wdm_ajax_object.trash_warning)){
            var current=jQuery(this);
            var parent=current.parent();
            parent.find('.wdm_edit').hide();
            parent.find('.wdm_trash').hide();
            parent.find('.wdm_view').hide();
            parent.find('.wdm_view_lessons').hide();
            parent.find('.wdm_view_quizzes').hide();
            var post_id=current.data('post_id');
            jQuery.ajax({
                type : "post",
                url : wdm_ajax_object.ajax_url,
                data : {
                    action: 'wdm_move_to_trash',
                    post_id: post_id,
                },
                success: function(response) {
                    parent.find('.wdm_remove_trash').show();
                    alert(response);
                }
            });
        }
    });
    jQuery(document).on('click', '.wdm_remove_trash', function(){
        var current=jQuery(this);
        var parent=current.parent();
        parent.find('.wdm_remove_trash').hide();
        var post_id=current.data('post_id');
        jQuery.ajax({
            type : "post",
            url : wdm_ajax_object.ajax_url,
            data : {
                action: 'wdm_undo_trash',
                post_id: post_id,
            },
            success: function(response) {
                alert(response);
                parent.find('.wdm_edit').show();
                parent.find('.wdm_trash').show();
                parent.find('.wdm_view').show();
                parent.find('.wdm_view_lessons').show();
                parent.find('.wdm_view_quizzes').show();
            }
        });
    });

    jQuery(document).on('click', '.wdm_delete', function(){
        var post_id=jQuery(this).data('post_id');
        var parent_row=jQuery(this).parent().parent();
        var post_title=parent_row.find('td').first().text();
        if(confirm(post_title+' '+wdm_ajax_object.removeQuestionsWarning)){
            jQuery.ajax({
                type : "post",
                url : wdm_ajax_object.ajax_url,
                data : {
                    action: 'wdm_delete_question',
                    post_id: post_id,
                },
                success: function(response) {
                    parent_row.hide();
                }
            });
        }
    });

    jQuery('#wdm_courses_filter').change(function(){
        console.log(jQuery(this).val());
        if(jQuery(this).val()!=''){
            location.assign(location.protocol + '//' + location.host + location.pathname+'?courseid='+jQuery(this).val());
        }else{
            location.assign(location.protocol + '//' + location.host + location.pathname);
        }
    });
});