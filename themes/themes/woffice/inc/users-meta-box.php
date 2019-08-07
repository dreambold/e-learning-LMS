<?php
// List Users
add_action("admin_init", "users_meta_init");

function users_meta_init()
{
    add_meta_box("users-meta", "Invite users", "invited_users", "offline-courses", "normal", "high");
    add_meta_box("users-meta", "Invite users", "invited_users", "webinars", "normal", "high");
}

function invited_users()
{
    global $post;
    $invited_users = get_post_meta($post->ID, '_invited_users', true);

// Create the WP_User_Query object
    $wp_user_query = new WP_User_Query([
        'orderby' => 'display_name'
    ]);
// Get the results
    $authors       = $wp_user_query->get_results();


// Check for results
    if (!empty($authors)) {
        // Name is your custom field key
        echo "<select name='invited_users[]' multiple='yes' size='15'>";
        // loop trough each author
        foreach ($authors as $author) {
            if (in_array($author->ID, $invited_users)) {
                echo "<option value='{$author->ID}' selected='selected'>{$author->first_name} {$author->last_name}</option>";
            } else {
                echo "<option value='{$author->ID}'>{$author->first_name} {$author->last_name}</option>";
            }
        }
        echo "</select>";
    } else {
        echo 'No authors found';
    }
}
// Save Meta Details
add_action('save_post', 'save_userlist');

function save_userlist()
{
    global $post;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post->ID;
    }

    
    update_post_meta($post->ID, "_invited_users", $_POST["invited_users"]);
}

//add_action('woffice_frontend_process_completed_success', 'save_userlist_front',10,3);
//
//function save_userlist_front($post_id, $post, $process_type){
//
//    if($post_id){
//         update_post_meta($post_id, "_invited_users", $_POST["invited_users"]);
//    }
//}