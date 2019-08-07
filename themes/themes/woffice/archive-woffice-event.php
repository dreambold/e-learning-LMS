<?php
    /**
     * Template Name: Calendar Events
     */
    
    get_header();
?>
    
    <div id="left-content">
        <?php
            woffice_title(get_the_title()); ?>
        
        <!-- START THE CONTENT CONTAINER -->
        <div id="content-container">
            
            <!-- START CONTENT -->
            <div id="content" class="woffice-calendar archive-calendar">
                <?php if (woffice_is_user_allowed()) {
                    // CUSTOM CLASSES ADDED BY THE THEME
                    $post_classes = array('box','content');
                ?>
                <div id="events-page-content" <?php post_class(); ?>>
                    
                    <!-- LOOP ALL THE EVENTS-->
                    <?php // GET EVENT POSTS OLDER THAN TODAY
                        if (fw_ext('woffice-event')) {
                            $pagination_slug = (is_front_page()) ? 'page' : 'paged';
                            $paged = (get_query_var($pagination_slug)) ? get_query_var($pagination_slug) : 1;
                            $args = array(
                                'post_type'      => 'woffice-event',
                                'paged'          => $paged,
                                'posts_per_page' => 10,
                                'meta_key'       => 'fw_option:woffice_event_date_end',
                                'orderby'        => 'meta_value',
                                'order'          => 'DESC',
                                'meta_query'     => array(
                                    'key'     => 'fw_option:woffice_event_date_end',
                                    'value'   => date('Y-m-d 00:00:00'),
                                    'type'    => 'DATE',
                                    'compare' => '<'
                                )
                            );
                            
                            /**
                             * Filter `woffice_archive_events_query_args`
                             *
                             * Add the ability to override the archive event query
                             *
                             * @param array $args - current archive event query arguments
                             *
                             * @return $array - archive event query arguments
                             */
                            $args = apply_filters('woffice_archive_events_query_args', $args);
                            
                            $query = new WP_Query($args);
                            if ($query->have_posts()) :
                                
                                echo'<ul id="event-list" class="event-list wo-date-events pt-4">';
                                // LOOP
                                while($query->have_posts()) : $query->the_post();
                                    get_template_part('template-parts/content', 'event-loop');
                                endwhile;
                                echo '</ul>';
                            else :
                                get_template_part('content','none' );
                            endif;
                            wp_reset_postdata();
                        }?>
                    
                    <?php
                        } else {
                        get_template_part('content', 'private');
                    }
                    ?>
                </div>
                <?php woffice_paging_nav($query); ?>
            </div>
        
        </div><!-- END #content-container -->
        
        <?php woffice_scroll_top(); ?>
    
    </div><!-- END #left-content -->
    

<?php
    get_footer();
