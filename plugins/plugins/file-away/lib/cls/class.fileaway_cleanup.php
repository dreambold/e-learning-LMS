<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_cleanup'))
{
	class fileaway_cleanup
	{
		public function __construct()
		{
			if(!wp_next_scheduled('fileaway_scheduled_cleanup')) 
				wp_schedule_event(time(), 'hourly', 'fileaway_scheduled_cleanup');
			add_action('fileaway_scheduled_cleanup', array($this, 'cleanup'));	
		}
		public function cleanup()
		{
			if(is_dir(fileaway_dir.'/temp'))
			{
				$files = glob(fileaway_dir.'/temp/*'); 
				if(is_array($files))
				{ 
					foreach($files as $file)
					{ 
						if(is_file($file) && (time() - filemtime($file)) >= 60*60)
						{ 
							unlink($file);
						}
					}
				}
			}
			die();	
		}
	}
}