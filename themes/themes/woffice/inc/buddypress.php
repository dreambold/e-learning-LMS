<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }


if(!function_exists('woffice_get_xprofile_table')) {
    /**
     * XPROFILE DATA TABLE'S NAME
     *
     * It's used in the extensions as well and if it doesn't work it can create
     * a loop over the fields, so you'll have thousands of fields
     * You can make the change here, if you're on multisite and it's not working
     *
     * @param $type
     * @return string : the table name in the database
     */
	function woffice_get_xprofile_table($type = null)
	{

		if ($type == "fields") {
			$table = 'bp_xprofile_fields';
		}
		else {
			$table = 'bp_xprofile_groups';
		}

		global $wpdb;
		/*
		 * We do additional checks on Multi sites
		 * The query is cached so it doesn't affect the performances
		 */
		if (is_multisite()) {
            $base_table_name = $wpdb->base_prefix . $table;
            // If it doesn't exist
            if($wpdb->get_var("SHOW TABLES LIKE '$base_table_name'") != $base_table_name) {
                $table_name = $wpdb->prefix . $table;
            } else {
                $table_name = $base_table_name;
            }
		}
		/*
		 * Normal case, we just return the table
		 */
		else {
			$table_name = $wpdb->prefix . $table;
		}

		return $table_name;
	}
}

