	<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_admin') && !class_exists('fileaway_modal'))
{
	class fileaway_modal extends fileaway_admin
	{
		public $shortcodes;
		public $optioninfo;
		public function __construct()
		{
			$get = new fileaway_attributes;
			$this->shortcodes = $get->shortcodes;
			$get = new fileaway_tutorials;
			$this->optioninfo = $get->optioninfo;
			unset($get);
			$this->params();
			$this->modal();
		}
		public function modal()
		{
			$output = null;
			$info = null; 
			$constainers = array(); 
			$sections = array(); 
			$fields = array(); 
			$i = 0; 
			$x = 0;
			$shortcode_select_options = null;
			foreach($this->shortcodes as $shortcode => $array)
			{
				$numtypes = count($array['types']);	
				$shortcode_select_options .= '<option data-types="'.$numtypes.'" id="fileaway_shortcode_'.$shortcode.'" value="'.$shortcode.'">'.$array['option'].'</option>';
				$instructions = $shortcode == 'fileaframe' 
					?	"<div class='clearfix' style='width:95%'>".
						"<h3>File Away iframe Instructions</h3>".
						"<ol style='font-size:11px;'>".
							"<li>Create a new page and using the Template dropdown under Page Attributes, set the template to File Away iframe.</li>".
							"<li>Under Sortable Data Tables, insert your [fileaway] shortcode with the Directory Tree setting enabled, and assign it a Unique Name.</li>".
							"<li>Save the page and remember the page slug.</li>".
							"<li>Edit another page with your normal template, and insert the above File Away iframe shortcode, with the page slug from the other page inserted ".
								"into the Source URL field, and the unique name from the [fileaway] shortcode inserted into the Unique Name field. Click on all the info links to ".
								"see what each setting does.</li>".
							"<li>Done! Now you\'ve got a Directory Tree table on your front-end page, ".
								"that will navigate through the directories without refreshing the parent page.</li>".
						"</ol>".
						"</div>" 
					: 	null;
				foreach($array['types'] as $type)
				{
					$prefix = $shortcode.'_'.$type;
					$datacontainer = $numtypes > 1 ? $shortcode.'_'.$type : $shortcode;	
					$containers[$shortcode][$type][] = 
						'<div id="options-container-'.$datacontainer.'" data-container="'.$datacontainer.'" '.
							'data-sc="'.$shortcode.'" data-type="'.$type.'" class="fileaway-wrap" style="display:none;">'.
						'<div class="fileaway-tabs"><ul class="fileaway-tabs-nav">';
					$ops = null;
					foreach($array as $key => $a)
					{
						if($key == 'type' || $key == 'options' || $key == 'sections' || $key == 'option' || $key == 'types') continue;
						if($numtypes > 1 && !isset($a[$type])) continue;
						$src = $numtypes > 1 ? $a[$type] : $a;
						$newline = strpos($a['class'], 'fileaway-first-inline') !== false ? '<div style="display:block; visibility:hidden;"></div>' : null;
						$style = isset($a['style']) ? $a['style'] : null;
						$section = $a['section'];
						$infolink = $key == 'theme' ? null : '<span class="link-fileaway-help-'.$key.' fileaway-helplink fileaway-help-iconinfo2" data-info="'.$key.'"></span>';
						if($a['element'] == 'text')
						{
							$fields[$shortcode][$type][$section][$i] = $newline.'<div style="'.$style.'" class="fileaway-inline '.$a['class'].'" '.
								'id="fileaway-container-'.$prefix.'_'.$section.'_'.$key.'_'.$i.'">';
							$fields[$shortcode][$type][$section][$i] .= '<div style="width:100%; text-align:right; margin: 2px 0 3px;">'.
								'<label for="'.$prefix.'_'.$key.'">'.$infolink.$a['label'].'</label></div>';
		 					$fields[$shortcode][$type][$section][$i] .=  
								'<input class="fileaway-text " type="text" id="'.$prefix.'_'.$key.'" '.
								'name="'.$prefix.'_'.$key.'" placeholder="" value="" data-attribute="'.$key.'" />'.
								'</div>';
						}
						elseif($a['element'] == 'select' || $a['element'] == 'multiselect')
						{				
							$ops = $numtypes > 1 ? $a[$type]['options'] : $a['options'];
							$multiple = $a['element'] == 'multiselect' ? ' multiple=multiple' : null;
							$fields[$shortcode][$type][$section][$i]  = $newline.'<div style="'.$style.'" class="fileaway-inline '.$a['class'].'" '.
								'id="fileaway-container-'.$prefix.'_'.$section.'_'.$key.'_'.$i.'">';
							$fields[$shortcode][$type][$section][$i] .= '<div style="width:100%; text-align:right; margin: 2px 0 3px;">'.
								'<label for="'.$prefix.'_'.$key.'">'.$infolink.$a['label'].'</label></div>';
							$fields[$shortcode][$type][$section][$i] .= '<select id="'.$prefix.'_'.$key.'" class="select '.
								'chozed-select" data-placeholder="&nbsp;" name="'.$prefix.'_'.$key.'" data-attribute="'.$key.'"'.$multiple.'>'.
								'<option value=""></option>';
							if(isset($ops) && is_array($ops))
							{
								foreach($ops as $value => $option)
								{
									$fields[$shortcode][$type][$section][$i] .= '<option value="'.esc_attr($value).'">'.stripslashes($option).'</option>';
								}
							}
							$fields[$shortcode][$type][$section][$i] .= '</select></div>';
						}
						$i++;
					}
					$initclass = 0; $tabindex = 0; $panelindex = 0;
					foreach($array['sections'] as $key => $section)
					{
						if($type == 'list' && $key == 'bannerize') continue;
						$initclass = $tabindex < 1 ? ' state-active' : '';
						$tabs[$shortcode][$type][] = 
							'<li class="'.$key.$initclass.'" data-tab="'.$key.'"><a href="javascript:" data-tab="'.$key.'" id="fileaway-tab-'.$key.'">'.$section.'</a></li>';
							$tabindex++;
						$initdisplay = $panelindex < 1 || count($array['sections']) < 2 ? 'block;' : 'none;'; 
						$sections[$shortcode][$type][] = '<div class="fileaway-tabs-panel" id="fileaway-panel-'.$key.'" style="display:'.$initdisplay.'">'.
						implode(' ',$fields[$shortcode][$type][$key]).'</div>';
						$panelindex++;
					}
					foreach($tabs[$shortcode][$type] as $tab)
					{
						$containers[$shortcode][$type][] .= $tab;
					}
					$containers[$shortcode][$type][] .= '</ul></div>';
					foreach($sections[$shortcode][$type] as $section)
					{
						$containers[$shortcode][$type][] .= $section;
					}
					$output .= implode(' ', $containers[$shortcode][$type]).$instructions.'</div>';
				}
			}
			$shortcode_select = 
				'<div class="fileaway-first-inline fileaway_shortcode_select" id="fileaway_shortcode_select">'.
					'<label for="fileaway_shortcode_select">Select Shortcode</label>'.
					'<select id="fileaway_shortcode_select" class="select chozed-select" data-placeholder="&nbsp;" name="fileaway_shortcode_select">'.
						'<option value=""></option>'.$shortcode_select_options.
					'</select>'.
				'</div>';
			$type_select = 
				'<div style="display:block; visibility:hidden; margin:20px 0;"></div>'.
				'<div class="fileaway-first-inline fileaway_type_select" style="display:none;" id="fileaway_type_select">'.
					'<label for="fileaway_type_select">Select Type</label>'.
					'<select id="fileaway_type_select" class="select chozed-select" data-placeholder="&nbsp;" name="fileaway_type_select">'.
						'<option value=""></option>'.
						'<option value="list">Sorted List</option>'.
						'<option value="table">Sortable Data Table</option>'.
					'</select>'.
				'</div>';
			foreach($this->optioninfo as $option => $array)
				$info .= 
					'<div id="fileaway-help-'.$option.'" class="fileaway-help-backdrop">'.
						'<div class="fileaway-help-content">'.
							'<div class="fileaway-help-close fileaway-help-iconclose2"></div>'.
							'<h4>'.$this->optioninfo[$option]['heading'].'</h4>'.
							$this->optioninfo[$option]['info'].
						'</div>'.
					'</div>';	
			$form = 
				'<div id="fileawaymodal-form" style="width:100%;">'.
				'<form id="fileawaymodal-form">'.
					'<table style="width:100%"><tr><td>'.
						'<div id="fileawaymodal-metacontainer" style="width:100%;">'.
							'<div class="clearfix" style="width:100%">'.
								'<img id="fileaway_banner_fileaway" src="'.fileaway_url.'/lib/img/fileaway_banner.png" '.
									'style="width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_attachaway" src="'.fileaway_url.'/lib/img/attachaway_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_fileup" src="'.fileaway_url.'/lib/img/fileup_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_fileaway_values" src="'.fileaway_url.'/lib/img/fileaway_values_banner.png" '.
									'style="display:none; height:60px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_formaway_open" src="'.fileaway_url.'/lib/img/formaway_banner.png" '.
									'style="display:none; height:60px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_formaway_row" src="'.fileaway_url.'/lib/img/formaway_banner.png" '.
									'style="display:none; height:60px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_formaway_cell" src="'.fileaway_url.'/lib/img/formaway_banner.png" '.
									'style="display:none; height:60px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_formaway_close" src="'.fileaway_url.'/lib/img/formaway_banner.png" '.
									'style="display:none; height:60px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_fileaframe" src="'.fileaway_url.'/lib/img/fileaway_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_stataway" src="'.fileaway_url.'/lib/img/stataway_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_stataway_user" src="'.fileaway_url.'/lib/img/stataway_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								'<img id="fileaway_banner_fileaway_tutorials" src="'.fileaway_url.'/lib/img/fileaway_banner.png" '.
									'style="display:none; width:300px; position:absolute; right:20px; top:35px; margin:0;">'.
								$shortcode_select.$type_select.		
								'<span class="fileaway-selectIt" id="fileaway-shortcode-submit">Insert Shortcode</span>'.
							'</div><br>'.
						'</div>'.
					'</td></tr></table>'.$output.$info.				
				'</form>'.
				'</div>';
			include fileaway_dir.'/lib/js/chosen/modal.chosen.js.php';
			echo $form;			
			include fileaway_dir.'/lib/js/modal.js.php';
		}
		public function params()
		{
			// FILE AWAY
			$fileaway = $this->shortcodes['fileaway'];
			$fileaway['option'] = 'Directory Files';
			$fileaway['types'] = array('list', 'table');
			$fileaway['sections'] = array(
				'config' => 'Config',
				'modes' => 'Modes',
				'filters' => 'Filters',
				'styles' => 'Styles',
				'bannerize' => 'Bannerize'				
			);
			// Config
			$fileaway['base']['section'] = 'config';
			$fileaway['base']['label'] = 'Base Directory';
			$fileaway['base']['element'] = 'select';
			$fileaway['base']['class'] = '';
			$fileaway['sub']['section'] = 'config';
			$fileaway['sub']['label'] = 'Sub Directory';
			$fileaway['sub']['element'] = 'text';
			$fileaway['sub']['class'] = '';
			$fileaway['makedir']['section'] = 'config';
			$fileaway['makedir']['label'] = 'Make Directory';
			$fileaway['makedir']['element'] = 'select';
			$fileaway['makedir']['class'] = 'fileaway-half';
			$fileaway['name']['section'] = 'config';
			$fileaway['name']['label'] = 'Unique Name';
			$fileaway['name']['element'] = 'text';
			$fileaway['name']['class'] = 'fileaway-half';
			$fileaway['paginate']['section'] = 'config';
			$fileaway['paginate']['label'] = 'Paginate';
			$fileaway['paginate']['element'] = 'select';
			$fileaway['paginate']['class'] = 'fileaway-half';
			$fileaway['pagesize']['section'] = 'config';
			$fileaway['pagesize']['label'] = '# per page';
			$fileaway['pagesize']['element'] = 'text';
			$fileaway['pagesize']['class'] = 'fileaway-half';
			$fileaway['search']['section'] = 'config';
			$fileaway['search']['label'] = 'Searchable';
			$fileaway['search']['element'] = 'select';
			$fileaway['search']['class'] = 'fileaway-half';
			$fileaway['searchlabel']['section'] = 'config';
			$fileaway['searchlabel']['label'] = 'Search Label';
			$fileaway['searchlabel']['element'] = 'text';
			$fileaway['searchlabel']['class'] = 'fileaway-half';	
			$fileaway['filenamelabel']['section'] = 'config';
			$fileaway['filenamelabel']['label'] = 'File Name Label';
			$fileaway['filenamelabel']['element'] = 'text';
			$fileaway['filenamelabel']['class'] = 'fileaway-half';	
			$fileaway['datelabel']['section'] = 'config';
			$fileaway['datelabel']['label'] = 'Date Label';
			$fileaway['datelabel']['element'] = 'text';
			$fileaway['datelabel']['class'] = 'fileaway-half';	
			$fileaway['customdata']['section'] = 'config';
			$fileaway['customdata']['label'] = 'Custom Column Name(s)';
			$fileaway['customdata']['element'] = 'text';
			$fileaway['customdata']['class'] = '';
			$fileaway['metadata']['section'] = 'config';
			$fileaway['metadata']['label'] = 'Metadata Storage';
			$fileaway['metadata']['element'] = 'select';
			$fileaway['metadata']['class'] = '';
			$fileaway['sortfirst']['section'] = 'config';
			$fileaway['sortfirst']['label'] = 'Initial Sort';
			$fileaway['sortfirst']['element'] = 'select';
			$fileaway['sortfirst']['class'] = '';
			$fileaway['mod']['section'] = 'config';
			$fileaway['mod']['label'] = 'Date Modified';
			$fileaway['mod']['element'] = 'select';
			$fileaway['mod']['class'] = 'fileaway-half';
			$fileaway['size']['section'] = 'config';
			$fileaway['size']['label'] = 'File Size';
			$fileaway['size']['element'] = 'select';
			$fileaway['size']['class'] = 'fileaway-half';
			$fileaway['nolinks']['section'] = 'config';
			$fileaway['nolinks']['label'] = 'Disable Links';
			$fileaway['nolinks']['element'] = 'select';
			$fileaway['nolinks']['class'] = 'fileaway-half';
			$fileaway['redirect']['section'] = 'config';
			$fileaway['redirect']['label'] = 'Guest Redirect';
			$fileaway['redirect']['element'] = 'select';
			$fileaway['redirect']['class'] = 'fileaway-half';			
			$fileaway['showrss']['section'] = 'config';
			$fileaway['showrss']['label'] = 'Show RSS Links';
			$fileaway['showrss']['element'] = 'select';
			$fileaway['showrss']['class'] = 'fileaway-half';
			$fileaway['fadein']['section'] = 'config';
			$fileaway['fadein']['label'] = 'Fade In';
			$fileaway['fadein']['element'] = 'select';
			$fileaway['fadein']['class'] = 'fileaway-half';
			$fileaway['fadetime']['section'] = 'config';
			$fileaway['fadetime']['label'] = 'Fade Time';
			$fileaway['fadetime']['element'] = 'select';
			$fileaway['fadetime']['class'] = 'fileaway-half';
			$fileaway['class']['section'] = 'config';
			$fileaway['class']['label'] = 'CSS Class';
			$fileaway['class']['element'] = 'text';
			$fileaway['class']['class'] = 'fileaway-half';
			$fileaway['debug']['section'] = 'config';
			$fileaway['debug']['label'] = 'Debug';
			$fileaway['debug']['element'] = 'select';
			$fileaway['debug']['class'] = 'fileaway-half';
			$fileaway['s2skipconfirm']['section'] = 'config';
			$fileaway['s2skipconfirm']['label'] = 'Skip Confirmation';
			$fileaway['s2skipconfirm']['element'] = 'select';
			$fileaway['s2skipconfirm']['style'] = 'display:none';
			$fileaway['s2skipconfirm']['class'] = '';
			// Modes
			$fileaway['stats']['section'] = 'modes';
			$fileaway['stats']['label'] = 'Download Stats';
			$fileaway['stats']['element'] = 'select';
			$fileaway['stats']['class'] = 'fileaway-half';
			$fileaway['bulkdownload']['section'] = 'modes';
			$fileaway['bulkdownload']['label'] = 'Bulk Download';
			$fileaway['bulkdownload']['element'] = 'select';
			$fileaway['bulkdownload']['class'] = 'fileaway-half';
			$fileaway['playback']['section'] = 'modes';
			$fileaway['playback']['label'] = 'Audio Playback';
			$fileaway['playback']['element'] = 'select';
			$fileaway['playback']['class'] = 'fileaway-half';
			$fileaway['playbackpath']['section'] = 'modes';
			$fileaway['playbackpath']['label'] = 'Playback Path';
			$fileaway['playbackpath']['element'] = 'text';
			$fileaway['playbackpath']['style'] = 'display:none';
			$fileaway['playbackpath']['class'] = '';
			$fileaway['playbacklabel']['section'] = 'modes';
			$fileaway['playbacklabel']['label'] = 'Playback Label';
			$fileaway['playbacklabel']['element'] = 'text';
			$fileaway['playbacklabel']['style'] = 'display:none';
			$fileaway['playbacklabel']['class'] = 'fileaway-half';
			$fileaway['onlyaudio']['section'] = 'modes';
			$fileaway['onlyaudio']['label'] = 'Audio Files Only';
			$fileaway['onlyaudio']['element'] = 'select';
			$fileaway['onlyaudio']['style'] = 'display:none';
			$fileaway['onlyaudio']['class'] = 'fileaway-half';
			$fileaway['loopaudio']['section'] = 'modes';
			$fileaway['loopaudio']['label'] = 'Loop Audio';
			$fileaway['loopaudio']['element'] = 'select';
			$fileaway['loopaudio']['style'] = 'display:none';
			$fileaway['loopaudio']['class'] = 'fileaway-half';			
			$fileaway['flightbox']['section'] = 'modes';
			$fileaway['flightbox']['label'] = 'FlightBox';
			$fileaway['flightbox']['element'] = 'select';
			$fileaway['flightbox']['class'] = 'fileaway-half';
			$fileaway['boxtheme']['section'] = 'modes';
			$fileaway['boxtheme']['label'] = 'Box Theme';
			$fileaway['boxtheme']['element'] = 'select';
			$fileaway['boxtheme']['style'] = 'display:none';
			$fileaway['boxtheme']['class'] = 'fileaway-half';
			$fileaway['nolinksbox']['section'] = 'modes';
			$fileaway['nolinksbox']['label'] = 'Box Links';
			$fileaway['nolinksbox']['element'] = 'select';
			$fileaway['nolinksbox']['style'] = 'display:none';
			$fileaway['nolinksbox']['class'] = 'fileaway-half';
			$fileaway['maximgwidth']['section'] = 'modes';
			$fileaway['maximgwidth']['label'] = 'Max Image Width';
			$fileaway['maximgwidth']['element'] = 'text';
			$fileaway['maximgwidth']['style'] = 'display:none';
			$fileaway['maximgwidth']['class'] = 'fileaway-half';
			$fileaway['maximgheight']['section'] = 'modes';
			$fileaway['maximgheight']['label'] = 'Max Image Height';
			$fileaway['maximgheight']['element'] = 'text';
			$fileaway['maximgheight']['style'] = 'display:none';
			$fileaway['maximgheight']['class'] = 'fileaway-half';
			$fileaway['videowidth']['section'] = 'modes';
			$fileaway['videowidth']['label'] = 'Video Width';
			$fileaway['videowidth']['element'] = 'text';
			$fileaway['videowidth']['style'] = 'display:none';
			$fileaway['videowidth']['class'] = 'fileaway-half';
			$fileaway['encryption']['section'] = 'modes';
			$fileaway['encryption']['label'] = 'Encrypted Links';
			$fileaway['encryption']['element'] = 'select';
			$fileaway['encryption']['class'] = 'fileaway-half';
			$fileaway['recursive']['section'] = 'modes';
			$fileaway['recursive']['label'] = 'Recursive Iteration';
			$fileaway['recursive']['element'] = 'select';
			$fileaway['recursive']['class'] = 'fileaway-half';
			$fileaway['directories']['section'] = 'modes';
			$fileaway['directories']['label'] = 'Directory Tree Nav';
			$fileaway['directories']['element'] = 'select';
			$fileaway['directories']['class'] = 'fileaway-half';
			$fileaway['manager']['section'] = 'modes';
			$fileaway['manager']['label'] = 'Manager Mode';
			$fileaway['manager']['element'] = 'select';
			$fileaway['manager']['class'] = 'fileaway-half';
			$fileaway['drawerid']['section'] = 'modes';
			$fileaway['drawerid']['label'] = 'Drawer ID#';
			$fileaway['drawerid']['element'] = 'text';
			$fileaway['drawerid']['style'] = 'display:none';
			$fileaway['drawerid']['class'] = 'fileaway-half';
			$fileaway['excludedirs']['section'] = 'modes';
			$fileaway['excludedirs']['label'] = 'Exclude Directories';
			$fileaway['excludedirs']['element'] = 'text';
			$fileaway['excludedirs']['style'] = 'display:none';
			$fileaway['excludedirs']['class'] = '';
			$fileaway['onlydirs']['section'] = 'modes';
			$fileaway['onlydirs']['label'] = 'Only These Directories';
			$fileaway['onlydirs']['element'] = 'text';
			$fileaway['onlydirs']['style'] = 'display:none';
			$fileaway['onlydirs']['class'] = '';
			$fileaway['drawericon']['section'] = 'modes';
			$fileaway['drawericon']['label'] = 'Directory Icon';
			$fileaway['drawericon']['element'] = 'select';
			$fileaway['drawericon']['style'] = 'display:none';
			$fileaway['drawericon']['class'] = 'fileaway-half';
			$fileaway['drawerlabel']['section'] = 'modes';
			$fileaway['drawerlabel']['label'] = 'Drawer Col Label';
			$fileaway['drawerlabel']['element'] = 'text';
			$fileaway['drawerlabel']['style'] = 'display:none';
			$fileaway['drawerlabel']['class'] = 'fileaway-half';
			$fileaway['parentlabel']['section'] = 'modes';
			$fileaway['parentlabel']['label'] = 'Parent Dir Pseudonym';
			$fileaway['parentlabel']['element'] = 'text';
			$fileaway['parentlabel']['style'] = 'display:none';
			$fileaway['parentlabel']['class'] = '';									
			$fileaway['password']['section'] = 'modes';
			$fileaway['password']['label'] = 'Override Password';
			$fileaway['password']['element'] = 'text';
			$fileaway['password']['style'] = 'display:none';
			$fileaway['password']['class'] = '';
			$fileaway['user_override']['section'] = 'modes';
			$fileaway['user_override']['label'] = 'User Access Override';
			$fileaway['user_override']['element'] = 'text';
			$fileaway['user_override']['style'] = 'display:none';
			$fileaway['user_override']['class'] = '';
			$fileaway['role_override']['section'] = 'modes';
			$fileaway['role_override']['label'] = 'Role/Cap Access Override';
			$fileaway['role_override']['element'] = 'multiselect';
			$fileaway['role_override']['style'] = 'display:none';
			$fileaway['role_override']['class'] = 'fileaway-first-inline';
			$fileaway['dirman_access']['section'] = 'modes';
			$fileaway['dirman_access']['label'] = 'Directory Management Access';
			$fileaway['dirman_access']['element'] = 'multiselect';
			$fileaway['dirman_access']['style'] = 'display:none';
			$fileaway['dirman_access']['class'] = '';
			// Filters
			$fileaway['exclude']['section'] = 'filters';
			$fileaway['exclude']['label'] = 'Exclude Specific';
			$fileaway['exclude']['element'] = 'text';
			$fileaway['exclude']['class'] = '';
			$fileaway['include']['section'] = 'filters';
			$fileaway['include']['label'] = 'Include Specific';
			$fileaway['include']['element'] = 'text';
			$fileaway['include']['class'] = '';
			$fileaway['only']['section'] = 'filters';
			$fileaway['only']['label'] = 'Show Only Specific';
			$fileaway['only']['element'] = 'text';
			$fileaway['only']['class'] = '';
			$fileaway['images']['section'] = 'filters';
			$fileaway['images']['label'] = 'Images';
			$fileaway['images']['element'] = 'select';
			$fileaway['images']['class'] = 'fileaway-half';
			$fileaway['code']['section'] = 'filters';
			$fileaway['code']['label'] = 'Code Docs';
			$fileaway['code']['element'] = 'select';
			$fileaway['code']['class'] = 'fileaway-half';
			$fileaway['show_wp_thumbs']['section'] = 'filters';
			$fileaway['show_wp_thumbs']['label'] = 'WP Thumbs';
			$fileaway['show_wp_thumbs']['element'] = 'select';
			$fileaway['show_wp_thumbs']['class'] = 'fileaway-half';			
			$fileaway['limit']['section'] = 'filters';
			$fileaway['limit']['label'] = 'Limit Results';
			$fileaway['limit']['element'] = 'text';
			$fileaway['limit']['class'] = 'fileaway-half';
			$fileaway['limitby']['section'] = 'filters';
			$fileaway['limitby']['label'] = 'Limit By';
			$fileaway['limitby']['element'] = 'select';
			$fileaway['limitby']['class'] = 'fileaway-half';
			$fileaway['limitby']['style'] = 'display:none';			
			$fileaway['devices']['section'] = 'filters';
			$fileaway['devices']['label'] = 'Device Visibility';
			$fileaway['devices']['element'] = 'select';
			$fileaway['devices']['class'] = '';
			$fileaway['showto']['section'] = 'filters';
			$fileaway['showto']['label'] = 'Show to Roles/Caps';
			$fileaway['showto']['element'] = 'multiselect';
			$fileaway['showto']['class'] = 'fileaway-first-inline';
			$fileaway['hidefrom']['section'] = 'filters';
			$fileaway['hidefrom']['label'] = 'Hide from Roles/Caps';
			$fileaway['hidefrom']['element'] = 'multiselect';
			$fileaway['hidefrom']['class'] = '';
			// Styles
			$fileaway['theme']['section'] = 'styles';
			$fileaway['theme']['label'] = 'Theme';
			$fileaway['theme']['element'] = 'select';
			$fileaway['theme']['class'] = '';
			$fileaway['heading']['section'] = 'styles';
			$fileaway['heading']['label'] = 'Heading';
			$fileaway['heading']['element'] = 'text';
			$fileaway['heading']['class'] = '';
			$fileaway['width']['section'] = 'styles';
			$fileaway['width']['label'] = 'Width';
			$fileaway['width']['element'] = 'text';
			$fileaway['width']['class'] = 'fileaway-half';
			$fileaway['perpx']['section'] = 'styles';
			$fileaway['perpx']['label'] = 'Width In';
			$fileaway['perpx']['element'] = 'select';
			$fileaway['perpx']['class'] = 'fileaway-half';
			$fileaway['align']['section'] = 'styles';
			$fileaway['align']['label'] = 'Align';
			$fileaway['align']['element'] = 'select';
			$fileaway['align']['class'] = 'fileaway-half';
			$fileaway['textalign']['section'] = 'styles';
			$fileaway['textalign']['label'] = 'Text Align';
			$fileaway['textalign']['element'] = 'select';
			$fileaway['textalign']['class'] = 'fileaway-half';
			$fileaway['hcolor']['section'] = 'styles';
			$fileaway['hcolor']['label'] = 'Heading Color';
			$fileaway['hcolor']['element'] = 'select';
			$fileaway['hcolor']['class'] = 'fileaway-half';
			$fileaway['color']['section'] = 'styles';
			$fileaway['color']['label'] = 'Link Color';
			$fileaway['color']['element'] = 'select';
			$fileaway['color']['class'] = 'fileaway-half';
			$fileaway['accent']['section'] = 'styles';
			$fileaway['accent']['label'] = 'Accent';
			$fileaway['accent']['element'] = 'select';
			$fileaway['accent']['class'] = 'fileaway-half';
			$fileaway['iconcolor']['section'] = 'styles';
			$fileaway['iconcolor']['label'] = 'Icon Color';
			$fileaway['iconcolor']['element'] = 'select';
			$fileaway['iconcolor']['class'] = 'fileaway-half';
			$fileaway['icons']['section'] = 'styles';
			$fileaway['icons']['label'] = 'Icons';
			$fileaway['icons']['element'] = 'select';
			$fileaway['icons']['class'] = 'fileaway-half';
			$fileaway['corners']['section'] = 'styles';
			$fileaway['corners']['label'] = 'Corners';
			$fileaway['corners']['element'] = 'select';
			$fileaway['corners']['class'] = '';
			$fileaway['display']['section'] = 'styles';
			$fileaway['display']['label'] = 'Display';
			$fileaway['display']['element'] = 'select';
			$fileaway['display']['class'] = '';
			$fileaway['prettify']['section'] = 'styles';
			$fileaway['prettify']['label'] = 'Prettify Filenames';
			$fileaway['prettify']['element'] = 'select';
			$fileaway['prettify']['class'] = '';
			$fileaway['thumbnails']['section'] = 'styles';
			$fileaway['thumbnails']['label'] = 'Media Thumbnails';
			$fileaway['thumbnails']['element'] = 'select';
			$fileaway['thumbnails']['class'] = '';
			$fileaway['thumbsize']['section'] = 'styles';
			$fileaway['thumbsize']['label'] = 'Thumbnail Size';
			$fileaway['thumbsize']['element'] = 'select';
			$fileaway['thumbsize']['style'] = 'display:none';
			$fileaway['thumbsize']['class'] = '';
			$fileaway['thumbstyle']['section'] = 'styles';
			$fileaway['thumbstyle']['label'] = 'Thumbnail Style';
			$fileaway['thumbstyle']['element'] = 'select';
			$fileaway['thumbstyle']['style'] = 'display:none';
			$fileaway['thumbstyle']['class'] = '';
			$fileaway['graythumbs']['section'] = 'styles';
			$fileaway['graythumbs']['label'] = 'Thumbnail Color Filter';
			$fileaway['graythumbs']['element'] = 'select';
			$fileaway['graythumbs']['style'] = 'display:none';
			$fileaway['graythumbs']['class'] = '';
			$fileaway['maxsrcbytes']['section'] = 'styles';
			$fileaway['maxsrcbytes']['label'] = 'Max Source Image Bytes';
			$fileaway['maxsrcbytes']['element'] = 'text';
			$fileaway['maxsrcbytes']['style'] = 'display:none';
			$fileaway['maxsrcbytes']['class'] = '';
			$fileaway['maxsrcwidth']['section'] = 'styles';
			$fileaway['maxsrcwidth']['label'] = 'Max Source Image Width';
			$fileaway['maxsrcwidth']['element'] = 'text';
			$fileaway['maxsrcwidth']['style'] = 'display:none';
			$fileaway['maxsrcwidth']['class'] = '';
			$fileaway['maxsrcheight']['section'] = 'styles';
			$fileaway['maxsrcheight']['label'] = 'Max Source Image Height';
			$fileaway['maxsrcheight']['element'] = 'text';
			$fileaway['maxsrcheight']['style'] = 'display:none';
			$fileaway['maxsrcheight']['class'] = '';
			// Bannerize
			$fileaway['bannerize']['section'] = 'bannerize';
			$fileaway['bannerize']['label'] = 'Banner Interval';
			$fileaway['bannerize']['element'] = 'text';
			$fileaway['bannerize']['class'] = 'fileaway-half';						
			$this->shortcodes['fileaway'] = $fileaway;
			// ATTACH AWAY
			$attachaway = $this->shortcodes['attachaway'];
			$attachaway['option'] = 'Post/Page Attachments';
			$attachaway['types'] = array('list', 'table');
			$attachaway['sections'] = array(
				'config' => 'Config',
				'modes' => 'Modes',
				'filters' => 'Filters',
				'styles' => 'Styles'
			);
			// Config
			$attachaway['postid']['section'] = 'config';
			$attachaway['postid']['label'] = 'Post ID';
			$attachaway['postid']['element'] = 'text';
			$attachaway['postid']['class'] = 'fileaway-half';
			$attachaway['search']['section'] = 'config';
			$attachaway['search']['label'] = 'Searchable';
			$attachaway['search']['element'] = 'select';
			$attachaway['search']['class'] = 'fileaway-half';
			$attachaway['searchlabel']['section'] = 'config';
			$attachaway['searchlabel']['label'] = 'Search Label';
			$attachaway['searchlabel']['element'] = 'text';
			$attachaway['searchlabel']['class'] = 'fileaway-half';
			$attachaway['filenamelabel']['section'] = 'config';
			$attachaway['filenamelabel']['label'] = 'File Name Label';
			$attachaway['filenamelabel']['element'] = 'text';
			$attachaway['filenamelabel']['class'] = 'fileaway-half';
			$attachaway['capcolumn']['section'] = 'config';
			$attachaway['capcolumn']['label'] = 'Cap Col Label';
			$attachaway['capcolumn']['element'] = 'text';
			$attachaway['capcolumn']['class'] = 'fileaway-half';
			$attachaway['descolumn']['section'] = 'config';
			$attachaway['descolumn']['label'] = 'Des Col Label';
			$attachaway['descolumn']['element'] = 'text';
			$attachaway['descolumn']['class'] = 'fileaway-half';
			$attachaway['paginate']['section'] = 'config';
			$attachaway['paginate']['label'] = 'Paginate';
			$attachaway['paginate']['element'] = 'select';
			$attachaway['paginate']['class'] = 'fileaway-half';
			$attachaway['pagesize']['section'] = 'config';
			$attachaway['pagesize']['label'] = '# per page';
			$attachaway['pagesize']['element'] = 'text';
			$attachaway['pagesize']['class'] = 'fileaway-half';
			$attachaway['size']['section'] = 'config';
			$attachaway['size']['label'] = 'File Size';
			$attachaway['size']['element'] = 'select';
			$attachaway['size']['class'] = 'fileaway-half';
			$attachaway['sortfirst']['section'] = 'config';
			$attachaway['sortfirst']['label'] = 'Initial Sort';
			$attachaway['sortfirst']['element'] = 'select';
			$attachaway['sortfirst']['class'] = '';
			$attachaway['orderby']['section'] = 'config';
			$attachaway['orderby']['label'] = 'Order By';
			$attachaway['orderby']['element'] = 'select';
			$attachaway['orderby']['class'] = 'fileaway-half';
			$attachaway['desc']['section'] = 'config';
			$attachaway['desc']['label'] = 'Asc/Desc';
			$attachaway['desc']['element'] = 'select';
			$attachaway['desc']['class'] = 'fileaway-half';
			$attachaway['fadein']['section'] = 'config';
			$attachaway['fadein']['label'] = 'Fade In';
			$attachaway['fadein']['element'] = 'select';
			$attachaway['fadein']['class'] = 'fileaway-half';
			$attachaway['fadetime']['section'] = 'config';
			$attachaway['fadetime']['label'] = 'Fade Time';
			$attachaway['fadetime']['element'] = 'select';
			$attachaway['fadetime']['class'] = 'fileaway-half';
			$attachaway['class']['section'] = 'config';
			$attachaway['class']['label'] = 'CSS Class';
			$attachaway['class']['element'] = 'text';
			$attachaway['class']['class'] = 'fileaway-half';
			$attachaway['debug']['section'] = 'config';
			$attachaway['debug']['label'] = 'Debug';
			$attachaway['debug']['element'] = 'select';
			$attachaway['debug']['class'] = 'fileaway-half';
			// Modes
			$attachaway['flightbox']['section'] = 'modes';
			$attachaway['flightbox']['label'] = 'Flightbox';
			$attachaway['flightbox']['element'] = 'select';
			$attachaway['flightbox']['class'] = 'fileaway-half';
			$attachaway['boxtheme']['section'] = 'modes';
			$attachaway['boxtheme']['label'] = 'Box Theme';
			$attachaway['boxtheme']['element'] = 'select';
			$attachaway['boxtheme']['style'] = 'display:none';
			$attachaway['boxtheme']['class'] = 'fileaway-half';
			$attachaway['nolinksbox']['section'] = 'modes';
			$attachaway['nolinksbox']['label'] = 'Box Links';
			$attachaway['nolinksbox']['element'] = 'select';
			$attachaway['nolinksbox']['style'] = 'display:none';
			$attachaway['nolinksbox']['class'] = 'fileaway-half';
			$attachaway['maximgwidth']['section'] = 'modes';
			$attachaway['maximgwidth']['label'] = 'Max Image Width';
			$attachaway['maximgwidth']['element'] = 'text';
			$attachaway['maximgwidth']['style'] = 'display:none';
			$attachaway['maximgwidth']['class'] = 'fileaway-half';
			$attachaway['maximgheight']['section'] = 'modes';
			$attachaway['maximgheight']['label'] = 'Max Image Height';
			$attachaway['maximgheight']['element'] = 'text';
			$attachaway['maximgheight']['style'] = 'display:none';
			$attachaway['maximgheight']['class'] = 'fileaway-half';
			$attachaway['videowidth']['section'] = 'modes';
			$attachaway['videowidth']['label'] = 'Video Width';
			$attachaway['videowidth']['element'] = 'text';
			$attachaway['videowidth']['style'] = 'display:none';
			$attachaway['videowidth']['class'] = 'fileaway-half';
			// Filters
			$attachaway['exclude']['section'] = 'filters';
			$attachaway['exclude']['label'] = 'Exclude Specific';
			$attachaway['exclude']['element'] = 'text';
			$attachaway['exclude']['class'] = '';
			$attachaway['include']['section'] = 'filters';
			$attachaway['include']['label'] = 'Include Specific';
			$attachaway['include']['element'] = 'text';
			$attachaway['include']['class'] = '';
			$attachaway['only']['section'] = 'filters';
			$attachaway['only']['label'] = 'Show Only Specific';
			$attachaway['only']['element'] = 'text';
			$attachaway['only']['class'] = '';
			$attachaway['images']['section'] = 'filters';
			$attachaway['images']['label'] = 'Images';
			$attachaway['images']['element'] = 'select';
			$attachaway['images']['class'] = 'fileaway-half';
			$attachaway['code']['section'] = 'filters';
			$attachaway['code']['label'] = 'Code Docs';
			$attachaway['code']['element'] = 'select';
			$attachaway['code']['class'] = 'fileaway-half';
			$attachaway['devices']['section'] = 'filters';
			$attachaway['devices']['label'] = 'Device Visibility';
			$attachaway['devices']['element'] = 'select';
			$attachaway['devices']['class'] = '';
			$attachaway['showto']['section'] = 'filters';
			$attachaway['showto']['label'] = 'Show to Roles/Caps';
			$attachaway['showto']['element'] = 'multiselect';
			$attachaway['showto']['class'] = 'fileaway-first-inline';
			$attachaway['hidefrom']['section'] = 'filters';
			$attachaway['hidefrom']['label'] = 'Hide from Roles/Caps';
			$attachaway['hidefrom']['element'] = 'multiselect';
			$attachaway['hidefrom']['class'] = '';
			// Styles
			$attachaway['theme']['section'] = 'styles';
			$attachaway['theme']['label'] = 'Theme';
			$attachaway['theme']['element'] = 'select';
			$attachaway['theme']['class'] = '';
			$attachaway['heading']['section'] = 'styles';
			$attachaway['heading']['label'] = 'Heading';
			$attachaway['heading']['element'] = 'text';
			$attachaway['heading']['class'] = '';
			$attachaway['width']['section'] = 'styles';
			$attachaway['width']['label'] = 'Width';
			$attachaway['width']['element'] = 'text';
			$attachaway['width']['class'] = 'fileaway-half';
			$attachaway['perpx']['section'] = 'styles';
			$attachaway['perpx']['label'] = 'Width In';
			$attachaway['perpx']['element'] = 'select';
			$attachaway['perpx']['class'] = 'fileaway-half';
			$attachaway['align']['section'] = 'styles';
			$attachaway['align']['label'] = 'Align';
			$attachaway['align']['element'] = 'select';
			$attachaway['align']['class'] = 'fileaway-half';
			$attachaway['textalign']['section'] = 'styles';
			$attachaway['textalign']['label'] = 'Text Align';
			$attachaway['textalign']['element'] = 'select';
			$attachaway['textalign']['class'] = 'fileaway-half';
			$attachaway['hcolor']['section'] = 'styles';
			$attachaway['hcolor']['label'] = 'Heading Color';
			$attachaway['hcolor']['element'] = 'select';
			$attachaway['hcolor']['class'] = 'fileaway-half';
			$attachaway['color']['section'] = 'styles';
			$attachaway['color']['label'] = 'Link Color';
			$attachaway['color']['element'] = 'select';
			$attachaway['color']['class'] = 'fileaway-half';
			$attachaway['accent']['section'] = 'styles';
			$attachaway['accent']['label'] = 'Accent';
			$attachaway['accent']['element'] = 'select';
			$attachaway['accent']['class'] = 'fileaway-half';
			$attachaway['iconcolor']['section'] = 'styles';
			$attachaway['iconcolor']['label'] = 'Icon Color';
			$attachaway['iconcolor']['element'] = 'select';
			$attachaway['iconcolor']['class'] = 'fileaway-half';
			$attachaway['icons']['section'] = 'styles';
			$attachaway['icons']['label'] = 'Icons';
			$attachaway['icons']['element'] = 'select';
			$attachaway['icons']['class'] = 'fileaway-half';
			$attachaway['corners']['section'] = 'styles';
			$attachaway['corners']['label'] = 'Corners';
			$attachaway['corners']['element'] = 'select';
			$attachaway['corners']['class'] = '';
			$attachaway['display']['section'] = 'styles';
			$attachaway['display']['label'] = 'Display';
			$attachaway['display']['element'] = 'select';
			$attachaway['display']['class'] = '';
			$this->shortcodes['attachaway'] = $attachaway;
			// FILE UP
			$fileup = $this->shortcodes['fileup'];
			$fileup['option'] = 'File Uploads';
			$fileup['types'] = array('upload');
			$fileup['sections'] = array(
				'config' => 'Config',
				'filters' => 'Filters',
				'styles' => 'Styles'
			);
			// Config
			$fileup['base']['section'] = 'config';
			$fileup['base']['label'] = 'Base Directory';
			$fileup['base']['element'] = 'select';
			$fileup['base']['class'] = '';
			$fileup['sub']['section'] = 'config';
			$fileup['sub']['label'] = 'Sub Directory';
			$fileup['sub']['element'] = 'text';
			$fileup['sub']['class'] = '';			
			$fileup['makedir']['section'] = 'config';
			$fileup['makedir']['label'] = 'Make Directory';
			$fileup['makedir']['element'] = 'select';
			$fileup['makedir']['class'] = 'fileaway-half';			
			$fileup['matchdrawer']['section'] = 'config';
			$fileup['matchdrawer']['label'] = 'Match Drawer';
			$fileup['matchdrawer']['element'] = 'text';
			$fileup['matchdrawer']['class'] = 'fileaway-half';
			$fileup['single']['section'] = 'config';
			$fileup['single']['label'] = 'Uploads at Once';
			$fileup['single']['element'] = 'select';
			$fileup['single']['class'] = 'fileaway-half';
			$fileup['maxsize']['section'] = 'config';
			$fileup['maxsize']['label'] = 'Max Size';
			$fileup['maxsize']['element'] = 'text';
			$fileup['maxsize']['class'] = 'fileaway-half';
			$fileup['maxsizetype']['section'] = 'config';
			$fileup['maxsizetype']['label'] = 'Max Size In';
			$fileup['maxsizetype']['element'] = 'select';
			$fileup['maxsizetype']['class'] = 'fileaway-half';
			$fileup['uploadlabel']['section'] = 'config';
			$fileup['uploadlabel']['label'] = 'Upload Label';
			$fileup['uploadlabel']['element'] = 'text';
			$fileup['uploadlabel']['class'] = 'fileaway-half';
			$fileup['fadein']['section'] = 'config';
			$fileup['fadein']['label'] = 'Fade In';
			$fileup['fadein']['element'] = 'select';
			$fileup['fadein']['class'] = 'fileaway-half';
			$fileup['fadetime']['section'] = 'config';
			$fileup['fadetime']['label'] = 'Fade Time';
			$fileup['fadetime']['element'] = 'select';
			$fileup['fadetime']['class'] = 'fileaway-half';
			$fileup['fixedlocation']['section'] = 'config';
			$fileup['fixedlocation']['label'] = 'Allow Subdirectories';
			$fileup['fixedlocation']['element'] = 'select';
			$fileup['fixedlocation']['class'] = '';
			$fileup['uploader']['section'] = 'config';
			$fileup['uploader']['label'] = 'Append Uploader Name';
			$fileup['uploader']['element'] = 'select';
			$fileup['uploader']['class'] = '';	
			$fileup['overwrite']['section'] = 'config';
			$fileup['overwrite']['label'] = 'Overwite Filename';
			$fileup['overwrite']['element'] = 'select';
			$fileup['overwrite']['class'] = '';			
			$fileup['class']['section'] = 'config';
			$fileup['class']['label'] = 'CSS Class';
			$fileup['class']['element'] = 'text';
			$fileup['class']['class'] = 'fileaway-half';			
			$fileup['name']['section'] = 'config';
			$fileup['name']['label'] = 'Unique Name';
			$fileup['name']['element'] = 'text';
			$fileup['name']['class'] = 'fileaway-half';
			// Filters
			$fileup['devices']['section'] = 'filters';
			$fileup['devices']['label'] = 'Device Visibility';
			$fileup['devices']['element'] = 'select';
			$fileup['devices']['class'] = '';
			$fileup['action']['section'] = 'filters';
			$fileup['action']['label'] = 'File Type Action';
			$fileup['action']['element'] = 'select';
			$fileup['action']['class'] = '';
			$fileup['filetypes']['section'] = 'filters';
			$fileup['filetypes']['label'] = 'File Types';
			$fileup['filetypes']['element'] = 'text';
			$fileup['filetypes']['class'] = '';
			$fileup['filegroups']['section'] = 'filters';
			$fileup['filegroups']['label'] = 'File Type Groups';
			$fileup['filegroups']['element'] = 'multiselect';
			$fileup['filegroups']['class'] = 'fileaway-first-inline';
			$fileup['showto']['section'] = 'filters';
			$fileup['showto']['label'] = 'Show to Roles/Caps';
			$fileup['showto']['element'] = 'multiselect';
			$fileup['showto']['class'] = '';
			$fileup['hidefrom']['section'] = 'filters';
			$fileup['hidefrom']['label'] = 'Hide from Roles/Caps';
			$fileup['hidefrom']['element'] = 'multiselect';
			$fileup['hidefrom']['class'] = '';
			// Style
			$fileup['theme']['section'] = 'styles';
			$fileup['theme']['label'] = 'Theme';
			$fileup['theme']['element'] = 'select';
			$fileup['theme']['class'] = '';
			$fileup['width']['section'] = 'styles';
			$fileup['width']['label'] = 'Width';
			$fileup['width']['element'] = 'text';
			$fileup['width']['class'] = 'fileaway-half';
			$fileup['perpx']['section'] = 'styles';
			$fileup['perpx']['label'] = 'Width In';
			$fileup['perpx']['element'] = 'select';
			$fileup['perpx']['class'] = 'fileaway-half';
			$fileup['align']['section'] = 'styles';
			$fileup['align']['label'] = 'Align';
			$fileup['align']['element'] = 'select';
			$fileup['align']['class'] = 'fileaway-half';
			$fileup['iconcolor']['section'] = 'styles';
			$fileup['iconcolor']['label'] = 'Icon Color';
			$fileup['iconcolor']['element'] = 'select';
			$fileup['iconcolor']['class'] = 'fileaway-half';
			$this->shortcodes['fileup'] = $fileup;
			// FILE AWAY VALUES
			$fileaway_values = $this->shortcodes['fileaway_values'];
			$fileaway_values['option'] = 'CSV Data Table';
			$fileaway_values['types'] = array('values');
			$fileaway_values['sections'] = array(
				'config' => 'Config',
				'modes' => 'Modes',
				'filters' => 'Filters',
				'styles' => 'Styles',				
			);
			// Config
			$fileaway_values['base']['section'] = 'config';
			$fileaway_values['base']['label'] = 'Base Directory';
			$fileaway_values['base']['element'] = 'select';
			$fileaway_values['base']['class'] = '';
			$fileaway_values['sub']['section'] = 'config';
			$fileaway_values['sub']['label'] = 'Sub Directory';
			$fileaway_values['sub']['element'] = 'text';
			$fileaway_values['sub']['class'] = '';
			$fileaway_values['filename']['section'] = 'config';
			$fileaway_values['filename']['label'] = 'Filename';
			$fileaway_values['filename']['element'] = 'text';
			$fileaway_values['filename']['class'] = '';
			$fileaway_values['makecsv']['section'] = 'config';
			$fileaway_values['makecsv']['label'] = 'Make New CSV';
			$fileaway_values['makecsv']['element'] = 'text';
			$fileaway_values['makecsv']['class'] = '';			
			$fileaway_values['makedir']['section'] = 'config';
			$fileaway_values['makedir']['label'] = 'Make Directory';
			$fileaway_values['makedir']['element'] = 'select';
			$fileaway_values['makedir']['class'] = 'fileaway-half';
			$fileaway_values['paginate']['section'] = 'config';
			$fileaway_values['paginate']['label'] = 'Paginate';
			$fileaway_values['paginate']['element'] = 'select';
			$fileaway_values['paginate']['class'] = 'fileaway-half';
			$fileaway_values['pagesize']['section'] = 'config';
			$fileaway_values['pagesize']['label'] = '# per page';
			$fileaway_values['pagesize']['element'] = 'text';
			$fileaway_values['pagesize']['class'] = 'fileaway-half';
			$fileaway_values['sorting']['section'] = 'config';
			$fileaway_values['sorting']['label'] = 'Sorting';
			$fileaway_values['sorting']['element'] = 'select';
			$fileaway_values['sorting']['class'] = 'fileaway-half';
			$fileaway_values['search']['section'] = 'config';
			$fileaway_values['search']['label'] = 'Searchable';
			$fileaway_values['search']['element'] = 'select';
			$fileaway_values['search']['class'] = 'fileaway-half';
			$fileaway_values['searchlabel']['section'] = 'config';
			$fileaway_values['searchlabel']['label'] = 'Search Label';
			$fileaway_values['searchlabel']['element'] = 'text';
			$fileaway_values['searchlabel']['class'] = 'fileaway-half';	
			$fileaway_values['placeholder']['section'] = 'config';
			$fileaway_values['placeholder']['label'] = 'Placeholder';
			$fileaway_values['placeholder']['element'] = 'text';
			$fileaway_values['placeholder']['class'] = 'fileaway-half';
			$fileaway_values['read']['section'] = 'config';
			$fileaway_values['read']['label'] = 'Read Encoding';
			$fileaway_values['read']['element'] = 'select';
			$fileaway_values['read']['class'] = 'fileaway-half';
			$fileaway_values['write']['section'] = 'config';
			$fileaway_values['write']['label'] = 'Write Encoding';
			$fileaway_values['write']['element'] = 'select';
			$fileaway_values['write']['class'] = 'fileaway-half';
			// Modes
			$fileaway_values['recursive']['section'] = 'modes';
			$fileaway_values['recursive']['label'] = 'Recursive Scan';
			$fileaway_values['recursive']['element'] = 'select';
			$fileaway_values['recursive']['class'] = 'fileaway-half';
			$fileaway_values['editor']['section'] = 'modes';
			$fileaway_values['editor']['label'] = 'Editor';
			$fileaway_values['editor']['element'] = 'select';
			$fileaway_values['editor']['class'] = 'fileaway-half';
			// Filters
			$fileaway_values['exclude']['section'] = 'filters';
			$fileaway_values['exclude']['label'] = 'Exclude Specific';
			$fileaway_values['exclude']['element'] = 'text';
			$fileaway_values['exclude']['class'] = '';
			$fileaway_values['include']['section'] = 'filters';
			$fileaway_values['include']['label'] = 'Include Specific';
			$fileaway_values['include']['element'] = 'text';
			$fileaway_values['include']['class'] = '';
			$fileaway_values['only']['section'] = 'filters';
			$fileaway_values['only']['label'] = 'Show Only Specific';
			$fileaway_values['only']['element'] = 'text';
			$fileaway_values['only']['class'] = '';
			$fileaway_values['excludedirs']['section'] = 'modes';
			$fileaway_values['excludedirs']['label'] = 'Exclude Directories';
			$fileaway_values['excludedirs']['element'] = 'text';
			$fileaway_values['excludedirs']['style'] = 'display:none';
			$fileaway_values['excludedirs']['class'] = '';
			$fileaway_values['onlydirs']['section'] = 'modes';
			$fileaway_values['onlydirs']['label'] = 'Only These Directories';
			$fileaway_values['onlydirs']['element'] = 'text';
			$fileaway_values['onlydirs']['style'] = 'display:none';
			$fileaway_values['onlydirs']['class'] = '';			
			$fileaway_values['devices']['section'] = 'filters';
			$fileaway_values['devices']['label'] = 'Device Visibility';
			$fileaway_values['devices']['element'] = 'select';
			$fileaway_values['devices']['class'] = '';
			$fileaway_values['showto']['section'] = 'filters';
			$fileaway_values['showto']['label'] = 'Show to Roles/Caps';
			$fileaway_values['showto']['element'] = 'multiselect';
			$fileaway_values['showto']['class'] = '';
			$fileaway_values['hidefrom']['section'] = 'filters';
			$fileaway_values['hidefrom']['label'] = 'Hide from Roles/Caps';
			$fileaway_values['hidefrom']['element'] = 'multiselect';
			$fileaway_values['hidefrom']['class'] = '';
			// Style
			$fileaway_values['theme']['section'] = 'styles';
			$fileaway_values['theme']['label'] = 'Theme';
			$fileaway_values['theme']['element'] = 'select';
			$fileaway_values['theme']['class'] = '';
			$fileaway_values['width']['section'] = 'styles';
			$fileaway_values['width']['label'] = 'Width';
			$fileaway_values['width']['element'] = 'text';
			$fileaway_values['width']['class'] = 'fileaway-half';
			$fileaway_values['perpx']['section'] = 'styles';
			$fileaway_values['perpx']['label'] = 'Width In';
			$fileaway_values['perpx']['element'] = 'select';
			$fileaway_values['perpx']['class'] = 'fileaway-half';
			$fileaway_values['align']['section'] = 'styles';
			$fileaway_values['align']['label'] = 'Align';
			$fileaway_values['align']['element'] = 'select';
			$fileaway_values['align']['class'] = 'fileaway-half';
			$fileaway_values['textalign']['section'] = 'styles';
			$fileaway_values['textalign']['label'] = 'Text Align';
			$fileaway_values['textalign']['element'] = 'select';
			$fileaway_values['textalign']['class'] = 'fileaway-half';
			$fileaway_values['hcolor']['section'] = 'styles';
			$fileaway_values['hcolor']['label'] = 'Heading Color';
			$fileaway_values['hcolor']['element'] = 'select';
			$fileaway_values['hcolor']['class'] = 'fileaway-half';
			$this->shortcodes['fileaway_values'] = $fileaway_values;
			// FORMAWAY OPEN
			$formaway_open = $this->shortcodes['formaway_open'];
			$formaway_open['option'] = 'Form Table Open';
			$formaway_open['types'] = array('open');
			$formaway_open['sections'] = array(
				'config' => 'Config',
				'columns' => 'Columns',
				'styles' => 'Styles',
			);
			// Config
			$formaway_open['paginate']['section'] = 'config';
			$formaway_open['paginate']['label'] = 'Paginate';
			$formaway_open['paginate']['element'] = 'select';
			$formaway_open['paginate']['class'] = 'fileaway-half';
			$formaway_open['pagesize']['section'] = 'config';
			$formaway_open['pagesize']['label'] = '# per page';
			$formaway_open['pagesize']['element'] = 'text';
			$formaway_open['pagesize']['class'] = 'fileaway-half';
			$formaway_open['search']['section'] = 'config';
			$formaway_open['search']['label'] = 'Searchable';
			$formaway_open['search']['element'] = 'select';
			$formaway_open['search']['class'] = 'fileaway-half';
			$formaway_open['searchlabel']['section'] = 'config';
			$formaway_open['searchlabel']['label'] = 'Search Label';
			$formaway_open['searchlabel']['element'] = 'text';
			$formaway_open['searchlabel']['class'] = 'fileaway-half';	
			$formaway_open['fadein']['section'] = 'config';
			$formaway_open['fadein']['label'] = 'Fade In';
			$formaway_open['fadein']['element'] = 'select';
			$formaway_open['fadein']['class'] = 'fileaway-half';
			$formaway_open['fadetime']['section'] = 'config';
			$formaway_open['fadetime']['label'] = 'Fade Time';
			$formaway_open['fadetime']['element'] = 'select';
			$formaway_open['fadetime']['class'] = 'fileaway-half';
			// Columns
			$formaway_open['numcols']['section'] = 'columns';
			$formaway_open['numcols']['label'] = 'Total Columns';
			$formaway_open['numcols']['element'] = 'text';
			$formaway_open['numcols']['class'] = 'fileaway-half';
			$formaway_open['sort']['section'] = 'columns';
			$formaway_open['sort']['label'] = 'Sorting';
			$formaway_open['sort']['element'] = 'select';
			$formaway_open['sort']['class'] = 'fileaway-half';
			$formaway_open['initialsort']['section'] = 'columns';
			$formaway_open['initialsort']['label'] = 'Initial Sort';
			$formaway_open['initialsort']['element'] = 'select';
			$formaway_open['initialsort']['class'] = 'fileaway-half';
			// Style
			$formaway_open['theme']['section'] = 'styles';
			$formaway_open['theme']['label'] = 'Theme';
			$formaway_open['theme']['element'] = 'select';
			$formaway_open['theme']['class'] = '';
			$formaway_open['heading']['section'] = 'styles';
			$formaway_open['heading']['label'] = 'Heading';
			$formaway_open['heading']['element'] = 'text';
			$formaway_open['heading']['class'] = '';			
			$formaway_open['hcolor']['section'] = 'styles';
			$formaway_open['hcolor']['label'] = 'Heading Color';
			$formaway_open['hcolor']['element'] = 'select';
			$formaway_open['hcolor']['class'] = 'fileaway-half';			
			$formaway_open['classes']['section'] = 'styles';
			$formaway_open['classes']['label'] = 'CSS Classes';
			$formaway_open['classes']['element'] = 'text';
			$formaway_open['classes']['class'] = 'fileaway-half';
			$formaway_open['width']['section'] = 'styles';
			$formaway_open['width']['label'] = 'Width';
			$formaway_open['width']['element'] = 'text';
			$formaway_open['width']['class'] = 'fileaway-half';
			$formaway_open['perpx']['section'] = 'styles';
			$formaway_open['perpx']['label'] = 'Width In';
			$formaway_open['perpx']['element'] = 'select';
			$formaway_open['perpx']['class'] = 'fileaway-half';
			$formaway_open['align']['section'] = 'styles';
			$formaway_open['align']['label'] = 'Align';
			$formaway_open['align']['element'] = 'select';
			$formaway_open['align']['class'] = 'fileaway-half';
			$formaway_open['textalign']['section'] = 'styles';
			$formaway_open['textalign']['label'] = 'Text Align';
			$formaway_open['textalign']['element'] = 'select';
			$formaway_open['textalign']['class'] = 'fileaway-half';
			$this->shortcodes['formaway_open'] = $formaway_open;
			// FORMAWAY ROW
			$formaway_row = $this->shortcodes['formaway_row'];
			$formaway_row['option'] = 'Form Table Row';
			$formaway_row['types'] = array('row');
			$formaway_row['sections'] = array(
				'styles' => 'Styles',
			);
			// Styles
			$formaway_row['classes']['section'] = 'styles';
			$formaway_row['classes']['label'] = 'CSS Classes';
			$formaway_row['classes']['element'] = 'text';
			$formaway_row['classes']['class'] = '';
			$this->shortcodes['formaway_row'] = $formaway_row;
			// FORMAWAY CELL
			$formaway_cell = $this->shortcodes['formaway_cell'];
			$formaway_cell['option'] = 'Form Table Cell';
			$formaway_cell['types'] = array('cell');
			$formaway_cell['sections'] = array(
				'config' => 'Config',
			);
			// Config
			$formaway_cell['sortvalue']['section'] = 'config';
			$formaway_cell['sortvalue']['label'] = 'Sort Value';
			$formaway_cell['sortvalue']['element'] = 'text';
			$formaway_cell['sortvalue']['class'] = '';
			$formaway_cell['classes']['section'] = 'config';
			$formaway_cell['classes']['label'] = 'CSS Classes';
			$formaway_cell['classes']['element'] = 'text';
			$formaway_cell['classes']['class'] = '';
			$formaway_cell['colspan']['section'] = 'config';
			$formaway_cell['colspan']['label'] = 'Colspan';
			$formaway_cell['colspan']['element'] = 'text';
			$formaway_cell['colspan']['class'] = 'fileaway-half';			
			$this->shortcodes['formaway_cell'] = $formaway_cell;
			// FORMAWAY CLOSE
			$formaway_close = $this->shortcodes['formaway_close'];
			$formaway_close['option'] = 'Form Table Close';
			$formaway_close['types'] = array('close');
			$formaway_close['sections'] = array(
				'config' => 'Config',
			);
			$formaway_close['clearfix']['section'] = 'config';
			$formaway_close['clearfix']['label'] = 'Append Clearfix';
			$formaway_close['clearfix']['element'] = 'select';
			$formaway_close['clearfix']['class'] = 'fileaway-half';
			$this->shortcodes['formaway_close'] = $formaway_close;
			// FILE-A-FRAME
			$fileaframe = $this->shortcodes['fileaframe'];
			$fileaframe['option'] = 'File Away iframe';
			$fileaframe['types'] = array('iframe');
			$fileaframe['sections'] = array(
				'config' => 'Config',
				'filters' => 'Filters',
			);
			// Config
			$fileaframe['source']['section'] = 'config';
			$fileaframe['source']['label'] = 'Source URL';
			$fileaframe['source']['element'] = 'text';
			$fileaframe['source']['class'] = '';
			$fileaframe['name']['section'] = 'config';
			$fileaframe['name']['label'] = 'Unique Name';
			$fileaframe['name']['element'] = 'text';
			$fileaframe['name']['class'] = 'fileaway-half';
			$fileaframe['scroll']['section'] = 'config';
			$fileaframe['scroll']['label'] = 'Scrolling';
			$fileaframe['scroll']['element'] = 'select';
			$fileaframe['scroll']['class'] = 'fileaway-half';
			$fileaframe['width']['section'] = 'config';
			$fileaframe['width']['label'] = 'Width';
			$fileaframe['width']['element'] = 'text';
			$fileaframe['width']['class'] = 'fileaway-half';
			$fileaframe['height']['section'] = 'config';
			$fileaframe['height']['label'] = 'Height';
			$fileaframe['height']['element'] = 'text';
			$fileaframe['height']['class'] = 'fileaway-half';			
			$fileaframe['mwidth']['section'] = 'config';
			$fileaframe['mwidth']['label'] = 'Margin Width';
			$fileaframe['mwidth']['element'] = 'text';
			$fileaframe['mwidth']['class'] = 'fileaway-half';
			$fileaframe['mheight']['section'] = 'config';
			$fileaframe['mheight']['label'] = 'Margin Height';
			$fileaframe['mheight']['element'] = 'text';
			$fileaframe['mheight']['class'] = 'fileaway-half';			
			// Filters			
			$fileaframe['showto']['section'] = 'filters';
			$fileaframe['showto']['label'] = 'Show to Roles/Caps';
			$fileaframe['showto']['element'] = 'multiselect';
			$fileaframe['showto']['class'] = '';
			$fileaframe['hidefrom']['section'] = 'filters';
			$fileaframe['hidefrom']['label'] = 'Hide from Roles/Caps';
			$fileaframe['hidefrom']['element'] = 'multiselect';
			$fileaframe['hidefrom']['class'] = '';
			$fileaframe['devices']['section'] = 'filters';
			$fileaframe['devices']['label'] = 'Device Visibility';
			$fileaframe['devices']['element'] = 'select';
			$fileaframe['devices']['class'] = '';				
			$this->shortcodes['fileaframe'] = $fileaframe;
			// STAT AWAY
			$stataway = $this->shortcodes['stataway'];
			$stataway['option'] = 'Download Stats';
			$stataway['types'] = array('list', 'table');
			$stataway['sections'] = array(
				'config' => 'Config',
				'modes' => 'Modes',
				'filters' => 'Filters',
				'styles' => 'Styles',
			);
			// Config
			$stataway['show']['section'] = 'config';
			$stataway['show']['label'] = 'Show';
			$stataway['show']['element'] = 'select';
			$stataway['show']['class'] = '';
			$stataway['scope']['section'] = 'config';
			$stataway['scope']['label'] = 'Scope';
			$stataway['scope']['element'] = 'select';
			$stataway['scope']['class'] = '';
			$stataway['number']['section'] = 'config';
			$stataway['number']['label'] = 'Number';
			$stataway['number']['element'] = 'text';
			$stataway['number']['class'] = 'fileaway-half';
			$stataway['class']['section'] = 'config';
			$stataway['class']['label'] = 'CSS Class';
			$stataway['class']['element'] = 'text';
			$stataway['class']['class'] = 'fileaway-half';
			$stataway['paginate']['section'] = 'config';
			$stataway['paginate']['label'] = 'Paginate';
			$stataway['paginate']['element'] = 'select';
			$stataway['paginate']['class'] = 'fileaway-half';
			$stataway['pagesize']['section'] = 'config';
			$stataway['pagesize']['label'] = '# per page';
			$stataway['pagesize']['element'] = 'text';
			$stataway['pagesize']['class'] = 'fileaway-half';
			$stataway['search']['section'] = 'config';
			$stataway['search']['label'] = 'Searchable';
			$stataway['search']['element'] = 'select';
			$stataway['search']['class'] = 'fileaway-half';
			$stataway['searchlabel']['section'] = 'config';
			$stataway['searchlabel']['label'] = 'Search Label';
			$stataway['searchlabel']['element'] = 'text';
			$stataway['searchlabel']['class'] = 'fileaway-half';	
			$stataway['mod']['section'] = 'config';
			$stataway['mod']['label'] = 'Date Modified';
			$stataway['mod']['element'] = 'select';
			$stataway['mod']['class'] = 'fileaway-half';
			$stataway['size']['section'] = 'config';
			$stataway['size']['label'] = 'File Size';
			$stataway['size']['element'] = 'select';
			$stataway['size']['class'] = 'fileaway-half';
			$stataway['filecolumn']['section'] = 'config';
			$stataway['filecolumn']['label'] = 'File Column';
			$stataway['filecolumn']['element'] = 'select';
			$stataway['filecolumn']['class'] = 'fileaway-half';
			$stataway['username']['section'] = 'config';
			$stataway['username']['label'] = 'Username';
			$stataway['username']['element'] = 'select';
			$stataway['username']['class'] = 'fileaway-half';
			$stataway['email']['section'] = 'config';
			$stataway['email']['label'] = 'Email';
			$stataway['email']['element'] = 'select';
			$stataway['email']['class'] = 'fileaway-half';
			$stataway['ip']['section'] = 'config';
			$stataway['ip']['label'] = 'IP Address';
			$stataway['ip']['element'] = 'select';
			$stataway['ip']['class'] = 'fileaway-half';
			$stataway['agent']['section'] = 'config';
			$stataway['agent']['label'] = 'User Agent';
			$stataway['agent']['element'] = 'select';
			$stataway['agent']['class'] = 'fileaway-half';
			$stataway['redirect']['section'] = 'config';
			$stataway['redirect']['label'] = 'Guest Redirect';
			$stataway['redirect']['element'] = 'select';
			$stataway['redirect']['class'] = 'fileaway-half';			
			$stataway['fadein']['section'] = 'config';
			$stataway['fadein']['label'] = 'Fade In';
			$stataway['fadein']['element'] = 'select';
			$stataway['fadein']['class'] = 'fileaway-half';
			$stataway['fadetime']['section'] = 'config';
			$stataway['fadetime']['label'] = 'Fade Time';
			$stataway['fadetime']['element'] = 'select';
			$stataway['fadetime']['class'] = 'fileaway-half';
			$stataway['s2skipconfirm']['section'] = 'config';
			$stataway['s2skipconfirm']['label'] = 'Skip Confirmation';
			$stataway['s2skipconfirm']['element'] = 'select';
			$stataway['s2skipconfirm']['style'] = 'display:none';
			$stataway['s2skipconfirm']['class'] = '';
			// Modes
			$stataway['stats']['section'] = 'modes';
			$stataway['stats']['label'] = 'Download Stats';
			$stataway['stats']['element'] = 'select';
			$stataway['stats']['class'] = 'fileaway-half';
			$stataway['flightbox']['section'] = 'modes';
			$stataway['flightbox']['label'] = 'FlightBox';
			$stataway['flightbox']['element'] = 'select';
			$stataway['flightbox']['class'] = 'fileaway-half';
			$stataway['boxtheme']['section'] = 'modes';
			$stataway['boxtheme']['label'] = 'Box Theme';
			$stataway['boxtheme']['element'] = 'select';
			$stataway['boxtheme']['style'] = 'display:none';
			$stataway['boxtheme']['class'] = 'fileaway-half';
			$stataway['maximgwidth']['section'] = 'modes';
			$stataway['maximgwidth']['label'] = 'Max Image Width';
			$stataway['maximgwidth']['element'] = 'text';
			$stataway['maximgwidth']['style'] = 'display:none';
			$stataway['maximgwidth']['class'] = 'fileaway-half';
			$stataway['maximgheight']['section'] = 'modes';
			$stataway['maximgheight']['label'] = 'Max Image Height';
			$stataway['maximgheight']['element'] = 'text';
			$stataway['maximgheight']['style'] = 'display:none';
			$stataway['maximgheight']['class'] = 'fileaway-half';
			$stataway['videowidth']['section'] = 'modes';
			$stataway['videowidth']['label'] = 'Video Width';
			$stataway['videowidth']['element'] = 'text';
			$stataway['videowidth']['style'] = 'display:none';
			$stataway['videowidth']['class'] = 'fileaway-half';
			$stataway['encryption']['section'] = 'modes';
			$stataway['encryption']['label'] = 'Encrypted Links';
			$stataway['encryption']['element'] = 'select';
			$stataway['encryption']['class'] = 'fileaway-half';
			// Filters
			$stataway['devices']['section'] = 'filters';
			$stataway['devices']['label'] = 'Device Visibility';
			$stataway['devices']['element'] = 'select';
			$stataway['devices']['class'] = '';
			$stataway['showto']['section'] = 'filters';
			$stataway['showto']['label'] = 'Show to Roles/Caps';
			$stataway['showto']['element'] = 'multiselect';
			$stataway['showto']['class'] = '';
			$stataway['hidefrom']['section'] = 'filters';
			$stataway['hidefrom']['label'] = 'Hide from Roles/Caps';
			$stataway['hidefrom']['element'] = 'multiselect';
			$stataway['hidefrom']['class'] = '';
			// Styles
			$stataway['theme']['section'] = 'styles';
			$stataway['theme']['label'] = 'Theme';
			$stataway['theme']['element'] = 'select';
			$stataway['theme']['class'] = '';
			$stataway['heading']['section'] = 'styles';
			$stataway['heading']['label'] = 'Heading';
			$stataway['heading']['element'] = 'text';
			$stataway['heading']['class'] = '';
			$stataway['width']['section'] = 'styles';
			$stataway['width']['label'] = 'Width';
			$stataway['width']['element'] = 'text';
			$stataway['width']['class'] = 'fileaway-half';
			$stataway['perpx']['section'] = 'styles';
			$stataway['perpx']['label'] = 'Width In';
			$stataway['perpx']['element'] = 'select';
			$stataway['perpx']['class'] = 'fileaway-half';
			$stataway['align']['section'] = 'styles';
			$stataway['align']['label'] = 'Align';
			$stataway['align']['element'] = 'select';
			$stataway['align']['class'] = 'fileaway-half';
			$stataway['textalign']['section'] = 'styles';
			$stataway['textalign']['label'] = 'Text Align';
			$stataway['textalign']['element'] = 'select';
			$stataway['textalign']['class'] = 'fileaway-half';
			$stataway['hcolor']['section'] = 'styles';
			$stataway['hcolor']['label'] = 'Heading Color';
			$stataway['hcolor']['element'] = 'select';
			$stataway['hcolor']['class'] = 'fileaway-half';
			$stataway['color']['section'] = 'styles';
			$stataway['color']['label'] = 'Link Color';
			$stataway['color']['element'] = 'select';
			$stataway['color']['class'] = 'fileaway-half';
			$stataway['accent']['section'] = 'styles';
			$stataway['accent']['label'] = 'Accent';
			$stataway['accent']['element'] = 'select';
			$stataway['accent']['class'] = 'fileaway-half';
			$stataway['iconcolor']['section'] = 'styles';
			$stataway['iconcolor']['label'] = 'Icon Color';
			$stataway['iconcolor']['element'] = 'select';
			$stataway['iconcolor']['class'] = 'fileaway-half';
			$stataway['icons']['section'] = 'styles';
			$stataway['icons']['label'] = 'Icons';
			$stataway['icons']['element'] = 'select';
			$stataway['icons']['class'] = 'fileaway-half';
			$stataway['corners']['section'] = 'styles';
			$stataway['corners']['label'] = 'Corners';
			$stataway['corners']['element'] = 'select';
			$stataway['corners']['class'] = '';
			$stataway['display']['section'] = 'styles';
			$stataway['display']['label'] = 'Display';
			$stataway['display']['element'] = 'select';
			$stataway['display']['class'] = '';
			$this->shortcodes['stataway'] = $stataway;
			// FILE AWAY TUTORIALS
			$stataway_user = $this->shortcodes['stataway_user'];
			$stataway_user['option'] = 'User Download Totals';
			$stataway_user['types'] = array('userstats');
			$stataway_user['sections'] = array(
				'config' => 'Config',
			);	
			// Config			
			$stataway_user['output']['section'] = 'config';
			$stataway_user['output']['label'] = 'Output Type';
			$stataway_user['output']['element'] = 'select';
			$stataway_user['output']['class'] = '';
			$stataway_user['scope']['section'] = 'config';
			$stataway_user['scope']['label'] = 'Scope';
			$stataway_user['scope']['element'] = 'select';
			$stataway_user['scope']['class'] = '';
			$stataway_user['user']['section'] = 'config';
			$stataway_user['user']['label'] = 'User ID';
			$stataway_user['user']['element'] = 'text';
			$stataway_user['user']['class'] = 'fileaway-half';
			$stataway_user['timestamp']['section'] = 'config';
			$stataway_user['timestamp']['label'] = 'Timestamp';
			$stataway_user['timestamp']['element'] = 'select';
			$stataway_user['timestamp']['class'] = 'fileaway-half';
			$stataway_user['class']['section'] = 'config';
			$stataway_user['class']['label'] = 'CSS Class';
			$stataway_user['class']['element'] = 'text';
			$stataway_user['class']['class'] = '';
			$this->shortcodes['stataway_user'] = $stataway_user;
			// FILE AWAY TUTORIALS
			$fileaway_tutorials = $this->shortcodes['fileaway_tutorials'];
			$fileaway_tutorials['option'] = 'File Away Tutorials';
			$fileaway_tutorials['types'] = array('tutorials');
			$fileaway_tutorials['sections'] = array(
				'visibility' => 'Visibility',
			);	
			// Visibility		
			$fileaway_tutorials['showto']['section'] = 'visibility';
			$fileaway_tutorials['showto']['label'] = 'Show to Roles/Caps';
			$fileaway_tutorials['showto']['element'] = 'multiselect';
			$fileaway_tutorials['showto']['class'] = 'fileaway-first-inline';
			$fileaway_tutorials['hidefrom']['section'] = 'visibility';
			$fileaway_tutorials['hidefrom']['label'] = 'Hide from Roles/Caps';
			$fileaway_tutorials['hidefrom']['element'] = 'multiselect';
			$fileaway_tutorials['hidefrom']['class'] = '';
			$this->shortcodes['fileaway_tutorials'] = $fileaway_tutorials;
		}
	}
}