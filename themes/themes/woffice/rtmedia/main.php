<?php
/**
 * BuddyPress - Rtmedia template
 *
 * @since   1.0.0
 * @version 3.0.0
 */
// by default it is not an ajax request
global $rt_ajax_request ;
$rt_ajax_request = false ;

// check if it is an ajax request

$_rt_ajax_request = rtm_get_server_var( 'HTTP_X_REQUESTED_WITH', 'FILTER_SANITIZE_STRING' );
if ( 'xmlhttprequest' === strtolower( $_rt_ajax_request ) ) {
	$rt_ajax_request = true;
}

$profile_layout = woffice_get_settings_option('profile_layout');
$profile_layout = (isset($_GET['profile_layout'])) ? $_GET['profile_layout'] : $profile_layout;
?>

<div id="buddypress" class="buddypress-wrap bp-dir-hori-nav rtmedia">

    <?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

    <div class="bp-wrap row woffice-profile--<?php echo $profile_layout; ?>" data-template="woffice">
        <?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

            <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-4' : 'col-md-12'; ?>" data-template="woffice">
                <div id="woffice-bp-sidebar" data-template="woffice">
                    <div id="item-header" role="complementary" data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>" data-bp-item-component="members" class="users-header single-headers">

                        <?php if (bp_displayed_user_id ()) : ?>
                            <?php bp_nouveau_member_header_template_part(); ?>
                        <?php elseif (bp_is_group()) : ?>
                            <?php bp_nouveau_group_header_template_part(); ?>
                        <?php endif; ?>

                    </div><!-- #item-header -->

                    <?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>
                </div>
            </div>

        <?php endif; ?>

        <div class="<?php echo ($profile_layout === 'vertical') ? 'col-md-8' : 'col-md-12'; ?>" data-template="woffice">
            <div id="item-body" class="item-body">

                <?php do_action ( 'bp_before_member_body' ) ; ?>
                <?php do_action ( 'bp_before_member_media' ) ; ?>

                <div class="bp-navs bp-subnavs no-ajax user-subnav" id="subnav">
                    <ul class="subnav">

                        <?php rtmedia_sub_nav () ; ?>
                        <?php do_action ( 'rtmedia_sub_nav' ) ; ?>

                    </ul>
                </div><!-- .item-list-tabs -->

                <div class="profile">

                    <?php
                    rtmedia_load_template () ;

                    if (!$rt_ajax_request ) {
                        if (function_exists( "bp_displayed_user_id" ) && (bp_displayed_user_id() || bp_is_group())) {
                            if (bp_is_group()) {
                                do_action('bp_after_group_media');
                                do_action('bp_after_group_body');
                            }
                            if (bp_displayed_user_id()) {
                                do_action('bp_after_member_media');
                                do_action('bp_after_member_body');
                            }
                        }
                    }
                    ?>
                </div>

            </div><!-- #item-body -->
        </div>

    </div><!-- // .bp-wrap -->

    <?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>

</div>