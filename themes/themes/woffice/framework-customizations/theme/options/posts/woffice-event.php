<?php if (!defined('FW')) {
    die('Forbidden');
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */
$user_id        = get_current_user_id();
$group_options = [];
if (woffice_bp_is_active('groups')) {
    $groups_query = BP_Groups_Group::get(array('show_hidden' => true));
    foreach ($groups_query['groups'] as $group) {
        if (groups_is_user_member($user_id, $group->id)) {
            $group_options['group_' . $group->id] = $group->name;
        }
    }
}
$args = array(
    'post_type' => 'project',
    'posts_per_page' => '-1',
);

$user_posts = get_posts($args);
$project_options = array();
foreach ($user_posts as $project ) {
    if ($user_id && $user_id !== (int)$project->post_author) {
        $project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($project->ID, 'project_members') : '';

        if (!empty($project_members) && !in_array($user_id, $project_members)) {
            continue;
        }
    }

    $project_options['project_'. $project->ID] = $project->post_title;
}

$repeat_options = array();
foreach (array('No', 'Daily', 'Weekly', 'Monthly', 'Yearly') as $opt) {
    $repeat_options[$opt] = $opt;
}

$color_options = array();
foreach (array('default', 'blue', 'orange', 'red', 'green', 'grey') as $opt) {
    $color_options[$opt] = $opt;
}

$options = array(
    'event-box' => array(
        'title'   => __('Event settings ', 'woffice'),
        'type'    => 'box',
        'options' => array(
            'woffice_event_title'       => array(
                'type'       => 'text',
                'label'      => __('Title', 'woffice'),
                'desc'       => __('Set event name.', 'woffice'),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_title',
                )
            ),
            'woffice_event_date_start'  => array(
                'type'       => 'datetime-picker',
                'label'      => __('Event Starting Date', 'woffice'),
                'desc'       => __('Will be used to display this event in the calendar.', 'woffice'),
                'min-date'   => date('1-0-2000'),
                'datetime-picker' => array(
                    'format'        => 'Y-m-d H:i',
                    'minDate'       => 0,
                ),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_date_start',
                )
            ),
            'woffice_event_date_end'    => array(
                'type'       => 'datetime-picker',
                'label'      => __('Event Ending Date', 'woffice'),
                'desc'       => __('Will be used to display this event in the calendar.', 'woffice'),
                'datetime-picker' => array(
                    'format'        => 'Y-m-d H:i',
                    'minDate'       => 0,
                ),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_date_end',
                )
            ),
            'woffice_event_repeat'      => array(
                'type'       => 'select',
                'label'      => __('Repeat', 'woffice'),
                'desc'       => __('Select repeat type, Choose no if not repeatable event.', 'woffice'),
                'choices'    => $repeat_options,
                'value'      => 'No',
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_repeat',
                )
            ),
            'woffice_event_color'       => array(
                'type'       => 'select',
                'label'      => __('Event Color', 'woffice'),
                'desc'       => __('Event color in calendar.', 'woffice'),
                'choices'    => $color_options,
                'value'      => 'default',
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_color',
                )
            ),
            'woffice_event_visibility'  => array(
                'type'       => 'select',
                'label'      => __('Event Visibility', 'woffice'),
                'desc'       => __('Set Event Visibility.', 'woffice'),
                'choices'    => array(
                    'general' => __('General', 'woffice'),
                    'personal' => __('Personal', 'woffice'),
                    array(
                        'attr' => array('label' => __('Project', 'woffice')),
                        'choices' => $project_options
                    ),
                    array(
                        'attr' => array('label' => __('Group', 'woffice')),
                        'choices' => $group_options
                    )
                ),
                'value'      => 'general',
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_visibility',
                )
            ),
            'woffice_event_description' => array(
                'type'       => 'textarea',
                'label'      => __('Description', 'woffice'),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_description',
                )
            ),
            'woffice_event_location'    => array(
                'type'       => 'text',
                'label'      => __('Location', 'woffice'),
                'desc'       => __('Location of the event.', 'woffice'),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_location',
                )
            ),
            'woffice_event_link'        => array(
                'type'       => 'text',
                'label'      => __('Event URL', 'woffice'),
                'desc'       => __('URL of the event.', 'woffice'),
                'fw-storage' => array(
                    'type'      => 'post-meta',
                    'post-meta' => 'fw_option:woffice_event_link',
                )
            ),
        )
    ),
);
