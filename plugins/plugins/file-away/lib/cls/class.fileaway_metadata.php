<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_metadata'))
{
	class fileaway_metadata
	{
		public $ops;
		public $pathoptions;
		public static $db;
		private $version;
		public function __construct()
		{
			self::$db = $GLOBALS['wpdb']->prefix.'fileaway_metadata';
			$this->version = '1.0';
			if(is_admin()) add_action('admin_init', array($this, 'addtable'));	
		}		
		public function addtable()
		{
			$oldversion = get_option('fileaway_db2_version');
			if($this->version != $oldversion)
			{
				global $wpdb;
				$table = self::$db;
				require_once(ABSPATH.'wp-admin/includes/upgrade.php');
   				$charset_collate = '';
				if($wpdb->has_cap('collation'))
				{
					if(!empty($wpdb->charset)) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
					if(!empty($wpdb->collate)) $charset_collate .= " COLLATE $wpdb->collate";
				}
				$sql = "CREATE TABLE {$table}(
					id int(11) NOT NULL auto_increment,
					file varchar(1000) default NULL,			 
					metadata longtext default NULL,
					PRIMARY KEY  (id)
				){$charset_collate};";
				dbDelta($sql);
	   	 		update_option('fileaway_db2_version', $this->version);
			}	
		}
	}
}