if(!function_exists('woffice_members_filter')) {
    /**
     * Display members filter in the member directory
     *
     * @return string
     */
    function woffice_members_filter()
    {
        $buddy_filter = woffice_get_settings_option('buddy_filter');

        if ($buddy_filter !== 'show') {
	        return '';
        }

	    global $wp_roles;

	    $buddy_excluded_directory = woffice_get_settings_option('buddy_excluded_directory');

	    /**
	     * Filters the members roles excluded from the dropdown filter, in the members page
	     *
	     * @param array[string]
	     */
	    $buddy_excluded_directory = (array)apply_filters('woffice_exclude_members_from_dropdown_filter', $buddy_excluded_directory);
	    $count_users = count_users();
        ?>

        <div id="woffice-roles-filter" class="dropdown text-center">

            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                <?php _e('Role filter', 'woffice'); ?>
            </button>

            <div class="dropdown-menu" role="menu">

                <a href="javascript:void(0)" data-role="0" class="dropdown-item font-weight-bold text-body"><?php esc_html_e('All members', 'woffice'); ?></a>

                <div class="dropdown-divider"></div>

                <?php foreach ($wp_roles->roles as $key => $value): ?>
                    <?php if (
                            substr($key, 0, 4) != 'bbp_' &&
                            !in_array($key, $buddy_excluded_directory) &&
                            array_key_exists($key, $count_users['avail_roles']) &&
                            ($count_users['avail_roles'][$key] > 0)
                    ) : ?>
                        <a href="javascript:void(0)" data-role="<?php esc_attr_e($key); ?>" class="dropdown-item text-body font-weight-bold">
                            <?php esc_html_e($value['name']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        </div>


        <?php
    }
}

if (!function_exists('woffice_custom_members_navigation')) {
	/**
     * Apply custom item to the BuddyPress nav in the members directory
     *
     * @since 2.8.0
     *
	 * @param $nav
	 *
	 * @return mixed
	 */
    function woffice_custom_members_navigation($nav) {
        if (function_exists('woffice_get_members_map')) {
	        $nav['map'] =  array (
		        'component' => 'members',
		        'slug'      => 'map-trigger',
                'li_class'  => array('no-count'),
                'link'      => '#',
		        'count'     => 0,
                'text'      =>  __('Members map', 'woffice'),
                'position'  => 1
            );
        }

	    $buddy_advanced_search = woffice_get_settings_option('buddy_advanced_search');
	    if ($buddy_advanced_search) {
		    $nav['advanced'] =  array (
			    'component' => 'members',
			    'slug'      => 'advanced-search',
			    'li_class'  => array('no-count'),
			    'link'      => '#',
			    'count'     => 0,
			    'text'      =>  __('Advanced Search', 'woffice'),
			    'position'  => 100
		    );
        }

        return $nav;
    }
}

add_filter('bp_nouveau_get_members_directory_nav_items', 'woffice_custom_members_navigation');

if(!function_exists('woffice_members_advanced_search')) {
	/**
	 * Renders the advanced search in the directory
	 */
	function woffice_members_advanced_search() {
		$buddy_advanced_search = woffice_get_settings_option( 'buddy_advanced_search' );

		if ( ! bp_is_active( 'xprofile' ) || ! $buddy_advanced_search ) {
			return;
		}

		woffice_render_advanced_search_fields();
	}
}
add_action('bp_before_directory_members_page', 'woffice_members_advanced_search');


if (!function_exists('woffice_deactivate_profile_home')) {
	/**
     * Deactivate the front template HOME
     *
     * @since 2.8.0
     *
	 * @param $displayed_user
	 *
	 * @return mixed
	 */
    function woffice_deactivate_profile_home($displayed_user) {
        if (empty($displayed_user)) {
            return $displayed_user;
        }

	    $displayed_user->front_template = null;

	    return $displayed_user;
    }
}
add_filter( 'bp_get_displayed_user', 'woffice_deactivate_profile_home' );

if (!function_exists('woffice_deactivate_profile_home')) {
	/**
	 * Deactivate the Group front template HOME
	 *
	 * @since 2.8.0.3
	 *
	 * @param $displayed_group
	 *
	 * @return mixed
	 */
	function woffice_deactivate_group_home($displayed_group) {
		if (empty($displayed_group)) {
			return $displayed_group;
		}

		$displayed_group->front_template = null;

		return $displayed_group;
	}
}
add_filter( 'bp_get_displayed_group', 'woffice_deactivate_group_home' );

if(!function_exists('woffice_render_advanced_search_fields')) {
	/**
	 * Render the fields for the advanced search
	 *
	 * @param bool $render_form Default FALSE. Set to TRUE, if you render the fields in a different position than the standard one
	 */
	function woffice_render_advanced_search_fields($render_form = false){

		if(!woffice_bp_is_active('xprofile'))
			return;

		if (!bp_has_profile(array('user_id' => 0, 'fetch_field_data' => false)))
			return;

		$style = (isset($_POST['advanced-search-submit']) || $render_form) ? 'style="display: block;"' : '';
		echo '<div id="woffice-members-advanced-search"  class="bg-light p-4 mb-4 rounded w-75 mx-auto" ' . $style .'>';

		/**
	      * Filters the title of the advanced search for members
          *
          * @param string $title
	     */
		$form_title = apply_filters('woffice_advanced_search_members_title', esc_html__('Filter Members', 'woffice'));

		if (!empty($form_title))
			echo '<h3 class="mt-0">' . $form_title . '</h3>';

		if ($render_form)
			echo '<form id="woffice-members-advanced-search-form" action="'.bp_get_members_directory_permalink().'" method="POST">';

        /**
         * Before the fields of the advanced search form. you might add some additional field
         */
		do_action('woffice_advanced_search_members_before_fields');

		$c = 0;

		// Check wordpress email
		$add_wordpress_email = woffice_get_settings_option('buddypress_wordpress_email_add_to_search');
		if( $add_wordpress_email ) {
			echo '<div class="form-group">';
			echo '<label for="wordpress_email">'.esc_html_x('Email', 'Label of the WordPress email field in the advanced search for members', 'woffice').'</label>';
			$value = (isset($_POST['wordpress_email'])) ? $_POST['wordpress_email'] : '';
			echo '<input type="text" id="wordpress_email" name="wordpress_email" value="'.esc_attr($value).'" />';

			echo '</div>';
			$c++;
		}

		while (bp_profile_groups()) : bp_the_profile_group();
			while (bp_profile_fields()) : bp_the_profile_field();

				$field_input_name = bp_get_the_profile_field_input_name();
				$field_type = bp_get_the_profile_field_type();

				// Remove the BuddyPress field username
				if ( $field_type == 'textarea')
					continue;

				$field_name = bp_get_the_profile_field_name();

				$show_in_form = woffice_get_settings_option('buddypress_'.$field_name.'_add_to_search');

				if (!$show_in_form)
					continue;

				$placeholder = '';
				if ($field_type == 'number')
					$placeholder = esc_html__('Min Value', 'woffice');

				echo '<div class="form-group">';
				$field_object = bp_xprofile_create_field_type($field_type);
				$value = (isset($_POST[$field_input_name])) ? $_POST[$field_input_name] : '';

				add_filter('bp_get_the_profile_field_is_required', function () {
				    return false;
                });

				if (is_array($value)) {
					$sanitized_value = array_map( 'esc_attr', $value );
                } else {
					$sanitized_value = esc_attr($value);
                }

				$field_object->edit_field_html(array(
					'value'         => $sanitized_value,
					'class'         => 'form-control',
					'placeholder'   => $placeholder,
                ));

				remove_filter('bp_get_the_profile_field_is_required', function () {
					return false;
				});

				if($field_type == 'number') {
					$value = ( isset( $_POST[ $field_input_name . '_max' ] ) ) ? $_POST[ $field_input_name . '_max' ] : '';
					echo '<input type="number" name="' . $field_input_name . '_max" value="' . $value . '" placeholder="' . esc_html__( 'Max Value', 'woffice' ) . '" />';
				}

				echo '</div>';

				$c++;
			 endwhile;
		endwhile;

        /**
         * After the fields of the advanced search form. you might add some additional field
         */
		do_action('woffice_advanced_search_members_after_fields');

		if (empty($c) && apply_filters('woffice_advanced_search_alert_no_fields_selected', true)) {
			echo '<p>';
			echo '<b>' . esc_html_x('IMPORTANT:', 'Advanced search form alert: no fields selected', 'woffice') . '</b>';
			echo ' ' . esc_html__('You have to set the filterable fields in "Appearance > Theme Settings > Buddypress > Display Fields in BuddyPress members directory"', 'woffice');
			echo '</p>';
		} else {
		    echo '<div class="text-right">';
			echo '<button type="submit" name="advanced-search-submit" id="advanced-search-submit" class="mb-0">';
			echo '<i class="fa fa-filter mr-2"></i>';
			echo  esc_html_x('Filter', 'Submit button for advanced search form', 'woffice');
			echo '</button>';
			echo '<a class="btn btn-secondary ml-2 mb-0 mt-0" id="advanced-search-reset" href="' . bp_get_members_directory_permalink() . '"><i class="fa fa-times-circle"></i> ' . esc_html_x('Clear', 'Submit button for advanced search form', 'woffice') . '</a>';
		    echo '</div>';
		}

		if ($render_form)
			echo '</form>';

		echo '</div>';

	}
}

if(!function_exists('woffice_advanced_search_fields_shortcode')) {
    /**
     * Search field shortcode
     *
     * @return string
     */
	function woffice_advanced_search_fields_shortcode() {

		ob_start();

		woffice_render_advanced_search_fields(true);

		return ob_get_clean();
	}
}
add_shortcode( 'woffice_advanced_search_fields', 'woffice_advanced_search_fields_shortcode' );

if(!function_exists('woffice_get_advanced_search_fields_from_post_request')) {
    /**
     * Get advanced search fields form post request
     *
     * @return array
     */
    function woffice_get_advanced_search_fields_from_post_request(){
        $advanced_fields = array();

        $post_data = $_POST['extras'];
        unset($_POST['extras']);
        
        foreach ($post_data as $key => $val) {
            if (!isset($_POST[$key])) {
                $_POST[$key] = $val;
            }
        }
        if (!bp_has_profile(array('user_id' => 0, 'fetch_field_data' => false))) {
            return $advanced_fields;
        }

        while (bp_profile_groups()) {
            bp_the_profile_group();

            while (bp_profile_fields()) {
                bp_the_profile_field();

                $field_input_name = bp_get_the_profile_field_input_name();
                $field_type       = bp_get_the_profile_field_type();

                if (!isset($_POST[$field_input_name])) {
                    continue;
                }

                if (empty($_POST[$field_input_name]) && $field_type !== 'number')
                    continue;

                $value = $_POST[$field_input_name];
                $value_max = '';

                if ($field_type == 'number') {
                    $value     = (!empty($_POST[ $field_input_name ])) ? $_POST[ $field_input_name ] : 0;
                    $value_max = (isset($_POST[$field_input_name . '_max']) && !empty($_POST[$field_input_name . '_max'])) ? $_POST[$field_input_name . '_max'] : '';

                    if (empty($value) && empty($value_max)) {
                        continue;
                    }
                }

                array_push($advanced_fields, array(
                        'key'   => str_replace( 'field_', '', $field_input_name ),
                        'value' => $value,
                        'value_max' => $value_max
                    )
                );
            }
        }

        return $advanced_fields;
    }
}

if(!function_exists('woffice_exclude_members')) {
    /**
     * Exclude some members according to a role
     *
     * @param string $roles - it's the role we look for
     * @param string $direction - exclude_all | exclude_role
     * @return string
     */
    function woffice_exclude_members($roles,$direction) {

        if(empty($roles)){
            return;
        }

        /* ALL USERS */
        $all_users = get_users( array('fields' => 'id') );

        /* REQUESTED USERS */
        if(is_array($roles)) {
            $requested_users = array();
            foreach ($roles as $role) {
                $requested_users_role = get_users(array('role' => $role, 'fields' => 'id', 'blog_id' => get_current_blog_id()));
                $requested_users = array_unique(array_merge($requested_users,$requested_users_role), SORT_REGULAR);
            }
        } else {
            $requested_users = get_users(array('role' => $roles, 'fields' => 'id'));
        }

        /* ALL USERS - REQUESTED MEMBERS = EXCLUDED MEMBERS
         * See members-loop.php file for more details
        */
        if ($direction == 'exclude_all') {
            $exclude_members = array_diff($all_users,$requested_users);
        } else {
            $exclude_members = $requested_users;
        }
        $query_exclude_members = implode(',', $exclude_members);

        return $query_exclude_members;
    }
}

if(!function_exists('woffice_get_cover_image')) {
    /**
     * We get the user's cover image
     *
     * @param int $user_ID - the member's ID
     * @return string (the URL)
     */
    function woffice_get_cover_image($user_ID) {

        if( ! woffice_bp_is_active( 'xprofile' ) ) {
            return '';
        }

        $the_cover_from_extension = ( woffice_bp_is_active( 'xprofile' ) ) ? bp_get_profile_field_data(array('field' => 'woffice_cover', 'user_id' => $user_ID)) : '';
        /*If the cover image extension is enabled*/
        if (!empty($the_cover_from_extension)) {
            return $the_cover_from_extension;
        }

        // BuddyPress default cover image
        $cover_src = bp_attachments_get_attachment( 'url', array(
            'item_id'   => $user_ID,
        ) );

        if( $cover_src ) {
            return $cover_src;
        }

        // XProfile cover image
        $the_cover_old = ( woffice_bp_is_active( 'xprofile' ) ) ? bp_get_profile_field_data(array('field' => 'Cover', 'user_id' => $user_ID)) : '';
        $array = array();
        preg_match( '/src="([^"]*)"/i', $the_cover_old, $array ) ;
        if (!empty($array[1])){
            return $array[1];
        }
        else {
            /*We check for default image*/
            $default_cover = (function_exists( 'fw_get_db_ext_settings_option' ) && function_exists("woffice_cover_upload_dir")) ? fw_get_db_ext_settings_option( 'woffice-cover', 'cover_default' ) : '';
            if (!empty($default_cover)) {
                return $default_cover['url'];
            }
        }

	    return '';

    }
}

if(!function_exists('woffice_user_notifications')) {
    /**
     * WOFFICE USER'S NOTIFICATION per compoment
     *
     * @param string $component - it's a BuddypPess compoment
     * @return string (HTML markup)
     */
    function woffice_user_notifications($component){

		if ($component == "notifications" && woffice_bp_is_active("notifications")) {
			$count_notifications = bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
			return (!empty($count_notifications)) ? '<span class="count">'.bp_core_number_format( $count_notifications ).'</span>' : '';
		}
		elseif ($component == "messages" && woffice_bp_is_active("messages")){
			$count_messages = messages_get_unread_count();
			if (!empty($count_messages)) {
				return '<span class="count">'.bp_core_number_format( $count_messages ).'</span>';
			}
		}
		elseif ($component == "friends" && woffice_bp_is_active("friends")){
			$count_friends = friends_get_total_friend_count();
			if ($count_friends > 0) {
				return '<span class="count">'.bp_core_number_format( $count_friends ).'</span>';
			}
		}
		elseif ($component == "groups" && woffice_bp_is_active("groups")){
			$count_groups = bp_get_total_group_count_for_user();
			if ($count_groups > 0) {
				return '<span class="count">'.bp_core_number_format( $count_groups ).'</span>';
			}
		}
		else {
			if (woffice_bp_is_active("notifications")) {
				/*Get all notifications*/
				$notifications = bp_notifications_get_notifications_for_user(bp_loggedin_user_id(), 'object');
				$count = 0;
				if (!empty($notifications)) {
					foreach ($notifications as $single_notifcation) {
						if ($single_notifcation->component_name == $component || $component == 'notifications') {
							$count++;
						}
					}
				}
				if ($count > 0) {
					$html_markup = '<span class="count">' . $count . '</span>';
					return $html_markup;
				} else {
					return '';
				}
			} else {
				return '';
			}
		}

    }
}

if(!function_exists('woffice_notifications_menu')) {
    /**
     * We create the wrapper (HTML), that's the default state while the AJAX is loading
     *
     * @return string (HTML markup)
     */
    function woffice_notifications_menu()
    {

        echo '<div id="woffice-notifications-menu">';

        echo '<div id="woffice-notifications-content"></div>';

        echo '</div>';

    }
}

if(!function_exists('woffice_calculate_time_span')) {
    /**
     * Calculate Time Difference between 2 dates (for the Notifications)
     *
     * @param string $date - the actual time
     * @return string
     */
    function woffice_calculate_time_span($date)
    {
        $seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

        $months = floor($seconds / (3600 * 24 * 30));
        $day = floor($seconds / (3600 * 24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours * 3600)) / 60);
        $secs = floor($seconds % 60);

        if ($seconds < 60)
            $time = $secs . __(" seconds ago", "woffice");
        else if ($seconds < 60 * 60)
            $time = $mins . __(" min ago", "woffice");
        else if ($seconds < 24 * 60 * 60)
            $time = $hours . __(" hours ago", "woffice");
        else if ($seconds < 24 * 60 * 60)
            $time = $day . __(" day ago", "woffice");
        else
            $time = $months . __(" month ago", "woffice");

        return $time;
    }
}

