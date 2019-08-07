<?php
/*
File: inc/options.php
Description: Plugin Options
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/

function mv_options() {
	
	if (!empty($_POST['update'])) {

		if(empty($_POST['mv_mime_pdf'])) {$mime_pdf = '';}else{$mime_pdf = $_POST['mv_mime_pdf'];}
		if(empty($_POST['mv_mime_txt'])) {$mime_txt = '';}else{$mime_txt = $_POST['mv_mime_txt'];}
		if(empty($_POST['mv_mime_zip'])) {$mime_zip = '';}else{$mime_zip = $_POST['mv_mime_zip'];}
		if(empty($_POST['mv_mime_rar'])) {$mime_rar = '';}else{$mime_rar = $_POST['mv_mime_rar'];}
		if(empty($_POST['mv_mime_doc'])) {$mime_doc = '';}else{$mime_doc = $_POST['mv_mime_doc'];}
		if(empty($_POST['mv_mime_xls'])) {$mime_xls = '';}else{$mime_xls = $_POST['mv_mime_xls'];}
		if(empty($_POST['mv_mime_ppt'])) {$mime_ppt = '';}else{$mime_ppt = $_POST['mv_mime_ppt'];}
		if(empty($_POST['mv_mime_gif'])) {$mime_gif = '';}else{$mime_gif = $_POST['mv_mime_gif'];}
		if(empty($_POST['mv_mime_png'])) {$mime_png = '';}else{$mime_png = $_POST['mv_mime_png'];}
		if(empty($_POST['mv_mime_jpeg'])) {$mime_jpeg = '';}else{$mime_jpeg = $_POST['mv_mime_jpeg'];}
		if(empty($_POST['mv_mime_others'])) {$mime_others = '';}else{$mime_others = $_POST['mv_mime_others'];}
		
		if(empty($_POST['mv_single_theme'])) {$mv_single_theme = '';}else{$mv_single_theme = $_POST['mv_single_theme'];}
		
		if(empty($_POST['mv_manage_page'])) {$mv_manage_page = '';}else{$mv_manage_page = $_POST['mv_manage_page'];}
		if(empty($_POST['mv_category_page'])) {$mv_category_page = '';}else{$mv_category_page = $_POST['mv_category_page'];}
		if(empty($_POST['mv_search_page'])) {$mv_search_page = '';}else{$mv_search_page = $_POST['mv_search_page'];}
		
		if(empty($_POST['mv_disable_downloader'])) {$mv_disable_downloader = '';}else{$mv_disable_downloader = $_POST['mv_disable_downloader'];}
		if(empty($_POST['mv_upload_size'])) {$mv_upload_size = '';}else{$mv_upload_size = $_POST['mv_upload_size'];}
		if(empty($_POST['mv_file_comments'])) {$mv_file_comments = '';}else{$mv_file_comments = $_POST['mv_file_comments'];}
		
		if(empty($_POST['mv_pt_slug'])) {$mv_pt_slug = '';}else{$mv_pt_slug = sanitize_title($_POST['mv_pt_slug']);}
		//if(empty($_POST['mv_tax_slug'])) {$mv_tax_slug = '';}else{$mv_tax_slug = sanitize_title($_POST['mv_tax_slug']);}
		
		if(empty($_POST['mv_custom_css'])) {$custom_css = '';}else{$custom_css = stripslashes($_POST['mv_custom_css']);}
		
		if(empty($_POST['mv_before_tpl'])) {$mv_before_tpl = '';}else{$mv_before_tpl = stripslashes($_POST['mv_before_tpl']);}
		if(empty($_POST['mv_after_tpl'])) {$mv_after_tpl = '';}else{$mv_after_tpl = stripslashes($_POST['mv_after_tpl']);}
		
		update_option('mv_mime_pdf', $mime_pdf );
		update_option('mv_mime_txt', $mime_txt );
		update_option('mv_mime_zip', $mime_zip );
		update_option('mv_mime_rar', $mime_rar );
		update_option('mv_mime_doc', $mime_doc );
		update_option('mv_mime_xls', $mime_xls );
		update_option('mv_mime_ppt', $mime_ppt );
		update_option('mv_mime_gif', $mime_gif );
		update_option('mv_mime_png', $mime_png );
		update_option('mv_mime_jpeg', $mime_jpeg );
		update_option('mv_mime_others', $mime_others );
		
		update_option('mv_single_theme', $mv_single_theme );
		
		update_option('mv_manage_page', $mv_manage_page );
		update_option('mv_category_page', $mv_category_page );
		update_option('mv_search_page', $mv_search_page );
		
		update_option('mv_disable_downloader', $mv_disable_downloader );	
		update_option('mv_upload_size', $mv_upload_size );		
		update_option('mv_file_comments', $mv_file_comments );
	
		update_option('mv_pt_slug', $mv_pt_slug );
		//update_option('mv_tax_slug', $mv_tax_slug );
		
		update_option('mv_custom_css', $custom_css );
		
		update_option('mv_before_tpl', $mv_before_tpl );
		update_option('mv_after_tpl', $mv_after_tpl );
		
		?>
		<div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php _e('Settings Saved', 'mvafsp');?>.</strong></p></div>
		<?php
	}

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
	
	$mv_single_theme = get_option('mv_single_theme');
	
	$mv_manage_page = get_option('mv_manage_page');
	$mv_category_page = get_option('mv_category_page');
	$mv_search_page = get_option('mv_search_page');
	
	$mv_disable_downloader = get_option('mv_disable_downloader');
	$mv_upload_size = get_option('mv_upload_size');
	$mv_file_comments = get_option('mv_file_comments');
	
	$mv_pt_slug = get_option('mv_pt_slug');
	//$mv_tax_slug = get_option('mv_tax_slug');
	
	
	$mv_custom_css = get_option('mv_custom_css');
	$mv_before_tpl = get_option('mv_before_tpl');
	$mv_after_tpl = get_option('mv_after_tpl');
	
	
	
	// Default CSS Classes
	$default_css_classess = '/* BASIC STYLES */
