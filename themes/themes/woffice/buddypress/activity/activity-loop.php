<?php
/**
 * BuddyPress - Activity Loop
 *
 * @version 3.1.0
 */

bp_nouveau_before_loop(); ?>

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
		<ul class="activity-list item-list bp-list">
	<?php endif; ?>

	<?php
	while ( bp_activities() ) :
		bp_the_activity();

		$item_id = bp_get_activity_item_id();

		if (!empty($item_id)) {
			$post_type = get_post_type($item_id);
			$allowed  = woffice_is_user_allowed($item_id);

			if (!$allowed) {
				continue;
			}
		}
		?>

		<?php bp_get_template_part( 'activity/entry' ); ?>

	<?php endwhile; ?>

	<?php if ( bp_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="<?php bp_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'buddypress' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'activity-loop-none' ); ?>

<?php endif; ?>

<?php bp_nouveau_after_loop(); ?>
