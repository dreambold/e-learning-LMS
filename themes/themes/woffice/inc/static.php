<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Include static files: Javascript and Css
 * Compiled files
 */
if (is_admin()) {
	return;
}
/*---------------------------------------------------------
**
** COMMENTS SCRIPTS FROM WP
**
----------------------------------------------------------*/
if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
}
/*---------------------------------------------------------
**
** CSS FILES NEEDED FOR WOFFICE
**
----------------------------------------------------------*/
if (function_exists('woffice_get_fonts_url') && woffice_get_fonts_url() ) {
	wp_enqueue_style('theme-fonts', woffice_get_fonts_url(), array(), null);
}

// Assets
wp_enqueue_style(
	'assets-css',
	get_template_directory_uri() . '/css/assets.min.css',
	array(),
	'1.0'
);

//Custom CSS
wp_enqueue_style(
	'custom-css',
	get_template_directory_uri() . '/css/custom-css.css',
	array(),
	'1.0'
);

// Load our main stylesheet.
wp_enqueue_style(
    'woffice-theme-style',
    get_template_directory_uri() . '/style.css',
    '1.0'
);

// Load printed stylesheet.
wp_enqueue_style(
    'woffice-printed-style',
    get_template_directory_uri() . '/css/print.min.css',
    array(),
    '1.0',
    'print'
);
/*---------------------------------------------------------
**
** JS FILES NEEDED FOR WOFFICE
**
----------------------------------------------------------*/
// LOAD JS PLUGINS FOR THE THEME

wp_enqueue_script(
	'woffice-theme-script',
	get_template_directory_uri() . '/js/woffice.min.js',
	array( 'jquery', 'underscore' ),
	'1.0',
	true
);

//NAVIGATION FIXED
$header_fixed = woffice_get_settings_option('header_fixed');
if( $header_fixed == "yep" ) :
    wp_enqueue_script(
        'woffice-fixed-navigation',
        get_template_directory_uri() . '/js/fixed-nav.js',
        array( 'jquery' ),
        '1.0',
        true
    );
endif;



// We load the chat JS
if(Woffice_AlkaChat::isChatEnabled()) {


    $has_emojis = woffice_get_settings_option('alka_pro_chat_emojis_enabled');
    if ($has_emojis) {
        // Emojis CSS
        wp_enqueue_style('woffice-css-emojis-picker', get_template_directory_uri() . '/css/emojis/jquery.emojipicker.css', '1.0');
        wp_enqueue_style('woffice-css-emojis-twitter', get_template_directory_uri() . '/css/emojis/jquery.emojipicker.tw.css', '1.0');
        // Emojis JS
        wp_enqueue_script('woffice-js-emojis-picker', get_template_directory_uri() . '/js/emojis/jquery.emojipicker.js', array('jquery'), '1.0', true);
        wp_enqueue_script('woffice-js-emojis', get_template_directory_uri() . '/js/emojis/jquery.emojis.js', array('jquery'), '1.0', true);
    }

    // Main JS
    wp_enqueue_script(
        'woffice-alka-chat-script',
        get_template_directory_uri() . '/js/alkaChat.vue.js',
        array( 'jquery', 'woffice-theme-script' ),
        '1.0',
        true
    );

}

//Load scripts needed to attach image in the frontend editors
wp_enqueue_media();

{

    $data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_url' => get_site_url(),
        'user_id' => get_current_user_id()
    );

    /**
     * We give the possibility to hook new data for the Theme Script JS
     * It's basically used for all things related to the Ajax calls
     *
     * @param array $data
     */
    $data = apply_filters('woffice_js_exchanged_data', $data);

    // Mobile menu threshold
    $data['menu_threshold'] = woffice_get_settings_option('menu_threshold');

    $data['cookie_allowed'] = [
	    /**
	     * Filter `woffice_cookie_sidebar_enabled`
	     *
	     * Whether we save the sidebar state in a browser cookie
	     *
	     * @package boolean
	     */
	    'sidebar'  => apply_filters('woffice_cookie_sidebar_enabled', true),
    ];

    /**
     * The data is passed to the JS file in order to adjust the timeout delay for alerts
     * This paramenter need to be passed in milliseconds for example 4000 for 4s duration
     *
     * @param int $timeout
     */
    $data['alert_timeout'] = apply_filters( 'woffice_alert_timeout', 4000 );

    wp_localize_script('woffice-theme-script', 'WOFFICE', $data);

}

add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ($menu , $args ) {
    if (is_user_logged_in() && $args->theme_location == 'primary') {
        $current_user = wp_get_current_user();
        $user=$current_user->user_nicename ;
        $profilelink = '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a class="" href="/members/' . $user . '/profile"><img width="64" height="64" src="https://finanzrecht-service.de/wp-content/uploads/2019/07/iconfinder_icons_user_1564534.png" class="_mi _before _image mCS_img_loaded" alt="" aria-hidden="true"><span>'.__( 'My account', 'woffice' ).'</span></a></li>';
        $menu = $menu . $profilelink;
    }
    return $menu;
}

add_filter( 'wp_nav_menu_items', 'search_menu_item', 10, 2 );
function search_menu_item ($menu , $args ) {
    if (is_user_logged_in() && $args->theme_location == 'primary') {
        $header_search = apply_filters( 'woffice_header_search_enabled', $header_search);  ?>
            <!-- SEACRH FORM -->
            <?php $profilelink = '<li class="menu-item menu-item-type-post_type menu-item-object-page search"><a href="javascript:void(0)" id="search-trigger"><i class="fa fa-search"></i></a></li>'; ?>
        <?php 
        $menu = $profilelink . $menu;
    }
    return $menu;
}

