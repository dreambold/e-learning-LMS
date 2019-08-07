<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

class Theme_Includes
{
	private static $rel_path = null;

	private static $include_isolated_callable;

	private static $initialized = false;

	public static function init()
	{
		if (self::$initialized) {
			return;
		} else {
			self::$initialized = true;
		}

		/**
		 * Include a file isolated, to not have access to current context variables
		 */
		self::$include_isolated_callable = function($path) {
			include $path;
		};

        /**
         * Classes to load :
         */
        $theme_classes = array(
            'Woffice_Setup',
            'Woffice_Security',
            'Woffice_Search',
            'Woffice_Welcome',
            'Woffice_Shortcodes',
            'Woffice_Dashboard',
            'Woffice_Blog',
            'Woffice_Register',
            'Woffice_Frontend',
	        'Woffice_Pending_Registration_Handler',
	        'Woffice_Notification_Handler',
	        'Woffice_Activity_Handler',
            'Woffice_Pro',  
            'Woffice_Alert',
            'Woffice_WooCommerce',
            'Woffice_Google_Signing',
            'Woffice_Facebook_Signing',
            'Woffice_Likes_Fetcher',
            'pro/Woffice_AlkaChat',
            'pro/Woffice_TabsCreator'
        );

		/**
		 * Both frontend and backend
		 */
		{

			/**
			 * Before any class or library is loaded
			 */
            do_action('woffice_before_loaded');

            /*
             * We add the Facebook & Google SDK classes, even if not activated it is not an heavy operation
             * as no method will be triggered
             * This require PHP 5.3 at least
             */
            $load_facebook_files = apply_filters('woffice_load_facebook', true);
            if (class_exists('Woffice_Core') && $load_facebook_files && strnatcmp(phpversion(),'5.3.0') >= 0) {
	            require_once WP_PLUGIN_DIR .'/woffice-core/libs/facebook-sdk/autoload.php';
            }
            $load_google_files = apply_filters('woffice_load_google', true);
            if (class_exists('Woffice_Core') && $load_google_files) {
                require_once WP_PLUGIN_DIR .'/woffice-core/libs/google-sdk/autoload.php';
            }

            self::include_child_first('/plugins.php');
			self::include_child_first('/helpers.php');
			self::include_child_first('/hooks.php');
            self::include_child_first('/customize.php');
            self::include_child_first('/login.php');
            self::include_classes($theme_classes);
            if ( function_exists( 'bp_is_active' ) ) {
                self::include_child_first('/buddypress.php');
            }
			self::include_child_first('/migrations.php');
			self::include_all_child_first('/includes');

			add_action('init', array(__CLASS__, '_action_init'));
			add_action('widgets_init', array(__CLASS__, '_action_widgets_init'));
            
			/**
			 * After any class or library is loaded. We call all the classes overriding those methods,
			 * from the child theme for instance.
			 */
            do_action('woffice_after_loaded');
		}

		/**
		 * Only frontend
		 */
		if (!is_admin()) {
			add_action('wp_enqueue_scripts', array(__CLASS__, '_action_enqueue_scripts'),
				20 // Include later to be able to make wp_dequeue_style|script()
			);
		}
	}

	private static function include_classes($classes_array)
    {
        foreach ($classes_array as $class_name)
        {
            self::include_child_first('/classes/'.$class_name.'.php');
        }
    }

	private static function get_rel_path($append = '')
	{
		if (self::$rel_path === null) {
			self::$rel_path = '/'. basename(dirname(__FILE__));
		}

		return self::$rel_path . $append;
	}

	private static function include_all_child_first($dir_rel_path)
	{
		$paths = array();

		if (is_child_theme()) {
			$paths[] = self::get_child_path($dir_rel_path);
		}

		$paths[] = self::get_parent_path($dir_rel_path);

		foreach ($paths as $path) {
			if ($files = glob($path .'/*.php')) {
				foreach ($files as $file) {
					self::include_isolated($file);
				}
			}
		}
	}

	/**
	 * @param string $dirname 'foo-bar'
	 * @return string 'Foo_Bar'
	 */
	private static function dirname_to_classname($dirname) {
		$class_name = explode('-', $dirname);
		$class_name = array_map('ucfirst', $class_name);
		$class_name = implode('_', $class_name);

		return $class_name;
	}

	public static function get_parent_path($rel_path)
	{
		return get_template_directory() . self::get_rel_path($rel_path);
	}

	public static function get_child_path($rel_path)
	{
		if (!is_child_theme()) {
			return null;
		}

		return get_stylesheet_directory() . self::get_rel_path($rel_path);
	}

	public static function include_isolated($path)
	{
		call_user_func(self::$include_isolated_callable, $path);
	}

	public static function include_child_first($rel_path)
	{
		if (is_child_theme()) {
			$path = self::get_child_path($rel_path);

			if (file_exists($path)) {
				self::include_isolated($path);
			}
		}

		{
			$path = self::get_parent_path($rel_path);

			if (file_exists($path)) {
				self::include_isolated($path);
			}
		}
	}

	/**
	 * @internal
	 */
	public static function _action_enqueue_scripts()
	{
		self::include_child_first('/static.php');
	}

	/**
	 * @internal
	 */
	public static function _action_init()
	{
		self::include_child_first('/menus.php');
		self::include_child_first('/posts.php');
	}

	/**
	 * @internal
	 */
	public static function _action_widgets_init()
	{
		{
			$paths = array();

			if (is_child_theme()) {
				$paths[] = self::get_child_path('/widgets');
			}

			$paths[] = self::get_parent_path('/widgets');
		}

		$included_widgets = array();

		foreach ($paths as $path) {
			$dirs = glob($path .'/*', GLOB_ONLYDIR);

			if (!$dirs) {
				continue;
			}

			foreach ($dirs as $dir) {
				$dirname = basename($dir);

				if (isset($included_widgets[$dirname])) {
					// this happens when a widget in child theme wants to overwrite the widget from parent theme
					continue;
				} else {
					$included_widgets[$dirname] = true;
				}

				self::include_isolated($dir .'/class-widget-'. $dirname .'.php');

				register_widget('Widget_'. self::dirname_to_classname($dirname));
			}
		}
	}
}

Theme_Includes::init();