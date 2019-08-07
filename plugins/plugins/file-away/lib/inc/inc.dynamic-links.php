<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(isset($fulls) && is_array($fulls)) 
{
	$parsers = array_keys($fulls, 'fileaway-url-parser.csv', true);
	if(count($parsers) > 0)
	{
		ini_set('auto_detect_line_endings', TRUE);
		foreach($parsers as $parser)
		{
			$parsepath = rtrim($dirs[$parser], '/');
			$parsefile = $fulls[$parser];
			if(file_exists($parsepath.'/'.$parsefile) && is_readable($parsepath.'/'.$parsefile))
			{
				$filename = $parsepath.'/'.$parsefile;
				$header = NULL;
				$dynamiclinks = array();
				if(($handle = fopen($filename, 'r')) !== FALSE)
				{
					while(($row = fgetcsv($handle, 0, ',')) !== FALSE)
					{
						if(!$header) $header = $row;
						else
						{
							if(count($header) > count($row))
							{
								$difference = count($header)-count($row);
								for($i = 1; $i <= $difference; $i++)
								{
									$row[count($row) + 1] = ',';
								}
							}
							$dynamiclinks[] = array_combine($header, $row);
						}
					}
					fclose($handle);
				}
				foreach($dynamiclinks as $dl)
				{
					if(isset($dl['URL']) && (preg_match('/[a-z]/i', $dl['URL']) || preg_match('/\d/', $dl['URL'])))
					{
						$finalname = isset($dl['FILENAME']) && (preg_match('/[a-z]/i', $dl['FILENAME']) || preg_match('/\d/', $dl['FILENAME'])) 
							? $dl['FILENAME'] 
							: str_replace('.', '', fileaway_utility::basename($dl['URL'])
						);	
						$youtube = preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $dl['URL']) ? true : false;
						$vimeo = preg_match('#vimeo.com/#', $dl['URL']) ? true : false;
						$ext = $youtube ? 'tube' : ($vimeo ? 'vmeo' : 'link');
						$exts[] = $ext;
						$locs[] = $locs[$parser]; 
						$fulls[] = $finalname.'.'.$ext; 
						$rawnames[] = $finalname;
						$links[] = $dl['URL'];
						$dirs[] = $dirs[$parser];
						$times[] = $times[$parser];
						$bannerads[] = false;
						$dynamics[] = true;
					}	
				}
				unset($exts[$parser]);
				unset($locs[$parser]);
				unset($fulls[$parser]);
				unset($rawnames[$parser]);
				unset($links[$parser]);
				unset($dirs[$parser]);
				unset($times[$parser]);
				unset($bannerads[$parser]);
				unset($dynamics[$parser]);
			}
		}
	}
}