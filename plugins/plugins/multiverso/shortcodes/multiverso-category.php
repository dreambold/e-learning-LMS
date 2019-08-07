<?php
/**
 * The Template for displaying single category page.
 *
 * @package WordPress
 * @subpackage Multiverso - Advanced File Sharing Plugin v2.6
 *
 */
?>
<?php 

if(isset($_GET['catid'])) {
	
	$catid = $_GET['catid'];
	
}elseif(!empty($mv_single_cat_id) && get_term_by('id', $mv_single_cat_id, 'multiverso-categories') ) {
	
	$catid = $mv_single_cat_id;
	
}


?>
<?php if(!empty($catid)) { ?>

    <?php $html = get_option('mv_before_tpl'); // BEFORE TPL CODE ?>
    
	<?php $html .= '<div class="mv-wrapper">'; ?>
	<?php $html .= '<div class="mv-content">'; ?> 
        
        
		<?php 
		$cat =	get_term($catid, 'multiverso-categories');
		$category_link = esc_url( add_query_arg( 'catid', $cat->term_id, get_permalink( get_option('mv_category_page') ) ) );		
		?>
	
        
        <?php 
			
			// Category Heading            
            $html .= '
            <div class="cat-title" id="category'.$cat->term_id.'">
                <a href="'.$category_link.'">'.$cat->name.'</a>
				<i class="mvico-zoomout closelist mv-button-show" data-filelist="filelist'.$cat->term_id.'"></i> 
                <i class="mvico-zoomin openlist mv-button-hide" data-filelist="filelist'.$cat->term_id.'"></i>                
            </div>
			'; 
            
            if (!empty($cat->description)) { 
			
				$html .= '<div class="cat-desc entry-content">'.$cat->description.'</div>'; 
			
			}
			
			
			// Subcategories & Files
			$html .= '<div class="cat-files" id="filelist'.$cat->term_id.'">';
				
				// Subcategories
				$html .= mv_display_subcategories($cat->term_id);
				
				// Files
				$html .= mv_display_catfiles($cat->slug, $cat->name);
				
			$html .= '</div>';
            
        ?>
     
     <?php $html .= '</div>'; ?>
     <?php $html .= '</div>'; ?>   
     
        <?php echo get_option('mv_after_tpl'); // AFTER TPL CODE ?>

<?php }else{ $html .= __('You can\'t directly access to this page, click on category in all files page or use the single category shortcode (using an existing category).', 'mvafsp'); } 


// Return HTML
return $html;

?>