if(!function_exists('wofficeNoticationsGetHandler')) {
    /**
     * AJAX SCRIPT, We fetch the notification for the users
     *
     * @return string (HTML markup)
     */
    function wofficeNoticationsGetHandler()
    {

        $user_id = intval($_POST['user']);

        if (!function_exists('bp_notifications_get_unread_notification_count') || !function_exists('bp_notifications_get_notifications_for_user'))
            return;

        if (bp_notifications_get_unread_notification_count($user_id) > 0) {
            $notifications = bp_notifications_get_notifications_for_user($user_id, "object");
            $notifications = array_reverse($notifications);
            /* Returns :
                [id] => '1'
                [user_id] => '1'
                [item_id] => '10'
                [component_name] => 'activity'
                [component_action] => 'new_at_mention'
                [date_notified] => '2015-11-08 14:50:08'
                [is_new] => '1'
                [content] => 'admin2 mentioned you'
                [href] => '...'
            */
            if (!empty($notifications)) {

                foreach ($notifications as $notification) {
                    // Unread
                    $active = ($notification->is_new == 1) ? 'active' : '';
                    // Icon
                    switch ($notification->component_name) {
                        case "activity":
                            $icon_class = "fa-share";
                            break;
                        case "blogs":
                            $icon_class = "fa-th-large";
                            break;
                        case "forums":
                            $icon_class = "fa-sitemap";
                            break;
                        case "friends":
                            $icon_class = "fa-user";
                            break;
                        case "groups":
                            $icon_class = "fa-users";
                            break;
                        case "messages":
                            $icon_class = "fa-envelope";
                            break;
                        default:
                            $icon_class = "fa-bell";
                    }
                    // Time
                    $time_difference = bp_core_time_since($notification->date_notified);

                    echo '<div class="woffice-notifications-item ' . $active . '">';

                    if (($notification->component_name == 'woffice_wiki' || $notification->component_name == 'woffice_project' || $notification->component_name == 'woffice_blog')
                        && (substr($notification->content, 0, 4) == 'Your')
                        || $notification->component_name == 'woffice_project' && ($notification->component_action == 'woffice_project_assigned_todo' || $notification->component_action == 'woffice_project_assigned_member') && (substr($notification->content, 0, 4) != 'You ')
                        && $notification->secondary_item_id != 0
                    ) {
                        echo get_avatar($notification->secondary_item_id, 50);
                    } else {
                        // We check for an username in the content :
                        $strings = explode(" ", $notification->content);

                        // We get all the users BUT we limit to 100 queries so it's pretty fast and we save the PHP memory
                        $woffice_wp_users = get_users(array('fields' => array('ID', 'display_name'), 'number' => 100));

                        foreach ($strings as $word) {
                            foreach ($woffice_wp_users as $user) {
                                if ($user->display_name == $word) {
                                    echo get_avatar($user->ID, 50);
                                    break;
                                }
                            }
                        }
                    }


                    // Display notification
                    echo '<a href="' . $notification->href . '" alt="' . $notification->content . '">';
                    echo '<i class="fa component-icon ' . $icon_class . '"></i> ' . $notification->content . ' <span>(' . $time_difference . ')</span>';
                    echo '</a>';

                    echo '<a href="javascript:void(0)" class="mark-notification-read" data-component-action="' . $notification->component_action . '" data-component-name="' . $notification->component_name . '" data-item-id="' . $notification->item_id . '">';
                    echo '<i class="fas fa-times"></i></a>';

                    echo '</div>';

                }

            }
        } else {
            echo '<p class="woffice-notification-empty">' . __("You have", "woffice") . " <b>0</b> " . __("unread notifications.", "woffice") . '</p>';
        }

        exit();

    }
}
add_action('wp_ajax_nopriv_wofficeNoticationsGet', 'wofficeNoticationsGetHandler');
add_action('wp_ajax_wofficeNoticationsGet', 'wofficeNoticationsGetHandler');

if(!function_exists('wofficeNoticationsMarkedHandler')) {
    /**
     * Mark a notification as read in BuddyPress
     *
     * @return null
     */
    function wofficeNoticationsMarkedHandler()
    {

        $user_id = intval($_POST['user']);
        $component_action = sanitize_text_field($_POST['component_action']);
        $component_name = sanitize_text_field($_POST['component_name']);
        $item_id = intval($_POST['item_id']);
        bp_notifications_mark_notifications_by_item_id($user_id, $item_id, $component_name, $component_action, false, 0);

        exit();

    }
}
add_action('wp_ajax_nopriv_wofficeNoticationsMarked', 'wofficeNoticationsMarkedHandler');
add_action('wp_ajax_wofficeNoticationsMarked', 'wofficeNoticationsMarkedHandler');

