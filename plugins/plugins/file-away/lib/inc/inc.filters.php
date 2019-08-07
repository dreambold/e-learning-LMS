<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$excluded = false;
while(!$excluded)
{
	// NEVER SHOW THESE
	foreach($get->nevershows as $nevershow) 
	{
		if(strripos($file, $nevershow) !== false) $excluded = true; 
	}
	if($excluded) break;
	// DON'T SHOW THUMBNAILS FROM UPLOADED IMAGES
	if(empty($show_wp_thumbs)) 
	{
		$excluded = preg_match('/\d{2,}[Xx]\d{2,}\./', $file) ? true : false; 
		if($excluded) break;
	}
	$excluded = !$manager && fileaway_utility::startswith(fileaway_utility::basename($file), '_thumb_') ? true : false; 
	if($excluded) break;
	$excluded = !$manager && fileaway_utility::startswith(fileaway_utility::basename($file), 'fa-feed-logo') ? true : false; 
	if($excluded) break;
	// ONLY INCLUDE THESE
	if($only)
	{ 
		$onlyinclude = 0; 
		$onlyincs = preg_split ('/(, |,)/', $only, -1, PREG_SPLIT_NO_EMPTY); 
		if(is_array($onlyincs))
		{
			foreach($onlyincs as $onlyinc)
			{ 
				if(strripos($file, str_replace('\\','/',$onlyinc)) !== false)
				{ 
					$onlyinclude = 1; 
					break; 
				}
			}
		}
		$excluded = $onlyinclude ? false : true;
		if($excluded) break;
	}
	// CUSTOM DEFINED SPECIAL INCLUSIONS
	$included = 0; 
	if($include)
	{ 
		$customincs = preg_split('/(, |,)/', $include, -1, PREG_SPLIT_NO_EMPTY); 
		if(is_array($customincs))
		{
			foreach($customincs as $custominc)
			{ 
				if(strripos($file, str_replace('\\','/',$custominc)) !== false)
				{ 
					$included = 1; 
					break; 
				}
			}
		}		
	}
	// EXCLUDE CODE TYPE DOCUMENTS
	$excluded = $code != 'yes' && !$included && in_array(strtolower($extension), $get->codexts) ? true : false; 
	if($excluded) break;
	// IMAGES ONLY OR NONE
	if($images && !$included)
	{ 
		$is_image = 0; 
		if(in_array(strtolower($extension), $get->imagetypes)) $is_image = 1; 
	}
	$imgonly = $images == 'only' ? $is_image : ($images == 'none' ? !$is_image : 1 );
	$excluded = $imgonly ? false : true;
	if($excluded) break;
	// AUDIO FILES ONLY	
	if($onlyaudio && !$included)
	{
		$is_audio = 0;
		if(in_array(strtolower($extension), $get->filegroups['audio'][2])) $is_audio = 1;
		$excluded = $is_audio ? false : true;
		if($excluded) break; 
	}
	// EXCLUDE THESE
	if(($exclude || $this->op['exclusions']) && !$included)
	{ 
		$customexes = $exclude ? preg_split('/(, |,)/', $exclude, -1, PREG_SPLIT_NO_EMPTY) : array(); 
		$allexcludes = array_unique(array_merge($customexes, $get->file_exclusions));
		if(is_array($allexcludes))
		{
			foreach($allexcludes as $exc)
			{ 
				if(strripos($file, str_replace('\\','/',$exc)) !== false)
				{ 
					$excluded = true; 
					break;
				}
			}
		}
		if($excluded) break;
	}
	// FINISHING TOUCHES
	$excluded = $file != "." && $file != ".." ? false : true; 
	break;
}