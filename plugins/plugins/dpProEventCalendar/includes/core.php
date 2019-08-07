<?php 

/************************************************************************/
/*** DISPLAY START
/************************************************************************/
class dpProEventCalendar_wpress_display {
	
	static $js_flag;
	static $js_declaration = array();
	static $id_calendar;
	static $type;
	static $limit;
	static $widget;
	static $limit_description;
	static $category;
	static $event_id;
	static $event;
	static $columns;
	static $from;
	static $view;
	static $author;
	static $get;
	static $opts;
	public $events_html;

	function dpProEventCalendar_wpress_display($id, $type, $limit, $widget, $limit_description = 0, $category, $author, $get = "", $event_id = "", $event = "", $columns = "", $from = "", $view = "", $opts = array()) {
		self::$id_calendar = $id;
		self::$type = $type;
		self::$limit = $limit;
		self::$widget = $widget;
		self::$limit_description = $limit_description;
		self::$category = $category;
		self::$event_id = $event_id;
		self::$event = $event;
		self::$columns = $columns;
		self::$view = $view;
		self::$author = $author;
		self::$get = $get;
		self::$opts = $opts;
		self::return_dpProEventCalendar();
		
		add_action('wp_footer', array(__CLASS__, 'add_scripts'), 100);
		
	}
	
	static function add_scripts() {
		global $dpProEventCalendar;
		
		if(self::$js_flag) {
			foreach( self::$js_declaration as $key) { echo $key; }
			echo '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';
		}
	}
	
	function return_dpProEventCalendar() {
		global $dpProEventCalendar, $wpdb, $table_prefix, $post;
		
		$id = self::$id_calendar;
		$type = self::$type;
		$limit = self::$limit;
		$author = self::$author;
		$get = self::$get;
		$widget = self::$widget;
		$limit_description = self::$limit_description;
		$category = self::$category;
		$event_id = self::$event_id;
		$event = self::$event;
		$columns = self::$columns;
		$view = self::$view;
		$from = self::$from;
		$opts = self::$opts;
		
		if($id == "") {
			$id = get_post_meta($post->ID, 'pec_id_calendar', true);
		}
		
		require_once (dirname (__FILE__) . '/../classes/base.class.php');
		$dpProEventCalendar_class = new DpProEventCalendar( false, $id, null, null, $widget, $category, $event_id, $author, $event, $columns, $from, $view, $limit_description, $opts );
		
		if($get != "") { 
			
			$this->events_html = $dpProEventCalendar_class->getFormattedEventData($get); return; 
		}
		
		if($type != "") { $dpProEventCalendar_class->switchCalendarTo($type, $limit, $limit_description, $category, $author, $event_id); }
		
		//array_walk($dpProEventCalendar, 'dpProEventCalendar_reslash_multi');
		//$rand_num = rand();

		//if(!$calendar->active) { return ''; }
		
		$events_script= $dpProEventCalendar_class->addScripts();
		self::$js_declaration[] = $events_script;
		
		self::$js_flag = true;
		
		if(!empty($event)) {
			$events_html = $dpProEventCalendar_class->outputEvent($event);
		} else {
			$events_html = $dpProEventCalendar_class->output();
		}

		$this->events_html = $events_html;
	}
}

