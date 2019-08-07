<?php
/**
 * Woffice custom member template
 *
 * @since 2.8.0
 */

/**
 * You can hide the role of users displayed in the members loop page
 *
 * @param bool
 */
$members_role_enabled = apply_filters('woffice_enable_member_role_on_members_page', true);

/**
 * You can hide the last activity of users displayed in the members loop page
 *
 * @param bool
 */
$last_activity_enabled = apply_filters('woffice_enable_member_last_activity_on_members_page', true);
?>

<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">
			<div class="list-wrap">

				<?php
				$user_id   = (int) bp_get_member_user_id();
				$the_cover = woffice_get_cover_image($user_id);
				?>

				<?php if (!empty($the_cover)): ?>
				<div class="item-avatar has-cover" style="background-image: url(<?php echo esc_url($the_cover); ?>)" data-template="woffice">
					<?php else : ?>
					<div class="item-avatar">
						<?php endif; ?>

						<?php $role = woffice_get_user_role($user_id); ?>

						<?php if ($members_role_enabled && !empty($role)): ?>
							<span class="badge badge-primary" data-template="woffice"><?php echo $role; ?></span>
						<?php endif; ?>

						<a href="<?php bp_member_permalink(); ?>">
							<?php bp_member_avatar( bp_nouveau_avatar_args() ); ?>
						</a>
					</div>

					<div class="item">

						<div class="item-block">

							<h2 class="list-title member-name">
								<a href="<?php bp_member_permalink(); ?>">
									<?php echo woffice_get_name_to_display($user_id); ?>
								</a>
							</h2>

							<?php if ( bp_nouveau_member_has_meta() && $last_activity_enabled ) : ?>
								<p class="item-meta last-activity">
									<?php bp_nouveau_member_meta(); ?>
								</p><!-- #item-meta -->
							<?php endif; ?>

							<?php
							/**
							 * Before the list of custom member fields, in the members page (card layout)
							 */
							do_action('woffice_before_list_xprofile_fields');

							woffice_list_xprofile_fields(bp_get_member_user_id());

							/**
							 * After the list of custom member fields, in the members page (card layout)
							 */
							do_action('woffice_after_list_xprofile_fields'); ?>

							<?php
							bp_nouveau_members_loop_buttons(
								array(
									'container'      => 'ul',
									'button_element' => 'button',
								)
							);
							?>

							<a href="<?php bp_member_permalink(); ?>" class="btn btn-primary mb-0 d-block" data-template="woffice">
								<?php _e('Profile', 'woffice') ?>
								<i class="fas fa-arrow-right pl-2 pr-0"></i>
							</a>

						</div>

					</div><!-- // .item -->



				</div>
		</li>

	<?php endwhile; ?>

</ul>