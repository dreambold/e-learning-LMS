<?php

class Wdm_Course
{

    public $course_builder;
    public $course_id;
    public $metaboxes;

    public function __construct()
    {
        add_shortcode('wdm_course_creation', array($this, 'wdm_course_creation'));

        add_action('init', array($this, 'wdm_course_save'));
        add_shortcode('wdm_course_list', array($this, 'wdm_course_list'));
        // add_action('wp_ajax_wdm_tag_add', array($this, 'wdm_tag_add'));

        add_action('wp_enqueue_scripts', array($this, 'registerScripts'));

        add_filter('learndash_course_builder_selector_args', array($this, 'modifyPaginationArgs'));

        add_filter('course_builder_selector_new_step_post_args', array($this, 'updatePostCreationArgs'));

        if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
            add_action('wp_ajax_update_course_builder', array($this, 'ajaxUpdateCourseBuilderData'));
        }
    }

    public function updatePostCreationArgs($args)
    {
        if (wdm_is_course_author()) {
            $args['post_status']=get_option('wdm_fcc_post_status', 'draft');
        }
        return $args;
    }

    public function modifyPaginationArgs($args)
    {
        if (wdm_is_course_author()) {
            $args['author__in'] = get_current_user_id();
        }
        return $args;
    }

    public function registerScripts()
    {
        /**
         * Register module stles and scripts
         *
         * @since 2.2.0
         */
        if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
            $min=( ( defined('LEARNDASH_SCRIPT_DEBUG') && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' );
            wp_register_script(
                'sfwd-module-script',
                LEARNDASH_LMS_PLUGIN_URL . 'assets/js/sfwd_module'. $min .'.js',
                array( 'jquery' ),
                LEARNDASH_SCRIPT_VERSION_TOKEN,
                true
            );
            wp_enqueue_style(
                'sfwd-module-style',
                LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module'. $min .'.css',
                array(),
                LEARNDASH_SCRIPT_VERSION_TOKEN
            );
            wp_register_style(
                'ld-datepicker-ui-css',
                LEARNDASH_LMS_PLUGIN_URL . 'assets/css/jquery-ui'. $min .'.css',
                array(),
                LEARNDASH_SCRIPT_VERSION_TOKEN
            );
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');
        if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=") && version_compare(LEARNDASH_VERSION, "2.6.0", "<")) {
            wp_register_script(
                'ld-course-builder',
                LEARNDASH_LMS_PLUGIN_URL . 'assets/js/ld-course-builder'. ( ( defined('LEARNDASH_SCRIPT_DEBUG') && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' ) .'.js',
                array(
                    'jquery-ui-core',
                    'jquery-ui-accordion',
                    'jquery-ui-draggable',
                    'jquery-ui-droppable',
                    'jquery-ui-sortable',
                    'jquery-ui-dialog'
                ),
                '1.1.0'
            );
        }
    }

    private function wdm_enqueue_action()
    {
        $learndash_course_builder_assets['learndash_upload_message'] = sprintf(__('You have unsaved %s Builder changes. Are you sure you want to leave?'), LearnDash_Custom_Label::get_label('course'));
        $learndash_course_builder_assets['course_id']=$this->course_id;
        $learndash_course_builder_assets['confirm_remove_sfwd-lessons'] = sprintf(__("Are you sure you want to remove this %s from the %s? (This will also remove all sub-items)", "fcc"), LearnDash_Custom_Label::get_label('lesson'), LearnDash_Custom_Label::get_label('course'));
        $learndash_course_builder_assets['confirm_remove_sfwd-topic'] = sprintf(__("Are you sure you want to remove this %s? (This will also remove all sub-items)", "fcc"), LearnDash_Custom_Label::get_label('topic'));
        $learndash_course_builder_assets['confirm_remove_sfwd-quiz'] = sprintf(__("Are you sure you want to remove this %s?", "fcc"), LearnDash_Custom_Label::get_label('quiz'));
        wp_enqueue_script('ld-course-builder');
        wp_localize_script('ld-course-builder', 'learndash_course_builder_assets', $learndash_course_builder_assets);
        wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_enqueue_script('wdm-course-script', plugins_url('js/wdm_course.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_enqueue_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))), array('jquery'));

        wp_localize_script(
            'wdm-validate-js',
            'wdm_validate_object',
            array(
                'wdm_enter_title'   => __('Please Enter Title', 'fcc')
                )
        );
        $data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wdm_empty_tag' => __('Please Enter Tag', 'fcc'),
            'wdm_tag_added' => __('Tag added successfully', 'fcc')
        );
        wp_localize_script('wdm-custom-js', 'wdm_data', $data);

        /**
         * Add script for handling new course builder
         * 
         * @since 2.2.0
         */
        if (version_compare(LEARNDASH_VERSION, "3.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') {
            $this->enqueueCourseModuleScripts();
            $this->enqueueLDAdminScripts();

            $filepath = \SFWD_LMS::get_template('learndash_pager.css', null, null, true);

            wp_enqueue_style(
                'learndash_pager_css',
                learndash_template_url_from_path($filepath),
                array(),
                LEARNDASH_SCRIPT_VERSION_TOKEN
            );

            $filepath = \SFWD_LMS::get_template('learndash_pager.js', null, null, true);

            wp_enqueue_script(
                'learndash_pager_js',
                learndash_template_url_from_path($filepath),
                array( 'jquery' ),
                LEARNDASH_SCRIPT_VERSION_TOKEN,
                true
            );

            if (( defined('LEARNDASH_SELECT2_LIB') ) && ( true === apply_filters('learndash_select2_lib', LEARNDASH_SELECT2_LIB) )) {
                wp_enqueue_style(
                    'learndash-select2-jquery-style',
                    LEARNDASH_LMS_PLUGIN_URL . 'assets/vendor/select2-jquery/css/select2.min.css',
                    array(),
                    LEARNDASH_SCRIPT_VERSION_TOKEN
                );

                wp_enqueue_script(
                    'learndash-select2-jquery-script',
                    LEARNDASH_LMS_PLUGIN_URL . 'assets/vendor/select2-jquery/js/select2.min.js',
                    array( 'jquery' ),
                    LEARNDASH_SCRIPT_VERSION_TOKEN,
                    true
                );
            }
            
            wp_enqueue_script(
                'wdm-custom-builder-js',
                plugins_url(
                    'js/wdm_custom_builder.js',
                    dirname(dirname(__FILE__))
                ),
                array('jquery')
            );
            wp_localize_script(
                'wdm-custom-builder-js',
                'wdm_builder_data',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'ld_data' => $this->getCourseLearnDashData()
                )
            );
        }

        include_once dirname(__FILE__).'/wdm_course_creation.php';
    }

    public function wdm_course_creation()
    {
        if (isset($_GET['courseid'])) {
            $this->course_id=$_GET['courseid'];
        } else {
            $course = array(
                'post_title' => 'Auto Draft',
                'post_status' => 'auto-draft',
                'post_type' => 'sfwd-courses',
                'post_author' => get_current_user_id(),
            );
            $this->course_id = wp_insert_post($course);
        }

        if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=") && version_compare(LEARNDASH_VERSION, "2.6.0", "<")) {
            $this->course_builder = new \Learndash_Admin_Metabox_Course_Builder();
            $this->course_builder->on_load();
        } elseif (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && version_compare(LEARNDASH_VERSION, '3.0', '<=')) {
            global $learndash_shortcode_used;
            $learndash_shortcode_used = true;
            $this->course_builder = new \Learndash_Admin_Metabox_Course_Builder();
            $this->course_builder->builder_init($this->course_id);
            $this->course_builder->builder_on_load();
            $this->course_builder->builder_admin_footer();
        } elseif (version_compare(LEARNDASH_VERSION, "3.0", '>=')) {
            $this->course_builder = new \Learndash_Admin_Metabox_Course_Builder();
            $this->course_builder->builder_init($this->course_id);
            $this->course_builder->builder_on_load();
            $this->courseBuilderHeaderScripts();
            $this->courseBuilderFooterScripts();
            $this->course_builder->builder_admin_footer();
        }
        ob_start();
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id()) || wdm_fcc_has_permissions()) {
                $this->wdm_enqueue_action();
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_course_save()
    {
        global $current_user;
        $wdm_error = '';
        //echo "<pre>";print_R($_POST);echo "</pre>";exit;
        if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
            $order_number = $_POST['order_number'];
        } else {
            $order_number = 0;
        }

        if (is_user_logged_in() && !is_super_admin(get_current_user_id())) {
            if (isset($current_user->roles)) {
                if (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles)) {
                    //$_REQUEST['post_id'] = 0;
                }
            }
        }

        $wdm_flag = 0;

        if (isset($_POST[ 'wdm_course_action' ])) {
            if ($_POST['title'] == '') {
                $wdm_error .= __('ERROR: Title is Required', 'fcc').'<br>';
                $wdm_flag = 1;
            }

            if ($wdm_flag == 1) {
                define('WDM_ERROR', $wdm_error);
                return;
            }
            global $wpdb;
            $term_relationship = $wpdb->prefix.'term_relationships';
            $wdm_path_data = wp_upload_dir();
            $wdm_path = $wdm_path_data[ 'path' ];
            $wdm_url = $wdm_path_data[ 'url' ];
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $course_id = $_POST['wdm_custom_post'];
            $post_status = (get_post_status($course_id)=='publish') ? 'publish' : get_option('wdm_fcc_post_status', 'draft');
            $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $course_id AND post_type like 'sfwd-courses'";
            $author_id = $wpdb->get_var($sql);
            if ($author_id != get_current_user_id()) {
                wp_die("cheating hu'h?");
                exit;
            }
            $course_post = array(
                'ID' => $course_id,
                'post_title' => $wdm_title,
                'post_content' => $wdm_content,
                'post_status' => $post_status,
                'post_author' => get_current_user_id(),
                'menu_order' => $order_number
            );

            // Update the post into the database
            wp_update_post($course_post);
            if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=") && version_compare(LEARNDASH_VERSION, "2.6.0", "<") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') {
                $this->course_builder = new Learndash_Admin_Metabox_Course_Builder();
                $this->course_builder->on_load();
                $this->course_builder->save_course_builder($course_id, get_post($course_id), false);
            }

            if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled') == 'yes') {
                $this->course_builder = new \Learndash_Admin_Metabox_Course_Builder();
                $this->course_builder->builder_init($course_id);
                $this->course_builder->save_course_builder($course_id, get_post($course_id), false);
            }

            if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
                $this->saveOtherCourseDetails($course_id);
            } else {
                $sql = "DELETE FROM $term_relationship WHERE object_id = $course_id";
                $wpdb->query($sql);
                //Start: WordPress Categories & Tags
                if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
                    foreach ($_POST[ 'category' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $course_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }
                if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                    foreach ($_POST[ 'tag' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $course_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }
                //End: WordPress Categories & Tags

                //Start: LearnDash Categories & Tags
                if (isset($_POST[ 'ld_category' ]) && (count($_POST[ 'ld_category' ]) > 0)) {
                    foreach ($_POST[ 'ld_category' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $course_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }
                if (isset($_POST[ 'ld_tag' ]) && (count($_POST[ 'ld_tag' ]) > 0)) {
                    foreach ($_POST[ 'ld_tag' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $course_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }
                //End: WordPress Categories & Tags

                if (isset($_FILES[ 'featured_image' ]) && $_FILES[ 'featured_image' ][ 'name' ] != '') {
                    if ($_FILES['featured_image']['type'] == 'image/jpeg' || $_FILES['featured_image']['type'] == 'image/png') {
                        $extension = explode('.', $_FILES[ 'featured_image' ][ 'name' ]);
                        $ext = $extension[ count($extension) - 1 ];
                        $target_file = $wdm_path.'/'.$course_id.'.'.$ext;
                        $target_file_url = $wdm_url.'/'.$course_id.'.'.$ext;
                        move_uploaded_file($_FILES[ 'featured_image' ][ 'tmp_name' ], $target_file);
                        wdm_insert_attachment($target_file_url, $course_id);
                    } else {
                        $wdm_error .= __('ERROR: For featured image only .png and .jpg extensions are allowed', 'fcc').'<br>';
                        $wdm_flag = 1;
                    }
                }
                $data = array();
                if (isset($_POST[ 'sfwd-courses_course_materials' ])) {
                    $data[ 'sfwd-courses_course_materials' ] = $_POST[ 'sfwd-courses_course_materials' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_price_type' ])) {
                    $data[ 'sfwd-courses_course_price_type' ] = $_POST[ 'sfwd-courses_course_price_type' ];
                }
                if (isset($_POST[ 'sfwd-courses_custom_button_url' ])) {
                    $data[ 'sfwd-courses_custom_button_url' ] = $_POST[ 'sfwd-courses_custom_button_url' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_price' ])) {
                    $data[ 'sfwd-courses_course_price' ] = $_POST[ 'sfwd-courses_course_price' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_access_list' ])) {
                    $data[ 'sfwd-courses_course_access_list' ] = $_POST[ 'sfwd-courses_course_access_list' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_lesson_orderby' ])) {
                    $data[ 'sfwd-courses_course_lesson_orderby' ] = $_POST[ 'sfwd-courses_course_lesson_orderby' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_lesson_order' ])) {
                    $data[ 'sfwd-courses_course_lesson_order' ] = $_POST[ 'sfwd-courses_course_lesson_order' ];
                }
                
                if (isset($_POST[ 'sfwd-courses_course_lesson_per_page' ])) {
                    if ($_POST[ 'sfwd-courses_course_lesson_per_page' ] == 'CUSTOM') {
                        $data[ 'sfwd-courses_course_lesson_per_page' ] = $_POST[ 'sfwd-courses_course_lesson_per_page' ];
                        if (isset($_POST[ 'sfwd-courses_course_lesson_per_page_custom' ])) {
                            $data[ 'sfwd-courses_course_lesson_per_page_custom' ] = $_POST[ 'sfwd-courses_course_lesson_per_page_custom' ];
                        }
                    } else {
                        $data[ 'sfwd-courses_course_lesson_per_page' ] = "";
                        $data[ 'sfwd-courses_course_lesson_per_page_custom' ] ="0";
                    }
                }
                // echo "<pre>";
                // var_dump($data);
                // echo "</pre>";
                // die;

                if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=")) {
                    if (isset($_POST[ 'sfwd-courses_course_prerequisite_enabled' ])) {
                        $data[ 'sfwd-courses_course_prerequisite_enabled' ] = $_POST[ 'sfwd-courses_course_prerequisite_enabled' ];
                        if (isset($_POST[ 'sfwd-courses_course_prerequisite' ])) {
                            $data[ 'sfwd-courses_course_prerequisite' ] = $_POST[ 'sfwd-courses_course_prerequisite' ];
                        }
                        if (isset($_POST[ 'sfwd-courses_course_prerequisite_compare' ])) {
                            $data[ 'sfwd-courses_course_prerequisite_compare' ] = $_POST[ 'sfwd-courses_course_prerequisite_compare' ];
                        } else {
                            $data[ 'sfwd-courses_course_prerequisite_compare' ] = 'ALL';
                        }
                    } else {
                        $data[ 'sfwd-courses_course_prerequisite_enabled' ] = 'off';
                        $data[ 'sfwd-courses_course_prerequisite' ] = array();
                        $data[ 'sfwd-courses_course_prerequisite_compare' ] = 'ANY';
                    }
                    if (isset($_POST[ 'sfwd-courses_course_points_enabled' ])) {
                        $data[ 'sfwd-courses_course_points_enabled' ] = $_POST[ 'sfwd-courses_course_points_enabled' ];
                        if (isset($_POST[ 'sfwd-courses_course_points_access' ])) {
                            $data[ 'sfwd-courses_course_points_access' ] = $_POST[ 'sfwd-courses_course_points_access' ];
                        }
                        if (isset($_POST[ 'sfwd-courses_course_points' ])) {
                            $data[ 'sfwd-courses_course_points' ] = $_POST[ 'sfwd-courses_course_points' ];
                        }
                    }
                } else {
                    if (isset($_POST[ 'sfwd-courses_course_prerequisite' ])) {
                        $data[ 'sfwd-courses_course_prerequisite' ] = $_POST[ 'sfwd-courses_course_prerequisite' ];
                    }
                }
                if (isset($_POST[ 'sfwd-courses_course_disable_lesson_progression' ])) {
                    $data[ 'sfwd-courses_course_disable_lesson_progression' ] = $_POST[ 'sfwd-courses_course_disable_lesson_progression' ];
                }
                if (isset($_POST[ 'sfwd-courses_expire_access' ])) {
                    $data[ 'sfwd-courses_expire_access' ] = $_POST[ 'sfwd-courses_expire_access' ];
                }
                if (isset($_POST[ 'sfwd-courses_expire_access_days' ])) {
                    $data[ 'sfwd-courses_expire_access_days' ] = $_POST[ 'sfwd-courses_expire_access_days' ];
                }
                if (isset($_POST[ 'sfwd-courses_expire_access_delete_progress' ])) {
                    $data[ 'sfwd-courses_expire_access_delete_progress' ] = $_POST[ 'sfwd-courses_expire_access_delete_progress' ];
                }
                if (isset($_POST[ 'sfwd-courses_course_disable_content_table' ])) {
                    $data[ 'sfwd-courses_course_disable_content_table' ] = $_POST[ 'sfwd-courses_course_disable_content_table' ];
                }
                if (isset($_POST[ 'sfwd-courses_certificate' ])) {
                    $data[ 'sfwd-courses_certificate' ] = $_POST[ 'sfwd-courses_certificate' ];
                }

                //$wdm_course_data = serialize($data);
                update_post_meta($course_id, '_sfwd-courses', $data);
                update_post_meta($course_id, 'course_price_billing_p3', $_POST[ 'course_price_billing_p3' ]);
                update_post_meta($course_id, 'course_price_billing_t3', $_POST[ 'course_price_billing_t3' ]);
            }

            //echo "12321";
            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_course_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?courseid='.$course_id;
            if (!isset($_POST[ 'courseid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            if ($wdm_flag == 1) {
                $_SESSION['wdm_error'] = $wdm_error;
        //  return;
            }
            wp_redirect($link);
            exit;
        }
    }

    private function wdm_course_list_enqueues($table)
    {
        global $wpdb;
        wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_localize_script(
            'wdm-datatable-script',
            'wdm_datatable_object',
            array(
            'wdm_no_data_string' => __('No data available in table', 'fcc'),
            'wdm_previous_btn'  => __('Previous', 'fcc'),
            'wdm_next_btn'  => __('Next', 'fcc'),
            'wdm_search_bar'    => __('Search', 'fcc'),
            'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
            'showing__start__to__end__of__total__entries' => sprintf(
                __('Showing %s to %s of %s entries', 'fcc'),
                '_START_',
                ' _END_',
                '_TOTAL_'
            ),
            'showing_length_of_table'   => sprintf(
                __('Show %s entries', 'fcc'),
                '_MENU_'
            ),
            'wdm_no_matching'   => __('No matching records found', 'fcc'),
            'wdm_filtered_from' => sprintf(__('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );

        include_once dirname(__FILE__).'/wdm_course_list.php';
    }

    public function wdm_course_list()
    {
        ob_start();
        global $wpdb;
        $table = $wpdb->prefix.'posts';
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id()) || wdm_fcc_has_permissions()) {
                $this->wdm_course_list_enqueues($table);
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    /**
     * Load course builder footer scripts
     *
     * @since 2.2.0
     */
    public function courseBuilderFooterScripts()
    {
        wp_enqueue_editor();
        wp_enqueue_style(
            'fcc-learndash-course-builder-style',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.css',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.css',
            array( 'wp-editor' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );

        wp_enqueue_script(
            'fcc-learndash-course-builder-script',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.js',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.js',
            array( 'wp-i18n', 'fcc-learndash-course-header-script', 'wp-data' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN,
            true
        );

        $builder_assets[ 'sfwd-courses' ]['post_data']['builder_editor'] = 'block';
        $builder_assets[ 'sfwd-courses' ]['post_data']['builder_class'] = 'Learndash_Admin_Metabox_Course_Builder';
        $builder_assets[ 'sfwd-courses' ]['post_data']['builder_post_id'] = $this->course_id;
        $builder_assets[ 'sfwd-courses' ]['post_data']['builder_post_title'] = get_the_title($this->course_id);
        $builder_assets[ 'sfwd-courses' ]['post_data']['builder_post_type'] = 'sfwd-courses';

        wp_localize_script('fcc-learndash-course-builder-script', 'learndash_builder_assets', $builder_assets);
    }

    /**
     * Load course builder header scripts
     *
     * @since 2.2.0
     */
    public function courseBuilderHeaderScripts()
    {
        wp_enqueue_style(
            'fcc-learndash-course-header-style',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.css',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header.min.css',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );
        wp_enqueue_script(
            'fcc-learndash-course-header-script',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.js',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header.min.js',
            array( 'wp-i18n' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN,
            true
        );

        $learndash_data = $this->getCourseLearnDashData();
        if (! empty($learndash_data)) {
            $css_lesson_label     = \LearnDash_Custom_Label::get_label('lesson')[0];
            $css_topic_label      = \LearnDash_Custom_Label::get_label('topic')[0];
            $css_quiz_label       = \LearnDash_Custom_Label::get_label('quiz')[0];
            $learndash_custom_css = "
            .learndash_navigation_lesson_topics_list .lesson > a:before,
            #sfwd-course-lessons h2:before {
                content: '{$css_lesson_label}';
            }
            .learndash_navigation_lesson_topics_list .topic_item > a > span:before,
            #sfwd-course-topics h2:before {
                content: '{$css_topic_label}';
            }
            #sfwd-course-quizzes h2:before {
                content: '{$css_quiz_label}';
            }
            ";
            wp_add_inline_style('fcc-learndash-course-header-style', $learndash_custom_css);
            wp_localize_script('fcc-learndash-course-header-script', 'LearnDashData', $learndash_data);
        }
    }

    /**
     * Get Learndash data
     *
     * @since 2.2.0
     * @return $learndash_data  LearndashData to be used by course builder
     */
    public function getCourseLearnDashData()
    {
        learndash_select_menu();
        $header_data = $this->getCourseHeaderData();
        $header_data['post_data']['builder_post_id'] = $this->course_id;
        if (! empty($header_data['post_data']['builder_post_id'])) {
            $header_data['post_data']['builder_post_title'] = get_the_title($header_data['post_data']['builder_post_id']);
        }
        $header_data['post_data']['builder_post_type'] = 'sfwd-courses';
        $header_data['currentTab'] = 'learndash_course_builder';
        $header_data['posts_per_page'] = \LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'per_page');

        learndash_load_inline_script_locale_data();

        $learndash_data = fccGetCourseSteps($this->course_id, $header_data);
        return $learndash_data;
    }

    /**
     * Get Header data for course builder
     * 
     * @since 2.2.0
     * @return $header_data     Header data for the course builder
     */
    public function getCourseHeaderData()
    {
        $header_data = array(
            'tabs'           => array(),
            'currentTab'     => 'learndash_course_builder',
            'editing'        => 1,
            'ajaxurl'        => admin_url( 'admin-ajax.php' ),
            'adminurl'       => admin_url( 'edit.php' ),
            'quizImportUrl'  => admin_url( 'admin.php?page=ldAdvQuiz' ),
            'postadminurl'   => admin_url( 'post.php' ),
            'back_to_title'  => '',
            'back_to_url'    => '',
            'error_messages' => array(
                'builder' => esc_html__( 'There was an unexpected error while loading. Please try refreshing the page. If the error continues, contact LearnDash support.', 'learndash' ),
                'header'  => esc_html__( 'There was an unexpected error while loading. Please try refreshing the page. If the error continues, contact LearnDash support.', 'learndash' ),
            ),
            'labels'         => array(
                'section-heading'     => esc_html__( 'Section Heading', 'learndash' ),
                'section-headings'    => esc_html__( 'Section Headings', 'learndash' ),
                'answer'              => esc_html__( 'answer', 'learndash' ),
                'answers'             => esc_html__( 'answers', 'learndash' ),
                'course'              => \LearnDash_Custom_Label::get_label( 'course' ),
                'courses'             => \LearnDash_Custom_Label::get_label( 'courses' ),
                'lesson'              => \LearnDash_Custom_Label::get_label( 'lesson' ),
                'lessons'             => \LearnDash_Custom_Label::get_label( 'lessons' ),
                'topic'               => \LearnDash_Custom_Label::get_label( 'topic' ),
                'topics'              => \LearnDash_Custom_Label::get_label( 'topics' ),
                'quiz'                => \LearnDash_Custom_Label::get_label( 'quiz' ),
                'quizzes'             => \LearnDash_Custom_Label::get_label( 'quizzes' ),
                'question'            => \LearnDash_Custom_Label::get_label( 'question' ),
                'questions'           => \LearnDash_Custom_Label::get_label( 'questions' ),
                'sfwd-course'         => \LearnDash_Custom_Label::get_label( 'course' ),
                'sfwd-courses'        => \LearnDash_Custom_Label::get_label( 'courses' ),
                'sfwd-lesson'         => \LearnDash_Custom_Label::get_label( 'lesson' ),
                'sfwd-lessons'        => \LearnDash_Custom_Label::get_label( 'lessons' ),
                'sfwd-topic'          => \LearnDash_Custom_Label::get_label( 'topic' ),
                'sfwd-topics'         => \LearnDash_Custom_Label::get_label( 'topics' ),
                'sfwd-quiz'           => \LearnDash_Custom_Label::get_label( 'quiz' ),
                'sfwd-quizzes'        => \LearnDash_Custom_Label::get_label( 'quizzes' ),
                'sfwd-courses'        => \LearnDash_Custom_Label::get_label( 'courses' ),
                'sfwd-question'       => \LearnDash_Custom_Label::get_label( 'question' ),
                'start-adding-lesson' => sprintf(
                    // translators: placeholder: Lesson.
                    esc_html_x( 'Start by adding a %s.', 'placeholder: Lesson', 'learndash' ),
                    \LearnDash_Custom_Label::get_label( 'lesson' )
                ),
            ),
            'sfwdMap'        => array(
                'lesson'   => 'sfwd-lessons',
                'topic'    => 'sfwd-topic',
                'quiz'     => 'sfwd-quiz',
                'question' => 'sfwd-question',
            ),
            'rest'           => array(
                'namespace' => LEARNDASH_REST_API_NAMESPACE . '/v1',
                'base'      => array(
                    'lessons'  => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-lessons' ),
                    'topic'    => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-topic' ),
                    'quiz'     => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-quiz' ),
                    'question' => 'sfwd-questions',
                ),
                'root'      => esc_url_raw( rest_url() ),
                'nonce'     => wp_create_nonce( 'wp_rest' ),
            ),
            'post_data'      => array(
                'builder_post_id'    => 0,
                'builder_post_title' => '',
                'builder_post_type'  => '',
            ),
            'posts_per_page' => 0,
            'lessons'        => array(),
            'topics'         => array(),
            'quizzes'        => array(),
            'questions'      => array(),
            'i18n'           => array(
                'back_to'                            => esc_html_x( 'Back to', 'Link back to the post type overview', 'learndash' ),
                'actions'                            => esc_html_x( 'Actions', 'Builder actions dropdown', 'learndash' ),
                'expand'                             => esc_html_x( 'Expand All', 'Builder elements', 'learndash' ),
                'collapse'                           => esc_html_x( 'Collapse All', 'Builder elements', 'learndash' ),
                'error'                              => esc_html__( 'An error occurred while submitting your request. Please try again.', 'learndash' ),
                'cancel'                             => esc_html__( 'Cancel', 'learndash' ),
                'edit'                               => esc_html__( 'Edit', 'learndash' ),
                'remove'                             => esc_html__( 'Remove', 'learndash' ),
                'save'                               => esc_html__( 'Save', 'learndash' ),
                'settings'                           => esc_html__( 'Settings', 'learndash' ),
                'edit_question'                      => esc_html__( 'Click here to edit the question', 'learndash' ),
                'correct_answer_message'             => esc_html__( 'Message for correct answer - optional', 'learndash' ),
                'different_incorrect_answer_message' => esc_html__( 'Use different message for incorrect answer', 'learndash' ),
                'same_answer_message'                => esc_html__( 'Currently same message is displayed as above.', 'learndash' ),
                'incorrect_answer_message'           => esc_html__( 'Message for incorrect answer - optional', 'learndash' ),
                'solution_hint'                      => esc_html__( 'Solution hint', 'learndash' ),
                'points'                             => esc_html__( 'points', 'learndash' ),
                'edit_answer'                        => esc_html__( 'Click here to edit the answer', 'learndash' ),
                'update_answer'                      => esc_html__( 'Update Answer', 'learndash' ),
                'allow_html'                         => esc_html__( 'Allow HTML', 'learndash' ),
                'correct'                            => esc_html__( 'Correct', 'learndash' ),
                'correct_1st'                        => wp_kses_post( _x( '1<sup>st</sup>', 'First sort answer correct', 'learndash' ) ),
                'correct_2nd'                        => wp_kses_post( _x( '2<sup>nd</sup>', 'Second sort answer correct', 'learndash' ) ),
                'correct_3rd'                        => wp_kses_post( _x( '3<sup>rd</sup>', 'Third sort answer correct', 'learndash' ) ),
                'correct_nth'                        => wp_kses_post( _x( '<sup>th</sup>', 'nth sort answer correct', 'learndash' ) ),
                'answer_updated'                     => esc_html__( 'Answer updated', 'learndash' ),
                'edit_answer_settings'               => esc_html__( 'Edit answer settings', 'learndash' ),
                'answer'                             => esc_html__( 'Answer:', 'learndash' ),
                'edit_matrix'                        => esc_html__( 'Click here to edit the matrix', 'learndash' ),
                'new_element_labels'                 => array(
                    'question'        => sprintf(
                        /* translators: placeholders: Question */
                        esc_html_x( 'New %1$s', 'placeholder: Question', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'question' )
                    ),
                    'quiz'            => sprintf(
                        /* translators: placeholders: Quiz */
                        esc_html_x( 'New %1$s', 'placeholder: Quiz', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'quiz' )
                    ),
                    'topic'           => sprintf(
                        /* translators: placeholders: Topic */
                        esc_html_x( 'New %1$s', 'placeholder: Topic', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'topic' )
                    ),
                    'lesson'          => sprintf(
                        /* translators: placeholders: Lesson */
                        esc_html_x( 'New %1$s', 'placeholder: Lesson', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'lesson' )
                    ),
                    'answer'          => esc_html__( 'New answer', 'learndash' ),
                    'section-heading' => esc_html__( 'New Section Heading', 'learndash' ),
                ),
                'enter_title'                        => esc_html_x( 'Enter a title', 'Title for the new course, lesson, quiz', 'learndash' ),
                'enter_answer'                       => esc_html_x( 'Enter an answer', 'Answer for a question', 'learndash' ),
                'please_wait'                        => esc_html_x( 'Please wait...', 'Please wait while the form is loading', 'learndash' ),
                'add_element'                        => esc_html_x( 'Add', 'Add lesson, topic, quiz...', 'learndash' ),
                'add_element_labels'                 => array(
                    'question'        => sprintf(
                        /* translators: placeholders: Question */
                        esc_html_x( 'Add %1$s', 'placeholder: Question', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'question' )
                    ),
                    'questions'       => sprintf(
                        /* translators: placeholders: Question */
                        esc_html_x( 'Add %1$s', 'placeholder: Questions', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'questions' )
                    ),
                    'quiz'            => sprintf(
                        /* translators: placeholders: Quiz */
                        esc_html_x( 'Add %1$s', 'placeholder: Quiz', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'quiz' )
                    ),
                    'topic'           => sprintf(
                        /* translators: placeholders: Topic */
                        esc_html_x( 'Add %1$s', 'placeholder: Topic', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'topic' )
                    ),
                    'lesson'          => sprintf(
                        /* translators: placeholders: Lesson */
                        esc_html_x( 'Add %1$s', 'placeholder: Lesson', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'lesson' )
                    ),
                    'answer'          => esc_html__( 'Add answer', 'learndash' ),
                    'section-heading' => esc_html__( 'Add Section Heading', 'learndash' ),
                ),
                'move_up'                            => esc_html_x( 'Move up', 'Move the current element up in the builder interface', 'learndash' ),
                'question_empty'                     => esc_html_x( 'The question is empty.', 'Warning when no question was entered', 'learndash' ),
                'move_down'                          => esc_html_x( 'Move down', 'Move the current element down in the builder interface', 'learndash' ),
                'rename'                             => esc_html_x( 'Rename', 'Rename the current element in the builder interface', 'learndash' ),
                'search_element_labels'              => array(
                    'lesson'  => sprintf(
                        /* translators: placeholders: lessons */
                        esc_html_x( 'Search %1$s', 'placeholders: lessons', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'lessons' )
                    ),
                    'quiz'  => sprintf(
                        /* translators: placeholders: quizzes */
                        esc_html_x( 'Search %1$s', 'placeholders: quizzes', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'quizzes' )
                    ),
                    'topic'  => sprintf(
                        /* translators: placeholders: topics */
                        esc_html_x( 'Search %1$s', 'placeholders: topics', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'topics' )
                    ),
                    'question'  => sprintf(
                        /* translators: placeholders: questions */
                        esc_html_x( 'Search %1$s', 'placeholders: questions', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'questions' )
                    ),
                ),
                'recent'                             => esc_html_x( 'Recent', 'List of recent lessons, topics, quizzes or questions', 'learndash' ),
                'view_all'                           => esc_html_x( 'View all', 'Lesson, Topic, Quiz or Question posts', 'learndash' ),
                'start_adding_element_labels'        => array(
                    'lesson' => sprintf(
                        /* translators: placeholders: Lesson*/
                        esc_html_x( 'Start adding your first %1$s', 'placeholders: Lesson', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'lesson' )
                    ),
                    'quiz'   => sprintf(
                        /* translators: placeholders: Quiz*/
                        esc_html_x( 'Start adding your first %1$s', 'placeholders: Quiz', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'quiz' )
                    ),
                    'topic'  => sprintf(
                        /* translators: placeholders: Topic*/
                        esc_html_x( 'Start adding your first %1$s', 'placeholders: Topic', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'topic' )
                    ),
                    'question'  => sprintf(
                        /* translators: placeholders: Question*/
                        esc_html_x( 'Start adding your first %1$s', 'placeholders: Question', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'question' )
                    ),
                ),
                'all_elements_added_labels'          => array(
                    'lesson' => sprintf(
                        /* translators: placeholders: Lessons*/
                        esc_html_x( 'All available %1$s have been added.', 'placeholders: Lessons', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'lessons' )
                    ),
                    'quiz' => sprintf(
                        /* translators: placeholders: Quizzes */
                        esc_html_x( 'All available %1$s have been added.', 'placeholders: Quizzes', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'quizzes' )
                    ),
                    'topic'  => sprintf(
                        /* translators: placeholders: Topics */
                        esc_html_x( 'All available %1$s have been added.', 'placeholders: Topics', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'topics' )
                    ),
                    'question'  => sprintf(
                        /* translators: placeholders: Questions */
                        esc_html_x( 'All available %1$s have been added.', 'placeholders: Questions', 'learndash' ),
                        LearnDash_Custom_Label::get_label( 'questions' )
                    ),
                ),
                'start_adding'                       => esc_html_x( 'Start adding your first', 'Lesson, Topic, Quiz or Question', 'learndash' ),
                'refresh'                            => esc_html_x( 'Refresh', 'Builder - Refresh list of  Lessons, Topics, Quizzes or Questions', 'learndash' ),
                'load_more'                          => esc_html_x( 'Load More', 'Builder - Load more Lessons, Topics, Quizzes or Questions', 'learndash' ),
                'add_selected'                       => esc_html_x( 'Add Selected', 'Builder - Add selected Lessons, Topics, Quizzes or Questions', 'learndash' ),
                'undo'                               => esc_html_x( 'Undo', 'Undo action in the builder', 'learndash' ),
                'criterion'                          => esc_html_x( 'Criterion', 'Matrix answer Criteroion', 'learndash' ),
                'sort_element'                       => esc_html_x( 'Sort element', 'Sort matrix answer element', 'learndash' ),
                'question_settings'                  => esc_html_x( 'Settings', 'Question settings. Placeholder in JavaScript', 'learndash' ),
                'select_option'                      => esc_html_x( 'Select', 'Select an option', 'learndash' ),
                'nothing_found'                      => esc_html_x( 'Nothing matches your search', 'No matching Lesson, Topic, Quiz or Question found', 'learndash' ),
                'drop_lessons'                       => sprintf(
                    /* translators: placeholders: Lessons */
                    esc_html_x( 'Drop %1$s here', 'placeholder: Lessons', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'lessons' )
                ),
                'drop_question'                      => sprintf(
                    /* translators: placeholders: Question */
                    esc_html_x( 'Drop %1$s here', 'placeholder: Question', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'question' )
                ),
                'drop_quizzes'                       => sprintf(
                    /* translators: placeholders: Quizzes */
                    esc_html_x( 'Drop %1$s here', 'placeholder: Quizzes', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'quizzes' )
                ),
                'drop_quizzes_topics'                => sprintf(
                    /* translators: placeholders: %1$s: Topics, %2$s: Quizzes */
                    esc_html_x( 'Drop %1$s or %2$s here', 'placeholder: %1$s: Topics, %2$s: Quizzes', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'topics' ),
                    LearnDash_Custom_Label::get_label( 'quizzes' )
                ),
                'step'                               => esc_html_x( 'step', 'singular - Amount of steps in a course or quiz', 'learndash' ),
                'steps'                              => esc_html_x( 'steps', 'plural - Amount of steps in a course or quiz', 'learndash' ),
                'in_this'                            => esc_html_x( 'in this', 'Amount of steps in this course or quiz', 'learndash' ),
                'final_quiz'                         => esc_html_x( 'Final', 'Builder - Final quiz. Placeholder in JavaScript', 'learndash' ),
                'quiz_no_questions'                  => sprintf(
                    // translators: placeholders: %1$s: Quiz, %2$s:   Questions
                    esc_html_x( 'This %1$s has no %2$s yet', 'This quiz has no questions.', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'quiz' ),
                    LearnDash_Custom_Label::get_label( 'questions' )
                ),
                'manage_questions_builder'           => sprintf(
                    /* translators: placeholders: Questions */
                    esc_html_x( 'Manage %1$s in builder', 'Manage Questions in builder', 'learndash' ),
                    LearnDash_Custom_Label::get_label( 'questions' )
                ),
                'total_points'                       => esc_html_x( 'TOTAL:', 'Total points', 'learndash' ),
                'no_content'                         => esc_html_x( 'has no content yet.', 'Displayed when the post type, e.g. course, has no content', 'learndash' ),
                'add_content'                        => esc_html_x( 'Add a new', 'Content type, e.g. lesson', 'learndash' ),
                'add_from_sidebar'                   => esc_html_x( 'or add an existing one from the sidebar', 'Content type, e.g. lesson', 'learndash' ),
                'essay_answer_format'                => esc_html_x( 'Answer format', 'Type of essay answer', 'learndash' ),
                'essay_text_answer'                  => esc_html_x( 'Text entry', 'Submit essay answer in a text box', 'learndash' ),
                'essay_file_upload_answer'           => esc_html_x( 'File upload', 'Submit essay answer as an upload', 'learndash' ),
                'essay_after_submission'             => esc_html_x( 'What should happen on quiz submission?', 'What grading options should be used after essay submission', 'learndash' ),
                'essay_not_graded_no_points'         => esc_html_x( 'Not Graded, No Points Awarded', 'Essay answer grading option', 'learndash' ),
                'essay_not_graded_full_points'       => esc_html_x( 'Not Graded, Full Points Awarded', 'Essay answer grading option', 'learndash' ),
                'essay_graded_full_points'           => esc_html_x( 'Graded, Full Points Awarded', 'Essay answer grading option', 'learndash' ),
                'essay_not_set'                      => esc_html_x( 'Not set', 'Essay answer grading option has not been set', 'learndash' ),
            ),
        );

        return $header_data;
    }

    /**
     * Ajax method to update course builder data
     * 
     * @since 2.2.0
     */
    public function ajaxUpdateCourseBuilderData()
    {
        if (! empty($_POST) && array_key_exists('course_id', $_POST) && ! empty($_POST['course_id'])) {
            $course_id = intval($_POST['course_id']);
            $builder_data = $_POST['builder_data'];

            $learndash_data = array(
                'sfwd-courses' => array(
                    $course_id  =>  $builder_data
                )
            );

            $_POST['learndash_builder'] = $learndash_data;
            $this->course_builder = new \Learndash_Admin_Metabox_Course_Builder();
            $this->course_builder->builder_init($course_id);
            $status = $this->course_builder->save_course_builder($course_id, get_post($course_id), false);

            echo $status;
        }
        wp_die();
    }

    public function loadMetaBoxes()
    {
        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-course-display-content.php';
        require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php';
        require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-course-navigation-settings.php';
        $this->metaboxes = apply_filters('learndash_post_settings_metaboxes_init_sfwd-courses', $this->metaboxes);
    }

    public function enqueueCourseModuleScripts()
    {
        wp_enqueue_script('sfwd-module-script');

        $script_data['learndash_categories_lang']          = esc_html__('LearnDash Categories', 'learndash');
        $script_data['loading_lang']                       = esc_html__('Loading...', 'learndash');
        $script_data['select_a_lesson_lang']               = sprintf(esc_html_x('-- Select a %s --', 'Select a Lesson Label', 'learndash'), \LearnDash_Custom_Label::get_label('lesson'));
        $script_data['select_a_lesson_or_topic_lang']      = sprintf(esc_html_x('-- Select a %s or %s --', 'Select a Lesson Topic Label', 'learndash'), \LearnDash_Custom_Label::get_label('lesson'), \LearnDash_Custom_Label::get_label('topic'));
        $script_data['advanced_quiz_preview_link']         = admin_url('admin.php?page=ldAdvQuiz&module=preview&id=');
        $script_data['valid_recurring_paypal_day_range']   = esc_html__('Valid range is 1 to 90 when the Billing Cycle is set to days.', 'learndash');
        $script_data['valid_recurring_paypal_week_range']  = esc_html__('Valid range is 1 to 52 when the Billing Cycle is set to weeks.', 'learndash');
        $script_data['valid_recurring_paypal_month_range'] = esc_html__('Valid range is 1 to 24 when the Billing Cycle is set to months.', 'learndash');
        $script_data['valid_recurring_paypal_year_range']  = esc_html__('Valid range is 1 to 5 when the Billing Cycle is set to years.', 'learndash');
        $script_data['ajaxurl']                            = admin_url('admin-ajax.php');

        // if ($this->post_details['name'] == 'quiz') {
        //     $script_data['quiz_pro'] = intval(learndash_get_setting($this->post_id, 'quiz_pro'));
        // }

        $data = array();
        if (! empty($script_data)) {
            $data = $script_data;
        }

        $data = array( 'json' => json_encode($data) );

        wp_localize_script('sfwd-module-script', 'sfwd_data', $data);
        wp_enqueue_style('wdm-module-style');
        wp_enqueue_style('sfwd-module-style');
    }

    public function enqueueLDAdminScripts()
    {
        wp_enqueue_script(
            'learndash-admin-binary-selector-script',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector.min.js',
            array( 'jquery' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN,
            true
        );

        wp_enqueue_style(
            'learndash-admin-binary-selector-style',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector.min.css',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );

        wp_enqueue_style(
            'learndash-admin-settings-page',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-settings-page.min.css',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );

        wp_enqueue_script(
            'learndash-admin-settings-page',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-settings-page.min.js',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );

        $admin_script_data = apply_filters('learndash_admin_settings_data', array());

        $admin_script_data['ajaxurl'] = admin_url('admin-ajax.php');

        wp_localize_script('learndash-admin-settings-page', 'learndash_admin_settings_data', array( 'json' => json_encode($admin_script_data)));

        wp_enqueue_style(
            'learndash-admin-style',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-style.min.css',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );
    }

    public function saveOtherCourseDetails($course_id)
    {
        if (empty($this->metaboxes)) {
            $this->loadMetaBoxes();
        }

        if (! empty($this->metaboxes)) {
            foreach ($this->metaboxes as $setting_key => $_metaboxes_instance) {
                if (apply_filters('learndash_show_metabox', true, $setting_key, 'sfwd-courses')) {
                    $settings_fields = array();
                    $settings_fields = $_metaboxes_instance->get_post_settings_field_updates(
                        $course_id,
                        get_post($course_id),
                        true
                    );
                    $_metaboxes_instance->save_post_meta_box($course_id, get_post($course_id), true, $settings_fields);
                }
            }
        }

        cmpUploadImageHandler($course_id, $_FILES);

        $this->saveCustomFields($course_id);

        $this->savePostCategories($course_id);
    }

    public function saveCustomFields($post_id)
    {
        $post_meta = get_post_meta($post_id, '_sfwd-courses', true);
        if (!empty($this->custom_fields)) {
            foreach ($this->custom_fields as $field_id => $field_details) {
                $field_name = $this->post_details['slug'].'_'.$field_id;
                if (isset($_POST[$field_name]) && !empty($_POST[$field_name])) {
                    $post_meta[$field_name] = $_POST[$field_name];
                } else {
                    unset($post_meta[$field_name]);
                }
            }
            update_post_meta($post_id, '_'.$this->post_details['slug'], $post_meta);
        }
        unset($field_details);
    }

    public function savePostCategories($post_id)
    {
        global $wpdb;
        $term_relationship = $wpdb->prefix.'term_relationships';
        $remove_post_terms = "DELETE FROM $term_relationship WHERE object_id = $post_id";
        $wpdb->query($remove_post_terms);
        if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
            $this->addTermTaxonomy($_POST[ 'category' ], 'category', $post_id);
        }
        if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
            $this->addTermTaxonomy($_POST[ 'tag' ], 'post_tag', $post_id);
        }
        if (isset($_POST[ 'ld_category' ]) && (count($_POST[ 'ld_category' ]) > 0)) {
            $this->addTermTaxonomy($_POST[ 'ld_category' ], 'ld_course_category', $post_id);
        }
        if (isset($_POST[ 'ld_tag' ]) && (count($_POST[ 'ld_tag' ]) > 0)) {
            $this->addTermTaxonomy($_POST[ 'ld_tag' ], 'ld_course_tag', $post_id);
        }
    }

    public function addTermTaxonomy($categories, $taxonomy, $post_id)
    {
        global $wpdb;
        $term_relationship = $wpdb->prefix.'term_relationships';
        if (empty($categories)) {
            return;
        }
        $error_class = '\WP_Error';
        foreach ($categories as $category) {
            if (!is_numeric($category)) {
                $new_term=wp_insert_term($category, $taxonomy);
                if (is_array($new_term) && isset($new_term['term_taxonomy_id'])) {
                    $category = $new_term['term_taxonomy_id'];
                } elseif ($new_term instanceof $error_class && isset($new_term->error_data['term_exists'])) {
                    $category = $new_term->error_data['term_exists'];
                } else {
                    continue;
                }
            }
            $category_data = array(
                'object_id' => $post_id,
                'term_taxonomy_id' => $category,
            );
            $wpdb->insert($term_relationship, $category_data);
        }
    }
}

new Wdm_Course();
