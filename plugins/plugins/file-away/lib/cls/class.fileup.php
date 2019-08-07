<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('fileup'))
{
	class fileup extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('fileup', array($this, 'sc'));
		}
		public function sc($atts)
		{
			$uid = rand(0, 9999); 
			$get = new fileaway_definitions;
			if(isset($atts['style'])) $atts['theme'] = $atts['style']; // legacy
			if(isset($atts['uploader']) && $atts['uploader'] === 'true') $atts['uploader'] = 'name'; // legacy
			extract($get->pathoptions);
			extract($this->correct(wp_parse_args($atts, $this->fileup), $this->shortcodes['fileup']));
			if($devices == 'mobile' && !$get->is_mobile) return;
			elseif($devices == 'desktop' && $get->is_mobile) return;
			if(!fileaway_utility::visibility($hidefrom, $showto)) return;
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			// Build Initial Directory
			$base = $base == 's2member-files' ? fileaway_utility::replacefirst(str_replace('\\','/',WP_PLUGIN_DIR.'/s2member-files'), $chosenpath, '') : str_replace('\\','/',$this->op['base'.$base]);
			$base = trim($base, '/'); 
			$base = trim($base, '/');
			$sub = $sub ? trim(str_replace('\\','/',$sub), '/') : false; 
			$dir = $sub ? $base.'/'.$sub : $base;
			extract(fileaway_utility::dynamicpaths($dir));
			$dir = str_replace('//', '/', $dir);
			$debugpath = $chosenpath.$dir;
			$dir = $problemchild ? $install.$dir : $dir;
			if(!is_dir($dir) && $makedir && (!$private_content || ($private_content && $logged_in && stripos($dir, 'fa-nullmeta') === false))&& mkdir($rootpath.$dir, 0775, true)) fileaway_utility::indexmulti($rootpath.$dir, $chosenpath);
			if($private_content && !is_dir("$dir")) return;
			if(!fileaway_utility::realpath($dir,$rootpath,$chosenpath)) return;
			$start = $dir; 
			if($matchdrawer)
			{
				$fixedlocation = true;
				$drawerid = $matchdrawer && $matchdrawer !== 'true' ? $matchdrawer : null;
				include fileaway_dir.'/lib/inc/inc.open-drawer.php';
				$start = $dir; 
			}
			$pathparts = explode('/', $start); 
			$basename = end($pathparts);
			$fixed = $start; 
			$fixed = $fixedlocation ? ($problemchild ? fileaway_utility::replacefirst($fixed, $install, '') : $fixed) : null;
			$path = '<input type="hidden" id="ssfa-upload-actionpath-'.$uid.'" value="'.$fixed.'" data-basename="'.$basename.'" data-start="'.$start.'" />';
			$location_nonce = 'fileaway-location-nonce-'.base64_encode(trim(trim($rootpath.$start,'/'),'\\'));
			$path .= '<input type="hidden" id="location_nonce_'.$uid.'" data-uid="'.$uid.'" value="'.wp_create_nonce($location_nonce).'" />';			
			// File Type Permissions
			$types = array(); 
			if($filetypes)
			{
				$filetypes = preg_split('/(, |,)/', $filetypes, -1, PREG_SPLIT_NO_EMPTY); 
				if(is_array($filetypes)) foreach($filetypes as $type) $types[] = strtolower(str_replace(array('.',' '), '', $type));
			}
			if($filegroups)
			{
				$groups = preg_split('/(, |,)/', strtolower(str_replace(' ', '', $filegroups)), -1, PREG_SPLIT_NO_EMPTY);
				foreach($get->filegroups as $group => $discard) if(in_array($group, $groups)) $types = array_merge($types, $get->filegroups[$group][2]);
			}
			if(count($types) > 0)
			{ 
				$types = array_unique($types); 
				asort($types); 
				$filetypes = '["'.implode('", "',$types).'"]'; 
			}
			else $filetypes = false; 
			$permitted = ($filetypes || $filegroups) && $action == 'permit' ? $filetypes : 'false';
			$prohibited = ($filetypes || $filegroups) && $action == 'prohibit' ? $filetypes : 'false';	
			// Configure Settings
			$name = $name ? $name : "ssfa-meta-container-$uid";
			$width = is_numeric(preg_replace('[\D]', '', $width)) ? preg_replace('[\D]', '', $width) : '100'; 
			$width = "width:$width$perpx;";
			if($width == '100' && $perpx == '%') $align = 'none';
			$clearfix = $align == 'none' ? '<div class="ssfa-clearfix"></div>' : null;
			$float = ' float:'.$align.';';  
			$margin = ($width !== 'width:100%;' ? ($align === 'right' ? ' margin-left:15px;' : ' margin-right:15px;') : null);
			$inlinestyle = $width.$float.$margin;
			$multiple = $single ? '' : ' multiple=multiple';
			$addfiles = $single ? __('+ Add File', 'file-away') : __('+ Add Files', 'file-away');
			$overwrite = $overwrite == 'true' ? 'true' : 'false';
			$uploadlabel = $uploadlabel ? $uploadlabel : __('File Up &#10138;', 'file-away');
			$pathcheck = $problemchild ? fileaway_utility::replacefirst($start, $install, '') : $start;
			$uploadedby = $uploader ? get_current_user_id() : 0;
			$uploadtype = $uploader ? $uploader : 'false';
			// Configure Max File Size Setting
			$max_file_size = trim(preg_replace('[\D]', '', $maxsize));
			$max_size_type = trim(strtolower($maxsizetype));
			$max_file_size = is_numeric($max_file_size) ? $max_file_size : 10; 
			$max_size_type = in_array($max_size_type, array('k','m','g')) ? $max_size_type : 'm';
			$ms = $max_file_size.$max_size_type;
			$ms = fileaway_utility::ini(false, true, false, $ms);
			$pms = fileaway_utility::ini('post_max_size');
			$ums = fileaway_utility::ini('upload_max_filesize');
			$maxsize = $pms < $ms ? $pms : $ms;
			$maxsize = $ums < $maxsize ? $ums : $maxsize;
			// Initialize Settings
			$fixedsetting = $fixedlocation ? '"'.$fixed.'"' : 'false';
			$initialize = 
				'<script> '.
						'FileUpConfig['.$uid.'] = { '.
							'form_id: "ssfa_fileup_form_'.$uid.'", '.
							'uid: '.$uid.', '.
							'nonce: "'.wp_create_nonce('fileaway-fileup-nonce').'", '.
							'container: "'.$name.'", '.
							'table: "'.$theme.'", '.
							'iconcolor: "'.$iconcolor.'", '.
							'maxsize: '.$maxsize.', '.
							'permitted: '.$permitted.', '.
							'prohibited: '.$prohibited.', '.
							'fixed: '.$fixedsetting.', '.
							'pathcheck: "'.$pathcheck.'", '.
							'uploader: '.$uploadedby.', '.
							'identby: "'.$uploadtype.'", '.
							'overwrite: "'.$overwrite.'", '.
							'loading: "'.fileaway_url.'/lib/img/ajax.gif" '.
						'}; '.
				'</script>';
			$fadeit = $fadein ? ($fadein == 'opacity' ? 'opacity:0;' : 'display:none;') : null;
			if($fadein)
			{
				$fadescript = $fadein == 'opacity' ? '.animate({opacity:"1"}, '.$fadetime.');' : '.fadeIn('.$fadetime.');';
				$initialize .= '<script> jQuery(document).ready(function($){ setTimeout(function(){ $("div#ssfa_fileup_container_'.$uid.'")'.$fadescript.' }, 1000); }); </script>';
			}				
			// Form Output
			if(!is_dir($debugpath)) return current_user_can('administrator') 
				? __('File Up Admin Notice: The initial directory specified does not exist:', 'file-away').'<br>'.$debugpath 
				: null;
			$dropdown = $fixedlocation 
				? null 
				: '<div id="ssfa-fileup-path-container-'.$uid.'" style="display:inline-block; float:left;">'.
					'<div id="ssfa-fileup-directories-select-container-'.$uid.'">'.
						'<label for="ssfa-fileup-directories-select-'.$uid.'" '.
							'style="display:block!important; margin-bottom:5px!important; text-align:left;">'.__('Destination Directory', 'file-away').
						'</label>'.
						'<select name="ssfa-fileup-directories-select-'.$uid.'" id="ssfa-fileup-directories-select-'.$uid.'" '.
							'class="chozed-select ssfa-fileup-directories-select" data-placeholder="&nbsp;">'.
							'<option></option>'.
							'<option value="'.$start.'">'.$basename.'</option>'.
						'</select>'.
						'<br>'.
						'<div id="ssfa-fileup-action-path-'.$uid.'" style="margin-top:5px; min-height:25px;">'.
							'<img id="ssfa-fileup-action-ajax-loading-'.$uid.'" src="'.fileaway_url.'/lib/img/ajax.gif" '.
								'style="width:15px; margin:0 0 0 5px!important; box-shadow:none!important; display:none;">'.
						'</div>'.
					'</div>'.
				'</div>';
			$form = 
				$clearfix.
				'<div id="ssfa_fileup_container_'.$uid.'" class="ssfa_fileup_container '.$class.'" data-uid="'.$uid.'" data-mn="'.wp_create_nonce('fileaway-manager-nonce').'" style="'.$inlinestyle.' '.$fadeit.'">'.
					'<form name="ssfa_fileup_form_'.$uid.'" id="ssfa_fileup_form_'.$uid.'" action="javascript:void(0);" enctype="multipart/form-data">'
						.$path.$dropdown.
						'<div class="ssfa_fileup_buttons_container" style="text-align:right;">'.
							'<span class="ssfa_fileup_wrapper" style="text-align:left;">'.
								'<input type="file" name="ssfa_fileup_files_'.$uid.'[]" id="ssfa_fileup_files_'.$uid.'" '.
									'class="ssfa_hidden_browse"'.$multiple.' data-uid="'.$uid.'" />'.
								'<span class="ssfa_add_files">'.$addfiles.'</span>'.
								'<span id="ssfa_submit_upload_'.$uid.'" data-uid="'.$uid.'">'.$uploadlabel.'</span>'.
							'</span>'.
						'</div>'.
					'</form>'.
					'<div id="ssfa_fileup_files_container_'.$uid.'" class="ssfa_fileup_files_container"></div>'.
					'<span id="ssfa_rf_'.$uid.'" style="display:none;"></span>'.
				'</div>'.
				$clearfix;
			return $initialize.$form;
		}		
	}
}