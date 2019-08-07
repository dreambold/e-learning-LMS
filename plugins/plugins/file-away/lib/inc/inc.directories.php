<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if($recursive || $directories)
{
	$globaldirexes = array(); $localdirexes = array(); 
	if($excludedirs) $localdirexes = preg_split('/(, |,)/', $excludedirs, -1, PREG_SPLIT_NO_EMPTY);
	if($this->op['direxclusions']) $globaldirexes = preg_split('/(, |,)/', $this->op['direxclusions'], -1, PREG_SPLIT_NO_EMPTY);
	if(!is_array($globaldirexes)) $globaldirexes = array();
	if(!is_array($localdirexes)) $localdirexes = array();
	$direxes = array_unique(array_merge($localdirexes, $globaldirexes)); 
	$excludedirs = count($direxes) > 0 ? $direxes : false;
	if(!$directories && $onlydirs) $onlydirs = $dir.','.rtrim($onlydirs);
	$justthesedirs = $onlydirs ? preg_split('/(, |,)/', $onlydirs, -1, PREG_SPLIT_NO_EMPTY) : 0; 
	$onlydirs = is_array($justthesedirs) && count($justthesedirs) > 0 ? $justthesedirs : 0;
}
if($directories)
{
	$recursive = false;
	$ccell = count($cells); 
	if($manager && $dirman)
	{
		$thefiles .= 
			"<tr id='row-ssfa-create-dir-$uid' class='ssfa-drawers'>".
				"<td id='folder-ssfa-create-dir-$uid' data-value='# # # #' class='ssfa-sorttype $theme-first-column'>".
					"<a id='ssfa-create-dir-$uid' href='javascript:' data-prettify='".(empty($prettify)?0:1)."'>".
						"<span style='font-size:20px; margin-left:3px;' class='ssfa-icon-chart-alt' aria-hidden='true'></span>".
						"<br>".__('new', 'file-away').
					"</a>".
				"</td>".
				"<td id='name-ssfa-create-dir-$uid' data-value='# # # #' class='ssfa-sortname'>".
					'<input id="input-ssfa-create-dir-'.$uid.'" type="text" placeholder="'.__('Name Your Sub-Directory', 'file-away').'" " value="" '.
						'style="width:90%; text-align:center; display:none">'.
				"</td>";
		$icell = 0;
		foreach($cells as $cell)
		{ 
			$icell++; 
			if($icell < $ccell) $thefiles .= "<td class='$theme' data-value='# # # #'> &nbsp; </td>"; 
			else $thefiles .= "<td id='manager-ssfa-create-dir-$uid' class='$theme' data-value='# # # #'> &nbsp; </td>";
		}
	}
	$subdircheck = glob($dir.'/*'); 
	$checksubdirs = is_array($subdircheck) ? array_filter($subdircheck, 'is_dir') : array();
	if(count($checksubdirs) > 0)
	{ 
		$f = 0;
		foreach(glob($dir.'/*', GLOB_ONLYDIR) as $k=> $folder)
		{
			$folder = str_replace('\\','/',$folder);
			if($iconcolor) $dir_icocol = " ssfa-$iconcolor"; 
			if($color && !$accent)
			{ 
				$dir_accent = $color; 
				$dir_colors = " ssfa-$color accent-$dir_accent"; 
			}
			if($color && $accent) $dir_colors = " ssfa-$color accent-$accent"; 
			if(($color) && !($iconcolor))
			{ 
				$dir_useIconColor = $randcolor[array_rand($randcolor)]; 
				$dir_icocol = " ssfa-$dir_useIconColor";
			}
			if(!($color) && ($iconcolor))
			{ 
				$dir_useColor = $randcolor[array_rand($randcolor)]; 
				$dir_colors = " ssfa-$dir_useColor accent-$dir_useColor";
			}
			if(!($color) && !($iconcolor))
			{ 
				$dir_useColor = $randcolor[array_rand($randcolor)]; 
				$dir_colors = " ssfa-$dir_useColor accent-$dir_useColor"; 
				$dir_icocol = " ssfa-$dir_useColor";
			}	
			$subrsslink = false;
			if($onlydirs)
			{ 
				$direxcluded = 1; 
				foreach($onlydirs as $onlydir)
				{ 
					if(strripos($folder, str_replace('\\','/',$onlydir)) !== false)
					{
						$direxcluded = 0; 
						continue;
					} 
				}
			}
			if($excludedirs)
			{ 
				foreach($excludedirs as $exclude) if(strripos($folder, str_replace('\\','/',$exclude)) !== false) continue 2; 
			}
			if(!$direxcluded)
			{			
				$f++; 
				$dlink = fileaway_utility::replacefirst($folder, $basebase, '');
				if($getrss)
				{
					$inilocation = is_file($rootpath.$basebase.'/'.trim($dlink, '/').'/_fa.feed.id.ini') ? $rootpath.$basebase.'/'.trim($dlink, '/').'/_fa.feed.id.ini' : false;
					if($inilocation)
					{
						$subini = parse_ini_file($inilocation);
						$subfeedid = $subini['id'];
						$subfeedfile = $rootpath.trim(str_replace('\\','/',$this->op['feeds']), '/').'/_feed_'.$subfeedid.'.xml';
						if(is_file($subfeedfile))
						{
							$subfeedurl = fileaway_utility::replacefirst($subfeedfile, $rootpath, rtrim(str_replace('\\','/',$this->op['baseurl']), '/').'/');
							$subrsslink = '<span class="ssfa-rssmini '.$dir_colors.'" data-href="'.$subfeedurl.'">rss</span>';
						}
					}		
				}
				$dirtext = $subrsslink ? null : _x('dir', 'abbrv. of *directory*', 'file-away');
				$folder = str_replace($dir.'/', '', $folder);
				$prettyfolder = $folder;
				if(!$prettify)
				{
					$prettyfolder = str_replace(array('~', '--', '_', '.', '*'), ' ', "$prettyfolder"); 
					$prettyfolder = preg_replace('/(?<=\D)-(?=\D)/', ' ', "$prettyfolder");
					$prettyfolder = preg_replace('/(?<=\D)-(?=\d)/', ' ', "$prettyfolder");
					$prettyfolder = preg_replace('/(?<=\d)-(?=\D)/', ' ', "$prettyfolder");
					$prettyfolder = fileaway_utility::strtotitle($prettyfolder);
				}
				$dpath = ltrim($dlink, '/'); 
				$dlink = str_replace('/', '*', $dpath);
				$managedir = $manager && $dirman 
					? 	"<a href='' id='rename-ssfa-dir-$uid-$f'>".__('Rename', 'file-away')."</a><br>".
						"<a href='' id='delete-ssfa-dir-$uid-$f'>".__('Delete', 'file-away')."</a></td>" 
					: 	' &nbsp; '; 
				$renamedir = $manager && $dirman ? '<input id="rename-ssfa-dir-'.$uid.'-'.$f.'" type="text" value="'.$folder.'" '.
					'style="width:90%; text-align:center; display:none">' : null;
				$thefiles .= 
					"<tr id='ssfa-dir-$uid-$f' class='ssfa-drawers'>".
						"<td id='folder-ssfa-dir-$uid-$f' data-value=\"# # # # # $folder\" class='ssfa-sorttype $theme-first-column'>".
							"<a href=\"".fileaway_utility::querystring(get_permalink(), $_SERVER["QUERY_STRING"], array("drawer".$drawerid => $dlink)).
								"\" data-name=\"".$folder."\" data-path=\"".$dpath."\">".
								"<span style='font-size:20px; margin-left:3px;' class='ssfa-faminicon ssfa-icon-$drawericon $dir_icocol' aria-hidden='true'></span>".
								"<br>".$dirtext.
							"</a>".
							$subrsslink.
						"</td>".
						"<td id='name-ssfa-dir-$uid-$f' data-value=\"# # # # # $folder\" class='ssfa-sortname'>".
							"<a href=\"".fileaway_utility::querystring(get_permalink(), $_SERVER["QUERY_STRING"], array("drawer".$drawerid => $dlink))."\" class=\"$dir_colors\">".
								"<span class='ssfa-filename' ".($prettify?'':"style='text-transform:uppercase;'").">$prettyfolder</span>".
							"</a>".$renamedir.
						"</td>"; 			
				$icell = 0;
				foreach ($cells as $cell)
				{
					$icell++; 
					$thefiles .= $icell < $ccell 
						? "<td class='$theme' data-value=\"# # # #  $folder\"> &nbsp; </td>" 
						: "<td id='manager-ssfa-dir-$uid-$f' class='$theme' data-value=\"# # # #  $folder\">$managedir</td>";
				}
				$thefiles .= "</tr>";
			}
		}
	}
}