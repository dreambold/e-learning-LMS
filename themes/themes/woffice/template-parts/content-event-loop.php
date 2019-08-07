<?php
    $start_date = esc_html(woffice_get_post_option(get_the_ID(), 'woffice_event_date_start'));
    $end_date   = esc_html(woffice_get_post_option(get_the_ID(), 'woffice_event_date_end'));
    
    $event = array(
        'woffice_event_title'           => woffice_get_post_option(get_the_ID(), 'woffice_event_title'),
        'woffice_event_date_start_i18n' => date_i18n(get_option('date_format'), strtotime($start_date)) . ', ' . date('h:i A', strtotime($start_date)),
        'woffice_event_date_end_i18n'   => date_i18n(get_option('date_format'), strtotime($end_date)) . ', ' . date('h:i A', strtotime($end_date)),
        'woffice_event_repeat'          => woffice_get_post_option(get_the_ID(), 'woffice_event_repeat'),
        'woffice_event_color'           => woffice_get_post_option(get_the_ID(), 'woffice_event_color'),
        'woffice_event_visibility'      => woffice_get_post_option(get_the_ID(), 'woffice_event_visibility')
    );


if (woffice_is_user_visible_event($event['woffice_event_visibility'])) : ?>
    <div class="event-box font-weight-normal text-white bg-<?php echo $event['woffice_event_color'];?>">
        <a class="text-white" target="_blank" href="<?php echo get_post_permalink(get_the_ID());?>">
        <span class="name pb-1 font-weight-bold"><?php echo $event['woffice_event_title'];?></span>
        
        <span class="pr-3 date-time">
            <i class="text-light fa fa-calendar-alt pr-1"></i>
                <?php echo $event['woffice_event_date_start_i18n'] . ' - '.  $event['woffice_event_date_end_i18n'];?>
        </span>
        <span class="pr-3 date-time">
            <i class="text-light fa fa-sync pr-1"></i>
            <?php echo $event['woffice_event_repeat'];?>
        </span>
        <span class="pr-3 date-time">
            <i class="text-light fa fa-user-shield pr-1"></i>
            <?php echo ucfirst(explode('_', $event['woffice_event_visibility'])[0]);?>
        </span>
        </a>
    </div>
<?php
endif;
