<?php

if (!function_exists('fccGetCourseSteps')) {
    function fccGetCourseSteps($course_id, $data = '')
    {
        if (empty($data)) {
            $data = array();
        }

        // Get a list of lessons to loop.
        $lessons        = learndash_get_course_lessons_list($course_id, null, array( 'num' => 0 ));
        $output_lessons = [];

        if (( is_array($lessons) )  && ( ! empty($lessons) )) {
            // Loop course's lessons.
            foreach ($lessons as $lesson) {
                $post          = $lesson['post'];
                // Get lesson's topics.
                $topics        = learndash_topic_dots($post->ID, false, 'array', null, $course_id);
                $output_topics = [];

                if (( is_array($topics) )  && ( ! empty($topics) )) {
                    // Loop Topics.
                    foreach ($topics as $topic) {
                        // Get topic's quizzes.
                        $topic_quizzes        = learndash_get_lesson_quiz_list($topic->ID, null, $course_id);
                        $output_topic_quizzes = [];

                        if (( is_array($topic_quizzes) )  && ( ! empty($topic_quizzes) )) {
                            // Loop Topic's Quizzes.
                            foreach ($topic_quizzes as $quiz) {
                                $quiz_post = $quiz['post'];

                                $output_topic_quizzes[] = [
                                    'ID'         => $quiz_post->ID,
                                    'expanded'   => true,
                                    'post_title' => $quiz_post->post_title,
                                    'type'       => $quiz_post->post_type,
                                    'url'        => learndash_get_step_permalink($quiz_post->ID, $course_id),
                                    'edit_link'  => fccGetPostEditLink($quiz_post->ID, '', 'quiz'),
                                    'tree'       => [],
                                ];
                            }
                        }

                        $output_topics[] = [
                            'ID'         => $topic->ID,
                            'expanded'   => true,
                            'post_title' => $topic->post_title,
                            'type'       => $topic->post_type,
                            'url'        => learndash_get_step_permalink($topic->ID, $course_id),
                            'edit_link'  => fccGetPostEditLink($topic->ID, '', 'topic'),
                            'tree'       => $output_topic_quizzes,
                        ];
                    }
                }

                // Get lesson's quizzes.
                $quizzes        = learndash_get_lesson_quiz_list($post->ID, null, $course_id);
                $output_quizzes = [];
                
                if (( is_array($quizzes) )  && ( ! empty($quizzes) )) {
                    // Loop lesson's quizzes.
                    foreach ($quizzes as $quiz) {
                        $quiz_post = $quiz['post'];

                        $output_quizzes[] = [
                            'ID'         => $quiz_post->ID,
                            'expanded'   => true,
                            'post_title' => $quiz_post->post_title,
                            'type'       => $quiz_post->post_type,
                            'url'        => learndash_get_step_permalink($quiz_post->ID, $course_id),
                            'edit_link'  => fccGetPostEditLink($quiz_post->ID, '', 'quiz'),
                            'tree'       => [],
                        ];
                    }
                }

                // Output lesson with child tree.
                $output_lessons[] = [
                    'ID'         => $post->ID,
                    'expanded'   => false,
                    'post_title' => $post->post_title,
                    'type'       => $post->post_type,
                    'url'        => $lesson['permalink'],
                    'edit_link'  => fccGetPostEditLink($post->ID, '', 'lesson'),
                    'tree'       => array_merge($output_topics, $output_quizzes),
                ];
            }
        }

        // Get a list of quizzes to loop.
        $quizzes        = learndash_get_course_quiz_list($course_id);
        $output_quizzes = [];

        if (( is_array($quizzes) )  && ( ! empty($quizzes) )) {
            // Loop course's quizzes.
            foreach ($quizzes as $quiz) {
                $post = $quiz['post'];

                $output_quizzes[] = [
                    'ID'         => $post->ID,
                    'expanded'   => true,
                    'post_title' => $post->post_title,
                    'type'       => $post->post_type,
                    'url'        => learndash_get_step_permalink($post->ID, $course_id),
                    'edit_link'  => fccGetPostEditLink($post->ID, '', 'quiz'),
                    'tree'       => [],
                ];
            }
        }

        // Merge sections at Outline.
        $sections_raw = get_post_meta($course_id, 'course_sections', true);
        $sections     = ! empty($sections_raw) ? json_decode($sections_raw) : [];

        foreach ($sections as $section) {
            array_splice($output_lessons, (int) $section->order, 0, [ $section ]);
        }

        // Output data.
        $data['outline'] = [
            'lessons' => $output_lessons,
            'quizzes' => $output_quizzes,
            'sections' => $sections,
        ];

        return $data;
    }
}

