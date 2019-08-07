<?php
/*
File: inc/download.php
Description: Download functions
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/

add_action('init', 'mv_get_download');


function mv_get_download() {
	if (isset($_GET['upf']) && isset($_GET['id'])) {
		
			$mv_access = get_post_meta($_GET['id'], 'mv_access', true);
			$mv_user = get_post_meta($_GET['id'], 'mv_user', true);
			global $current_user;
			get_currentuserinfo();
			$mv_logged = $current_user->user_login;
			
			if ($mv_access == 'public' || ($mv_access == 'registered' && is_user_logged_in()) || ($mv_access == 'personal' && $mv_user == $mv_logged) ) { 
			
			// Start Download

			$mv_file = get_post_meta($_GET['id'], 'mv_file', true);
			
			
			if (!empty($mv_file['file'])){
			$mv_file_path = WP_CONTENT_DIR.'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.$mv_file['file'];
			$mv_file_name = substr($mv_file['file'], stripos($mv_file['file'], '/')+1);
			}
			
			set_time_limit(0);

			$action = $_GET['upf']=='vw'?'view':'download';
			if(!empty($mv_file_name)) {
			
			// Check download limit			
			$mv_meta_array = get_post_meta($_GET['id']);
			$mv_download_limit = get_post_meta($_GET['id'], 'mv_download_limit', true);
						
			if ($mv_download_limit > 0 || $mv_download_limit == -1 || !array_key_exists("mv_download_limit",$mv_meta_array) ) {
				
				// Update count
				if ($mv_download_limit > 0) { update_post_meta($_GET['id'], 'mv_download_limit', $mv_download_limit-1); }
				
				output_file($mv_file_path, $mv_file_name, $mv_file['type'], $action);
				
			}else{
				wp_die(__('We are sorry but the File reached limit of downloads', 'mvafsp')); 
			}
			
			}else{
				wp_die(__('File removed from server', 'mvafsp')); 
			}
			
			// End Download
		}
		else {
			wp_redirect(get_permalink( $_GET['id'] ));
			exit;
		}
	}
}


function output_file($file, $name, $mime_type='', $action = 'download') {
	if(!is_readable($file)) {
		//die('File not found or inaccessible!<br />'.$file.'<br /> '.$name);
		return;
	}
	$size = filesize($file);
	$name = rawurldecode($name);

	$known_mime_types=array(
		"pdf" => "application/pdf",
		"txt" => "text/plain",
		"zip" => "application/zip",
		"rar" => "application/rar",
		"doc" => "application/msword",
		"xls" => "application/vnd.ms-excel",
		"ppt" => "application/vnd.ms-powerpoint",
		"gif" => "image/gif",
		"png" => "image/png",
		"jpeg"=> "image/jpg",
		"jpg" =>  "image/jpg"
	);

	if($mime_type==''){
		$file_extension = strtolower(substr(strrchr($file,"."),1));
		if(array_key_exists($file_extension, $known_mime_types)){
			$mime_type=$known_mime_types[$file_extension];
		} else {
			$mime_type="application/force-download";
		};
	};

	@ob_end_clean(); //turn off output buffering to decrease cpu usage

	// required for IE, otherwise Content-Disposition may be ignored
	if(ini_get('zlib.output_compression'))
		ini_set('zlib.output_compression', 'Off');

	header('Content-Type: ' . $mime_type);
	if ($action == 'download') header('Content-Disposition: attachment; filename="'.$name.'"');
	else header('Content-Disposition: inline; filename="'.$name.'"');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes');

	/* The three lines below basically make the	download non-cacheable */
	header("Cache-control: private");
	header('Pragma: private');
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// multipart-download and download resuming support
	if(isset($_SERVER['HTTP_RANGE']))
	{
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
		list($range) = explode(",",$range,2);
		list($range, $range_end) = explode("-", $range);
		$range=intval($range);

		if(!$range_end) {
			$range_end=$size-1;
		} else {
			$range_end=intval($range_end);
		}

		$new_length = $range_end-$range+1;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range-$range_end/$size");
	} else {
		$new_length=$size;
		header("Content-Length: ".$size);
	}

	/* output the file itself */
	$chunksize = 1*(1024*1024); //you may want to change this
	$bytes_send = 0;
	if ($file = fopen($file, 'r'))
	{
		if(isset($_SERVER['HTTP_RANGE']))
			fseek($file, $range);

		while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length)) {
			$buffer = fread($file, $chunksize);
			print($buffer); //echo($buffer); // is also possible
			flush();
			$bytes_send += strlen($buffer);
		}
		fclose($file);
	} 
	else die('Error - can not open file.');

	die();
}   


?>