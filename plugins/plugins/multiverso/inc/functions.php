<?php
/*
File: inc/functions.php
Description: Plugin Controller
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/


// Sanitize filename to a safe filename
function mv_sanitize_file_name($string) {
	
	// Remove special accented characters - ie. sí.
	$clean_name = strtr($string, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
	$clean_name = strtr($clean_name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
	
	$clean_name = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $clean_name);
	
	$clean_name = strtolower($clean_name);
	
	return $clean_name ;
	
}

// Get Term Link
function mv_get_taxonomy_link( $termid, $termslug, $termname, $taxonomy = 'category', $post_type = 'post', $link = true, $backend = false ) {

            $chain = '';
			
			if ($backend == true) {
				
				if($link == true) {
					$chain = "<a href='edit-tags.php?action=edit&taxonomy=$taxonomy&post_type=$post_type&tag_ID={$termid}'> " . esc_html(sanitize_term_field('name', $termname, $termid, 'category', 'display')) . "</a>";
				}else{
					$chain = 'edit-tags.php?action=edit&taxonomy=$taxonomy&post_type=$post_type&tag_ID={$termid}';
				}
				
			}else{
				
				if($link == true) {
					$chain = "<a href='".get_term_link( $termslug, $taxonomy )."'> " . esc_html(sanitize_term_field('name', $termname, $termid, 'category', 'display')) . "</a>";
				}else{
					$chain = $termname;
				}
				
			}

            return $chain;
}

// Get file categories
function mv_file_categories($post_id, $taxonomy, $posttype, $link, $backend) {
       
	    $terms = get_the_terms( $post_id, $taxonomy );
        if ( !empty( $terms ) ) {
            $out = array();
            foreach ( $terms as $c )
				$out[] = mv_get_taxonomy_link($c->term_id, $c->slug, $c->name, $taxonomy, $posttype, $link, $backend);
            	echo join( ', ', $out );
        }
        else {
            echo 'No Category';
        }
		
}

// Check if url exist
function url_exists($url) {
	$a_url = parse_url($url);
	if (!isset($a_url['port'])) $a_url['port'] = 80;
	$errno = 0;
	$errstr = '';
	$timeout = 30;
	if(isset($a_url['host']) && $a_url['host']!=gethostbyname($a_url['host'])){
	$fid = fsockopen($a_url['host'], $a_url['port'], $errno, $errstr, $timeout);
	if (!$fid) return false;
	$page = isset($a_url['path']) ?$a_url['path']:'';
	$page .= isset($a_url['query'])?'?'.$a_url['query']:'';
	fputs($fid, 'HEAD '.$page.' HTTP/1.0'."\r\n".'Host: '.$a_url['host']."\r\n\r\n");
	$head = fread($fid, 4096);
	fclose($fid);
	return preg_match('#^HTTP/.*\s+[200|302]+\s#i', $head);
	} else {
	return false;
	}
}




// Check Functions
function mv_is_file_public ( $fileID = 0 ) {
	
	if ( !$fileID ) {
		$fileID = get_the_ID();
	}
	
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	
	if ( $mv_access == 'public' ) { 
		return true; 
	}else{
		return false;
	}
	
}


function mv_is_file_registered ( $fileID = 0 ) {
	
	if ( !$fileID ) {
		$fileID = get_the_ID();
	}
	
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	
	if ( $mv_access == 'registered' ) { 
		return true; 
	}else{
		return false;
	}
	
}


function mv_is_file_personal ( $fileID = 0 ) {
	
	if ( !$fileID ) {
		$fileID = get_the_ID();
	}
	
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	
	if ( $mv_access == 'personal' ) { 
		return true; 
	}else{
		return false;
	}
	
}


function mv_is_file_owned_by ( $fileID = 0, $username = NULL ) {
	
	if ( !$fileID ) {
		$fileID = get_the_ID();
	}
	
	if ( !$username ) {
		global $current_user;	
		get_currentuserinfo();
		$username = $current_user->user_login;
	}
	
	$mv_owner = get_post_meta($fileID, 'mv_user', true);
	
	if ( $mv_owner == $username ) { 
		return true; 
	}else{
		return false;
	}	
	
}


function mv_user_can_view_file ( $fileID = 0 ) {
	
	if ( !$fileID ) {
		$fileID = get_the_ID();
	}
	
	global $current_user;	
	get_currentuserinfo();
	$username = $current_user->user_login;
	
	$mv_owner = get_post_meta($fileID, 'mv_user', true);
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	
	
	if ( $mv_owner == $username || $mv_access == 'public' || ($mv_access == 'registered' && is_user_logged_in() ) ) { 
		return true; 
	}else{
		return false;
	}	
	
}


function mv_is_file_existing ($fileID) {
	
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	
	if ($mv_access) {
		return true;
	}else{
		return false;
	}
	
}


// Array Supported Types

function mv_supported_types() {
		
		$supported_types = array();
		
		$mv_mime_pdf = get_option('mv_mime_pdf');
		$mv_mime_txt = get_option('mv_mime_txt');
		$mv_mime_zip = get_option('mv_mime_zip');
		$mv_mime_rar = get_option('mv_mime_rar');
		$mv_mime_doc = get_option('mv_mime_doc');
		$mv_mime_xls = get_option('mv_mime_xls');
		$mv_mime_ppt = get_option('mv_mime_ppt');
		$mv_mime_gif = get_option('mv_mime_gif');
		$mv_mime_png = get_option('mv_mime_png');
		$mv_mime_jpeg = get_option('mv_mime_jpeg');
		$mv_mime_others = get_option('mv_mime_others');
		
				
		// Setup the array of supported file types.
		if (!empty($mv_mime_pdf)){$supported_types[] = $mv_mime_pdf; }
		if (!empty($mv_mime_txt)){$supported_types[] = $mv_mime_txt; }
		if (!empty($mv_mime_zip)){$supported_types[] = $mv_mime_zip; }
		if (!empty($mv_mime_rar)){$supported_types[] = $mv_mime_rar; }
		if (!empty($mv_mime_doc)){$supported_types[] = $mv_mime_doc; $supported_types[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; }
		if (!empty($mv_mime_xls)){$supported_types[] = $mv_mime_xls; $supported_types[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; }
		if (!empty($mv_mime_ppt)){$supported_types[] = $mv_mime_ppt; $supported_types[] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation'; }
		if (!empty($mv_mime_gif)){$supported_types[] = $mv_mime_gif; }
		if (!empty($mv_mime_png)){$supported_types[] = $mv_mime_png; }
		if (!empty($mv_mime_jpeg)){$supported_types[] = $mv_mime_jpeg; }
		
		if(!empty($mv_mime_others)) {
		$mv_mime_others_ar = explode(",", $mv_mime_others);
		$supported_types = array_merge($supported_types, $mv_mime_others_ar);
		}
		
		return $supported_types;
		
}


// META FOR FRONTEND (UPDATE)

function mv_meta_frontend_file_update($post_id) { 

	$mv_file = get_post_meta($post_id, 'mv_file', true);
	if (!empty($mv_file)) { ?>
		<p><?php _e('Current file:', 'mvafsp');?> <a href="<?php echo $mv_file['url'];?>" target="_blank"><?php echo basename($mv_file['file']);?></a>  
         <input name="mv_remove_file" id="mv_remove_file" type="checkbox" value="1"> <?php _e("Flag this to remove file (you need to update post).", "mvafsp")?></p>
		<?php
	}
	
	$mv_download_limit = get_post_meta($post_id, 'mv_download_limit', true);
	
	?>
	<label for="mv_file"><?php _e('<b>Upload a local file</b> <br><em>(if you fill this field system will ignore next two fields)</em>', 'mvafsp');?></label>
	<input type="file" name="mv_file" id="mv_file" />
    <div class="mv-clear"></div>
    
    <label for="mv_file_r"><?php _e('<b>Upload a remote file</b> <br><em>(if you fill this field system will ignore next field)</em>', 'mvafsp');?></label>
	<input type="text" class="medium" name="mv_file_r" id="mv_file_r" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em>
    <div class="mv-clear"></div>
    
    <label for="mv_file_d"><?php _e('<b>Direct link for the file</b> <br><em>(the file will not be uploaded on the server)</em>', 'mvafsp');?></label>
	<input type="text" class="medium" name="mv_file_d" id="mv_file_d" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em>
	<div class="mv-clear"></div>
    
    <label for="mv_download_limit"><?php _e('<b>Downloads Limit</b> <br><em>(set -1 for unlimited)</em>', 'mvafsp');?></label>
	<input type="text" class="small" name="mv_download_limit" id="mv_download_limit" value="<?php if($mv_download_limit >= 0){echo $mv_download_limit; }else{echo '-1';}?>" style="text-align:center;" />
    <div class="mv-clear"></div>
    

    <?php
		
		$mv_user = get_post_meta($post_id, 'mv_user', true);
		
		global $current_user;	
		get_currentuserinfo();
		$mv_logged = $current_user->user_login;
		
	?>
        
	<label for="mv_user"><?php _e('<b>File Owner</b>', 'mvafsp');?> </label>
    <span class="mv_user"><i title="Owner" class="mvico-user3"></i> <?php echo $mv_logged;?></span>
	<input type="hidden" name="mv_user" id="mv_user" value="<?php echo $current_user->ID;?>" /> 
	<div class="mv-clear"></div>	
    
    <label for="mv_access"><?php _e('<b>File Access</b>', 'mvafsp');?></label>
	<select name="mv_access" id="mv_access">
    
    <?php $mv_access = get_post_meta($post_id, 'mv_access', true); ?>
    
    <option value="public" <?php if ($mv_access == 'public') echo 'selected="selected"';?>><?php _e('Public', 'mvafsp'); ?></option>
    <option value="registered" <?php if ($mv_access == 'registered') echo 'selected="selected"';?>><?php _e('Registered', 'mvafsp'); ?></option>
    <option value="personal" <?php if ($mv_access == 'personal') echo 'selected="selected"';?>><?php _e('Personal', 'mvafsp'); ?></option>
    
    </select>
    <div class="mv-clear"></div>
	<?php _e('Note: If you select "Personal" only you will be able to access the file. ', 'mvafsp');?>
	
	
	<?php 
}


// SAVE POST BY FRONTEND (UPDATE)

function mv_update_frontend_post($post_id) {
	
	
	// Multisite fix
	if(is_multisite()) {
		switch_to_blog(SITE_ID_CURRENT_SITE);
	 }
		
    // if invalid $post object or post type is not 'multiverso', return
    if(get_post_type($post_id) != 'multiverso') return;
	

	
	/* --- START SAVING --- */
	
	// Remove Meta Field
	if (!empty($_POST['mv_remove_file'])) {
		delete_post_meta($post_id, 'mv_file');
	}
	
	//Add Meta Field
	$user_info = get_userdata($_POST['mv_user']);
	add_post_meta($post_id, 'mv_user', $user_info->user_login);
	update_post_meta($post_id, 'mv_user', $user_info->user_login);
	
	add_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	update_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	
	if($_POST['mv_download_limit'] >= 0 &&  is_numeric($_POST['mv_download_limit'])){
		add_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
		update_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
	}else{
		add_post_meta($post_id, 'mv_download_limit', '-1');
		update_post_meta($post_id, 'mv_download_limit', '-1');
	}
	
	
	// Make sure the file array isn't empty
	if(!empty($_FILES['mv_file']['name'])) {
		
		$limit_option = get_option('mv_upload_size');
		
		if( !empty($limit_option) && $_FILES['mv_file']['size'] > $limit_option ) {
			echo '<div class="mv_error">The file size ('.$_FILES['mv_file']['size'].' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes)</div>';
			return;
		}
		
		
		// Check Supported Types
		$supported_types = mv_supported_types();
	
		
		// Get the file type of the upload
		$arr_file_type = wp_check_filetype(basename($_FILES['mv_file']['name']));
		
		$uploaded_type = $arr_file_type['type'];
	
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($uploaded_type, $supported_types)) {
			
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			
			if ($mv_file) {
				$mv_file_path = WP_CONTENT_DIR.'/multiverso/'.$mv_file['file'];
				if (file_exists($mv_file_path)) unlink($mv_file_path);
			}
			
			// Use the WordPress API to upload the file
			require_once(ABSPATH . 'wp-admin/includes/admin.php');
			$upload = wp_handle_upload( $_FILES['mv_file'], array( 'test_form' => false ) );
		
			

			if(isset($upload['error']) && !empty($upload['error'])) {
				echo '<div class="mv_error">There was an error uploading your file. The error is: ' . $upload['error'].'</div>';
				return;
			} else {
			
				// Update custom field
				$upload['file'] = substr($upload['file'],stripos($upload['file'],'wp-content/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/')+28);
				
				add_post_meta($post_id, 'mv_file', $upload);
				update_post_meta($post_id, 'mv_file', $upload); 
				
			} // end if/else
			
			
		} else {
			_e('<div class="mv_error">The file type that you\'ve uploaded is not allowed.</div>', 'mvafsp');
			return;
		} // end if/else
		
	}elseif (!empty($_POST['mv_file_r'])){ // else if empty upload files

		// Setup vars
		$uploaddir = WP_CONTENT_DIR.'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.mv_get_user_dir($_POST['mv_user']);
		
		$remoteurl = $_POST['mv_file_r'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$fileunipath = mv_get_user_dir($_POST['mv_user']).'/'.$filename;
		
		$fileurl = content_url().'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.$fileunipath;
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];		
		
		$uploadfile = $uploaddir . '/' . $filename;
		
		// Check for MIME TYPE
		$supported_types = mv_supported_types();
	
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($filemime, $supported_types)) {
			
			if(url_exists($remoteurl)) {
			$contents = file_get_contents($remoteurl);
			}else{
				_e('<div class="mv_error">Remote url is wrong, there isn\'t any file to upload.</div>', 'mvafsp');
				return;
			}
			
			$limit_option = get_option('mv_upload_size');
			
			if( !empty($limit_option) && strlen($contents) > $limit_option ){
				echo '<div class="mv_error">The file size ('.strlen($contents).' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes)</div>';
				return;
			}
			
			$savefile = fopen($uploadfile, 'w');
			fwrite($savefile, $contents);
			fclose($savefile);
			
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			if ($mv_file) {				
				update_post_meta( $post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl, 'type'=>$filemime ) ); 
			}else{
				add_post_meta($post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl ) );
			}
				
		}else{
			_e('<div class="mv_error">The file type that you\'ve uploaded is not allowed.</div>', 'mvafsp');
			return;
		}
	
	}elseif (!empty($_POST['mv_file_d'])){ // else if empty remote upload too 
		
		// Get vars
		$remoteurl = $_POST['mv_file_d'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];	
	
		$mv_file = get_post_meta($post_id, 'mv_file', true);
		if ($mv_file) {				
			update_post_meta( $post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) ); 
		}else{
			add_post_meta($post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) );
		}
	
	}
	
	// WP Terms Saving	
	wp_set_post_terms( $post_id, $_POST['mvCategory'], 'multiverso-categories' );
	
	// WP Post Saving
	$file_information = array(
    'ID' => $post_id,
    'post_title' =>  wp_strip_all_tags( $_POST['fileTitle'] ),
    'post_content' => $_POST['fileContent'],
    'post_type' => 'multiverso',
    'post_status' => $_POST['mvStatus']
	);
	
	$file_update = wp_update_post( $file_information ); 
	
	// Multisite fix
	if(is_multisite()) {
		restore_current_blog();	
	}
	
}



