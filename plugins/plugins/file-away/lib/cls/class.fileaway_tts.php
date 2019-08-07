<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_tts'))
{
	class fileaway_tts
	{
		public function __construct()
		{
			add_shortcode('fileaway_tutorials', array($this, 'sc'));
		}
		public function sc($attr)
		{
			$options = get_option('fileaway_options');
			$levels = array($options['modalaccess'], 'edit_pages', 'edit_posts');
			foreach($levels as $level) if(!current_user_can($level)) return;
			$fileaway = new fileaway;
			$atts = array(
				'tutorials' => 1,
				'type' => 'table',
				'filenamelabel' => 'Topic',
				'fadein' => 'opacity',
				'customdata' => 'Description,Length',
				'size' => 'no',
				'mod' => 'no',
				'flightbox' => 'multi',
				'boxtheme' => 'yang',
				'only' => 'fileaway-url-parser.csv',
				'theme' => 'silver-bullet',
				'heading' => 'File Away Tutorials',
				'hcolor' => 'black',
				'align' => 'none',
				'textaign' => 'left',
				'color' => 'blue',
				'iconcolor' => 'black'
			);
			if(isset($attr['showto'])) $atts['showto'] = $attr['showto'];
			if(isset($attr['hidefrom'])) $atts['hidefrom'] = $attr['hidefrom'];
			return $fileaway->sc($atts);	
		}
	}
}