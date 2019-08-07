<?php
/* Plugin Name: Frontend Course Creation
 * Plugin URI: https://wisdmlabs.com/front-end-course-creation-for-learndash/
 * Description: The plugin creates a user role 'Course Author' with privileges to add a course, lesson, topic or quiz from the front-end.
 * Version: 2.2.1
 * Author: Wisdmlabs
 * Author URI: http://wisdmlabs.com/
 * Text Domain: fcc
 * Domain Path: /languages
 * */

if (session_id() == '') {
    session_start();
}
if (!defined('FCC_PLUGIN_VERSION')) {
    define('FCC_PLUGIN_VERSION', '2.2.1');
}

/**
 * Define plugin file path constant
 *
 * @since 2.2.0
 */
if (!defined('FCC_PLUGIN_PATH')) {
    define('FCC_PLUGIN_PATH', __FILE__);
}

if (!defined('FCC_PLUGIN_DIR_PATH')) {
    define('FCC_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}
//
global $fccPluginData;
load_plugin_textdomain('fcc', false, dirname(plugin_basename(__FILE__)).'/languages');

add_action('admin_init', 'wdm_fcc_admin_activation');

if (!function_exists('wdm_fcc_has_permissions')) {
    function wdm_fcc_has_permissions()
    {
        global $current_user;
        if (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
            return true;
        }
        return false;
    }
}

function wdm_fcc_admin_activation()
{
    if (!is_plugin_active('sfwd-lms/sfwd_lms.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET[ 'activate' ]);
        add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
    }

    if (is_multisite()) {
        if (!function_exists('is_plugin_active_for_network')) {
            include_once ABSPATH.'/wp-admin/includes/plugin.php';
        }
        if (is_plugin_active_for_network('fcc-learndash/fcc.php')) {
            add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
        }
    } else {
        if (!is_plugin_active('buddypress/bp-loader.php')) {
            deactivate_plugins(plugin_basename(__FILE__));
            unset($_GET[ 'activate' ]);
            add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
        }
    }
}

function wdm_fcc_my_plugin_admin_notices()
{
    if (!is_plugin_active('sfwd-lms/sfwd_lms.php')) {
        ?>
        <div class='error'><p>
                <?php echo __("LearnDash LMS plugin is not active. In order to make the 'Frontend Course Creation' plugin work, you need to install and activate LearnDash LMS first.", 'fcc');
        ?>
            </p></div>

        <?php
    }
    if (!is_plugin_active('buddypress/bp-loader.php')) {
        if (!is_plugin_active_for_network('fcc-learndash/fcc.php')) {
            ?>
        <div class='error'><p>
                <?php echo __('Please Activate BuddyPress Plugin for Activating Frontend Course Creation plugin', 'fcc');
            ?>
            </p></div>

        <?php
        } else {
            ?>
        <div class='error'><p>
                <?php echo __('Please Activate BuddyPress Plugin to Run Front End Course Creation', 'fcc');
            ?>
            </p></div>

        <?php
        }
    }
}

function wdm_fcc_admin_activation_network()
{
    if (!function_exists('is_plugin_active_for_network')) {
        include_once ABSPATH.'/wp-admin/includes/plugin.php';
    }

    if (!is_plugin_active_for_network('sfwd-lms/sfwd_lms.php')) {
        ?><div class='error'><p><?php
        echo __("LearnDash LMS plugin is not active. In order to make the 'Frontend Course Creation' plugin work, you need to install and activate LearnDash LMS first.", 'fcc');
        ?></p></div>

        <?php
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

add_action('network_admin_notices', 'wdm_fcc_admin_activation_network');

$wdm_plugin_data = array(
    'pluginShortName' => 'Frontend Course Creation', //Plugins short name appears on the License Menu Page
    'pluginSlug' => 'fcc', //this slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
    'pluginVersion' => '2.1.7', //Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
    'pluginName' => 'Frontend Course Creation', //Under this Name product should be created on WisdmLabs Site
     'storeUrl' => 'https://wisdmlabs.com', //Url where program pings to check if update is available and license validity
    'authorName' => 'WisdmLabs', //Author Name
    'pluginTextDomain' => 'fcc'
);

// include_once 'modules/includes/class-wdm-add-license-data.php';
// new WdmWuspAddDataFCC\WdmAddLicenseData($wdm_plugin_data);

/*
 * This code checks if new version is available
 */
add_action('plugins_loaded', 'fccloadLicense', 9);

function fccloadLicense()
{
    global $fccPluginData;
    $fccPluginData = include_once('license.config.php');
    require_once 'licensing/class-wdm-license.php';
    new Licensing\WdmLicense($fccPluginData);
}

$l_key = null;


add_action('init', 'wdm_fcc_plugin_activation');

function wdm_fcc_plugin_activation()
{
    global $wdm_plugin_data;
    $getDataFromDb = Licensing\WdmLicense::checkLicenseAvailiblity('fcc');
    if ($getDataFromDb == 'available') {
        // remove_role( 'wdm_course_author' );

        add_role(
            'wdm_course_author',
            __('Course Author', 'fcc'),
            array(
            'read' => true,
            'upload_files' => true,
            'publish_courses' => true,
            )
        );
        $wdm_course_create_page = get_option('wdm_course_create_page');

        if ($wdm_course_create_page == '') {
            $course_create_page = array(
                'post_title' => __('Create '.LearnDash_Custom_Label::get_label('course'), 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_course_creation]',
                'post_author' => get_current_user_id(),
            );

            $course_page_id = wp_insert_post($course_create_page);
            update_option('wdm_course_create_page', $course_page_id);
        }
        $wdm_course_list_page = get_option('wdm_course_list_page');

        if ($wdm_course_list_page == '') {
            $course_list_page = array(
                'post_title' => __(LearnDash_Custom_Label::get_label('course').' List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_course_list]',
                'post_author' => get_current_user_id(),
            );

            $course_page_id = wp_insert_post($course_list_page);
            update_option('wdm_course_list_page', $course_page_id);
        }
        $wdm_lesson_create_page = get_option('wdm_lesson_create_page');

        if ($wdm_lesson_create_page == '') {
            $lesson_create_page = array(
                'post_title' => __('Create '.LearnDash_Custom_Label::get_label('lesson'), 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_lesson_creation]',
                'post_author' => get_current_user_id(),
            );

            $lesson_page_id = wp_insert_post($lesson_create_page);
            update_option('wdm_lesson_create_page', $lesson_page_id);
        }
        $wdm_lesson_list_page = get_option('wdm_lesson_list_page');

        if ($wdm_lesson_list_page == '') {
            $lesson_list_page = array(
                'post_title' => __(LearnDash_Custom_Label::get_label('lesson').' List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_lesson_list]',
                'post_author' => get_current_user_id(),
            );

            $lesson_page_id = wp_insert_post($lesson_list_page);
            update_option('wdm_lesson_list_page', $lesson_page_id);
        }

        $wdm_topic_create_page = get_option('wdm_topic_create_page');


        if ($wdm_topic_create_page == '') {
            $topic_create_page = array(
                'post_title' => __('Create '.LearnDash_Custom_Label::get_label('topic'), 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_topic_creation]',
                'post_author' => get_current_user_id(),
            );

            $topic_page_id = wp_insert_post($topic_create_page);
            update_option('wdm_topic_create_page', $topic_page_id);
        }
        $wdm_topic_list_page = get_option('wdm_topic_list_page');

        if ($wdm_topic_list_page == '') {
            $topic_list_page = array(
                'post_title' => __(LearnDash_Custom_Label::get_label('topic').' List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_topic_list]',
                'post_author' => get_current_user_id(),
            );

            $topic_page_id = wp_insert_post($topic_list_page);
            update_option('wdm_topic_list_page', $topic_page_id);
        }
        $wdm_quiz_create_page = get_option('wdm_quiz_create_page');

        if ($wdm_quiz_create_page == '') {
            $quiz_create_page = array(
                'post_title' => __('Create '.LearnDash_Custom_Label::get_label('quiz'), 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_quiz_creation]',
                'post_author' => get_current_user_id(),
            );

            $quiz_page_id = wp_insert_post($quiz_create_page);
            update_option('wdm_quiz_create_page', $quiz_page_id);
        }
        $wdm_quiz_list_page = get_option('wdm_quiz_list_page');

        if ($wdm_quiz_list_page == '') {
            $quiz_list_page = array(
                'post_title' => __(LearnDash_Custom_Label::get_label('quiz').' List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_quiz_list]',
                'post_author' => get_current_user_id(),
            );

            $quiz_list_page = wp_insert_post($quiz_list_page);
            update_option('wdm_quiz_list_page', $quiz_list_page);
        }
        $wdm_question_create_page = get_option('wdm_question_create_page');

        if ($wdm_question_create_page == '') {
            $question_create_page = array(
                'post_title' => __('Create Question', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_question_creation]',
                'post_author' => get_current_user_id(),
            );

            $question_page_id = wp_insert_post($question_create_page);
            update_option('wdm_question_create_page', $question_page_id);
        }
        $wdm_question_list_page = get_option('wdm_question_list_page');

        if ($wdm_question_list_page == '') {
            $question_list_page = array(
                'post_title' => __('Question List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_question_list]',
                'post_author' => get_current_user_id(),
            );

            $question_page_id = wp_insert_post($question_list_page);
            update_option('wdm_question_list_page', $question_page_id);
        }
    }// End-If License
}
add_action('plugins_loaded', 'loadRequiredFCCFiles');
function loadRequiredFCCFiles()
{
    $getDataFromDb = Licensing\WdmLicense::checkLicenseAvailiblity('fcc');
    if ($getDataFromDb == 'available') {
        include_once dirname(__FILE__).'/modules/course/class_wdm_course.php';
        include_once dirname(__FILE__).'/modules/lesson/class_wdm_lesson.php';
        include_once dirname(__FILE__).'/modules/topic/class_wdm_topic.php';
        include_once dirname(__FILE__).'/modules/quiz/class_wdm_quiz.php';
        include_once dirname(__FILE__).'/modules/question/class_wdm_question.php';
        include_once dirname(__FILE__).'/modules/buddypress/class_ld_buddypress.php';
        include_once dirname(__FILE__).'/modules/settings/wdm_course_setting.php';
        include_once dirname(__FILE__).'/modules/commission/commission.php';
        // Load Helper functions
        include_once dirname(__FILE__).'/modules/helper/fcc-course-helper-functions.php';
    }
}

function wdm_insert_attachment($imgurl, $post_id)
{
    // $filename should be the path to a file in the upload directory.
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($imgurl);

    $filename = basename($imgurl);

    // The ID of the post this attachment is for.
    $parent_post_id = $post_id;

    $file = $upload_dir[ 'path' ].'/'.$filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype[ 'type' ],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $file, $parent_post_id);
    require_once ABSPATH.'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    update_post_meta($parent_post_id, '_thumbnail_id', $attach_id);
}

//replace last occurrence of string
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

add_action('wp_ajax_wdm_tag_add', 'wdm_fcc_add_tag');

function wdm_fcc_add_tag()
{
    $tag = $_POST[ 'tag' ];
    $term_exists = term_exists($tag, 'post_tag');
    if ($term_exists=='0' || $term_exists== null) {
        $new_term=wp_insert_term($tag, 'post_tag');
        $message = $new_term['term_id'].'$'.$tag;
        echo json_encode(array('success' => $message));
    } else {
        echo json_encode(array('error' => __('Tag already exist', 'fcc')));
    }
    die();
}

add_action('wp_ajax_wdm_ld_tag_add', 'wdm_fcc_add_ld_tag');

function wdm_fcc_add_ld_tag()
{
    $tag = $_POST[ 'tag' ];
    $type= $_POST['type'];
    $term_exists = term_exists($tag, 'ld_'.$type.'_tag');
    if ($term_exists=='0' || $term_exists== null) {
        $new_term=wp_insert_term($tag, 'ld_'.$type.'_tag');
        $message = $new_term['term_id'].'$'.$tag;
        echo json_encode(array('success' => $message));
    } else {
        echo json_encode(array('error' => __('Tag already exist', 'fcc')));
    }
    die();
}

add_action('wp_ajax_wdm_move_to_trash', 'moveToTrash');

function moveToTrash()
{
    wp_trash_post($_POST['post_id']);
    echo sprintf(__('%s moved to trash', 'fcc'), get_the_title($_POST['post_id']));
    die();
}

add_action('wp_ajax_wdm_undo_trash', 'undoTrash');

function undoTrash()
{
    wp_untrash_post($_POST['post_id']);
    echo sprintf(__('%s restored.', 'fcc'), get_the_title($_POST['post_id']));
    die();
}

add_action('wp_ajax_wdm_delete_question', 'deleteQuestion');

function deleteQuestion()
{
    if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'enabled') == 'yes') {
        wp_trash_post($_POST['post_id']);
    } else {
        $questionMapper=new WpProQuiz_Model_QuestionMapper();
        $questionMapper->setOnlineOff($_POST['post_id']);
    }
    die();
}
add_filter('ajax_query_attachments_args', 'wdm_fcc_show_users_own_attachments', 1, 1);

function wdm_fcc_show_users_own_attachments($query)
{
    $id = get_current_user_id();
    if (!current_user_can('manage_options')) {
        $query[ 'author' ] = $id;
    }

    return $query;
}

add_action('admin_menu', 'wdm_fcc_reset_author_metabox');

/**
 *  To remove default author meta box and add custom author meta box, to list users having role "authors" or "Instructor" in LD custom post types.
 */
function wdm_fcc_reset_author_metabox()
{

    // Determine if user is a network (super) admin. Will also check if user is admin if network mode is disabled.
    if (is_super_admin()) {
        $wdm_ar_post_types = array(
            'sfwd-courses',
            'sfwd-lessons',
            'sfwd-quiz',
            'sfwd-topic', );

        foreach ($wdm_ar_post_types as $value) {
            remove_meta_box('authordiv', $value, 'normal');
            add_meta_box('authordiv', __('Author', 'fcc'), 'wdm_fcc_post_author_meta_box', $value);
        }
    }
}

/**
 * Custom Author meta box to display on a edit post page.
 */
function wdm_fcc_post_author_meta_box($post)
{
    global $user_ID;
    ?>
    <label class="screen-reader-text" for="post_author_override"><?php _e('Author', 'fcc');
    ?></label>
    <?php
    $wdm_args = array(
        'name' => 'post_author_override',
        'selected' => empty($post->ID) ? $user_ID : $post->post_author,
        'include_selected' => true,
    );
    $args = apply_filters('wdm_author_args', $wdm_args);
    wdm_fcc_wp_dropdown_users($args);
}

/**
 * To create HTML dropdown element of the users for given argument.
 */
function wdm_fcc_wp_dropdown_users($args = '')
{
    $defaults = array(
        'show_option_all' => '',
        'show_option_none' => '',
        'hide_if_only_one_author' => '',
        'orderby' => 'display_name',
        'order' => 'ASC',
        'include' => '',
        'exclude' => '',
        'multi' => 0,
        'show' => 'display_name',
        'echo' => 1,
        'selected' => 0,
        'name' => 'user',
        'class' => '',
        'id' => '',
        'include_selected' => false,
        'option_none_value' => -1,
    );

    $defaults[ 'selected' ] = is_author() ? get_query_var('author') : 0;

    $r = wp_parse_args($args, $defaults);
    $show = $r[ 'show' ];
    $show_option_all = $r[ 'show_option_all' ];
    $show_option_none = $r[ 'show_option_none' ];
    $option_none_value = $r[ 'option_none_value' ];

    $query_args = wp_array_slice_assoc($r, array('blog_id', 'include', 'exclude', 'orderby', 'order'));
    $query_args[ 'fields' ] = array('ID', 'user_login', $show);

    $users = array_merge(get_users(array('role' => 'administrator')), get_users(array('role' => 'wdm_course_author')), get_users(array('role' => 'author')));


    if (!empty($users) && (count($users) > 1)) {
        $name = esc_attr($r[ 'name' ]);
        if ($r[ 'multi' ] && !$r[ 'id' ]) {
            $id = '';
        } else {
            $id = $r[ 'id' ] ? " id='".esc_attr($r[ 'id' ])."'" : " id='$name'";
        }
        $output = "<select name='{$name}'{$id} class='".$r[ 'class' ]."'>\n";

        if ($show_option_all) {
            $output .= "\t<option value='0'>$show_option_all</option>\n";
        }

        if ($show_option_none) {
            $_selected = selected($option_none_value, $r[ 'selected' ], false);
            $output .= "\t<option value='".esc_attr($option_none_value)."'$_selected>$show_option_none</option>\n";
        }

        $found_selected = false;
        foreach ((array) $users as $user) {
            $user->ID = (int) $user->ID;
            $_selected = selected($user->ID, $r[ 'selected' ], false);
            if ($_selected) {
                $found_selected = true;
            }
            $display = !empty($user->$show) ? $user->$show : '('.$user->user_login.')';
            $output .= "\t<option value='$user->ID'$_selected>".esc_html($display)."</option>\n";
        }

        if ($r[ 'include_selected' ] && !$found_selected && ($r[ 'selected' ] > 0)) {
            $user = get_userdata($r[ 'selected' ]);
            $_selected = selected($user->ID, $r[ 'selected' ], false);
            $display = !empty($user->$show) ? $user->$show : '('.$user->user_login.')';
            $output .= "\t<option value='$user->ID'$_selected>".esc_html($display)."</option>\n";
        }

        $output .= '</select>';
    }
    if ($r[ 'echo' ]) {
        echo $output;
    }

    return $output;
}

function wdm_is_course_author($user_id = 0)
{
    if ($user_id==0) {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
        } else {
            return false;
        }
    }

    $current_user = get_user_by('id', $user_id);
   
    if (in_array('wdm_course_author', $current_user->roles)) {
        return true;
    } else {
        return false;
    }
}
add_action('init', 'wdm_fcc_role_problem');

function wdm_fcc_role_problem()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_obj = wp_get_current_user();

    if (count($user_obj->roles) < 2) {
        if (wdm_is_course_author(get_current_user_id())) {
            if (is_admin() && !defined('DOING_AJAX')) {
                $allowDashboardAccess = apply_filters('wdm_course_author_accessing_dashboard', false);
                if (!$allowDashboardAccess) {
                    wp_redirect(site_url());
                    die();
                }
            }
        }
    }

    if (wdm_is_course_author(get_current_user_id())) {
        $user = new WP_User(get_current_user_id());
        $user->add_cap('edit_post');
        $user->add_cap('edit_posts');
        $user->add_cap('edit_others_pages');
        $user->add_cap('edit_published_pages');
        $user->add_cap('upload_files');
        $user->add_cap('wpProQuiz_edit_quiz');
        $user->add_cap('wpProQuiz_add_quiz');
        $user->add_cap('edit_published_courses');
        $user->add_cap('read_private_courses');
        $user->add_cap('edit_courses');
    }
}

add_action('admin_menu', 'wdm_fcc_remove_menu', 1000);
// removing posts menu from backend
function wdm_fcc_remove_menu()
{
    if (wdm_is_course_author(get_current_user_id())) {
        remove_menu_page('edit.php');
    }
}

add_filter('pre_get_posts', 'wdm_fcc_show_public_preview');

function wdm_fcc_show_public_preview($query)
{
    if (!is_user_logged_in()) {
        return $query;
    }
    $user_id = get_current_user_id();
    if (!wdm_is_course_author($user_id)) {
        return $query;
    }

    if ($query->is_singular() && (isset($_GET[ 'wdm_preview' ]) || isset($_GET['p']))
    ) {
        add_filter('posts_results', 'wdm_fcc_set_post_to_publish', 10, 1);
    }

    return $query;
}

//changing post status to publish for course author
function wdm_fcc_set_post_to_publish($posts)
{
    if (empty($posts)) {
        return;
    }
    if (!is_user_logged_in()) {
        return $posts;
    }
    $user_id = get_current_user_id();
    if (!wdm_is_course_author($user_id)) {
        return $posts;
    }
    //echo '<pre>';print_R($posts);echo '</pre>';exit;
    if (isset($posts[ 0 ]->post_status) && isset($posts[ 0 ]->post_author) && $posts[ 0 ]->post_author == $user_id) {
        $post_id = $posts[ 0 ]->ID;
        if ($posts[ 0 ]->post_status == 'draft') {
            $posts[ 0 ]->post_status = 'publish';

            // Disable comments and pings for this post
            add_filter('comments_open', '__return_false');
            add_filter('pings_open', '__return_false');
        }
        return $posts;
    } else {
        return $posts;
    }
}

add_filter('sfwd_lms_has_access', 'wdm_fcc_sfwd_lms_has_access', 100, 3);
/*
 * Giving access to course author
 */

function wdm_fcc_sfwd_lms_has_access($status, $post_id, $user_id)
{
    if (!is_user_logged_in()) {
        return $status;
    }
    if ($user_id == '') {
        $user_id = get_current_user_id();
    }
    if (!wdm_is_course_author($user_id)) {
        return $status;
    }
    $post_data = get_post($post_id);

    if (isset($post_data->post_author) && $post_data->post_author == $user_id) {
        return true;
    } else {
        return $status;
    }
}

/**
 * Function to hide some of the admin bar options.
 */
function hideAdminBarOptions()
{
    $user_obj = wp_get_current_user();

    if (count($user_obj->roles) < 2) {
        if (wdm_is_course_author(get_current_user_id())) {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('site-name');
            $wp_admin_bar->remove_menu('new-content');
            $wp_admin_bar->remove_menu('my-sites');
            $wp_admin_bar->remove_menu('comments');
            $wp_admin_bar->remove_node('edit');
        }
    }
}
add_action('wp_before_admin_bar_render', 'hideAdminBarOptions');

add_action('wp_enqueue_scripts', 'wdmEnqueueScripts');
function wdmEnqueueScripts()
{
    wp_enqueue_script('wdm-actions-script', plugins_url('/js/actions.js', __FILE__));
    wp_localize_script('wdm-actions-script', 'wdm_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'trash_warning' => __('will be moved to trash.', 'fcc'), 'removeQuestionsWarning' => __('will be deleted permanently.', 'fcc')));
}