// META FOR FRONTEND (ADD)

function mv_meta_frontend_file_add() { 

	?>
	<label for="mv_file"><?php _e('<b>Upload a local file</b> <br><em>(if you fill this field system will ignore next two fields)</em>', 'mvafsp');?></label>
	<input type="file" name="mv_file" id="mv_file" />
    <div class="mv-clear"></div>
    <label for="mv_file_r"><?php _e('<b>Upload a remote file</b> <br><em>(if you fill this field system will ignore next field)</em>', 'mvafsp');?></label>
	<input type="text" class="medium" name="mv_file_r" id="mv_file_r" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em>
    <div class="mv-clear"></div>
    <label for="mv_file_d"><?php _e('<b>Direct link for the file</b> <br><em>(the file will not be uploaded on the server)</em>', 'mvafsp');?></label>
	<input type="text" class="medium" name="mv_file_d" id="mv_file_d" value="" /> <em style="color:#999;"><?php _e('ex. http://www.domain.ext/filename.ext', 'mvafsp');?></em>
	<div class="mv-clear"></div>
    <label for="mv_download_limit"><?php _e('<b>Downloads Limit</b> <br><em>(set -1 for unlimited)</em>', 'mvafsp');?></label>
	<input type="text" class="small" name="mv_download_limit" id="mv_download_limit" value="-1" style="text-align:center;" />
    <div class="mv-clear"></div>
   
    
    <?php
		
		global $current_user;	
		get_currentuserinfo();
		$mv_logged = $current_user->user_login;
		
	?>
        
	<label for="mv_user"><?php _e('<b>File Owner</b>', 'mvafsp');?> </label>
    <span class="mv_user"><i title="Owner" class="mvico-user3"></i> <?php echo $mv_logged;?></span>
	<input type="hidden" name="mv_user" id="mv_user" value="<?php echo $current_user->ID;?>" /> 
	<div class="mv-clear"></div>	
    
    <label for="mv_access"><?php _e('<b>File Access</b>', 'mvafsp');?></label>
	<select name="mv_access" id="mv_access">
    <div class="mv-clear"></div>
    
    <option value="public"><?php _e('Public', 'mvafsp'); ?></option>
    <option value="registered"><?php _e('Registered', 'mvafsp'); ?></option>
    <option value="personal"><?php _e('Personal', 'mvafsp'); ?></option>
    
    </select>
	<?php _e('Note: If you select "Personal" only you will be able to access the file. ', 'mvafsp');?>
	
	
	<?php 
}


