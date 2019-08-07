<?php
/**
 * BP Nouveau - Groups Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div class="box content" data-template="woffice">

	<div class="intern-padding clearfix" data-template="woffice">

		<?php bp_nouveau_before_groups_directory_content(); ?>

		<?php bp_nouveau_template_notices(); ?>

        <?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

		<div class="screen-content">

			<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

			<div id="groups-dir-list" class="groups dir-list" data-bp-list="groups">
				<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-groups-loading' ); ?></div>
			</div>

			<?php bp_nouveau_after_groups_directory_content(); ?>

		</div>


	</div>

</div>