<?php
/**
 * The Template for displaying file page.
 *
 * @package WordPress
 * @subpackage Multiverso - Advanced File Sharing Plugin v2.6
 *
 */

?>
		
		<?php 
		global $post;
		$mv_access = get_post_meta($post->ID, 'mv_access', true);
		$mv_user = get_post_meta($post->ID, 'mv_user', true);
		global $current_user;
        get_currentuserinfo();
		$mv_logged = $current_user->user_login;
		
		$html = '';
		
		if ( mv_user_can_view_file(get_the_ID()) ) { 
		// Start Display template
		
		?>
         
        <?php $html .= '<div class="entry-content">'.do_shortcode(get_the_content()).'</div>'; ?>
       
        <?php $html .= '<h3 class="file-details-title">'.__('File Details', 'mvafsp').'</h3>'; ?>
        <?php $html .= '<div class="file-info">'; ?>
        
		<?php $html .= __( 'Uploaded', 'mvafsp').' <span class="file-date">'.human_time_diff( get_the_time('U'), current_time('timestamp') ). __( ' ago', 'mvafsp').'</span> in ';  ?>
        <?php 
		$_taxonomy = 'multiverso-categories';
		$_post_type = 'multiverso';
        $terms = get_the_terms( get_the_ID(), $_taxonomy );
		if ( !empty( $terms ) ) {
            $out = array();
            foreach ( $terms as $c ) 
				$out[] = '<a href="'.get_permalink( get_option('mv_category_page') ).'?catid='.$c->term_id.'">'.$c->name.'</a>';
            	$html .= join( ', ', $out );
        }
        else {
            $html .= 'No Category';
        }	
		?>
        
        <?php $html .= '</div>'; ?>
        
        <?php $html .= mv_file_details_html( get_the_ID() ); ?>
        
        
        <?php // End Display Template 

		}else{ // Start Display Error
		
			$html .= __('You are not allowed to view this file', 'mvafsp');
		
		} // End Display Error 
		
		// Return HTML
		return $html;
		
		?>



