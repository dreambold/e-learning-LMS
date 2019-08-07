<?php
/**
 * The template used for displaying post content on the masonry layout
 */

$blog_listing_content = woffice_get_settings_option('blog_listing_content','excerpt');
?>


		<?php 
		/*
		 * Basically it's a copy paste of the directory one, so we may add a function later
		*/ 	
		echo '<div class="box blog-item">';
			/* Featured Image */
			if ( has_post_thumbnail() ) :

                Woffice_Frontend::render_featured_image_single_post($post->ID, "", true);
			endif; 
			/* Content */
			echo '<div class="intern-padding">';
				/* Title */
				echo'<div class="intern-box box-title">
					<h3><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h3>
				</div>';
				/* Excerpt */
				echo '<div>';
					if (is_single() || $blog_listing_content == 'content') {
						the_content('');
					} else {
						the_excerpt();
					}
				echo '</div>';
				
				/* Categories */
				if (get_the_category_list() !== "") {
					echo '<span class="directory-category"><i class="fa fa-tag"></i>';
					echo get_the_category_list( __( ', ', 'woffice' ) );
					echo '</span>';
				}
				
				/* Comments */
				if (get_comment_count(get_the_ID()) > 0){
					echo'<span class="directory-comments"><i class="fa fa-comments"></i> ';
						echo'<a href="'. get_the_permalink().'#respond">'. get_comments_number( get_the_ID()) .'</a>';
						echo'</span>';	
				}

                /* Date */
                echo '<span class="post-date directory-comments">';
                echo '<i class="fa fa-clock"></i> ' . get_the_date();
                echo '</span>';
			echo '</div>';
			
			echo '<div class="intern-box text-center">';
				echo '<a href="'.get_the_permalink().'" class="btn btn-default mb-0"><i class="fa fa-arrow-right"></i> '.__('Read more','woffice').'</a>';
			echo '</div>';
		echo '</div>'; ?>
