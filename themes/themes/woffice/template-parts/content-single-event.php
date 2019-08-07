<?php

global $process_result;
global $post;

$edit_allowed = Woffice_Frontend::edit_allowed('woffice-event') == true;
$post_link = get_edit_post_link($post->ID);

$start_date = esc_html(woffice_get_post_option($post->ID, 'woffice_event_date_start'));
$end_date = esc_html(woffice_get_post_option($post->ID, 'woffice_event_date_end'));

$edit_object = (object) array(
	'woffice_event_title'           => woffice_get_post_option($post->ID, 'woffice_event_title'),
    'woffice_event_date_start'      => $start_date,
    'woffice_event_date_start_i18n' => date_i18n(get_option(
		                                 'date_format'),
                                         strtotime($start_date)
                                       ) . ', ' . date('h:i A', strtotime($start_date)),
    'woffice_event_date_end'        => woffice_get_post_option($post->ID, 'woffice_event_date_end'),
	'woffice_event_date_end_i18n'   => date_i18n(get_option(
	                                     'date_format'),
                                          strtotime($end_date)
                                       ) . ', ' . date('h:i A', strtotime($end_date)),
    'woffice_event_repeat'          => woffice_get_post_option($post->ID, 'woffice_event_repeat'),
    'woffice_event_color'           => woffice_get_post_option($post->ID, 'woffice_event_color'),
    'woffice_event_visibility'      => woffice_get_post_option($post->ID, 'woffice_event_visibility'),
    'woffice_event_description'     => woffice_get_post_option($post->ID, 'woffice_event_description'),
    'woffice_event_location'        => woffice_get_post_option($post->ID, 'woffice_event_location'),
    'woffice_event_image'           => '',
    'woffice_event_image_name'      => '',
    'woffice_event_link'            => woffice_get_post_option($post->ID, 'woffice_event_link'),
    'woffice_event_post_id'         => get_the_ID()
);

$post_classes = array('box', 'content');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <!-- THUMBNAIL IMAGE -->
        <?php Woffice_Frontend::render_featured_image_single_post($post->ID) ?>

    <?php endif; ?>
    <div id="event-nav" class="intern-box">
        <div class="item-list-tabs-wiki">
            <ul>
                <li id="event-tab-view" class="active">
                    <a href="javascript:void(0)" class="fa-file"><?php _e("View", "woffice"); ?></a>
                </li>
                <?php if ($edit_allowed) { ?>
                    <li id="event-tab-edit">
                        <a href="<?php echo $post_link ?>" class="fa-edit"><?php _e("Edit", "woffice"); ?></a>
                    </li>
                <?php } ?>

                <?php
                if (Woffice_Frontend::edit_allowed('woffice-event', 'delete')) :
                    ?>
                    <li id="event-tab-delete">
                        <a onclick="return confirm('<?php echo __('Are you sure you wish to delete article :',
                                'woffice') . ' ' . get_the_title(); ?> ?')"
                           href="<?php echo get_site_url() . wp_nonce_url('/wp-admin/post.php?action=trash&amp;post=' . get_the_ID(),
                                   'trash-post_' . get_the_ID()); ?>" class="fa-trash">
                            <?php _e("Delete", "woffice"); ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <!-- DISPLAY ALL THE CONTENT OF THE project ARTICLE-->
    <div id="event-content-view">
        <div id="event-view">
            <calendar-single-view :event='<?php echo json_encode($edit_object); ?>'></calendar-single-view>
        </div>
    </div>

</article>