function dpProEventCalendar_simple_shortcode($atts) {
	global $dpProEventCalendar, $wp_scripts;
	
	// Clear all W3 Total Cache
	if( class_exists('W3_Plugin_TotalCacheAdmin') )
	{
		$plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
	
		$plugin_totalcacheadmin->flush_all();
	
	}

	extract(shortcode_atts(array(
		'id' => '',
		'type' => '',
		'category' => '',
		'event_id' => '',
		'event' => '',
		'columns' => '',
		'from' => '',
		'past' => '',
		'scope' => '',
		'author' => '',
		'get' => '',
		'view' => '',
		'hide_old_dates' => '',
		'limit' => '',
		'widget' => '',
		'group' => '',
		'skin' => '',
		'pagination' => '',
		'echo' => '',
		'start_date' => null,
		'end_date' => null,
		'allow_user_edit_remove' => 1,
		'calendar_per_date' => 3,
		'include_all_events' => '',
		'limit_description' => '',
		'force_dates' => '',
		'rtl' => ''
	), $atts));

	/* Add JS files */
	if ( !is_admin() ){ 
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );

		wp_enqueue_script( 'jquery-ui-datepicker'); 
		wp_enqueue_script( 'placeholder.js', dpProEventCalendar_plugin_url( 'js/jquery.placeholder.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'selectric', dpProEventCalendar_plugin_url( 'js/jquery.selectric.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'jquery-form', dpProEventCalendar_plugin_url( 'js/jquery.form.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'icheck', dpProEventCalendar_plugin_url( 'js/jquery.icheck.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		
		
		wp_enqueue_script( 'isotope', dpProEventCalendar_plugin_url( 'js/isotope.pkgd.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 

		wp_enqueue_script( 'modulo-columns', dpProEventCalendar_plugin_url( 'js/modulo-columns.js' ),
			array('isotope'), DP_PRO_EVENT_CALENDAR_VER, false); 
			
		wp_enqueue_script( 'dpProEventCalendar', dpProEventCalendar_plugin_url( 'js/jquery.dpProEventCalendar.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		
		$data = $wp_scripts->get_data('dpProEventCalendar', 'data');
		if(empty($data)) {

			$localize = array( 
			'ajaxurl' => admin_url( 'admin-ajax.php'.(defined('ICL_LANGUAGE_CODE') ? '?lang='.ICL_LANGUAGE_CODE : '') ), 
			'postEventsNonce' => wp_create_nonce( 'ajax-get-events-nonce' ),
			);

			$localize['recaptcha_enable'] = false;
			$localize['recaptcha_site_key'] = '';
			if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
				$localize['recaptcha_enable'] = true;
				$localize['recaptcha_site_key'] = $dpProEventCalendar['recaptcha_site_key'];
			}

			wp_localize_script( 'dpProEventCalendar', 'ProEventCalendarAjax', $localize);
		}

		if(!$dpProEventCalendar['exclude_gmaps']) {
			wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.$dpProEventCalendar['google_map_key'],
				array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false); 
		}

		wp_enqueue_script( 'infobubble', dpProEventCalendar_plugin_url( 'js/infobubble.js' ),
			array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false);

		wp_enqueue_script( 'oms', dpProEventCalendar_plugin_url( 'js/oms.min.js' ),
			array('dpProEventCalendar'), DP_PRO_EVENT_CALENDAR_VER, false);
		
		if(isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
			wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js',
				'dpProEventCalendar', DP_PRO_EVENT_CALENDAR_VER, false); 
		}

	}
	
	//wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'jquery-ui-datepicker-style' , dpProEventCalendar_plugin_url( 'css/jquery.datepicker.min.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		
	
	if($dpProEventCalendar['rtl_support'] || $rtl || is_rtl()) {
		wp_enqueue_style( 'dpProEventCalendar_rtlcss', dpProEventCalendar_plugin_url( 'css/rtl.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
	}
	
	if($author == 'current') {
		if(is_user_logged_in()) {
			global $current_user;
			
			$author = $current_user->ID;
		} else {
			$author = strval(rand(1, 1000)).'00000000000000000000';
		}
	}

	if(!is_numeric($author) && $author != "") {
		$user_author = get_user_by( 'login', $author );
		$author = $user_author->ID;
	}
	
	$opts = array(
		'limit' => $limit,
		'widget' => $widget,
		'limit_description' => $limit_description,
		'category' => $category,
		'author' => $author,
		'get' => $get,
		'event_id' => $event_id,
		'event' => $event,
		'columns' => $columns,
		'from' => $from,
		'view' => $view,
		'hide_old_dates' => $hide_old_dates,
		'scope' => $scope,
		'start_date' => $start_date,
		'end_date' => $end_date,
		'skin' => $skin,
		'group' => $group,
		'echo' => $echo,
		'calendar_per_date' => $calendar_per_date,
		'allow_user_edit_remove' => $allow_user_edit_remove,
		'include_all_events' => $include_all_events,
		'pagination' => $pagination,
		'force_dates' => $force_dates,
		'rtl' => $rtl
	);
	
	$dpProEventCalendar_wpress_display = new dpProEventCalendar_wpress_display($id, $type, $limit, $widget, $limit_description, $category, $author, $get, $event_id, $event, $columns, $from, $view, $opts);


	if($echo) {

		echo $dpProEventCalendar_wpress_display->events_html;

	} else {

		return $dpProEventCalendar_wpress_display->events_html;
	}
	
}
add_shortcode('dpProEventCalendar', 'dpProEventCalendar_simple_shortcode');

/************************************************************************/
/*** DISPLAY END
/************************************************************************/

/************************************************************************/
/*** WIDGET START
/************************************************************************/

class DpProEventCalendar_Widget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => 'Use the calendar as a widget',
			'name' => 'DP Pro Event Calendar'
		);
		
		parent::__construct('EventsCalendar', '', $params);
	}
	
	public function form($instance) {
		global $wpdb, $table_prefix;
		$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;

		extract($instance);

		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title','dpProEventCalendar'); ?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description','dpProEventCalendar'); ?>: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar','dpProEventCalendar'); ?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('category');?>"><?php _e('Category','dpProEventCalendar'); ?>: </label>
            	<select name="<?php echo $this->get_field_name('category');?>" id="<?php echo $this->get_field_id('category');?>">
                    <option value=""><?php _e('All Categories...','dpProEventCalendar'); ?></option>
					<?php 
                     $categories = get_categories(array('taxonomy' => 'pec_events_category', 'hide_empty' => 0)); 
                      foreach ($categories as $category_key) {

                        $option = '<option value="'.$category_key->term_id.'" '.($category == $category_key->term_id ? 'selected="selected"' : '').'>';
                        $option .= $category_key->cat_name;
                        $option .= '</option>';
                        echo $option;
                      }
					  ?>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('author');?>"><?php _e('Author','dpProEventCalendar'); ?>: </label>
            	<select name="<?php echo $this->get_field_name('author');?>" id="<?php echo $this->get_field_id('author');?>">
                    <option value=""><?php _e('All Authors...','dpProEventCalendar'); ?></option>
                    <option value="current" <?php if($author == 'current') { ?> selected="selected"<?php }?>><?php _e('Current logged in user','dpProEventCalendar'); ?></option>
					<?php 
					$blogusers = get_users('who=authors');
					foreach ($blogusers as $user) {
						echo '<option value="'.$user->ID.'" '.($author == $user->ID ? 'selected="selected"' : '').'>' . $user->display_name . ' ('.$user->user_nicename.')</option>';
					}?>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('layout');?>"><?php _e('Layout', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('layout');?>" id="<?php echo $this->get_field_id('layout');?>">
                    <option value=""><?php _e('Default','dpProEventCalendar'); ?></option>
                    <option value="compact" <?php if($layout == 'compact') {?> selected="selected" <?php } ?>><?php _e('Compact','dpProEventCalendar'); ?></option>
                </select>
            </p>

            <p>
            	<label for="<?php echo $this->get_field_id('rtl');?>"><?php _e('RTL (Right-to-left)', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('rtl');?>" id="<?php echo $this->get_field_id('rtl');?>">
                    <option value=""><?php _e('Default','dpProEventCalendar'); ?></option>
                    <option value="1" <?php if($rtl == '1') {?> selected="selected" <?php } ?>><?php _e('Yes','dpProEventCalendar'); ?></option>
                </select>
            </p>

            <p>
            	<label for="<?php echo $this->get_field_id('skin');?>"><?php _e('Skin', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('skin');?>" id="<?php echo $this->get_field_id('skin');?>">
                    <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                    <option value="red" <?php if($skin == 'red') {?> selected="selected" <?php } ?>><?php _e('Red','dpProEventCalendar'); ?></option>
                    <option value="pink" <?php if($skin == 'pink') {?> selected="selected" <?php } ?>><?php _e('Pink','dpProEventCalendar'); ?></option>
                    <option value="purple" <?php if($skin == 'purple') {?> selected="selected" <?php } ?>><?php _e('Purple','dpProEventCalendar'); ?></option>
                    <option value="deep_purple" <?php if($skin == 'deep_purple') {?> selected="selected" <?php } ?>><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                    <option value="indigo" <?php if($skin == 'indigo') {?> selected="selected" <?php } ?>><?php _e('Indigo','dpProEventCalendar'); ?></option>
                    <option value="blue" <?php if($skin == 'blue') {?> selected="selected" <?php } ?>><?php _e('Blue','dpProEventCalendar'); ?></option>
                    <option value="light_blue" <?php if($skin == 'light_blue') {?> selected="selected" <?php } ?>><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                    <option value="cyan" <?php if($skin == 'cyan') {?> selected="selected" <?php } ?>><?php _e('Cyan','dpProEventCalendar'); ?></option>
                    <option value="teal" <?php if($skin == 'teal') {?> selected="selected" <?php } ?>><?php _e('Teal','dpProEventCalendar'); ?></option>
                    <option value="green" <?php if($skin == 'green') {?> selected="selected" <?php } ?>><?php _e('Green','dpProEventCalendar'); ?></option>
                    <option value="light_green" <?php if($skin == 'light_green') {?> selected="selected" <?php } ?>><?php _e('Light Green','dpProEventCalendar'); ?></option>
                    <option value="lime" <?php if($skin == 'lime') {?> selected="selected" <?php } ?>><?php _e('Lime','dpProEventCalendar'); ?></option>
                    <option value="yellow" <?php if($skin == 'yellow') {?> selected="selected" <?php } ?>><?php _e('Yellow','dpProEventCalendar'); ?></option>
                    <option value="amber" <?php if($skin == 'amber') {?> selected="selected" <?php } ?>><?php _e('Amber','dpProEventCalendar'); ?></option>
                    <option value="orange" <?php if($skin == 'orange') {?> selected="selected" <?php } ?>><?php _e('Orange','dpProEventCalendar'); ?></option>
                    <option value="deep_orange" <?php if($skin == 'deep_orange') {?> selected="selected" <?php } ?>><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                    <option value="brown" <?php if($skin == 'brown') {?> selected="selected" <?php } ?>><?php _e('Brown','dpProEventCalendar'); ?></option>
                    <option value="grey" <?php if($skin == 'grey') {?> selected="selected" <?php } ?>><?php _e('Grey','dpProEventCalendar'); ?></option>
                    <option value="blue_grey" <?php if($skin == 'blue_grey') {?> selected="selected" <?php } ?>><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                </select>
            </p>
            
            
        <?php
	}
	
	public function widget($args, $instance) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar';
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' widget=1 category="'.$category.'" author="'.$author.'" skin="'.$skin.'" rtl="'.$rtl.'" type="'.$layout.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_widget');
function dpProEventCalendar_register_widget() {
	register_widget('DpProEventCalendar_Widget');
}

/************************************************************************/
/*** WIDGET END
/************************************************************************/

/************************************************************************/
/*** WIDGET UPCOMING EVENTS START
/************************************************************************/

class DpProEventCalendar_UpcomingEventsWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => 'Display the upcoming events of a calendar.',
			'name' => 'DP Pro Event Calendar - Upcoming Events'
		);
		
		parent::__construct('EventsCalendarUpcomingEvents', '', $params);
	}
	
	public function form($instance) {
		global $wpdb, $table_prefix;
		$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		
		extract($instance);

		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title', 'dpProEventCalendar')?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>n: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('layout');?>"><?php _e('Layout')?>: </label>
            	<select name="<?php echo $this->get_field_name('layout');?>" id="<?php echo $this->get_field_id('layout');?>" onchange="pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>(this.value);">
                	<option value=""><?php _e('Default')?></option>
                    <option value="accordion-upcoming" <?php if($layout == 'accordion-upcoming') {?> selected="selected" <?php } ?>><?php _e('Accordion', 'dpProEventCalendar')?></option>
                    <option value="gmap-upcoming" <?php if($layout == 'gmap-upcoming') {?> selected="selected" <?php } ?>><?php _e('Google Map', 'dpProEventCalendar')?></option>
                    <option value="grid-upcoming" <?php if($layout == 'grid-upcoming') {?> selected="selected" <?php } ?>><?php _e('Grid', 'dpProEventCalendar')?></option>
                    <option value="countdown" <?php if($layout == 'countdown') {?> selected="selected" <?php } ?>><?php _e('Countdown', 'dpProEventCalendar')?></option>
                    <option value="compact-upcoming" <?php if($layout == 'compact-upcoming') {?> selected="selected" <?php } ?>><?php _e('Compact', 'dpProEventCalendar')?></option>
                    <option value="list-upcoming" <?php if($layout == 'list-upcoming') {?> selected="selected" <?php } ?>><?php _e('List', 'dpProEventCalendar')?></option>
                </select>
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('category');?>"><?php _e('Category')?>: </label>
            	<select name="<?php echo $this->get_field_name('category');?>" id="<?php echo $this->get_field_id('category');?>">
                	<option value=""><?php _e('All')?></option>
                    <?php
                    $categories=  get_categories(array('taxonomy' => 'pec_events_category', 'hide_empty' => 0)); 
					foreach ($categories as $cat) {
                    ?>
                        <option value="<?php echo $cat->term_id?>" <?php if($category == $cat->term_id) {?> selected="selected" <?php } ?>><?php echo $cat->name?></option>
                    <?php }?>
                </select>
            </p>
            
            <p id="list-<?php echo $this->get_field_id('skin');?>">
            	<label for="<?php echo $this->get_field_id('skin');?>"><?php _e('Skin', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('skin');?>" id="<?php echo $this->get_field_id('skin');?>">
                    <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                    <option value="red" <?php if($skin == 'red') {?> selected="selected" <?php } ?>><?php _e('Red','dpProEventCalendar'); ?></option>
                    <option value="pink" <?php if($skin == 'pink') {?> selected="selected" <?php } ?>><?php _e('Pink','dpProEventCalendar'); ?></option>
                    <option value="purple" <?php if($skin == 'purple') {?> selected="selected" <?php } ?>><?php _e('Purple','dpProEventCalendar'); ?></option>
                    <option value="deep_purple" <?php if($skin == 'deep_purple') {?> selected="selected" <?php } ?>><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                    <option value="indigo" <?php if($skin == 'indigo') {?> selected="selected" <?php } ?>><?php _e('Indigo','dpProEventCalendar'); ?></option>
                    <option value="blue" <?php if($skin == 'blue') {?> selected="selected" <?php } ?>><?php _e('Blue','dpProEventCalendar'); ?></option>
                    <option value="light_blue" <?php if($skin == 'light_blue') {?> selected="selected" <?php } ?>><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                    <option value="cyan" <?php if($skin == 'cyan') {?> selected="selected" <?php } ?>><?php _e('Cyan','dpProEventCalendar'); ?></option>
                    <option value="teal" <?php if($skin == 'teal') {?> selected="selected" <?php } ?>><?php _e('Teal','dpProEventCalendar'); ?></option>
                    <option value="green" <?php if($skin == 'green') {?> selected="selected" <?php } ?>><?php _e('Green','dpProEventCalendar'); ?></option>
                    <option value="light_green" <?php if($skin == 'light_green') {?> selected="selected" <?php } ?>><?php _e('Light Green','dpProEventCalendar'); ?></option>
                    <option value="lime" <?php if($skin == 'lime') {?> selected="selected" <?php } ?>><?php _e('Lime','dpProEventCalendar'); ?></option>
                    <option value="yellow" <?php if($skin == 'yellow') {?> selected="selected" <?php } ?>><?php _e('Yellow','dpProEventCalendar'); ?></option>
                    <option value="amber" <?php if($skin == 'amber') {?> selected="selected" <?php } ?>><?php _e('Amber','dpProEventCalendar'); ?></option>
                    <option value="orange" <?php if($skin == 'orange') {?> selected="selected" <?php } ?>><?php _e('Orange','dpProEventCalendar'); ?></option>
                    <option value="deep_orange" <?php if($skin == 'deep_orange') {?> selected="selected" <?php } ?>><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                    <option value="brown" <?php if($skin == 'brown') {?> selected="selected" <?php } ?>><?php _e('Brown','dpProEventCalendar'); ?></option>
                    <option value="grey" <?php if($skin == 'grey') {?> selected="selected" <?php } ?>><?php _e('Grey','dpProEventCalendar'); ?></option>
                    <option value="blue_grey" <?php if($skin == 'blue_grey') {?> selected="selected" <?php } ?>><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('events_count');?>"><?php _e('Max Number of Events to Display', 'dpProEventCalendar')?>: </label>
                <input type="number" class="widefat" style="width:40px;" min="1" max="10" id="<?php echo $this->get_field_id('events_count');?>" name="<?php echo $this->get_field_name('events_count');?>" value="<?php echo !empty($events_count) ? $events_count : 5; ?>" />
            </p>

            <p id="pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>">
            	<label for="<?php echo $this->get_field_id('pagination');?>"><?php _e('Pagination', 'dpProEventCalendar')?>: </label>
                <input type="number" class="widefat" style="width:60px;" min="1" max="50" id="<?php echo $this->get_field_id('pagination');?>" name="<?php echo $this->get_field_name('pagination');?>" value="<?php echo !empty($pagination) ? $pagination : ''; ?>" />
            </p>

            <p id="pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>">
            	<label for="<?php echo $this->get_field_id('columns');?>"><?php _e('Columns', 'dpProEventCalendar')?>: </label>
                <select name="<?php echo $this->get_field_name('columns');?>" id="<?php echo $this->get_field_id('columns');?>">
                    <option value="1"><?php _e('1 Column','dpProEventCalendar'); ?></option>
                    <option value="2" <?php if($columns == 2) {?>selected="selected"<?php }?>><?php _e('2 Columns','dpProEventCalendar'); ?></option>
                    <option value="3" <?php if($columns == 3) {?>selected="selected"<?php }?>><?php _e('3 Columns','dpProEventCalendar'); ?></option>
                    <option value="4" <?php if($columns == 4) {?>selected="selected"<?php }?>><?php _e('4 Columns','dpProEventCalendar'); ?></option>
                </select>
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('limit_description');?>"><?php _e('Limit Description', 'dpProEventCalendar')?>: </label>
                <input type="number" min="0" max="500" id="<?php echo $this->get_field_id('limit_description');?>" name="<?php echo $this->get_field_name('limit_description');?>" value="<?php if(isset($limit_description)) echo esc_attr($limit_description); ?>" />&nbsp;words
            </p>
            
            <script type="text/javascript">
			function pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>(val) {
				jQuery('#list-<?php echo $this->get_field_id('skin');?>').hide(); 
				jQuery('#pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>').show(); 
				jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').show(); 
				
				if(val == 'accordion-upcoming' || val == 'compact-upcoming') { 
				
					jQuery('#list-<?php echo $this->get_field_id('skin');?>').show(); 
				
				} 	
				
				if(val == 'gmap-upcoming' || val == 'compact-upcoming') {
					jQuery('#pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>').hide();
					jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').hide();
				}
				
				if(val == 'grid-upcoming') {
					jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').hide();
				}
			}

			<?php if($layout != "") {?>
				pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>("<?php echo $layout?>");
			<?php }?>

			</script>
        <?php
	}
	
	public function widget($args, $instance) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		$type = 'upcoming';
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		if(!is_numeric($events_count)) { $events_count = 5; }
		
		if($layout != "") {
			$type = $layout;
		}
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' widget=1 type="'.$type.'" category="'.$category.'" limit="'.$events_count.'" limit_description="'.$limit_description.'" columns="'.$columns.'" skin="'.$skin.'" pagination="'.$pagination.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_upcomingeventswidget');
function dpProEventCalendar_register_upcomingeventswidget() {
	register_widget('DpProEventCalendar_UpcomingEventsWidget');
}

/************************************************************************/
/*** WIDGET UPCOMING EVENTS END
/************************************************************************/

/************************************************************************/
/*** WIDGET ACCORDION EVENTS START
/************************************************************************/

class DpProEventCalendar_AccordionWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => 'Display events in an Accordion list.',
			'name' => 'DP Pro Event Calendar - Accordion List'
		);
		
		parent::__construct('EventsCalendarAccordion', '', $params);
	}
	
	public function form($instance) {
		global $wpdb, $table_prefix;
		$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		
		extract($instance);
		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title', 'dpProEventCalendar')?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('skin');?>"><?php _e('Skin', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('skin');?>" id="<?php echo $this->get_field_id('skin');?>">
                    <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                    <option value="red" <?php if($skin == 'red') {?> selected="selected" <?php } ?>><?php _e('Red','dpProEventCalendar'); ?></option>
                    <option value="pink" <?php if($skin == 'pink') {?> selected="selected" <?php } ?>><?php _e('Pink','dpProEventCalendar'); ?></option>
                    <option value="purple" <?php if($skin == 'purple') {?> selected="selected" <?php } ?>><?php _e('Purple','dpProEventCalendar'); ?></option>
                    <option value="deep_purple" <?php if($skin == 'deep_purple') {?> selected="selected" <?php } ?>><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                    <option value="indigo" <?php if($skin == 'indigo') {?> selected="selected" <?php } ?>><?php _e('Indigo','dpProEventCalendar'); ?></option>
                    <option value="blue" <?php if($skin == 'blue') {?> selected="selected" <?php } ?>><?php _e('Blue','dpProEventCalendar'); ?></option>
                    <option value="light_blue" <?php if($skin == 'light_blue') {?> selected="selected" <?php } ?>><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                    <option value="cyan" <?php if($skin == 'cyan') {?> selected="selected" <?php } ?>><?php _e('Cyan','dpProEventCalendar'); ?></option>
                    <option value="teal" <?php if($skin == 'teal') {?> selected="selected" <?php } ?>><?php _e('Teal','dpProEventCalendar'); ?></option>
                    <option value="green" <?php if($skin == 'green') {?> selected="selected" <?php } ?>><?php _e('Green','dpProEventCalendar'); ?></option>
                    <option value="light_green" <?php if($skin == 'light_green') {?> selected="selected" <?php } ?>><?php _e('Light Green','dpProEventCalendar'); ?></option>
                    <option value="lime" <?php if($skin == 'lime') {?> selected="selected" <?php } ?>><?php _e('Lime','dpProEventCalendar'); ?></option>
                    <option value="yellow" <?php if($skin == 'yellow') {?> selected="selected" <?php } ?>><?php _e('Yellow','dpProEventCalendar'); ?></option>
                    <option value="amber" <?php if($skin == 'amber') {?> selected="selected" <?php } ?>><?php _e('Amber','dpProEventCalendar'); ?></option>
                    <option value="orange" <?php if($skin == 'orange') {?> selected="selected" <?php } ?>><?php _e('Orange','dpProEventCalendar'); ?></option>
                    <option value="deep_orange" <?php if($skin == 'deep_orange') {?> selected="selected" <?php } ?>><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                    <option value="brown" <?php if($skin == 'brown') {?> selected="selected" <?php } ?>><?php _e('Brown','dpProEventCalendar'); ?></option>
                    <option value="grey" <?php if($skin == 'grey') {?> selected="selected" <?php } ?>><?php _e('Grey','dpProEventCalendar'); ?></option>
                    <option value="blue_grey" <?php if($skin == 'blue_grey') {?> selected="selected" <?php } ?>><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('limit');?>"><?php _e('Limit Events', 'dpProEventCalendar')?>: </label>
                <input type="number" min="0" max="100" id="<?php echo $this->get_field_id('limit');?>" name="<?php echo $this->get_field_name('limit');?>" value="<?php if(isset($limit)) echo esc_attr($limit); ?>" />
            </p>

            <p>
            	<label for="<?php echo $this->get_field_id('limit_description');?>"><?php _e('Limit Description', 'dpProEventCalendar')?>: </label>
                <input type="number" min="0" max="500" id="<?php echo $this->get_field_id('limit_description');?>" name="<?php echo $this->get_field_name('limit_description');?>" value="<?php if(isset($limit_description)) echo esc_attr($limit_description); ?>" />&nbsp;words
            </p>
        <?php
	}
	
	public function widget($args, $instance) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		
		if($limit == "") {
			$limit = 0;
		}
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar widget=1 id='.$calendar.' type="accordion" category="'.$category.'" limit_description="'.$limit_description.'" skin="'.$skin.'" limit="'.$limit.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_accordionwidget');
function dpProEventCalendar_register_accordionwidget() {
	register_widget('DpProEventCalendar_AccordionWidget');
}

/************************************************************************/
/*** WIDGET ACCORDION END
/************************************************************************/

/************************************************************************/
/*** WIDGET ADD EVENTS START
/************************************************************************/

class DpProEventCalendar_AddEventsWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => 'Allow logged in users to submit events.',
			'name' => 'DP Pro Event Calendar - Add Events'
		);
		
		parent::__construct('EventsCalendarAddEvents', '', $params);
	}
	
	public function form($instance) {
		global $wpdb, $table_prefix;
		$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		
		extract($instance);
		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title', 'dpProEventCalendar')?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('skin');?>"><?php _e('Skin', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('skin');?>" id="<?php echo $this->get_field_id('skin');?>">
                    <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                    <option value="red" <?php if($skin == 'red') {?> selected="selected" <?php } ?>><?php _e('Red','dpProEventCalendar'); ?></option>
                    <option value="pink" <?php if($skin == 'pink') {?> selected="selected" <?php } ?>><?php _e('Pink','dpProEventCalendar'); ?></option>
                    <option value="purple" <?php if($skin == 'purple') {?> selected="selected" <?php } ?>><?php _e('Purple','dpProEventCalendar'); ?></option>
                    <option value="deep_purple" <?php if($skin == 'deep_purple') {?> selected="selected" <?php } ?>><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                    <option value="indigo" <?php if($skin == 'indigo') {?> selected="selected" <?php } ?>><?php _e('Indigo','dpProEventCalendar'); ?></option>
                    <option value="blue" <?php if($skin == 'blue') {?> selected="selected" <?php } ?>><?php _e('Blue','dpProEventCalendar'); ?></option>
                    <option value="light_blue" <?php if($skin == 'light_blue') {?> selected="selected" <?php } ?>><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                    <option value="cyan" <?php if($skin == 'cyan') {?> selected="selected" <?php } ?>><?php _e('Cyan','dpProEventCalendar'); ?></option>
                    <option value="teal" <?php if($skin == 'teal') {?> selected="selected" <?php } ?>><?php _e('Teal','dpProEventCalendar'); ?></option>
                    <option value="green" <?php if($skin == 'green') {?> selected="selected" <?php } ?>><?php _e('Green','dpProEventCalendar'); ?></option>
                    <option value="light_green" <?php if($skin == 'light_green') {?> selected="selected" <?php } ?>><?php _e('Light Green','dpProEventCalendar'); ?></option>
                    <option value="lime" <?php if($skin == 'lime') {?> selected="selected" <?php } ?>><?php _e('Lime','dpProEventCalendar'); ?></option>
                    <option value="yellow" <?php if($skin == 'yellow') {?> selected="selected" <?php } ?>><?php _e('Yellow','dpProEventCalendar'); ?></option>
                    <option value="amber" <?php if($skin == 'amber') {?> selected="selected" <?php } ?>><?php _e('Amber','dpProEventCalendar'); ?></option>
                    <option value="orange" <?php if($skin == 'orange') {?> selected="selected" <?php } ?>><?php _e('Orange','dpProEventCalendar'); ?></option>
                    <option value="deep_orange" <?php if($skin == 'deep_orange') {?> selected="selected" <?php } ?>><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                    <option value="brown" <?php if($skin == 'brown') {?> selected="selected" <?php } ?>><?php _e('Brown','dpProEventCalendar'); ?></option>
                    <option value="grey" <?php if($skin == 'grey') {?> selected="selected" <?php } ?>><?php _e('Grey','dpProEventCalendar'); ?></option>
                    <option value="blue_grey" <?php if($skin == 'blue_grey') {?> selected="selected" <?php } ?>><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                </select>
            </p>
        <?php
	}
	
	public function widget($args, $instance) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' type="add-event" category="'.$category.'" skin="'.$skin.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_addeventswidget');
