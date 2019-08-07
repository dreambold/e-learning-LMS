<?php
/*
File: inc/functions-html.php
Description: HTML Functions
Plugin: Multiverso - Advanced File Sharing Plugin
Author: Alessio Marzo & Andrea Onori
*/


//***********************************************//
//              DISPLAY SUB CATEGORY             //
//***********************************************//

function mv_display_subcategories($parent, $personal = false, $registered = false) {
	
	$html = '';
	
	// Sub-Categories Query
	$sub_args = array(
		'type'                     => 'multiverso',
		'parent'                   => $parent,
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'taxonomy'                 => 'multiverso-categories',
		'pad_counts'               => false );
		
	$sub_categories = get_categories($sub_args);
	
	if(!empty($sub_categories)){ // IF Array not empty
		
		foreach($sub_categories as $subcategory) { //Sub-Categories Loop
		
		$subcategory_link = esc_url( add_query_arg( 'catid', $subcategory->term_id, get_permalink( get_option('mv_category_page') ) ) );		
		
			// Sub-Category Heading            
            $html .= '
            <div class="cat-title subcat" id="category'.$subcategory->term_id.'">
                <a href="'.$subcategory_link.'">'.$subcategory->name.'</a>
                <i class="mvico-zoomin openlist mv-button-show" data-filelist="filelist'.$subcategory->term_id.'"></i>
                <i class="mvico-zoomout closelist mv-button-hide" data-filelist="filelist'.$subcategory->term_id.'"></i> 
            </div>
			'; 
            
            if (!empty($subcategory->description)) { $html .= '<div class="cat-desc entry-content subcat">'.$subcategory->description.'</div>'; }
			
			
			// Sub-Categories & Files
			$html .= '<div class="cat-files mv-hide subcat" id="filelist'.$subcategory->term_id.'">';
				
				// Sub-Categories
				$html .= mv_display_subcategories($subcategory->term_id, $personal, $registered);
				
				// Files
				$html .= mv_display_catfiles($subcategory->slug, $subcategory->name, $personal, $registered);
				
			$html .= '</div>';
			
		
		}// End Sub-Categories Loop
		
		
	} // End IF Array not empty
	
	
	// Return HTML
	return $html;
	
}


//***********************************************//
//             DISPLAY CATEGORY FILES            //
//***********************************************//

function mv_display_catfiles($catslug, $catname = NULL, $personal = false, $registered = false) {
	
	$html = '';
	
	if ( $personal == true ) {
		
		// Check current user logged
		global $current_user;
		$mv_logged = $current_user->user_login;
		
					
		// Personal Query Args
		$args = array(
			'post_type'	 =>	'multiverso',
			'post_status' => 'publish',
			'meta_key' => 'mv_user', 
			'meta_value' => $mv_logged, 
			'meta_compare' => '==',
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'multiverso-categories',
					'terms' => $catslug,
					'field' => 'slug',
					'include_children' => false
				)
    		)
	     );
		
	}elseif ( $registered == true ) {
		
		// Check current user logged
		global $current_user;
		$mv_logged = $current_user->user_login;
		
					
		// Personal Query Args
		$args = array(
			'post_type'	 =>	'multiverso',
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'mv_access',
					'value' => 'registered',
					'compare' => '=='
				),
				array(
					'key' => 'mv_access',
					'value' => 'public',
					'compare' => '=='
				),
			),
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'multiverso-categories',
					'terms' => $catslug,
					'field' => 'slug',
					'include_children' => false
				)
    		)
	     );
		
	}else{
		
		// Standard Query Args
		$args = array(
			'post_type'	 =>	'multiverso',
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'multiverso-categories',
					'terms' => $catslug,
					'field' => 'slug',
					'include_children' => false
				)
    		)              
		);
		 
	}
	
	
	// Start Query	 
	$files = new WP_Query( $args  ); 
	
	
	// Object security Check
	if ($files) {
		$fcount = 0;
		// Loop
		while ( $files->have_posts() ) { 
		
			// Set Post Data
			$files->the_post();
			
			// Check the Access for the file
			if(mv_user_can_view_file( get_the_ID() )) {
				
				// FCount incrementation
				$fcount++;
				
				// Display the File
				$html .= mv_file_details_html( get_the_ID() );
				
			}
			
			// Reset Post Data
			wp_reset_postdata();
			
		}
		
		// IF Cat is empty
		if ( !$files->have_posts() || $fcount == 0 ) {
			
			//echo '<div class="mv-no-files">'. __('No files found in ', 'mvafsp').$catname.'</div>';
			
		}
		
		
	}
	
	// Return HTML
	return $html;
		
}


//***********************************************//
//               DISPLAY FILE HTML               //
//***********************************************//

