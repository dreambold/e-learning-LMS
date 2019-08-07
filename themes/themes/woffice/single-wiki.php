<?php
/**
 * The Template for displaying all single wiki
 */
 
// UPDATE POST
global $post;

$edit_allowed = (Woffice_Frontend::edit_allowed('wiki') == true) ? true : false;

if($edit_allowed) {
    $process_result = Woffice_Frontend::frontend_process('wiki', $post->ID);
}

get_header(); ?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT

			woffice_title(get_the_title()); ?>

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">
				<!-- START CONTENT -->
				<div id="content">
					<?php if (woffice_is_user_allowed_wiki()){
						
						$post_classes = array('box', 'content', 'woffice-tab-layout');

                        if(get_post_status() == 'draft')
                            array_push($post_classes, 'is-draft');
                        ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
							<?php if ( has_post_thumbnail()) : ?>
								<!-- THUMBNAIL IMAGE -->
								<?php Woffice_Frontend::render_featured_image_single_post(get_the_ID()); ?>
							<?php endif; ?>

							<div id="wiki-nav" class="intern-box">
								<div class="item-list-tabs-wiki">
									<ul class="woffice-tab-layout__nav">
										<li id="wiki-tab-view" class="active" data-tab="view">
											<a href="javascript:void(0)" class="fa-file"><?php _e("View","woffice"); ?></a>
										</li>
										<?php if ($edit_allowed) { ?>
											<li id="wiki-tab-edit" data-tab="edit">
												<a href="javascript:void(0)" class="fa-edit"><?php _e("Edit","woffice"); ?></a>
											</li>
										<?php } ?>
										<?php if(woffice_wiki_have_comments()): ?>
										<li id="wiki-tab-comments" data-tab="comments">
											<a href="javascript:void(0)" class="fa-comments">
												<?php _e("Comments","woffice"); ?>
												<span><?php comments_number( '0', '1', '%' ) ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php
                                        /**
                                         * You can disable the tab "Revision" of the single wiki page
                                         *
                                         * @param bool
                                         */
                                        $wiki_tab_revision_enabled = apply_filters('woffice_enable_wiki_tab_revisions', true);

                                        if( $wiki_tab_revision_enabled ): ?>
                                            <li id="wiki-tab-revisions" data-tab="revisions">
                                                <a href="javascript:void(0)" class="fa-history"><?php _e("Revisions","woffice"); ?></a>
                                            </li>
										<?php endif; ?>
										<?php
										if (Woffice_Frontend::edit_allowed('wiki', 'delete')) :
										?>
											<li id="wiki-tab-delete">
												<a onclick="return confirm('<?php echo __('Are you sure you wish to delete article :','woffice').' '. get_the_title(); ?> ?')" href="<?php echo get_site_url().wp_nonce_url('/wp-admin/post.php?action=trash&amp;post='.get_the_ID(), 'trash-post_'.get_the_ID() ); ?>" class="fa-trash">
													<?php _e("Delete","woffice"); ?>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
							<div class="wiki-tabs-wrapper intern-padding woffice-tab-layout__content">
								<!-- DISPLAY ALL THE CONTENT OF THE WIKI ARTICLE-->
								<div id="wiki-content-view" data-tab="view" class="woffice-tab-layout__tab">
									<?php // THE CONTENT 
									the_content(); ?>
									<?php // THE LIKE BUTTON
									echo woffice_get_wiki_like_html(get_the_ID()); ?>
							  		<?php // DISPLAY THE NAVIGATION
							  		woffice_post_nav(); ?>
								</div>
								
								<?php if ($edit_allowed) { ?>
									<!-- EDIT THE CONTENT IN FRONTEND VIEW-->
									<div id="wiki-content-edit" data-tab="edit" class="woffice-tab-layout__tab">

                                        <?php
                                        Woffice_Frontend::frontend_render('wiki', $process_result, get_the_ID()); ?>

									</div>
								<?php } ?>

                                <?php if(woffice_wiki_have_comments()) : ?>
                                    <!-- SEE THE COMMENTS-->
                                    <div id="wiki-content-comments" data-tab="comments" class="woffice-tab-layout__tab">
                                        <?php
                                        // If comments are open or we have at least one comment, load up the comment template.
                                        if ( comments_open() || get_comments_number() ) {
                                            comments_template();
                                        }
                                        else {
                                            _e("Comments are closed...","woffice");
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
								
								<!-- SEE THE REVISIONS-->
								<div id="wiki-content-revisions" data-tab="revisions" class="woffice-tab-layout__tab">
									<?php // GET REVISIONS
										$revisions = wp_get_post_revisions(get_the_ID());
										if(!empty($revisions)):
											echo '<ul class="list-styled list-change">'; 
											foreach ($revisions as $revision) {
												$post_author = woffice_get_name_to_display( $revision->post_author );
												$date = wp_post_revision_title( $revision, false );
												echo '<li>'. '<strong>'. esc_html( $post_author ) . '</strong> ' . 'on ' .esc_html( $date ) .'</li>';
											}
											echo '</ul>';
										else : 
											echo "<p>". __("This article has not been revised since publication.","woffice") ."</p>";
										endif; 
									?>
									<p>
										<?php echo __('This post was created by','woffice') .' <strong>'. get_the_author() .'</strong>';
										echo __(' on','woffice') .'<strong> '. get_the_date() .'</strong>.'; ?>
									</p>
								</div>
								
							</div>
						</article> 
					<?php } else { 
						get_template_part( 'content', 'private' );
					} ?>

				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->
	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();