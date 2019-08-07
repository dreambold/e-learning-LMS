<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * This files contains custom hooks/actions/filters used by Woffice
 * You can find many of them organized within woffice/inc/classes/
 */

if(!function_exists('woffice_remove_fa_scripts')) {
	/**
	 * Remove any Font Awesome loading call so we use the Woffice one only
	 * This is to avoid conflicts between the version 4.7.0 and 5.x (loaded by Woffice)
	 *
	 * @since 2.8.1
	 */
	function woffice_remove_fa_scripts()
	{
		wp_dequeue_style('font-awesome');
		wp_dequeue_style('​fontawesome');
		wp_dequeue_style('font-awesome-original');
		wp_dequeue_style('uagb-fontawesome-css');
	}
}
add_action('wp_print_styles', 'woffice_remove_fa_scripts', 100);


if ( !function_exists( 'woffice_remove_default_wperp_admin_styles' ) ) {
    /**
     * Remove default WP ERP jQuery ui styles as it conflicts with some theme admin components (Unyson, etc)
     * @param string $hook
     */
    function woffice_remove_default_wperp_admin_styles( $hook ) {

        if ( $hook !== 'appearance_page_fw-settings' && $hook !== 'toplevel_page_fw-extensions' ) {
            return;
        }
        
        wp_dequeue_style( 'jquery-ui' );
    }
}
add_action( 'admin_enqueue_scripts', 'woffice_remove_default_wperp_admin_styles' );

if(!function_exists('woffice_allfiles')) {
    /**
     * All File Shortcode to exclude portfolio's NEW category
     * @return mixed|void
     */
    function woffice_allfiles()
    {
        if (!class_exists('multiverso_mv_category_files'))
            return;
        // Include allfiles.php template
        return include(get_template_directory() . '/inc/allfiles.php');
    }
}
add_shortcode( 'woffice_allfiles', 'woffice_allfiles' );

if(!function_exists('woffice_fix_admin_buddypress_style')) {
    /**
     * BuddyPress Admin CSS patch
     */
    function woffice_fix_admin_buddypress_style()
    {
        echo '<style>
      .bp-profile-field .datebox > label:first-child {width: 200px;}
      .bp-profile-field .datebox > label{width: auto;}
      .bp-profile-field select{margin-right:20px}
     </style>';
    }
}
add_action('admin_print_scripts', 'woffice_fix_admin_buddypress_style');