if(!function_exists('woffice_user_sidebar')) {
    /**
     * Create the user's sidebar
     *
     * @return string - HTML markup
     */
    function woffice_user_sidebar()
    {
        echo '<!-- START USER LINKS - WAITING FOR FIRING -->';
        echo '<div id="user-sidebar">';
        $user_ID = get_current_user_id();
	    $woffice_cover_url = woffice_get_cover_image($user_ID);
	    $buddypress_cover_url = woffice_get_bp_member_cover($user_ID);
	    if (!empty($woffice_cover_url) && function_exists('woffice_upload_cover_btn')):
            echo '<header id="user-cover" class="has-woffice-cover" style="background-image: url(' . esc_url($woffice_cover_url) . ')">';
        elseif (!empty($buddypress_cover_url)):
	        echo '<header id="user-cover" class="has-bp-cover" style="background-image: url(' . esc_url($buddypress_cover_url) . ')">';
        else:
            echo '<header id="user-cover">';
        endif;
        echo '<a href="' . bp_core_get_user_domain($user_ID) . '" class="clearfix">';
        echo get_avatar($user_ID);

        $name_to_display = woffice_get_name_to_display($user_ID);

        echo '<span>' . sprintf( _x( 'Welcome <span class="woffice-welcome">%s</span>', "Welcome USERNAME - It's the welcome message in the user sidebar", 'woffice' ), $name_to_display ) . '</span>';

        echo '</a>';
        echo '<div class="user-cover-layer"></div>';
        echo '</header>';
        echo '<nav>';
        echo '<ul id="menu-bp" class="menu">';
        $profile = bp_loggedin_user_domain();
        // Activity :
        if( woffice_bp_is_active( 'activity' ) ) {
	        $activity_personal_li = '<li id="activity-personal-li" class="menu-parent">
					<a href="' . bp_get_activity_directory_permalink() . '">' . __('Activity', 'woffice') . ' ' . woffice_user_notifications('activity') . '</a>
					<ul class="sub-menu">
						<li id="just-me-personal-li" class="menu-child">
							<a href="' . $profile . 'activity/">' . __('Activity', 'woffice') . '</a>
						</li>
						<li id="activity-mentions-personal-li" class="menu-child">
							<a href="' . $profile . 'activity/mentions/">' . __('Mentions', 'woffice') . '</a>
						</li>
						<li id="activity-favs-personal-li" class="menu-child">
							<a href="' . $profile . 'activity/favorites/">' . __('Favorites', 'woffice') . '</a>
						</li>';
					if (woffice_bp_is_active('friends')) {
						$activity_personal_li .=  '<li id="activity-friends-personal-li" class="menu-child">
							<a href="' . $profile . 'activity/friends/">' . __('Friends', 'woffice') . '</a>
						</li>';
					}
					if (woffice_bp_is_active('groups')) {
						$activity_personal_li .=  '<li id="activity-groups-personal-li" class="menu-child">
							<a href="' . $profile . 'activity/groups/">' . __('Groups', 'woffice') . '</a>
						</li>';
					}
	        $activity_personal_li .=  '</ul>
			</li>';

	        /**
	         * Filters the Activity menu items in the user menu on the right
           *
           * @param string $activity_personal_li The HTML of the menu and eventual submenus
           * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_activity_personal_li', $activity_personal_li, $profile );
        }

        // XPROFILE :
        if (woffice_bp_is_active('xprofile')) {
            $xprofile_personal_li = '<li id="xprofile-personal-li" class="menu-parent">
                    <a href="' . $profile . '">' . __('Profile', 'woffice') . ' ' . woffice_user_notifications('xprofile') . '</a>
                    <ul class="sub-menu">
                        <li id="public-personal-li" class="menu-child">
                            <a href="' . $profile . 'profile/">' . __('View', 'woffice') . '</a>
                        </li>
                        <li id="edit-personal-li" class="menu-child">
                            <a href="' . $profile . 'profile/edit/">' . __('Edit', 'woffice') . '</a>
                        </li>';
                // == 0, this field has an inverted value
                if(bp_core_get_root_option( 'bp-disable-avatar-uploads' ) == 0){
	                $xprofile_personal_li .= '<li id="change-avatar-personal-li" class="menu-child">
                            <a href="' . $profile . 'profile/change-avatar/">' . __('Change Profile Photo', 'woffice') . '</a>
                        </li>';
                }
	        $xprofile_personal_li .= '</ul>
            </li>';

	        /**
	         * Filters the xprofile menu items in the user menu on the right
	         *
	         * @param string $xprofile_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_xprofile_personal_li', $xprofile_personal_li, $profile );
        }

        // NOTIFICATIONS :
        if (woffice_bp_is_active('notifications')) {
	        $notifications_personal_li = '<li id="notifications-personal-li" class="menu-parent">
                <a href="' . $profile . 'notifications/">' . __('Notifications', 'woffice') . ' ' . woffice_user_notifications('notifications') . '</a>';
	        $notifications_personal_li .= '<ul class="sub-menu">
					<li id="notifications-my-notifications-personal-li" class="menu-child">
						<a href="' . $profile . 'notifications/">' . __('Unread', 'woffice') . '</a>
					</li>
					<li id="read-personal-li" class="menu-child">
						<a href="' . $profile . 'notifications/read/">' . __('Read', 'woffice') . '</a>
					</li>
				</ul>
			</li>';

	        /**
	         * Filters the Notifications menu items in the user menu on the right
	         *
	         * @param string $notifications_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_notifications_personal_li', $notifications_personal_li, $profile );
        }

        // STATISTIC :
        if( current_user_can( 'edit_posts' ) ) {
            $statistics_personal_li = '<li id="notifications-personal-li" class="menu-parent" style="content:\f080;">
                <a href="' . $profile . 'statistic/">' . __('Statistic', 'woffice') . ' ' . woffice_user_notifications('statistic') . '</a>';
            $statistics_personal_li .= '<ul class="sub-menu">
                    <li id="notifications-my-notifications-personal-li" class="menu-child">
                        <a href="' . $profile . 'statistic/">' . __('Full Statistics', 'woffice') . '</a>
                    </li>
                </ul>
            </li>';

            /**
             * Filters the Statistics menu items in the user menu on the right
             *
             * @param string $statistics_personal_li The HTML of the menu and eventual submenus
             * @param string $profile The url of the user profile root
             */
            echo apply_filters( 'woffice_statistics_personal_li', $statistics_personal_li, $profile );
        }        

        // Messages :
        if (woffice_bp_is_active('messages')) {
            $messages_personal_li = '<li id="messages-personal-li" class="menu-parent">
            	<a href="' . $profile . 'messages/">' . __('Messages', 'woffice') . ' ' . woffice_user_notifications('messages') . '</a>';
	        $messages_personal_li .='<ul class="sub-menu">
					<li id="inbox-personal-li" class="menu-child">
						<a href="' . $profile . 'messages/">' . __('Inbox', 'woffice') . '</a>
					</li>
					<li id="starred-personal-li" class="menu-child">
						<a href="' . $profile . 'messages/starred/">' . __('Starred', 'woffice') . '</a>
					</li>
					<li id="sentbox-personal-li" class="menu-child">
						<a href="' . $profile . 'messages/sentbox/">' . __('Sent', 'woffice') . '</a>
					</li>
					<li id="compose-personal-li" class="menu-child">
						<a href="' . $profile . 'messages/compose/">' . __('Compose', 'woffice') . '</a>
					</li>';

	                if( current_user_can( 'manage_options' ) ) {
		                $messages_personal_li .= '<li id="notices-personal-li" class="menu-child">
						    <a href="' . admin_url() . 'users.php?page=bp-notices">' . __( 'Notices', 'woffice' ) . '</a>
					    </li>';
	                }

              $messages_personal_li .= '	</ul>
			</li>';

	        /**
	         * Filters the Messages menu items in the user menu on the right
	         *
	         * @param string $messages_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_messages_personal_li', $messages_personal_li, $profile );
        }

	    // Calendar :
	    if (fw_ext('woffice-event')) {
		    $calendar_url = bp_loggedin_user_domain() . 'calendar';
		    $event_personal_li =  '<li id="event-personal-li">
                <a href="' . $calendar_url . '" class="fa-calendar-alt">' . __("Calendar", "woffice") . '</a>
			</li>';

		    /**
		     * Filters the Event menu items in the user menu on the right
		     *
		     * @param string $event_personal_li The HTML of the menu and eventual submenus
		     * @param string $profile The url of the user profile root
		     */
		    echo apply_filters( 'woffice_event_personal_li', $event_personal_li, $profile );
	    }

        // friends :
        if (woffice_bp_is_active('friends')) {
            $friends_personal_li =  '<li id="friends-personal-li" class="menu-parent">
                <a href="' . $profile . 'friends/">' . __('Friends', 'woffice') . ' ' . woffice_user_notifications('friends') . '</a>';
	        $friends_personal_li .='<ul class="sub-menu">
					<li id="friends-my-friends-personal-li" class="menu-child">
						<a href="' . $profile . 'friends/">' . __('Friendships', 'woffice') . '</a>
					</li>
					<li id="requests-personal-li" class="menu-child">
						<a href="' . $profile . 'friends/requests/">' . __('Requests', 'woffice') . '</a>
					</li>
				</ul>
			</li>';

	        /**
	         * Filters the Friends menu items in the user menu on the right
	         *
	         * @param string $friends_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_friends_personal_li', $friends_personal_li, $profile );
        }

        // groups :
        if (woffice_bp_is_active('groups')) {
	        $groups_personal_li = '<li id="groups-personal-li" class="menu-parent">
            	<a href="' . $profile . 'groups/">' . __('Groups', 'woffice') . ' ' . woffice_user_notifications('groups') . '</a>';
	        $groups_personal_li .='<ul class="sub-menu">
					<li id="groups-my-groups-personal-li" class="menu-child">
						<a href="' . $profile . 'groups/">' . __('Memberships', 'woffice') . '</a>
					</li>
					<li id="invites-personal-li" class="menu-child">
						<a href="' . $profile . 'groups/invites/">' . __('Invitations', 'woffice') . '</a>
					</li>
					<li id="groups-create-li" class="menu-child">
						<a href="' . $profile . 'groups/create/">' . __('Create', 'woffice') . '</a>
					</li>
				</ul>
			</li>';

	        /**
	         * Filters the Groups menu items in the user menu on the right
	         *
	         * @param string $groups_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_groups_personal_li', $groups_personal_li, $profile );
        }

        // settings :
        if( woffice_bp_is_active( 'settings' ) ) {
	        $settings_personal_li = '<li id="settings-personal-li" class="menu-parent">
                    <a href="'.$profile.'settings/">'.__('Settings','woffice').' '.woffice_user_notifications('settings').'</a>
                    <ul class="sub-menu">
                        <li id="general-personal-li" class="menu-child">
                            <a href="'.$profile.'settings/">'.__('General','woffice').'</a>
                        </li>
                        <li id="notifications-personal-li" class="menu-child">
                            <a href="'.$profile.'settings/notifications/">'.__('Email','woffice').'</a>
                        </li>
                        <li id="profile-personal-li" class="menu-child">
                            <a href="'.$profile.'settings/profile/">'.__('Profile Visibility','woffice').'</a>
                        </li>
                    </ul>
                </li>';

	        /**
	         * Filters the Settings menu items in the user menu on the right
	         *
	         * @param string $settings_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_settings_personal_li', $settings_personal_li, $profile );
        }

        // Courses :
        if (function_exists("buddypress_learndash")) {

	        $courses_personal_li = '<li id="courses-personal-li" class="menu-parent">
                    <a href="'.$profile.'courses/">'.__('Courses','woffice').'</a>
                    <ul class="sub-menu">
                        <li id="general-personal-li" class="menu-child">
                            <a href="'.$profile.'courses/">'.__('General','woffice').'</a>
                        </li>
                    </ul>
                </li>';

	        /**
	         * Filters the Courses menu items in the user menu on the right
	         *
	         * @param string $courses_personal_li The HTML of the menu and eventual submenus
	         * @param string $profile The url of the user profile root
	         */
	        echo apply_filters( 'woffice_courses_personal_li', $courses_personal_li, $profile );
        }

        if ( has_nav_menu('woffice_user') ) {
            wp_nav_menu(array('theme_location' => 'woffice_user', 'menu_id'=>'dropdown-user-menu', 'container' => ''));
        }

        // Log out URL
        echo'<li id="logout-li"><a href="'.wp_logout_url().'">'. __('Log Out','woffice').'</a></li>';
        echo'</ul>';

        echo'</nav>';
        echo'</div>';

    }
}

if(!function_exists('woffice_cover_image_css')) {
    /**
     * Woffice Group Cover image support
     *
     * @since 2.3.0
     * @link https://codex.buddypress.org/themes/buddypress-cover-images/
     * @param array $settings
     * @return array
     */
    function woffice_cover_image_css($settings = array())
    {
        $theme_handle = 'bp-parent-css';
        $settings['width'] = 1600;
        $settings['height'] = 440;
        $settings['theme_handle'] = $theme_handle;
        $settings['callback'] = 'woffice_cover_image_callback';

        return $settings;
    }
}
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'woffice_cover_image_css', 10, 1 );

if(!function_exists('woffice_cover_image_callback')) {
    /**
     * Call back for custom CSS about the cover image in groups
     * @param array $params
     * @return string
     */
    function woffice_cover_image_callback($params = array())
    {
        if (empty($params) || empty($params['cover_image'])) {
            return '';
        }

        return '
        .bp_group #buddypress #item-header {
            background-image: url(' . $params['cover_image'] . ');
        }
    ';
    }
}

if(!function_exists('woffice_dequeue_bp_styles')) {
    /**
     * Remove CSS from BuddyPress, we include it through static.php
     *
     * @return null
     */
    function woffice_dequeue_bp_styles()
    {
        wp_dequeue_style('bp-legacy-css');
    }
}
add_action( 'wp_enqueue_scripts', 'woffice_dequeue_bp_styles', 20 );

if(!function_exists('woffice_member_username')) {
    /**
     * Removing TITLE OF MEMBER PAGE
     *
     * @return string (a template for BuddyPress)
     */
    function woffice_member_username()
    {
        global $members_template;

        return $members_template->member->user_login;
    }
}
add_filter('bp_member_name','woffice_member_username');

if(!function_exists('woffice_buddypress_name_to_display')) {
    function woffice_buddypress_name_to_display()
    {
        global $members_template;
        return woffice_get_name_to_display($members_template->member->ID);
    }
}
//add_filter('bp_core_get_user_displayname', 'woffice_buddypress_name_to_display');
add_filter('bp_member_name', 'woffice_buddypress_name_to_display');

if ( !function_exists('woffice_social_fields') ){
    /**
     * Creating Social fields group & fields for Woffice
     *
     * @return null
     */
	function woffice_social_fields() {

	    if(!woffice_bp_is_active( 'xprofile' ))
	        return;

		$buddy_social = woffice_get_settings_option('buddy_social');
		if ($buddy_social == "show") {
			global $wpdb;
			$group_args = array(
				'name' => 'Social',
				'field_group_id' => 'woffice_options',
			);
			$table_name = woffice_get_xprofile_table();
			$sqlStr = "SELECT * FROM " . $table_name . " WHERE name = 'Social'; ";
			$groups = $wpdb->get_results($sqlStr);
			if (count($groups) > 0) {
				return;
			}

			$group_id = xprofile_insert_field_group($group_args);
			$field_desc = __('Copy paste your URL in this field please, if it is empty it will not be displayed.', 'woffice');

			/*
			 * FACEBOOK FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Facebook'
				)
			);
			/*
			 * TWITTER FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Twitter'
				)
			);
			/*
			 * LINKEDIN FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Linkedin'
				)
			);
			/*
			 * SLACK FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Slack'
				)
			);
			/*
			 * GOOGLE FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Google'
				)
			);
			/*
			 * GITHUB FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Github'
				)
			);
			/*
			 * INSTAGRAM FIELD
			 */
			xprofile_insert_field(
				array(
					'field_group_id' => $group_id,
					'can_delete' => true,
					'type' => 'textbox',
					'description' => $field_desc,
					'name' => 'Instagram'
				)
			);
		}
	}
}
add_action('after_switch_theme', 'woffice_social_fields');
add_action('fw_settings_form_saved', 'woffice_social_fields');

