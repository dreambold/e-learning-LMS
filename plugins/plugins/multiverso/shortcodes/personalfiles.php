<?php
/**
 * Personal Files Shortcode
 *
 * @package WordPress
 * @subpackage Multiverso - Advanced File Sharing Plugin v2.6
 *
 */
 ?>
 
<?php $html = get_option('mv_before_tpl'); // BEFORE TPL CODE ?>

<?php 	
		// Check current user logged
		global $current_user;
		$mv_logged = $current_user->user_login;
		
		// Categories Query
		$args = array(
		'type'                     => 'multiverso',
		'parent'                   => 0,
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'taxonomy'                 => 'multiverso-categories',
		'pad_counts'               => false );
		
		$categories = get_categories($args);
		
?>

<?php $html .= '<div class="mv-wrapper">'; ?>

	<?php $html .= '<div class="mv-content">'; ?>
    
    <?php if( !empty($mv_logged) ) { // Check if user is logged ?> 
    	
		<?php foreach ($categories as $category) { // LOOP ALL CATEGORIES 
		
		$category_link = esc_url( add_query_arg( 'catid', $category->term_id, get_permalink( get_option('mv_category_page') ) ) );	
		
		?>
        
			<?php  
			
			// Category Heading            
            $html .= '
            <div class="cat-title" id="category'.$category->term_id.'">
                <a href="'.$category_link.'">'.$category->name.'</a>
                <i class="mvico-zoomin openlist mv-button-show" data-filelist="filelist'.$category->term_id.'"></i>
                <i class="mvico-zoomout closelist mv-button-hide" data-filelist="filelist'.$category->term_id.'"></i> 
            </div>
			'; 
            
            if (!empty($category->description)) { 
			
				$html .= '<div class="cat-desc entry-content">'.$category->description.'</div>'; 
			
			}
			
			
			// Subcategories & Files
			$html .= '<div class="cat-files mv-hide" id="filelist'.$category->term_id.'">';
				
				// Subcategories
				$html .= mv_display_subcategories($category->term_id, true);
				
				// Files
				$html .= mv_display_catfiles($category->slug, $category->name, true);
				
			$html .= '</div>';
            
            ?>
        
        <?php } // END CATEGORY LOOP ?>
     
    <?php }else{ ?>
    
    	<?php $html .= __('You must be logged to view this page.', 'mvafsp'); ?>
    
    <?php } // End IF User is Logged ?>
        
    <?php $html .= '</div> <!-- /mv-content -->'; ?>

<?php $html .= '<div class="mv-clear"></div>'; ?>
  
<?php $html .= '</div> <!-- /mv-wrapper -->'; ?>

<?php $html .= get_option('mv_after_tpl'); // AFTER TPL CODE

// Return HTML
return $html;

