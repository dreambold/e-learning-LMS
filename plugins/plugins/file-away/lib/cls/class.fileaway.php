<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('fileaway'))
{
	class fileaway extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('fileaway', array($this, 'sc'));
		}
		public function sc($atts)
		{
			if(isset($atts['style'])) $atts['theme'] = $atts['style'];
			$get = new fileaway_definitions;
			extract($get->pathoptions);
			extract($this->correctatts(wp_parse_args($atts, $this->fileaway), $this->shortcodes['fileaway'], 'fileaway'));
			if($devices == 'mobile' && !$get->is_mobile) return;
			elseif($devices == 'desktop' && $get->is_mobile) return;
			if(!fileaway_utility::visibility($hidefrom, $showto)) return;
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			include fileaway_dir.'/lib/inc/inc.declarations.php';
			include fileaway_dir.'/lib/inc/inc.base.php';
			extract(fileaway_utility::dynamicpaths($dir, $playbackpath));
			if($debug == 'on' && $logged_in) return $this->debug($rootpath.$dir); 
			if(isset($atts['tutorials'])) $dir = fileaway_dir.'/lib/tts';
			if(!is_dir($dir) && $makedir && (!$private_content || ($private_content && $logged_in && stripos($dir, 'fa-nullmeta') === false)) && mkdir($rootpath.$dir, 0775, true)) fileaway_utility::indexmulti($rootpath.$dir, $chosenpath); 
			if(!is_dir($dir)) return;
			if(!fileaway_utility::realpath($dir,$rootpath,$chosenpath)) return;
			$start = $dir;
			$uid = rand(0, 9999); 
			$name = ($name ? $name : 'ssfa-meta-container-'.$uid );
			$manager = $playback ? false : $manager;
			if($manager) include fileaway_dir.'/lib/inc/inc.manager-access.php';
			if($manager) $encryption = false;
			if($metadata) global $wpdb;
			if($type != 'table') $bulkdownload = false;
			$limit = $manager ? false : $limit;
			$bulkclass = $bulkdownload ? 'bd-table' : ($manager ? 'mngr-table' : null);
			include fileaway_dir.'/lib/inc/inc.styles.php'; 
			$fadeit = $fadein ? ($fadein == 'opacity' ? 'opacity:0;' : 'display:none;') : null;
			if($fadein)
			{
				$fadescript = $fadein == 'opacity' ? '.animate({opacity:"1"}, '.$fadetime.');' : '.fadeIn('.$fadetime.');';
				$thefiles .= '<script> jQuery(document).ready(function($){ setTimeout(function(){ $("div#'.$name.'")'.$fadescript.' }, 1000); }); </script>';
			}
			$mobileclass = $get->is_mobile ? 'ssfa-mobile' : null;
			$flightbox_nonce = !empty($flightbox) ? 'data-fbn="'.wp_create_nonce('fileaway-flightbox-nonce').'"' : '';
			$flightbox_class = !empty($flightbox) ? 'flightbox-parent' : '';
			$thefiles .= $clearfix.'<div id="'.$name.'" class="ssfa-meta-container '.$flightbox_class.' '.$mobileclass.' '.$class.'" data-uid="'.$uid.'" '.$flightbox_nonce.' style="margin: 10px 0 20px; '.$fadeit.' '.$howshouldiputit.'">';
			$location_nonce = 'fileaway-location-nonce-'.base64_encode(trim(trim($rootpath.$dir,'/'),'\\'));
			$thefiles .= '<input type="hidden" id="location_nonce_'.$uid.'" data-uid="'.$uid.'" value="'.wp_create_nonce($location_nonce).'" />';
			if($directories)
			{ 
				$recursive = false;
				include fileaway_dir.'/lib/inc/inc.open-drawer.php';
			}
			if($showrss) include fileaway_dir.'/lib/inc/inc.rss-link.php';
			if($directories) include fileaway_dir.'/lib/inc/inc.directories-nav.php';
			include fileaway_dir.'/lib/inc/inc.stats-redirects.php';
			include fileaway_dir.'/lib/inc/inc.precontent.php';
			include fileaway_dir.'/lib/inc/inc.thead.php';
			include fileaway_dir.'/lib/inc/inc.directories.php';
			$files = $recursive ? fileaway_utility::recursefiles($dir, $onlydirs, $excludedirs) : scandir($dir); 
			$count = 0; 
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			include fileaway_dir.'/lib/inc/inc.file-array.php';
			include fileaway_dir.'/lib/inc/inc.dynamic-links.php';
			$fcount = empty($rawnames) ? 0 : count($rawnames);
			if($fcount < 1 && !$directories) return; 
			if($playback)
			{ 
				$GLOBALS['fileaway_playback_script'] = true; 
				if($playback == 'compact') $playaway = new playaway;
				$sources = $get->filegroups['audio'][2]; 
				$used = array(); 
			}
			include fileaway_dir.'/lib/inc/inc.thumbnails-setup.php';
			if(is_array($rawnames))
			{
				include fileaway_dir.'/lib/inc/inc.sort.php';
				if($type == 'table' && !$manager && $bannerize) include fileaway_dir.'/lib/inc/inc.bannerize.php';
				foreach($rawnames as $k => $rawname)
				{
					if($playback && in_array($rawname, $used) && in_array($exts[$k], $sources)) continue;
					$link = $links[$k];
					$loc = $locs[$k]; 
					$ext = $exts[$k]; 
					$oext = $ext; 
					$extension = strtolower($ext); 
					$full = $fulls[$k]; 
					$dir = $dirs[$k];
					$thetime = $times[$k];
					$file = $full;
					$dynamiclink = $dynamics[$k];
					$bannerad = isset($bannerads[$k]) ? $bannerads[$k] : false;
					if($s2mem)
					{
						list($trash, $s2dir) = explode('s2member-files', $dir);	
						$s2dir = trim($s2dir, '/');
						$s2dir = $s2dir == '' ? $s2dir : $s2dir.'/';
					}
					if($onlydirs && is_array($onlydirs))
					{ 
						foreach($onlydirs as $only)
						{ 
							$keeper = 0; 
							if(strpos("$dir", "$only") !== false)
							{ 
								$keeper = 1; 
								break;
							} 
						}
						if(!$keeper) continue; 
					}
					if($excludedirs && is_array($excludedirs))
					{ 
						foreach($excludedirs as $ex) if(strpos("$dir", "$ex") !== false) continue 2; 
					}
					include fileaway_dir.'/lib/inc/inc.prettify.php';
					if(!$dynamiclink && (is_dir($dir.'/'.$file) || $thename == '')) continue;
					$link = $encryption && !$dynamiclink &&!$bannerad ? $encrypt->url($rootpath.$dir.'/'.$file) : $link;
					include fileaway_dir.'/lib/inc/inc.thumbnails.php';
					include fileaway_dir.'/lib/inc/inc.colors.php';
					$datemodified = $type != 'table' && $mod == 'yes' 
						? "<div class='ssfa-datemodified'>".sprintf(_x('Last modified %s at %s', 'For List Types: *Date* at *Time*', 'file-away'), $date, $time)."</div>" 
						: null;
					$listfilesize = $type != 'table' && $size != 'no' 
						? ($theme == 'ssfa-minimal-list' 
							? "<span class='ssfa-listfilesize'>($fsize)</span>" 
							: "<span class='ssfa-listfilesize'>$fsize</span>") 
						: null;
					$link = $s2mem && !$manager ? $url.'/?s2member_file_download='.$s2dir.$file.$s2skip : $link;
					$fulllink = 'href="'.$link.'"';
					$redirect = $dynamiclink || $bannerad ? false : $redirect;
					$statstatus = $stats && !$dynamiclink && !$bannerad ? 'true' : 'false';
					$fulllink = $redirect ? 'href="'.$this->op['redirect'].'"' : $fulllink;
					include fileaway_dir.'/lib/inc/inc.icons.php'; 
					$linktype = $s2mem || $encryption ? '' : $linktype;
					$linktype = $dynamiclink || $redirect ? 'target="_blank"' : $linktype;
					if($flightbox && !$bannerad && !fileaway_utility::startswith($file, '_thumb_')) 
						include fileaway_dir.'/lib/inc/inc.flightbox.php';
					$audiocorrect = null;
					if($playback) 
					{
						include fileaway_dir.'/lib/inc/inc.playback.php'; 
						if($skipthis) continue; 						
					}
					else
					{ 
						$player = null; 
						$players = null;
					}
					$thename = $prettify ? $thename : fileaway_utility::strtotitle($thename);
					$thename = "<span class='ssfa-filename' $audiocorrect>".$thename."</span>"; 
					$count += 1;
					if($nolinks && !$dynamiclink)
					{
						$nolinkslist = "<a id='ssfa-$uid-$count' href='javascript:' class='$display$noicons$colors' style='cursor:default'>"; 
						$nolinkstable = "<a id='ssfa-$uid-$count' href='javascript:' class='$colors' style='cursor:default'>"; 
						$players = null;
					}
					else
					{	
						$nolinkslist = "<a id='ssfa-$uid-$count' class='$display$noicons$colors' $fulllink $linktype data-stat='$statstatus'>"; 
						$nolinkstable = "<a id='ssfa-$uid-$count' class='$colors' $fulllink $linktype data-stat='$statstatus'>";
					}
					if(!$type || $type != 'table')
						$thefiles .= 
							"$nolinkslist".
								"<div class='ssfa-listitem $ellipsis'>".
									"<span class='ssfa-topline'>$icon $thename $listfilesize</span> ".
									"$datemodified".
								"</div>".
							"</a>"; 				
					elseif($type == 'table' && $bannerad)
					{
						$src = str_replace($rootpath.$dir, rtrim($url, '/').'/'.$dir, $rootpath.$dir.'/'.$file); 
						$thefiles .= 
							"<tr id='ssfa-banner-$uid-$count' class='fileaway-dynamic fileaway-banner'>".
								"<td id='filetype-ssfa-file-$uid-$count' colspan='100' class='ssfa-banner $theme-first-column'>".
									"<a href=\"$link\" rel='nofollow' target='_blank'><img src=\"$src\" style='width:100%; border:0;'></a>".
								"</td>".
							"</tr>";	
					}
					elseif($type == 'table')
					{
						$oext = $manager || $bulkdownload ?  'data-ext="'.$oext.'"' : null;
						$filepath = $manager || $bulkdownload ? 'data-path="'.$dir.'"' : null;
						$oldname = $manager || $bulkdownload ? 'data-name="'.$rawname.'"' : null;
						$salvaged_filename = $manager ? trim($salvaged_filename) : $salvaged_filename;
						if($manager && $customdata) 
							$fileinput = '<input id="rawname-ssfa-file-'.$uid.'-'.$count.'" type="text" value="'.$salvaged_filename.'" '.
								'style="width:80%; height:26px; font-size:12px; text-align:center; display:none">';
						elseif($manager && !$customdata) 
							$fileinput = '<input id="rawname-ssfa-file-'.$uid.'-'.$count.'" type="text" value="'.$rawname.'" '.
								'style="width:80%; height:26px; font-size:12px; text-align:center; display:none">';
						else $fileinput = null;
						if($playback && in_array($rawname, $used))
						{ 
							if($has_sample && $playback === 'compact')
							{ 
								$iconarea = $player; 
								$thefinalname = $thename;
							}
							elseif($has_sample && $playback === 'extended')
							{ 
								$iconarea = "<br>$nolinkstable$icon</a>"; 
								$thefinalname = $thename.$players.$player; 
								$players = null;
							}
							elseif(!$has_sample && $has_multiple)
							{ 
								$thefinalname = $thename; 
								$iconarea = "<br>$nolinkstable$icon</a>"; 
							}
							elseif(!$has_sample && !$has_multiple){ 
								$iconarea = "$nolinkstable$icon $ext</a>"; 
								$thefinalname = "$nolinkstable$thename</a>"; 
								$players = null;
							}
						}
						else
						{
							$iconarea = "$nolinkstable$icon $ext</a>"; 
							$thefinalname = "$nolinkstable$thename</a>"; 
							$players = null;
						}
						if($getthumb) $iconarea = "$nolinkstable$icon</a>";
						$dynamicclass = $dynamiclink ? 'fileaway-dynamic' : '';
						$thefiles .= 
							"<tr id='ssfa-file-$uid-$count' class='$dynamicclass'>".
								"<td id='filetype-ssfa-file-$uid-$count' class='ssfa-sorttype $theme-first-column' $oext>$iconarea</td>".
								"<td id='filename-ssfa-file-$uid-$count' class='ssfa-sortname' $filepath $oldname>$thefinalname$players $fileinput</td>";
						if($customdata && is_array($customarray))
						{
							if($metadata) 
							{
								$customdataclassname = 'fileaway-metadata';
								$mdata_value = $wpdb->get_row($wpdb->prepare("SELECT metadata FROM ".$wpdb->prefix."fileaway_metadata WHERE file = %s", $dir.'/'.$file));
								if($mdata_value) $customvalues = unserialize($mdata_value->metadata);
								else $customvalues = explode(',', $customvalue);
							}
							else 
							{
								$customvalues = explode(',', $customvalue);
								$customdataclassname = 'fileaway-customdata';
							}
							foreach($customarray as $z => $customdatum)
							{
								if($customdatum !== ' ')
								{
									$value = !isset($customvalues[$z]) ? '' : ($prettify ? $customvalues[$z] : fileaway_utility::strtotitle(trim($customvalues[$z])));
									$custominput[$z] = $manager 
										? '<input class="'.$customdataclassname.'" id="customdata-'.$z.'-ssfa-file-'.$uid.'-'.$count.'" type="text" value="'.$value.'" '.
											'style="width:80%; height:26px; font-size:12px; text-align:center; display:none">' 
										: null;
									$thefiles .= "<td id='customadata-cell-$z-ssfa-file-$uid-$count' class='ssfa-sortcustomdata'>".
										"<span id='customadata-$z-ssfa-file-$uid-$count'>"."$value"."</span>".trim($custominput[$z])."</td>";
								}
							}
						}
						$thefiles .= $mod !== 'no' ? "<td id='mod-ssfa-file-$uid-$count' class='ssfa-sortdate' data-value='$sortdatekey'>$sortdate</td>" : null;
						$thefiles .= $size !== 'no' ? "<td id='size-ssfa-file-$uid-$count' class='ssfa-sortsize' data-value='$bytes'>$fsize</td>" : null;
						if($manager)
						{
							$thefiles .= "<td id='manager-ssfa-file-$uid-$count' class='ssfa-sortmanager'>";
							if(!$dynamiclink)
							{		
								$thefiles .=	
									"<a href='' id='rename-ssfa-file-$uid-$count'>".__('Rename', 'file-away')."</a><br>".
									"<a href='' id='delete-ssfa-file-$uid-$count'>".__('Delete', 'file-away')."</a>";
							}
							$thefiles .= "</td>"; 
						}
						$thefiles .= '</tr>'; 
					}
				} 
			}
			$thefiles .= $type == 'table' ? '</tbody></table>' : null;
			if($manager) include fileaway_dir.'/lib/inc/inc.bulk-action-content.php';
			elseif($bulkdownload) include fileaway_dir.'/lib/inc/inc.bulk-download-content.php';
			$thefiles .= "</div></div>$clearfix";	
			if($flightbox && $fb) 
			{
				$thefiles .= '<script>FlightBoxes['.$uid.'] = '.$fb.'; ';
				if(count($boximages) > 0) $thefiles .= implode(' ', $boximages);
				$thefiles .= '</script>';
			}
			date_default_timezone_set($original_timezone);
			if($private_content && $logged_in && $count > 0) return $thefiles; 	
			elseif(!$private_content && $count > 0) return $thefiles; 
			elseif($directories && (!$private_content || ($logged_in && $private_content))) return $thefiles;
			else return;
		}
		public function debug($dir)
		{
			return
				'<div style="background:#FFFFFF; border: 5px solid #CFCAC5; border-radius:0px; padding:20px; color:#444;">'.
					'<img src="'.fileaway_url.'/lib/img/fileaway_banner.png" style="width:300px; box-shadow:none!important; border:0!important;"><br><br>'.
					'Your File Away shortcode is pointing to the following directory:<br /><br />'.
					'<code class="ssfa-code">'.$dir.'</code><br /><br />'.
					'Remember that if you want to display files recursively, you need to enable recursive mode with <code class="ssfa-code">recursive="on"</code> '.
					'or directory tree navigation with <code class="ssfa-code">directories="on"</code>.<br /><br />'.
					'Maybe you used one of these?<br /><br />'.
					'<code class="ssfa-code">fa-firstlast</code> '.
					'<code class="ssfa-code">fa-username</code> '.
					'<code class="ssfa-code">fa-userid</code> '.
					'<code class="ssfa-code">fa-userrole</code><br /><br />'.
					'If you used one of the four dynamic path codes, then the path above is going to be different for every logged-in user.<br /><br />'.
					'Sincerely, <br />'.
					'MGMT'.
				'</div>';
		}
	}
}