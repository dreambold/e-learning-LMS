<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;
?>
	<!-- WIDGET -->
	<ul class="list-styled list-events">
		<?php
		if( defined( 'DP_PRO_EVENT_CALENDAR_VER' ) ) {
			// DP PRO Event Calendar
			$args = array(
				'post_type' => 'pec-events',
				'showposts' => - 1,
				'meta_key'  => 'pec_date',
				'orderby'   => 'meta_value_num',
				'order'     => 'ASC',
			);
		} else {
			// EventON args
			$args = array(
				'post_type' => 'ajde_events',
				'showposts' => - 1,
				'meta_key'  => 'evcal_srow',
				'orderby'   => 'meta_value_num',
				'order'     => 'ASC',
			);
		}

        if(!empty($categories_included) || !(empty($categories_excluded))) {
            $taxonomy = (defined( 'DP_PRO_EVENT_CALENDAR_VER' )) ? 'pec_events_category' : 'event_type';
            if(empty($categories_included))
                $args['tax_query'] =  array(
                    array(
                        'taxonomy' => 'event_type',
                        'field' => 'term_id',
                        'terms' => $categories_excluded,
                        'operator' => 'NOT IN'
                    )
                );
            else
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'event_type',
                        'field' => 'term_id',
                        'terms' => $categories_included
                    ),
                    array(
                        'taxonomy' => 'event_type',
                        'field' => 'term_id',
                        'terms' => $categories_excluded,
                        'operator' => 'NOT IN'
                    )
                );
        }

        $widget_eventon_query = new WP_Query($args);

        $loop_number = 0;

        if ( $widget_eventon_query->have_posts() ) {
            $events = array();
            while($widget_eventon_query->have_posts() && $loop_number <= $show) : $widget_eventon_query->the_post();
                //Get start date
                $meta_key = (defined( 'DP_PRO_EVENT_CALENDAR_VER' )) ? 'pec_date' : 'evcal_srow';
                $start_date_meta_option = get_post_meta(get_the_ID(), $meta_key);

                //Check if the event is recurring
                $repeat_intervals = get_post_meta(get_the_ID(), 'repeat_intervals');

                //If the event is recurring, then check the next closer date
                if(!empty($repeat_intervals) && !defined( 'DP_PRO_EVENT_CALENDAR_VER' ) ) {
                    $repeat_intervals = array_reverse($repeat_intervals[0]);
                    $available = false;
                    $i = 0;
                    while($repeat_intervals[$i][0] > time()) {

                        //Avoid undefined index
                        if(!array_key_exists($i, $repeat_intervals))
                            continue;

                        $available = $repeat_intervals[$i][0];
                        $i++;
                    }

                    if(!empty($available))
                        $start_date_meta_option[0] = $available;
                }

                if( defined( 'DP_PRO_EVENT_CALENDAR_VER' ) )
                    $start_date_timestamp = strtotime($start_date_meta_option[0]);
                else
                    $start_date_timestamp = $start_date_meta_option[0];

                //Check if start date is a past date
                if($start_date_timestamp > current_time( 'timestamp' ) ) {

                    $events[$start_date_timestamp] = '<li>';

                    $events[$start_date_timestamp] .= '<a href="'. get_the_permalink() .'" rel="bookmark">';

                    $events[$start_date_timestamp] .=  get_the_title();

                    $events[$start_date_timestamp] .=  '</a>';

                    $events[$start_date_timestamp] .= ' - ' . date_i18n('d M', $start_date_timestamp) . '</li>';

                    $loop_number++;

                }

            endwhile;


            if(empty($events)) {
                _e("No events found...","woffice");
            } else {
                //Sort the events, this is necessary cause the recurring events
                ksort($events);
                foreach($events as $event)
                    echo $event;
            }

        }
        else{
            _e("No events found...","woffice");
        }
        wp_reset_postdata();
		?>
	</ul>
<?php echo $after_widget; ?>