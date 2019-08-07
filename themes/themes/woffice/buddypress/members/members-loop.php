<?php
/**
 * BuddyPress - Members Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */
$buddy_members_layout = woffice_get_settings_option('buddy_members_layout');
$buddy_members_layout_temp = woffice_get_settings_option('buddy_members_layout_temp');

if (!empty($buddy_members_layout_temp)) {
	$buddy_members_layout = $buddy_members_layout_temp;
}

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

bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<?php if ($buddy_members_layout == "cards") : ?>

		<?php bp_get_template_part( 'members/layout/card' ); ?>

	<?php else: ?>

		<?php bp_get_template_part( 'members/layout/table' ); ?>

	<?php endif; ?>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php
else :

	bp_nouveau_user_feedback( 'members-loop-none' );

endif;

if (!empty($buddy_members_layout_temp)) {
	fw_set_db_settings_option( 'buddy_members_layout_temp', '');
}


?>

<?php bp_nouveau_after_loop(); ?>
