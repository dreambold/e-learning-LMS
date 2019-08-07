<?php
/**
 * Class Woffice_Setup
 *
 * Load everything related to the theme setup for WordPress
 * Also includes filters and actions related to Unyson
 *
 * @since 2.1.3
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_Setup' ) ) {
    class Woffice_Setup
    {
        /**
         * Woffice_Setup constructor
         */
        public function __construct()
        {
            add_action('init', array($this, 'action_theme_setup'));
            add_action('after_setup_theme', array($this, 'theme_support'));
            if (!function_exists('_wp_render_title_tag')) {
                add_action('wp_head', array($this, 'theme_slug_render_title'));
            }
            add_action('admin_bar_menu', array($this, 'toolbar_admin_menu'), 999);
            add_filter('update_footer', array($this, 'filter_footer_version'), 12);
            add_action('admin_enqueue_scripts', array($this, 'settings_wp_admin_style'), 99);
            add_action('admin_print_scripts', array($this, 'backend_style_patch'));
            add_filter('fw:ext:backups-demo:demos', array($this, 'filter_theme_fw_ext_backups_demos'));
            add_action('fw_settings_form_saved', array($this, 'create_json_manifest'));
            add_filter('get_search_form', array($this, 'search_form'));
            add_action('widgets_init', array($this, 'sidebars'));
            add_action('admin_print_footer_scripts', array($this, 'add_quicktags'));
	        add_action('admin_notices', array($this, 'check_core'));
        }

	    /**
	     * Check that the Woffice Core plugin is enabled
	     */
        public function check_core()
        {
            if (defined('WOFFICE_CORE_ENABLED')) {
                return;
            }

            $url = admin_url('themes.php?page=tgmpa-install-plugins');

	        ?>
            <div class="error notice">
                <p><?php echo sprintf(__( 'You are using Woffice without the <b>Woffice Core</b> plugin. <a href="%s">Please activate it to enjoy Woffice\'s features</a>.', 'woffice' ), $url); ?></p>
            </div>
	        <?php
        }

        /**
         * Basic features for the WP side
         * Like Theme support, language domain, image size
         */
        public function action_theme_setup()
        {
            /*
             * Make Theme available for translation.
             */
            load_theme_textdomain('woffice', get_template_directory() . '/languages');

            // This theme uses its own gallery styles.
            add_filter('use_default_gallery_style', function () {
                return false;
            });
        }

        /**
         * Page title for WordPress 4.1 and any higher version
         */
        public function theme_support()
        {
            add_theme_support('title-tag');

	        add_theme_support('buddypress-use-nouveau');

	        // Add RSS feed links to <head> for posts and comments.
	        add_theme_support('automatic-feed-links');

	        // Enable support for Post Thumbnails, and declare two sizes.
	        add_theme_support('post-thumbnails');
	        set_post_thumbnail_size(800, 600, true);
	        add_image_size('fw-theme-full-width', 1038, 576, true);

	        if (!isset($content_width))
	            $content_width = 900;

	        /*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
	        add_theme_support('html5', array(
		        'search-form',
		        'comment-form',
		        'comment-list',
		        'gallery',
		        'caption'
	        ));

	        /* Woocommerce Support since @1.2.0 */
	        add_theme_support('woocommerce');
	        add_theme_support('wc-product-gallery-slider');

	        /**
	         * Disable the Zoom feature on Woocommerce product images
	         *
	         * @param bool
	         *
	         */
	        if (apply_filters( 'woffice_woocommerce_gallery_zoom_enabled', true))
		        add_theme_support('wc-product-gallery-zoom');

	        /**
	         * Disable the Lightbox feature on Woocommerce product images
	         *
	         * @param bool
	         *
	         */
	        if (apply_filters( 'woffice_woocommerce_gallery_lightbox_enabled', true))
		        add_theme_support('wc-product-gallery-lightbox');
        }

        /**
         * Renders title before WordPress 4.1
         *
         * @return void
         */
        public function theme_slug_render_title()
        {
            ?>
            <title><?php wp_title('|', true, 'right'); ?></title>
            <?php
        }

        /**
         * Creates the Woffice admin menu in the top bar
         *
         * @param $wp_admin_bar
         */
        public function toolbar_admin_menu($wp_admin_bar)
        {

            /*DOC LINK*/
            $topbar_woffice = woffice_get_settings_option('topbar_woffice');

            if (current_user_can('administrator') && $topbar_woffice == "yep") {
                // Main Link
                $wp_admin_bar->add_node(array(
	                'id' => 'woffice',
	                'title' => 'Woffice',
	                'href' => admin_url('themes.php?page=fw-settings'),
	                'meta' => array('class' => 'woffice_page')
                ));

	            $wp_admin_bar->add_node(array(
		            'id' => 'woffice_settings',
		            'title' => 'Theme Settings',
		            'parent' => 'woffice',
		            'href' => admin_url('themes.php?page=fw-settings'),
		            'meta' => array('class' => 'woffice-theme-settings')
	            ));

                $settings = array(
                    'general' => __('General', 'woffice'),
                    'chat' => __('Live chat', 'woffice'),
                    'permissions' => __('Permissions', 'woffice'),
                    'login' => __('Login/Register', 'woffice'),
                    'dashboard' => __('Dashboard', 'woffice'),
                    'buddypress' => __('BuddyPress', 'woffice'),
                    'posts' => __('Posts/Wiki/Projects', 'woffice'),
                    'news' => __('Blog', 'woffice'),
                    'menu' => __('Menu', 'woffice'),
                    'header' => __('Header bar', 'woffice'),
                    'page-title' => __('Page title', 'woffice'),
                    'sidebar' => __('Sidebar', 'woffice'),
                    'footer' => __('Footer & Extrafooter', 'woffice'),
                    'styling' => __('Styling', 'woffice'),
                    'custom' => __('Custom code', 'woffice'),
                    'settings' => __('System status', 'woffice'),
                );

	            foreach ($settings as $tab_key=>$tab_label) {
		            $wp_admin_bar->add_node(array(
			            'id' => 'woffice_settings_'. $tab_key,
			            'title' => $tab_label,
			            'parent' => 'woffice_settings',
			            'href' => admin_url('themes.php?page=fw-settings#fw-options-tab-'. $tab_key),
			            'meta' => array('class' => 'woffice-theme-settings-'. $tab_key)
		            ));
                }

                $wp_admin_bar->add_node(array(
	                'id' => 'woffice_doc',
	                'title' => 'Online Documentation',
	                'parent' => 'woffice',
	                'href' => 'https://alkaweb.atlassian.net/wiki/spaces/WOF/overview',
	                'meta' => array('class' => 'woffice-documentation-page')
                ));
                $wp_admin_bar->add_node(array(
	                'id' => 'woffice_extensions',
	                'title' => 'Extensions',
	                'parent' => 'woffice',
	                'href' => admin_url('index.php?page=fw-extensions'),
	                'meta' => array('class' => 'woffice-extension-page')
                ));
                $wp_admin_bar->add_node(array(
	                'id' => 'woffice_welcome',
	                'title' => 'Getting Started',
	                'parent' => 'woffice',
	                'href' => admin_url('index.php?page=woffice-welcome'),
	                'meta' => array('class' => 'woffice-welcome-page')
                ));
                $wp_admin_bar->add_node(array(
	                'id' => 'woffice_support',
	                'title' => 'Support',
	                'parent' => 'woffice',
	                'href' => 'https://alkaweb.ticksy.com/',
	                'meta' => array('class' => 'woffice-support-page')
                ));
	            $wp_admin_bar->add_node(array(
		            'id' => 'woffice_plugins',
		            'title' => 'Download bundled plugins',
		            'parent' => 'woffice',
		            'href' => '#',
		            'meta' => array('class' => 'woffice-plugin-page')
	            ));

	            $bundled = array(
		            'revslider',
//		            'dpProEventCalendar',
		            'js_composer',
		            'vc-super-bundle',
		            'eventON',
		            'eventon-full-cal',
		            'multiverso',
	            );

	            foreach ($bundled as $slug) {
	                $plugin_info = woffice_core_bundled_plugin($slug);

		            $wp_admin_bar->add_node(array(
			            'id' => 'woffice_plugins_'. $slug,
			            'title' => $plugin_info['name'],
			            'parent' => 'woffice_plugins',
			            'href' => $plugin_info['source'],
			            'meta' => array('class' => 'woffice-theme-plugins-'. $slug)
		            ));
	            }

                $wp_admin_bar->add_node(array(
	                'id' => 'woffice_changelog',
	                'title' => 'Changelog',
	                'parent' => 'woffice',
	                'href' => 'https://hub.alka-web.com/woffice/changelog/',
	                'meta' => array('class' => 'woffice-changelog-page')
                ));
	            $wp_admin_bar->add_node(array(
		            'id' => 'woffice_pro',
		            'title' => 'Pro account (feature request, videos...)',
		            'parent' => 'woffice',
		            'href' => 'https://hub.alka-web.com/pro',
		            'meta' => array('class' => 'woffice-pro-page')
	            ));
            }
        }

        /**
         * Adding the version number to the footer
         *
         * @param $html
         * @return string
         */
        public function filter_footer_version($html)
        {
            if ((current_user_can('update_themes') || current_user_can('update_plugins')) && defined("FW")) {
                return (empty($html) ? '' : $html . ' | ') . fw()->theme->manifest->get('name') . ' ' . fw()->theme->manifest->get('version');
            } else {
                return $html;
            }
        }

        /**
         * Custom changes to the backend CSS
         * Actually only the Theme Settings for now
         */
        public function settings_wp_admin_style()
        {
            wp_register_style('woffice_wp_admin_css', get_template_directory_uri() . '/css/backend.min.css', false, '1.0.0');
            wp_enqueue_style('woffice_wp_admin_css');

	        wp_register_style('woffice_theme_fonts', woffice_get_fonts_url(), false, '1.0.0');
	        wp_enqueue_style('woffice_theme_fonts');
        }

        /**
         * Quick CSS patch for Unyson
         *
         * @return void
         */
        public function backend_style_patch()
        {
            if (function_exists('fw_current_screen_match')) {
                if (fw_current_screen_match(array('only' => array('id' => 'toplevel_page_fw-extensions')))) {

                    ?>
                    <style type="text/css">
                        #fw-ext-events,
                        #fw-ext-translation,
                        #fw-ext-feedback, #fw-ext-styling
                        { display: none !important; }
                    </style>
                    <?php
                }
            }

            ?>
            <style type="text/css">
                #fw-option-login_layout.fw-option-type-image-picker ul.thumbnails.image_picker_selector li .thumbnail img,
                {width: 250px; height: auto}
                .fw-brz-dismiss{display: none;}
            </style>
            <?php

        }

        /**
         * Creating the demos from the Unyson Backups extension
         *
         * @param $demos
         * @return array
         */
        public function filter_theme_fw_ext_backups_demos($demos)
        {
            $demos_array = array(
                'allinone-demo' => array(
                    'title' => __('All In One Demo', 'woffice'),
                    'screenshot' => 'https://hub.alka-web.com/storage/woffice/demos/demo-allinone.png',
                    'preview_link' => 'https://demos.alka-web.com/woffice/allinone/',
                ),
                'business-demo' => array(
                    'title' => __('Business Demo', 'woffice'),
                    'screenshot' => 'https://hub.alka-web.com/storage/woffice/demos/demo-business.png',
                    'preview_link' => 'https://demos.alka-web.com/woffice/business/',
                ),
                'community-demo' => array(
                    'title' => __('Community Demo', 'woffice'),
                    'screenshot' => 'https://hub.alka-web.com/storage/woffice/demos/demo-community.png',
                    'preview_link' => 'https://demos.alka-web.com/woffice/community/',
                ),
                'school-demo' => array(
                    'title' => __('School Demo', 'woffice'),
                    'screenshot' => 'https://hub.alka-web.com/storage/woffice/demos/demo-school.png',
                    'preview_link' => 'https://demos.alka-web.com/woffice/school/',
                ),
            );

            /**
             * Enable the hub's endpoint
             *
             * @param bool
             */
            $enable_hub = apply_filters('woffice_hub_demo_enabled', true);

            if ($enable_hub) {

                $download_url = 'https://hub.alka-web.com/api/woffice/demos/';

            } else {

                $download_url = 'https://woffice.io/demos/index.php';


            }

            foreach ($demos_array as $id => $data) {
                $demo = new FW_Ext_Backups_Demo($id, 'piecemeal', array(
                    'url' => $download_url,
                    'file_id' => $id,
                ));
                $demo->set_title($data['title']);
                $demo->set_screenshot($data['screenshot']);
                $demo->set_preview_link($data['preview_link']);

                $demos[$demo->get_id()] = $demo;

                unset($demo);
            }

            /**
             * Excludes the All In One demo from Envato Hosted platform
             */

            if ( defined( 'ENVATO_HOSTED_SITE' ) ) {
                unset( $demos['allinone-demo'] );
            }

            return $demos;
        }

        /**
         * Creates a JSON manifest for the Mobile icons
         * Started on every theme settings saving proccess
         */
        public function create_json_manifest()
        {
            $favicon_android_1 = woffice_get_settings_option('favicon_android_1');
            $favicon_android_2 = woffice_get_settings_option('favicon_android_2');
            if (!empty($favicon_android_1)) {
                $size1 = '{"src": "http:' . esc_url($favicon_android_1['url']) . '","sizes": "192x192","type": "image\/png","density": "4.0"}';
                $sizes = $size1;
            }
            if (!empty($favicon_android_2)) {
                $size2 = '{"src": "http:' . esc_url($favicon_android_2['url']) . '","sizes": "144x144","type": "image\/png","density": "3.0"}';
                $sizes = $size2;
                if (!empty($favicon_android_1)) {
                    $sizes = $size2 . ',' . $size1;
                }
            } else {
                $sizes = "";
            }
            $json_content = '{"name": "' . get_bloginfo('name') . '","icons": [' . $sizes . ']}';
            $fp = fopen(get_template_directory() . '/js/manifest.json', 'w');
            fwrite($fp, $json_content);
            fclose($fp);
        }

        /**
         * We re-create the Search form in order to include our own post types to the search page
         *
         * @param string $form - the HTML markup
         * @return string
         */
        public function search_form($form)
        {
            if ( is_page_template("page-templates/wiki.php")
                || isset($_GET['post_type']) && $_GET['post_type'] == 'wiki') {
                $extrafield = '<input type="hidden" name="post_type" value="wiki" />';
            } else if ( is_page_template("page-templates/projects.php")
                        || isset($_GET['post_type']) && $_GET['post_type'] == 'projects' ) {
                $extrafield = '<input type="hidden" name="post_type" value="projects" />';
            } else if ( is_page_template("page-templates/page-directory.php")
                       || isset($_GET['post_type']) && $_GET['post_type'] == 'directory' ) {
                $extrafield = '<input type="hidden" name="post_type" value="directory" />';
            } else {
                $extrafield = '';
            }
            $form = '<form role="search" method="get" action="' . esc_url(home_url('/')) . '" >
                <input type="text" value="' . esc_attr(get_search_query()) . '" name="s" id="s" placeholder="' . __('Search...', 'woffice') . '"/>
                <input type="hidden" name="searchsubmit" id="searchsubmit" value="true" />' . $extrafield . '
                <button type="submit" name="searchsubmit"><i class="fa fa-search"></i></button>
            </form>';
            return $form;
        }

        /**
         * We register all the Woffice sidebars
         */
        public function sidebars()
        {

            register_sidebar(array(
                'name' => __('Right Sidebar', 'woffice'),
                'id' => 'content',
                'description' => __('Appears in the main content, left or right as you like see theme settings. Every widget need a title.', 'woffice'),
                'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="intern-padding">',
                'after_widget' => '</div></div>',
                'before_title' => '<div class="intern-box box-title"><h3>',
                'after_title' => '</h3></div>',
            ));

            register_sidebar(array(
                'name' => __('Dashboard Widgets (Page content)', 'woffice'),
                'id' => 'dashboard',
                'description' => __('Appears in the dashboard page.', 'woffice'),
                'before_widget' => '<div id="%1$s" class="widget box %2$s"><div class="intern-padding">',
                'after_widget' => '</div></div>',
                'before_title' => '<div class="intern-box box-title"><h3>',
                'after_title' => '</h3></div>',
            ));

            register_sidebar(array(
                'name' => __('Footer Widgets', 'woffice'),
                'id' => 'widgets',
                'description' => __('Appears in the footer section of the site.', 'woffice'),
                'before_widget' => '<div id="%1$s" class="widget col-md-3 %2$s animate-me fadeIn">',
                'after_widget' => '</div>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));


        }

        /**
         * Add quicktags to the WP editor
         * Using JS script
         *
         * @return void
         */
        public function add_quicktags()
        {
            if (wp_script_is('quicktags')) {
                ?>
                <script type="text/javascript">
                    QTags.addButton('eg_highlight', 'highlight', '<span class="highlight">', '</span>', 'hightlight', 'Highlight tag', 1);
                    QTags.addButton('eg_label', 'label', '<span class="label">', '</span>', 'label', 'Label tag', 1);
                    QTags.addButton('eg_dropcap', 'dropcap', '<span class="dropcap">', '</span>', 'dropcap', 'Dropcap tag', 1);
                </script>
                <?php
            }
        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Setup();