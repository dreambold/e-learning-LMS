<?php

class Wdm_Lesson
{
    public $metaboxes;

    public function __construct()
    {
        add_shortcode('wdm_lesson_creation', array($this, 'wdm_lesson_creation'));
        add_action('init', array($this, 'wdm_lesson_save'));
        add_shortcode('wdm_lesson_list', array($this, 'wdm_lesson_list'));

        add_action('wp_enqueue_scripts', array($this, 'wdmRegisterScripts'));
    }

    public function wdmRegisterScripts()
    {
        // wp_register_script('ld-lesson-script', plugins_url('js/sfwd_module.js', dirname(dirname(__FILE__))));
        wp_register_style('wdm-lesson-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
        wp_register_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
        wp_register_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))));
        wp_register_script('wdm-lesson-script', plugins_url('js/wdm_lesson.js', dirname(dirname(__FILE__))));
        wp_register_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
        wp_register_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))));
        wp_register_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))));
        wp_register_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))));
        wp_register_script('wdm-datetimepicker', plugins_url('js/jquery.datetimepicker.min.js', dirname(dirname(__FILE__))));
        // wp_register_script('wdm-bootstrap-script', plugins_url('js/bootstrap.min.js', dirname(dirname(__FILE__))));
        wp_register_style('wdm-bootstrap-style', plugins_url('css/jquery.datetimepicker.min.css', dirname(dirname(__FILE__))));
        // wp_register_style('wdm-bootstrap-datepicker', plugins_url('css/bootstrap-datetimepicker.min.css', dirname(dirname(__FILE__))));

        if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
            $min=( ( defined('LEARNDASH_SCRIPT_DEBUG') && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' );
    
            wp_register_script(
                'sfwd-module-script',
                LEARNDASH_LMS_PLUGIN_URL . 'assets/js/sfwd_module'. $min .'.js',
                array( 'jquery' ),
                LEARNDASH_SCRIPT_VERSION_TOKEN,
                true
            );
    
            wp_register_script(
                'wpProQuiz_admin_javascript',
                plugins_url('js/wpProQuiz_admin'. $min .'.js', WPPROQUIZ_FILE)
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
    }

    public function wdm_lesson_creation()
    {
        global $current_user;
        ob_start();
        $is_new=isset($_GET['lessonid']) ? true : false;
        
        if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
            $this->enqueueLessonScripts();
        }
        if (is_user_logged_in()) {
            wp_enqueue_script('wdm-bootstrap-script');
            wp_enqueue_style('wdm-bootstrap-style');
            wp_enqueue_style('wdm-bootstrap-datepicker');
            wp_enqueue_script('wdm-datetimepicker');
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-lesson-style');
                wp_enqueue_style('wdm-select2-style');
                // wp_enqueue_script('ld-lesson-script');
                wp_enqueue_script('wdm-accordion-script', array('jquery'));
                wp_enqueue_script('wdm-lesson-script');
                $date_validation_msg=sprintf(__('Date entered in "Make %s Visible on Specific Date" field must be greater then current date.', 'fcc'), LearnDash_Custom_Label::get_label('lesson'));
                wp_localize_script('wdm-lesson-script', 'wdm_lesson_data', array('is_new'=>$is_new, 'visible_date_validation'=>$date_validation_msg));
                wp_enqueue_style('wdm-accordion-style');
                include_once dirname(__FILE__).'/wdm_lesson_creation.php';
                wp_enqueue_script('wdm-select2-js');
                wp_enqueue_script('wdm-custom-js');
                wp_enqueue_script('wdm-validate-js');
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
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
                wp_enqueue_style('wdm-lesson-style');
                wp_enqueue_style('wdm-select2-style');
                wp_enqueue_script('wdm-accordion-script', array('jquery'));
                wp_enqueue_script('wdm-lesson-script');
                $date_validation_msg=sprintf(__('Date entered in "Make %s Visible on Specific Date" field must be greater then current date.', 'fcc'), LearnDash_Custom_Label::get_label('lesson'));
                wp_localize_script('wdm-lesson-script', 'wdm_lesson_data', array('is_new'=>$is_new, 'visible_date_validation'=>$date_validation_msg));
                wp_enqueue_style('wdm-accordion-style');
                include_once dirname(__FILE__).'/wdm_lesson_creation.php';
                wp_enqueue_script('wdm-select2-js', array('jquery'));
                wp_enqueue_script('wdm-custom-js', array('jquery'));
                wp_enqueue_script('wdm-validate-js', array('jquery'));
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
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_lesson_save()
    {
        global $wpdb;
        $wdm_flag = 0;
        $wdm_error = '';
        if (isset($_POST[ 'wdm_lesson_action' ])) {
            if ($_POST['title'] == '') {
                $wdm_error .= __('ERROR: Title is Required', 'fcc').'<br>';
                $wdm_flag = 1;
            }

            if ($wdm_flag == 1) {
                define('WDM_ERROR', $wdm_error);

                return;
            }
            $term_relationship = $wpdb->prefix.'term_relationships';
            $wdm_path_data = wp_upload_dir();
            $wdm_path = $wdm_path_data[ 'path' ];
            $wdm_url = $wdm_path_data[ 'url' ];
            if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
                $order_number = $_POST['order_number'];
            } else {
                $order_number = 0;
            }
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $post_status = get_option('wdm_fcc_post_status', 'draft');
            $lesson_id;

            if (isset($_POST[ 'lessonid' ])) {
                $lesson_id = $_POST[ 'lessonid' ];
                $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $lesson_id AND post_type like 'sfwd-lessons'";
                $author_id = $wpdb->get_var($sql);
                if ($author_id != get_current_user_id()) {
                    wp_die("cheating hu'h?");
                    exit;
                }
                $lesson_post = array(
                    'ID' => $lesson_id,
                    'post_title' => $wdm_title,
                    'post_content' => $wdm_content,
                    'post_status' => $post_status,
                    'post_author' => get_current_user_id(),
                    'menu_order'    => $order_number,
                );

                // Update the post into the database
                wp_update_post($lesson_post);
            } else {
                $post_sql = "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name LIKE '".sanitize_title($wdm_title)."'";
                $post_name = $wpdb->get_var($post_sql);
                if ($post_name == '') {
                    $post_name = sanitize_title($wdm_title);
                } else {
                    $post_name .= '-'.time();
                }
                $lesson = array(
                    'post_title' => $wdm_title,
                    'post_status' => $post_status,
                    'post_type' => 'sfwd-lessons',
                    'post_content' => $wdm_content,
                    'post_author' => get_current_user_id(),
                    'post_name' => $post_name,
                    'menu_order'    => $order_number,
                );


                $lesson_id = wp_insert_post($lesson);
            }


            $sql = "DELETE FROM $term_relationship WHERE object_id = $lesson_id";
            $wpdb->query($sql);
            //Start: WordPress Categories & Tags
            if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
                foreach ($_POST[ 'category' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $lesson_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                foreach ($_POST[ 'tag' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $lesson_id,
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
                        'object_id' => $lesson_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            if (isset($_POST[ 'ld_tag' ]) && (count($_POST[ 'ld_tag' ]) > 0)) {
                foreach ($_POST[ 'ld_tag' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $lesson_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            //End: LearnDash Categories & Tags

            if (isset($_FILES[ 'featured_image' ]) && $_FILES[ 'featured_image' ][ 'name' ] != '') {
                $extension = explode('.', $_FILES[ 'featured_image' ][ 'name' ]);
                $ext = $extension[ count($extension) - 1 ];
                $target_file = $wdm_path.'/'.$lesson_id.'.'.$ext;
                $target_file_url = $wdm_url.'/'.$lesson_id.'.'.$ext;
                move_uploaded_file($_FILES[ 'featured_image' ][ 'tmp_name' ], $target_file);
                wdm_insert_attachment($target_file_url, $lesson_id);
            }

            if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
                $this->saveLessonSettings($lesson_id);
            } else {
                $data = array();
                if (isset($_POST[ 'sfwd-lessons_lesson_materials' ])) {
                    $data[ 'sfwd-lessons_lesson_materials' ] = $_POST[ 'sfwd-lessons_lesson_materials' ];
                }
                if (isset($_POST[ 'sfwd-lessons_course' ])) {
                    $data[ 'sfwd-lessons_course' ] = $_POST[ 'sfwd-lessons_course' ];
                    update_post_meta($lesson_id, 'course_id', $_POST[ 'sfwd-lessons_course' ]);
                }
                if (isset($_POST[ 'sfwd-lessons_forced_lesson_time' ])) {
                    $data[ 'sfwd-lessons_forced_lesson_time' ] = $_POST[ 'sfwd-lessons_forced_lesson_time' ];
                }
                if (isset($_POST[ 'sfwd-lessons_lesson_assignment_upload' ])) {
                    $data[ 'sfwd-lessons_lesson_assignment_upload' ] = $_POST[ 'sfwd-lessons_lesson_assignment_upload' ];
                }
                if (isset($_POST[ 'sfwd-lessons_auto_approve_assignment' ])) {
                    $data[ 'sfwd-lessons_auto_approve_assignment' ] = $_POST[ 'sfwd-lessons_auto_approve_assignment' ];
                }

                if (isset($_POST['sfwd-lessons_lesson_assignment_points_enabled'])) {
                    $data['sfwd-lessons_lesson_assignment_points_enabled'] = $_POST['sfwd-lessons_lesson_assignment_points_enabled'];
                }

                if (isset($_POST[ 'sfwd-lessons_lesson_assignment_points_amount' ])) {
                    $data[ 'sfwd-lessons_lesson_assignment_points_amount' ] = $_POST[ 'sfwd-lessons_lesson_assignment_points_amount' ];
                }

                if (isset($_POST[ 'sfwd-lessons_assignment_upload_limit_count' ])) {
                    $data[ 'sfwd-lessons_assignment_upload_limit_count' ] = $_POST[ 'sfwd-lessons_assignment_upload_limit_count' ];
                }
                if (isset($_POST[ 'sfwd-lessons_lesson_assignment_deletion_enabled' ])) {
                    $data[ 'sfwd-lessons_lesson_assignment_deletion_enabled' ] = $_POST[ 'sfwd-lessons_lesson_assignment_deletion_enabled' ];
                }
                if (isset($_POST[ 'sfwd-lessons_assignment_upload_limit_extensions' ])) {
                    $data[ 'sfwd-lessons_assignment_upload_limit_extensions' ] = $_POST[ 'sfwd-lessons_assignment_upload_limit_extensions' ];
                }
                if (isset($_POST[ 'sfwd-lessons_assignment_upload_limit_size' ])) {
                    $data[ 'sfwd-lessons_assignment_upload_limit_size' ] = $_POST[ 'sfwd-lessons_assignment_upload_limit_size' ];
                }

                if (isset($_POST[ 'sfwd-lessons_sample_lesson' ])) {
                    $data[ 'sfwd-lessons_sample_lesson' ] = $_POST[ 'sfwd-lessons_sample_lesson' ];
                }
                if (isset($_POST[ 'sfwd-lessons_visible_after' ])) {
                    $data[ 'sfwd-lessons_visible_after' ] = $_POST[ 'sfwd-lessons_visible_after' ];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_enabled'])) {
                    $data['sfwd-lessons_lesson_video_enabled'] = $_POST['sfwd-lessons_lesson_video_enabled'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_url'])) {
                    $data['sfwd-lessons_lesson_video_url'] = $_POST['sfwd-lessons_lesson_video_url'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_auto_start'])) {
                    $data['sfwd-lessons_lesson_video_auto_start'] = $_POST['sfwd-lessons_lesson_video_auto_start'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_show_controls'])) {
                    $data['sfwd-lessons_lesson_video_show_controls'] = $_POST['sfwd-lessons_lesson_video_show_controls'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_shown'])) {
                    $data['sfwd-lessons_lesson_video_shown'] = $_POST['sfwd-lessons_lesson_video_shown'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_auto_complete'])) {
                    $data['sfwd-lessons_lesson_video_auto_complete'] = $_POST['sfwd-lessons_lesson_video_auto_complete'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_auto_complete_delay'])) {
                    $data['sfwd-lessons_lesson_video_auto_complete_delay'] = $_POST['sfwd-lessons_lesson_video_auto_complete_delay'];
                }
                if (isset($_POST['sfwd-lessons_lesson_video_hide_complete_button'])) {
                    $data['sfwd-lessons_lesson_video_hide_complete_button'] = $_POST['sfwd-lessons_lesson_video_hide_complete_button'];
                }
                if (isset($_POST[ 'sfwd-lessons_visible_after_specific_date' ])) {
                    if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=")) {
                        if (!empty($_POST[ 'sfwd-lessons_visible_after_specific_date' ])) {
                            $date=new DateTime($_POST[ 'sfwd-lessons_visible_after_specific_date' ]);
                            $timestamp=$date->getTimestamp();
                            $gmt_offset=get_option('gmt_offset');
                            if ($gmt_offset && $gmt_offset != '0') {
                                if (strpos($gmt_offset, '-') !== false) {
                                    $gmt_offset=str_replace('-', '', $gmt_offset);
                                    if (strpos($gmt_offset, '.') !== false) {
                                        $time=explode('.', $gmt_offset);
                                        if ($time[1]==5) {
                                            $time[1]=30;
                                        } elseif ($time[1]==75) {
                                            $time[1]=45;
                                        }
                                        $gmt_offset=(intval($time[0])*60)+intval($time[1]);
                                    } else {
                                        $gmt_offset=intval($gmt_offset)*60;
                                    }
                                    $timestamp=intval($timestamp)+($gmt_offset*60);
                                } else {
                                    if (strpos($gmt_offset, '.') !== false) {
                                        $time=explode('.', $gmt_offset);
                                        if ($time[1]==5) {
                                            $time[1]=30;
                                        } elseif ($time[1]==75) {
                                            $time[1]=45;
                                        }
                                        $gmt_offset=(intval($time[0])*60)+intval($time[1]);
                                    } else {
                                        $gmt_offset=intval($gmt_offset)*60;
                                    }
                                    $timestamp=intval($timestamp)-($gmt_offset*60);
                                }
                            }
                            $data[ 'sfwd-lessons_visible_after_specific_date' ] = $timestamp;
                        }
                    } else {
                        $data[ 'sfwd-lessons_visible_after_specific_date' ] = $_POST[ 'sfwd-lessons_visible_after_specific_date' ];
                    }
                }

                update_post_meta($lesson_id, '_sfwd-lessons', $data);
            }

            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_lesson_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?lessonid='.$lesson_id;
            if (!isset($_POST[ 'lessonid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            wp_redirect($link);
            exit;
        }
    }

    private function wdm_enqueue_action($table)
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
        wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))), array('jquery'));

        include_once dirname(__FILE__).'/wdm_lesson_list.php';
    }

    public function wdm_lesson_list()
    {
        ob_start();
        global $wpdb;
        global $current_user;
        $table = $wpdb->prefix.'posts';
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id()) || wdm_fcc_has_permissions()) {
                $this->wdm_enqueue_action($table);
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function saveLessonSettings($post_id)
    {
        if (empty($this->metaboxes)) {
            $this->loadMetaBoxes();
        }
        if (! empty($this->metaboxes)) {
            foreach ($this->metaboxes as $setting_key => $_metaboxes_instance) {
                if (apply_filters('learndash_show_metabox', true, $setting_key, 'sfwd-lessons')) {
                    $settings_fields = array();
                    $settings_fields = $_metaboxes_instance->get_post_settings_field_updates($post_id, get_post($post_id), true);
                    $_metaboxes_instance->save_post_meta_box($post_id, get_post($post_id), true, $settings_fields);
                }
            }
        }

        cmpUploadImageHandler($post_id, $_FILES);

        $this->saveCustomFields($post_id);

        $this->savePostCategories($post_id);
    }

    public function saveCustomFields($post_id)
    {
        $post_meta = get_post_meta($post_id, '_sfwd-lessons', true);
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
            $this->addTermTaxonomy($_POST[ 'ld_category' ], 'ld_'.$_POST['post_name'].'_category', $post_id);
        }
        if (isset($_POST[ 'ld_tag' ]) && (count($_POST[ 'ld_tag' ]) > 0)) {
            $this->addTermTaxonomy($_POST[ 'ld_tag' ], 'ld_'.$_POST['post_name'].'_tag', $post_id);
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

    public function loadMetaBoxes()
    {
        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-lesson-display-content.php';
        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-lesson-access-settings.php';
        $this->metaboxes = apply_filters('learndash_post_settings_metaboxes_init_sfwd-lessons', $this->metaboxes);
    }

    public function enqueueLessonScripts()
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

        $data = array();
        if (! empty($script_data)) {
            $data = $script_data;
        }

        $data = array( 'json' => json_encode($data) );

        wp_localize_script('sfwd-module-script', 'sfwd_data', $data);
        wp_enqueue_style('sfwd-module-style');

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
}

new Wdm_Lesson();