.mv-wrapper {
	width:100%; /* Set your content width */
	margin:auto; /* Set MV content to the center */
}
.mv-content {
}
.mv-clear {
}
.mvhr {
}

/* FILE VIEW */
.file-thumb img {
}
.file-title {
}
.file-info {
}
.file-date {
}
.file-info a:active, .file-info a:visited, .file-info a:link {
}
.file-details-title {
}
.file-details-wrapper {
}
.file-details {
}
.file-data {
}

/* Start Extension Icons */
.file-pdf {
}
.file-txt {
}
.file-zip {
}
.file-rar {
}
.file-doc {
}
.file-xls {
}
.file-ppt {
}
.file-gif {
}
.file-png {
}
.file-jpg {
}
.file-none {
}
.file-others {
}

/* End Extension Icons */
.file-name {
}
.file-name a:visited, .file-name a:link, .file-name a:active {
}
.file-data-list {
}
.file-data-list li {
}
.file-data-list i {
}
.file-owner {
}
.file-publish {
}
.file-access {
}
.file-public {
}
.file-registered {
}
.file-personal {
}
.file-dw-button {
}
.file-dw-button a {
}
.mv-btn {
}
.mv-btn:hover, .mv-btn:focus, .mv-btn:active, .mv-btn.active, .mv-btn.disabled, .mv-btn[disabled] {
}
.mv-btn:active, .mv-btn.active {
}
.mv-btn:hover, .mv-btn:focus, .mv-btn:active, .mv-btn.active, .mv-btn.disabled, .mv-btn[disabled] {
}
.mv-btn:active, .mv-btn.active {
}
.mv-btn:first-child {
}
.mv-btn:first-child {
}
.mv-btn:hover, .mv-btn:focus {
}
.mv-btn:focus {
}
.mv-btn.active, .mv-btn:active {
}
.mv-btn.disabled, .mv-btn[disabled] {
}
.mv-btn-success {
}
.mv-btn-success:hover, .mv-btn-success:focus, .mv-btn-success:active, .mv-btn-success.active, .mv-btn-success.disabled, .mv-btn-success[disabled] {
}
.mv-btn-success:active, .mv-btn-success.active {
}
.mv-btn-success:hover, .mv-btn-success:focus, .mv-btn-success:active, .mv-btn-success.active, .mv-btn-success.disabled, .mv-btn-success[disabled] {
}
.mv-btn-success:active, .mv-btn-success.active {
}