if ( !function_exists('woffice_member_social_extend') ) {
    /**
     * Renders the HTML markup for the social fields
     *
     * @return void|string (HTML markup)
     */
    function woffice_member_social_extend()
    {
        if (!woffice_bp_is_active('xprofile')) {
            return;
        }

	    global $bp;
        $member_id = $bp->displayed_user->id;

        // Get the data
        $woffice_facebook   = xprofile_get_field_data('Facebook', $member_id);
        $woffice_twitter    = xprofile_get_field_data('Twitter', $member_id);
        $woffice_linkedin   = xprofile_get_field_data('Linkedin', $member_id);
        $woffice_slack      = xprofile_get_field_data('Slack', $member_id);
        $woffice_google     = xprofile_get_field_data('Google', $member_id);
        $woffice_github     = xprofile_get_field_data('Github', $member_id);
        $woffice_instagram  = xprofile_get_field_data('Instagram', $member_id);

        // Rendering
        if (empty($woffice_facebook) && empty($woffice_twitter) && empty($woffice_linkedin) && empty($woffice_slack) && empty($woffice_google) && empty($woffice_github) && empty($woffice_instagram)) {
	        return;
        }

        echo '<ul class="woffice-member-social list-inline text-center h4">';

        /**
         * Before the list of rendered social icons of the member.
         *
         * @param int $member_id
         */
        do_action('woffice_before_member_icons', $member_id);

        echo ($woffice_facebook)    ? '<li class="list-inline-item"><a href="' . esc_url($woffice_facebook) . '"  title="' . __('Facebook URL', 'woffice') . '" target="_blank"><i class="fab fa-facebook"></i></a></li>' : '';
        echo ($woffice_twitter)     ? '<li class="list-inline-item"><a href="' . esc_url($woffice_twitter) . '"  title="' . __('Twitter URL', 'woffice') . '" target="_blank"><i class="fab fa-twitter"></i></a></li>' : '';
        echo ($woffice_linkedin)    ? '<li class="list-inline-item"><a href="' . esc_url($woffice_linkedin) . '"  title="' . __('Linkedin URL', 'woffice') . '" target="_blank"><i class="fab fa-linkedin"></i></a></li>' : '';
        echo ($woffice_slack)       ? '<li class="list-inline-item"><a href="' . esc_url($woffice_slack) . '"  title="' . __('Slack URL', 'woffice') . '" target="_blank"><i class="fab fa-slack"></i></a></li>' : '';
        echo ($woffice_google)      ? '<li class="list-inline-item"><a href="' . esc_url($woffice_google) . '"  title="' . __('Google URL', 'woffice') . '" target="_blank"><i class="fab fa-google-plus"></i></a></li>' : '';
        echo ($woffice_github)      ? '<li class="list-inline-item"><a href="' . esc_url($woffice_github) . '"  title="' . __('Github URL', 'woffice') . '" target="_blank"><i class="fab fa-github"></i></a></li>' : '';
        echo ($woffice_instagram)   ? '<li class="list-inline-item"><a href="' . esc_url($woffice_instagram) . '"  title="' . __('Instagram URL', 'woffice') . '" target="_blank"><i class="fab fa-instagram"></i></a></li>' : '';

        /**
         * After the list of rendered social icons of the member.
         *
         * @param string $member_id
         */
        do_action('woffice_after_member_icons', $member_id);

        echo '</ul>';

    }
}
add_action( 'bp_member_header_actions', 'woffice_member_social_extend' );

if(!function_exists('bp_woffice_directory_groups_search_form')) {
    /**
     * Renders the HTML markup for the custom search form in the main groups page
     *
     * @return string (HTML markup)
     */
    function bp_woffice_directory_groups_search_form()
    {
        $default_search_value = bp_get_search_default_text('groups');
        $search_value = !empty($_REQUEST['s']) ? stripslashes($_REQUEST['s']) : $default_search_value; ?>

        <form action="" method="get" id="search-groups-form">
            <label><input type="text" name="s" id="groups_search" placeholder="<?php echo esc_attr($search_value) ?>"/></label>
            <button type="submit" id="groups_search_submit" name="groups_search_submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
        <?php
    }
}

if(!function_exists('bp_woffice_directory_members_search_form')) {
    /**
     * Renders the HTML markup for the custom search form in the main members page
     *
     * @return string (HTML markup)
     */
    function bp_woffice_directory_members_search_form() {
        $default_search_value = bp_get_search_default_text( 'members' );
        $search_value         = !empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value; ?>

        <form action="" method="get" id="search-members-form">
            <label><input type="text" name="s" id="members_search" placeholder="<?php echo esc_attr( $search_value ) ?>" /></label>
            <button type="submit" id="members_search_submit" name="members_search_submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
        <?php
    }
}

if(!function_exists('woffice_list_xprofile_fields')) {
    /**
     * List of BuddyPress fields for the icons in the main members page
     *
     * @param int $user_ID
     * @param bool $is_printable
     *
     * @return string (HTML markup)
     */
    function woffice_list_xprofile_fields($user_ID, $is_printable = true)
    {

        if (!woffice_bp_is_active('xprofile')) {
            return;
        }

        $html = '';
        global $members_template;
    
        if (!isset($members_template->member) && !empty($members_template)) {
            $members_template->member = get_userdata($user_ID);
        }
        
        // We fetch all the BuddyPress fields
        bp_get_member_profile_data(array('user_id' => $user_ID));
	    
        $fields_values = array();
        if ($members_template->member->profile_data) {
            $fields_values = $members_template->member->profile_data;
        }

        //Add wordpress email to the array of fields fields
        $wordpress_email_field = array();
        $wordpress_email_field['field_id'] = null;
        $wordpress_email_field['name'] = 'wordpress_email';
        $wordpress_email_field['field_type'] = 'email';
        $wordpress_email_field['field_data'] = '';
    
       
        $fields_values = array_merge(array('wordpress_email' => $wordpress_email_field), $fields_values);

	    $formatted_social_items = array();

	    $social_fields_available = woffice_get_social_fields_available();
    
        $html .= '<div class="woffice-xprofile-list">';

        foreach ($fields_values as $field_name => $field) {

            if ($field_name == 'user_login' || $field_name == 'user_nicename' || $field_name == 'user_email')
                continue;

            // Skip display name used by BuddyPress
            if ($field['field_id'] == 1 && !apply_filters('woffice_include_display_name_in_members_loop_fields', false))
                continue;

            $field_type = $field['field_type'];
            $field_show = woffice_get_settings_option('buddypress_' . $field_name . '_display');
            $field_icon = woffice_get_settings_option('buddypress_' . $field_name . '_icon');

            // We check if the field have to be displayed
            if (!$field_show)
                continue;

            if ($field_name != 'wordpress_email') {
                $field_value = bp_get_profile_field_data('field=' . $field_name . '&user_id=' . $user_ID);
            } else {
                $user_info = get_userdata($user_ID);
                $field_value = "<a href='mailto:" . $user_info->user_email . "' rel='nofollow'>$user_info->user_email</a>";
            }

            // We check if the field is empty
            if (empty($field_value))
                continue;

	        // Try to understand if the field is a social link
	        $social_field     = false;
	        $field_name_lower = strtolower( $field_name );
	        foreach ( $social_fields_available as $socials_detectable_key => $socials_detectable_field ) {

		        if ( strpos( $field_name_lower, $socials_detectable_key ) !== false ) {

			        if ( empty( $field_icon ) ) {
				        $field_icon = $socials_detectable_field['icon'];
			        }

			        $social_field = true;
			        break;
		        }

	        }

             // We try to set a default icon
            if (empty($field_icon) && !$social_field) {
                $field_icon = 'fa-arrow-right';

                if ($field_type == 'datebox') {
                    $field_icon = 'fa-calendar';
                } elseif ($field_type == 'email') {
                    $field_icon = 'fa-envelope';
                }
            }

            $str_field_html = '<div class="row">';

	        // We format the field
	        if ( ! $social_field ) {
		        if ( $field_type == 'url' || $field_type == 'web' || $field_type == 'email' || is_array( $field_value ) ) {

                    $str_field_html .= '<span class="col-sm-1 text-center" ><i class="' . woffice_convert_fa4_to_fa5($field_icon) . '"></i></span>';
                    $str_field_html .= '<span class="col-sm">';

			        if ( is_array( $field_value ) ) {
                        $str_field_html .= implode( ", ", $field_value );
			        } else {
                        $str_field_html .= $field_value;
			        }
                    $str_field_html .= '</span>';
                    $str_field_html .= '</div>';
                    $html .= $str_field_html;

		        } else {
                    $str_field_html .= '<span class="col-sm-1 text-center"><i class="'. woffice_convert_fa4_to_fa5($field_icon) .'"></i></span>';
                    $str_field_html .= '<span class="col-sm">';
                    $str_field_html .= woffice_auto_link( $field_value, $field_name );
                    $str_field_html .= '</span>';
                    $str_field_html .= '</div>';
                    $html .= $str_field_html;
		        }
	        } else {
		        $field_string = '<a href="' . $field_value . '" target="_blank" class="mx-2"><i class="'. woffice_convert_fa4_to_fa5($field_icon) .' fa-2x"></i></a>';
		        $formatted_social_items[ $field_name ] = $field_string ;
	        }

        }
    
        $html .= '</div>';
	    
	    // We render the list of social fields
	    if ( ! empty( $formatted_social_items ) ) {
            $html .= '<div class="member-xprofile-social-items text-center mt-2">';
		    foreach ( $formatted_social_items as $field ) {
			    // Already escaped by BuddyPress multiple times
			    $html .= $field;
		    }
		    $html .= '</div>';
	    }

	    if ($is_printable) {
	        echo $html;
        }
	    else {
	        return addslashes($html);
        }
    }

}

