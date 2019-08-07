<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('fileaway_encrypted'))
{
	class fileaway_encrypted
	{
		public function url($file = false)
		{
			if(!$file) return false;	
			$file = fileaway_utility::urlesc($file);
			$nonce = wp_create_nonce('fileaway-download');
			$ops = get_option('fileaway_options');
			return $ops['baseurl'].'?fileaway_download=1&fileaway='.$this->encrypt($file).'&nonce='.$nonce;	
		}
		public function encrypt($file)
		{ 
			if(empty($file)) return '';
			$op = get_option('fileaway_options');
			if(!isset($op['encryption_key']) || strlen($op['encryption_key']) < 16) $key = $this->key($op);
			else $key = $op['encryption_key'];	
			$encrypted = '';
			$keys = array_values(array_unique(str_split($key)));
			$keyr = array_reverse($keys);
			foreach(str_split(strrev(base64_encode($file))) as $s) $encrypted .= in_array($s, $keys) ? $keyr[array_search($s, $keys)] : $s;
			return fileaway_utility::urlesc($encrypted);
		}
		private function key($options)
		{
			if(function_exists('openssl_random_pseudo_bytes'))
				$options['encryption_key'] = bin2hex(openssl_random_pseudo_bytes(16));
			else
			{
				$key = '';
				$keys = array_merge(range(0, 9), range('a', 'z'));
				for($i = 0; $i < 32; $i++) $key .= $keys[array_rand($keys)];
				$options['encryption_key'] = $key;
			}
			update_option('fileaway_options', $options);
			return $options['encryption_key'];
		}
	}
}