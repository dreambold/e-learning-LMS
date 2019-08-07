<?php
/**
 * BuddyPress for Learndash Uninstall
 *
 * Uninstalling BuddyPress for Learndash deletes connection between courses and groups,
 * course activities, and options.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Perform uninstall clean-up
 */
function bp_learndash_uninstall_plugin() {
    global $wpdb;

    // Load BuddyPres
    include_once( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );

    $bp_prefix = bp_core_get_table_prefix();

    // Delete course id from group meta
    $wpdb->query("DELETE FROM {$bp_prefix}bp_groups_groupmeta WHERE meta_key = 'bp_course_attached'");

    // Delete group id from course meta
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'bp_course_group'");

    // Delete course activities
    $wpdb->query("DELETE FROM {$bp_prefix}bp_activity WHERE type IN ('started_course', 'created_lesson', 'created_topic', 'completed_lesson', 'completed_topic', 'completed_course', 'lesson_comment', 'course_comment', 'completed_quiz')");

    // Delete plugin options
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'buddypress_learndash_plugin_options'");
}

if ( is_multisite() ) {
    global $wpdb;

    /*
     * Multisite uninstall
     */
    $blogs = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs", ARRAY_A );
    if ( $blogs ) {
        foreach ( $blogs as $blog ) {
            switch_to_blog( $blog['blog_id'] );
            bp_learndash_uninstall_plugin();
        }
        restore_current_blog();
    }

    // Delete plugin options from the sitemeta
    $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE meta_key = 'buddypress_learndash_plugin_options'");

    // Single site uninstall
} else {
    bp_learndash_uninstall_plugin();
}