if( !function_exists( 'woffice_get_social_fields_available') ) {
	function woffice_get_social_fields_available() {

		$social_fields_available = array(
			'facebook'  => array(
				'name' => 'Facebook',
				'icon' => 'fa-facebook-square'
			),
			'twitter'   => array(
				'name' => 'Twitter',
				'icon' => 'fa-twitter-square'
			),
			'linkedin'  => array(
				'name' => 'Linkedin',
				'icon' => 'fa-linkedin-square'
			),
			'slack'     => array(
				'name' => 'Slack',
				'icon' => 'fa-slack-square'
			),
			'google'    => array(
				'name' => 'Google',
				'icon' => 'fa-google-plus-square'
			),
			'github'    => array(
				'name' => 'Github',
				'icon' => 'fa-github-square'
			),
			'instagram' => array(
				'name' => 'Instagram',
				'icon' => 'fa-instagram'
			)
		);

		/**
		 * Filters the social fields available for the users
		 *
		 * @param array $fields [
		 *    'facebook' => array(
		 *      'name' => 'Facebook',
		 *      'icon' => 'fa-facebook-square'
		 *     ),
		 *      'twitter' => array(
		 *      'name' => 'Twitter',
		 *      'icon' => 'fa-twitter-square'
		 *     ),
		 *     ....
		 *     ....
		 *  ]
		 */
		return apply_filters( 'woffice_social_fields_available', $social_fields_available );
	}
}


if(!function_exists('woffice_auto_link')) {
	/**
     * Create an auto link to search the value in the directory
     * from the Buddypress function xprofile_filter_link_profile_data();
     *
	 * @param $field_value
	 * @param null|string $field_name
	 *
	 * @return array|string
	 */
	function woffice_auto_link($field_value, $field_name = null) {
		$buddy_directory_autolink = woffice_get_settings_option('buddy_directory_autolink');
		$buddy_directory_autolink = ($buddy_directory_autolink == 'yup');

        /**
         * Filters if the autolink option is enabled for some specific fields
         *
         * @param bool $buddy_directory_autolink The autolink option is enabled or not
         * @param string $field_name The current field name
         */
		$buddy_directory_autolink = apply_filters( 'woffice_buddypress_directory_autolink_field_enabled', $buddy_directory_autolink, $field_name);

		if (!$buddy_directory_autolink) {
			return $field_value;
		}

        if (!strpos( $field_value, ',') && (count(explode(' ', $field_value)) > 5)) {
            return $field_value;
        }

        $values = explode(',', $field_value);

        if (!empty($values)) {
	        $new_values = array();

            foreach ((array) $values as $value) {
                $value = trim($value);

                // More than 5 spaces.
                if (count( explode( ' ', $value ) ) > 5) {
                    $new_values[] = $value;

                    // Less than 5 spaces.
                } else {
                    $query_arg    = bp_core_get_component_search_query_arg( 'members' );
                    $search_url   = add_query_arg( array( $query_arg => urlencode( $value ) ), bp_get_members_directory_permalink() );
                    $new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>';
                }
            }
            $values = implode(', ', $new_values);
        }

        return $values;
	}
}

if(!function_exists('woffice_profile_tab_calendar')) {
    /**
     * Add Calendar Tab to the BuddyPress profile
     *
     * @return null
     */
    function woffice_profile_tab_calendar()
    {

        $buddy_calendar = woffice_get_settings_option('buddy_calendar');
        if (function_exists('add_eventon') && $buddy_calendar == "show") {
            bp_core_new_nav_item(array(
                'name' => __('Calendar', 'woffice'),
                'slug' => 'calendar',
                'default_subnav_slug' => 'calendar',
                'screen_function' => 'woffice_profile_calendar_screen',
                'position' => 99,
                'show_for_displayed_user' => true,
            ));
        }

    }
}
add_action( 'bp_setup_nav', 'woffice_profile_tab_calendar' );