function dpProEventCalendar_register_addeventswidget() {
	register_widget('DpProEventCalendar_AddEventsWidget');
}

/************************************************************************/
/*** WIDGET ADD EVENTS END
/************************************************************************/

/************************************************************************/
/*** WIDGET TODAY EVENTS START
/************************************************************************/

class DpProEventCalendar_TodayEventsWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => 'Display today\'s events in a list.',
			'name' => 'DP Pro Event Calendar - Today\'s Events'
		);
		
		parent::__construct('EventsCalendarTodayEvents', '', $params);
	}
	
	public function form($instance) {
		global $wpdb, $table_prefix;
		$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		
		extract($instance);
		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title', 'dpProEventCalendar')?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
        <?php
	}
	
	public function widget($args, $instance) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' type="today-events" widget=1]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_todayeventswidget');
function dpProEventCalendar_register_todayeventswidget() {
	register_widget('DpProEventCalendar_TodayEventsWidget');
}

/************************************************************************/
/*** WIDGET ADD EVENTS END
/************************************************************************/


/*
function dpProEventCalendar_enqueue_scripts() {
	
}

add_action( 'init', 'dpProEventCalendar_enqueue_scripts' );
*/

function dpProEventCalendar_enqueue_styles() {	
  	global $post, $dpProEventCalendar, $wp_registered_widgets,$wp_widget_factory;
  
	wp_enqueue_style( 'dpProEventCalendar_headcss', dpProEventCalendar_plugin_url( 'css/dpProEventCalendar.css' ),
		false, DP_PRO_EVENT_CALENDAR_VER, 'all');
	wp_enqueue_style( 'font-awesome-original', dpProEventCalendar_plugin_url( 'css/font-awesome.css' ),
		false, DP_PRO_EVENT_CALENDAR_VER, 'all');
  
}
add_action( 'init', 'dpProEventCalendar_enqueue_styles' );