if (! function_exists('fccGetQuizData')) {
    function fccGetQuizData($data)
    {
        $quiz_id = $data['post_data']['builder_post_id'];

        if (! empty($quiz_id)) {
            // Get quiz's questions.
            $questions_ids    = array_keys(learndash_get_quiz_questions($quiz_id));
            $output_questions = [];

            // Loop quiz's questions.
            foreach ($questions_ids as $question_id) {
                // Get answers from question.
                $question_pro_id = (int) get_post_meta($question_id, 'question_pro_id', true);
                $question_mapper = new \WpProQuiz_Model_QuestionMapper();

                if (! empty($question_pro_id)) {
                    $question_model = $question_mapper->fetch($question_pro_id);
                } else {
                    $question_model = $question_mapper->fetch(null);
                }

                $question_data       = $question_model->get_object_as_array();
                $controller_question = new \WpProQuiz_Controller_Question();

                if ($question_model && is_a($question_model, 'WpProQuiz_Model_Question')) {
                    $answers_data = $controller_question->setAnswerObject($question_model);
                } else {
                    $answers_data = $controller_question->setAnswerObject();
                }

                // Store answers in our format used at FE.
                $processed_answers = [];

                foreach ($answers_data as $answer_type => $answers) {
                    foreach ($answers as $answer) {
                        $processed_answers[ $answer_type ][] = [
                            'answer'             => $answer->getAnswer(),
                            'html'               => $answer->isHtml(),
                            'points'             => $answer->getPoints(),
                            'correct'            => $answer->isCorrect(),
                            'sortString'         => $answer->getSortString(),
                            'sortStringHtml'     => $answer->isSortStringHtml(),
                            'graded'             => $answer->isGraded(),
                            'gradingProgression' => $answer->getGradingProgression(),
                            'gradedType'         => $answer->getGradedType(),
                            'type'               => 'answer',
                        ];
                    }
                }

                // Output question's data and answers.
                $output_questions[] = [
                    'ID'              => $question_id,
                    'expanded'        => false,
                    'post_title'      => $question_data['_title'],
                    'post_content'    => $question_data['_question'],
                    'edit_link'       => fccGetPostEditLink($question_id, '', 'question', $quiz_id, $question_pro_id),
                    'type'            => get_post_type($question_id),
                    'question_type'   => $question_data['_answerType'],
                    'points'          => $question_data['_points'],
                    'answers'         => $processed_answers,
                    'correctMsg'      => $question_data['_correctMsg'],
                    'incorrectMsg'    => $question_data['_incorrectMsg'],
                    'correctSameText' => $question_data['_correctSameText'],
                    'tipEnabled'      => $question_data['_tipEnabled'],
                    'tipMsg'          => $question_data['_tipMsg'],
                ];
            }

            // Output all the quiz's questions.
            $data['outline'] = [
                'questions' => $output_questions,
            ];

            // Add labels and data to Quiz Builder at FE.
            $data['labels']['questions_types']             = $GLOBALS['learndash_question_types'];
            $data['questions_types_map']                   = [
                'single'             => 'classic_answer',
                'multiple'           => 'classic_answer',
                'sort_answer'        => 'sort_answer',
                'matrix_sort_answer' => 'matrix_sort_answer',
                'cloze_answer'       => 'cloze_answer',
                'free_answer'        => 'free_answer',
                'assessment_answer'  => 'assessment_answer',
                'essay'              => 'essay',
            ];
            $data['labels']['points']                      = [
                'singular' => esc_html__('point', 'learndash'),
                'plural'   => esc_html__('points', 'learndash'),
            ];
            $data['labels']['questions_types_description'] = [
                'free_answer'       => esc_html_x('correct answers (one per line) (answers will be converted to lower case)', 'Question type description for Free Answers', 'learndash'),
                'sort_answer'       => esc_html_x('Please sort the answers in the right order with the "move" button. The answers will be displayed randomly.', 'Question type description for Sort Answers', 'learndash'),
                'cloze_answer'      => [
                    wp_kses_post(__('Use <strong class="description-red">{ }</strong> to mark a gap and correct answer:<br /> <strong>I <span class="description-red">{</span>play<span class="description-red">}</span> soccer.</strong>', 'learndash')),
                    wp_kses_post(__('Use <strong class="description-red">[ ]</strong> to mark multiple correct answers:<br /> <strong>I {<span class="description-red">[</span>love<span class="description-red">][</span>hate<span class="description-red">]</span>} soccer.</strong>', 'learndash')),
                ],
                'essay'             => [
                    esc_html__('How should the user submit their answer?', 'learndash'),
                    sprintf(
                        // translators: placeholders: course
                        esc_html_x('This is a question that can be graded and potentially prevent a user from progressing to the next step of the %s.', 'placeholders: course', 'learndash'),
                        \learndash_get_custom_label_lower('course')
                    ),
                    esc_html__('The user can only progress if the essay is marked as "Graded" and if the user has enough points to move on.', 'learndash'),
                    sprintf(
                        // translators: placeholders: quiz
                        esc_html_x('How should the answer to this question be marked and graded upon %s submission?', 'placeholders: quiz', 'learndash'),
                        \learndash_get_custom_label_lower('quiz')
                    ),
                ],
                'assessment_answer' => [
                    wp_kses_post(__('Use <strong class="description-red">{ }</strong> to mark an assessment:<br /> <strong>Less true <span class="description-red">{</span> [1] [2] [3] [4] [5] <span class="description-red">}</span> More true</strong>', 'learndash')),
                    wp_kses_post(__('Use <strong class="description-red">[ ]</strong> to mark selectable items:<br /> <strong>Less true { <span class="description-red">[</span>A<span class="description-red">]</span> <span class="description-red">[</span>B<span class="description-red">]</span> <span class="description-red">[</span>C<span class="description-red">]</span> } More true</strong>', 'learndash')),
                ],
            ];
        }

        return $data;
    }
}

