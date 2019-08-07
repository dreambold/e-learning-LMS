<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway') && !class_exists('playaway'))
{
	class playaway
	{
		public function __construct()
		{
			add_action('wp_footer', array($this, 'footerscript'));
		}
		public function footerscript()
		{ 
			if(!$GLOBALS['fileaway_playback_script']) return; 
			$script = 
			"<script>".
			"jQuery(document).ready(function($){ ".
				"$('td.ssfa-sortname a').on('click', function(){ ".
					"var current_url = window.location.href; ".
					"var download_url = $(this).attr('href'); ".
					"history.replaceState({},'',download_url); ".
					"history.replaceState({},'',current_url); ".
				"}); ".
			"}); ".
			"soundManager.useFlashBlock = false; /* optional - if used, required flashblock.css */".
			"soundManager.url = '".fileaway_url."/lib/swf/soundmanager2.swf'; ".
			"function fileaplay(flg, ids, audiourl, volume, loops) ".
			"{ ".
				"var pieces = audiourl.split('|'); ".
				"if(pieces.length > 1) audiourl = pieces; ".
				"soundManager.createSound ".
				"({ ".
					"id:'fileaplay_'+ids, ".
					"volume: volume, ".
					"url: audiourl ".
				"}); ".
				"if(flg == 'stop') soundManager.pause('fileaplay_'+ids); ".
				"else if(flg == 'play') ".
				"{ ".
					"stop_all_tracks(); ".
					"soundManager.play('fileaplay_'+ids, ".
					"{ ".
						"onfinish: function() ".
						"{ ".
							"if(loops !== 'false') loopSound('fileaplay_' + ids); ".
							"else ".
							"{ ".
								"document.getElementById('fileaplay_'+ids).style.display = 'inline-block'; ".
								"document.getElementById('fileapause_'+ids).style.display = 'none'; ".
							"} ".
						"} ".
					"}); ".
				"} ".
			"} ".
			"function show_hide(flag,ids) ".
			"{ ".
				"if(flag=='play') ".
				"{ ".
					"document.getElementById('fileaplay_'+ids).style.display = 'none'; ".
					"document.getElementById('fileapause_'+ids).style.display = 'inline-block'; ".
				"} ".
				"else if (flag == 'stop') ".
				"{ ".
					"document.getElementById('fileaplay_'+ids).style.display = 'inline-block'; ".
					"document.getElementById('fileapause_'+ids).style.display = 'none'; ".
				"} ".
			"} ".
			"function loopSound(soundID) ".
			"{ ".
				"window.setTimeout(function() ".
				"{ ".
					"soundManager.play(soundID, {onfinish: function() ".
					"{ ".
						"loopSound(soundID); ".
					"}}); ".
				"}, 1); ".
			"} ".
			"function stop_all_tracks() ".
			"{ ".
				"soundManager.stopAll(); ".
				"var inputs = document.getElementsByTagName('span'); ".
				"for(var i = 0; i < inputs.length; i++) ".
				"{ ".
					"if(inputs[i].id.indexOf('fileaplay_') == 0) inputs[i].style.display = 'inline-block'; ".
					"if(inputs[i].id.indexOf('fileapause_') == 0) inputs[i].style.display = 'none'; ".
				"} ".
			"} ".
			"</script> ";
			echo $script;
		}
		public function player($atts)
		{
			extract(shortcode_atts(array(
				'fileurl' => 'No file found.',
				'volume' => '100',
				'class' => 'ssfa-player',
				'loops' => 'false',
			), $atts));	
			$ids = uniqid();
			$player_cont = '<div style="position:relative; display:inline-block"><div class="'.$class.'">';
			$player_cont .= '<span id="fileaplay_'.$ids.'" class="ssfa-fileaplay-play4 ssfaButton_play" '.
				'onClick="fileaplay(\'play\',\''.$ids.'\',\''.fileaway_utility::urlesc($fileurl).'\',\''.$volume.'\',\''.$loops.'\');show_hide(\'play\',\''.$ids.'\');"></span>';
			$player_cont .= '<span id="fileapause_'.$ids.'" class="ssfa-fileaplay-pause4 ssfaButton_stop" '.
				'onClick="fileaplay(\'stop\',\''.$ids.'\',\'\',\''.$volume.'\',\''.$loops.'\');show_hide(\'stop\',\''.$ids.'\');"></span>';	
		 	$player_cont .= '</div></div>';
			return $player_cont;
		}
	}
}