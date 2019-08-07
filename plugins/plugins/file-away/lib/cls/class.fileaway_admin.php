<?php 
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_admin'))
{
	class fileaway_admin
	{
		private $sections;
		protected $settings;
		protected $defaults;
		public $options;
		public function __construct()
		{
			$this->sections = array(
				'config'	=> 'Basic Configuration',
				'options'	=> 'Options',
				'customcss'	=> 'Custom Styles',
				'manager'	=> 'Manager Mode',
				'stats'		=> 'Statistics',
				'feeds'		=> 'RSS Feeds',
				'database'	=> 'Database',
				'tutorials'	=> 'Tutorials',
				'about'		=> 'About',
			);
			$this->settings = array();
			$this->get_settings();
			$this->defaults = array(
				'id'      	=> '',
				'section' 	=> 'config',
				'title'   	=> false,
				'type'    	=> 'text',
				'input'		=> 'text',
				'choices' 	=> array(),
				'dflt'    	=> false,
				'holder'	=> false,
				'class'   	=> 'fileaway-inline',
				'helplink'	=> true,
				'submit'  	=> false
			);
			if(!get_option('fileaway_options')) $this->initialize();
			$this->options = get_option('fileaway_options');
			if(!is_subclass_of($this, 'fileaway_admin'))
			{
				$this->check();
				$this->version();
				$this->encryption();
				add_filter('plugin_action_links', array($this, 'pluginpage'), 10, 2);
				add_action('admin_menu', array($this, 'menu'));
				add_action('admin_enqueue_scripts', array($this, 'enqueue'));
				add_action('admin_init', array($this, 'deregister'), 20);
				add_action('wp_ajax_fileaway_save', array($this, 'save'));
				add_action('admin_init', array($this, 'button'));
				foreach(array('post.php','post-new.php') as $hook) add_action("admin_head-$hook", array($this, 'modalvars')); 
				add_action('wp_ajax_fileaway_tinymce', array($this, 'tinymce'));
				if($this->options['postidcolumn'] == 'enabled') new attachaway_column;
			}
		}
		public function initialize(){
			$default_settings = array();
			foreach($this->settings as $id => $setting) 
				$default_settings[$id] = isset($setting['dflt']) ? $setting['dflt'] : false;
			update_option('fileaway_options', $default_settings);
		}
		public function check()
		{
			$update = false;
			foreach($this->settings as $setting => $config)
			{
				if(!isset($this->options[$setting]))
				{
					$this->options[$setting] = isset($config['dflt']) ? $config['dflt'] : false;
					$update = true;
				}
			}	
			if($update) update_option('fileaway_options', $this->options);
		}
		public function version()
		{
			$version = isset($this->options['version']) ? (string)$this->options['version'] : '1.0';
			$themedir = get_template_directory(); 
			$template = 'file-away-iframe-template.php';
			if(!file_exists($themedir.'/'.$template)) copy(fileaway_dir.'/templates/'.$template, $themedir.'/'.$template); 
			elseif($version < '2.4') copy(fileaway_dir.'/templates/'.$template, $themedir.'/'.$template);
			if($version < '3.6') wp_clear_scheduled_hook('ssfa_scheduled_cleanup');
			if($version === (string)fileaway_version) return;
			$this->options['version'] = fileaway_version;
			update_option('fileaway_options', $this->options);
			$path = WP_CONTENT_DIR.'/uploads/fileaway-custom-css';
			if(!is_dir($path)) mkdir($path, 0775, true);
		}
		private function encryption()
		{
			if(!isset($this->options['encryption_key']) || strlen($this->options['encryption_key']) < 16)
			{
				if(function_exists('openssl_random_pseudo_bytes')) 
					$this->options['encryption_key'] = bin2hex(openssl_random_pseudo_bytes(16));
				else
				{
					$key = '';
					$keys = array_merge(range(0, 9), range('a', 'z'));
					for($i = 0; $i < 32; $i++) $key .= $keys[array_rand($keys)];
					$this->options['encryption_key'] = $key;
				}
				update_option('fileaway_options', $this->options);
			}
		}
		public function menu()
		{
			add_menu_page(
			'File Away',
			'File Away', 
			'manage_options', 
			'file-away', 
			array($this, 'page'), 
			fileaway_url.'/lib/img/fileawayicon.png', 
			'99.00000000000000001');
		}
		public function pluginpage($links, $file)
		{
			if(plugin_basename(fileaway) == $file)
			{
				$config = '<a href="'.get_admin_url().'admin.php?page=file-away">Configuration</a>';
				array_unshift ($links, $config); 
			}
			return $links; 
		}
		public function button()
		{
			global $pagenow;
			if(!in_array($pagenow, array('post.php', 'post-new.php'))) return;
			if(current_user_can($this->options['modalaccess']))
			{
				add_filter('mce_external_plugins', array($this, 'tinymce_plugin'));  
				add_filter('mce_buttons'.$this->options['tmcerows'], array($this, 'register_button'));
			}
		}
		public function tinymce_plugin($plugin_array)
		{  
			$plugin_array['fileawaymodal'] = fileaway_url.'/lib/js/modal.js'; 
			return $plugin_array;
		}
		public function register_button($buttons)
		{ 
			array_push($buttons, 'fileawaymodal'); 
			return $buttons;
		}
		public function modalvars()
		{
			$img = fileaway_url.'/lib/img/fileawayicon.png';
			global $wp_version; 
			$version = $wp_version >= 3.9 ? 'new' : 'old';
			$output = 
			"<script>".
				"var fileaway_mce_config = {".
					"'tb_title': 'File Away',".
					"'button_img': '".$img."',".
					"'version': '".$version."',".
					"'ajax_url': '".admin_url('admin-ajax.php')."',".
					"'ajax_nonce': '".wp_create_nonce('_nonce_fileaway_tinymce')."'".
				"};".
			"</script>".
			"<style>".
				"i.fileaway-icon{ background-image: url('".$img."'); }".
			"</style>";
			echo $output;
		}
		public function tinymce()
		{
			if(!check_ajax_referer('_nonce_fileaway_tinymce', 'security', false)) echo 'Ajax Error'; 
			else new fileaway_modal; 
			exit;
		}
		public function save()
		{
			$reload = false;
			if(!wp_verify_nonce($_POST['nonce'], 'fileaway-admin-nonce'))
			{ 
				echo 'Could not verify nonce.'; 
				exit;
			}
			$settings = $_POST['settings']; 
			if(!is_array($settings)) 
			{
				echo 'error';
				exit;
			}
			if($settings['reset_options'] === 'reset')
			{
				$this->initialize(); 
				$this->css(false, true);
				echo 'success'; 
				exit;
			}
			$settings['version'] = fileaway_version;
			if($settings['feedinterval'] != $this->options['feedinterval']) 
				wp_clear_scheduled_hook('fileaway_scheduled_rss_feeds');
			if($settings['compiled_stats'] != $this->options['compiled_stats']) 
				wp_clear_scheduled_hook('fileaway_scheduled_compiled_stats');				
			$reloaders = array('rootdirectory', 'encryption_key', 'loadusers', 'adminstyle', 'css_editor', 'reset_options', 'stats');
			foreach($reloaders as $reloader)
			{
				if($settings[$reloader] != $this->options[$reloader]) $reload = true;
				if($reload) break;
			}
			$success = $settings !== $this->options ? update_option('fileaway_options', $settings) : true;
			$this->css($settings['customcss']); 
			if($settings['updatefeeds'] == 'true')
			{
				$update = new feedaway;
				$update->feeds();	
			}
			echo $success && $reload ? 'reload' : ($success ? 'success' : 'error'); 
			exit;
		}
		private function css($content = false, $unlink = false)
		{
			$path = WP_CONTENT_DIR.'/uploads/fileaway-custom-css';
			$file = $path.'/fileaway-custom-styles.css';
			$unlink = $unlink ? $unlink : (!empty($content) && $content != '' ? false : true);
			if(!$unlink)
			{	
				$content = stripslashes(strip_tags($content));
				if(!is_dir($path)) mkdir($path, 0775, true);
				$success = file_put_contents($file, "\n\n\n".$content);
				return $success;
			}
			if($unlink)
			{
				if(file_exists($file)) unlink($file);
				return file_exists($file) ? false : true;
			}
		}
		public function page(){
			$info = new fileaway_tutorials;
			if($this->options['adminstyle'] !== 'minimal')
			{
				$savers = array(
					array('Filing away...', 'Oh Glory!'),
					array('Gettin\' saved...', 'Hallelujah, by and by.'),
					array('Just a few more weary days and then...', '...your settings will be saved.')
				);
				$randysave = array_rand($savers, 1);
				$saving = $savers[$randysave][0];
				$saved = $savers[$randysave][1];
				$savinganimation = "<img src='".fileaway_url."/lib/img/saving.gif'>"; 
				$bannersize = '400px';
			}
			else
			{
				unset($this->sections['about']);
				$saving = 'Saving changes...'; 
				$saved = 'Changes saved.'; 
				$savinganimation = null; 
				$bannersize = '300px';
			}
			$tabindex = 0; 
			$panelindex = 0;
			$output =  
				'<div id="fileaway-options-container" class="fileaway-wrap">'.
					'<img src="'.fileaway_url.'/lib/img/fileaway_banner.png" style="width:'.$bannersize.'; margin: 20px 0 -10px;">'.
					'<div class="fileaway-tabs"><ul class="fileaway-tabs-nav">';
			foreach($this->sections as $slug => $section)
			{
				$initclass = $tabindex < 1 ? ' state-active' : '';
				$output .= '<li class="'.$slug.$initclass.'" data-tab="'.$slug.'"><a href="javascript:" data-tab="'.$slug.'" id="fileaway-tab-'.$slug.'">'.$section.'</a></li>';
				$tabindex++;
			}
			$output .= '</ul></div>';
			foreach($this->sections as $slug => $section)
			{
				$initdisplay = $panelindex < 1 ? 'block;' : 'none;'; 
				$output .= '<div class="fileaway-tabs-panel" id="fileaway-panel-'.$slug.'" style="display:'.$initdisplay.'"><h3>'.$section.'</h3>';
				if($slug == 'config') include fileaway_dir.'/lib/inc/inc.admin.config-instructions.php';
				if($slug != 'tutorials' && $slug != 'about') $output .= $this->display($slug, $this->defaults);
				elseif($slug == 'tutorials') include fileaway_dir.'/lib/inc/inc.admin.tutorials.php';
				elseif($slug == 'about') include fileaway_dir.'/lib/inc/inc.admin.about.php';
				$output .= '</div>';
				$panelindex++;
			}
			foreach($info->helplinks as $help => $link)
			{
				$output .= 
					'<div id="fileaway-help-'.$help.'" class="fileaway-help-backdrop">'.
						'<div class="fileaway-help-content">'.
							'<div class="fileaway-help-close fileaway-help-iconclose2"></div>'.
							'<h4>'.$link['heading'].'</h4>'.
							$link['info'].
						'</div>'.
					'</div>';	
			}
			$output .=  
				'<div id="fileaway-saving-backdrop">'.
					'<div id="fileaway-saving">'.$saving.'</div>'.
					'<div id="fileaway-saving-img">'.$savinganimation.'</div>'.
					'<div id="fileaway-settings-saved">'.$saved.'</div>';
				'</div>';
			echo $output;
		}
		public function display($slug, $defaults)
		{
			$output = null;
			foreach($this->settings as $id => $setting)
			{
				$setting['id'] = $id; 
				extract(wp_parse_args($setting, $defaults));
				if($section !== $slug) continue;
				if(!isset($this->options[$id])) $this->options[$id] = $dflt;
				if(!isset($this->options[$id])) $this->options[$id] = 0;
				if(!$class) $class = null;
				$submit = $submit 
					? '<br><br><br><span class="fileaway-save-settings fileaway-selectIt">Save Changes</span>' 
					: null;
				global $is_IE, $is_chrome;
				$is_opera = preg_match('/opr/i', $_SERVER['HTTP_USER_AGENT']) ? true : false;
				$chromefix = ($is_chrome or $is_opera ? ' fileaway-abspath-chromefix' : null);
				$iefix = ($is_IE ? ' fileaway-abspath-iefix' : null);
				$get = new fileaway_definitions;
				$pathoptions = $get->pathoptions;
				$abspath = $pathoptions['chosenpath'];
				$abspath = strpos($abspath,'/public_html/') !== false 
					? strstr($abspath, '/public_html/') 
					: (strpos($abspath,'/www/') !== false 
						? strstr($abspath, '/www/')
						: $abspath
					);
				$rootpath = $pathoptions['rootpath'];
				$rootpath = strpos($rootpath,'/public_html/') !== false 
					? strstr($rootpath, '/public_html/') 
					: (strpos($rootpath,'/www/') !== false 
						? strstr($rootpath, '/www/')
						: $rootpath
					);				
				$helplink = $helplink ? '<span class="link-fileaway-help-'.$id.' fileaway-helplink fileaway-help-iconinfo4"></span>' : null;
				switch($type)
				{
					case 'text':
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
		 					$output .=  
								'<input class="regular-text '.$class.'" type="'.$input.'" id="'.$id.'" '.
								'name="fileaway_options['.$id.']" placeholder="'.$holder.'" value="'.stripslashes(esc_attr($this->options[$id])).'" />'.
								$helplink.'</div>'.$submit;
					break;
					case 'basedir':
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
							$output .=  
								'<div id="fileaway-wrap-'.$id.'" class="fileaway-wrap-base">'.
								'<span id="fileaway-abspath-'.$id.'" class="fileaway-abspath'.$chromefix.'">'.$abspath.'</span> '.
								'<input class="regular-text '.$class.'" type="text" id="'.$id.'" name="fileaway_options['.$id.']" '.
								'placeholder="'.$holder.'" value="'.esc_attr($this->options[$id]).'" />'.$helplink.'<br />'.
								'<div id="fileaway-error-'.$id.'" style="display:none; line-height:14px;">'.
								'<span class="warning-text">Sorry. You can\'t point to the wp-admin/ or wp-includes/ directories.<br />'.
								'Use wp-content/uploads, or custom folders in your installation directory.</span></div></div></div>';
				 	break;
					case 'rootpath':
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
							$output .=  
								'<div id="fileaway-wrap-'.$id.'" class="fileaway-wrap-base">'.
								'<span id="fileaway-abspath-'.$id.'" class="fileaway-abspath'.$chromefix.'">'.$rootpath.'</span> '.
								'<input class="regular-text '.$class.'" type="text" id="'.$id.'" name="fileaway_options['.$id.']" '.
								'placeholder="'.$holder.'" value="'.esc_attr($this->options[$id]).'" />'.
								'</div>'.$helplink.'</div>';
				 	break;
					case 'select':
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
							$output .= '<select id="'.$id.'" class="select '.$class.' chozed-select" data-placeholder="&nbsp;" name="fileaway_options['.$id.']">';
							if(is_array($choices))
								foreach($choices as $value => $label)
									$output .= '<option value="'.esc_attr($value).'" '.selected($this->options[$id], $value, false).'>'.$label.'</option>';
							$output .= '</select>'.$helplink.'</div>'.$submit;
					break;
					case 'customcss':
							$output .= '<div style="clear:both!important;"></div>';
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
							$output .= '<textarea class="fileaway-customcss '.$class.'" id="'.$id.'" name="fileaway_options['.$id.']" placeholder="'.$holder.'" rows="10" cols="50" '.
							'>'.stripslashes(strip_tags($this->options[$id])).'</textarea>'.$helplink.'</div>';
							if($this->options['css_editor'] === 'syntax')
								$output .= '<script>var textcss = document.getElementById("'.$id.'"); var CodeMirror = CodeMirror.fromTextArea(textcss, {lineNumbers: true});</script>';
							else $output .= '<script>var CodeMirror = false;</script>';
					break;
					case 'rolescaps':
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
							$output .= '<input type="'.$input.'" id="'.$id.'" name="fileaway_options['.$id.']" value="'.esc_attr($this->options[$id]).'" />';
							$output .= '<select id="'.$id.'" class="select chozed-select" data-placeholder="&nbsp;" multiple>';
							$roles = fileaway_utility::caps();
							if(is_array($roles))
							{
								foreach($roles as $role => $name)
								{ 
									$permitroles = explode(',', $this->options[$id]);
									$selected = null;
									if(is_array($permitroles)) foreach($permitroles as $r) if(trim($r) === $role) $selected = 'selected';
									$output .= '<option value="'.$role.'" '.$selected.'>'.$name.'</option>';
								}
							}
							$output .= '</select>'.$helplink.'</div>';
					break;	
					case 'users':
							if($this->options['loadusers'] == 'true')
							{
								if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
								$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'">';
								$output .= '<input type="'.$input.'" id="'.$id.'" name="fileaway_options['.$id.']" value="'.esc_attr($this->options[$id]).'" />';
								$output .= '<select id="'.$id.'" class="select chozed-select" data-placeholder="&nbsp;" multiple>';
								$users = get_users('blog_id='.$GLOBALS['blog_id'].'&orderby=nicename');
								if(is_array($users))
								{
									foreach($users as $user)
									{
										$approved = explode(',', str_replace(' ', '', $this->options[$id]));
										$selected = null;
										if(is_array($approved)) foreach($approved as $appr) if($appr == $user->ID) $selected = 'selected';
										$output .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.'</option>';
									}
								}
								$output .= '</select>'.$helplink.'</div>';
							}
							else
							{
								if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label></div>';
								$output .= '<div class="fileaway-overridepassword '.$class.'" id="fileaway-container-'.$id.'">';
								$output .= '<input type="text" class="regular-text fileaway-overridepassword '.$class.'" '.
									'id="'.$id.'" name="fileaway_options['.$id.']" value="'.esc_attr($this->options[$id]).'" />';
								$output .= $helplink.'</div>';								
							}
					break;	
					case 'basefeed':
							$excluded_feeds = $id == 'excluded_feeds' ? true : false;
							if($title) $output .= '<div class="fileaway-label"><label for="'.$id.'">'.$title.'</label>'.
								'<span id="fileaway_add_new_'.$id.'" class="fileaway-add-another fileaway-selectIt">Add Another</span></div>';
							$output .= '<div class="'.$class.'" id="fileaway-container-'.$id.'" style="margin-bottom:30px;">';
							$feeds = fileaway_utility::feeds($excluded_feeds);
							if(count($feeds) < 1)
							{
								$output .=  
									'<div id="fileaway-wrap-'.$id.'_0" data-feed="0" class="fileaway-wrap-base">'.
									'<span id="fileaway-abspath-'.$id.'_0" class="fileaway-abspath'.$chromefix.'">'.$rootpath.'</span> '.
									'<input class="regular-text '.$class.'" type="text" id="'.$id.'_0" name="fileaway_options['.$id.'][]" '.
									'placeholder="'.$holder.'" value="" />'.
									'</div>'.$helplink.'</div>';
							}
							else
							{
								foreach($feeds as $x => $feed)
								{
									$multiclass = $x > 0 ? 'fileaway-subsequent' : null;
									$output .=  
										'<div id="fileaway-wrap-'.$id.'_'.$x.'" data-feed="'.$x.'" class="fileaway-wrap-base '.$multiclass.'">'.
										'<span id="fileaway-abspath-'.$id.'_'.$x.'" class="fileaway-abspath'.$chromefix.'">'.$rootpath.'</span> '.
										'<input class="regular-text '.$class.'" type="text" id="'.$id.'_'.$x.'" name="fileaway_options['.$id.'][]" '.
										'placeholder="'.$holder.'" value="'.esc_attr($feed).'" />'.
										'</div>';
									$output .= $x > 0 ? null : $helplink;
								}
								$output .= '</div>';
							}
				 	break;					
				}
			}
			return $output;
		}
		public function get_settings()
		{
			/* Basic Configuration */
			$this->settings['rootdirectory'] = array(
				'title'		=> 'Set Root Directory',
				'type'		=> 'select',
				'choices'	=> array('install' => 'WP Install Directory', 'siteurl' => 'Site Root Directory'),
				'dflt'		=> 'install'
			);	
			$this->settings['symlinks'] = array(
				'title'		=> 'Allow Symlinks',
				'type'		=> 'select',
				'choices'	=> array('0' => 'No', '1' => 'Yes'),
				'dflt'		=> '0'
			);			
			$this->settings['strictlogin'] = array(
				'title'		=> 'Dynamic Usernames',
				'type'		=> 'select',
				'choices'	=> array('false' => 'Force Lowercase', 'true' => 'Strict Matching'),
				'dflt'		=> 'false'
			);
			$this->settings['base1'] = array(
				'title'		=> 'Base Directory 1',
				'type'		=> 'basedir',
				'class'		=> 'fileaway-basedir fileaway-inline',
				'helplink'	=> false
			);
			$this->settings['bs1name'] = array(
				'holder'	=> 'Display Name',
				'class'		=> 'fileaway-basename fileaway-inline',
				'helplink'	=> false
			);		
			$this->settings['base2'] = array(
				'title'		=> 'Base Directory 2',
				'type'		=> 'basedir',
				'class'		=> 'fileaway-basedir fileaway-inline',
				'helplink'	=> false
			);
			$this->settings['bs2name'] = array(
				'holder'	=> 'Display Name',
				'class'		=> 'fileaway-basename fileaway-inline',
				'helplink'	=> false
			);		
			$this->settings['base3'] = array(
				'title'		=> 'Base Directory 3',
				'type'		=> 'basedir',
				'class'		=> 'fileaway-basedir fileaway-inline',
				'helplink'	=> false
			);
			$this->settings['bs3name'] = array(
				'holder'	=> 'Display Name',
				'class'		=> 'fileaway-basename fileaway-inline',
				'helplink'	=> false
			);		
			$this->settings['base4'] = array(
				'title'		=> 'Base Directory 4',
				'type'		=> 'basedir',
				'class'		=> 'fileaway-basedir fileaway-inline',
				'helplink'	=> false
			);
			$this->settings['bs4name'] = array(
				'holder'	=> 'Display Name',
				'class'		=> 'fileaway-basename fileaway-inline',
				'helplink'	=> false
			);
			$this->settings['base5'] = array(
				'title'		=> 'Base Directory 5',
				'type'		=> 'basedir',
				'class'		=> 'fileaway-basedir fileaway-inline',
				'helplink'	=> false
			);	
			$this->settings['bs5name'] = array(
				'holder'	=> 'Display Name',
				'class'		=> 'fileaway-basename fileaway-inline',
				'helplink'	=> false
			);	
			$this->settings['baseurl'] = array(
				'title'		=> 'Base URL',
				'type'		=> 'select',
				'choices'	=> fileaway_utility::urls(),
				'dflt'		=> rtrim(get_home_url(1), '/'),
			);	
			$this->settings['redirect'] = array(
				'title'		=> 'Guest Redirect URL',
				'holder'	=> 'http://yourdomain.com/registration-page/',
				'class'		=> 'fileaway-permexclusions fileaway-inline',
			);													
			$this->settings['exclusions'] = array(
				'title'		=> 'Permanent Exclusions',
				'holder'	=> '.avi, My Embarrasing Photograph, .tif, My Rough Draft Essay',
				'class'		=> 'fileaway-permexclusions fileaway-inline'
			);
			$this->settings['direxclusions'] = array(
				'title'		=> 'Exclude Directories',
				'holder'	=> 'My Private Files, Weird_Server_Directory_Name, etc.',
				'class'		=> 'fileaway-permexclusions fileaway-inline'
			);
			$this->settings['newwindow'] = array(
				'title'		=> 'New Window',
				'holder'	=> 'Example: .pdf, .jpg, .png, .gif, .mp3, .mp4',
				'class'		=> 'fileaway-newwindow fileaway-inline',
			);			
			$this->settings['encryption_key'] = array(
				'title'		=> 'Encryption Key',
				'class'		=> 'fileaway-encryptionkey fileaway-inline',
				'submit'	=> true
			);
			$this->settings['banner_directory'] = array(
				'title'		=> 'Banner Directory',
				'type'		=> 'rootpath',
				'class'		=> 'fileaway-basedir fileaway-inline',
			);				
			$this->settings['download_prefix'] = array(
				'title'		=> 'Bulk Download File Prefix',
				'class'		=> 'fileaway-download-prefix fileaway-inline',
				'dflt'		=> '',
			);								
			/* Feature Options */				
			$this->settings['modalaccess'] = array(
				'section'	=> 'options',
				'title'		=> 'Modal Access',
				'type'		=> 'select',
				'dflt'		=> 'edit_posts',
				'choices'	=> fileaway_utility::caps()
			);
			$this->settings['tmcerows'] = array(
				'section'	=> 'options',
				'title'		=> 'Button Position',
				'type'		=> 'select',
				'choices'	=> array('' => 'First Row', '_2' => 'Second Row', '_3' => 'Third Row', '_4' => 'Fourth Row'),
				'dflt'		=> '_2'
			);		
			$this->settings['stylesheet'] = array(
				'section'	=> 'options',
				'title'		=> 'Stylesheet Placement',
				'type'		=> 'select',
				'choices'	=> array('footer' => 'Footer when necessary', 'header' => 'Header all the time'),
				'dflt'		=> 'footer'
			);
			$this->settings['javascript'] = array(
				'section'	=> 'options',
				'title'		=> 'Javascript Placement',
				'type'		=> 'select',
				'choices'	=> array('footer' => 'Footer when necessary', 'header' => 'Header all the time'),
				'dflt'		=> 'header'
			);	
			$this->settings['pathinfo'] = array(
				'section'	=> 'options',
				'title'		=> 'Alternative Pathinfo',
				'type'		=> 'select',
				'choices'	=> array('disabled' => 'Disabled', 'enabled' => 'Enabled'),
				'dflt'		=> 'disabled'
			);	
			$this->settings['daymonth'] = array(
				'section'	=> 'options',
				'title'		=> 'Date Display Format',
				'type'		=> 'select',
				'choices'	=> array('md' => 'MM/DD/YYYY', 'dm' => 'DD/MM/YYYY'),
				'dflt'		=> 'md'
			);
			$this->settings['postidcolumn'] = array(
				'section'	=> 'options',
				'title'		=> 'Post ID Column',
				'type'		=> 'select',
				'choices'	=> array('enabled' => 'Enabled', 'disabled' => 'Disabled'),
				'dflt'		=> 'enabled'
			);
			$this->settings['loadusers'] = array(
				'section'	=> 'options',
				'title'		=> 'Load Users',
				'type'		=> 'select',
				'choices'	=> array('false' => 'False', 'true' => 'True'),
				'dflt'		=> 'false'
			);
			$this->settings['adminstyle'] = array(
				'section'	=> 'options',
				'title'		=> 'Admin Style',
				'type'		=> 'select',
				'choices'	=> array('classic' => 'Classic', 'minimal' => 'Minimal'),
				'dflt'		=> 'classic',
				'submit'	=> true
			);		
			/* Custom Styles */
			$this->settings['custom_list_classes'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom List Classes',
				'holder'	=> 'classname1|Display Name 1, classname2|Display Name 2',
				'class'		=> 'fileaway-custom fileaway-inline'
			);
			$this->settings['custom_table_classes'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Table Classes',
				'holder'	=> 'classname1|Display Name 1, classname2|Display Name 2',
				'class'		=> 'fileaway-custom fileaway-inline'
			);
			$this->settings['custom_flightbox_classes'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Flightbox Classes',
				'holder'	=> 'classname1|Display Name 1, classname2|Display Name 2',
				'class'		=> 'fileaway-custom fileaway-inline'
			);			
			$this->settings['custom_color_classes'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Color Classes',
				'holder'	=> 'classname1|Display Name 1, classname2|Display Name 2',
				'class'		=> 'fileaway-custom fileaway-inline'
			);
			$this->settings['custom_accent_classes'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Accent Classes',
				'holder'	=> 'classname1|Display Name 1, classname2|Display Name 2',
				'class'		=> 'fileaway-custom fileaway-inline'
			);
			$this->settings['custom_stylesheet'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Stylesheet',
				'holder'	=> 'my-custom-stylesheet.css',
				'class'		=> 'fileaway-custom-stylesheet fileaway-inline',
			);
			$this->settings['customcss'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Custom Styles',
				'type'		=> 'customcss',
				'class'		=> 'code fileaway-customcss',
				'helplink'	=> false
			);
			$this->settings['css_editor'] = array(
				'section'	=> 'customcss',
				'title'		=> 'Switch Editors',
				'type'		=> 'select',
				'choices'	=> array('syntax' => 'Syntax Highlighted', 'plain' => 'Resizable (Plain Text)'),
				'dflt'		=> 'syntax',
				'class'		=> 'fileaway-custom fileaway-inline',
				'helplink'	=> false,
				'submit'	=> true
			);			
			/*Manager Mode */
			$this->settings['manager_role_access'] = array(
				'section'	=> 'manager',
				'title'		=> 'Access by Role/Capability',
				'type'		=> 'rolescaps',
				'input'		=> 'hidden'
			);
			$this->settings['manager_user_access'] = array(
				'section'	=> 'manager',
				'title'		=> 'Access by User',
				'type'		=> 'users',
				'input'		=> 'hidden'
			);
			$this->settings['managerpassword'] = array(
				'section'	=> 'manager',
				'title'		=> 'Override Password',
				'class'		=> 'fileaway-overridepassword fileaway-inline',
				'input'		=> 'password',
				'submit'	=> true
			);	
			/* Statistics */
			$this->settings['stats'] = array(
				'section'	=> 'stats',
				'title'		=> 'Download Statistics',
				'type'		=> 'select',
				'choices'	=> array('false' => 'Disabled', 'true' => 'Enabled'),
				'dflt'		=> 'false',
			);
			$this->settings['ignore_roles'] = array(
				'section'	=> 'stats',
				'title'		=> 'Ignore Roles/Caps',
				'type'		=> 'rolescaps',
				'input'		=> 'hidden'
			);
			$this->settings['ignore_users'] = array(
				'section'	=> 'stats',
				'title'		=> 'Ignore Users',
				'type'		=> 'users',
				'input'		=> 'hidden'
			);
			$this->settings['recordlimit'] = array(
				'section'	=> 'stats',
				'title'		=> 'Record Limit',
				'holder'	=> 'e.g., 1000',
				'class'		=> 'fileaway-inline fileaway-integer'
			);
			$this->settings['recordlifespan'] = array(
				'section'	=> 'stats',
				'title'		=> 'Record Lifespan',
				'type'		=> 'select',
				'choices'	=> array(
					'1 week' 	=> 'One Week',
					'2 weeks' 	=> 'Two Weeks',
					'1 month' 	=> 'One Month',
					'3 months' 	=> 'Three Months',
					'6 months' 	=> 'Six Months',
					'1 year' 	=> 'One Year',
					'2 years' 	=> 'Two Years',
					'forever' 	=> 'Eternal Life', 
				),
				'dflt'		=> '3months',
			);
			$this->settings['instant_stats'] = array(
				'section'	=> 'stats',
				'title'		=> 'Instant Notifications',
				'type'		=> 'select',
				'choices'	=> array('false' => 'Disabled', 'true' => 'Enabled'),
				'dflt'		=> 'false',
			);			
			$this->settings['instant_sender_name'] = array(
				'section'	=> 'stats',
				'title'		=> 'Sender Name',
				'dflt'		=> get_bloginfo('site_name'),
				'holder'	=> 'Your Name',
			);
			$this->settings['instant_sender'] = array(
				'section'	=> 'stats',
				'title'		=> 'Sender Email',
				'dflt'		=> get_option('admin_email'),
				'holder'	=> 'your@email.com',				
			);
			$this->settings['instant_recipients'] = array(
				'section'	=> 'stats',
				'title'		=> 'Recipient Emails',
				'holder'	=> 'one@email.com, two@email.com, etc.',
				'submit'	=> true
			);
			$this->settings['instant_subject'] = array(
				'section'	=> 'stats',
				'title'		=> 'Email Subject',
				'holder'	=> '%blog% %file% %datetime%',
				'dflt'		=> '%blog% - %file% downloaded at %datetime%',
				'submit'	=> true
			);
			$this->settings['compiled_stats'] = array(
				'section'	=> 'stats',
				'title'		=> 'Compiled Notifications',
				'type'		=> 'select',
				'choices'	=> array(
					'false'			=> 'Disabled',
					'daily'			=> 'Daily',
					'weekly' 		=> 'Weekly',
					'fortnightly'	=> 'Fortnightly',
				),
				'dflt'		=> 'false',
			);
			$this->settings['compiled_sender_name'] = array(
				'section'	=> 'stats',
				'title'		=> 'Sender Name',
				'dflt'		=> get_bloginfo('site_name'),
				'holder'	=> 'Your Name',
			);
			$this->settings['compiled_sender'] = array(
				'section'	=> 'stats',
				'title'		=> 'Sender Email',
				'holder'	=> 'your@email.com',
				'dflt'		=> get_option('admin_email'),
			);
			$this->settings['compiled_recipients'] = array(
				'section'	=> 'stats',
				'title'		=> 'Recipient Emails',
				'holder'	=> 'one@email.com, two@email.com, etc.',
				'submit'	=> true
			);
			$this->settings['compiled_subject'] = array(
				'section'	=> 'stats',
				'title'		=> 'Email Subject',
				'holder'	=> '%blog% %dates%',
				'dflt'		=> '%blog% - Download Stats for %dates%',
				'submit'	=> true
			);
			/* RSS Feeds */
			$this->settings['feeds'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Feed Storage Directory',
				'class'		=> 'fileaway-basedir fileaway-feeds fileaway-inline',
				'type'		=> 'rootpath',
			);	
			$this->settings['basefeeds'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Monitored Directories',
				'class'		=> 'fileaway-basedir fileaway-feeds fileaway-inline',
				'type'		=> 'basefeed',
			);	
			$this->settings['excluded_feeds'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Excluded Sub-Directories',
				'class'		=> 'fileaway-basedir fileaway-feeds fileaway-inline',
				'type'		=> 'basefeed',
			);
			$this->settings['feed_excluded_exts'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Excluded File Extensions',
				'class'		=> 'fileaway-inline',
				'type'		=> 'text',
				'holder'	 => 'ini, html, etc.',
			);
			$this->settings['feed_excluded_files'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Excluded Strings',
				'class'		=> 'fileaway-inline',
				'type'		=> 'text',
				'holder'	 => 'example, test, draft',
			);
			$this->settings['feedlimit'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Feed Limit',
				'class'		=> 'fileaway-inline fileaway-integer',
				'type'		=> 'text',
				'holder'	 => 'e.g., 50',
			);
			$this->settings['feeddates'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Show Dates Modified',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array('true' => 'Enabled', 'false' => 'Disabled'),
				'dflt'		=> 'true',
			);
			$this->settings['feedsize'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Show File Size',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array('true' => 'Enabled', 'false' => 'Disabled'),
				'dflt'		=> 'true',
			);
			$this->settings['feedlinks'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Direct File Links',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array('true' => 'Enabled', 'false' => 'Disabled'),
				'dflt'		=> 'true',
			);							
			$this->settings['recursivefeeds'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Feeds within Feeds',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array('true' => 'Enabled', 'false' => 'Disabled'),
				'dflt'		=> 'true',
			);	
			$this->settings['feedinterval'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Auto Feed Updates',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array(
					'fifteenminutes' => 'Every 15 Minutes', 
					'thirtyminutes' => 'Every 30 Minutes',
					'fortyfiveminutes' => 'Every 45 Minutes',
					'hourly' => 'Every Hour',
					'sixhours' => 'Every 6 Hours',
					'twicedaily' => 'Every 12 Hours',
					'daily' => 'Once a Day',
					'weekly' => 'Once a Week',
				),
				'dflt'		=> 'hourly',
				'submit'	=> true
			);	
			$this->settings['updatefeeds'] = array(
				'section'	=> 'feeds',
				'title'		=> 'Manual Feed Updates',
				'class'		=> 'fileaway-inline',
				'type'		=> 'select',
				'choices'	=> array('true' => 'Update Feeds on Save', 'false' => 'Disable Manual Updates'),
				'dflt'		=> 'true',
				'submit'	=> true
			);						
			/* Database Options */
			$this->settings['reset_options'] = array(
				'section'	=> 'database',
				'title'		=> 'Reset to Defaults',
				'type'		=> 'select',
				'choices'	=> array('' => '', 'reset' => 'Reset on Save'),
				'dflt'		=> ''
			);
			$this->settings['preserve_options'] = array(
				'section'	=> 'database',
				'title'		=> 'On Uninstall',
				'type'		=> 'select',
				'choices'	=> array('preserve' => 'Preserve Settings', 'delete' => 'Delete Settings'),
				'dflt'		=> 'preserve',
				'submit'	=> true
			);
		}
		public function enqueue()
		{
			if(isset($_REQUEST['page']) && $_GET['page'] === 'file-away')
			{	
				wp_enqueue_style('fileaway-admin-css', fileaway_url.'/lib/css/admin.options.css', array(), fileaway_version);
				wp_enqueue_style('fileaway-chozed', fileaway_url.'/lib/js/chosen/admin.chosen.css', array(), '1.1.0');
				wp_enqueue_script('fileaway-chozed', fileaway_url.'/lib/js/chosen/chosen.js', array('jquery'), '1.1.0');
				wp_enqueue_script('fileaway-footable', fileaway_url.'/lib/js/footable.js', array('jquery'), '2.0.1.2');
				wp_enqueue_script('fileaway-filertify', fileaway_url.'/lib/js/filertify.js', array('jquery'), '0.3.11');
				wp_enqueue_script('fileaway-clipboard', fileaway_url.'/lib/js/clipboard/ZeroClipboard.js', array('jquery'), '1.2.1');
				wp_enqueue_script('fileaway-admin-js', fileaway_url.'/lib/js/admin.options.js', array('jquery'), fileaway_version);
				wp_localize_script('fileaway-admin-js', 'fileaway_admin_ajax', array(
					'ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('fileaway-admin-nonce'))
				);
				if($this->options['css_editor'] === 'syntax')
				{
					wp_enqueue_style('fileaway-admin-codemirror', fileaway_url.'/lib/js/codemirror/codemirror.css', array(), '4.4');
					wp_enqueue_script('fileaway-admin-codemirror', fileaway_url.'/lib/js/codemirror/codemirror.js', array('jquery'), '4.4');
					wp_enqueue_script('fileaway-admin-css', fileaway_url.'/lib/js/codemirror/css.js', array('jquery'), '4.4');	
				}
				if($this->options['adminstyle'] == 'minimal')
				{
					wp_enqueue_style('fileaway-admin-minimal', fileaway_url.'/lib/css/admin.options-minimal.css', array(), fileaway_version);
				}
			}
			global $pagenow; 
			if(!in_array($pagenow, array('post.php', 'post-new.php'))) return;
			wp_enqueue_style('fileaway-modal-css', fileaway_url.'/lib/css/admin.modal.css', array(), fileaway_version);
			wp_enqueue_style('fileaway-chozed', fileaway_url.'/lib/js/chosen/admin.chosen.css', array(), '1.1.0');
		}
		public function deregister()
		{
			if(isset($_REQUEST['page']) && $_GET['page'] === 'file-away')
			{ 
				wp_deregister_style('jquery-ui-style-plugin');	
				if(is_plugin_active("wp-editor/wpeditor.php"))
				{
					wp_deregister_style('wpeditor');
					wp_deregister_style('fancybox');
					wp_deregister_style('codemirror');
					wp_deregister_style('codemirror_dialog');
					wp_deregister_style('codemirror_themes');
					wp_deregister_script('wpeditor');
					wp_deregister_script('wp-editor-posts-jquery');
					wp_deregister_script('fancybox');
					wp_deregister_script('codemirror');
					wp_deregister_script('codemirror_php');
					wp_deregister_script('codemirror_javascript');
					wp_deregister_script('codemirror_css');
					wp_deregister_script('codemirror_xml');
					wp_deregister_script('codemirror_clike');
					wp_deregister_script('codemirror_dialog');
					wp_deregister_script('codemirror_search');
					wp_deregister_script('codemirror_searchcursor');
					wp_deregister_script('codemirror_mustache'); 
				}
			}
		}
	}
}