if(!function_exists('woffice_profile_calendar_screen')) {
    /**
     * We register the screen for Buddypress engine
     *
     * @return null
     */
    function woffice_profile_calendar_screen()
    {
        // add title and content here - last is to call the members plugin.php template
        // add_action( 'bp_template_title', 'woffice_profile_calendar_title' );
        add_action('bp_template_content', 'woffice_profile_calendar_content');
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
}

if(!function_exists('woffice_profile_calendar_title')) {
    /**
     * Our new tab's title for the calendar
     *
     * @return string
     */
    function woffice_profile_calendar_title() {
        _e('Personal Calendar','woffice');
    }
}

if(!function_exists('woffice_profile_calendar_content')) {
    /**
     * The content of the calendar tab's content
     *
     * @return string - PHP content
     */
    function woffice_profile_calendar_content()
    {
        global $bp;
        $user_ID = $bp->displayed_user->id;
        if (!empty($user_ID)) {
            echo do_shortcode("[add_eventon_fc users='" . $user_ID . "']");
        }
    }
}

if(!function_exists('woffice_profile_tab_note')) {
    /**
     * Add Personal Notes to the BuddyPress profile tab
     *
     * @return null
     */
    function woffice_profile_tab_note()
    {
        // Check if the xprofile is active
	    if (!woffice_bp_is_active('xprofile'))
		    return;

	    // Check if the notes are active and the user id match the current logged in user
	    $buddy_notes = woffice_get_settings_option('buddy_notes');
        if ( $buddy_notes != 'show' )
            return;

	    // Check if the field actually exists (it might be deleted manually by mistake)
        global $wpdb;
        $table_name = woffice_get_xprofile_table('fields');
        $sqlStr = "SELECT `id` FROM $table_name WHERE `name` = 'Woffice_Notes'";
        $field = $wpdb->get_results($sqlStr);
        if (count($field) == 0)
            return;

        bp_core_new_nav_item(array(
            'name' => __('Notes', 'woffice'),
            'slug' => 'notes',
            'default_subnav_slug' => 'notes',
            'screen_function' => 'woffice_profile_tab_note_screen',
            'position' => 99,
            'show_for_displayed_user' => false, // Displays only for current logged in user
        ));


    }
}
add_action( 'bp_setup_nav', 'woffice_profile_tab_note' );

if(!function_exists('woffice_profile_tab_note_screen')) {
    /**
     * We register the screen for BuddyPress engine
     *
     * @return null
     */
    function woffice_profile_tab_note_screen()
    {
        //add title and content here - last is to call the members plugin.php template
        //add_action( 'bp_template_title', 'woffice_profile_calendar_title' );
        add_action('bp_template_content', 'woffice_profile_note_content');
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
}

if(!function_exists('woffice_profile_note_title')) {
    /**
     * Our new tab's title for the notes
     *
     * @return string
     */
    function woffice_profile_note_title()
    {
        _e('Personal Notes', 'woffice');
    }
}

if(!function_exists('woffice_profile_note_content')) {
    /**
     * The content of the note tab's content
     *
     * @return void
     */
    function woffice_profile_note_content() {

        global $bp;

        $user_ID = $bp->displayed_user->id;
        
        if (empty($user_ID)) {
	        return;
        }
	        
        // We check the is the notes have been saved
        if (isset($_POST['woffice_notes'])) {
            xprofile_set_field_data( 'Woffice_Notes', $user_ID, wp_kses($_POST['woffice_notes'], array(
	            'a' => array(
		            'href' => array(),
		            'title' => array()
	            ),
	            'img' => array(
		            'src' => array(),
		            'title' => array()
	            ),
	            'ul' => array(),
	            'ol' => array(),
	            'blockquote' => array(),
	            'li' => array(),
	            'br' => array(),
	            'em' => array(),
	            'strong' => array(),
	            'hr' => array(),
            )));
        }

        $the_notes = xprofile_get_field_data('Woffice_Notes', $user_ID);
        $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        ?>

        <form action="<?php  echo $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" method="POST" enctype="multipart/form-data" id="woffice_user_notes">
            <?php wp_editor($the_notes , 'woffice_notes' ,  array(
                    'media_buttons' => false,
                    'textarea_name' => 'woffice_notes'
            )); ?>
            <button type="submit" class="btn btn-default"><i class="fa fa-edit"></i> <?php _e('Save my notes', 'woffice'); ?></button>
        </form>

        <?php
    }
}

if(!function_exists('woffice_notes_add_field')) {
    /**
     * We create the personal note field for Xprofiles
     *
     * @return null
     */
    function woffice_notes_add_field()
    {

        if (!woffice_bp_is_active('xprofile'))
            return;

        $buddy_notes = woffice_get_settings_option('buddy_notes');
        if ($buddy_notes == "show") {
            /*
             * Create the FIELD
             */
            global $bp;
            global $wpdb;
            $table_name = woffice_get_xprofile_table('fields');
            $sqlStr = "SELECT `id` FROM $table_name WHERE `name` = 'Woffice_Notes'";
            $field = $wpdb->get_results($sqlStr);
            if (count($field) > 0) {
                return;
            }
            xprofile_insert_field(
                array(
                    'field_group_id' => 1,
                    'can_delete' => true,
                    'type' => 'textarea',
                    'name' => 'Woffice_Notes',
                    'field_order' => 1,
                    'is_required' => false,
                )
            );
        }

    }
}
add_action('fw_settings_form_saved', 'woffice_notes_add_field');

if(!function_exists('woffice_is_user_allowed_buddypress')) {
    /**
     * Control View of BuddyPress Component, if the user is allowed
     *
     * @param $type - view || redirect
     * @return boolean : true on allowed
     */
    function woffice_is_user_allowed_buddypress($type) {

        // We check if the role isn't excluded
        if ($type == "view") {
            // We grab the options
            // It returns a role
            $buddy_members_excluded = woffice_get_settings_option('buddy_members_excluded', array());
            $buddy_groups_excluded = woffice_get_settings_option('buddy_groups_excluded', array());
            $buddy_activity_excluded = woffice_get_settings_option('buddy_activity_excluded', array());

            if(is_user_logged_in()) {

                // User data :
                $user = wp_get_current_user();
                /* Thanks to BBpress we only keep the main role */
                $the_user_role = (is_array($user->roles)) ? $user->roles[0] : $user->roles;


                $excluded_roles = array();

                // Members :
                if (bp_is_members_component()){
                    $excluded_roles = $buddy_members_excluded;
                }
                // Groups :
                if (bp_is_groups_component()){
                    $excluded_roles = $buddy_groups_excluded;
                }
                // Activity : 
                if (bp_is_activity_component()){
                    $excluded_roles = $buddy_activity_excluded;
                }
                
                if(empty($excluded_roles)) {
	                $excluded_roles = array();
                }

                if(!is_array($excluded_roles)) {
                  $excluded_roles = array($excluded_roles);
                }

                if (!empty($excluded_roles) && in_array($the_user_role, $excluded_roles) && $the_user_role != "administrator") {
                    //if (in_array( $the_user_role , $excluded_roles ) && $the_user_role != "administrator") {
                    //return apply_filters('woffice_is_user_allowed_buddypress', false);
	                  $is_allowed = false;

                } else {
                    //return apply_filters('woffice_is_user_allowed_buddypress', true);
                    $is_allowed = true;
                }
            }
            // Otherwise it means the page have been set to public anyway
            else {
                //return apply_filters('woffice_is_user_allowed_buddypress', true);
	              $is_allowed = true;
            }

        }
        // It's in the redirection process
        else {
            // We grab the options
            // Either private or public
            $buddy_members_state = woffice_get_settings_option('buddy_members_state');
            $buddy_groups_state = woffice_get_settings_option('buddy_groups_state');
            $buddy_activity_state = woffice_get_settings_option('buddy_activity_state');

            // Members :
            if((bp_is_members_component() || bp_is_user()) && $buddy_members_state == "private" && !is_user_logged_in()) {
                return apply_filters('woffice_is_user_allowed_buddypress', false);
            }
            else {
                // Groups :
                if( woffice_bp_is_active( 'groups' ) && bp_is_groups_component() && $buddy_groups_state == "private" && !is_user_logged_in()) {
                    //return apply_filters('woffice_is_user_allowed_buddypress', false);
                    $is_allowed = false;
                }
                // Activity : 
                elseif ( woffice_bp_is_active( 'activity' ) && bp_is_activity_component() && $buddy_activity_state == "private" && !is_user_logged_in()) {
	                //return apply_filters('woffice_is_user_allowed_buddypress', false);
                    $is_allowed = false;
                }
                else {
                    //return apply_filters('woffice_is_user_allowed_buddypress', true);
                    $is_allowed = true;
                }
            }

        }

        /**
         * Filters the result of the function woffice_is_user_allowed_buddypress($type)
         *
         * @see woffice/inc/buddypress.php
         *
         * @param bool $is_allowed
         */
        return apply_filters('woffice_is_user_allowed_buddypress', $is_allowed);

    }
}

if(!function_exists('woffice_record_custom_post_type_posts')) {
    /**
     * Add theme's post type to the activity tracker
     *
     * @param string $post_types - not used as we re-declare it within the function
     * @return null
     */
    function woffice_record_custom_post_type_posts($post_types)
    {
        $post_types = array('project', 'wiki', 'post');
        return $post_types;
    }
}
add_filter( 'bp_blogs_record_post_post_types', 'woffice_record_custom_post_type_posts' );
add_filter( 'bp_blogs_record_comment_post_types', 'woffice_record_custom_post_type_posts' );

if(!function_exists('woffice_add_autocomplete_js')) {
    /**
     * Woffice Auto complete for members (on messages)
     */
	function woffice_add_autocomplete_js() {
		if (woffice_bp_is_active('messages')) {
			// Include the autocomplete JS for composing a message.
			if (bp_is_messages_component() && bp_is_current_action('compose')) {

				// ditch previously queued scripts
				wp_dequeue_script('bp-jquery-autocomplete');
				wp_dequeue_script('bp-jquery-autocomplete-fb');
				wp_dequeue_script('bp-jquery-bgiframe');
				wp_dequeue_script('bp-jquery-dimensions');

				// requeue scripts in footer
				$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script('bp-jquery-autocomplete', BP_PLUGIN_URL . "bp-messages/js/autocomplete/jquery.autocomplete{$min}.js", array('jquery'), bp_get_version(), true);
				wp_enqueue_script('bp-jquery-autocomplete-fb', BP_PLUGIN_URL . "bp-messages/js/autocomplete/jquery.autocompletefb{$min}.js", array(), bp_get_version(), true);
				wp_enqueue_script('bp-jquery-bgiframe', BP_PLUGIN_URL . "bp-messages/js/autocomplete/jquery.bgiframe{$min}.js", array(), bp_get_version(), true);
				wp_enqueue_script('bp-jquery-dimensions', BP_PLUGIN_URL . "bp-messages/js/autocomplete/jquery.dimensions{$min}.js", array(), bp_get_version(), true);

			}
		}
	}
}
add_action( 'bp_actions', 'woffice_add_autocomplete_js', 11 );

if(!function_exists('woffice_bp_core_activated_user')) {
	/**
	 * By default, buddypress set the default role to all users who are activated,
	 * this function set again the role and caps selected during the registration.
	 *
	 * @param $user_id
	 * @param null $key
	 * @param null $user
	 */
	function woffice_bp_core_activated_user( $user_id, $key = null, $user = null ) {

		$roles_on_registration = woffice_get_settings_option('register_role');
		if ( empty( $user_id ) || empty( $key ) || empty( $user ) || $roles_on_registration == 'nope') {
			return;
		}

		$user_created = get_userdata( $user_id );

		$user_created->set_role( '' );
		foreach ( $user['meta']['roles'] as $role ) {
			$user_created->add_role( $role );
		}

		$user_created->remove_all_caps();
		foreach ( $user['meta']['allcaps'] as $key => $cap ) {
			$user_created->add_cap( $key );
		}

	}
}
add_action('bp_core_activated_user', 'woffice_bp_core_activated_user', 10, 3);


if(!function_exists('woffice_css_to_remove_bbpress_default_breadcrumbs')) {
    /**
     * BBPress Default BreadCrumb
     */
	function woffice_css_to_remove_bbpress_default_breadcrumbs() {
		echo '<style>#content .bbp-breadcrumb {display: none;}</style>';
	}
}

if(!function_exists('woffice_remove_bbpress_default_breadcrumbs')) {
    /**
     * BBpress deactivate breadcrumb
     */
    function woffice_remove_bbpress_default_breadcrumbs() {
        if ( function_exists( 'fw' ) ) {
            if ( fw()->extensions->get( 'breadcrumbs' ) ) {
                add_action( 'wp_head', 'woffice_css_to_remove_bbpress_default_breadcrumbs' );
            }
        }
    }
}
//add_action('after_setup_theme', 'woffice_remove_bbpress_default_breadcrumbs');

if (!function_exists('woffice_members_filter_query')) {
	/**
     * Woffice members filter query
     * See bp_nouveau_ajax_querystring() for details
     *
     * @since 2.8.0
     *
	 * @param $query_string
     *
     * @return string
	 */
	function woffice_members_filter_query($query_string) {

	    global $bp;

	    if ($bp->current_component !== 'members') {
	        return $query_string;
        }

		$query_array = array();
		parse_str($query_string, $query_array);

        /**
         * Filter member by matching field
         */
        $matching_field_id = woffice_get_settings_option('member_matching_fields');
        $user_id = get_current_user_id();
        $all_include_string = '';
        $all_include_ids = '';
        if (isset($matching_field_id, $user_id)) {

            $matching_field = xprofile_get_field($matching_field_id, $user_id);
            if (isset($matching_field->data->value)) {

                global $wpdb;
                $query = "SELECT user_id FROM " . $wpdb->prefix . "bp_xprofile_data WHERE field_id = " . $matching_field->id . " AND value = '" . $matching_field->data->value . "';";

                $all_include_ids = $wpdb->get_col($query);

                $all_include_string = ($all_include_ids !== '' && sizeof($all_include_ids) > 0) ? implode(',',
                    $all_include_ids) : '';
            }
        }

		$buddy_excluded_directory = woffice_get_settings_option('buddy_excluded_directory');

		if (isset($_POST['role']) || !empty($buddy_excluded_directory)) {
			$exclude_members_role_filter = 0;
			$exclude_members_role_option = 0;

			if (isset($_POST['role'])) {
				$the_role = sanitize_text_field($_POST['role']);
				// We set a role and we want the list of all the  other users not in the role
				$exclude_members_role_filter = woffice_exclude_members($the_role, 'exclude_all');
			}

			if (!empty($buddy_excluded_directory)) {
				// We set a role and we want to exclude it so all its users
				$exclude_members_role_option = woffice_exclude_members($buddy_excluded_directory, 'exclude_role');
			}

			if ($exclude_members_role_filter || $exclude_members_role_option) {
			    $query_array['exclude'] = $exclude_members_role_filter . ',' . $exclude_members_role_option;
            }
		}

		// Get the request to filter members by xProfile fields
		if (woffice_bp_is_active('xprofile') && (!empty($_POST['advanced-search-submit']) || (!empty($_POST['extras'])))) {
			$advanced_fields = woffice_get_advanced_search_fields_from_post_request();
			$ids             = woffice_get_users_ids_by_xprofile_fields($advanced_fields);
			$include_string  = ($ids !== '' && sizeof($ids) > 0) ? implode( ',', $ids ) : '';

			// If empty => no result so we return to avoid BuddyPress fallback (all active users)
			if ($include_string === '') {
				return 'include=0';
			}
            $all_include_ids = array_merge($all_include_ids, $ids);
			$all_include_string .= ','. $include_string;
		}

        /**
         * Filter `woffice_members_loop_query_members_included`
         *
         * Includes members in the BuddyPress directory
         *
         * @param string $all_include_string - included members as a string
         * @param array $all_include_ids - all user ids as an array
         */
        $include_string = apply_filters( 'woffice_members_loop_query_members_included', $all_include_string, $all_include_ids );

        if (!empty($include_string)) {
            $query_array['include'] = $include_string;
        }

		$members_per_page   = woffice_get_settings_option( 'buddypress_members_per_page', 12 );
		$members_per_page   = (is_numeric($members_per_page) && $members_per_page > 0) ? absint( $members_per_page ) : 12;

		$query_array['per_page'] = $members_per_page;

		/**
		 * Woffice members directory query
		 *
		 * @param string $members_loop_query - the current query
		 */
		$members_loop_query = apply_filters('woffice_members_loop_query', http_build_query($query_array));

		return $members_loop_query;
	}
}
add_filter('bp_nouveau_ajax_querystring', 'woffice_members_filter_query');

if (woffice_bp_is_active('messages')) {
    define('BP_MESSAGES_AUTOCOMPLETE_ALL', true);
}

if (!function_exists('woffice_custom_datebox')) {
    /**
     * Custom Date Year HTML for BuddyPress
     *
     * @param $html
     * @param $type
     * @param $day
     * @param $month
     * @param $year
     * @param $field_id
     * @param $date
     * @return string
     */
    function woffice_custom_datebox($html, $type, $day, $month, $year, $field_id, $date) {

        if ($type == 'year') {

            $html = '<option value=""' . selected($year, '', false) . '>----</option>';

            for ($i = date('Y'); $i > 1900; $i--) {
                $html .= '<option value="' . $i . '"' . selected($year, $i, false) . '>' . $i . '</option>';
            }
        }

        return $html;

    }
}
add_filter( 'bp_get_the_profile_field_datebox', 'woffice_custom_datebox',10,7);

if(!function_exists('woffice_custom_bp_alerts')) {
    function woffice_custom_bp_alerts()
    {

        $bp = buddypress();

        // Get our alert values
        $message = $bp->template_message;
        $type = $bp->template_message_type;

        if (empty($type) || empty($message))
            return;

        // feedback coming from the side-wide notice
        if ($message == "Notice successfully created.")
            return;

        // Add our own alert
       Woffice_Alert::create()->setType($type)->setContent($message)->queue();

    }
}
add_action('bp_actions', 'woffice_custom_bp_alerts', 6);

if(!function_exists('woffice_render_notices')) {
    /**
     * Handling BuddyPress site-wide notices
     */
    function woffice_render_notices()
    {

        // If BuddyPress Private Messaging component is not active
        // Or if user not logged
        if (!woffice_bp_is_active( 'messages') || !is_user_logged_in())
            return;

        $user_id    = get_current_user_id();
        $notice = BP_Messages_Notice::get_active();

        // If no notice
       if ((empty($notice)) || !isset($notice->id) || ($notice->id === null))
            return;

        // We get the closed notices for our logged user
        $closed_notices = bp_get_user_meta( bp_loggedin_user_id(), 'closed_notices', true );

        // We initialised (in case our user doesn't have any closed notice yet)
        if (!is_array($closed_notices))
            $closed_notices = array();

        // We checked that our current active notices hasn't been closed by the logged user
        if (in_array($notice->id, $closed_notices))
            return;

        $subject = $notice->subject;
        $message = $notice->message;

        $alert = '<strong>'.$subject.'</strong><br>'.$message;

        // Our slightly alert
        Woffice_Alert::create()->setType('notice')->setContent($alert)->setTimeout(false)->queue();


        // As a matter of simplification, we set this displayed Alert as unactive for our current user
        // to improve, we would - later - use Ajax to do this
        // But it could do the trick for now on

        // We get all the closed id for our logged user
        $notice_ids = bp_get_user_meta( $user_id, 'closed_notices', true );
        if (!is_array( $notice_ids ))
            $notice_ids = array();

        $notice_ids[] = (int) $notice->id;

        bp_update_user_meta( $user_id, 'closed_notices', $notice_ids );

    }
}

// This hook is triggered before BuddyPress try to render its ugly alert - so no need to filter it
add_action('template_redirect', 'woffice_render_notices');

if(!function_exists('woffice_bp_core_fetch_avatar')) {
	/**
	 * @param string $gravatar_img
	 * @param array $args
	 *
	 * @return string
	 */
	function woffice_bp_core_fetch_avatar( $gravatar_img, $args ) {

		if ( 'user' == $args['object'] && strpos( $gravatar_img, 'gravatar' ) !== false ) {

			$social_default_avatar = get_user_meta( $args['item_id'], 'woffice_social_avatar', true );

			if ( $social_default_avatar ) {

				$doc = new DOMDocument();
				$doc->loadHTML( $gravatar_img );
				$tags = $doc->getElementsByTagName( 'img' );
				foreach ( $tags as $tag ) {
					$new_src_url = $social_default_avatar;
					$tag->setAttribute( 'src', $new_src_url );
				}

				return $doc->saveHTML();
			}

		}

		return $gravatar_img;

	}
}
add_filter( 'bp_core_fetch_avatar', 'woffice_bp_core_fetch_avatar', 10, 2);

if(!function_exists('woffice_bp_core_fetch_avatar_url')) {
	/**
	 * @param string $gravatar_img
	 * @param array $args
	 *
	 * @return mixed
	 */
	function woffice_bp_core_fetch_avatar_url( $gravatar_img, $args ) {

		if ( 'user' == $args['object'] && strpos( $gravatar_img, 'gravatar' ) !== false ) {

			$social_default_avatar = get_user_meta( $args['item_id'], 'woffice_social_avatar', true );

			if ( $social_default_avatar ) {
				$gravatar_img = $social_default_avatar;
			}

		}

		return $gravatar_img;
	}
}
add_filter( 'bp_core_fetch_avatar_url', 'woffice_bp_core_fetch_avatar_url', 10, 2);

if(!function_exists('woffice_bp_xprofile_cover_style')) {
	/**
	 * Profile Cover image support
	 *
	 * @link https://codex.buddypress.org/themes/buddypress-cover-images/
	 *
	 * @param $settings array
	 *
	 * @return array
	 */
	function woffice_bp_xprofile_cover_style( $settings = array() ) {

		$settings['width']    = 1000;
		$settings['height']   = 400;
		$settings['callback'] = 'woffice_bp_xprofile_cover_callback';

		return $settings;

	}
}
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'woffice_bp_xprofile_cover_style', 10, 1 );

if(!function_exists('woffice_bp_xprofile_cover_callback')) {
	/**
	 * Call back for custom CSS about the cover image in profiles
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	function woffice_bp_xprofile_cover_callback( $params = array() ) {
		if ( empty( $params ) ) {
			return '';
		}

		return '#buddypress div#item-header, #user-cover {
            background-image: url(' . $params['cover_image'] . ') !important;
        }';
	}
}

if(!function_exists( 'woffice_get_bp_member_cover') ) {
	/**
	 * Get Member Cover URL
	 *
	 * @param $user_id
	 * @return string
	 */
	function woffice_get_bp_member_cover($user_id) {

		$member_cover_image_url = bp_attachments_get_attachment('url', array(
			'object_dir' => 'members',
			'item_id' => $user_id,
		));

		return ($member_cover_image_url == false) ? '' : esc_url($member_cover_image_url);

	}
}

if(!function_exists( 'woffice_get_bp_group_cover') ) {
	/**
	 * Get Member Cover URL
	 *
	 * @param $group_id
	 * @return string
	 */
	function woffice_get_bp_group_cover($group_id) {

		$group_cover_image_url = bp_attachments_get_attachment('url', array(
			'object_dir' => 'groups',
			'item_id' => $group_id,
		));

		return ($group_cover_image_url == false) ? '' : esc_url($group_cover_image_url);

	}
}

if(!function_exists('woffice_get_current_group')) {
	/**
     * Returns the current group member
     *
	 * @return array
	 */
	function woffice_get_current_group() {
		$args = array(
			'group_id'            => bp_get_group_id(),
			'exclude_admins_mods' => false
		);

		$group_members_result = groups_get_group_members($args);
		$group_members        = array();

		foreach ( $group_members_result['members'] as $member ) {
			$group_members[] = array(
			   'id' => $member->ID,
			   'username' => $member->user_login,
            );
		}

		return $group_members;
	}
}

if (!function_exists('woffice_members_count')) {
    /**
     * Change the BuddyPress members count to get the real users and not the ones based on the activity
     */
    function woffice_members_count() {
        global $wpdb;

        return (int) $wpdb->get_var($sql = "SELECT COUNT(*) FROM {$wpdb->users}");
    }
}
add_filter('bp_core_get_active_member_count', 'woffice_members_count');

if (!function_exists('woffice_order_members_options')) {
    /**
     * Returns the ordered options of members list
     *
     * @param array $filter
     *
     * @return array
     */
    function woffice_order_members_options($filter) {
	    $filter['random'] = 'Random';

        $buddy_sort_members_by = woffice_get_settings_option('buddy_sort_members_by');

        if (!empty($filter) && isset($buddy_sort_members_by) && isset($filter[$buddy_sort_members_by])) {

            $new_filter = array(
                $buddy_sort_members_by => $filter[$buddy_sort_members_by]
            );

            foreach ($filter as $k => $v) {
                if ($k === $buddy_sort_members_by)
                    continue;

                $new_filter[$k] = $v;
            }

            return $new_filter;
        }

        return $filter;
    }
}
add_filter('bp_nouveau_get_members_filters', 'woffice_order_members_options', 10, 2);
