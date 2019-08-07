<?php
/*
** Plugin Name: File Away
** Plugin URI: http://wordpress.org/plugins/file-away/
** Text Domain: file-away
** Domain Path: /lib/lng
** Description: Upload, manage, and display files from your server directories or page attachments in stylized lists or sortable data tables.
** Version: 3.9.9.0.1
** Author: Thom Stark
** Author URI: http://imdb.me/thomstark
** License: To Fly. File Away comes as is with no guarantees about anything.
*/
define('fileaway', __FILE__);
define('fileaway_dir', str_replace('\\','/',dirname(fileaway)));
define('fileaway_url', str_replace('\\','/',plugins_url('', fileaway)));
define('fileaway_version', '3.9.9.0.1');
if(!class_exists('fileaway_autofiler'))
{
	class fileaway_autofiler
	{
		public function __construct()
		{
			spl_autoload_register(array($this, 'load'));
		}
        private function load($class)
		{
			$file = fileaway_dir.'/lib/cls/class.'.$class.'.php';
			if(!file_exists($file)) return false;
			include_once($file);
        }
	}
}
new fileaway_autofiler;
new fileaway_languages;
if(is_admin())
{ 
	new fileaway_admin;
	new fileaway_notices;
}
new fileaway_attributes;
new fileaway_definitions;
new feedaway;
new fileaway_stats;
new fileaway_metadata;
if(!is_admin())
{	
	new fileaway_prints;
	new fileaway;
	new attachaway;
	new fileup;
	new fileaway_values;
	new formaway;
	new fileaframe;
	new stataway;
	new fileaway_tts;
	new fileaway_downloader;	
}
new fileaway_management;
new fileaway_cleanup;
include_once fileaway_dir.'/lib/inc/inc.deprecated.php';