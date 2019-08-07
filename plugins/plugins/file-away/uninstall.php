<?php
if(!defined('WP_UNINSTALL_PLUGIN')) exit();
$options = get_option('fileaway_options');
if($options['preserve_options'] != 'preserve')
	delete_option('fileaway_options');
unset($options);