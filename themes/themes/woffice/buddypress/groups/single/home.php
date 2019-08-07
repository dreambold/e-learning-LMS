<?php
/**
 * BuddyPress - Groups Home
 *
 * @since 3.0.0
 * @version 3.0.0
 */

$profile_layout = woffice_get_settings_option('profile_layout');
$profile_layout = (isset($_GET['profile_layout'])) ? $_GET['profile_layout'] : $profile_layout;

if ( bp_has_groups() ) :
	while ( bp_groups() ) :
		bp_the_group();
		?>

    <?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

    <div class="bp-wrap row woffice-profile--<?php echo $profile_layout; ?>" data-template="woffice">
        <?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

            <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-4' : 'col-md-12'; ?>" data-template="woffice">
                <div id="woffice-bp-sidebar" data-template="woffice">
                    <div id="item-header" role="complementary" data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups" class="groups-header single-headers">

                        <?php bp_nouveau_group_header_template_part(); ?>

                    </div><!-- #item-header -->

                    <?php bp_get_template_part( 'groups/single/parts/item-nav' ); ?>
                </div>
            </div>

        <?php endif; ?>

        <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-8' : 'col-md-12'; ?>" data-template="woffice">
            <div id="item-body" class="item-body">

                <?php bp_nouveau_group_template_part(); ?>

            </div><!-- #item-body -->
        </div>

    </div><!-- // .bp-wrap -->

    <?php bp_nouveau_group_hook( 'after', 'home_content' ); ?>

	<?php endwhile; ?>

<?php
endif;
