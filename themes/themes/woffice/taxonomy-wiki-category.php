<?php

$wiki_create = woffice_get_settings_option('wiki_create');
$woffice_role_allowed = Woffice_Frontend::role_allowed($wiki_create, 'wiki');
$process_result = array();

if (function_exists( 'woffice_wiki_extension_on' )){

	if ($woffice_role_allowed):

		$process_result = Woffice_Frontend::frontend_process('wiki');

	endif;

}


get_header();
?>

<?php // Start the Loop.

// We check for excluded categories
$wiki_excluded_categories = woffice_get_settings_option('wiki_excluded_categories');
/*If it's not a child only*/
$wiki_excluded_categories_ready = (!empty($wiki_excluded_categories)) ? $wiki_excluded_categories : array();
$enable_wiki_accordion = woffice_get_settings_option('enable_wiki_accordion');
$enable_wiki_accordion = ( $enable_wiki_accordion == 'yep') ? true : false;
$sortbylike_option = ( function_exists( 'fw_get_db_settings_option' ) ) ? fw_get_db_settings_option('wiki_sortbylike') : '';
$wiki_sortbylike = ($sortbylike_option == 'yep' && isset($_GET['sortby']) && $_GET['sortby']=='like') ? true : false ;

 ?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT

		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title =  $term->name;
		woffice_title($title);
		?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				<?php if (woffice_is_user_allowed()) { ?>
					<?php
					// CUSTOM CLASSES ADDED BY THE THEME
					$post_classes = array('box','content');
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
						<div id="wiki-page-content" class="intern-padding">
							<?php
							//DISABLED IN THIS THEME
							wp_link_pages(array('echo'  => 0));
							//EDIT LINK
							edit_post_link( __( 'Edit', 'woffice' ), '<span class="edit-link">', '</span>' );

							woffice_wiki_sort_by_like();

							$wiki_display = new Woffice_Wiki_Display_Manager($term->term_id);

							$wiki_display->displayCategories();

							woffice_wiki_display_actions_buttons();
							?>
						</div>

						<?php
						if (function_exists( 'woffice_wiki_extension_on' )){
							// CHECK IF USER CAN CREATE WIKI PAGE
							if ($woffice_role_allowed):  ?>

								<?php Woffice_Frontend::frontend_render('wiki', $process_result); ?>

							<?php endif;
						} ?>
					</article>

					<?php
				} else {
					get_template_part( 'content', 'private' );
				}
				?>
			</div>

		</div><!-- END #content-container -->

		<?php woffice_scroll_top(); ?>

	</div><!-- END #left-content -->

<?php // END THE LOOP
?>

<?php
get_footer();