//admin settings
function dpProEventCalendar_admin_scripts($force = false) {
	global $dpProEventCalendar;
	if ( is_admin() ){ // admin actions
		// Settings page only

		if ( $force || (isset($_GET['page']) && 
		('dpProEventCalendar-admin' == $_GET['page'] 
		or 'dpProEventCalendar-settings' == $_GET['page'] 
		or 'dpProEventCalendar-events' == $_GET['page'] 
		or 'dpProEventCalendar-special' == $_GET['page'] 
		or 'dpProEventCalendar-import' == $_GET['page'] 
		or 'dpProEventCalendar-custom-shortcodes' == $_GET['page'] 
		or 'dpProEventCalendar-eventdata' == $_GET['page'] 
		or 'dpProEventCalendar-payments' == $_GET['page'] ))  ) {
		wp_register_script('jquery', false, false, false, false);
		//wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker'); 
		wp_enqueue_style( 'dpProEventCalendar_admin_head_css', dpProEventCalendar_plugin_url( 'css/admin-styles.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		wp_enqueue_style( 'jquery-ui-datepicker-style' , dpProEventCalendar_plugin_url( 'css/jquery.datepicker.min.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		
		wp_enqueue_script( 'dpProEventCalendar', dpProEventCalendar_plugin_url( 'js/jquery.dpProEventCalendar.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_localize_script( 'dpProEventCalendar', 'ProEventCalendarAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'postEventsNonce' => wp_create_nonce( 'ajax-get-events-nonce' ) ) );
		wp_enqueue_script( 'colorpicker2', dpProEventCalendar_plugin_url( 'js/colorpicker.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_script( 'selectric', dpProEventCalendar_plugin_url( 'js/jquery.selectric.min.js' ),
			array('jquery'), DP_PRO_EVENT_CALENDAR_VER, false); 
		wp_enqueue_media();
		wp_enqueue_script ( 'dpProEventCalendar_admin', dpProEventCalendar_plugin_url( 'js/admin_settings.js' ), array('jquery-ui-dialog') ); 
    	wp_enqueue_style ('wp-jquery-ui-dialog');
		wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload', 'word-count', 'post'));
		wp_enqueue_style( 'dpProEventCalendar_headcss', dpProEventCalendar_plugin_url( 'css/dpProEventCalendar.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		wp_enqueue_style( 'colorpicker', dpProEventCalendar_plugin_url( 'css/colorpicker.css' ),
			false, DP_PRO_EVENT_CALENDAR_VER, 'all');
		};
		
		//if(!$dpProEventCalendar['exclude_gmaps']) {
			wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.$dpProEventCalendar['google_map_key'],
				null, DP_PRO_EVENT_CALENDAR_VER, false); 
		//}
		
		wp_enqueue_style('thickbox');
  	}
}

add_action( 'admin_init', 'dpProEventCalendar_admin_scripts' );
add_action( 'pec_enqueue_admin', 'dpProEventCalendar_admin_scripts' );

function dpProEventCalendar_admin_head() {
	global $dpProEventCalendar;
	if ( is_admin() ){ // admin actions
	   
	  	// Special Dates page only
		if ( isset($_GET['page']) && 'dpProEventCalendar-special' == $_GET['page'] ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				function confirmSpecialDelete()
				{
					var agree=confirm("Delete this Special Date?");
					if (agree)
					return true ;
					else
					return false ;
				}
				
				function special_checkform ()
				{
					if (document.getElementById('dpProEventCalendar_title').value == "") {
						alert( "Please enter the title of the special date." );
						document.getElementById('dpProEventCalendar_title').focus();
						return false ;
					}
					return true ;
				}
				
				function special_checkform_edit ()
				{
					if (document.getElementById('dpPEC_special_title').value == "") {
						alert( "Please enter the title of the special date." );
						document.getElementById('dpPEC_special_title').focus();
						return false ;
					}
					return true ;
				}
				
				jQuery(document).ready(function() {
					jQuery('#specialDate_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#specialDate_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_color').val('#' + hex);
						}
					});
					
					jQuery('#specialDate_colorSelector_Edit').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#specialDate_colorSelector_Edit div').css('backgroundColor', '#' + hex);
							jQuery('#dpPEC_special_color').val('#' + hex);
						}
					});
				});
			//]]>
			</script>
	<?php
	   } 
	   
	   // Calendars page only
		if ( isset($_GET['page']) && 
			('dpProEventCalendar-admin' == $_GET['page'] 
				|| 'dpProEventCalendar-payments' == $_GET['page'] 
				|| 'dpProEventCalendar-settings' == $_GET['page']
				|| 'dpProEventCalendar-custom-shortcodes' == $_GET['page']) ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				function confirmCalendarDelete()
				{
					var agree=confirm("<?php echo __("Are you sure?", "dpProEventCalendar")?>");
					if (agree)
					return true ;
					else
					return false ;
				}
				
				function confirmCalendarEventsDelete()
				{
					var agree=confirm("<?php echo __("All the events in this calendar will be deleted. Are you sure?", "dpProEventCalendar")?>");
					if (agree)
					return true ;
					else
					return false ;
				}
				
				function calendar_checkform ()
				{
					if (document.getElementById('dpProEventCalendar_title').value == "") {
						alert( "Please enter the title of the calendar." );
						document.getElementById('dpProEventCalendar_title').focus();
						return false ;
					}
					
					return true ;
				}
				
				function toggleFormat() {
					if(jQuery('#dpProEventCalendar_show_time').attr("checked")) {
						jQuery('#div_time_extended').slideDown('fast');
					} else {
						jQuery('#div_time_extended').slideUp('fast');
					}
				}
				
				function toggleTranslations() {
					if(jQuery('#dpProEventCalendar_enable_wpml').attr("checked")) {
						jQuery('#div_translations_fields').slideUp('fast');
					} else {
						jQuery('#div_translations_fields').slideDown('fast');
					}
				}
				
				function toggleNewEventRoles() {
					if(jQuery('#dpProEventCalendar_allow_user_add_event').attr("checked")) {
						jQuery('#allow_user_add_event_roles').slideDown('fast');
					} else {
						jQuery('#allow_user_add_event_roles').slideUp('fast');
					}
				}
				
				function toggleFormatCategories() {
					if(jQuery('#dpProEventCalendar_show_category_filter').attr("checked")) {
						jQuery('#div_category_filter').slideDown('fast');
					} else {
						jQuery('#div_category_filter').slideUp('fast');
					}
				}

				function toggleFormatVenues() {
					if(jQuery('#dpProEventCalendar_show_location_filter').attr("checked")) {
						jQuery('#div_venue_filter').slideDown('fast');
					} else {
						jQuery('#div_venue_filter').slideUp('fast');
					}
				}
				
				function showAccordion(div, elem) {
					if(jQuery('#'+div).css('display') == 'none') {
						jQuery('#'+div).slideDown('fast');
						jQuery(elem).addClass('dp_ui_on');
					} else {
						jQuery('#'+div).slideUp('fast');
						jQuery(elem).removeClass('dp_ui_on');
					}
				}
				
				jQuery(document).ready(function() {
					
					var custom_uploader;


				    jQuery('#upload_image_button').click(function(e) {

				        e.preventDefault();

				        //If the uploader object has already been created, reopen the dialog
				        if (custom_uploader) {
				            custom_uploader.open();
				            return;
				        }

				        //Extend the wp.media object
				        custom_uploader = wp.media.frames.file_frame = wp.media({
				            title: '<?php _e('Choose Image', 'dpProEventCalendar')?>',
				            button: {
				                text: '<?php _e('Choose Image', 'dpProEventCalendar')?>'
				            },
				            multiple: true
				        });

				        //When a file is selected, grab the URL and set it as the text field's value
				        custom_uploader.on('select', function() {
				            attachment = custom_uploader.state().get('selection').first().toJSON();
				            jQuery('#dpProEventCalendar_options_map_marker').val(attachment.url);
				        });

				        //Open the uploader dialog
				        custom_uploader.open();

				    });
					
					jQuery('#currentDate_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#currentDate_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_current_date_color').val('#' + hex);
						}
					});

					jQuery('#bookedEvent_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#bookedEvent_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_booking_event_color').val('#' + hex);
						}
					});

					jQuery(".pec_calendar_shortcode, .pec_custom_shortcode").on("focus", function () {
					    jQuery(this).selectText();
					});
					jQuery(".pec_calendar_shortcode, .pec_custom_shortcode").on("click", function () {
					    jQuery(this).selectText();
					});
					
				});

				jQuery.fn.selectText = function(){
				   var doc = document;
				   var element = this[0];
				   
				   if (doc.body.createTextRange) {
				       var range = document.body.createTextRange();
				       range.moveToElementText(element);
				       range.select();
				   } else if (window.getSelection) {
				       var selection = window.getSelection();        
				       var range = document.createRange();
				       range.selectNodeContents(element);
				       selection.removeAllRanges();
				       selection.addRange(range);
				   }
				};
			//]]>
			</script>
	<?php
		}
	   // Settings page only
		if ( isset($_GET['page']) && 'dpProEventCalendar-settings' == $_GET['page'] ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function() {
					jQuery('#holidays_colorSelector').ColorPicker({
						onShow: function (colpkr) {
							jQuery(colpkr).fadeIn(500);
							return false;
						},
						onHide: function (colpkr) {
							jQuery(colpkr).fadeOut(500);
							return false;
						},
						onChange: function (hsb, hex, rgb) {
							jQuery('#holidays_colorSelector div').css('backgroundColor', '#' + hex);
							jQuery('#dpProEventCalendar_holidays_color').val('#' + hex);
						}
					});
				});
			//]]>
			</script>
	<?php
	   } //Settings page only
	   
	   // Import page only
		if ( isset($_GET['page']) && 'dpProEventCalendar-import' == $_GET['page'] ) {
		?>
			<script type="text/javascript">
			// <![CDATA[
				function import_checkform ()
				{
					return true;
				}
			//]]>
			</script>
	<?php
	   } //Settings page only
	   
	 }//only for admin
}
add_action('admin_head', 'dpProEventCalendar_admin_head');
?>