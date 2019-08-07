<form id='sfwd-mark-complete' method='post' action=''>
    <input type='hidden' value='<?php echo $post->ID; ?>' name='post'/>
<input type='hidden' value='<?php echo wp_create_nonce( 'sfwd_mark_complete_'. get_current_user_id() .'_'. $post->ID ) ?>' name='sfwd_mark_complete'/>
</form>