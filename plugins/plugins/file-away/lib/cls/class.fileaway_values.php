<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('fileaway_values'))
{
	class fileaway_values extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('fileaway_values', array($this, 'sc'));
		}
		public function sc($atts)
		{
			if(isset($atts['style'])) $atts['theme'] = $atts['style'];
			$get = new fileaway_definitions;
			extract($get->pathoptions);
			extract($this->correct(wp_parse_args($atts, $this->fileaway_values), $this->shortcodes['fileaway_values']));
			if($devices == 'mobile' && !$get->is_mobile) return;
			elseif($devices == 'desktop' && $get->is_mobile) return;
			if(!fileaway_utility::visibility($hidefrom, $showto)) return;
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			include fileaway_dir.'/lib/inc/inc.values-declarations.php';
			global $is_IE, $is_safari;
			$uid = rand(0, 9999); 
			$name = "ssfa-meta-container-$uid";
			$randcolor = array("red","green","blue","brown","black","orange","silver","purple","pink");
			$paginated = $paginate ? " data-page-navigation='.ssfa-pagination'" : null;
			$pagearea = $paginate ? "<div class='ssfa-pagination ssfa-pagination-centered hide-if-no-paging'></div>" : null;
			$pagesized = $paginate ? " data-page-size='$pagesize'" : null;
			$page = $paginate ? $paginated.$pagesized : "$paginated data-page-size='100000'";
			$theme = "ssfa-$theme";
			$textalign = $textalign ? ' ssfa-'.$textalign : null;
			$width = preg_replace('[\D]', '', $width);
			$width = $width ? "width:$width$perpx;" : null;
			$float = " float:$align;";
			$margin = $width !== 'width:100%;' ? ($align === 'right' ? ' margin-left:15px;' : ' margin-right:15px;') : null;
			$howshouldiputit = $width.$float.$margin;
			if($width == '100' && $perpx == '%') $align = 'none';
			$clearfix = $align == 'none' ? "<div class='ssfa-clearfix'></div>" : null;
			include fileaway_dir.'/lib/inc/inc.base.php';
			extract(fileaway_utility::dynamicpaths($dir, $playbackpath));
			$makedir = $makecsv ? true : $makedir;
			$dir = rtrim($dir, '/');
			if(!is_dir("$dir") && $makedir && (!$private_content || ($private_content && $logged_in)))
				if(mkdir($rootpath.$dir, 0775, true)) fileaway_utility::indexmulti($rootpath.$dir, $chosenpath); 
			if(!is_dir("$dir")) return;
			$nonce = wp_create_nonce('fileaway-values-nonce');
			$start = "$dir";
			if($filename)
			{
				if(!fileaway_utility::endswith(strtolower($filename), '.csv')) $filename = $filename.'.csv';
				$filename = str_replace('fa-userid', $fa_userid, $filename);
				$filename = str_replace('fa-userrole', $fa_userrole, $filename);
				$filename = str_replace('fa-firstlast', $fa_firstlast, $filename);
				$filename = str_replace('fa-username', $fa_username, $filename);
				if(stripos($filename, 'fa-usermeta(') !== false)
				{
					$umetas = array();
					$countmetas = preg_match_all('/\((.*)\)/U', $filename, $umetas);
					if(is_array($umetas[1]))
					{
						foreach($umetas[1] as $umeta)
						{
							$metavalue = get_user_meta($fa_userid, $umeta, true);
							if(!$metavalue || $metavalue == '') $makecsv = false;
							$filename = str_ireplace('fa-usermeta('.$umeta.')', $metavalue, $filename);
						}
					}
				}	
				$showoptions = false;
				if(!is_file($rootpath.$dir.'/'.$filename) && $makecsv)
				{
					$csv = new fileaway_csv();
					$csv->encoding($read, $write);
					$rows = array();
					$cols = array();
					$csv->titles = preg_split('/(, |,)/', $makecsv, -1, PREG_SPLIT_NO_EMPTY);
					foreach($csv->titles as $header) $cols[$header] = '';
					$rows[0] = $cols;
					$csv->data = $rows;					
					$csv->save($rootpath.$dir.'/'.$filename);
				}
			}
			else
			{
				if($recursive)
				{		
					$globaldirexes = array(); $localdirexes = array(); 
					if($excludedirs) $localdirexes = preg_split('/(, |,)/', $excludedirs, -1, PREG_SPLIT_NO_EMPTY);
					if($this->op['direxclusions']) $globaldirexes = preg_split('/(, |,)/', $this->op['direxclusions'], -1, PREG_SPLIT_NO_EMPTY);
					if(!is_array($globaldirexes)) $globaldirexes = array();
					if(!is_array($localdirexes)) $localdirexes = array();
					$direxes = array_unique(array_merge($localdirexes, $globaldirexes)); 
					$excludedirs = count($direxes) > 0 ? $direxes : false;
					if($onlydirs) $onlydirs = $dir.','.rtrim($onlydirs);
					$justthesedirs = $onlydirs ? preg_split('/(, |,)/', $onlydirs, -1, PREG_SPLIT_NO_EMPTY) : 0; 
					$onlydirs = is_array($justthesedirs) && count($justthesedirs) > 0 ? $justthesedirs : 0;
				}
				$selected = false;
				$showoptions = false;
				if(isset($_REQUEST['fa_csv']) && isset($_REQUEST['fa_index']))
				{
					$selected = $_GET['fa_csv'];
					$filename = base64_decode($selected);
					$file_index = $_GET['fa_index'];	
					$selected = fileaway_utility::querystring(get_permalink(), $_SERVER['QUERY_STRING'], array('fa_csv' => $selected, 'fa_index' => $file_index));
				}
				$dir = rtrim($dir, '/');
				$files = $recursive ? fileaway_utility::recursefiles($dir, $onlydirs, $excludedirs, '[cC][sS][vV]') : glob("{$dir}/*.[cC][sS][vV]"); 
				if(!$editor && !is_array($files)) return;
				$count = count($files);
				if($count !== 1) $showoptions = true;
				elseif($count === 1)
				{
					$showoptions = false;	
					$filename = fileaway_utility::basename($files[0]);
					$file_index = 0;
					$dir = $recursive ? str_replace($filename, '', $files[0]) : $dir;
					$dir = rtrim($dir, '/');
				}
				if($showoptions)
				{
					$options = array();
					$original_url = fileaway_utility::querystring(get_permalink(), $_SERVER['QUERY_STRING'], array('fa_csv', 'fa_index'), true);
					if(is_array($files))
					{
						foreach($files as $key => $file)
						{
							if(isset($file_index) && $file_index == $key)
							{
								$dir = $recursive ? str_replace(fileaway_utility::basename($file), '', $file) : $dir;
								$dir = rtrim($dir, '/');
							}
							$is_selected = $file === $dir.'/'.$filename ? 'selected' : null;
							$link = fileaway_utility::querystring(get_permalink(), $_SERVER['QUERY_STRING'], array('fa_csv' => base64_encode(fileaway_utility::basename($file)), 'fa_index' => $key));
							$option_display = $recursive ? ($install ? fileaway_utility::replacefirst($file, $install, '') : $file) : preg_replace('/.csv$/i', '', fileaway_utility::basename($file));
							$option_display = $recursive ? str_replace('/', ' &gt; ', $option_display) : $option_display;
							$options[] = '<option value="'.$link.'" '.$is_selected.'>'.trim($option_display, '/').'</option>';
						}
					}
					$download_link = $filename && isset($file_index) && array_key_exists($file_index, $files)
						? 	'<a href="'.fileaway_utility::urlesc($url.'/'.$dir.'/'.$filename).'" '.
								'style="text-decoration:none!important; position:relative; top:4px; left:10px;" '.
								'class="ssfa-csv-download" download="'.$filename.'">'.
								'<span class="ssfa-icon-arrow-down-2" style="font-size:18px; color: #AAA;"></span>'.
							'</a>'
						:	null;
					$is_recursive = $recursive ? 'true' : 'false';	
					$deletecsv = $editor && isset($file_index) && array_key_exists($file_index, $files)
						? '<span id="ssfa-delete-csv-'.$uid.'" style="font-size:18px; position:relative; top:4px; left:20px; color:#AAA; cursor:pointer;" '.
							'class="ssfa-icon-remove" aria-hidden="true" data-uid="'.$uid.'"></span>' 
						: null;
					$newcsvspace = $deletecsv == null ? 10 : 30;
					$newcsv = $editor 
						? '<span id="ssfa-new-csv-'.$uid.'" style="font-size:18px; position:relative; top:4px; left:'.$newcsvspace.'px; color:#AAA; cursor:pointer;" '.
							'class="ssfa-icon-file-4" aria-hidden="true" data-uid="'.$uid.'" data-pg="'.$GLOBALS['post']->ID.'" data-path="'.base64_encode($dir).'" '.
							'data-recurse="'.$is_recursive.'" data-read="'.$read.'" data-write="'.$write.'"></span>' 
						: null;
					$select = 
						"<div class='ssfa-clearfix' style='float:left; margin-bottom:15px;'>".
							"<div id='ssfa-fileaway-values-$uid' style='text-align:left;'>".
								"<div style='text-align:left; margin-top:5px;'>".
									"<select class='chozed-select ssfa-fileaway-values-select' id='ssfa-fileaway-values-select-$uid' ".
										"data-placeholder=\"$placeholder\">".
										"<option></option>".
										implode(' ',$options).
									"</select>".
									$download_link.
									$deletecsv.
									$newcsv.
								"</div>".
							"</div>".
						"</div>";
					$selectscript = 
						"<script>".
							"jQuery(document).ready(function($){ ".
								"var select = $('select#ssfa-fileaway-values-select-$uid'); ".
								"$(select).chozed({ ".
									"allow_single_deselect:true, ".
									"width: '300px', ".
									"inherit_select_classes:true, ".
									"no_results_text: fileaway_mgmt.no_results, ".
									"search_contains: true ".
								"}); ".
								"$(select).on('change', function(){ ".
									"var shadow = $('<div id=\"ssfa-values-shadow\" style=\"display:none;\" />'); ".
									"$('body').append(shadow); ".
									"shadow.fadeIn(1000); ".
									"$('body').css('cursor', 'progress'); ".
									"if($(this).val() == '') window.location.href = \"".$original_url."\"; ".
									"else window.location.href = $(this).val(); ".
								"}); ".
							"}); ".
						"</script>";
				}
			}
			if(!$filename && !$showoptions) return 'No file found.';
			$edit = $editor ? 'true' : 'false';
			$mobileclass = $get->is_mobile ? 'ssfa-mobile' : null;
			$thefiles .= $clearfix.
				"<div id='$name' class='ssfa-meta-container ssfa-fileaway-values $mobileclass' data-editor='$edit' data-uid='$uid' data-fvn=\"".$nonce."\" style='opacity:0; margin: 10px 0 20px; $howshouldiputit'>";
			if($filename && is_file($rootpath.$dir.'/'.$filename))
			{
				$disablesort = $sorting ? false : "data-sort='false'";
				$heading = $showoptions 
					? $select 
					: '<a href="'.fileaway_utility::urlesc($url.'/'.$dir.'/'.$filename).'" download="'.$filename.'" '.
						'style="text-decoration:none; color:inherit!important;">'.$filename.'</a>';
				include fileaway_dir.'/lib/inc/inc.precontent.php';
				$thefiles .= 
					"<script type='text/javascript'>jQuery(function(){ jQuery('.footable').footable();});</script>".
					"<table id='ssfa-table-$uid' class='ssfa-values-table footable ssfa-sortable $theme $textalign' data-filter='#filter-$uid' data-uid='$uid' ".
						"data-theme=\"$theme\" data-src=\"".base64_encode($dir.'/'.$filename)."\" data-filename=\"".fileaway_utility::basename($filename)."\" ".
						"data-read=\"$read\" data-write=\"$write\" $disablesort $page>".
						"<thead><tr>";	
				ini_set('auto_detect_line_endings', TRUE);
				$filename = $rootpath.$dir.'/'.$filename;
				if(file_exists($filename) && is_readable($filename))
				{
					$csv = new fileaway_csv();
					$csv->encoding($read, $write);
					$csv->auto($filename);
					$rows = $csv->data;
					$headers = $csv->titles;
					$editorcursor = $editor ? 'style="cursor:cell"' : null;
					$editorcontext = $editor ? 'class="ssfa-values-context"' : null;
					foreach($headers as $key => $header)
					{
						$sortinitial = $key < 1 ? " data-sort-initial='true'" : null;
						$initialclass = $key < 1 ? 'class="'.$theme.'-first-column"' : null;
						$thefiles .= "<th id='ssfa-values-header-$uid-$key' $initialclass data-colnum=\"$key\" data-col=\"$header\" $sortinitial>".$header."</th>";
					}	
					$thefiles .= "</tr></thead><tfoot><tr><td colspan='100'>$pagearea</td></tr></tfoot><tbody>"; 
					foreach($rows as $k => $row)
					{
						$thefiles .= "<tr id='ssfa-values-$uid-$k' $editorcontext  data-row='$k'>";
						foreach($headers as $key=> $header)
						{ 
							$col1class = $key < 1 ? "class='$theme-first-column'" : null;
							$input = $editor 
								? '<input type="text" id="input-ssfa-values-'.$uid.'-'.$k.'-'.$key.'" data-row="'.$k.'" data-col="'.$header.'" data-colnum="'.$key.'" '.
									'value="'.$row[$header].'" style="display:none; width:90%">' 
								: null;
							$thefiles .= 
								'<td id="cell-ssfa-values-'.$uid.'-'.$k.'-'.$key.'" '.$col1class.' '.$editorcursor.'>'.
									'<span id="value-ssfa-values-'.$uid.'-'.$k.'-'.$key.'" data-row="'.$k.'" data-col="'.$header.'" data-colnum="'.$key.'">'.
										$row[$header].
									'</span>&nbsp;'.
									$input.
								'</td>';
						}
						$thefiles .= "</tr>";	
					}
					$thefiles .= "</tbody></table></div>";
				}
			}
			elseif($showoptions) $thefiles .= $select;
			$thefiles .= $showoptions ? $selectscript : null;
			$thefiles .= "<script> jQuery(document).ready(function($){ setTimeout(function(){ $('div#".$name."').animate({opacity:'1'},1000); }, 1000 ); }); </script>";
			$thefiles .= "</div>$clearfix";
			return $thefiles;
		}
	}
}