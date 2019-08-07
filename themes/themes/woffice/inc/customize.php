<?php
/**
 * We get all the style options from the Theme Settings and we inject CSS in the page's header
 *
 * @return string
 */
function woffice_get_custom_css()
{

	/*---------------------------------------------------------
	**
	** MAIN FONTS SETTINGS
	**
	----------------------------------------------------------*/
	$font_main_typography = woffice_get_settings_option('font_main_typography');
	$font_headline_typography = woffice_get_settings_option('font_headline_typography');
	$font_headline_bold = woffice_get_settings_option('font_headline_bold');
	$font_headline_uppercase = woffice_get_settings_option('font_headline_uppercase');
	$dashboard_headline_uppercase = woffice_get_settings_option('dashboard_headline_uppercase');
	$menu_headline_uppercase = woffice_get_settings_option('menu_headline_uppercase');

    $css = '';

	$font_main_ready = woffice_customization_get_array('font_main', 'font_main');

    if(isset($font_main_typography['family']) && isset($font_main_typography['size'])) {
        $css .= $font_main_ready . '{';
        $css .= 'font-family: ' . $font_main_typography['family'] . ',helvetica, arial, sans-serif; ';
        $css .= 'font-size: ' . $font_main_typography['size'] . 'px;';
        $css .= '}';
        $css .= 'h1, h2, h3, h4, h5, h6, #content-container .infobox-head{';
        $css .= 'font-family: ' . $font_headline_typography['family'] . ',helvetica, arial, sans-serif; ';
        $css .= '}';
    }
	$css .= 'h1, h2, h3, h4, h5, h6{';
	if ($font_headline_uppercase == "yep"):
		$css .= 'text-transform: uppercase;';
	endif;
	if ($font_headline_bold == "yep"):
		$css .= 'font-weight: bold;';
	endif;
	$css .= '}';
	$css .= '#content-container .intern-box.box-title h3{';
	if ($dashboard_headline_uppercase == "yep"):
		$css .= 'text-transform: uppercase;';
	else:
		$css .= 'text-transform: none;';
	endif;
	$css .= '}';
	if ($menu_headline_uppercase == "yep"):
		$css .= '.main-menu li > a{';
		$css .= 'text-transform: uppercase;';
		$css .= '}';
	endif;
	/*---------------------------------------------------------
	**
	** MAIN COLORS SETTINGS
	**
	----------------------------------------------------------*/
	$color_colored = woffice_get_settings_option('color_colored');
	$color_text = woffice_get_settings_option('color_text');
	$color_main_bg = woffice_get_settings_option('color_main_bg');
	$headline_color = woffice_get_settings_option('color_headline');
	$color_light1 = woffice_get_settings_option('color_light1');
	$color_light2 = woffice_get_settings_option('color_light2');
	$color_light3 = woffice_get_settings_option('color_light3');
	$color_notifications = woffice_get_settings_option('color_notifications');
	$color_notifications_green = woffice_get_settings_option('color_notifications_green');

	$colored_color_ready = woffice_customization_get_array('colored_color');
	$css .= $colored_color_ready . '{';
	$css .= 'color: ' . esc_html($color_colored) . ';';
	$css .= '}';
	$colored_color_important_ready = woffice_customization_get_array('colored_color_important');
	$css .= $colored_color_important_ready . '{';
	$css .= 'color: ' . esc_html($color_colored) . ' !important;';
	$css .= '}';
	$colored_color_background_ready = woffice_customization_get_array('colored_color_background');
	$css .= $colored_color_background_ready . '{';
	$css .= 'background-color: ' . esc_html($color_colored) . ';';
	$css .= '}';
	$css .= '.gantt::-webkit-scrollbar{';
	$css .= 'background-color: ' . esc_html($color_colored) . ' !important;';
	$css .= '}';

	/*FIREFOX FIX*/
	$colored_background_important_ready = woffice_customization_get_array('colored_background_important');
	$css .= $colored_background_important_ready . '{';
	//$css .= 'background-color: '.esc_html($color_colored).' !important;';
	$css .= 'background-color: ' . esc_html($color_colored) . ' !important;';
	$css .= '}';

	$colored_border_important_ready = woffice_customization_get_array('colored_border_important');
	$css .= $colored_border_important_ready . '{';
	$css .= 'border-color: ' . esc_html($color_colored) . ' !important';
	$css .= '}';
	$color_text_color_ready = woffice_customization_get_array('color_text_color');
	$css .= '.woocommerce #content-container div.product p.price .amount:after, .it-exchange-product-price ins:after{border-right-color:' . esc_html($color_colored) . '}';
	$css .= $color_text_color_ready . '{';
	$css .= 'color: ' . esc_html($color_text) . ';';
	$css .= '}';
	$css .= '#buddypress div#message,#bp-uploader-warning{';
	$css .= 'background-color: ' . esc_html($color_text) . ';';
	$css .= '}';
	$css .= '#left-content, #user-sidebar, #main-content, body.is-blank-page {';
	$css .= 'background: ' . esc_html($color_main_bg) . ';';
	$css .= '}';
	$css .= '.box .intern-padding h1,.box .intern-padding h2,.box .intern-padding h3,.box .intern-padding h4,.box .intern-padding h5,.box .intern-padding h6{';
	$css .= 'color: ' . $headline_color . ';';
	$css .= '}';
	$color_light1_color_ready = woffice_customization_get_array('color_light1_color');
	$css .= $color_light1_color_ready . '{';
	$css .= 'background: ' . esc_html($color_light1) . ';';
	$css .= '}';

	$color_light1_border_ready = woffice_customization_get_array('color_light1_border');
	$css .= $color_light1_border_ready . '{';
	$css .= 'border-color: ' . esc_html($color_light1) . ';';
	$css .= '}';
	$css .= '.it-exchange-super-widget .it-exchange-sw-product, .it-exchange-super-widget .it-exchange-sw-processing, .it-exchange-product-price, .it-exchange-super-widget .cart-items-wrapper .cart-item, .it-exchange-super-widget .payment-methods-wrapper, .it-exchange-account .it-exchange-customer-menu, #it-exchange-purchases .it-exchange-purchase, #it-exchange-downloads .it-exchange-download-wrapper {';
	$css .= 'border-color: ' . esc_html($color_light1) . ' !important;';
	$css .= '}';


	$css .= '#content-container .bp_members #buddypress #item-nav.intern-box div.item-list-tabs ul li a,#content-container .bp_group #buddypress #item-nav.intern-box div.item-list-tabs ul li a{border-top-color: ' . esc_html($color_light1) . ';border-right-color: ' . esc_html($color_light1) . ';border-bottom-color: ' . esc_html($color_light1) . ';}';
	$css .= '#buddypress .rtm-like-comments-info:after{border-bottom-color: ' . esc_html($color_light1) . ';}';

	$css .= '.wcContainer .wcMessage .wcMessageContent:before{color: ' . esc_html($color_light1) . ';}';

	$color_light2_background_ready = woffice_customization_get_array('color_light2_background');
	$css .= $color_light2_background_ready . '{';
	$css .= 'background: ' . esc_html($color_light2) . ';';
	$css .= '}';
	$css .= '#buddypress .activity-list .activity-content::before{';
	$css .= 'color: ' . $color_light2 . ';';
	$css .= '}';

	$color_light3_array_ready = woffice_customization_get_array('color_light3_color', 'color_light3_array');
	$css .= $color_light3_array_ready . '{';
	$css .= 'color: ' . esc_html($color_light3) . ';';
	$css .= '}';
	$color_light3_array_important_ready = woffice_customization_get_array('color_light3_color_important');
	$css .= $color_light3_array_important_ready . '{';
	$css .= 'color: ' . esc_html($color_light3) . ' !important;';
	$css .= '}';
	$color_light3_border_ready = woffice_customization_get_array('color_light3_border');
	$css .= $color_light3_border_ready . '{';
	$css .= 'border-color: ' . esc_html($color_light3) . ';';
	$css .= '}';
	$color_notifications_array_ready = woffice_customization_get_array('color_notification', 'color_notifications_array');
	$css .= $color_notifications_array_ready . '{background: ' . esc_html($color_notifications) . ' !important;}';
	$color_notifications_array_green_ready = woffice_customization_get_array('color_notification_green', 'color_notifications_array_green');
	$css .= $color_notifications_array_green_ready . '{background: ' . esc_html($color_notifications_green) . ' !important;}';
	$css .= '.assigned-tasks-empty i.fa,.woffice-poll-ajax-reply.sent i.fa{color: ' . esc_html($color_notifications_green) . ' !important;}';


	/*---------------------------------------------------------
	**
	** MENU SETTINGS
	**
	----------------------------------------------------------*/
	$menu_background = woffice_get_settings_option('menu_background');
	$menu_width      = woffice_get_settings_option('menu_width');
	$menu_color2     = woffice_get_settings_option('menu_color2');
	$menu_hover      = woffice_get_settings_option('menu_hover');
	$menu_width      = (int) esc_html($menu_width);
	$menu_background = esc_html($menu_background);

	// Before the version 2.5.8 it was (2 * $menu_width > 200) ? 200 : (2 * $menu_width);
	$calculated_menu_width = $menu_width;

	$css .= '#navigation{width: ' . $menu_width . 'px;background: ' . $menu_background . ';}';
	$css .= '.main-menu ul.sub-menu li a, .main-menu ul.sub-menu li.current-menu-item a{background: ' . $menu_background . '}';
	$css .= 'body.menu-is-vertical #navigation.navigation-hidden{left: -' . $menu_width . 'px;}';
	$css .= 'body.rtl #navigation.navigation-hidden{left: auto; right: -' . $menu_width . 'px;}';
	$css .= '.main-menu{max-width: ' . $menu_width . 'px;}';
	$css .= '.main-menu ul.sub-menu{left: -' . (2 * $menu_width) . 'px;}';
	$css .= '.main-menu ul.sub-menu.display-submenu,.main-menu .mega-menu.open{left: ' . $menu_width . 'px;}';
	$css .= '.main-menu ul.sub-menu li a{width: ' . (2 * $menu_width) . 'px;}';
	/*THIRD LEVEL SUPPORT*/
	$css .= '.main-menu ul.sub-menu.display-submenu ul.sub-menu.display-submenu{left: ' . (2 * $menu_width) . 'px;}';
	$css .= 'body.rtl.menu-is-vertical .main-menu ul.sub-menu, body.rtl.menu-is-vertical.main-menu .mega-menu.open{left: auto; right: ' . ($menu_width) . 'px !important;}';
	$css .= 'body.rtl.menu-is-vertical .main-menu ul.sub-menu ul.sub-menu{left: auto; right: ' . (2 * $menu_width) . 'px !important;}';
	$css .= '@media only screen and (min-width: 993px){';
	$css .= 'body.rtl.menu-is-vertical .main-menu ul.sub-menu li:hover> .sub-menu{left: auto !important; right: ' . (2 * $menu_width) . 'px !important;}';
	$css .= '}';


	$css .= '.main-menu ul.sub-menu li a:hover,.main-menu li > a:hover, .main-menu li.current-menu-item a, .main-menu li.current_page_item a{ background: ' . $menu_hover . ';}';

	$css .= '.main-menu li > a, .main-menu ul.sub-menu li a:hover,.main-menu li > a:hover, .main-menu li.current-menu-item a, .main-menu li.current_page_item a{ color: ' . esc_html($menu_color2) . ';}';
	$css .= '.main-menu li > a { border-color: rgba(0,0,0,.15);}';

	// LAYOUT
	$css .= '#main-content:not(.navigation-hidden), #main-header:not(.navigation-hidden), #main-footer:not(.navigation-hidden){padding-left: ' . $menu_width . 'px;}';
	$css .= 'body.rtl.menu-is-vertical #main-content:not(.navigation-hidden), body.rtl.menu-is-vertical #main-header:not(.navigation-hidden), body.rtl.menu-is-vertical #main-footer:not(.navigation-hidden){ padding-right: ' . $menu_width . 'px;}';

	$css .= 'body.menu-is-vertical #main-header:not(.navigation-hidden) #nav-left{ padding-left: ' . $menu_width . 'px;}';
	$css .= 'body.rtl.menu-is-vertical #main-header:not(.navigation-hidden) #nav-left{ padding-left: 0; padding-right: ' . $menu_width . 'px;}';

	$css .= 'body.rtl.menu-is-vertical #navbar.navigation-fixed{padding-left: 0; padding-right: ' . $menu_width . 'px;}';
	$css .= 'body.menu-is-vertical #main-content:not(.navigation-hidden) #left-content { width: calc( 75% - ' . $menu_width/4 . 'px);}';

	//MOBILE CHANGES SINCE 1.4.3
	$css .= '@media only screen and (max-width: 992px) {';
	$css .= '#navigation{width: ' . $calculated_menu_width . 'px;}';
	$css .= '.main-menu{max-width: ' . $calculated_menu_width . 'px;}';
	$css .= 'body.menu-is-vertical #navigation.navigation-hidden{left: -' . $calculated_menu_width . 'px;}';
	$css .= 'body.rtl #navigation.navigation-hidden{left: auto; right: -' . $calculated_menu_width . 'px;}';
	$css .= '}';

	/*---------------------------------------------------------
	**
	** HEADER SETTINGS
	**
	----------------------------------------------------------*/
	$header_height = woffice_get_settings_option('header_height');
	$header_logo_bg = woffice_get_settings_option('header_logo_bg');
	$header_width = woffice_get_settings_option('header_width');
	$header_color = woffice_get_settings_option('header_color');
	$header_link = woffice_get_settings_option('header_link');
	$header_background = woffice_get_settings_option('header_background');
	$header_link_hover = woffice_get_settings_option('header_link_hover');
	$header_bold = woffice_get_settings_option('header_bold');
	$header_user = woffice_get_settings_option('header_user');
	$css .= '#nav-logo{width: ' . esc_html($header_width) . 'px;}';
	/*Horizontal Menu*/
	$css .= 'body.menu-is-horizontal #navigation{top: ' . ($header_height + 2) . 'px ;}';
	$css .= 'body.menu-is-horizontal.admin-bar #navigation{top: ' . ($header_height + 32) . 'px;}';

	$css .= '@media only screen and (max-width: 783px) {';
	$css .= 'body.menu-is-horizontal.admin-bar #navigation{top: ' . ($header_height + 46) . 'px;}';
	$css .= '}';

	/*End*/
	$css .= '#main-header { padding-top: ' . $header_height . 'px }';
	$css .= '#navbar{ height: ' . esc_html($header_height) . 'px; line-height: ' . esc_html($header_height) . 'px; background-color: ' . esc_html($header_background) . '; }';
	$css .= '#navbar #nav-user a#user-thumb {';
	$css .= 'color: ' . esc_html($header_color) . ';';
	$css .= '}';
	$css .= '#nav-left{height: ' . esc_html($header_height) . 'px;}';
	$css .= 'a#nav-trigger, #nav-buttons a{color: ' . esc_html($header_link) . ';}';
	$css .= 'a#nav-trigger:hover,#nav-buttons a:hover {color: ' . esc_html($header_link) . ';}';
	/*Fix for the searchform on mobile - Added in 1.4.2 */
	$css .= '@media only screen and (max-width: 600px) {#main-search .container{padding-top: ' . esc_html($header_height) . 'px;}}';
	$css .= '@media only screen and (max-width: 450px) {#navigation{top: ' . esc_html($header_height) . 'px;}.logged-in.admin-bar #navigation{top: ' . ($header_height + 45) . 'px;}}';

	/*WE PICK THE COLOR FROM THE MENU (not fair)*/
	$css .= '#nav-user{';
	$css .= 'color: ' . esc_html($menu_background) . ';';
	$css .= '}';
	/*---------------------------------------------------------
	**
	** PAGE TITLE SETTINGS
	**
	----------------------------------------------------------*/
	$main_featured_image = woffice_get_settings_option('main_featured_image');
	$main_featured_height = woffice_get_settings_option('main_featured_height');
	$main_featured_font_size = woffice_get_settings_option('main_featured_font_size');
	$main_featured_uppercase = woffice_get_settings_option('main_featured_uppercase');
	$main_featured_color = woffice_get_settings_option('main_featured_color');
	$main_featured_opacity = woffice_get_settings_option('main_featured_opacity');
	$main_featured_bg = woffice_get_settings_option('main_featured_bg');
	$main_featured_border = woffice_get_settings_option('main_featured_border');
	$main_featured_border_color = woffice_get_settings_option('main_featured_border_color');
	$main_featured_alignment = woffice_get_settings_option('main_featured_alignment');
	$main_featured_bold = woffice_get_settings_option('main_featured_bold');
	$main_featured_height = (int) esc_html($main_featured_height);
	if ($main_featured_border == "yep") :
		$css .= '#featuredbox{';
		$css .= 'border-color: ' . esc_html($main_featured_border_color) . ' !important;';
		$css .= 'border-bottom: 6px solid;';
		$css .= '}';
	endif;
	$css .= '#featuredbox .pagetitle, #featuredbox .pagetitle h1{';
	$css .= 'color: ' . $main_featured_color . ';';
	$css .= '}';
	$css .= '#featuredbox.centered .pagetitle > h1{';
	if ($main_featured_uppercase == true) :
		$css .= 'text-transform: uppercase;';
	else:
		$css .= 'text-transform: none;';
	endif;
	$css .= ($main_featured_bold == true) ? 'font-weight: bold;' : 'font-weight: 200;';
	$css .= (!empty($main_featured_font_size)) ? 'font-size: ' . $main_featured_font_size . 'px;' : 'font-size: 4em;';
	$css .= '}';
	$css .= '#featuredbox .pagetitle{';
	$css .= 'height: ' . ($main_featured_height - 44) . 'px;';
	$css .= '}';
	$css .= '#featuredbox.has-search .featured-background,#featuredbox.has-search .pagetitle{';
	$css .= 'height: ' . ($main_featured_height + 50) . 'px;';
	$css .= '}';
	$css .= '#featuredbox .featured-background{';
	$css .= 'height: ' . $main_featured_height . 'px;';
	$css .= '}';
	$css .= '.featured-layer{';
	$css .= 'background-color: ' . $main_featured_bg . ';';
	$css .= 'opacity: ' . esc_html($main_featured_opacity) . ';';
	$css .= '}';
	$css .= '#featuredbox .featured-background{';
	$css .= 'background-position: ' . $main_featured_alignment . ' center;';
	$css .= '}';
	if (!empty($main_featured_font_size)) {
		$css .= '@media only screen and (max-width: 600px) {';
		$css .= 'body #featuredbox .pagetitle > h1, #featuredbox.has-search.is-404 .pagetitle > h1, #featuredbox.has-search.search-buddypress .pagetitle > h1 {';
		$css .= 'font-size: ' . round($main_featured_font_size / 2) . 'px !important;';
		$css .= '}';
		$css .= '}';
	}
	/*---------------------------------------------------------
	**
	** WOFFICE 2.0 changes
	**
	----------------------------------------------------------*/
	$design_update = woffice_get_settings_option('design_update');
	if ($design_update == "2.X") {
		$css .= 'body.woffice-2-x .featured-layer{';
		$css .= 'background: ' . $main_featured_bg . ';';
		$css .= 'background: -webkit-linear-gradient(-30deg, ' . woffice_get_adjust_brightness($main_featured_bg, -80) . ' , ' . woffice_get_adjust_brightness($main_featured_bg, 20) . ');';
		$css .= 'background: linear-gradient(-30deg, ' . woffice_get_adjust_brightness($main_featured_bg, -80) . ' , ' . woffice_get_adjust_brightness($main_featured_bg, 20) . ');';
		$css .= '}';
		$css .= 'body.woffice-2-x .progress-bar,
		 body.woffice-2-x .btn.btn-default:not(.btn-shortcode){';
		$css .= 'background: ' . $color_colored . ';';
		$css .= 'background: -webkit-linear-gradient(-30deg, ' . woffice_get_adjust_brightness($color_colored, -10) . ' , ' . woffice_get_adjust_brightness($color_colored, 10) . ');';
		$css .= 'background: linear-gradient(-30deg, ' . woffice_get_adjust_brightness($color_colored, -10) . ' , ' . woffice_get_adjust_brightness($color_colored, 10) . ') !important;';
		$css .= '}';
        $css .= 'body.woffice-2-x .main-menu ul.sub-menu li > a:hover,body.woffice-2-x .main-menu li > a:hover,
		 body.woffice-2-x .main-menu li.current-menu-item > a, .main-menu li.current_page_item > a,
		 .main-menu li.current_page_ancestor > a{';
        $css .= 'background: ' . $menu_hover . ';';
        $css .= 'background: -webkit-linear-gradient(-30deg, ' . woffice_get_adjust_brightness($menu_hover, -10) . ' , ' . woffice_get_adjust_brightness($menu_hover, 10) . ');';
        $css .= 'background: linear-gradient(-30deg, ' . woffice_get_adjust_brightness($menu_hover, -10) . ' , ' . woffice_get_adjust_brightness($menu_hover, 10) . ') !important;';
        $css .= '}';
	}
	/*---------------------------------------------------------
	**
	** FOOTER & EXTRA FOOTER SETTINGS
	**
	----------------------------------------------------------*/
	$footer_color 				= woffice_get_settings_option('footer_color');
	$footer_link 				= woffice_get_settings_option('footer_link');
	$footer_background 			= woffice_get_settings_option('footer_background');
	$footer_copyright_background= woffice_get_settings_option('footer_copyright_background');
	$footer_border_color		= woffice_get_settings_option('footer_border_color');
	$extrafooter_border_color   = woffice_get_settings_option('extrafooter_border_color');
	$footer_copyright_uppercase   = woffice_get_settings_option('footer_copyright_uppercase');

	$css .= '#widgets{';
		$css .= 'background-color: '.esc_html($footer_background).';';
		$css .= 'color: '.esc_html($footer_color).';';
	$css .= '}';
	$css .= '#copyright{';
		$css .= 'background-color: '.esc_html($footer_copyright_background).';';
		$css .= 'border-color: '.esc_html($footer_border_color).';';
	$css .= '}';
	$css .= '#copyright p, #widgets p{';
		$css .= 'color: '.esc_html($footer_color).';';
	$css .= '}';
	if (!empty($footer_copyright_uppercase)){
		$css .= '#copyright p{';
			$css .= 'text-transform: uppercase;';
		$css .= '}';
	}
	$css .= '#widgets .widget{';
		$css .= 'border-color: '.esc_html($color_text).';';
	$css .= '}';
	$css .= '#widgets h3:after, #widgets .widget.widget_search button{';
		$css .= 'background-color: '.esc_html($footer_link).';';
	$css .= '}';
	$css .= '#copyright a, #widgets a, #extrafooter-layer h1 span{';
		$css .= 'color: '.esc_html($footer_link).';';
	$css .= '}';
	$css .= '#extrafooter{';
		$css .= 'border-color: '.esc_html($extrafooter_border_color).';';
	$css .= '}';
	/* /!\ NEED CHANGE */
	$css .= '#widgets .widget{';
		$css .= 'border-color: '.esc_html($color_text).';';
	$css .= '}';
	$css .= '#extrafooter-layer{';
		$css .= 'background: rgba(0,0,0,.5);';
	$css .= '}';

	/*---------------------------------------------------------
	**
	** SIDEBAR SETTINGS
	**
	----------------------------------------------------------*/
	$sidebar_mobile 			= woffice_get_settings_option('sidebar_mobile');
	$sidebar_min 	     		= woffice_get_settings_option('sidebar_min');
	//$css .= '#right-sidebar{ min-height: '.esc_html($sidebar_min).'px;}';
	$css .= ' @media only screen and (max-width: 992px) {';
		$css .= '#nav-sidebar-trigger, #right-sidebar {';
			if( $sidebar_mobile == "yep" )
				$css .= "display: table-cell !important;";
			else
				$css .= "display: none !important;";
		$css .= '}}';

	/*---------------------------------------------------------
	**
	** LOGIN PAGE SETTINGS
	**
	----------------------------------------------------------*/
	$login_background_color 	= woffice_get_settings_option('login_background_color');
	$login_background_image 	= woffice_get_settings_option('login_background_image');
	$login_background_opacity 	= woffice_get_settings_option('login_background_opacity');
	//$login_logo_image 			= woffice_get_settings_option('login_logo_image');
	$login_logo_image_width 	= woffice_get_settings_option('login_logo_image_width');
	//$login_logo_image_height 	= woffice_get_settings_option('login_logo_image_height');

	if ( woffice_is_custom_login_page_enabled() ) :
		$css .= '#woffice-login{';
			$css .= 'background-color: '.esc_html($login_background_color).';';
		$css .= '}';

		$css .= '#woffice-login-left{';
			if (!empty($login_background_image)):
				$css .= "background-image: url(".esc_url($login_background_image["url"]).");";
			else :
				$css .= "background-image: url(".get_template_directory_uri() ."/images/1.jpg);";
			endif;
			$css .= "background-repeat: no-repeat;";
			$css .= "
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;";
			$css .= "background-position: center top;";
			$css .= "opacity: ".esc_html($login_background_opacity).";";
			$css .= '}';

		/*if (!empty($login_logo_image)):
			echo "#login-logo{";
				echo "background-image: url(".esc_url($login_logo_image["url"]).");";
				echo "background-size: ".$login_logo_image_width."px ".$login_logo_image_height."px;";
				echo "width: ".esc_html($login_logo_image_width)."px;";
				echo "height: ".esc_html($login_logo_image_height)."px;";
			echo "}";
		endif;*/

		if (!empty($login_logo_image_width)) {
			$css .= '#login-logo img {';
			$css .= '  width: '.intval($login_logo_image_width).'px;';
			$css .= '}';
		}


	endif;

	/*---------------------------------------------------------
	**
	** PAGE LOADING OPTION
	**
	----------------------------------------------------------*/
	$page_loading 				= woffice_get_settings_option('page_loading');
	if ($page_loading == "no") :
		$css .= ".pace {display: none !important;}";
	endif;

	/*---------------------------------------------------------
	**
	** REMOVE BORDER RADIUS OPTION
	**
	----------------------------------------------------------*/
	$remove_radius 				= woffice_get_settings_option('remove_radius');
	if ($remove_radius == true) :
		$border_radius_ready = woffice_customization_get_array('border_radius');
		$css .= $border_radius_ready."{border-radius:0!important}";
	endif;

	/*---------------------------------------------------------
	**
	** CUSTOM CSS
	**
	----------------------------------------------------------*/
	$custom_css 				= woffice_get_settings_option('custom_css');
	$css .= $custom_css;


	return $css;


}

/**
 * Include a Woffice CSS customization file
 *
 * @param $file_name - ie "color_notifications"
 * @param $array_name - ie "color_notifications"
 *
 * @return string
 */
function woffice_customization_get_array($file_name, $array_name = '') {

	require_once 'css_arrays/'.$file_name.'.php';

	if (empty($array_name))
		$array_name = $file_name;

	$values = (isset(${$array_name})) ? ${$array_name} : array();

	/**
	 * Filter to customize the dynamic CSS arrays in Woffice
	 *
	 * @param $values array
	 *
	 * @param $file_name string
	 *
	 * @return array
	 */
	$values = apply_filters('woffice_customization_array', $values, $file_name);

	return implode(", ", $values);

}

function woffice_save_custom_css() {
	update_option('woffice_custom_css', woffice_get_custom_css());
}
add_action('fw_settings_form_saved', 'woffice_save_custom_css');

function woffice_custom_css_header() {
	echo '<!-- Custom CSS from Woffice -->';
	echo '<style type="text/css">';
		$custom_css = get_option('woffice_custom_css');
		if (!empty($custom_css) && WP_DEBUG === false) {
			echo '/*FROM : Database options*/';
			echo $custom_css;
		} else {
			echo '/*FROM : Dynamical load*/';
			echo woffice_get_custom_css();
		}
	echo '</style>';
}
add_action( 'wp_head', 'woffice_custom_css_header' );

/**
* We output the Custom JS set in the theme setiings in the footer
*
*/
function woffice_custom_js() {
	$custom_js = woffice_get_settings_option( 'custom_js' );
	if ( ! empty( $custom_js ) ) {
		echo '<script type="text/javascript">';
		echo 'jQuery(document).ready(function() {';
		echo $custom_js;
		echo '});';
		echo '</script>';
	}

	$footer_scripts = woffice_get_settings_option( 'footer_scripts' );
	if ( ! empty( $footer_scripts ) ) {
		echo $footer_scripts;
	}
}
add_action( 'wp_footer', 'woffice_custom_js' );

/**
* Register Fonts.
*
* @return string|null
*/
function woffice_get_fonts_url() {

    $fonts_url = null;

    // Get the fonts used in the theme
	$font_main_typography = woffice_get_settings_option('font_main_typography'); 
	$font_headline_typography = woffice_get_settings_option('font_headline_typography'); 
	$font_extentedlatin = woffice_get_settings_option('font_extentedlatin');

	$system_fonts = array(
		"arial",
		"verdana",
		"trebuchet",
		"georgia",
		"times-new-roman",
		"tahoma",
		"palatino",
		"helvetica",
		"calibri",
		"myriad-pro",
		"lucida",
		"arial-black",
		"gill-sans",
		"geneva",
		"impact",
		"serif"
	);

	if (empty($font_main_typography) || !isset($font_main_typography['family']) || empty($font_headline_typography) || !isset($font_headline_typography['family']))
		return $fonts_url;

	$main_font = '';
	if(!in_array(sanitize_title($font_main_typography['family']), $system_fonts)) {
		$main_font = $font_main_typography['family'].":100,200,300,400,400italic,600,700italic,800,900";
	}

	$second_font = '';
	if(!in_array(sanitize_title($font_headline_typography['family']), $system_fonts)) {
		$second_font = $font_headline_typography['family'].":100,200,300,400,400italic,600,700italic,800,900";
	}

	if(empty($main_font) && empty($second_font))
		return $fonts_url;

	$subset = ($font_extentedlatin == "yep") ? '&subset=latin,latin-ext' : '';

	$query_args = 'family=';

	if(!empty($main_font))
		$query_args .= $main_font;

	if(!empty($second_font))
		$query_args .= '|'. $second_font;

	$query_args .= $subset;

	return '//fonts.googleapis.com/css?'.preg_replace("/ /","+",$query_args);

}