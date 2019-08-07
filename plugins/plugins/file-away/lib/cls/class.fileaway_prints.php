<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
global $fileaway_add_scripts, $fileaway_add_styles, $fileaway_playback_script;
if(!class_exists('fileaway_prints'))
{
	class fileaway_prints
	{
		private $options;
		private $path;
		private $url;		
		private $file1;
		private $file2;
		public function __construct()
		{
			$this->options = get_option('fileaway_options');
			$this->path = WP_CONTENT_DIR.'/uploads/fileaway-custom-css';
			$this->url = WP_CONTENT_URL.'/uploads/fileaway-custom-css';
			$this->file1 = is_file($this->path.'/fileaway-custom-styles.css') ? 
				$this->url.'/fileaway-custom-styles.css' : false;
			$this->file2 = $this->options['custom_stylesheet'] && is_file($this->path.'/'.$this->options['custom_stylesheet']) ? 
				$this->url.'/'.$this->options['custom_stylesheet'] : false;
			add_action('init', array($this, 'register'));
			$style = $this->options['stylesheet'] == 'footer' ? 'wp_footer' : 'wp_enqueue_scripts';
			$scrpt = $this->options['javascript'] == 'header' ? 'wp_enqueue_scripts' : 'wp_footer';
			$types = $scrpt == 'wp_footer' ? $scrpt : 'wp_head';
			add_action($style, array($this, 'styles')); 
			add_action($scrpt, array($this, 'scripts'));
			add_action($types, array($this, 'filetypes'));
			add_action('wp_enqueue_scripts', array($this, 'manager'));
			add_action('wp_enqueue_scripts', array($this, 'stats'));
		}
		public function register()
		{
			wp_register_script('fileaway-soundmanager2', fileaway_url.'/lib/js/soundmanager2.js', array('jquery'), '2.97a.20130101');			
			wp_register_script('fileaway-alphanum', fileaway_url.'/lib/js/alphanum.js', array('jquery'), '1.0');
			wp_register_script('fileaway-chozed', fileaway_url.'/lib/js/chosen/chosen.js', array('jquery'), '1.1.0');
			wp_register_script('fileaway-footable', fileaway_url.'/lib/js/footable.js', array('jquery'), '2.0.1.2');
			wp_register_script('fileaway-filertify', fileaway_url.'/lib/js/filertify.js', array('jquery'), '0.3.11');
			wp_register_script('fileaway-contextmenu', fileaway_url.'/lib/js/context/contextmenu.js', array('jquery'), fileaway_version);
			wp_register_script('fileaway-management', fileaway_url.'/lib/js/management.js', array('jquery'), fileaway_version);
			wp_register_script('fileaway-stats', fileaway_url.'/lib/js/stats.js', array('jquery', 'fileaway-management'), fileaway_version);
			wp_register_style('fileaway-chozed', fileaway_url.'/lib/js/chosen/chosen.css', array(), '1.1.0');
			wp_register_style('fileaway-icons', fileaway_url.'/lib/css/fileaway-icons.css', array(), fileaway_version);
			wp_register_style('fileaway-styles', fileaway_url.'/lib/css/fileaway-styles.css', array(), fileaway_version); 
			if($this->file1) wp_register_style('fileaway-custom-styles', $this->file1, array('fileaway-icons', 'fileaway-styles'), fileaway_version); 
			if($this->file2) wp_register_style('fileaway-custom-stylesheet', $this->file2, array('fileaway-icons', 'fileaway-styles'), fileaway_version); 			
		}
		public function styles()
		{
			if($this->options['stylesheet'] == 'header' || $GLOBALS['fileaway_add_styles'])
			{
				wp_enqueue_style('fileaway-chozed');
				wp_enqueue_style('fileaway-icons');		
				wp_enqueue_style('fileaway-styles');
				if($this->file1) wp_enqueue_style('fileaway-custom-styles');	
				if($this->file2) wp_enqueue_style('fileaway-custom-stylesheet');
			}
		}
		public function scripts()
		{
			if($this->options['javascript'] == 'header' || $GLOBALS['fileaway_add_scripts'])
			{
				wp_enqueue_script('fileaway-alphanum'); 
				wp_enqueue_script('fileaway-chozed');
				wp_enqueue_script('fileaway-contextmenu'); 
				wp_enqueue_script('fileaway-footable'); 
				wp_enqueue_script('fileaway-filertify');
			}
		}
		public function manager()
		{
			$get = new fileaway_definitions;
			$vars = array(
				'ajaxurl' => admin_url('admin-ajax.php'), 
				'nonce' => wp_create_nonce('fileaway-nonce'),
				'device' => $get->is_mobile ? 'mobile' : 'desktop',
				'no_results' => __('Nothing found.', 'file-away'),
				'cancel_link' => __('Cancel', 'file-away'),
				'save_link' => __('Save', 'file-away'),
				'proceed_link' => _x('Proceed', 'verb', 'file-away'),
				'delete_check' => __('Delete?', 'file-away'),
				'ok_label' => __('OK', 'file-away'),
				'confirm_label' => _x('I\'m Sure', 'i.e., Confirm', 'file-away'),
				'cancel_label' => _x('Nevermind', 'i.e., Cancel', 'file-away'),
				'file_singular' => _x('file', 'singular', 'file-away'),
				'file_plural' => _x('files', 'plural', 'file-away'),
				'delete_confirm' => sprintf(__('You are about to permanently delete %s. Are you sure you\'re OK with that?', 'file-away'), 'numfiles'),
				'tamper1' => __('Sorry, there was a problem verifying the correct path to the files.', 'file-away'),
				'tamper2' => __('There was an error completing your request. The path to the directory has not been properly defined.', 'file-away'),
				'tamper3' => __('Sorry, but the name you specified cannot be processed.', 'file-away'),
				'tamper4' => __('An error has been triggered.', 'file-away'),
				'no_files_selected' =>  __('No files have been selected. Click on the table rows of the files you wish to select.', 'file-away'),
				'no_files_chosen' => __('No files have been chosen.', 'file-away'),
				'no_action' => __('No action has been selected.', 'file-away'),
				'no_destination' => __('No destination directory has been selected.', 'file-away'),
				'no_subdir_name' => __('You did not specify a name for your sub-directory.', 'file-away'),
				'unreadable_file' => __('Sorry, a file you have specified could not be read.', 'file-away'),
				'build_path' => __('Please build the path to your destination directory.', 'file-away'),
				'no_upload_support' => __('Your browser does not support the File Upload API. Please update.', 'file-away'),
				'exceeds_size' => sprintf(__('This file exceeds the %s max file size.', 'file-away'), 'prettymax'),
				'type_not_permitted' => __('This file type is not permitted.', 'file-away'),
				'view_all_permitted' => __('View all permitted file types.', 'file-away'),
				'view_all_prohibited' => __('View all prohibited file types.', 'file-away'),
				'double_dots_override' => __('You may not use double dots or attempt to override the upload directory.', 'file-away'),
				'double_dots' => __('You may not use double dots in the filename.', 'file-away'),
				'creation_disabled' => __('Sub-directory creation is disabled.', 'file-away'),
				'no_override' => __('You may not attempt to override the upload directory.', 'file-away'),
				'multi_type' => __('You may not specify a script filetype prior to a non-script filetype.', 'file-away'),
				'upload_failure' => sprintf(__('Sorry about that, but %s could not be uploaded.', 'file-away'), 'filename'),
				'rename_column' => __('Rename Column', 'file-away'),
				'delete_column' => __('Delete Column', 'file-away'),
				'insert_col_before' => __('Insert New Column Before', 'file-away'),
				'insert_col_after' => __('Insert New Column After', 'file-away'),
				'insert_row' => __('Insert New Row', 'file-away'),
				'delete_row' => __('Delete Row', 'file-away'),
				'save_backup' => __('Save Backup', 'file-away'),
				'new_column_name' => __('New Column Name', 'file-away'),
				'atleast_one_column' => __('There must be at least one column at all times.', 'file-away'),
				'atleast_one_row' => __('There must be at least one row at all times.', 'file-away'),
				'next_label' => __('Next', 'file-away'),
				'create_label' => __('Create', 'file-away'),
				'new_file_name' => __('New File Name', 'file-away'),
				'specify_file_name' => __('You must specify a file name.', 'file-away'),
				'specify_column_name' => __('You must specify at least one column name.', 'file-away'),
				'column_names' => __('Column Names, Comma-separated', 'file-away'),
			);
		    wp_enqueue_script('fileaway-soundmanager2');
			wp_enqueue_script('fileaway-management');	
    		wp_localize_script('fileaway-management', 'fileaway_mgmt', $vars);
		}
		public function stats()
		{
			$vars = array(
				'ajaxurl' => admin_url('admin-ajax.php'), 
				'nonce' => wp_create_nonce('fileaway-stats-nonce'),
			);
			wp_enqueue_script('fileaway-stats');
			wp_localize_script('fileaway-stats', 'fileaway_stats', $vars);
		}
		public function filetypes()
		{
			if($this->options['javascript'] == 'header' || $GLOBALS['fileaway_add_scripts'])
			{
				$defs = new fileaway_definitions;
				$groups = array(); $types = array(); 
				foreach($defs->filegroups as $group => $array)
				{
					if($group != 'unknown') $groups[] = "'$group' : ['".implode("', '", $array[2])."']";
					$types[] = "'$group' : '".$array[1]."'";
				}
				$output = "<script> var fileaway_filetype_groups = {";
				$output .= implode(', ', $groups);
				$output .= "}; ";
				$output .= "var ssfa_filetype_icons = {";
				$output .= implode(', ', $types);
				$output .= '} </script>';
				echo $output;
			}
		}
	}
}