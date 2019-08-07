<?php
/**
 * The Template for displaying search file page.
 *
 * @package WordPress
 * @subpackage Multiverso - Advanced File Sharing Plugin v2.6
 *
 */
?>


	<?php $html = get_option('mv_before_tpl'); // BEFORE TPL CODE ?>

	<?php $html .= '<div class="mv-wrapper">'; ?>
	<?php $html .= '<div class="mv-content">'; ?>

        <?php if (!empty($_POST['mvs'])){ $keyword = $_POST['mvs']; }else{$keyword = 'No Keyword';} ?>
        
		<?php $html .= '<h1 class="search-title"><i class="icon-search mvsearch-icon"></i>'.__('Results for: ', 'mvafsp').'"'.$keyword.'"</h1>'; ?>

        <?php $html .= '<div class="search-files">'; ?>
		
		<?php 
		global $wp;
				
		$args = array(
		'post_type'	 =>	'multiverso',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		's' => $keyword,
		'posts_per_page' => -1 
	     );
		 
		$loop = new WP_Query( $args  ); 	
		?>
       
        <?php if($loop->have_posts()) { ?>
    
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>	
                    
                    <?php // Start check permissions
					$mv_access = get_post_meta(get_the_ID(), 'mv_access', true);
					$mv_user = get_post_meta(get_the_ID(), 'mv_user', true);
					global $current_user;
					get_currentuserinfo();
					$mv_logged = $current_user->user_login;
					
					// Start check visibility
					if ( mv_user_can_view_file(get_the_ID()) ) { 
                        
                        $html .= mv_file_details_html( get_the_ID() );
                       
                    }else{
						$nofile = __('No file found.', 'mvafsp');
					}// End check permissions ?>
                     
					<?php endwhile; ?>
            
		  <?php }else{ $html .= __('<br>No results for your keyword.', 'mvafsp'); } ?>
          
		  <?php wp_reset_query(); wp_reset_postdata(); ?> 
          
          <?php $html .= '</div> <!-- End .file list -->'; ?>
        
        <?php $html .= '</div>'; ?>
        
   
        
        <?php $html .= '<div class="mv-clear"></div>'; ?>
        <?php $html .= ' </div>'; ?>
        
        
        <?php $html .= get_option('mv_after_tpl'); // AFTER TPL CODE 
		

// Return HTML
return $html;
		
?>
        

