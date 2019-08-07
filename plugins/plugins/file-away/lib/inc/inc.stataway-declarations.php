<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
global $wpdb;
$now = date('Y-m-d H:i:s');
$url = rtrim($this->op['baseurl'], '/');
$s2mem = fileaway_definitions::$s2member ? true : false;
$nolinksbox = false;
$nolinks = false;
$manager = false;
$playback = false;
$dynamiclink = false;
$bannerad = false;
$directories = false;
$logged_in = is_user_logged_in();
$uid = rand(0, 9999); 
$name = "ssfa-meta-container-$uid";
$datestring = $this->op['daymonth'] == 'md' ? 'm/d/Y' : 'd/m/Y'; 