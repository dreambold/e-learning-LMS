<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$output .= 
	'<div style="width:80%!important;">'.
		'<dl class="fileaway-accordion">'.
			'<dt><label class="fileaway-accordion-label">File Away</label></dt>'.
			'<dd style="display:none;">'.
				'File Away is a WordPress plugin written by Thom Stark. Thom Stark is a <a href="http://imdb.me/thomstark" target="_blank">filmmaker</a>. A filmmaker is a narcissist with a camera. A narcissist with a camera is a Thom Stark. A Thom Stark is a WordPress plugin author. A WordPress plugin is File Away.<br /><br />Thom started learning PHP while he was waiting for a paycheck to arrive. This plugin is the result. <br /><br />You can get support for File Away <a href="http://wordpress.org/support/plugin/file-away" target="_blank">here</a>, and you can leave a glowing, sycophantic review <a href="http://wordpress.org/support/view/plugin-reviews/file-away" target="_blank">here</a>.'. 
			'</dd>'.
			'<dt><label class="fileaway-accordion-label">Other Plugins</label></dt>'.
			'<dd style="display:none;">'.
				'<img src="'.fileaway_url.'/lib/img/other_plugins.png"><br />'.
				'<a href="http://wordpress.org/plugins/formidable-kinetic/" target="_blank">'.
					'<img src="'.fileaway_url.'/lib/img/fk_banner.png" style="display:block; position:relative; left:10px;">'.
				'</a>'.
				'<a href="http://wordpress.org/plugins/formidable-customizations/" target="_blank">'.
					'<img src="'.fileaway_url.'/lib/img/fc_banner.png" style="display:block; position:relative; left:10px;">'.
				'</a>'.
				'<a href="http://wordpress.org/plugins/formidable-email-shortcodes/" target="_blank">'.
					'<img src="'.fileaway_url.'/lib/img/fes_banner.png" style="display:block; position:relative; left:10px;">'.
				'</a>'.
				'<a href="http://wordpress.org/plugins/eyes-only-user-access-shortcode/" target="_blank">'.
					'<img src="'.fileaway_url.'/lib/img/eyesonly_banner.png" style="display:inline-block; position:relative; left:10px;">'.
				'</a>'.
				'<a href="http://wordpress.org/plugins/browser-body-classes-with-shortcodes/" target="_blank">'.
					'<img src="'.fileaway_url.'/lib/img/bbc_banner.png" style="display:inline-block; position:relative; top:-15px; left:20px;">'.
				'</a>'.
			'</dd>'.
			'<dt><label class="fileaway-accordion-label">Donate</label></dt>'.
			'<dd style="display:none;">'.
				'Thom doesn\'t want your money. He just wants your unconditional approval. This stems from a longstanding state of confusion regarding his relationship with his father. But if you must donate money, you can do so <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2JHFN4UF23ARG" target="_blank">here</a>. All proceeds will go toward feeding Thom\'s daughter, and making movies. Not always necessarily in that order.'.
			'</dd>'.
		'</dl>'.
		'<br><code>File Away Version: '.fileaway_version.'</code><br><br>
		<a href="https://twitter.com/fileawayplugin" class="twitter-follow-button" target="_blank"><img src="'.fileaway_url.'/lib/img/twitterfile.png"></a>'.
		'<br><br><br>'.
	'</div>';