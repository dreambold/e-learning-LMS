<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if($thumbnails) $getthumb = in_array($extension, array('jpg','jpeg','gif','png','pdf','flv','mp4','m4v','webm','ogv','tube','vmeo')) ? true : false;
if($manager && $thumbnails && stripos($file, '_thumb_') !== false) $getthumb = false;
if($bannerad) $getthumb = false;
if($getthumb)
{
	if(in_array($extension, array('flv', 'mp4', 'm4v', 'webm', 'ogv', 'tube', 'vmeo')))
	{
		$thumbpath = $rootpath.$dir.'/_thumb_vid_'.$rawname;
		$tempfile = fileaway_utility::urlesc($file);
		if(!is_file($thumbpath.'.jpg') && !is_file($thumbpath.'.png') && !in_array($extension, array('tube','vmeo'))) $getthumb = false;
		else 
		{
			$vidthumbext = is_file($thumbpath.'.png') ? '.png' : '.jpg';
			if($extension == 'tube')
			{
				if(is_file($thumbpath.$vidthumbext)) $thumblink = fileaway_utility::urlesc(str_replace($rootpath, $url.'/', $thumbpath).$vidthumbext);
				else
				{
					if(stripos($link, 'youtu.be/') !== false)
					{
						$youtube = explode('youtu.be/', $link);
						$yt = explode('?', $youtube[1]);
						$vid_id = $yt[0];				
					}
					else
					{
						$youtube = explode('?', $link);
						parse_str($youtube[1], $yt);
						$vid_id = $yt['v'];
					}
					$thumblink = 'http://img.youtube.com/vi/'.$vid_id.'/mqdefault.jpg';
				}
			}
			elseif($extension == 'vmeo')
			{
				if(is_file($thumbpath.$vidthumbext)) $thumblink = fileaway_utility::urlesc(str_replace($rootpath, $url.'/', $thumbpath).$vidthumbext);
				else
				{
					$vimeo = explode('vimeo.com/', $link);
					$vid_id = trim($vimeo[1], '/');
					$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vid_id.php"));
					$thumblink = $hash[0]['thumbnail_medium'];  
				}
			}
			else
			{
				$thumblink = str_replace($tempfile, '_thumb_vid_'.fileaway_utility::replacelast($tempfile, '.'.$oext, $vidthumbext), $links[$k]);
			}
		}
	}
	elseif($thumbnails !== "permanent")
	{
		$imgprop = getimagesize($rootpath.$dir.'/'.$file);
		while($getthumb)
		{
			if($extension == 'pdf') 
			{
				$getthumb = false;
				break;
			}
			if(isset($imgprop[0]) && $maxsrcwidth && $imgprop[0] > $maxsrcwidth)
			{ 
				$getthumb = false;
				break;
			}
			if(isset($imgprop[1]) && $maxsrcheight && $imgprop[1] > $maxsrcheight)
			{
				$getthumb = false;
				break; 
			}
			if($maxsrcbytes && $bytes > $maxsrcbytes)
			{ 
				$getthumb = false; 
				break;
			}
			break;
		}
	}
	elseif($thumbnails == 'permanent')
	{		
		if($extension == 'pdf' && function_exists('exec'))
		{ 
			$thumbpath = $rootpath.$dir.'/_thumb_'.$thumbfix.$rawname.'.jpg';
			$tempfile = fileaway_utility::urlesc($file);
			if(!is_file($thumbpath))
			{
				$pdfpath = $rootpath.$dir.'/'.$file;
				//exec("convert \"{$pdfpath}[0]\" -colorspace RGB -geometry 60x40 $thumbpath");
				//exec("convert -define jpeg:size=120x60 \"{$pdfpath}[0]\" -colorspace RGB -geometry 120x60 $thumbpath");
				if($thumbsize == 'large') exec("convert -define jpeg:size=180x180 \"{$pdfpath}[0]\" -colorspace RGB -thumbnail 180x180 -gravity center -crop 180x180+0+0 +repage $thumbpath");
				elseif($thumbsize == 'medium') exec("convert -define jpeg:size=120x120 \"{$pdfpath}[0]\" -colorspace RGB -thumbnail 120x120 -gravity center -crop 120x120+0+0 +repage $thumbpath");
				else exec("convert -define jpeg:size=60x60 \"{$pdfpath}[0]\" -colorspace RGB -thumbnail 60x60 -gravity center -crop 60x60+0+0 +repage $thumbpath");
			}
			$thumblink = is_file($thumbpath) ? str_replace($tempfile, '_thumb_'.$thumbfix.fileaway_utility::replacelast($tempfile, '.pdf', '.jpg'), $links[$k]) : false;
		}
		else 
		{
			$tempfile = fileaway_utility::urlesc($file);
			if(!is_file($rootpath.$dir.'/_thumb_'.$thumbfix.$file)) 
			{
				fileaway_utility::createthumb($rootpath.$dir.'/'.$file, $rootpath.$dir.'/_thumb_'.$thumbfix.$file, $extension, $thumbwidth, $thumbheight);
			}
			$thumblink = is_file($rootpath.$dir.'/_thumb_'.$thumbfix.$file) ? str_replace($tempfile, '_thumb_'.$thumbfix.$tempfile, $links[$k]) : false;
		}
	}
}