<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('attachaway'))
{
	class attachaway extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('attachaway', array($this, 'sc'));
		}
		public function sc($atts)
		{
			if(isset($atts['style'])) $atts['theme'] = $atts['style'];
			$get = new fileaway_definitions;
			extract($get->pathoptions);
			extract($this->correctatts(wp_parse_args($atts, $this->attachaway), $this->shortcodes['attachaway'], 'attachaway'));
			if($devices == 'mobile' && !$get->is_mobile) return;
			elseif($devices == 'desktop' && $get->is_mobile) return;
			if(!fileaway_utility::visibility($hidefrom, $showto)) return;
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			$attachaway = true;
			$statstatus = 'false';
			$iss2 = false;
			$rsslink = null;
			$crumbies = null;
			$clearfix = $align == 'none' ? "<div class='ssfa-clearfix'></div>" : null;
			$boximages= array();
			$flightbox = $get->is_mobile ? false : $flightbox;
			$randcolor = array("red","green","blue","brown","black","orange","silver","purple","pink");
			$fb = 0;
			$count = 0;
			$thefiles = '';
			$uid = rand(0, 9999); 
			global $post, $is_IE, $is_safari; 
			$ascdesc = $desc ? 'DESC' : 'ASC'; 
			$id = $postid ? $postid : $post->ID;
			$attachments = get_posts
			(
				array
				(
					'orderby' => $orderby,
					'order' => $ascdesc,
					'post_type' => 'attachment',
					'posts_per_page' => -1,
					'post_parent' => $id
				)
			); 
			if($debug === 'on' && is_user_logged_in()) return $this->debug($id, $attachments); 
			include fileaway_dir.'/lib/inc/inc.styles.php';
			$fadeit = $fadein ? ($fadein == 'opacity' ? 'opacity:0;' : 'display:none;') : null;
			if($fadein)
			{
				$fadescript = $fadein == 'opacity' ? '.animate({opacity:"1"}, '.$fadetime.');' : '.fadeIn('.$fadetime.');';
				$thefiles .= '<script> jQuery(document).ready(function($){ setTimeout(function(){ $("div#ssfa-meta-container-'.$uid.'")'.$fadescript.' }, 1000); }); </script>';
			}
			$mobileclass = $get->is_mobile ? 'ssfa-mobile' : null;
			$flightbox_nonce = !empty($flightbox) ? 'data-fbn="'.wp_create_nonce('fileaway-flightbox-nonce').'"' : '';
			$flightbox_class = !empty($flightbox) ? 'flightbox-parent' : '';
			$thefiles .= "$clearfix<div id='ssfa-meta-container-$uid' data-uid='$uid' ".
				"$flightbox_nonce class='ssfa-meta-container $mobileclass $class $flightbox_class' style='margin: 10px 0 20px; $fadeit $howshouldiputit'>";
			include fileaway_dir.'/lib/inc/inc.precontent.php';
			if($type === 'table')
			{
				$typesort = null; 
				$filenamesort = null; 
				$capsort = null; 
				$dessort = null; 
				$sizesort = null;
				if($sortfirst === 'type') $typesort = " data-sort-initial='true'"; 
				elseif($sortfirst === 'type-desc') $typesort = " data-sort-initial='descending'"; 
				elseif($sortfirst === 'filename') $filenamesort = " data-sort-initial='true'"; 
				elseif($sortfirst === 'filename-desc') $filenamesort = " data-sort-initial='descending'";
				elseif($sortfirst === 'caption') $capsort = " data-sort-initial='true'"; 
				elseif($sortfirst === 'caption-desc') $capsort = " data-sort-initial='descending'";
				elseif($sortfirst === 'description') $dessort = " data-sort-initial='true'"; 
				elseif($sortfirst === 'description-desc') $dessort = " data-sort-initial='descending'";
				elseif($sortfirst === 'size') $sizesort = " data-sort-initial='true'"; 
				elseif($sortfirst === 'size-desc') $sizesort = " data-sort-initial='descending'";
				else $filenamesort = " data-sort-initial='true' "; 
	 			$disablesort = $sortfirst == 'disabled' ? "data-sort='false'" : false;
				$filenamesort = $disablesort ? null : $filenamesort;
				$filenamelabel = $filenamelabel ? $filenamelabel : _x('File&nbsp;Name', 'File Name Column', 'file-away');
				$thefiles .= 
					"<script type='text/javascript'>jQuery(function(){jQuery('.footable').footable();});</script>".
					"<table id='ssfa-table' data-filter='#filter-$uid' $disablesort $page class='footable ssfa-sortable $theme$textalign'><thead><tr>".
					"<th class='ssfa-sorttype $theme-first-column' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$typesort.">".
						_x('Type', 'File Type Column', 'file-away').
					"</th>".
					"<th class='ssfa-sortname' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$filenamesort.">".
						$filenamelabel.
					"</th>";
				$thefiles .= $capcolumn 
					? "<th class='ssfa-sortcapcolumn' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$capsort.">".$capcolumn."</th>" 
					: null;
				$thefiles .= $descolumn 
					? "<th class='ssfa-sortdescolumn' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$dessort.">".$descolumn."</th>" 
					: null;
				$thefiles .= $size !== 'no' 
					? "<th class='ssfa-sortsize' data-type='numeric' title=\""._x('Click to Sort', 'Column Sort Message', 'file-away')."\"".$sizesort.">".
						_x('Size', 'File Size Column', 'file-away')."</th>" 
					: null;
				$thefiles .= "</tr></thead><tfoot><tr><td colspan='100'>$pagearea</td></tr></tfoot><tbody>";
			}
			if(is_array($attachments))
			{ 
				foreach($attachments as $attachment)
				{
					extract(fileaway_utility::getattachment($attachment->ID)); 
					$filetype = wp_check_filetype($filelink); 
					$ext = $filetype['ext']; 
					$extension = $ext; 
					$oext = $ext; 					
					$basename = fileaway_utility::basename($filelink);
					$rawname = str_replace('.'.$ext, '', $basename); 
					$filename = str_replace(array('~', '-', '--', '_', '.', '*'), ' ', $rawname); 
					$title = $title ? $title : $filename;
					if($caption === strtoupper($caption) || $caption === strtolower($caption)) $caption = fileaway_utility::sentencecase($caption);
					if($description === strtoupper($description) || $description === strtolower($description)) fileaway_utility::sentencecase($description);
					$title = "<span class='ssfa-filename'>".fileaway_utility::strtotitle(strtolower($title))."</span>"; 
					$ext = !$ext ? '?' : $ext; 
					$ext = strtolower($ext); 
					$ext = substr($ext,0,4).'';
					$bytes = filesize(get_attached_file($attachment->ID));
					if($size !== 'no')
					{ 
						$fsize = fileaway_utility::formatBytes($bytes, 1); 
						$fsize = !preg_match('/[a-z]/i', $fsize) ? '1k' : ($fsize === 'NAN' ? '0' : $fsize);
					}
					include fileaway_dir.'/lib/inc/inc.colors.php';
					$listfilesize = $type !== 'table' && $size !== 'no' 
						? ($theme === "ssfa-minimal-list" 
							? "<span class='ssfa-listfilesize'> ($fsize)</span>" 
							: "<span class='ssfa-listfilesize'>$fsize</span>") 
						: null;
					$file = $basename;
					$manager = false;
					$onlyaudio = false;
					include fileaway_dir.'/lib/inc/inc.filters.php';
					if($excluded) continue; 
					$getthumb = false;
					$thumbnails = false;
					include fileaway_dir.'/lib/inc/inc.icons.php';
					$count += 1;
					$link = $filelink;
					$fulllink = 'href="'.$link.'"';
					if($flightbox) include fileaway_dir.'/lib/inc/inc.flightbox.php';
					if($type !== 'table')
					{ 
						$thefiles .= 
							"<a id='ssfa' class='$display$noicons$colors' $fulllink $linktype>".
							"<div class='ssfa-listitem $ellipsis'><span class='ssfa-topline'>$icon $title $listfilesize</span></div>".
							"</a>"; 
					}
					else
					{
						$thefiles .= 				
							"<tr><td id='filetype-ssfa-file-$uid-$count' class='ssfa-sorttype $theme-first-column'><a $fulllink $linktype>$icon $ext</a></td>".
							"<td id='filename-ssfa-file-$uid-$count' class='ssfa-sortname'><a $fulllink class='$colors' $linktype>$title</a></td>"; 
						$thefiles .= ($capcolumn ? "<td class='ssfa-sortcapcolumn'>$caption</td>" : null);
						$thefiles .= ($descolumn ? "<td class='ssfa-sortdescolumn'>$description</td>" : null);
						$thefiles .= ($size !== 'no' ? "<td class='ssfa-sortsize' data-value='$bytes'>$fsize</td>" : null);
						$thefiles .= '</tr>';
					} 
				}
				$thefiles .= $type === 'table' ? '</tbody></table></div>' : '</div>';
				$thefiles .= "</div>$clearfix";
			}
			if($flightbox && $fb) 
			{
				$thefiles .= '<script>FlightBoxes['.$uid.'] = '.$fb.'; ';
				if(count($boximages) > 0) $thefiles .= implode(' ', $boximages);
				$thefiles .= '</script>';
			}
			return $count > 0 ? $thefiles : null;
		}
		public function debug($id, $attachments)
		{
			$post_title = get_the_title($id);
			$idcheck = get_post($id);
			if(!$attachments)
			{ 
				if($idcheck)
				{ 
					if($post_title !== '') $post_title = '<em>'.$post_title.'</em>,'; 
					else $post_title = null;  
					return 
						"<div style='background:#FFFFFF; border: 5px solid #CFCAC5; border-radius:0px; padding:20px; color:#444;'>".
							"<img src='".fileaway_url."/lib/img/attachaway_banner.png' style='width:300px; box-shadow:none!important; border-radius:0!important; border:0!important'>".
							"<br /><br />".
							"You\'re trying to display attachments from $post_title post ID $id, but there\'s nothing attached to that one.".
						"</div>"; 
				}
				else
				{  
					return 
						"<div style='background:#FFFFFF; border: 5px solid #CFCAC5; border-radius:0px; padding:20px; color:#444;'>".
							"<img src='".fileaway_url."/lib/img/attachaway_banner.png' style='width:300px; box-shadow:none!important; border-radius:0!important; border:0!important'>".
							"<br /><br />".
							"You\'re trying to display attachments from post ID $id, but I\'m not sure that post even exists.".
						"</div>";
				}
			}
			else
			{
				if($post_title !== '') $post_title = "<em>$post_title</em>,"; 
				else $post_title = null;  
				return 
					"<div style='background:#FFFFFF; border: 5px solid #CFCAC5; border-radius:0px; padding:20px; color:#444;'>".
						"<img src='".fileaway_url."/lib/img/attachaway_banner.png' style='width:300px; box-shadow:none!important; border-radius:0!important; border:0!important'>".
						"<br /><br />".
						"You\'re trying to display attachments from $post_title post ID $id. It\'s got stuff attached. Maybe you\'ve excluded everything?'".
					"</div>"; 
			}
		}
	}
}