if(!function_exists('woffice_trashed_post_handler')) {
    /**
     * Redirect to the home page after a post is deleted
     */
	function woffice_trashed_post_handler() {
		if ( ! is_admin() && ( ( array_key_exists( 'deleted', $_GET ) && $_GET['deleted'] == '1' ) || ( array_key_exists( 'trashed', $_GET ) && $_GET['trashed'] == '1' ) ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'parse_request', 'woffice_trashed_post_handler' );

if(!function_exists('woffice_display_feeds_error')) {
    /**
     * Keep the feed private
     */
    function woffice_display_feeds_error() {
        $feeds_private = woffice_get_settings_option('feeds_private');
        if($feeds_private) {
            wp_die( __( 'No feed available, please visit our home page!', 'woffice' ) );
        }
    }
}
add_action('do_feed', 'woffice_display_feeds_error', 1);
add_action('do_feed_rdf', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss2', 'woffice_display_feeds_error', 1);
add_action('do_feed_atom', 'woffice_display_feeds_error', 1);
add_action('do_feed_rss2_comments', 'woffice_display_feeds_error', 1);
add_action('do_feed_atom_comments', 'woffice_display_feeds_error', 1);

if(!function_exists('woffice_display_feeds_error_2')) {
    /**
     * Keep BuddyPress feed private
     *
     * @return void
     */
    function woffice_display_feeds_error_2() {
        $feeds_private = woffice_get_settings_option('feeds_private');
        if($feeds_private) {
            echo '>';
            echo '</rss>';
            die();
        }
    }
}
add_action('bp_activity_sitewide_feed', 'woffice_display_feeds_error_2', 1);
add_action('bp_activity_personal_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_friends_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_my_groups_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_mentions_feed', 'woffice_display_feeds_error_2', 1 );
add_action('bp_activity_favorites_feed', 'woffice_display_feeds_error_2', 1 );
add_action('groups_group_feed', 'woffice_display_feeds_error_2', 1 );


if(!function_exists('woffice_add_bp_mentions_on_comments_area')) {
	/**
	 * Enable BuddyPress mentions on every comment area
	 *
	 * @param $field
	 * @return mixed
	 */
	function woffice_add_bp_mentions_on_comments_area( $field ) {
		return str_replace( 'textarea', 'textarea class="bp-suggestions"', $field );
	}
}
add_filter( 'comment_form_field_comment', 'woffice_add_bp_mentions_on_comments_area' );

if( !function_exists('woffice_woocommerce_prevent_admin_accesss')) {
    /**
     * Disable WooCommerce to prevent access from the dashboard
     *
     * @return boolean
     */
    function woffice_woocommerce_prevent_admin_accesss() {

        return false;

    }
}
add_filter( 'woocommerce_prevent_admin_access', '__return_false' );

if( !function_exists('woffice_remove_restricted_posts_from_query')) {
	/**
	 * Remove by default all restricted blog posts from all the query call, if the quey is not relative to a single post
	 *
	 * @param WP_Query $query
	 *
	 * @return mixed
	 */
	function woffice_remove_restricted_posts_from_query( $query ) {

		if (
			current_user_can( 'manage_options' )
			|| $query->is_single
			|| $query->is_page
			|| ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] != 'post' )
			|| ( isset( $query->query_vars['woffice_ignore_posts_permission'] ) && $query->query_vars['woffice_ignore_posts_permission'] )
		) {
			return $query;
		}

		$new_args                                    = $query->query_vars;
		$new_args['woffice_ignore_posts_permission'] = true;
		$my_query                                    = new WP_Query( $new_args );

		$excluded_posts = array();

		while ( $my_query->have_posts() ) : $my_query->the_post();
			if ( ! woffice_is_user_allowed( get_the_ID() ) ) {
				array_push( $excluded_posts, get_the_ID() );
			}
		endwhile;

		wp_reset_postdata();

		//If not exclude it from the real query call
		$query->set( 'post__not_in', $excluded_posts );

		return $query;

	}
}
add_filter('pre_get_posts', 'woffice_remove_restricted_posts_from_query');

if ( !function_exists( 'woffice_set_posts_per_page' ) ) {
	/**
	 * Set the posts per page of posts
	 *
	 * @param WP_Query $query
	 * @return mixed
	 */
	function woffice_set_posts_per_page( $query ) {

		if(is_admin()) return $query;

		if ( $query->is_main_query() && ( $query->is_home() || $query->is_tag() || $query->is_category() || $query->is_archive()) ) {

			$posts_per_page = woffice_get_settings_option('blog_number');
			$query->set( 'posts_per_page', (int) $posts_per_page );
		}

		return $query;
	}
}
add_filter('pre_get_posts', 'woffice_set_posts_per_page');

if( !function_exists( 'woffice_override_embed_site_icon' ) ) {
	/**
	 * Override the default embed site title in order to use the icon of Woffice theme
	 *
	 * @param $site_title
	 * @return string
	 */
	function woffice_override_embed_site_icon( $site_title ) {

		$site_title = sprintf(
			'<a href="%s" target="_top"><span>%s</span></a>',
			esc_url( home_url() ),
			esc_html( get_bloginfo( 'name' ) )
		);

		return '<div class="wp-embed-site-title">' . $site_title . '</div>';

	}
}
add_filter( 'embed_site_title_html', 'woffice_override_embed_site_icon' );

if( !function_exists('woffice_load_admin_textdomain_in_front') ) {
	/**
	 * Used foremost in order to translate the roles in frontend
	 */
	function woffice_load_admin_textdomain_in_front() {
		if ( ! is_admin() ) {
			load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
		}
	}
}
add_action( 'init', 'woffice_load_admin_textdomain_in_front' );

if (!function_exists('woffice_reset_extrafooter_transient')) {
	/**
	 * Refresh the transient of the extrafooter when a new user is added or when an old user is deleted
	 */
	function woffice_reset_extrafooter_transient() {

		delete_transient('woffice_extrafooter_member_ids');

	}
}
add_action( 'user_register', 'woffice_reset_extrafooter_transient');
add_action( 'delete_user', 'woffice_reset_extrafooter_transient');
add_action( 'fw_settings_form_saved', 'woffice_reset_extrafooter_transient');

if ( !function_exists( 'woffice_ajax_extrafooter_avatars' ) ) {
	/**
	 * Return to the AJAX callback the avatars to display in the extrafooter
	 */
	function woffice_ajax_extrafooter_avatars() {

		woffice_extrafooter_print_avatars();

		wp_die();

	}
}
add_action( 'wp_ajax_load_extrafooter_avatars', 'woffice_ajax_extrafooter_avatars');
add_action( 'wp_ajax_nopriv_load_extrafooter_avatars', 'woffice_ajax_extrafooter_avatars');

if(!function_exists('woffice_members_suggestion_autocomplete')) {
    /**
     * AJAX handler for project member autocomplete requests.
     *
     */
    function woffice_members_suggestion_autocomplete() {

        // Fail it's a large network.
        if ( is_multisite() && wp_is_large_network( 'users' ) ) {
            wp_die( - 1 );
        }

        $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';

        /**
         * Filter the members ids included in the members suggestion (in project assignation)
         *
         * @param array
         */
        $include = apply_filters( 'woffice_members_suggestion_include', array());

        /**
         * Filter the members ids excluded from the members suggestion (in project assignation)
         *
         * @param array
         */
        $exclude = apply_filters( 'woffice_members_suggestion_exclude', array());

        if ( ! $term ) {
            wp_die( - 1 );
        }

        $user_fields = array( 'ID' );

        //TODO remove users already added?
        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'search'         => "*{$term}*",
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
            ),
            'include' => $include,
            'exclude' => $exclude
        ) );

        $users_found_1 = $users->get_results();

        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
            ),
            'include' => $include,
            'exclude' => $exclude

        ) );
        $users_found_2 = $users->get_results();

        $users_found_3 = array_unique( array_merge($users_found_1, $users_found_2), SORT_REGULAR );

        // If we have a filter coming
        $users_filtered = isset( $_GET['filter'] ) ? $_GET['filter'] : '';

        $users_found = array();

        if ($users_filtered !== '') {
            foreach ($users_found_3 as $user) {
                if (array_key_exists($user->ID, $users_filtered))
                    array_push($users_found, $user);
            }
        } else {
            $users_found = $users_found_3;
        }

        $matches = array();

        if ( $users_found && ! is_wp_error( $users_found ) ) {
            foreach ( $users_found as $user ) {

                if( function_exists( 'bp_is_user_active' ) && !bp_is_user_active($user->ID) )
                    continue;

                $matches[] = array(
                    'label' => woffice_get_name_to_display($user->ID),
                    'value' => $user->ID,
                );
            }
        }

        wp_die( json_encode( $matches ) );
    }
}
add_action( 'wp_ajax_woffice_members_suggestion_autocomplete', 'woffice_members_suggestion_autocomplete'  );
add_action( 'wp_ajax_nopriv_woffice_members_suggestion_autocomplete', 'woffice_members_suggestion_autocomplete'  );