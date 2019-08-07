<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_languages'))
{
	class fileaway_languages
	{
		public function __construct()
		{
			add_action('plugins_loaded', array($this, 'load'));
		}
		public function load()
		{
			load_plugin_textdomain('file-away', false, basename(dirname(fileaway)).'/lib/lng/');
		}
	}
}