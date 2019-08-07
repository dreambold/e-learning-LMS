<?php
/**
 * BuddyPress - Members Home
 *
 * @since   1.0.0
 * @version 3.0.0
 */

$profile_layout = woffice_get_settings_option('profile_layout');
$profile_layout = (isset($_GET['profile_layout'])) ? $_GET['profile_layout'] : $profile_layout;
?>

	<?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

	<div class="bp-wrap row woffice-profile--<?php echo $profile_layout; ?>" data-template="woffice">

        <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-4' : 'col-md-12'; ?>" data-template="woffice">
            <div id="woffice-bp-sidebar" data-template="woffice">
                <div id="item-header" role="complementary" data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>" data-bp-item-component="members" class="users-header single-headers">

                    <?php bp_nouveau_member_header_template_part(); ?>

                </div><!-- #item-header -->

                <?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>
            </div>
        </div>

        <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-8' : 'col-md-12'; ?>" data-template="woffice">
            <div id="item-body" class="item-body">

		        <?php bp_nouveau_member_template_part(); ?>

            </div><!-- #item-body -->
        </div>

	</div><!-- // .bp-wrap -->

	<?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>
