<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_stats'))
{
	class fileaway_stats
	{
		public $ops;
		public $pathoptions;
		public static $db;
		private $version;
		public function __construct()
		{
			self::$db = $GLOBALS['wpdb']->prefix.'fileaway_downloads';
			$this->ops = get_option('fileaway_options');
			$define = new fileaway_definitions;
			$this->pathoptions = $define->pathoptions;
			$this->version = '1.0';
			if(is_admin())
			{
				add_action('admin_init', array($this, 'addtable'));	
				add_action('wp_ajax_fileaway-stats', array($this, 'ajax'));
				add_action('wp_ajax_nopriv_fileaway-stats', array($this, 'ajax'));
			}
			if($this->ops['stats'] == 'true' && isset($this->ops['recordlimit']) && $this->ops['recordlimit'] && is_numeric($this->ops['recordlimit']))
			{
				if(!wp_next_scheduled('fileaway_scheduled_record_limit')) 
					wp_schedule_event(time(), 'sixhours', 'fileaway_scheduled_record_limit');
				add_action('fileaway_scheduled_record_limit', array($this, 'limitpurge'));	
			}
			elseif(wp_next_scheduled('fileaway_scheduled_record_limit')) 
				wp_clear_scheduled_hook('fileaway_scheduled_record_limit');
			if($this->ops['stats'] == 'true' && isset($this->ops['recordlifespan']) && $this->ops['recordlifespan'] && $this->ops['recordlifespan'] != 'forever')
			{
				if(!wp_next_scheduled('fileaway_scheduled_record_lifespan')) 
					wp_schedule_event(time(), 'daily', 'fileaway_scheduled_record_lifespan');
				add_action('fileaway_scheduled_record_lifespan', array($this, 'lifespanpurge'));	
			}
			elseif(wp_next_scheduled('fileaway_scheduled_record_lifespan')) 
				wp_clear_scheduled_hook('fileaway_scheduled_record_lifespan');
			if($this->ops['stats'] == 'true' && isset($this->ops['compiled_stats']) && $this->ops['compiled_stats'] && $this->ops['recordlimit'] != 'false')
			{
				if(!wp_next_scheduled('fileaway_scheduled_compiled_stats')) 
					wp_schedule_event(time(), $this->ops['compiled_stats'], 'fileaway_scheduled_compiled_stats');
				add_action('fileaway_scheduled_compiled_stats', array($this, 'cmail'));	
			}
			elseif(wp_next_scheduled('fileaway_scheduled_compiled_stats')) 
				wp_clear_scheduled_hook('fileaway_scheduled_compiled_stats');				
		}
		public function ajax()
		{
			if(!wp_verify_nonce($_POST['nonce'], 'fileaway-stats-nonce')) 
				die('Go directly to jail. Do not pass GO. Do not collect $200 dollars.');
			extract($this->pathoptions);
			$action = sanitize_html_class($_POST['act']);
			$type = sanitize_html_class($_POST['type']);
			if($type == 's2member')
			{
				list($trash, $file) = explode("?s2member_file_download=", $_POST['file']);
				$pre = fileaway_utility::replacefirst(WP_PLUGIN_DIR.'/s2member-files', $rootpath, '');
				$file = $pre.'/'.fileaway_utility::urlesc($file, true);
				$file = str_replace('&s2member_skip_confirmation', '', $file);
				$response = str_replace(array('%3F','%26'),array('?','&'),fileaway_utility::urlesc($_POST['file']));
			}
			elseif($type == 'encrypted')
			{
				list($trash, $file) = explode("?", $_POST['file']);
				parse_str($file,$fileParsed);
				extract($fileParsed);
				$crypt = new fileaway_encrypted;
				$file = fileaway_utility::urlesc(fileaway_utility::replacefirst($this->decrypt($fileaway), $rootpath, ''),true);
				$response = $this->ops['baseurl'].'?fileaway_download=1&fileaway='.$crypt->encrypt($rootpath.$file).'&nonce='.wp_create_nonce('fileaway-download');
			}
			else
			{ 
				$crypt = new fileaway_encrypted;
				$file = fileaway_utility::urlesc(fileaway_utility::replacefirst($_POST['file'], rtrim($this->ops['baseurl'], '/').'/', ''), true);
				$response = $this->ops['baseurl'].'?fileaway_download=1&fileaway='.$crypt->encrypt($rootpath.$file).'&nonce='.wp_create_nonce('fileaway-download');
			}
			if($action == 'insert') $response = $this->insert($file) ? $response : 'error';
			$response = json_encode($response); 
			header("Content-Type: application/json");
			echo $response;	
			exit;	
		}
		public function imail($data)
		{
			if($this->ops['instant_stats'] != 'true') return false;
			$sender = $this->ops['instant_sender'] ? $this->ops['instant_sender'] : (get_option('admin_email') ? get_option('admin_email') : false);
			if(!$sender || !$this->ops['instant_recipients']) return false;
			$original_timezone = date_default_timezone_get();			
			fileaway_utility::timezone();
			$sendername = $this->ops['instant_sender_name'] ? $this->ops['instant_sender_name'] : stripslashes(get_bloginfo('site_name'));
			$recipients = preg_split('/(, |,)/', trim($this->ops['instant_recipients']), -1, PREG_SPLIT_NO_EMPTY);
			$subject = $this->ops['instant_subject'] ? $this->ops['instant_subject'] : '%blog% - %file% downloaded at %datetime%';
			$subject = str_replace(
				array('%blog%','%file%','%datetime%'), 
				array(stripslashes(get_bloginfo('site_name')), fileaway_utility::basename($data['file']), $data['timestamp']), 
				$subject
			);
			$message = ''; foreach($data as $key => $value) $message .= strtoupper($key).": ".$value."\r\n\r\n";	
			$headers[] = 'From: '.$sendername.' <'.$sender.'>';
			date_default_timezone_set($original_timezone);
			return wp_mail($recipients, $subject, $message, $headers) ? true : false;
		}
		public function cmail()
		{
			global $wpdb;
			$offset = null;
			if($this->ops['compiled_stats'] == 'false') return false;
			if($this->ops['compiled_stats'] == 'daily') $offset = '-1 day';
			elseif($this->ops['compiled_stats'] == 'weekly') $offset = '-1 week';
			elseif($this->ops['compiled_stats'] == 'fortnightly') $offset = '-2 weeks';
			elseif($offset == null) return;
			$datestring = $this->ops['daymonth'] == 'md' ? 'm/d/Y' : 'd/m/Y'; 
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			$begin = date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00').' '.$offset));
			$end = date('Y-m-d H:i:s', strtotime(date('Y-m-d 23:59:59').' -1 day'));
			$sender = $this->ops['compiled_sender'] ? $this->ops['compiled_sender'] : (get_option('admin_email') ? get_option('admin_email') : false);
			if(!$sender || !$this->ops['compiled_recipients']) return false;
			$sendername = $this->ops['compiled_sender_name'] ? $this->ops['compiled_sender_name'] : stripslashes(get_bloginfo('site_name'));
			$headers[] = 'From: '.$sendername.' <'.$sender.'>';
			$recipients = preg_split('/(, |,)/', trim($this->ops['compiled_recipients']), -1, PREG_SPLIT_NO_EMPTY);
			$subject = $this->ops['compiled_subject'] ? $this->ops['compiled_subject'] : '%blog% - Download Stats for %dates%';
			$dates = $this->ops['compiled_stats'] == 'daily' 
				? date($datestring, strtotime(date('Y-m-d').' -1 day')) 
				: date($datestring, strtotime(date('Y-m-d').' '.$offset)).' - '.date($datestring, strtotime(date('Y-m-d').' -1 day'));
			$subject = str_replace(
				array('%blog%','%dates%'), 
				array(stripslashes(get_bloginfo('site_name')), $dates),
				$subject
			);
			$message = ''; 
			$records = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM ".self::$db." WHERE timestamp >= %s AND timestamp <= %s ORDER BY timestamp DESC", $begin, $end), ARRAY_A);
			if(!$records) $message = __('No downloads were recorded during this period. Bummer.', 'file-away');
			else
			{
				$message = $subject."\n";
				$message .= "\n+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+\n\n";
				foreach($records as $record)
				{
					$user = $record['uid'] == 0 ? __('Guest', 'file-away') : new WP_User($record['uid']);
					if($record['uid'] != 0) $user = $user->user_login;
					$message .= 'FILE: '.$record['file']."\n";
					$message .= 'TIMESTAMP: '.$record['timestamp']."\n";
					$message .= 'UID: '.$record['uid']."\n";
					$message .= 'USER: '.$user."\n";
					$message .= $record['uid'] != 0 ? 'EMAIL: '.$record['email']."\n" : null;
					$message .= 'IP: '.$record['ip']."\n";
					$message .= 'AGENT: '.$record['agent']."\n";
					$message .= "\n+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+\n\n";
				}
				$message .= __('TOTAL DOWNLOADS:', 'file-away').' '.count($records);
			}
			wp_mail($recipients, $subject, $message, $headers);
			date_default_timezone_set($original_timezone);
			exit;
		}
		public function insert($file, $notify = true)
		{
			if(!$file) return false;
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			global $wpdb;
			$current = wp_get_current_user();
			$data = array(
				'timestamp' => date('Y-m-d H:i:s'),
				'file' => $file,
				'uid' => $current->ID,
				'email' => $current->user_email,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'agent' => $_SERVER['HTTP_USER_AGENT'],
			);
			if($this->ops['instant_stats'] == 'true' && $notify && $this->imail($data)) $data['notified'] = 1; 
			elseif($this->ops['instant_stats'] == 'true' && !$notify) $data['notified'] = 1; 
			date_default_timezone_set($original_timezone);
			return $wpdb->insert(self::$db, $data) ? true : false;
		}
		public function limitpurge()
		{
			global $wpdb;
			if($this->ops['recordlimit'] && is_numeric($this->ops['recordlimit']))
			{
				$limit = trim($this->ops['recordlimit']);
				$limit = round($limit, 0);
				$count = $wpdb->get_results('SELECT COUNT(id) FROM '.self::$db, ARRAY_N);
				if($count[0][0] > $limit)
				{
					$num = $count[0][0] - $limit;
					$records = $wpdb->get_results('SELECT id FROM '.self::$db.' ORDER BY timestamp ASC', ARRAY_N);
					$trash = array_slice($records, 0, $num);
					foreach($trash as $record) $wpdb->delete(self::$db, array('id' => $record[0]));
				}
			}
			exit;
		}
		public function lifespanpurge()
		{
			global $wpdb;
			if($this->ops['recordlifespan'] == 'forever') return;
			$original_timezone = date_default_timezone_get();
			fileaway_utility::timezone();
			$cutoff = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' -'.$this->ops['recordlifespan']));
			date_default_timezone_set($original_timezone);
			$records = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".self::$db." WHERE timestamp <= %s", $cutoff), ARRAY_N);
			if(!$records) return;
			foreach($records as $record) $wpdb->delete(self::$db, array('id' => $record[0]));
			exit;
		}
		private function decrypt($file)
		{
			if(empty($file)) return '';
			$key = $this->ops['encryption_key'];
			$decrypted = '';
			$keys = array_values(array_unique(str_split($key)));
			$keyr = array_reverse($keys);
			foreach(str_split(fileaway_utility::urlesc($file,true)) as $s) $decrypted .= in_array($s, $keyr) ? $keys[array_search($s, $keyr)] : $s;				
			return base64_decode(strrev($decrypted));
		}		
		public function addtable()
		{
			$oldversion = get_option('fileaway_db_version');
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
					timestamp varchar(255) default NULL,
					file varchar(1000) default NULL,			 
					uid int(11) default NULL, 
					email varchar(255) default NULL, 
					ip varchar(255) default NULL, 
					agent varchar(255) default NULL,
					notified bit default 0, 
					PRIMARY KEY  (id),
					KEY uid (uid) 
				){$charset_collate};";
				dbDelta($sql);
	   	 		update_option('fileaway_db_version', $this->version);
			}	
		}
	}
}