if (! function_exists('fccGetPostEditLink')) {
    function fccGetPostEditLink($post_id, $context, $type, $quiz_id = '', $pro_quiz_id = '')
    {
        global $wpdb;

        if (current_user_can('manage_options')) {
            return get_edit_post_link($post_id, $context);
        }

        if (wdm_is_course_author(get_current_user_id())) {
            switch ($type) {
                case 'course':
                    $shortcode = '[wdm_course_creation]';
                    $args = array('courseid' => $post_id);
                    break;
                case 'lesson':
                    $shortcode = '[wdm_lesson_creation]';
                    $args = array('lessonid' => $post_id);
                    break;
                case 'topic':
                    $shortcode = '[wdm_topic_creation]';
                    $args = array('topicid' => $post_id);
                    break;
                case 'quiz':
                    $shortcode = '[wdm_quiz_creation]';
                    $args = array('quizid' => $post_id);
                    break;

                case 'question':
                    $shortcode = '[wdm_question_creation]';
                    $args = array(
                        "questionid" => $pro_quiz_id,
                        "post_id" => $post_id,
                        'quiz_id' => $quiz_id
                    );
                    break;
                
                default:
                    $shortcode = '[wdm_course_creation]';
                    break;
            }
            $table =  $wpdb->prefix.'posts';

            $sql = $wpdb->prepare(
                "SELECT ID FROM $table WHERE post_content like %s AND post_status IN ('draft','publish','trash')",
                "%$shortcode%"
            );
            $result = $wpdb->get_var($sql);
            $link = get_permalink($result);

            if (! empty($link)) {
                $link = add_query_arg($args, $link);
            }

            return $link;
        }
    }
}

if (! function_exists('fccTemplate')) {
    function fccTemplate()
    {
        if (! array_key_exists('REQUEST_URI', $_SERVER)) {
            return false;
        }

        if (false !== strpos($_SERVER['REQUEST_URI'], 'wp-cron.php') || false !== strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php')) {
            return false;
        }

        $page_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        global $wp_rewrite, $wp;
        
        if (empty($wp_rewrite) && empty($wp)) {
            $wp_rewrite = new WP_Rewrite();
            $wp = new WP();
        }

        $post_id = url_to_postid($page_url);

        if (empty($post_id)) {
            return false;
        }

        $post = get_post($post_id);

        if (empty($post)) {
            return false;
        }

        $fcc_shortcodes = array(
            'wdm_topic_creation',
            'wdm_quiz_creation',
            'wdm_course_creation',
            'wdm_lesson_creation',
            'wdm_question_creation',
        );

        foreach($fcc_shortcodes as $shortcode) {
            if (false !== strpos($post->post_content, "[$shortcode]")) {
                return true;
            }
        }
        return false;
    }
}


if (!function_exists('get_current_screen') && !is_admin() && fccTemplate()) {
    function get_current_screen()
    {
        require_once 'fcc-screen.php';
        return \FccScreen::getInstance();
    }
}

//This function is written to avoid the get_settings_errors() function not exists error in the edit ld posts page
if (!function_exists('get_settings_errors') && !is_admin() && fccTemplate()) {
    function get_settings_errors($setting = '', $validate = false)
    {
        return array();
    }
}

if (! function_exists('cmpUploadImageHandler')) {
    function cmpUploadImageHandler($post_id, $files)
    {
        if (isset($files[ 'wdm_featured_image' ]) && $files[ 'wdm_featured_image' ][ 'name' ] != '') {
            $wdm_path_data = wp_upload_dir();
            $wdm_path = $wdm_path_data[ 'path' ];
            $wdm_url = $wdm_path_data[ 'url' ];
            if ($files['wdm_featured_image']['type'] == 'image/jpeg' || $files['wdm_featured_image']['type'] == 'image/png') {
                $extension = explode('.', $files[ 'wdm_featured_image' ][ 'name' ]);
                $ext = $extension[ count($extension) - 1 ];
                $target_file = $wdm_path.'/'.$post_id.'.'.$ext;
                $target_file_url = $wdm_url.'/'.$post_id.'.'.$ext;
                move_uploaded_file($files[ 'wdm_featured_image' ][ 'tmp_name' ], $target_file);
                cmpInsertAttachment($target_file_url, $post_id);
            }
        }
    }
}

if (! function_exists('fccGetMetaboxProperty')) {
    function fccGetMetaboxProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $value = $reflection->getProperty($property);
        $value->setAccessible(true);
        return $value->getValue($object);
    }
}