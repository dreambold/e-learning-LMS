<?php
/**
 * Class Woffice_Reports
 *
 * Manage everything related to the Hub reports
 *
 * @since 2.5.2
 * @author Alkaweb
 */

if( ! class_exists( 'Woffice_Reports' ) ) {
	/**
	 * Class Woffice_Reports
	 *
	 * @deprecated 2.8.2
	 */
    class Woffice_Reports
    {

        /**
         * Woffice_Reports constructor.
         */
        public function __construct()
        {
            add_action( 'wp_ajax_nopriv_hub_get_data', array($this, 'hub_get_data' ));
            add_action( 'wp_ajax_nopriv_hub_send_product', array($this, 'hub_send_product' ));
            add_action( 'wp_ajax_hub_get_data', array($this, 'hub_get_data' ));
            add_action( 'wp_ajax_hub_send_product', array($this, 'hub_send_product' ));
        }

        /**
         *
         * Provide data for the Hub
         *
         * @return array
         */
        protected function provideData()
        {

            global $wpdb;

            $data = array();

            //we get the number of posts
            $data['number_blog_posts'] = count(get_posts(array('numberposts'=>-1)));
            $data['number_wiki'] = count(get_posts(array('post_type' => 'wiki', 'numberposts' => -1)));
            $data['number_members_directory'] = count(get_posts(array('post_type' => 'directory', 'numberposts' => -1)));
            $data['number_projects'] = count(get_posts(array('post_type' => 'project', 'numberposts' => -1)));
            $data['number_comments'] = wp_count_comments();

            // Add the polls data
            $data = array_merge($data, $this->getPolls());

            $data['count_posts'] = $this->getGraphActivity('post');

            $data['count_wikis'] = $this->getGraphActivity('wiki');

            // Projects
            $data['recent_projects'] = $this->getRecentProjects();
            $data['count_due_tasks'] = $this->getNumberTasks();

            // Users data
            $data['number_users'] = count_users();
            $table_name_users = $wpdb->prefix . 'users';
            $data['recent_users'] = $wpdb->get_results("SELECT user_login, user_nicename, user_email FROM ".$table_name_users." ORDER BY ID DESC LIMIT 5");
            //Data about BuddyPress
            $data['community'] = array(
                'Members' => bp_core_get_total_member_count(),
                'Active Members' => bp_core_get_active_member_count(),
                'Groups' => groups_get_total_group_count(),
            );
            $bp_activities = bp_activity_get();
            $bp_activity = array();
            // We keep only relevant information
            foreach($bp_activities['activities'] as $key=>$activity) {
                $bp_activity[$key]['action'] = $activity->action;
                $bp_activity[$key]['component'] = $activity->component;
                $bp_activity[$key]['user_email'] = $activity->user_email;
                $bp_activity[$key]['user_nicename'] = $activity->user_nicename;
                // We only keep the 8 most recent activities
                if ($key == 7) break;
            }
            $data['bp_activity'] = $bp_activity;

            // Theme / Config related data
            $data['theme'] = wp_get_theme();
            $data['site_config'] = array(
                'Php Version' => PHP_VERSION,
                'Php Zip Extension' => extension_loaded('zip'),
                'Php Memory Limit' => round((ini_get('memory_limit')), 1),
                'Php Remote Calls' => ini_get('allow_url_fopen'),
                'Wordpress Version' => get_bloginfo('version'),
                'Wordpress Debug' => ((defined('WP_DEBUG') && true === WP_DEBUG) ? 'Enabled' : 'Disabled'),
                'Number of Plugins Enabled' => count(get_option('active_plugins')),
                'Wordpress Database' => DB_NAME
            );
            $data['update_data'] = wp_get_update_data();
            //we only keep relevant information related to the theme : name (child or no)
            $data['theme_info'] = array(
                'name' => $data['theme']->get( 'Name' ),
                //we test if it's the child/parent theme and display relevant version
                'version' => $data['theme']->get( 'Version' ) ? $data['theme']->get( 'Version' ) : wp_get_theme(get_template())->get('Version')
            );

            return $data;
        }

        /**
         * Provides site data to our API
         * Safely checked
         */
        public function hub_get_data()
        {

            // we get the ip address sending the request
           if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            // if the request doesn't come from the hub => we reject
            if ($ip !== "162.144.127.86") {
                echo json_encode(array(
                    'type' => 'error',
                    'message' => 'Wrong IP address'
                ));
                wp_die();
            }

            /*
             * We check if an email address is provided
             */
            $email = $_SERVER['HTTP_X_EMAIL'];
            if(empty($email)) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The email is missing from the request.'
                ]);
                wp_die();
            }


            /*
             * We check if admin email matches
             */
            if(($email !== get_option('admin_email'))) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The email provided does not match the admin of this website.'
                ]);
                wp_die();
            }

            // We check that this is a pro account, one more time
            if(!(Woffice_Pro::is_pro())) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'This account is not a pro account - please upgrade.'
                ]);
                wp_die();
            }

            /*
             * We check for the Product key
             */
            $product_key = $_SERVER['HTTP_X_PRODUCTKEY'];
            if(empty($product_key)) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The product key is missing from the request.'
                ]);
            }

            // we check if the product_key match the website product key
            if (($product_key !== Woffice_Pro::$product_key)) {
                echo json_encode(array(
                    'type' => 'error',
                    'message' => "Wrong product key"
                ));
                wp_die();
            }

            //so far so good : we provide data
            $provided_data = $this->provideData();

            // We build our array to send out
            $data = array(
                'activity' => array (
                    'blog' => $provided_data['number_blog_posts'],
                    'wiki' => $provided_data['number_wiki'],
                    'directory' => $provided_data['number_members_directory'],
                    'project' => $provided_data['number_projects'],
                ),
                'number_polls' => $provided_data['number_polls'],
                'polls' => $provided_data['polls'],
                'comments' => $provided_data['number_comments'],
                'recent_users' => $provided_data['recent_users'],
                'count_posts' => $provided_data['count_posts'],
                'count_wikis' => $provided_data['count_wikis'],
                'count_due_tasks' => $provided_data['count_due_tasks'],
                'recent_projects' => $provided_data['recent_projects'],
                'theme' => $provided_data['theme_info'],
                'updates' => $provided_data['update_data'],
                'admin_url' => serialize(admin_url()),
                'site_config' => $provided_data['site_config'],
                'community' => $provided_data['community'],
                'bp_activity' => $provided_data['bp_activity']
            );

            echo json_encode(array('success' => true, 'data' => $data));

            wp_die();
        }

        /**
         * We send the product key over to the hub
         * when adding a license from the hub
         */
        public function hub_send_product() {

            // we get the ip address sending the request
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            // if the request doesn't come from the hub => we reject
            if ($ip !== "162.144.127.86") {
                echo json_encode(array(
                    'type' => 'error',
                    'message' => 'Wrong IP address'
                ));
                wp_die();
            }

            /*
             * We check if an email address is provided
             */
            $email = $_SERVER['HTTP_X_EMAIL'];
            if(empty($email)) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The email is missing from the request.'
                ]);
                wp_die();
            }


            /*
             * We check if admin email matches
             */
            if(($email !== get_option('admin_email'))) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The email provided does not match the admin of this website.'
                ]);
                wp_die();
            }

            /*
           * We check if the pro key is provided
           */
            $pro_key = $_SERVER['HTTP_X_PROKEY'];
            if(empty($pro_key)) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The pro key is missing from the request.'
                ]);
                wp_die();
            }

            /*
             * We check if it matches
             */
            if($pro_key !== get_option('alka_pro_key')) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'The pro key provided does not match our records.'
                ]);
                wp_die();
            }

            // We have the right email address and this email address is associated with a Pro Account
            // We send over the product_key
            $data = array(
                'product_key' => Woffice_Pro::$product_key
            );

            echo json_encode(array('success' => true, 'data' => $data));

            wp_die();
        }


        /**
         *
         */
        private function getGraphActivity($post_type)
        {
            // Recent articles
            //WP_Query Arguments
            $args = array(
                'date_query' => array(
                    array(
                        'column' => 'post_date',
                        'after' => '1 week ago',
                    )
                ),
                'posts_per_page' => -1,
                'post_type' => $post_type
            );

            //Execute WP_Query (Results placed in $the_query)
            $the_query = new WP_Query($args);

            //The WordPress Loop
            if($the_query->have_posts()) {
                foreach ( $the_query->posts as $post_object ) {
                    $new_array[]['date'] = date('M j', strtotime($post_object->post_date));
                }
            } else { //If no posts found
                $new_array = null;
            }
            wp_reset_postdata();
            wp_reset_query();

            return $new_array;
        }

        /**
         * Format the projects for the Hub
         */
        private function getRecentProjects(){

            //QUERY $tax
            $query_args = array(
                'post_type' => 'project',
                'posts_per_page' => 6,
                'orderby'   => 'post_date',
                'order' => 'DESC',
            );

            // GET PROJECTS POSTS
            $projects = new WP_Query( $query_args );
            $i = 1;
            while($projects->have_posts()) : $projects->the_post();
                $projects_sent[$i]['permalink'] = get_the_permalink();
                $projects_sent[$i]['title'] = get_the_title();
                $projects_sent[$i]['percentage'] = woffice_projects_percentage();
                $project_todo_lists = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_todo_lists') : '';
                // WE track by tasks
                if (!empty($project_todo_lists)) {
                    $tasks_count = 0;
                    $tasks_done = 0;
                    foreach ($project_todo_lists as $todo) {
                        $tasks_count++;
                        if ($todo['done'] == TRUE) {
                            $tasks_done++;
                        }
                    }
                }
                $projects_sent[$i]['count'] = $tasks_count;
                $projects_sent[$i]['done'] = $tasks_done;
                $i++;
            endwhile;

            return $projects_sent;
        }

        /**
         * Send data about last week's tasks to the Hub
         */
        private function getNumberTasks()
        {

            //QUERY $tax
            $query_args = array(
                'post_type' => 'project',
                'posts_per_page' => -1,
            );

            // GET PROJECTS POSTS
            $projects = new WP_Query( $query_args );
            $i = 1;
            while($projects->have_posts()) : $projects->the_post();
                // We get the tasks
                $project_todo_lists = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_todo_lists') : '';
                // We loop through and filter the date
                if (!empty($project_todo_lists)) {

                    $today = new DateTime('now');
                    foreach ($project_todo_lists as $todo) {
                        $date_todo = date_create($todo['date']);
                        $date_diff = date_diff($date_todo, $today)->days;
                        // if the task was created during the last 7 days
                        if ($date_diff <= 7) {
                            $final_dates[]['date'] = $todo['date'];
                        }
                        unset($date_todo);
                        unset($date_diff);
                    }
                }
                if (is_array($final_dates) && sizeof($final_dates) > 0) {
                    $projects_sent = $final_dates;
                }
                $i++;
            endwhile;

            return $projects_sent;
        }

        /**
         * Get the polls data
         *
         * @return array
         */
        private function getPolls()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'woffice_poll';

            // Verify that the table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name)
                return [];

            $data = [];

            $data['number_polls'] = $wpdb->get_results("SELECT COUNT(*) AS number_polls FROM " . $table_name);
            $data['polls'] = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 5");

            return $data;
        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Reports();