/* CATEGORY VIEW */
.cat-title {
}
.cat-desc {
}
.cat-file-wrapper {
}
.cat-file-name {
}
.cat-file-name a {
}

/* ALL FILES VIEW */
.cat-files {
}

/* SHOW/HIDE FILES */
.mv-show {  
}
.mv-hide  {  
}
.mv-button-hide {
}
.mv-button-show {
}';
	
	?>
	<div class="wrap mv-options">
		<h2 style="font-size:26px;"><?php _e('Multiverso Settings', 'mvafsp');?></h2>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<h2 style="font-size:22px;"><?php _e('File types', 'mvafsp');?></h2>
            <?php _e('Enable/Disable file types you want accept in the uploading form', 'mvafsp'); ?>
            <ul class="mv_opt_types">
            <li class="file-pdf">
			<input type="checkbox" class="" name="mv_mime_pdf" id="mv_mime_pdf" <?php if (!empty($mv_mime_pdf)){echo 'checked="checked"';} ?> value="application/pdf">
            </li>
            <li class="file-txt">
            <input type="checkbox" class="" name="mv_mime_txt" id="mv_mime_txt" <?php if (!empty($mv_mime_txt)){echo 'checked="checked"';} ?> value="text/plain"> 
            </li>
            <li class="file-zip">
            <input type="checkbox" class="" name="mv_mime_zip" id="mv_mime_zip" <?php if (!empty($mv_mime_zip)){echo 'checked="checked"';} ?> value="application/zip">
            </li>
            <li class="file-rar">
            <input type="checkbox" class="" name="mv_mime_rar" id="mv_mime_rar" <?php if (!empty($mv_mime_rar)){echo 'checked="checked"';} ?> value="application/rar">
            </li>
            <li class="file-doc">
            <input type="checkbox" class="" name="mv_mime_doc" id="mv_mime_doc" <?php if (!empty($mv_mime_doc)){echo 'checked="checked"';} ?> value="application/msword">
            </li>
            <li class="file-xls">
            <input type="checkbox" class="" name="mv_mime_xls" id="mv_mime_xls" <?php if (!empty($mv_mime_xls)){echo 'checked="checked"';} ?> value="application/vnd.ms-excel">
            </li>
            <li class="file-ppt">
            <input type="checkbox" class="" name="mv_mime_ppt" id="mv_mime_ppt" <?php if (!empty($mv_mime_ppt)){echo 'checked="checked"';} ?> value="application/vnd.ms-powerpoint">
            </li>
            <li class="file-gif">
            <input type="checkbox" class="" name="mv_mime_gif" id="mv_mime_gif" <?php if (!empty($mv_mime_gif)){echo 'checked="checked"';} ?> value="image/gif">
            </li>
            <li class="file-png">
            <input type="checkbox" class="" name="mv_mime_png" id="mv_mime_png" <?php if (!empty($mv_mime_png)){echo 'checked="checked"';} ?> value="image/png">
            </li>
            <li class="file-jpg">
            <input type="checkbox" class="" name="mv_mime_jpeg" id="mv_mime_jpeg" <?php if (!empty($mv_mime_jpeg)){echo 'checked="checked"';} ?> value="image/jpeg">
            </li>
            </ul>
            <div class="file-others"></div>
            <textarea class="small" name="mv_mime_others" id="mv_mime_others"><?php if (!empty($mv_mime_others)){echo $mv_mime_others;} ?> </textarea><br>
            <?php _e('Add your MIME Types <b>comma separated</b>. For ex. <em>video/mp4,audio/mpeg</em> (if you can\'t find the mime in the web try to use something like <b>application/psd</b> replacing <b>psd</b> with the extension of your file)', 'mvafsp'); ?><br><br>      
            
            <h2 style="font-size:22px;"><?php _e('File details Page', 'mvafsp');?></h2>
            <?php _e('You can use your theme\'s <em>single.php</em> file to show the Multiverso\'s file details', 'mvafsp'); ?><br>
            <div style="margin-top:10px;"><input type="checkbox" class="" name="mv_single_theme" id="mv_single_theme" <?php if (!empty($mv_single_theme)){echo 'checked="checked"';} ?> value="1"> <?php _e('Enable file details page', 'mvafsp'); ?></div><br>
            
            
            <h2 style="font-size:22px;"><?php _e('Manage Files Page', 'mvafsp');?></h2>
            <?php _e('This page can be used to allow the subscriber to add and edit his files by <b>Frontend</b>', 'mvafsp'); ?><br>
            <div style="margin-top:10px;">
            <select name="mv_manage_page" id="mv_manage_page">
            
			<?php 
			$mv_pages = get_pages(); 
			foreach ($mv_pages as $page){
				if (!empty($mv_manage_page) && $mv_manage_page == $page->ID) {
					echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.'</option>';
				}else{
					echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
				}
			}
			?>
            
            </select>
			<?php _e('Choose the page with <code>[mv_managefiles]</code> tag', 'mvafsp'); ?></div><br>
            
            
            <h2 style="font-size:22px;"><?php _e('Category Page', 'mvafsp');?></h2>
            <?php _e('This page is required to show your category view', 'mvafsp'); ?><br>
            <div style="margin-top:10px;">
            <select name="mv_category_page" id="mv_category_page">
            
			<?php 
			$mv_pages = get_pages(); 
			foreach ($mv_pages as $page){
				if (!empty($mv_category_page) && $mv_category_page == $page->ID) {
					echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.'</option>';
				}else{
					echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
				}
			}
			?>
            
            </select>
			<?php _e('Choose the page with <code>[mv_categories]</code> tag', 'mvafsp'); ?></div><br>
            
            
            <h2 style="font-size:22px;"><?php _e('Search Page', 'mvafsp');?></h2>
            <?php _e('This page is required to show your search view', 'mvafsp'); ?><br>
            <div style="margin-top:10px;">
            <select name="mv_search_page" id="mv_search_page">
            
			<?php 
			$mv_pages = get_pages(); 
			foreach ($mv_pages as $page){
				if (!empty($mv_search_page) && $mv_search_page == $page->ID) {
					echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.'</option>';
				}else{
					echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
				}
			}
			?>
            
            </select>
			<?php _e('Choose the page with <code>[mv_search]</code> tag', 'mvafsp'); ?></div><br>
        
            
            <h2 style="font-size:22px;"><?php _e('Miscellaneous', 'mvafsp');?></h2>
            
            <h2 style="font-size:18px;"><?php _e('Disable Downloader Feature', 'mvafsp'); ?></h2>
            <div style="margin-top:10px;"><input type="checkbox" class="" name="mv_disable_downloader" id="mv_disable_downloader" <?php if (!empty($mv_disable_downloader)){echo 'checked="checked"';} ?> value="1"> <?php _e('Checking this option you will disable the downloader feature', 'mvafsp'); ?></div> 
            <div class="<?php if (!empty($mv_disable_downloader)){echo 'error';}else{echo 'updated';} ?> below-h2" id="warning" style="width:800px;"><p><?php _e('<b>Attention</b>: With this option the url of files will be visible (not crypted) and some options like "File Size Limit" or "Download Number Limit" will be disabled. So use this option only if your hosting doesn\'t allow to do the download of a file.', 'mvafsp'); ?></p></div><br>
            
            <h2 style="font-size:18px;"><?php _e('File Size Limit', 'mvafsp');?></h2>
            <input class="small" name="mv_upload_size" id="mv_upload_size" value="<?php if (!empty($mv_upload_size)){echo $mv_upload_size;} ?>"> <?php _e('bytes', 'mvafsp');?><br>
            <?php _e('Insert file size limit in bytes for upload (leave blank to use default server limit)', 'mvafsp'); ?><br>
            <?php _e('<b><em>Pay Attention: this limit is not able to override your server setup!</em></b>', 'mvafsp'); ?><br><br> 
            
            <h2 style="font-size:18px;"><?php _e('Enable/Disable comments for file details page', 'mvafsp'); ?></h2>
            <div style="margin-top:10px;"><input type="checkbox" class="" name="mv_file_comments" id="mv_file_comments" <?php if (!empty($mv_file_comments)){echo 'checked="checked"';} ?> value="1"> <?php _e('Enable Comments', 'mvafsp'); ?></div><br>
            
            <h2 style="font-size:18px;"><?php _e('Custom Slugs', 'mvafsp');?></h2>
            <div class="updated below-h2" id="attention" style="width:800px;"><p><?php echo __('<b>Attention</b>: to change the slugs you need to save Multiverso\'s Settings and then the', 'mvafsp').'<a href="'.get_admin_url('', 'options-permalink.php').'"> '.__('Permalink\'s Settings', 'mvafsp').'</a>';?></p></div>            
            
            <?php _e('<b>Post Type Slug</b>', 'mvafsp'); ?><br>
            <input class="small2" name="mv_pt_slug" id="mv_pt_slug" value="<?php if (!empty($mv_pt_slug)){echo $mv_pt_slug;} ?>"> <br><?php _e('Name for the permalink folder in file view (ex. www.domain.ext/<b>files</b>/file-name). Leave blank for default name', 'mvafsp');?><br><br>
            <!--<?php _e('<b>Taxonomy Slug</b>', 'mvafsp'); ?><br>
            <input class="small2" name="mv_tax_slug" id="mv_tax_slug" value="<?php if (!empty($mv_tax_slug)){echo $mv_tax_slug;} ?>"> <br><?php _e('Name for the permalink folder in category view (ex. www.domain.ext/<b>file-category</b>/category-name). Leave blank for default name', 'mvafsp');?><br><br>-->
            
            
            <h2 style="font-size:22px; margin-top:20px;"><?php _e('Theme Compatibility', 'mvafsp');?></h2>
            
            <h2 style="font-size:18px;"><?php _e('Custom CSS', 'mvafsp');?></h2>
            <?php _e('Use Multiverso\'s classes to customize layouts with your <b>personal styles</b>', 'mvafsp'); ?><br>
            <textarea class="large" name="mv_custom_css" id="mv_custom_css" ><?php if (!empty($mv_custom_css)){ echo $mv_custom_css; }else{echo $default_css_classess;} ?></textarea>
            
            <h2 style="font-size:18px;"><?php _e('Before Templates', 'mvafsp');?></h2>
            <?php _e('Write here the HTML code to add BEFORE templates\'s code ', 'mvafsp'); ?><br>
            <textarea class="small" name="mv_before_tpl" id="mv_before_tpl" ><?php if (!empty($mv_before_tpl)){ echo $mv_before_tpl; } ?></textarea>
            
            <h2 style="font-size:18px;"><?php _e('After Templates', 'mvafsp');?></h2>
            <?php _e('Write here the HTML code to add AFTER templates\'s code ', 'mvafsp'); ?><br>
            <textarea class="small" name="mv_after_tpl" id="mv_after_tpl" ><?php if (!empty($mv_after_tpl)){ echo $mv_after_tpl; } ?></textarea>
            
			<input type="hidden" name="update" value="update">
			<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'mvafsp');?>" class="button-primary" id="submit" name="submit"></p>
		</form>
	</div>
	<?php
}

?>