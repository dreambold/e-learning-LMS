<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$skipthis = 0; 
$mfile = null; 
$mfiles = array(); 
$has_sample = 0; 
$has_multiple = 0;
$samples = array('mp3','ogg','wav');
foreach($samples as $sample) if(!in_array($sample, $sources) && in_array($oext, $samples) && !in_array($oext, $sources)) $skipthis = 1;
if(!$skipthis && in_array($oext, $sources))
{
	$pbdir = $install ? rtrim(fileaway_utility::replacefirst($dir, $install, ''),'/').'/' : rtrim($dir,'/').'/'; 
	if($playbackpath) $pbpath = $install ? rtrim(fileaway_utility::replacefirst($playbackpath, $install, ''),'/').'/' : rtrim($playbackpath,'/').'/'; 
	else $pbpath = $install ? rtrim(fileaway_utility::replacefirst($dir, $install, ''),'/').'/' : rtrim($dir,'/').'/'; 
	$samplefile = $playback_url.$pbpath.$rawname; 
	$mfilepath = $chosenpath.$pbpath.$rawname;
	foreach($samples as $x=> $sample)
	{ 
		if(is_file($mfilepath.'.'.$sample))
		{ 
			$mfiles[$sample] = $samplefile.'.'.$sample; 
			$has_sample = 1;
		}
	}
	$player = null; 
	if(is_array($mfiles) && count($mfiles) > 0)
	{
		if($playback == 'compact')
		{
			$audiocorrect = "style='display:block; margin-bottom:5px;'"; 
			$loopaudio = $loopaudio == 'true' ? 'true' : 'false';
			$mfile = implode('|', $mfiles);
			$playeratts = array('fileurl'=>fileaway_utility::urlesc($mfile), 'class'=>'ssfa-player '.$icocol, 'loops'=>$loopaudio);
			$player = $playaway->player($playeratts);
		}
		else
		{
			$audiocorrect = "style='margin-right:10px;'";
			$playeratts = array();
			if($loopaudio == 'true') $playeratts['loop'] = 'true';
			foreach($mfiles as $e=>$s) $playeratts[$e] = fileaway_utility::urlesc($s);
			$player = '<div class="ssfa-player-extended">'.wp_audio_shortcode($playeratts).'</div>';
		}
	}
	$sourcefilepath = $chosenpath.$pbdir.$rawname;
	$sourcefileurl = $playback_url.$pbdir.$rawname; 
	$players = null; 
	$sourcecount = 1;
	foreach($sources as $audioext)
	{
		if(is_file($sourcefilepath.'.'.$audioext))
		{ 
			$dlcolor = !$color ? " ssfa-".$randcolor[array_rand($randcolor)] : " ssfa-$colors";
			$players .= $redirect ? 
				'<a class="ssfa-audio-download'.$dlcolor.'" '.
					'href="'.$this->op['redirect'].'" target="_blank">'
				: ($encryption 
					? '<a class="ssfa-audio-download '.$dlcolor.'" '.
						'href="'.$encrypt->url($sourcefilepath.'.'.$audioext).'" data-stat="'.$statstatus.'">' 
					: ($s2mem 
						? '<a class="ssfa-audio-download '.$dlcolor.'" '.
							'href="'.$url.'/?s2member_file_download='.$s2dir.fileaway_utility::urlesc($rawname).'.'.$audioext.$s2skip.'" data-stat="'.$statstatus.'">' 
						: '<a class="ssfa-audio-download '.$dlcolor.'" '.
							'href="'.fileaway_utility::urlesc($sourcefileurl).'.'.$audioext.'" download="'.$rawname.'.'.$audioext.'" data-stat="'.$statstatus.'">'
					)
				);
			$players .= '<div class="ssfa-audio-download" style="margin-bottom:10px;">';
			$players .= '<span class="ssfa-fileaplay-in ssfa-audio-download"></span>';
			$players .= strtoupper($audioext);
			$players .= '</div>';
			$players .= '</a>'; 
			if($sourcecount > 1) $has_multiple = 1;
			$sourcecount++;
		}
	}
 	$used[] = $rawname; 
}