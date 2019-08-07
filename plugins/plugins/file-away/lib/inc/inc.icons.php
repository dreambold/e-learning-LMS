<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$linktype = false;
if($is_IE || $is_safari) $linktype = 'target="_blank"';
elseif($this->op['newwindow'] !== '')
{
	$newwindows = preg_split("/(,\s|,)/", preg_replace('/\s+/', ' ', $this->op['newwindow']), -1, PREG_SPLIT_NO_EMPTY);
	if(is_array($newwindows))
	{
		foreach($newwindows as $new)
		{
			if($extension === strtolower($new) || '.' . $extension === strtolower($new))
			{ 
				$linktype = 'target="_blank"'; 
				break; 
			}
		}
	}
}
if(!$linktype) $linktype = 'download="'.$rawname.'.'.$oext.'"';
if(!$icons || $icons === 'filetype')
{
	$icontype = false; 
	$icon = null;
	while(!$icontype)
	{
		if($extension === 'link')
		{
			 $icon = '&#57483;';
			 $icontype = 'dynamiclink';
			 break;
		}
		if($extension === 'tube')
		{
			 $icon = '&#x3b;';
			 $icontype = 'youtube';
			 break;
		}
		if($extension === 'vmeo')
		{
			 $icon = '&#xe137;';
			 $icontype = 'vimeo';
			 break;
		}		
		if(in_array($extension, $get->filegroups['adobe'][2]))
		{
			$icon = $get->filegroups['adobe'][1]; 
			$icontype = 'adobe'; 
			break;
		}
		if(in_array($extension, $get->filegroups['image'][2]))
		{ 
			$icon = $get->filegroups['image'][1];
			$icontype = 'image';
			break; 
		}
		if(in_array($extension, $get->filegroups['audio'][2]))
		{ 
			$icon = $get->filegroups['audio'][1];
			$icontype = 'audio';
			break; 
		}
		if(in_array($extension, $get->filegroups['video'][2]))
		{ 
			$icon = $get->filegroups['video'][1]; 
			$icontype = 'video'; 
			break;
		}
		if(in_array($extension, $get->filegroups['msdoc'][2]))
		{ 
			$icon = $get->filegroups['msdoc'][1]; 
			$icontype = 'msdoc'; 
			break;
		}
		if(in_array($extension, $get->filegroups['msexcel'][2]))
		{ 
			$icon = $get->filegroups['msexcel'][1]; 
			$icontype = 'msexcel'; 
			break; 
		}
		if(in_array($extension, $get->filegroups['powerpoint'][2]))
		{ 
			$icon = $get->filegroups['powerpoint'][1]; 
			$icontype = 'powerpoint'; 
			break;
		}
		if(in_array($extension, $get->filegroups['openoffice'][2]))
		{
			$icon = $get->filegroups['openoffice'][1]; 
			$icontype = 'openoffice'; 
			break;
		}
		if(in_array($extension, $get->filegroups['text'][2]))
		{ 
			$icon = $get->filegroups['text'][1]; 
			$icontype = 'text'; 
			break;
		}
		if(in_array($extension, $get->filegroups['compression'][2]))
		{ 
			$icon = $get->filegroups['compression'][1]; 
			$icontype = 'compression'; 
			break;
		}
		if(in_array($extension, $get->filegroups['application'][2]))
		{ 
			$icon = $get->filegroups['application'][1]; 
			$icontype = 'application'; 
			break;
		}
		if(in_array($extension, $get->filegroups['script'][2]))
		{ 
			$icon = $get->filegroups['script'][1];
			$icontype = 'script'; 
			break;
		}
		if(in_array($extension, $get->filegroups['css'][2]))
		{ 
			$icon = $get->filegroups['css'][1]; 
			$icontype = 'css'; 
			break;
		}
		$icon = $get->filegroups['unknown'][1]; 
		$icontype = 'unknown'; 
	}
	$iconstyle = $type == 'table' ? 'ssfa-faminicon' : 'ssfa-listicon';
	$icon = "<span data-ssfa-icon='$icon' class='$iconstyle $icocol' aria-hidden='true'></span>";
	$icon = $type == 'table' ? $icon.'<br />' : $icon;
}
else
{
	$papersize = $type == 'table' ? ' style="font-size:18px;"' : null;
	$icon = $icons == 'paperclip' ? "<span data-ssfa-icon='&#xe1d0;' class='ssfa-paperclip $icocol' $papersize aria-hidden='true'></span>" : null;
	$icon = $type == 'table' ? $icon.'<br />' : $icon;
}
if($getthumb)
{
	if(in_array($extension, array('flv', 'mp4', 'm4v', 'webm', 'ogv', 'tube', 'vmeo')))
	{
		$icon = $thumblink 
			? 	'<div class="ssfa-thumb ssfa-thumb-'.$thumbstyle.$graythumbs.'" style="background-image:url('.$thumblink.'); width:'.$thumbwidth.'px; height:'.$thumbheight.'px;">'.
					$playoverlay.
				'</div>' 
			:	 $icon;
		if(!$thumblink) $getthumb = false;
	}
	elseif($thumbnails == 'permanent')
	{
		$icon = $thumblink ? '<img src="'.$thumblink.'" class="ssfa-thumb ssfa-thumb-'.$thumbstyle.$graythumbs.'">' : $icon;
		if(!$thumblink) $getthumb = false;
	}
	else
	{
		$thumb = fileaway_url.'/lib/inc/ext.thumbnails.php?fileaway='.base64_encode($rootpath.$dir.'/'.$file).'&width='.$thumbwidth.'&height='.$thumbheight.'';
		$icon = '<img src="'.$thumb.'" class="ssfa-thumb ssfa-thumb-'.$thumbstyle.$graythumbs.'">';
	}
}