// File Details (HTML)
function mv_file_details_html( $fileID ) {
	
	$html = '';
	
	$mv_access = get_post_meta($fileID, 'mv_access', true);
	$mv_user = get_post_meta($fileID, 'mv_user', true);	
	$mv_file_check = get_post_meta($fileID, 'mv_file', true);
	
	
	if (!empty($mv_file_check)) {	
		
		
		// Switch right icon
		switch ($mv_file_check['type']) {
			
			case 'application/pdf':
			$icon_class = 'file-pdf';
			break;
			
			case 'text/plain':
			$icon_class = 'file-txt';
			break;
			
			case 'application/zip':
			$icon_class = 'file-zip';
			break;
			
			case 'application/rar':
			$icon_class = 'file-rar';
			break;
			
			case 'application/msword':
			$icon_class = 'file-doc';
			break;
			
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			$icon_class = 'file-doc';
			break;
			
			case 'application/vnd.ms-excel':
			$icon_class = 'file-xls';
			break;
			
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			$icon_class = 'file-xls';
			break;
			
			case 'application/vnd.ms-powerpoint':
			$icon_class = 'file-ppt';
			break;
			
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			$icon_class = 'file-ppt';
			break;
			
			case 'image/gif':
			$icon_class = 'file-gif';
			break;
			
			case 'image/png':
			$icon_class = 'file-png';
			break;
			
			case 'image/jpeg':
			$icon_class = 'file-jpg';
			break;
			
			default:
			$icon_class = 'file-others';
			break;
	}
		
	// Filename
	$filename_ar = explode('/', $mv_file_check['file']);
	
	if(!empty($filename_ar[1])){
		
		$filename = $filename_ar[1];
		
		if (get_option('mv_disable_downloader') == 1) {
			$filedownload = WP_CONTENT_URL.'/'.WP_MULTIVERSO_UPLOAD_FOLDER.'/'.$filename_ar[0].'/'.$filename_ar[1];
		}else{
			$filedownload = '?upf=dl&id='.$fileID;
		}
		
		
		
	}else{
		$filename = $mv_file_check['file'];
		$filedownload = $mv_file_check['url'];
	}
	
	$html .= '
	
	<div class="file-details-wrapper">
        <div class="file-details">     
    

        <div class="file-data '.$icon_class.'">';
		
		if (get_option('mv_single_theme') == 1) {
        	$html .= '<div class="file-name"><a href="'.get_the_permalink($fileID).'" title="'.__('View details', 'mvafsp').'">'.get_the_title($fileID).'</a> 
				 (<a href="'.$filedownload.'" target="_blank" title="'.__('Download file', 'mvafsp').'">'.$filename.'</a>)</div>';
		}else{
			$html .= '<div class="file-name">'.__('File', 'mvafsp').' <a href="'.$filedownload.'" target="_blank" title="'.__('Download file', 'mvafsp').'">'.$filename.'</a></div>';
		}
		
        $html .= '<ul class="file-data-list">
            	<li class="file-owner"><i class="mvico-user3" title="'.__('Owner', 'mvafsp').'"></i>'.ucfirst($mv_user).'</li>
                <li class="file-publish"><i class="mvico-calendar"></i>'.get_post_time( 'F j, Y', false,  $fileID ).'</li>
                <li class="file-access file-'.$mv_access.'"><i class="mvico-eye"></i>'.ucfirst($mv_access).'</li>
            </ul>
        </div>
		
		<div class="file-dw-button">
        <a class="mv-btn mv-btn-success" href="'.$filedownload.'" target="_blank" title="'.__('Download file', 'mvafsp').'">'.__('Download', 'mvafsp').'</a>
        </div>';
		
		if (get_option('mv_single_theme') == 1) {
			$html .= '<div class="file-dw-button">
        			  <a class="mv-btn mv-btn-success" href="'.get_the_permalink($fileID).'" title="'.__('Details', 'mvafsp').'">'.__('Details', 'mvafsp').'</a>
        			  </div>';
		}
        
		$html .= '<div class="mv-clear"></div>
		
        </div>
        </div>';
	
	
	
	}else{
		
	$html .= '
	
	<div class="file-details-wrapper">
        <div class="file-details">     
    

        <div class="file-data file-others">';
		
		if (get_option('mv_single_theme') == 1) {
        	$html .= '<div class="file-name"><a href="'.get_the_permalink($fileID).'" title="'.__('View details', 'mvafsp').'">'.get_the_title($fileID).'</a></div>';
		}else{
			$html .= __('File: none', 'mvafsp');
		}
            $html .= '<ul class="file-data-list">
            	<li class="file-owner"><i class="mvico-user3" title="'.__('Owner', 'mvafsp').'"></i>'.ucfirst($mv_user).'</li>
                <li class="file-publish"><i class="mvico-calendar"></i>'.get_post_time( 'F j, Y', false,  $fileID ).'</li>
                <li class="file-access file-'.$mv_access.'"><i class="mvico-eye"></i>'.ucfirst($mv_access).'</li>
            </ul>
        </div>
		
		<div class="file-dw-button disabled" title="'.__('No file uploaded yet', 'mvafsp').'">
        <span class="mv-btn mv-btn-success">'.__('Download', 'mvafsp').'</span>
        </div>';
		
		if (get_option('mv_single_theme') == 1) {
			$html .= '<div class="file-dw-button">
			<a class="mv-btn mv-btn-success" href="'.get_the_permalink($fileID).'" title="'.__('Details', 'mvafsp').'">'.__('Details', 'mvafsp').'</a>
			</div>';
		}
        
		$html .= '<div class="mv-clear"></div>
		
        </div>
        </div>';
		
	}
	
	// Return HTML
	return $html;
	
}
