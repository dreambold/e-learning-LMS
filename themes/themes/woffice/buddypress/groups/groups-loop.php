<?php
/**
 * BuddyPress - Groups Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="<?php bp_nouveau_loop_classes(); ?>">

		<?php
		while ( bp_groups() ) :
			bp_the_group();
			?>

			<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
				<div class="list-wrap">

					<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
						<?php
						/**
						 * We get the cover image if it exists
						 * @since 2.3.0
						 */
						$group_cover_image_url = bp_attachments_get_attachment('url', array(
							'object_dir' => 'groups',
							'item_id' => bp_get_group_id(),
						));
						if(!empty($group_cover_image_url)) {
							$cover_style = 'style="background-image: url('.$group_cover_image_url.')"';
							$cover_class = 'has-cover';
						} else {
							$cover_style = '';
							$cover_class = '';
						}
						?>
						<div class="item-avatar <?php echo $cover_class; ?>" <?php echo $cover_style; ?> data-template="woffice">
							<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
						</div>
					<?php endif; ?>

					<div class="item">

						<div class="item-block">

							<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

							<?php if ( bp_nouveau_group_has_meta() ) : ?>

								<p class="item-meta group-details"><?php bp_nouveau_group_meta(); ?></p>

							<?php endif; ?>

							<p class="last-activity item-meta">
								<?php
								printf(
								/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
									__( 'active %s', 'buddypress' ),
									bp_get_group_last_active()
								);
								?>
							</p>

						</div>

						<div class="group-desc"><p><?php bp_nouveau_group_description_excerpt(); ?></p></div>

						<?php bp_nouveau_groups_loop_item(); ?>

						<?php bp_nouveau_groups_loop_buttons(); ?>

					</div>


				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>

<?php
bp_nouveau_after_loop();