// SAVE POST BY FRONTEND (ADD)

function mv_save_frontend_post($page_id) {
	
	
	// Multisite fix
	if(is_multisite()) {
		switch_to_blog(SITE_ID_CURRENT_SITE);
	 } 
	 
	/* --- START SAVING --- */

	// WP Post Saving
	$file_information = array(
    'post_title' =>  wp_strip_all_tags( $_POST['fileTitle'] ),
    'post_content' => $_POST['fileContent'],
    'post_type' => 'multiverso',
    'post_status' => $_POST['mvStatus']
	);
	
	$post_id = wp_insert_post( $file_information ); 
	
	//Add Meta Field
	$user_info = get_userdata($_POST['mv_user']);
	add_post_meta($post_id, 'mv_user', $user_info->user_login);
	update_post_meta($post_id, 'mv_user', $user_info->user_login);
	
	add_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	update_post_meta($post_id, 'mv_access', $_POST['mv_access']);
	
	if($_POST['mv_download_limit'] >= 0 &&  is_numeric($_POST['mv_download_limit'])){
		add_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
		update_post_meta($post_id, 'mv_download_limit', $_POST['mv_download_limit']);
	}else{
		add_post_meta($post_id, 'mv_download_limit', '-1');
		update_post_meta($post_id, 'mv_download_limit', '-1');
	}
	
	
	// Make sure the file array isn't empty
	if(!empty($_FILES['mv_file']['name'])) {
		
		$limit_option = get_option('mv_upload_size');
		
		if( !empty($limit_option) && $_FILES['mv_file']['size'] > $limit_option ) {
			echo '<div class="mv_error">The file size ('.$_FILES['mv_file']['size'].' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes). Your data was saved but you need to edit the file from the list below to complete your insert.</div>';
			return;
		}
		
		
		// Check Supported Types
		$supported_types = mv_supported_types();
	
		
		// Get the file type of the upload
		$arr_file_type = wp_check_filetype(basename($_FILES['mv_file']['name']));
		
		$uploaded_type = $arr_file_type['type'];
	
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($uploaded_type, $supported_types)) {
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			if ($mv_file) {
				$mv_file_path = WP_CONTENT_DIR.'/multiverso/'.$mv_file['file'];
				if (file_exists($mv_file_path)) unlink($mv_file_path);
			}
			
			// Use the WordPress API to upload the file
			require_once(ABSPATH . 'wp-admin/includes/admin.php');
			$upload = wp_handle_upload( $_FILES['mv_file'], array( 'test_form' => false ) );
		
			

			if(isset($upload['error']) && !empty($upload['error'])) {
				echo '<div class="mv_error">There was an error uploading your file. The error is: ' . $upload['error'].'<br> Your data was saved but you need to edit the file from the list below to complete your insert.</div>';
				return;
			} else {
			
				// Update custom field
				$upload['file'] = substr($upload['file'],stripos($upload['file'],'wp-content/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/')+28);
				
				add_post_meta($post_id, 'mv_file', $upload);
				update_post_meta($post_id, 'mv_file', $upload); 
				
			} // end if/else
			
			
		} else {
			_e('<div class="mv_error">The file type that you\'ve uploaded is not allowed. Your data was saved but you need to edit the file from the list below to complete your insert.</div>', 'mvafsp');
			return;
		} // end if/else
		
	}elseif (!empty($_POST['mv_file_r'])){ // else if empty upload files

		// Setup vars
		$uploaddir = WP_CONTENT_DIR.'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.mv_get_user_dir($_POST['mv_user']);
		
		$remoteurl = $_POST['mv_file_r'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$fileunipath = mv_get_user_dir($_POST['mv_user']).'/'.$filename;
		
		$fileurl = content_url().'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.$fileunipath;
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];		
		
		$uploadfile = $uploaddir . '/' . $filename;
		
		// Check for MIME TYPE
		$supported_types = mv_supported_types();

		
		// Check if the type is supported. If not, throw an error.
		if(in_array($filemime, $supported_types)) {
			
			if(url_exists($remoteurl)) {
			$contents = file_get_contents($remoteurl);
			}else{
				_e('<div class="mv_error">Remote url is wrong, there isn\'t any file to upload. Your data was saved but you need to edit the file from the list below to complete your insert.</div>', 'mvafsp');
				return;
			}
			
			$limit_option = get_option('mv_upload_size');
			
			if( !empty($limit_option) && strlen($contents) > $limit_option ){
				echo '<div class="mv_error">The file size ('.strlen($contents).' bytes) exceeds the maximum allowed from Administrator ('.get_option('mv_upload_size').' bytes).  Your data was saved but you need to edit the file from the list below to complete your insert.</div>';
				return;
			}
			
			$savefile = fopen($uploadfile, 'w');
			fwrite($savefile, $contents);
			fclose($savefile);
			
			$mv_file = get_post_meta($post_id, 'mv_file', true);
			if ($mv_file) {				
				update_post_meta( $post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl, 'type'=>$filemime ) ); 
			}else{
				add_post_meta($post_id, 'mv_file', array( 'file'=>$fileunipath, 'url'=>$fileurl ) );
			}
				
		}else{
			_e('<div class="mv_error">The file type that you\'ve uploaded is not allowed. Your data was saved but you need to edit the file from the list below to complete your insert.</div>', 'mvafsp');
			return;
		}
	
	}elseif (!empty($_POST['mv_file_d'])){ // else if empty remote upload too 
		
		// Get vars
		$remoteurl = $_POST['mv_file_d'];		 
		
		$filename = mv_sanitize_file_name(basename($remoteurl));
		
		$filetype = wp_check_filetype(basename($filename));
		
		$filemime = $filetype['type'];	
	
		$mv_file = get_post_meta($post_id, 'mv_file', true);
		if ($mv_file) {				
			update_post_meta( $post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) ); 
		}else{
			add_post_meta($post_id, 'mv_file', array( 'file'=>$filename, 'url'=>$remoteurl, 'type'=>$filemime ) );
		}
	
	}
	
	// WP Terms Saving	
	wp_set_post_terms( $post_id, $_POST['mvCategory'], 'multiverso-categories' );
	
	
	// Multisite fix
	if(is_multisite()) {
		restore_current_blog();	
	}
	
}



