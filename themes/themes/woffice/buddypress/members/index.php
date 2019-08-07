<?php
/**
 * BuddyPress Members Directory
 *
 * @version 3.0.0
 */

if (isset($_GET['members_table'])) {
	fw_set_db_settings_option( 'buddy_members_layout_temp', 'table');
}

?>

<div class="box content" data-template="woffice">

	<div class="intern-padding clearfix" data-template="woffice">

		<?php bp_nouveau_before_members_directory_content(); ?>

		<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

			<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

		<?php endif; ?>

		<?php woffice_members_filter(); ?>

		<div class="screen-content">

			<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

			<div id="members-dir-list" class="members dir-list" data-bp-list="members">
				<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-members-loading' ); ?></div>
			</div>

			<?php bp_nouveau_after_members_directory_content(); ?>
		</div>

	</div>

</div>
