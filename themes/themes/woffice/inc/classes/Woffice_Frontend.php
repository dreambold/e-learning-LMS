<?php
/**
 * Class Woffice_Frontend
 *
 * This class handle the frontend actions: form creation and submitting for every post type
 *
 * @since 2.3.1
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_Frontend' ) && ! defined( 'WOFFICE_DEACTIVATE_FRONTEND' ) ) {
    class Woffice_Frontend
    {

        /**
         * Woffice_Frontend constructor
         */
        public function __construct()
        {
            add_action('wp_ajax_nopriv_wofficeTaxonomyFetching', array($this,'taxonomy_fetching'));
            add_action('wp_ajax_wofficeTaxonomyFetching', array($this,'taxonomy_fetching'));
            add_action('wp_ajax_nopriv_wofficeTaxonomyAdd', array($this,'taxonomy_add'));
            add_action('wp_ajax_wofficeTaxonomyAdd', array($this,'taxonomy_add'));
        }

        /**
         * Processing the data sent by the form
         * -
         * This function process the data from the FORM (see next function)
         *
         * @param string $type is the kind of post (project, wiki, directory or post)
         * @param bool $is_shortcode we can't redirect in the shortcode
         * @param $post_id int if it's an edit
         * @return mixed array\int on errors or post id
         */
        static function frontend_process( $type, $post_id = 0, $is_shortcode = false ) {
            
            /* We catch the errors */
            $errors = array();

            if ( !isset( $_POST['submitted'] ) || !isset( $_POST['post_nonce_field'] ) || !wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
                return $errors;
            };
            
            /**
             * We check that title isn't empty
             */
            if ( trim( $_POST['post_title'] ) === '' ) {
                $errors[] = __( 'Please enter a title.', 'woffice' );
                return $errors;
            }

            /**
             * Get the post status when submitted
             */
            $use_meta_caps = woffice_check_meta_caps( $type );
            if ( $use_meta_caps && ( $type == 'wiki' || $type == 'post' ) ) {
                $slug           = woffice_get_slug_for_meta_caps( $type );
                $prefix         = ( $type != 'post' ) ? 'woffice_' : '';
                $frontend_state = ( current_user_can( $prefix . 'publish_' . $slug ) ) ? 'publish' : 'draft';
            } else {
                $frontend_state = woffice_get_settings_option( 'frontend_state' );
            }

	        if  (!empty($post_id) && $post_id > 0) {
		        $frontend_state = 'publish';
	        }

            /**
             * The status of the created post in WordPress
             *
             * @param string $frontend_state
             * @param int $post_id
             */
            $frontend_state = apply_filters('woffice_frontend_state', $frontend_state, $post_id);

            /**
             * We insert or update the post:
             * We must get a $post_id out of this step
             */
            $post_information = array(
                'post_title'   => wp_strip_all_tags( $_POST['post_title'] ),
                'post_content' => $_POST['post_content'],
                'post_status'  => $frontend_state,
                'post_type'    => $type,
            );

            if ($type == 'directory') {
                $post_information['post_excerpt'] = '';
                $post_information['post_content'] = $_POST['post_content'];
            }

            if (!empty($post_id) && $post_id > 0) {
                $post_information['ID'] = $post_id;
                $post_id = wp_update_post( $post_information );
                $is_new = false;
            } else {
                $post_id = wp_insert_post( $post_information );
                $is_new = true;
            }

            if ($post_id == 0) {
                $errors[] = __( 'An error occurred, please try again later.', 'woffice' );
                return $errors;
            }

            /**
             * Wiki post type
             */
            if ( $type == 'wiki' ) {

                self::frontend_set_terms( $_POST['wiki_category'], $type, $post_id );

                fw_set_db_post_option( $post_id, 'everyone_edit', true );

            }
            /**
             * Projects post type
             */
            elseif ( $type == 'project' ) {

                self::frontend_set_terms( $_POST['project_category'], $type, $post_id );

	            // TODO anche solo uan data deve essere salvata
                /**
                 * We check the dates
                 */
	            $start_date = $end_date = '';
                if ( ! empty( $_POST['project_start'] ) || ! empty( $_POST['project_end'] ) ) {

                    $start_date = (! empty( $_POST['project_start'] )) ? wp_strip_all_tags( $_POST['project_start'] ) : '';
                    $end_date   = (! empty( $_POST['project_end'] )) ? wp_strip_all_tags( $_POST['project_end'] ) : '';

                    /* We check if dates are end isn't before date start */
	                if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
		                if ( strtotime( $start_date ) > strtotime( $end_date ) ) {
			                $errors[] = __( 'Dates are not correct, please check again.', 'woffice' );
		                }
	                }

                }

                /**
                 * Projects members
                 */
                $projects_users = array();

                /* We check if some members have been selected */
                if ( ! empty( $_POST['project_members'] ) ) {

	                if(!is_array($_POST['project_members']))
		                $_POST['project_members'] = explode(',', $_POST['project_members']);

                    foreach ( $_POST['project_members'] as $member ) {
                        $projects_users[] = $member;
                    }
                }

                $only_author_can_edit    = ( isset( $_POST['only_author_can_edit'] ) && $_POST['only_author_can_edit'] == 'yes' ) ? true : false;
                $calendar_sync           = ( isset( $_POST['calendar_sync'] ) && $_POST['calendar_sync'] == 'yes' ) ? true : false;
                $enable_todo             = ( isset( $_POST['enable_todo'] ) && $_POST['enable_todo'] == 'yes' ) ? true : false;
                $enable_files            = ( isset( $_POST['enable_files'] ) && $_POST['enable_files'] == 'yes' ) ? true : false;
                $project_archived        = ( isset( $_POST['project_archived'] ) && $_POST['project_archived'] == 'yes' ) ? true : false;
                $tracking_type           = ( isset( $_POST['tracking_type'] ) ) ? $_POST['tracking_type'] : 'time';
                $project_status          = ( isset( $_POST['project_current_status'] ) ) ? $_POST['project_current_status'] : 'planned';
                $project_calendar_choice = ( isset( $_POST['project_calendar_choice'] ) ) ? $_POST['project_calendar_choice'] : '';

                /* We save the values */
                fw_set_db_post_option( $post_id, 'project_date_start', $start_date );
                fw_set_db_post_option( $post_id, 'project_date_end', $end_date );
                fw_set_db_post_option( $post_id, 'project_completed', $project_archived );
                fw_set_db_post_option( $post_id, 'project_members', $projects_users );
                fw_set_db_post_option( $post_id, 'project_files', $enable_files );
                fw_set_db_post_option( $post_id, 'project_progress', $tracking_type );
                fw_set_db_post_option( $post_id, 'project_todo', $enable_todo );
                fw_set_db_post_option( $post_id, 'project_calendar', $calendar_sync );
                fw_set_db_post_option( $post_id, 'only_author_can_edit', $only_author_can_edit );
                fw_set_db_post_option( $post_id, 'project_current_status', $project_status );
                fw_set_db_post_option( $post_id, 'project_calendar_choice', $project_calendar_choice );


                if($is_new) {
                    fw_set_db_post_option( $post_id, 'project_edit', 'frontend-edit' );
                    fw_set_db_post_option( $post_id, 'project_links', array() );
                    fw_set_db_post_option( $post_id, 'project_todo_lists', array() );
                    fw_set_db_post_option( $post_id, 'project_wunderlist', '' );
                    fw_set_db_post_option( $post_id, 'single_project_public', 'default' );
                }

            }
            /**
             * Directory post type
             */
            elseif ( $type == 'directory' ) {

                self::frontend_set_terms( $_POST['directory_category'], $type, $post_id );

                /**
                 * We handle the custom fields attached to teh directory
                 */
                $item_button_icon = ( ! empty( $_POST['item_button_icon'] ) ) ? $_POST['item_button_icon'] : "";
                $item_button_text = ( ! empty( $_POST['item_button_text'] ) ) ? $_POST['item_button_text'] : "";
                $item_button_link = ( ! empty( $_POST['item_button_link'] ) ) ? $_POST['item_button_link'] : "";

                $location = "";

                if ( ! empty( $_POST['item_location_lng'] ) && ! empty( $_POST['item_location_lat'] ) ) {
                    $location = array(
                        'location'    => '',
                        'venue'       => '',
                        'address'     => '',
                        'city'        => '',
                        'state'       => '',
                        'country'     => '',
                        'zip'         => '',
                        'coordinates' => array(
                            'lat' => $_POST['item_location_lat'],
                            'lng' => $_POST['item_location_lng'],
                        )
                    );
                }

                $item_fields = (isset($_POST['item_fields'])) ? json_decode( preg_replace('/\\\\/', '', $_POST['item_fields']), true ) : array();
                if ($item_fields) {
	                fw_set_db_post_option( $post_id, 'item_fields', $item_fields );
                }

                fw_set_db_post_option( $post_id, 'item_location',    $location );
                fw_set_db_post_option( $post_id, 'item_button_text', $item_button_text );
                fw_set_db_post_option( $post_id, 'item_button_icon', $item_button_icon );
                fw_set_db_post_option( $post_id, 'item_button_link', $item_button_link );

            }

            /**
             * Blog post type
             */
            else {

                self::frontend_set_terms( $_POST['blog_category'], $type, $post_id );

                /**
                 * We set the edit capability
                 * By default, the "everyone_edit" field is true by frontend for non admins
                 */
                if ( woffice_current_is_admin() ) {
                    $edit_val = (isset( $_POST['everyone_edit'] ) && $_POST['everyone_edit'] == 'yes' ) ? true : false;

                } else {
                    $edit_val = true;
                }
                fw_set_db_post_option( $post_id, 'everyone_edit', $edit_val );
            }

            /**
             * We add the featured image
             */
            if ( $type != 'wiki' && $type != 'project' ) {

                self::featured_upload('post_thumbnail', $post_id);

            }
            
            /**
             * Fired after that an editing/creation by frontend has been processed by Woffice
             *
             * @param int $post_id
             * @param WP_POST
             */
	        do_action( 'woffice_after_frontend_process', $post_id, get_post($post_id));

            /**
             * We return our values
             */
            if ( empty($errors) ) {
                
                $process_type = ($is_new) ? 'created' : 'updated';

                /**
                 * Fired after that an editing/creation by frontend has been completed successfully (without errors) by Woffice
                 *
                 * @param int $post_id
                 * @param WP_Post $post
                 */
                do_action('woffice_frontend_process_completed_success', $post_id, get_post($post_id), $process_type);

                /**
                 * Fire specific action of Woffice to some post types
                 *
                 * The dynamic portion of the hook name, '$type' and '$process_type', refers to the type of the current
                 * post(post|wiki|project|directory) and the type of the current process (creation|editing)
                 *
                 * @param WP_Post
                 */
                do_action('woffice_after_' . $type . '_' . $process_type, $post_id, get_post($post_id) );

                if($is_shortcode == false && $frontend_state == 'publish') {
                    /**
                     * Alerts
                     */
                    $process_string = ($is_new) ? __('created', 'woffice') : __('updated', 'woffice');
                    $type_string = self::getBaseLabel($type);

                    $message = $type_string. ' '.$process_string.'!';
                    Woffice_Alert::create()->setType('success')->setContent($message)->queue();
                    /**
                     * We redirect to the post once created / updated
                     */
                    wp_redirect(get_permalink($post_id));
                    exit();
                } else{
                    return $post_id;
                }

            }

            return $errors;

        }

        /**
         * Gets the base label according to the type of the post type
         *
         * @param string $type
         * @return string
         */
        static function getBaseLabel($type) {
            if ( $type == "project" ) {
                $base_label = esc_html_x( 'Project', 'Label for frontend actions (Update Project, Create, Project, etc)', 'woffice' );
            } elseif ( $type == "directory" ) {
                $base_label = esc_html_x( 'Item', 'Label for frontend actions (Update Item, Create, Item, etc)', 'woffice' );
            } elseif ( $type == "wiki" ) {
                $base_label = esc_html_x( 'Wiki', 'Label for frontend actions (Update Wiki, Create, Wiki, etc)', 'woffice' );
            } else {
                $base_label = esc_html_x( 'Article', 'Label for frontend actions (Update Article, Create, Article, etc)', 'woffice' );
            }
            return $base_label;
        }

        /**
         * Render form HTML
         * -
         * This function displays HTML form for the frontend edit
         *
         * @param $type string kind of post (project, wiki or post)
         * @param $process_val int|array  ID of created post | array of error
         * @param $post_id int if it's an edit
         */
        static function frontend_render( $type, $process_val, $post_id = 0 ) {
            
            $html = '';

            /**
             * Base Label used in pretty much all labels:
             */
            $base_label = self::getBaseLabel($type);

            $form_type = (!empty($post_id)) ? 'edit' : 'create';

            /**
             * If a post has been created and we're in the shortcode:
             */
            if(is_int($process_val) && $process_val != 0){
                $html .= '<div class="infobox fa fa-check-circle" style="background-color: #3cb500;">';
                if(get_post_status( $process_val ) == 'publish') {
                    $html .= '<span class="infobox-head">' . __('Created!', 'woffice') . '</span>';
                    $html .= '<p>' . __('Check it out :', 'woffice');
                    $html .= ' <a href="' . get_the_permalink($process_val) . '">' . get_the_title($process_val) . '</a></p>';
                } else {
                    $html .= '<span class="infobox-head">' . __('Done!', 'woffice') . '</span>';
                    $html .= '<p>' . __('Action has been successfully proceeded and it\'s currently saved as:', 'woffice').' '. get_post_status( $process_val ) .'</p>';
                }
                $html .= '</div>';
            }

            $type = ( $type == 'post' ) ? 'blog' : $type;

            /**
             * We render the wrapper and its form
             */
            $is_revealed = (($type === 'project' || $type === 'wiki') && $form_type === 'edit') ? 'frontend-wrapper__content--revealed' : '';

            $html .= '<div id="'. $type .'-'. $form_type .'" class="intern-padding frontend-wrapper__content '. $is_revealed .'">';

            $form_extra = ($type === 'blog' || $type === 'directory') ? ' enctype="multipart/form-data"' : '';

            $html .= '<form action="'. self::get_form_url($type) .'" id="primary-post-form" class="mt-0" method="POST" '.$form_extra.'>';


            /**
             * Alerts
             */
            if(is_array($process_val) && !empty($process_val)) {

                $html .= '<div id="message" class="infobox" style="background-color: #DE1717;">';
                    $html .= '<ul>';
                    foreach ($process_val as $error) {
                        $html .= '<li>'. $error . '</li>';
                    }
                    $html .= '</ul>';
                $html .= '</div>';

            }

            /**
             * Project info box
             */
            if ( $type == 'project' && ! current_user_can( "edit_posts" ) ) {

                $html .= '<div class="infobox fa fa-cogs" style="background-color: #7B7B7B;">';
                $html .= '<span class="infobox-head">'. __( "Information", "woffice" ) .'</span>';
                $html .= '<p>'. __( "You'll only find here basic settings for the projects, for more settings please contact an user with author right.", "woffice" ) .'</p>';
                $html .= '</div>';

            }

            /**
             * Post Title
             */
            $html .= '<p>';
                $val_title = (!empty($post_id)) ? 'value="'.get_the_title($post_id).'"' : '';
                $html .= '<label for="post_title">'. $base_label. ' '. __( 'Title:', 'woffice' ) .'</label>';
                $html .= '<input type="text" name="post_title" id="post_title" class="required" '.$val_title.' required/>';
            $html .= '</p>';

            /**
             * Wiki Post fields
             */
            if ( $type == 'wiki' ) {

                /**
                 * Wiki category search
                 */
                $html .= self::taxonomy_field('wiki_category', 'wiki-category', __('Article', 'woffice'), $post_id);

            }

            /**
             * Project Post fields
             */
            if ( $type == 'project' ) {

                global $post;

                /**
                 * Filter if the status selectbox is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if(apply_filters('woffice_frontend_project_status_enabled', true)) {
                    
                    $project_status = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_current_status') : '';
                    
                    $archived_selected    = ($project_status == 'archived' || empty($project_status)) ? 'selected' : '';
                    $done_selected        = ($project_status == 'done' || empty($project_status)) ? 'selected' : '';
                    $in_progress_selected = ($project_status == 'in_progress' || empty($project_status)) ? 'selected' : '';
                    $in_review_selected   = ($project_status == 'in_review' || empty($project_status)) ? 'selected' : '';
                    $planned_selected      = ($project_status == 'planned' || empty($project_status)) ? 'selected' : '';
                   
                    $html .= '<p>';
                        $html .= '<label for="project_current_status">'. $base_label . ' '. __('status:', 'woffice' ). '</label>';
                        $html .= '<small>'. __( 'The project current status', 'woffice' ). '</small>';
                        $html .= '<select name="project_current_status" class="form-control custom-select">';
                            $html .= '<option value="archived" '.$archived_selected.'>' . __('Archived','woffice') . '</option>';
                            $html .= '<option value="done" '.$done_selected.'>' . __('Done','woffice') . '</option>';
                            $html .= '<option value="in_progress" '.$in_progress_selected.'>' . __('In Progress','woffice') . '</option>';
                            $html .= '<option value="in_review" '.$in_review_selected.'>' . __('In Review','woffice') . '</option>';
                            $html .= '<option value="planned" '.$planned_selected.'>' . __('planned','woffice') . '</option>';
                        $html .= '</select>';
                    $html .= '</p>';
                  }

                /**
                 * Filter if the dates input are enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if(apply_filters('woffice_frontend_project_dates_enabled', true)) {
                    $project_date_start = (!empty($post_id) && function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_start') : date('d-m-Y');
                    $project_date_end = (!empty($post_id) && function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_end') : date('d-m-Y');
                    $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">';
                    $html .= '<p>';
                    $html .= '<label for="project_start">' . $base_label . ' ' . __('starting date:', 'woffice') . '</label>';
                    $html .= '<input type="text" name="project_start" id="project_start" class="datepicker" value="' . $project_date_start . '" placeholder="' . $project_date_start . '"/>';
                    $html .= '</p>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-6">';
                    $html .= '<p>';
                    $html .= '<label for="project_end">' . $base_label . ' ' . __('ending date:', 'woffice') . '</label>';
                    $html .= '<input type="text" name="project_end" id="project_end" class="datepicker" value="' . $project_date_end . '" placeholder="' . $project_date_end . '"/>';
                    $html .= '</p>';
                    $html .= '</div>';
                    $html .= '</div>';
                }

                /**
                 * Filter if the tracking selectbox is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if(apply_filters('woffice_frontend_project_tracking_enabled', true)) {
                  $project_progress = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_progress') : '';
                  $time_selected = ($project_progress == "time" || empty($project_progress)) ? 'selected' : '';
                  $tasks_selected = (empty($time_selected) && $project_progress == "tasks") ? 'selected' : '';
                  $html .= '<p>';
                  $html .= '<label for="tracking_type">'. $base_label . ' '. __( 'tracking:', 'woffice' ). '</label>';
                  $html .= '<small>'. __( 'How is the progress tracked', 'woffice' ). '</small>';
                  $html .= '<select name="tracking_type" class="form-control custom-select">';
                  $html .= '<option value="time" '.$time_selected.'>' . __(' By Time','woffice') . '</option>';
                  $html .= '<option value="tasks" '.$tasks_selected.'>' . __(' By Tasks','woffice') . '</option>';
                  $html .= '</select>';
                  $html .= '</p>';
                }

                /**
                 * Filter if the taxonomy field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if(apply_filters('woffice_frontend_project_taxonomy_enabled', true)) {
                    $html .= self::taxonomy_field('project_category', 'project-category', __('Project', 'woffice'), $post_id);
                }

                /**
                 * Filter if the members field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if(apply_filters('woffice_frontend_project_members_enabled', true)) {
                    $project_members = ( !empty($post_id) ) ? woffice_get_project_members($post_id) : array();

	                $disable_members_suggestion = ( is_multisite() && wp_is_large_network( 'users' ) );
	                $disable_members_suggestion = apply_filters( 'woffice_disable_projects_members_suggestion', $disable_members_suggestion);
	                
	                if( $disable_members_suggestion) {

		                $html .= '<p>';
			                $html .= '<label for="project_members">' . $base_label . ' ' . __( 'members:', 'woffice' ) . '</label>';
			                $html .= '<small>' . __( 'If it\'s empty, all members\'ll be allowed to see it (leave empty for groups projects)', 'woffice' ) . '</small>';
			                $html .= '<select multiple="multiple" name="project_members[]" class="form-control">';
				                $tt_users_obj = get_users( array(
					                'fields' => array(
						                'ID',
						                'user_nicename',
						                'display_name'
					                )
				                ) );
				                $tt_users_obj = apply_filters( 'woffice_project_assignation_include', $tt_users_obj, get_the_ID(), $project_members );

				                foreach ( $tt_users_obj as $tt_user ) {
					                $selected = ( in_array( $tt_user->ID, $project_members ) ) ? "selected" : "";
					                $html .= '<option value="' . $tt_user->ID . '" ' . $selected . '>' . woffice_get_name_to_display( $tt_user->ID ) . '</option>';
				                }
			                $html .= '</select>';
		                $html .= '</p>';

	                } else {

		                $project_members_value = (!empty($project_members)) ? implode(',',$project_members) : '';

		                $html .= '<div class="form-group">';
			                $html .= '<label for="project_members">'. $base_label . ' '. __( 'members:', 'woffice' ). '</label>';
			                $html .= '<small>'. __( "If it's empty, all members'll be allowed to see it (leave empty for groups projects)", 'woffice' ). '</small>';
			                $html .= '<div class="woffice-users-suggest"
			                    data-post-id="'.get_the_ID().'"
			                    >';

				                $html .= '<input
				                  type="text"
				                  name="woffice-users-suggest_input"
				                  id="woffice-users-suggest_input"
				                  class="woffice-users-suggest_input"
				                  placeholder="' . esc_attr__( 'Search for members...', 'woffice' ) . '"/>';
				                    
				                $html .= '<input name="project_members" class="woffice-users-suggest_members-ids" type="hidden" value="'.$project_members_value.'" />';
				                $html .= '<ul class="woffice-users-suggest_members-list">';

				                foreach($project_members as $project_member) {
					                $html .= '<li data-id="' . $project_member . '"><a href="javascript:void(0)" class="woffice-users-suggest_remove-member"><i class="fa fa-times"></i></a> ' . woffice_get_name_to_display($project_member). '</li>';
				                }

				                $html .= '</ul>';
			                $html .= '</div>';
		                $html .= '</div>';

	                }

                }


                /**
                 * Who can edit field
                 */
                if( woffice_current_user_can_see_only_author_checkbox( $post_id  ) ) {
                    $only_author_can_edit_val = (!empty($post_id) && function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'only_author_can_edit') : true;
                    $only_author_can_edit_checked = ($only_author_can_edit_val) ? 'checked="checked"' : '';
                    $only_author_can_edit_class = ($only_author_can_edit_val) ? 'checked' : '';
                    $html .= '<p>';
                    $html .= '<span class="wpcf7-checkbox">';
                        $html .= '<span class="wpcf7-list-item">';
                            $html .= '<label class="frontend-checkbox '.$only_author_can_edit_class.'">';
                                $html .= '<input type="checkbox" name="only_author_can_edit" id="only_author_can_edit" value="yes" '.$only_author_can_edit_checked.'>';
                                $html .= '<span class="wpcf7-list-item-label">'. __( "Only author can edit?", "woffice" ) .'</span>';
                            $html .= '</label>';
                        $html .= '</span>';
                    $html .= '</span>';
                    $html .= '</p>';
                }

                /*
                 * EventON sync
                 */

                /**
                 * Filter if the calendar switch field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                $calendar_sync_enabled = apply_filters( 'woffice_allow_to_sync_calendar_on_project_creation', true );

	            if ($calendar_sync_enabled) {
                    $project_calendar_val = (!empty($post_id) && function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'project_calendar') : true;
                    $project_calendar_checked = ($project_calendar_val) ? 'checked="checked"' : '';
                    $project_calendar_class = ($project_calendar_val) ? 'checked' : '';
                    $html .= '<p>';
                    $html .= '<span class="wpcf7-checkbox">';
                        $html .= '<span class="wpcf7-list-item">';
                            $html .= '<label class="frontend-checkbox '.$project_calendar_class.'">';
                                $html .= '<input type="checkbox" name="calendar_sync" id="calendar_sync" value="yes" '.$project_calendar_checked.'>';
                                $html .= '<span class="wpcf7-list-item-label">'. __( "Calendar sync?", "woffice" ) .'</span>';
                        $html .= '</label>';
                        $html .= '</span>';
                    $html .= '</span>';
                    $html .= '</p>';
                }

                /**
                 * Filter if the to-do switch field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                if (apply_filters('woffice_frontend_project_todo_enabled', true)){
                    $project_todo_val = (!empty($post_id) && function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'project_todo') : true;
                    $project_todo_checked = ($project_todo_val) ? 'checked="checked"' : '';
                    $project_todo_class = ($project_todo_val) ? 'checked' : '';
                    $html .= '<p>';
                    $html .= '<span class="wpcf7-checkbox">';
                        $html .= '<span class="wpcf7-list-item">';
                            $html .= '<label class="frontend-checkbox '.$project_todo_class.'">';
                                $html .= '<input type="checkbox" name="enable_todo" id="enable_todo" value="yes" '.$project_todo_checked.'>';
                                $html .= '<span class="wpcf7-list-item-label">'. __( "Enable project Todo?", "woffice" ). '</span>';
                            $html .= '</label>';
                        $html .= '</span>';
                    $html .= '</span>';
                    $html .= '</p>';
                }

                /*
                 * File Away manager
                 */

                /**
                 * Filter if the files switch field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                $files_upload_enabled = apply_filters( 'woffice_allow_to_add_files_tab_on_project_creation', true );

                if (defined('fileaway') && $files_upload_enabled){
                    $project_files_val = (!empty($post_id) && function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'project_files') : true;
                    $project_files_checked = ($project_files_val) ? 'checked="checked"' : '';
                    $project_files_class = ($project_files_val) ? 'checked' : '';
                    $html .= '<p>';
                    $html .= '<span class="wpcf7-checkbox">';
                        $html .= '<span class="wpcf7-list-item">';
                            $html .= '<label class="frontend-checkbox '.$project_files_class.'">';
                                $html .= '<input type="checkbox" name="enable_files" id="enable_files" value="yes" '.$project_files_checked.'>';
                                $html .= '<span class="wpcf7-list-item-label">'. __( "Enable project Files Manager?", "woffice" ) .'</span>';
                            $html .= '</label>';
                        $html .= '</span>';
                    $html .= '</span>';
                    $html .= '</p>';
                }

	            /*
	             * Project Completed checkbox
	             */
	            if( woffice_current_user_can_complete_project($post_id) ) {
		            $project_archived_val = (!empty($post_id) ) ? woffice_get_post_option($post_id, 'project_completed', false) : false;
                    $project_archived_checked = ($project_archived_val) ? 'checked="checked"' : '';
                    $project_archived_class = ($project_archived_val) ? 'checked' : '';
		            $html .= '<p>';
		                $html .= '<span class="wpcf7-checkbox">';
		                    $html .= '<span class="wpcf7-list-item">';
		                        $html .= '<label class="frontend-checkbox '.$project_archived_class.'">';
		                            $html .= '<input type="checkbox" name="project_archived" id="project_archived" value="yes" '.$project_archived_checked.'>';
                                    $html .= '<span class="wpcf7-list-item-label">'. __( "The project is archived", "woffice" ) .'</span>';
		                        $html .= '</label>';
		                    $html .= '</span>';
		                $html .= '</span>';
		            $html .= '</p>';
                }

                /*
                 * Project Calendar Choices (DP Pro Event Calendar Only) 
                 */

                /**
                 * Filter if the calendar switch field is enabled on the projects frontend creation/editing
                 *
                 * @param bool
                 */
                $calendar_choice_enabled = apply_filters( 'woffice_allow_calendar_choice_on_project_creation', true );

                if ($calendar_choice_enabled && defined('DP_PRO_EVENT_CALENDAR_VER') ) {

                    global $wpdb;
		            $dp_event_calendars = array();
                    $dp_calendar_table = $wpdb->prefix . 'dpProEventCalendar_calendars'; 
                    $dp_calendar_selected = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_calendar_choice') : '';

                    $query = "
                        SELECT *
                        FROM " . $dp_calendar_table . "
                        ORDER BY title ASC";
		
		            $calendars_obj = $wpdb->get_results($query, OBJECT);
                    $html .= '<p>';
                        $html .= '<label for="project_calendar_choice">' .__( 'Event Calendar:', 'woffice' ). '</label>';
                        $html .= '<select name="project_calendar_choice" class="postform form-control">';
                            if(is_array($calendars_obj)) {
                                foreach($calendars_obj as $calendar) {
                                    $selected = '';
                                    if ($calendar->id == $dp_calendar_selected)
                                        $selected = 'selected';
                                    $html .= '<option value="'.esc_attr( $calendar->id ).'" '.$selected.'>'.esc_html( $calendar->title ).'</option>';
                                }
                            }
                        $html .= '</select>';
                    $html .= '</p>';
                }
            }

            /**
             * Directory post fields
             */
            if ( $type == 'directory' ) {

                $item_fields      = fw_get_db_post_option(get_the_ID(), 'item_fields', array());
                $item_location    = fw_get_db_post_option(get_the_ID(), 'item_location');
	            $item_button_text = fw_get_db_post_option(get_the_ID(), 'item_button_text');
	            $item_button_icon = fw_get_db_post_option(get_the_ID(), 'item_button_icon');
	            $item_button_link = fw_get_db_post_option(get_the_ID(), 'item_button_link');
	            $lat = (!empty($item_location) && is_array($item_location)) ? $item_location['coordinates']['lat'] : '';
	            $lng = (!empty($item_location) && is_array($item_location)) ? $item_location['coordinates']['lng'] : '';

                $html .= '<woffice-addable-items 
                    label="'. __('Item fields', 'woffice') .'"
                    name="item_fields" 
                    :data=\''. json_encode($item_fields) .'\'></woffice-addable-items>';

                $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">';
                        $html .= '<p>';
                            $html .= '<label for="item_location_lng">'. __( 'Location\'s Longitude :', 'woffice' ). '</label>';
                            $html .= '<small>'. __( 'You can use : ', 'woffice' ).'<a href="http://www.latlong.net/" target="_blank">LatLong.net</a></small>';
                            $html .= '<input type="text" value="'. $lng .'" name="item_location_lng" id="item_location_lng" placeholder="-88.242188"/>';
                        $html .= '</p>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-6">';
                        $html .= '<p>';
                            $html .= '<label for="item_location_lat">'. __('Location\'s Latitude :', 'woffice' ). '</label>';
                            $html .= '<small>'. __( 'You can use : ', 'woffice' ) .'<a href="http://www.latlong.net/" target="_blank">LatLong.net</a></small>';
                            $html .= '<input type="text" value="'. $lat .'" name="item_location_lat" id="item_location_lat" placeholder="37.544577"/>';
                        $html .= '</p>';
                    $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="row">';
                    $html .= '<div class="col-md-6">';
                        $html .= '<p>';
                            $html .= '<label for="item_button_text">'. __( 'Button text:', 'woffice' ) .'</label>';
                            $html .= '<small>'. __( 'Button will be displayed on the single page.', 'woffice' ) .'</small>';
                            $html .= '<input type="text" value="'. $item_button_text .'" name="item_button_text" id="item_button_text"/>';
                        $html .= '</p>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-6">';
                        $html .= '<p>';
                            $html .= '<label for="item_button_icon">'. __( 'Button\'s icon (Font Awesome):', 'woffice' ) .'</label>';
                            $html .= '<small>'. __( 'Please see : ', 'woffice' ) .'<a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">FontAwesome Icons</a></small>';
                            $html .= '<input type="text" value="'. $item_button_icon .'" name="item_button_icon" id="item_button_icon" placeholder="fa-star"/>';
                        $html .= '</p>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12">';
                        $html .= '<p>';
                        $html .= '<label for="item_button_link">'. __( 'Button Link :', 'woffice' ) .'</label>';
                        $html .= '<input type="text" value="'. $item_button_link .'" name="item_button_link" id="item_button_link"/>';
                        $html .= '</p>';
                    $html .= '</div>';
                $html .= '</div>';

                /**
                 * Directory category search
                 */
                $html .= self::taxonomy_field('directory_category', 'directory-category', __('Directory','woffice'), $post_id);

            }

            /**
             * Blog post fields
             */
            if ($type == 'blog') {

                /**
                 * Blog category search
                 */
                $html .= self::taxonomy_field('blog_category', 'category', __('Article','woffice'), $post_id);

                if ( woffice_current_is_admin() ) {
                    $everyone_edit_val = (!empty($post_id) && function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, 'everyone_edit') : true;
                    $everyone_edit_val_checked = ($everyone_edit_val) ? 'checked="checked"' : '';
                    $everyone_edit_val_class = ($everyone_edit_val) ? 'checked' : '';
                    $html .= '<p>';
                        $html .= '<span class="wpcf7-checkbox">';
                        $html .= '<span class="wpcf7-list-item">';
                            $html .= '<label class="frontend-checkbox '.$everyone_edit_val_class.'">';
                                $html .= '<input type="checkbox" name="everyone_edit" id="everyone_edit" value="yes" '.$everyone_edit_val_checked.'>';
                                $html .= '<span class="wpcf7-list-item-label">'. __( "Everyone can edit?", "woffice" ) .'</span>';
                            $html .= '</label>';
                        $html .= '</span>';
                        $html .= '</span>';
                    $html .= '</p>';
                }

            }

            /**
             * Featured image input
             */
            if ($type !== 'wiki' && $type !== 'project') {

                $html .= '<p>';
                    $upload_label = (empty($post_id)) ?  __( 'Add', 'woffice' ) :  __( 'Change:', 'woffice' );
                    $html .= '<label for="post_thumbnail">'. $upload_label . ' '. __( 'Article Thumbnail :', 'woffice' ) .'</label>';
                    $html .= '<div class="custom-file"><input type="file" id="post_thumbnail" name="post_thumbnail" class="custom-file-input"><label class="custom-file-label" for="post_thumbnail">'. __('Choose file', 'woffice') .'</label></div>';
                $html .= '</p>';

             }

             if (get_post_type($post_id) || get_post_type('offline-courses')) {

                $html.='<p><label >Invited users:</label></p>';
                //INVITED USERS
                $invited_users = get_post_meta($post_id, '_invited_users', true);

// Create the WP_User_Query object
                $wp_user_query = new WP_User_Query([
                    'orderby' => 'display_name'
                ]);
// Get the results
                $authors       = $wp_user_query->get_results();


// Check for results
                if (!empty($authors)) {
                    // Name is your custom field key
                    $html .= "<select name='invited_users[]' multiple='yes' size='10' class='post-form form-control'>";
                    // loop trough each author
                    foreach ($authors as $author) {
                        if (in_array($author->ID, $invited_users)) {
                            $html .= "<option value='{$author->ID}' selected='selected'>{$author->first_name} {$author->last_name}</option>";
                        } else {
                            $html .= "<option value='{$author->ID}'>{$author->first_name} {$author->last_name}</option>";
                        }
                    }
                    $html .= "</select>";
                } else {
                    $html .= 'No authors found';
                }
            }

            /**
             * Editor
             */
            $html .= '<p>';
                $html .= '<label for="post_content">'. $base_label . ' '. __( 'content:', 'woffice' ).'</label>';
                $settings = array(
                    'textarea_name' => 'post_content',
                    'textarea_rows' => 20,
                    'editor_height' => 400,
                    'dfw'           => true
                );

                $content_val = (!empty($post_id)) ? get_the_content($post_id) : '';
                ob_start();
                wp_editor($content_val, 'post_content', $settings);
                $html .= ob_get_clean();
            $html .= '</p>';

            /**
             * Frontend form content before the submit button
             *
             * @param string $extra_content Option extra content
             * @param string $html - the form's HTML
             * @param int|array $process_val - ID of created post | array of error
             * @param string $type - the post type
             * @param int $post_id - the post ID
             */
            $html .= apply_filters( 'woffice_frontend_render_before_submit', '', $html, $type, $process_val, $post_id);

            
            /**
             * Submit button
             */
            $html .= '<p class="text-center">';
                $html .= wp_nonce_field( 'post_nonce', 'post_nonce_field', true, false );
                $html .= '<input type="hidden" name="submitted" id="submitted" value="true"/>';
                $button_type = (!empty($post_id)) ? __( 'Update', 'woffice' ) : __( 'Create', 'woffice' );
                $html .= '<button type="submit" id="woffice-frontend-submit" class="btn btn-default">';
                    $html .= '<i class="fa fa-pencil-alt"></i>'.$button_type .' '. $base_label;
                $html .= '</button>';
            $html .= '</p>';

            $html .= '</form>';

            /**
             * Go Back button
             * Only for creation
             */
            if (empty($post_id) || $type == 'blog' || $type == 'directory') {
                $html .= '<div class="center"><a href="#" class="btn btn-default frontend-wrapper__toggle my-0" data-action="hide" id="hide-'. $type .'-'. $form_type .'">';
                $html .= '<i class="fa fa-arrow-left"></i> ' . __("Go Back", "woffice");
                $html .= '</a></div>';
            }

            $html .= '</div>';

            

            echo $html;

        }

        /**
         * Return a taxonomy picker field
         *
         * @param $field_name
         * @param $taxonomy
         * @param $label
         * @param $post_id
         * @return string
         */
        static function taxonomy_field($field_name, $taxonomy, $label, $post_id) {

            $html = '';

            $terms = get_terms( $taxonomy, array( 'hide_empty' => false, 'parent' => 0) );

            if ( $terms ) {

                $post_terms = (!empty($post_id)) ? wp_get_post_terms( $post_id, $taxonomy, array("fields" => "slugs") ) : array();

                $html .= '<p>';

                $html .= '<label for="'.$field_name.'">'. $label . ' ' .__( 'Category:', 'woffice' ). '</label>';

                $html .= '<select multiple="multiple" name="'.$field_name.'[]" class="postform form-control">';
                $html .= '<option value="no-category">' . __( "No category", "woffice" ) . '</option>';

                foreach ( $terms as $term ) {
                    $selected = (in_array($term->slug, $post_terms)) ? 'selected' : '';
                    $html .= '<option value="'.esc_attr( $term->slug ).'" '.$selected.'>'.esc_html( $term->name ).'</option>';

	                $html .= static::get_taxonomy_options( $taxonomy, $post_terms, $term->term_id, '> ');
                }
                $html .= '</select>';

                $html .= '</p>';

            }

            return $html;

        }

	    /**
	     * Return recursively the options of a specific parent
	     *
	     * @param $taxonomy
	     * @param $post_terms
	     * @param $parent_id
	     * @param $tab
         * @param $displayed_terms
	     *
	     * @return string
	     */
	    static function get_taxonomy_options($taxonomy, $post_terms, $parent_id, $tab, $displayed_terms = array()) {

		    $terms = get_terms( $taxonomy, array( 'hide_empty' => false, 'parent' => $parent_id ) );

		    $html = '';
		    foreach ( $terms as $term ) {
			    array_push( $displayed_terms, $term->id );
			    $selected  = ( in_array( $term->slug, $post_terms ) ) ? 'selected' : '';
			    $html     .= '<option value="' . esc_attr( $term->slug ) . '" ' . $selected . '>' . $tab . esc_html( $term->name ) . '</option>';
			    $html     .= static::get_taxonomy_options( $taxonomy, $post_terms, $term->term_id, '> '.$tab, $displayed_terms);
		    }

		    return $html;

	    }

        /**
         * Process the upload of the featured image and attach it to a post
         *
         * @param $field_name
         * @param $post_id
         */
        static function featured_upload($field_name, $post_id) {

            if ( $_FILES[$field_name]["error"] == 0 ) {
                $attach_id = 0;
                require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
                require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
                if ( $_FILES ) {
                    foreach ( $_FILES as $file => $array ) {
                        if ( $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) {
                            fw_print( "upload error : " . $_FILES[ $file ]['error'] );
                        }
                        $attach_id = media_handle_upload( $file, $post_id );
                    }
                }
                if ( $attach_id > 0 ) {
                    //and if you want to set that image as Post  then use:
                    update_post_meta( $post_id, '_thumbnail_id', $attach_id );
                }
            }

        }

        /**
         * Does the user can create in frontend ?
         * This function check if the current member can create a post according to the options set in the Theme Settings
         *
         * @param array $users_from_options is an array of users from the theme settings
         * @param null|string $post_type the type of the post the the function have to check the permissions
         *
         * @return bool
         */
        static function role_allowed( $users_from_options, $post_type = null ) {
            /* If the users is not logged we reeturn false */
            if ( ! is_user_logged_in() || !$users_from_options) {
                return false;
            }

            //Check if woffice permissions settings are overrited by meta caps
            $use_meta_caps = woffice_check_meta_caps( $post_type );

            if ( $use_meta_caps ) {
                $slug   = woffice_get_slug_for_meta_caps( $post_type );
                $prefix = ( $post_type != 'post' ) ? 'woffice_' : '';

                return current_user_can( $prefix . 'edit_' . $slug );
            } else {
                /* We force the arg to be an array */
                if ( is_array( $users_from_options ) == false ) {
                    $users_from_options = array( $users_from_options );
                }

                /* We get the current user data */
                $user          = wp_get_current_user();
                $the_user_role = (array) $user->roles;

                $role_intersect = array_intersect( $the_user_role, $users_from_options );

                /* We check if it's in the array, OR if it's the administrator  */
				$is_allowed = ( ! empty( $role_intersect ) || woffice_current_is_admin() );

	            /**
	             * Filter if the result of the function Woffice_Frontend::role_allowed()
               *
               * @see woffice/inc/classes/Woffice_frontend.php*
               *
	             * @param bool $is_allowed
               * @param array $users_from_options
               * @param string $post_type
               *
	             */
                return apply_filters( 'woffice_frontend_role_allowed', $is_allowed, $users_from_options, $post_type);
            }

        }

        /**
         * Helper, set the terms to a post type with the frontend values
         *
         * @param $post_values : Values selected in the frontend ($_POST) it's an array
         * @param $type : the post type
         * @param $post_id : The ID of the new post
         * @return bool
         */
        static function frontend_set_terms( $post_values, $type, $post_id ) {

            if ( ! isset( $post_values ) ) {
                return false;
            }

            /* Categories name */
            if ( $type == "project" ) {
                $term_name = "project-category";
            } elseif ( $type == "wiki" ) {
                $term_name = "wiki-category";
            } elseif ( $type == "directory" ) {
                $term_name = "directory-category";
            } else {
                $term_name = "category";
            }

            $term_array = array();

            foreach ( $post_values as $category ) {

                if ( $category != "no-category" ) {

                    if ( $type == "post" ) {
                        $type_catgeory_object = get_category_by_slug( $category );
                    } else {
                        $type_catgeory_object = get_term_by( 'slug', $category, $term_name );
                    }

                    //fw_print($project_catgeory_object);
                    $term_array[] = $type_catgeory_object->term_id;

                }

            }
            $value_set = wp_set_post_terms( $post_id, $term_array, $term_name );

            if ( $type == "project" ) {
                $post_object = get_post( $post_id );
                woffice_groups_sync_members( $post_id, $post_object, false );
                //do_action("save_post", $post_id, $post_object, false);
            }

            return true;

        }

        /**
         * Get the form URL in the frontend side
         *
         * @param $type string the post type, blog = post (!)
         * @return string
         */
        static function get_form_url($type) {
            
	        $form_url = '';
            if ( $type == "blog" && !is_singular('post') ) {
                $the_option = get_option( 'show_on_front' );
                if ( $the_option == 'page' ) {
                    $blog_page = get_option( 'page_for_posts' );
                    if ( empty( $blog_page ) ) {
                        $pages     = get_pages( array(
                            'meta_key'   => '_wp_page_template',
                            'meta_value' => 'page-templates/blog.php'
                        ) );

	                    if( isset($pages[0]) )
	                        $blog_page = $pages[0]->ID;

                    }

                    if( !empty($blog_page) )
                        $form_url = get_permalink( $blog_page );
                }
            } elseif(is_tax()) {
                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                $form_url = get_term_link($term);
            } else {
                $form_url = get_the_permalink();
            }

            if( empty($form_url) )
	            $form_url = get_site_url() . '/';

            return esc_url($form_url);

        }

        /**
         * Check if edit is allowed
         *
         * @param $post_type string post type that needs to be edited
         * @param $editing_type string
         * @return bool
         */
        static function edit_allowed( $post_type = null, $editing_type = 'edit' ) {

            /* If the users is not logged we reeturn false */
            if ( ! is_user_logged_in() || is_null( $post_type ) ) {
                return false;
            }

            /* We get the current user data */
            $user = wp_get_current_user();

            //Check if woffice permissions settings are overrited by meta caps
            $use_meta_caps = woffice_check_meta_caps( $post_type );
            global $post;

            if ( $use_meta_caps ) {

                $slug   = woffice_get_slug_for_meta_caps( $post_type );
                $prefix = ( $post_type != 'post' ) ? 'woffice_' : '';

                //If the post is aleady published then user needs an additional capability
                $published_cap_bool = ( ( $post->post_status == 'publish' && current_user_can( $prefix . $editing_type . '_published_' . $slug ) ) || $post->post_status == 'draft' );

                if ( $post->post_author == $user->ID && current_user_can( $prefix . $editing_type . '_' . $slug ) && $published_cap_bool ) {
                    return true;
                }

                if ( $post->post_author != $user->ID && current_user_can( $prefix . $editing_type . '_others_' . $slug ) && $published_cap_bool ) {
                    return true;
                }

            } else {
                /*We get TRUE or FALSE from the settings */
                $everyone_edit = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( get_the_ID(), 'everyone_edit' ) : '';

                //Check if the current user is the author of the post or is an admin
                //Both are always allowed to edit by frontend
                if ( $post->post_author == $user->ID || woffice_current_is_admin() ) {
                    return true;
                }

	            //we check if it's allowed to others
	            if ($everyone_edit == true) {

		            //Get te roles allowed to edit the current post type
		            $roles_selected = array();
		            if ($post_type == 'wiki' || $post_type == 'post') {
			            $roles_selected = woffice_get_settings_option($post_type . '_edit');
		            }

		            //Check if the current user has one of that roles assigned
		            $the_user_role = (array)$user->roles;

		            $role_intersect = array_intersect($the_user_role, $roles_selected);

                    /**
                     * Filter if the result of the function Woffice_Frontend::edit_allowed()
                     *
                     * @see woffice/inc/classes/Woffice_frontend.php*
                     *
                     * @param bool $is_allowed
                     * @param array $post_type
                     * @param string $editing_type
                     *
                     */
		            return apply_filters( 'woffice_edit_allowed', (!empty($role_intersect)), $post_type, $editing_type);

	            }

	            /**
	             * This filter is documented above
	             */
	            return apply_filters( 'woffice_edit_allowed', false, $post_type, $editing_type);
            }

        }

        /**
         * We re-recreate an addable option
         * http://manual.unyson.io/en/latest/options/built-in-option-types.html#addable-option
         * On the FRONTEND, this is relative to a post
         *
         * @param $post_id INTEGER, it's the post's ID
         * @param $field_name STRING, it's the field's name
         * @param $field_options ARRAY, an array of Unyson options
         * @param $field_label STRING, it's the field's label
         * -
         * Returns HTTML
         *
         * Not Ready for now ....
         */
        static function addable_option_form( $post_id, $field_name, $field_options, $field_label ) {

            if ( empty( $post_id ) || empty( $field_name ) || empty( $field_options ) || empty( $field_label ) ) {
                return;
            }

            //
            // We display the current values
            //
            /* Get all the existing todos : */
            $box_content = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option( $post_id, $field_name ) : '';
            /*Values */
            echo '<form id="woffice-addable-box-list" class="woffice-project-todo-group" action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';
            /* First we display all the values */
            if ( ! empty( $box_content ) ) {
                echo '<input type="hidden" name="post_ID" value="' . $post_id . '" />';
                $counter = 0;
                foreach ( $box_content as $fields ) {
                    $note_class = "";
                    echo '<div class="woffice-box ' . $note_class . '">';
                    echo '<ul>';
                    /* We display each field within the box */
                    foreach ( $fields as $field_name => $field_value ) {
                        echo '<li><i class="fa fa-arrow-right"></i> ' . $field_name . '</li>';
                    }
                    echo '</ul>';

                    /* Delete Icon */
                    echo '<a href="#" onclick="return false" class="woffice-box-delete"><i class="fa fa-trash"></i></a>';

                    /* We create some input fields to pass the data through ajax form */
                    foreach ( $fields as $field_name => $field_value ) {
                        echo '<li><i class="fa fa-arrow-right"></i> ' . $field_name . '</li>';
                        echo '<input type="hidden" name="addable_list[' . $counter . '][' . $field_name . ']" value="' . $field_value . '" />';
                    }
                    /* Other Data */
                    echo '<input type="hidden" name="post_ID" value="' . $post_id . '" />';
                    echo '<input type="hidden" name="action" value="wofficeAddableDelete" />';
                    echo '</div>';
                    $counter ++;
                }
            }
            echo '</form>';

            //
            // THE FORM TO ADD A NEW BOX
            //
            echo '<div id="woffice-addable-box-alert"></div>';
            echo '<form id="woffice-addable-box" action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';
            /* The heading */
            echo '<div class="heading"><h3>' . $field_label . '</h3></div>';

            /* The Fields */
            foreach ( $field_options as $option_name => $option ) {
                echo '<div class="row">';
                echo '<div class="col-md-6">';
                echo '<label for="' . $option_name . '">' . $option->label . '</label>';
                if ( $option->type == "text" ) {
                    echo '<input type="text" name="' . $option_name . '" required="required">';
                } elseif ( $option->type == "textarea" ) {
                    echo '<textarea rows="2" name="' . $option_name . '"></textarea>';
                } elseif ( $option->type == "icon" ) {
                    echo '<select name="' . $option_name . '" class="form-control">';
                    // We grab all the icons :
                    $response_icon = wp_remote_get( 'https://raw.githubusercontent.com/Smartik89/SMK-Font-Awesome-PHP-JSON/master/font-awesome/json/font-awesome-data-readable.json' );
                    if ( is_array( $response_icon ) ) {
                        $body  = $response_icon['body']; // use the content
                        $icons = json_decode( $body );
                        foreach ( $icons as $class => $name ) {
                            echo '<option value="' . $class . '"><i class="fa ' . $class . '></i> "' . $name . '</option>';
                        }
                    } else {
                        echo '<option value="no-icon">' . __( 'No icon available.', 'woffice' ) . '</option>';
                    }
                    echo '</select>';
                }
                echo '</div>';
                echo '</div>';
            }

            /* Submit button */
            echo '<div class="text-right">';
            echo '<button type="submit" class="btn btn-default"><i class="fa fa-plus-square"></i> ' . __( 'Add a box', 'woffice' ) . '</button>';
            echo '</div>';

            /* Passing extra args */
            echo '<input type="hidden" name="post_ID" value="' . $field_name . '" />';
            echo '<input type="hidden" name="option_name" value="' . $post_id . '" />';
            echo '<input type="hidden" name="action" value="wofficeAddableFrontend" />';
            echo '</form>';

            /* SCRIPT called */
            echo '<script type="text/javascript">
	jQuery(document).ready( function() {
		// Delete Box
		jQuery(".woffice-box").on("click", ".woffice-box-delete", function(){
			var Item = jQuery(this).closest(".woffice-box");
			Item.remove();
			var woffice_BoxDelete_data = jQuery("#woffice-addable-box-list").serialize();
			jQuery.ajax({
				type:"POST",
				url: "' . get_site_url() . '/wp-admin/admin-ajax.php",
				data: woffice_BoxDelete_data,
				success:function(returnval){
					console.log("task removed");
					jQuery("#woffice-addable-box-alert").html(returnval);
					jQuery("#woffice-addable-box-alert div.infobox").hide(4000, function(){ jQuery("#woffice-addable-box-alert div.infobox").remove(); });
				},
			}); return false;
		});

	});
	</script>';

        }

        /**
         * Render a single featured image according to the device's width
         *
         * @param $id
         * @param string $featured_height
         * @param bool $masonry
         */
        static function render_featured_image_single_post( $id, $featured_height = "", $masonry = false ) {
            // Getting the post thumbnail url
            $auto_height = woffice_get_settings_option( 'auto_height_featured_image', 'nope');

            // Full
            $image_full_url = wp_get_attachment_url( get_post_thumbnail_id( $id ) );


            $image_large_url = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), array(800,600) );

            if ($auto_height == 'auto'){
                $featured_height = '';
                $auto_height_class = ' auto-height';
            } else {
                $auto_height_class = '';
                $featured_height = (empty($featured_height)) ? $featured_height : 'height: ' . esc_attr( $featured_height ) . 'px;';
            }

	        /**
	         * Filter `woffice_featured_images_height`
             *
             * Add the ability to override the featured image's height
             *
             * @param string $featured_height - string
             * @param int    $id - the post ID
             *
             * @return string - CSS applying the height
	         */
	        $featured_height = apply_filters('woffice_featured_images_height', $featured_height, $id);

            ?>

            <div class="intern-thumbnail <?php echo $auto_height_class ?>" style="<?php echo $featured_height ?>">
                <?php if (!is_single()): ?>
                    <a href="<?php the_permalink(); ?>">
                <?php endif; ?>

                    <?php

                    if ($masonry) {
                        echo '<picture>';
                        echo '<source srcset="' .esc_url($image_full_url). '" media="(min-width: 1920px)" />';
                        echo '<img src="'.$image_large_url[0].'" />';
                        echo '</picture>';
                    } else {
                        echo '<picture>';
                        echo '<source srcset="'. esc_url($image_full_url) .'" media="(min-width: 801px)" />';
                        echo '<img src="'.$image_large_url[0].'" />';
                        echo '</picture>';
                    }

                    ?>

                <?php if ( ! is_single() ): ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php

        }

        /**
         * Returns a list of taxonomy
         */
        public function taxonomy_fetching(){

            // We get the Post Name selected
            $post_name = $_POST['ajax_post_name'];
            if (isset($post_name)) {

                $taxonomy_objects = get_object_taxonomies( $post_name, 'names' );
                echo'<label for="taxonomy"><i class="fa fa-tag"></i> '. __('Choose a taxonomy','woffice') .'</label>';
                echo'<select class="form-control" name="taxonomy">';

                if (!empty($taxonomy_objects)) {
                    foreach ($taxonomy_objects as $key=>$taxonomy) {
                        echo '<option value="'.$taxonomy.'">'.$taxonomy.'</option>';
                    }
                }
                else {
                    echo '<option value="no_tax">'. __('No Taxonomy..', 'woffice') .'</option>';
                }

                echo '</select>';

            }
            wp_die();

        }

        /**
         * Add taxonomy through an Ajax request
         */
        public function taxonomy_add(){

            // The check the values
            if (empty($_POST['ajax_taxonomy'])) {
                $message = __('You need to choose a taxonomy to add your new category.','woffice');
            }
            elseif (empty($_POST['ajax_new_tax'])) {
                $message = __('You need to enter a new category.','woffice');
            }
            else {
                // We set the new term :
                $tax_ready = sanitize_text_field($_POST['ajax_new_tax']);
                $taxonomy = sanitize_text_field($_POST['ajax_taxonomy']);

                if ($taxonomy != 'no_tax') {

                    // We insert the tax
                    $insert_term = wp_insert_term( $tax_ready, $taxonomy);

                    $message = __('Successfully Added.','woffice');

                }
            }

            echo '<div class="infobox notification-color">'.$message.'</div>';

            wp_die();

        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Frontend();