// MV get Categories

function mv_get_categories()
{
	$wp_cat = get_categories(array('type' => 'multiverso', 'taxonomy' => 'multiverso-categories', 'hide_empty' => 0 ));

	$result = array();
	foreach ($wp_cat as $cat)
	{
		$result[] = array('value' => $cat->cat_ID, 'label' => $cat->name);
	}
	return $result;
}

function mv_get_categories_ordered()
{
	$wp_cat = get_categories(array('type' => 'multiverso', 'taxonomy' => 'multiverso-categories', 'hide_empty' => 0 ));

	$result = array();
	foreach ($wp_cat as $cat)
	{
		$result[] = array('value' => $cat->cat_ID, 'label' => $cat->name);
	}
	return $result;
}


// Sub Categories for Category Widget 

function mv_get_subcats($cat_id) {
		
		$cat_args = array('parent' => $cat_id, 'orderby' => 'name', 'hierarchical' => $h, 'taxonomy' => 'multiverso-categories');
		
		$categories = get_categories($cat_args);
		
		echo '<ul class="mv-subcat-list">';
		
		foreach($categories as $category) {
			
				$category_link = esc_url( add_query_arg( 'catid', $category->term_id, get_permalink( get_option('mv_category_page') ) ) );		
				
				echo '<li>';
				echo '<a href="'.$category_link.'">'.$category->name.'</a>';
				mv_get_subcats($category->term_id);
				echo '</li>';
			
		}
		
		echo '</ul>';
		
	}