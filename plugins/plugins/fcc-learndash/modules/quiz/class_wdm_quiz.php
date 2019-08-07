<?php

class Wdm_Quiz
{
    public $course_builder;
    public $quiz_builder;
    private $quiz_id;
    public $metaboxes;

    /**
     * WPProQuiz Quiz instance.
     * This is used to bridge the WPProQuiz to WP systems.
     *
     * @var object $pro_quiz_edit WPProQuiz instance.
     */
    private $pro_quiz_edit = null;

    /**
     * Common array set within init_quiz_edit and used by other class functions.
     *
     * @var array $_get;
     */
    private $_get = array();

    /**
     * Common array set within init_quiz_edit and used by other class functions.
     *
     * @var array $_post;
     */
    private $_post = array();

    public function __construct()
    {
        add_shortcode('wdm_quiz_creation', array($this, 'wdm_quiz_creation'));
        add_action('init', array($this, 'wdm_quiz_save'));
        add_shortcode('wdm_quiz_list', array($this, 'wdm_quiz_list'));
        add_action('before_delete_post', array($this, 'wdm_quiz_delete'));
        //add_filter('learndash_quiz_email_admin',array($this,'wdm_quiz_email'),10,2);
        if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
            add_action('wp_ajax_update_quiz_builder', array($this, 'ajaxUpdateQuizBuilderData'));
            add_action('wp_enqueue_scripts', array($this, 'fccEnqueueScripts'));
            add_filter('body_class', array($this, 'fccAddBodyClasses'), 10, 1);
        }
    }

    private function wdm_enqueue_action()
    {
        wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-course-style', plugins_url('css/style.css', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_enqueue_script('wdm-quiz-script', plugins_url('js/wdm_quiz.js', dirname(dirname(__FILE__))), array('jquery'));
        wp_localize_script(
            'wdm-quiz-script',
            'wdm_quiz_script_object',
            array(
                'lesson_or_topic_string' => sprintf(__('-- Select a %s or %s --', 'fcc'), LearnDash_Custom_Label::get_label('lesson'), LearnDash_Custom_Label::get_label('topic'))
            )
        );
        wp_enqueue_script('wdm-question-script', plugins_url('js/wdm_question.js', dirname(dirname(__FILE__))), array('jquery'));

        wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
        include_once dirname(__FILE__).'/wdm_quiz_creation.php';
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
                    'ld_data' => $this->getQuizLearnDashData()
                )
            );
        }
    }

    public function wdm_quiz_creation()
    {
        ob_start();
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id()) || wdm_fcc_has_permissions()) {
                if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'enabled') == 'yes') {
                    if (isset($_GET['quizid'])) {
                        $this->quiz_id=$_GET['quizid'];
                    } else {
                        $quiz = array(
                            'post_title' => 'Auto Draft',
                            'post_status' => 'auto-draft',
                            'post_type' => 'sfwd-quiz',
                            'post_author' => get_current_user_id(),
                        );
                        $this->quiz_id = wp_insert_post($quiz);
                    }

                    global $learndash_shortcode_used;
                    $learndash_shortcode_used = true;

                    if (version_compare(LEARNDASH_VERSION, "3.0", '>=')) {
                        $this->fccEnqueuePostEditScripts();
                        $this->quizBuilderHeaderScripts();
                        $this->quiz_builder = new \Learndash_Admin_Metabox_Quiz_Builder();
                        $this->quiz_builder->builder_init($this->quiz_id);
                        $this->quiz_builder->builder_on_load();
                        $this->quizBuilderFooterScripts();
                        $this->quiz_builder->builder_admin_footer();
                    } elseif (version_compare(LEARNDASH_VERSION, "2.6.0", '>=')) {
                        $this->quiz_builder = new \Learndash_Admin_Metabox_Quiz_Builder();
                        $this->quiz_builder->builder_init($this->quiz_id);
                        $this->quiz_builder->builder_on_load();
                        $this->quiz_builder->builder_admin_footer();
                    }
                }
                $this->wdm_enqueue_action();
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_quiz_save()
    {
        $wdm_flag = 0;
        $wdm_error = '';
        $quiz_id;

        if (isset($_POST[ 'wdm_quiz_action' ])) {
            if ($_POST['title'] == '') {
                $wdm_error .= __('ERROR: Title is Required', 'fcc').'<br>';
                $wdm_flag = 1;
            }

            // if($_POST['wdm_content'] == ''){
            //  $wdm_error .= __('ERROR: Description is Required','fcc').'<br>';
            //  $wdm_flag = 1;
            // }

            if ($wdm_flag == 1) {
                define('WDM_ERROR', $wdm_error);
                return;
            }

            if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
                $order_number = $_POST['order_number'];
            } else {
                $order_number = 0;
            }

            global $wpdb;

            $term_relationship = $wpdb->prefix.'term_relationships';
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $post_status = get_option('wdm_fcc_post_status', 'draft');

            if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'enabled') == 'yes') {
                $this->quiz_builder = new \Learndash_Admin_Metabox_Quiz_Builder();
                $this->quiz_builder->builder_init($_POST['quizid']);
                $this->quiz_builder->save_course_builder($_POST['quizid'], get_post($_POST['quizid']), false);
            }

            if (isset($_POST[ 'quizid' ])) {
                //echo $wdm_content;exit;

                $quiz_id = $_POST[ 'quizid' ];
                $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $quiz_id AND post_type like 'sfwd-quiz'";
                $author_id = $wpdb->get_var($sql);
                if ($author_id != get_current_user_id()) {
                    wp_die("cheating hu'h?");
                    exit;
                }
                $quiz_post = array(
                    'ID' => $quiz_id,
                    'post_title' => $wdm_title,
                    'post_content' => $wdm_content,
                    'post_status' => $post_status,
                    'post_author' => get_current_user_id(),
                    'menu_order'  => $order_number,
                );
                // Update the post into the database
                wp_update_post($quiz_post);
            } else {
                $post_sql = "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name LIKE '".sanitize_title($wdm_title)."'";
                $post_name = $wpdb->get_var($post_sql);
                if ($post_name == '') {
                    $post_name = sanitize_title($wdm_title);
                } else {
                    $post_name .= '-'.time();
                }
                $quiz = array(
                    'post_title' => $wdm_title,
                    'post_status' => $post_status,
                    'post_type' => 'sfwd-quiz',
                    'post_content' => $wdm_content,
                    'post_author' => get_current_user_id(),
                    'post_name' => $post_name,
                    'menu_order'  => $order_number,
                );

                //$is_visible = ($course->visible == 1 ? 'visible' : 'private');
                //$sync_log .= "<br />Course Created: ".$course->fullname."<br /> <br />";
                //generate a random unique sku for courses imported.
                //$sku = "course_".mt_rand();
                $quiz_id = wp_insert_post($quiz);
            }

            if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) {
                $this->saveQuizSettings($_POST['quizid']);
            } else {
                if (isset($_POST['viewProfileStatistics'])) {
                    update_post_meta($quiz_id, '_timeLimitCookie', $_POST['timeLimitCookie']);
                }

                if (isset($_POST['viewProfileStatistics'])) {
                    update_post_meta($quiz_id, '_viewProfileStatistics', $_POST['viewProfileStatistics']);
                } else {
                    update_post_meta($quiz_id, '_viewProfileStatistics', '');
                }

                $sql = "DELETE FROM $term_relationship WHERE object_id = $quiz_id";
                $wpdb->query($sql);
                if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
                    foreach ($_POST[ 'category' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $quiz_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }
                if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                    foreach ($_POST[ 'tag' ] as $k => $v) {
                        $category_data = array(
                            'object_id' => $quiz_id,
                            'term_taxonomy_id' => $v,
                        );
                        $wpdb->insert($term_relationship, $category_data);
                    }
                }

                $wdm_path_data = wp_upload_dir();
                $wdm_path = $wdm_path_data[ 'path' ];
                $wdm_url = $wdm_path_data[ 'url' ];
                if (isset($_FILES[ 'featured_image' ]) && $_FILES[ 'featured_image' ][ 'name' ] != '') {
                    $extension = explode('.', $_FILES[ 'featured_image' ][ 'name' ]);
                    $ext = $extension[ count($extension) - 1 ];
                    $target_file = $wdm_path.'/'.$quiz_id.'.'.$ext;
                    $target_file_url = $wdm_url.'/'.$quiz_id.'.'.$ext;
                    move_uploaded_file($_FILES[ 'featured_image' ][ 'tmp_name' ], $target_file);
                    wdm_insert_attachment($target_file_url, $quiz_id);
                }
                $data = array();
                if (isset($_POST[ 'sfwd-quiz_quiz_materials' ])) {
                    $data[ 'sfwd-quiz_quiz_materials' ] = $_POST[ 'sfwd-quiz_quiz_materials' ];
                }
                if (isset($_POST[ 'sfwd-quiz_course' ])) {
                    $data[ 'sfwd-quiz_course' ] = $_POST[ 'sfwd-quiz_course' ];
                    update_post_meta($quiz_id, 'course_id', $_POST[ 'sfwd-quiz_course' ]);
                }
                if (isset($_POST[ 'sfwd-quiz_repeats' ])) {
                    $data[ 'sfwd-quiz_repeats' ] = $_POST[ 'sfwd-quiz_repeats' ];
                }
                if (isset($_POST[ 'sfwd-quiz_threshold' ])) {
                    $data[ 'sfwd-quiz_threshold' ] = $_POST[ 'sfwd-quiz_threshold' ];
                }
                if (isset($_POST[ 'sfwd-quiz_passingpercentage' ])) {
                    $data[ 'sfwd-quiz_passingpercentage' ] = $_POST[ 'sfwd-quiz_passingpercentage' ];
                }
                if (isset($_POST[ 'sfwd-quiz_lesson' ])) {
                    $data[ 'sfwd-quiz_lesson' ] = $_POST[ 'sfwd-quiz_lesson' ];
                    update_post_meta($quiz_id, 'lesson_id', $_POST[ 'sfwd-quiz_lesson' ]);
                }
                if (isset($_POST[ 'sfwd-quiz_certificate' ])) {
                    $data[ 'sfwd-quiz_certificate' ] = $_POST[ 'sfwd-quiz_certificate' ];
                }

                $toplist_data = array(
                    'toplistDataAddPermissions' => (isset($_POST[ 'toplistDataAddPermissions' ]) ? $_POST[ 'toplistDataAddPermissions' ] : ''),
                    'toplistDataSort' => (isset($_POST[ 'toplistDataSort' ]) ? $_POST[ 'toplistDataSort' ] : ''),
                    'toplistDataAddMultiple' => (isset($_POST[ 'toplistDataAddMultiple' ]) ? $_POST[ 'toplistDataAddMultiple' ] : ''),
                    'toplistDataAddBlock' => (isset($_POST[ 'toplistDataAddBlock' ]) ? $_POST[ 'toplistDataAddBlock' ] : ''),
                    'toplistDataShowLimit' => (isset($_POST[ 'toplistDataShowLimit' ]) ? $_POST[ 'toplistDataShowLimit' ] : ''),
                    'toplistDataShowIn' => (isset($_POST[ 'toplistDataShowIn' ]) ? $_POST[ 'toplistDataShowIn' ] : ''),
                    'toplistDataCaptcha' => (isset($_POST[ 'toplistDataCaptcha' ]) ? $_POST[ 'toplistDataCaptcha' ] : ''),
                    'toplistDataAddAutomatic' => (isset($_POST[ 'toplistDataAddAutomatic' ]) ? $_POST[ 'toplistDataAddAutomatic' ] : ''),
                );
                $toplist_data = serialize($toplist_data);
                //echo "<pre>";print_r($toplist_data);echo "</pre>";
                if (isset($_POST['resultGradeEnabled'])) {
                    $resultText_temp = array();
                    $result_data = $_POST['resultTextGrade'];
                    foreach ($result_data['activ'] as $k => $v) {
                        if ($v == 1) {
                            $resultText_temp['text'][] = $result_data['text'][$k];
                            $resultText_temp['prozent'][] = $result_data['prozent'][$k];
                        }
                    }
                    $resultText = serialize($resultText_temp);
                } else {
                    $resultText = (isset($_POST[ 'resultText' ]) ? $_POST[ 'resultText' ] : '');
                }

                $quiz_master = array(
                    'name' => $wdm_title,
                    'text' => (isset($_POST[ 'text' ]) ? $_POST[ 'text' ] : ''),
                    'result_text' => $resultText,
                    'result_grade_enabled' => (isset($_POST[ 'resultGradeEnabled' ]) ? $_POST[ 'resultGradeEnabled' ] : ''),
                    'title_hidden' => (isset($_POST[ 'titleHidden' ]) ? $_POST[ 'titleHidden' ] : 0),
                    'btn_restart_quiz_hidden' => (isset($_POST[ 'btnRestartQuizHidden' ]) ? $_POST[ 'btnRestartQuizHidden' ] : 0),
                    'btn_view_question_hidden' => (isset($_POST[ 'btnViewQuestionHidden' ]) ? $_POST[ 'btnViewQuestionHidden' ] : 0),
                    'question_random' => (isset($_POST[ 'questionRandom' ]) ? $_POST[ 'questionRandom' ] : 0),
                    'answer_random' => (isset($_POST[ 'answerRandom' ]) ? $_POST[ 'answerRandom' ] : 0),
                    'sort_categories' => (isset($_POST[ 'sortCategories' ]) ? $_POST[ 'sortCategories' ] : 0),
                    'time_limit' => (isset($_POST[ 'timeLimit' ]) ? $_POST[ 'timeLimit' ] : 0),
                    'statistics_on' => (isset($_POST[ 'statisticsOn' ]) ? $_POST[ 'statisticsOn' ] : 0),
                    'statistics_ip_lock' => (isset($_POST[ 'statisticsIpLock' ]) ? $_POST[ 'statisticsIpLock' ] : 0),
                    'show_points' => (isset($_POST[ 'showPoints' ]) == 'on' ? 1 : 0),
                    'quiz_run_once' => (isset($_POST[ 'quizRunOnce' ]) ? $_POST[ 'quizRunOnce' ] : 0),
                    'quiz_run_once_type' => (isset($_POST[ 'quizRunOnceType' ]) ? $_POST[ 'quizRunOnceType' ] : 0),
                    'quiz_run_once_cookie' => (isset($_POST[ 'quizRunOnceCookie' ]) ? $_POST[ 'quizRunOnceCookie' ] : 0),
                    'numbered_answer' => (isset($_POST[ 'numberedAnswer' ]) ? $_POST[ 'numberedAnswer' ] : 0),
                    'hide_answer_message_box' => (isset($_POST[ 'hideAnswerMessageBox' ]) ? $_POST[ 'hideAnswerMessageBox' ] : 0),
                    'disabled_answer_mark' => (isset($_POST[ 'disabledAnswerMark' ]) ? $_POST[ 'disabledAnswerMark' ] : 0),
                    'show_max_question' => (isset($_POST[ 'showMaxQuestion' ]) ? $_POST[ 'showMaxQuestion' ] : 0),
                    'show_max_question_value' => (isset($_POST[ 'showMaxQuestionValue' ]) ? $_POST[ 'showMaxQuestionValue' ] : 0),
                    'show_max_question_percent' => (isset($_POST[ 'showMaxQuestionPercent' ]) ? $_POST[ 'showMaxQuestionPercent' ] : 0),
                    'toplist_activated' => (isset($_POST[ 'toplistActivated' ]) ? $_POST[ 'toplistActivated' ] : 0),
                    'toplist_data' => $toplist_data,
                    'show_average_result' => (isset($_POST[ 'showAverageResult' ]) ? $_POST[ 'showAverageResult' ] : 0),
                    'prerequisite' => (isset($_POST[ 'prerequisite' ]) ? $_POST[ 'prerequisite' ] : 0),
                    'quiz_modus' => (isset($_POST[ 'quizModus' ]) ? $_POST[ 'quizModus' ] : 0),
                    'show_review_question' => (isset($_POST[ 'showReviewQuestion' ]) ? $_POST[ 'showReviewQuestion' ] : 0),
                    'quiz_summary_hide' => (isset($_POST[ 'quizSummaryHide' ]) ? $_POST[ 'quizSummaryHide' ] : 0),
                    'skip_question_disabled' => (isset($_POST[ 'skipQuestionDisabled' ]) ? $_POST[ 'skipQuestionDisabled' ] : 0),
                    'email_notification' => (isset($_POST[ 'emailNotification' ]) ? $_POST[ 'emailNotification' ] : 0),
                    'user_email_notification' => (isset($_POST[ 'userEmailNotification' ]) ? $_POST[ 'userEmailNotification' ] : 0),
                    'show_category_score' => (isset($_POST[ 'showCategoryScore' ]) ? $_POST[ 'showCategoryScore' ] : 0),
                    'hide_result_correct_question' => (isset($_POST[ 'hideResultCorrectQuestion' ]) ? $_POST[ 'hideResultCorrectQuestion' ] : 0),
                    'hide_result_quiz_time' => (isset($_POST[ 'hideResultQuizTime' ]) ? $_POST[ 'hideResultQuizTime' ] : 0),
                    'hide_result_points' => (isset($_POST[ 'hideResultPoints' ]) ? $_POST[ 'hideResultPoints' ] : 0),
                    'autostart' => (isset($_POST[ 'autostart' ]) ? $_POST[ 'autostart' ] : 0),
                    'forcing_question_solve' => (isset($_POST[ 'forcingQuestionSolve' ]) ? $_POST[ 'forcingQuestionSolve' ] : 0),
                    'hide_question_position_overview' => (isset($_POST[ 'hideQuestionPositionOverview' ]) ? $_POST[ 'hideQuestionPositionOverview' ] : 0),
                    'hide_question_numbering' => (isset($_POST[ 'hideQuestionNumbering' ]) ? $_POST[ 'hideQuestionNumbering' ] : 0),
                    'form_activated' => (isset($_POST[ 'formActivated' ]) ? $_POST[ 'formActivated' ] : 0),
                    'form_show_position' => (isset($_POST[ 'formShowPosition' ]) ? $_POST[ 'formShowPosition' ] : 0),
                    'start_only_registered_user' => (isset($_POST[ 'startOnlyRegisteredUser' ]) ? $_POST[ 'startOnlyRegisteredUser' ] : 0),
                    'questions_per_page' => (isset($_POST[ 'questionsPerPage' ]) ? $_POST[ 'questionsPerPage' ] : 0),
                    'show_category' => (isset($_POST[ 'showCategory' ]) ? $_POST[ 'showCategory' ] : 0),
                );

                if (isset($_POST[ 'sfwd-quiz_quiz_pro' ])) {
                    $data[ 'sfwd-quiz_quiz_pro' ] = $_POST[ 'sfwd-quiz_quiz_pro' ];

                    $wpdb->update($wpdb->prefix.'wp_pro_quiz_master', $quiz_master, array('id' => $_POST[ 'sfwd-quiz_quiz_pro' ]));

                    $quiz_master_id = $_POST[ 'sfwd-quiz_quiz_pro' ];
                } else {
                    $wpdb->insert($wpdb->prefix.'wp_pro_quiz_master', $quiz_master);
                    $data[ 'sfwd-quiz_quiz_pro' ] = $wpdb->insert_id;
                    $quiz_master_id = $wpdb->insert_id;
                    update_post_meta($quiz_id, 'quiz_pro_id', $quiz_master_id);
                    update_post_meta($quiz_id, 'quiz_pro_id_'.$quiz_master_id, $quiz_master_id);
                }
                $wdm_custom_field_data = array();
                $i = 0;
                //echo "<pre>";print_R($_POST['form']);echo "</pre>";
                $sql = "DELETE FROM {$wpdb->prefix}wp_pro_quiz_form WHERE quiz_id = ".$quiz_master_id;
                $results = $wpdb->query($sql);
                if (isset($_POST['formActivated'])) {
                    if (isset($_POST[ 'form' ])) {
                        foreach ($_POST[ 'form' ] as $k => $v) {
                            if ($k > 4) {
                                foreach ($v as $key => $value) {
                                    if ($key == 'form_id' && $value != 0) {
                                        $wdm_custom_field_data[ $key ] = $value;
                                    } elseif ($key == 'form_delete') {
                                        $wdm_custom_field_data[ 'quiz_id' ] = $quiz_master_id;
                                    //echo "<pre>";print_r($wdm_custom_field_data);echo "</pre>";exit;
                                        $wdm_custom_field_data[ 'sort' ] = $i;
                                        ++$i;
                                        $wpdb->insert($wpdb->prefix.'wp_pro_quiz_form', $wdm_custom_field_data);
                                        $wdm_custom_field_data = array();
                                    } elseif ($key == 'data') {
                                        if ($value != '') {
                                            $items = explode("\n", $value);
                                        //echo "<pre>";print_R($items);echo "</pre>";exit;
                                            $f[ 'data' ] = array();

                                            foreach ($items as $item) {
                                                $item = trim($item);

                                                if (!empty($item)) {
                                                    $f[ 'data' ][] = $item;
                                                }
                                            }

                                            $form_data_new = '["'.implode('","', $f[ 'data' ]).'"]';
                                        //update_option('wdm_temp',$form_data_new);
                                        //echo $form_data_new;
                                            $wdm_custom_field_data[ 'data' ] = $form_data_new;
                                        } else {
                                            $form_data_new = null;
                                        }
                                    } elseif ($key != 'form_id') {
                                        $wdm_custom_field_data[ $key ] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($_POST[ 'prerequisite' ])) {
                    if (isset($_POST[ 'prerequisiteList' ])) {
                        if (count($_POST[ 'prerequisiteList' ]) > 0) {
                            $sql = "DELETE FROM {$wpdb->prefix}wp_pro_quiz_prerequisite WHERE prerequisite_quiz_id = ".$quiz_master_id;
                            $results = $wpdb->query($sql);
                            $prerequisite_data = array();
                            foreach ($_POST[ 'prerequisiteList' ] as $k => $v) {
                                $prerequisite_data[ 'prerequisite_quiz_id' ] = $quiz_master_id;
                                $quizMeta = get_post_meta($v, '_sfwd-quiz', true);
                                // $prerequisite_data[ 'quiz_id' ]               = $v;
                                $prerequisite_data[ 'quiz_id' ] = $quizMeta['sfwd-quiz_quiz_pro'];
                                $wpdb->insert($wpdb->prefix.'wp_pro_quiz_prerequisite', $prerequisite_data);
                                $prerequisite_data = array();
                            }
                        }
                    }
                }
                update_post_meta($quiz_id, '_sfwd-quiz', $data);
            }

            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_quiz_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?quizid='.$quiz_id;
            if (!isset($_POST[ 'quizid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            wp_redirect($link);
            exit;
        }
    }

    public function wdm_quiz_list()
    {
        ob_start();
        global $wpdb;
        global $current_user;
        $table = $wpdb->prefix.'posts';
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
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
                    )
                    )
                );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))), array('jquery'));

                include_once dirname(__FILE__).'/wdm_quiz_list.php';
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
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

                include_once dirname(__FILE__).'/wdm_quiz_list.php';
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_quiz_email($email, $quiz)
    {
        $pro_quiz_id = $quiz->getid();
    //$quiz_id = get_ld_quiz_id($pro_quiz_id);
        $ld_pro = new LD_QuizPro();
        $quiz_id = $ld_pro->get_ld_quiz_id($pro_quiz_id);
    //echo $quiz_id;
        $quiz_post = get_post($quiz_id);
        $author_id = $quiz_post->post_author;
        $user = get_user_by('id', $author_id);
        $author_email = $user->user_email;
    //echo "<pre>";print_R($author_email);echo "</pre>";exit;
    //exit;
        if ($email['email'] != '') {
            $email['email'] .= ','.$author_email;
        } else {
            $email['email'] = $author_email;
        }
//  $email['email'] = 'jignashu.solanki@wisdmlabs.com';
        return $email;
    }
    public function wdm_quiz_delete($postid)
    {
        //echo $postid;exit;
        // We check if the global post type isn't ours and just return
        global $post_type;
        global $wpdb;
        if ($post_type != 'sfwd-quiz') {
            return;
        }

        $sql = 'DELETE FROM '.$wpdb->prefix."usermeta WHERE meta_key like 'wdm_question_id%' AND meta_value like '$postid'";
        $wpdb->query($sql);

        // My custom stuff for deleting my custom post type here
    }

    /**
     * Load quiz builder header scripts
     *
     * @since 2.2.0
     */
    public function quizBuilderHeaderScripts()
    {
        wp_enqueue_editor();
        wp_enqueue_style(
            'fcc-learndash-quiz-header-style',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.css',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header.min.css',
            array(),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );
        wp_enqueue_script(
            'fcc-learndash-quiz-header-script',
            // LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ( ( defined( 'LEARNDASH_BUILDER_DEBUG' ) && ( LEARNDASH_BUILDER_DEBUG === true ) ) ? '' : '.min' ) . '.js',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/header.min.js',
            array( 'wp-i18n' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN,
            true
        );

        $learndash_data = $this->getQuizLearndashData();
        if (! empty($learndash_data)) {
            $css_lesson_label     = \LearnDash_Custom_Label::get_label('lesson')[0];
            $css_topic_label      = \LearnDash_Custom_Label::get_label('topic')[0];
            $css_quiz_label       = \LearnDash_Custom_Label::get_label('quiz')[0];
            $css_question_label   = \LearnDash_Custom_Label::get_label('question')[0];
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
            #sfwd-quiz-questions h2:before,
            .ld-question-overview-widget-item:before {
                content: '{$css_question_label}';
            }
            ";
            wp_add_inline_style('fcc-learndash-quiz-header-style', $learndash_custom_css);
            // error_log('LD Data : '.print_r($learndash_data, 1));
            wp_localize_script('fcc-learndash-quiz-header-script', 'LearnDashData', $learndash_data);
        }
    }

    /**
     * Load quiz builder footer scripts
     *
     * @since 2.2.0
     */
    public function quizBuilderFooterScripts()
    {
        wp_enqueue_style(
            'fcc-learndash-quiz-builder-style',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.css',
            //LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.css',
            array( 'wp-editor' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN
        );
        wp_enqueue_script(
            'fcc-learndash-quiz-builder-script',
            LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.js',
            //LEARNDASH_LMS_PLUGIN_URL . 'assets/js/builder/dist/builder.min.js',
            array( 'wp-i18n', 'fcc-learndash-quiz-header-script', 'wp-data' ),
            LEARNDASH_SCRIPT_VERSION_TOKEN,
            true
        );

        $builder_assets[ 'sfwd-quiz' ]['post_data']['builder_editor'] = 'block';
        $builder_assets[ 'sfwd-quiz' ]['post_data']['builder_class'] = 'Learndash_Admin_Metabox_Quiz_Builder';
        $builder_assets[ 'sfwd-quiz' ]['post_data']['builder_post_id'] = $this->quiz_id;
        $builder_assets[ 'sfwd-quiz' ]['post_data']['builder_post_title'] = get_the_title($this->quiz_id);
        $builder_assets[ 'sfwd-quiz' ]['post_data']['builder_post_type'] = 'sfwd-quiz';
        wp_localize_script('fcc-learndash-quiz-builder-script', 'learndash_builder_assets', $builder_assets);
    }

    /**
     * Get Header data for quiz builder
     * 
     * @since 2.2.0
     * @return $header_data     Header data for the quiz builder
     */
    public function getQuizHeaderData()
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
     * Get Learndash data
     *
     * @since 2.2.0
     * @return $learndash_data  LearndashData to be used by quiz builder
     */
    public function getQuizLearndashData()
    {
        $header_data = $this->getQuizHeaderData();
        
        $screen_post_type = 'sfwd-quiz';
        $header_data['post_data']['builder_post_id'] = $this->quiz_id;

        if (! empty($header_data['post_data']['builder_post_id'])) {
            $header_data['post_data']['builder_post_title'] = get_the_title($header_data['post_data']['builder_post_id']);
        }
        $header_data['post_data']['builder_post_type'] = $screen_post_type;
        $header_data['back_to_title'] = esc_html__('Back', 'learndash');
        $header_data['post_data']['builder_post_id'] = $this->quiz_id;
        $header_data['post_data']['builder_post_title'] = '';

        if (! empty($header_data['post_data']['builder_post_id'])) {
            $header_data['post_data']['builder_post_title'] = get_the_title($header_data['post_data']['builder_post_id']);
        }

        $post_type_object = get_post_type_object($screen_post_type);
        if ($post_type_object) {
            $header_data['back_to_title'] = sprintf(
                // translators: placeholder: Post Type Plural Name.
                esc_html_x('Back to %s', 'placeholder: Post Type Plural Name', 'learndash'),
                $post_type_object->labels->name
            );
        }

        $header_data['posts_per_page'] = \LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'per_page');

        // Load the MO file translations into wp.i18n script hook.
        learndash_load_inline_script_locale_data();

        $learndash_data = fccGetQuizData($header_data);

        return $learndash_data;
    }

    /**
     * Ajax method to update quiz builder data
     * 
     * @since 2.2.0
     */
    public function ajaxUpdateQuizBuilderData()
    {
        if (! empty($_POST) && array_key_exists('quiz_id', $_POST) && ! empty($_POST['quiz_id'])) {
            $quiz_id = intval($_POST['quiz_id']);
            $builder_data = $_POST['builder_data'];

            $learndash_data = array(
                'sfwd-quiz' => array(
                    $quiz_id  =>  $builder_data
                )
            );

            $_POST['learndash_builder'] = $learndash_data;
            $this->quiz_builder = new \Learndash_Admin_Metabox_Quiz_Builder();
            $this->quiz_builder->builder_init($quiz_id);
            $status = $this->quiz_builder->save_course_builder($quiz_id, get_post($quiz_id), false);

            echo $status;
        }
        wp_die();
    }

    public function saveQuizSettings($post_id)
    {
        // Check the Quiz custom fields to see if they need to be reformatted.
        if ( isset( $_POST['form'] ) ) {
            $form = $_POST['form'];
            if ( 1 === count( $form[0] ) ) {
                $form_items = array();
                $form_item  = array();
                foreach ( $form as $form_ele ) {
                    foreach ( $form_ele as $form_ele_name => $form_ele_value ) {
                        if ( 'fieldname' === $form_ele_name ) {
                            if ( ! empty( $form_item ) ) {
                                $form_items[] = $form_item;
                            }
                            $form_item = array();
                        }
                        $form_item[ $form_ele_name ] = $form_ele_value;
                    }
                }
                if ( ! empty( $form_item ) ) {
                    $form_items[] = $form_item;
                }
                $form_item     = array();
                $_POST['form'] = $form_items;
            }
        }

        $this->fccInitQuizEdit($post_id);

        if (empty($this->metaboxes)) {
            $this->loadMetaBoxes();
        }
        if (! empty($this->metaboxes)) {
            foreach ($this->metaboxes as $_metaboxes_instance) {
                $settings_fields = $_metaboxes_instance->get_post_settings_field_updates($post_id, get_post($post_id), true);
                $_metaboxes_instance->save_post_meta_box($post_id, get_post($post_id), true, $settings_fields);
                $_metaboxes_instance->save_fields_to_post($this->pro_quiz_edit, $settings_fields);
            }
        }
        $quizId   = absint(learndash_get_setting($post_id, 'quiz_pro', true));
        $_POST['post_ID'] = $post_id;
        $pro_quiz = new \WpProQuiz_Controller_Quiz();
        $pro_quiz->route(
            array(
                'action'  => 'addUpdateQuiz',
                'quizId'  => $quizId,
                'post_id' => $post_id,
            )
        );

        // parent::saveOtherPostDetails($post_id);
        cmpUploadImageHandler($post_id, $_FILES);

        $this->savePostCategories($post_id);
    }

    public function loadMetaBoxes()
    {
        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-quiz-access-settings.php';

        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-quiz-progress-settings.php';

        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-quiz-display-content.php';

        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-quiz-results-display-content-options.php';

        require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/settings/settings-metaboxes/class-ld-settings-metabox-quiz-admin-data-handling-settings.php';

        $this->metaboxes = apply_filters('learndash_post_settings_metaboxes_init_sfwd-quiz', $this->metaboxes);
    }

    public function fccInitQuizEdit($post_id)
    {
        $post = get_post($post_id);
        if ( is_null( $this->pro_quiz_edit ) ) {
            $quiz_pro_id = (int) learndash_get_setting( $post->ID, 'quiz_pro' );

            $this->_post = array( '1' );
            $this->_get  = array(
                'action'  => 'getEdit',
                'quizId'  => $quiz_pro_id,
                'post_id' => $post->ID,
            );

            if ( ( isset( $_GET['templateLoadId'] ) ) && ( ! empty( $_GET['templateLoadId'] ) ) ) {
                $this->_get['templateLoad']   = 'yes';
                $this->_get['templateLoadId'] = $_GET['templateLoadId'];
            }

            $pro_quiz            = new WpProQuiz_Controller_Quiz();
            $this->pro_quiz_edit = $pro_quiz->route(
                $this->_get,
                $this->_post
            );
        }
    }

    public function fccEnqueuePostEditScripts()
    {
        global $wp_locale;
        // global $wp_query;

        $this->enqueueQuizModuleScripts();
        $this->enqueueQuizAdminScripts();


        $isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;

        $translation_array = array(
            'delete_msg' => sprintf(esc_html_x('Do you really want to delete the %s/question?', 'Do you really want to delete the quiz/question?', 'fcc'), \LearnDash_Custom_Label::label_to_lower('quiz')),
            'no_title_msg' => esc_html__('Title is not filled!', 'fcc'),
            'no_question_msg' => esc_html__('No question deposited!', 'fcc'),
            'no_correct_msg' => esc_html__('Correct answer was not selected!', 'fcc'),
            'no_answer_msg' => esc_html__('No answer deposited!', 'fcc'),
            'no_quiz_start_msg' => sprintf(esc_html_x('No %s description filled!', 'No quiz description filled!', 'fcc'), \LearnDash_Custom_Label::label_to_lower('quiz')),
            'fail_grade_result' => esc_html__('The percent values in result text are incorrect.', 'fcc'),
            'no_nummber_points' => esc_html__('No number in the field "Points" or less than 1', 'fcc'),
            'no_nummber_points_new' => esc_html__('No number in the field "Points" or less than 0', 'fcc'),
            'no_selected_quiz' => sprintf(esc_html_x('No %s selected', 'No quiz selected', 'fcc'), \LearnDash_Custom_Label::label_to_lower('quiz')),
            'reset_statistics_msg' => esc_html__('Do you really want to reset the statistic?', 'fcc'),
            'no_data_available' => esc_html__('No data available', 'fcc'),
            'no_sort_element_criterion' => esc_html__('No sort element in the criterion', 'fcc'),
            'dif_points' => esc_html__('"Different points for every answer" is not possible at "Free" choice', 'fcc'),
            'category_no_name' => esc_html__('You must specify a name.', 'fcc'),
            'confirm_delete_entry' => esc_html__('This entry should really be deleted?', 'fcc'),
            'not_all_fields_completed' => esc_html__('Not all fields completed.', 'fcc'),
            'temploate_no_name' => esc_html__('You must specify a template name.', 'fcc'),
            'no_delete_answer' => esc_html__('Cannot delete only answer', 'fcc'),
            'closeText'         => esc_html__('Close', 'fcc'),
            'currentText'       => esc_html__('Today', 'fcc'),
            'monthNames'        => array_values($wp_locale->month),
            'monthNamesShort'   => array_values($wp_locale->month_abbrev),
            'dayNames'          => array_values($wp_locale->weekday),
            'dayNamesShort'     => array_values($wp_locale->weekday_abbrev),
            'dayNamesMin'       => array_values($wp_locale->weekday_initial),
            'dateFormat'        => \WpProQuiz_Helper_Until::convertPHPDateFormatToJS(get_option('date_format', 'm/d/Y')),
            'firstDay'          => get_option('start_of_week'),
            'isRTL'             => $isRtl
        );
        wp_enqueue_script('wdmwpProQuiz_admin_javascript');
        wp_localize_script('wdmwpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
    }

    public function fccEnqueueScripts()
    {
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

        wp_register_script(
            'wdmwpProQuiz_admin_javascript',
            plugins_url('js/wpProQuiz_admin.js', WPPROQUIZ_FILE)
        );
    }

    public function enqueueQuizModuleScripts()
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

    public function enqueueQuizAdminScripts()
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

    public function fccAddBodyClasses($classes)
    {
        $classes[] = 'post-type-sfwd-quiz';

        return $classes;
    }
}

new Wdm_Quiz();
