<?php

class FccScreen{

    public $id;
    public static $instance = null;

	public function __construct()
    {
        global $wp_query;
        $posts = $wp_query->get_posts();
        if (! empty($posts)) {
            $current_post = array_pop($posts);
            $post_content = $current_post->post_content;
            $matches = array();
            if (preg_match('/\[(wdm_+[A-Za-z_-]+)\]/', $post_content, $matches)) {
                $match = array_pop($matches);
                switch ($match) {
                    case 'wdm_course_creation':
                        $this->id = 'sfwd-courses';
                        break;
                    case 'wdm_lesson_creation':
                        $this->id = 'sfwd-lessons';
                        break;
                    case 'wdm_topic_creation':
                        $this->id = 'sfwd-topic';
                        break;
                    case 'wdm_quiz_creation':
                        $this->id = 'sfwd-quiz';
                        break;
                    case 'wdm_question_creation':
                        $this->id = 'sfwd-question';
                        break;
                    
                    default:
                        $this->id = 'sfwd-courses';
                        break;
                }
            }
        }

        if (! empty($_POST) && array_key_exists('fcc-post-type', $_POST)) {
            $this->id = $_POST['fcc-post-type'];
        }
    }

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}