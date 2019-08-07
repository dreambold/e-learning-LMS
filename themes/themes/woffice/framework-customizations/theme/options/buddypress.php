<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/* Roles array ready for options */
global $wp_roles;
$tt_roles = array();
foreach ($wp_roles->roles as $key=>$value){
	$tt_roles[$key] = $value['name'];
}
$tt_roles_tmp = $tt_roles;

if (woffice_bp_is_active( 'xprofile' )) {
	
	$fields_options = array();
	
	// We fetch all the BuddyPress fields :
	global $wpdb;
    $table_name = woffice_get_xprofile_table('fields');
            
	$sqlStr = "SELECT id, name, parent_id, type type FROM ".$table_name;
	$fields = $wpdb->get_results($sqlStr);
    $matching_field_options = [];
    $multi_types = ['checkbox', 'selectbox', 'multiselectbox', 'radio'];

	$matching_field_options[0] = 'N/A';

	if(count($fields) > 0) {
	
		foreach ($fields as $field) {
			if($field->parent_id != 0)
				continue;

			$field_name = $field->name;

			if (in_array($field->type, $multi_types)) {
                $matching_field_options[$field->id] = $field_name;
            }
			$fields_options["buddypress_".$field_name] = array(
				'type' => 'group',
				'title' => $field_name,
				'options' => array()
			);

			$fields_options["buddypress_".$field_name]['options']['buddypress_'.$field_name.'_display'] = array(
				'type'  => 'checkbox',
				'label' => __('Show','woffice'). ' ' .$field_name. '?',
				'value' => false,
				'desc' => __('If checked the field will be displayed on the members page.','woffice'),
			);

			if($field->type != 'datebox') {
				$fields_options[ "buddypress_" . $field_name ]['options'][ 'buddypress_'.$field_name.'_add_to_search' ] = array(
					'type'  => 'checkbox',
					'label' => sprintf(__('Add %s to advanced search?','woffice'), $field_name),
					'value' => false,
					'desc' => __('If checked the field will be aded to the advanced search form in the members page.','woffice'),
				);
			}

			$fields_options["buddypress_".$field_name]['options']['buddypress_'.$field_name.'_icon'] = array(
				'type'  => 'icon',
				'value' => null,
				'label' => __('Field\'s icon','woffice'),
			);

		}	
	
	}
} 
else {
	$fields_options = array();
}

array_unshift($fields_options, array('buddypress_wordpress_email' => array(
    'type' => 'group',
    'title' => 'wordpress_email',
    'options' => array(
        'buddypress_wordpress_email_display' => array(
            'type'  => 'checkbox',
            'label' => __('Show the WordPress email','woffice'),
            'value' => false,
            'desc' => __('If checked the standard WordPress email (the email used to login) will be displayed on the members page.','woffice'),
        ),
        'buddypress_wordpress_email_add_to_search' => array(
	        'type'  => 'checkbox',
	        'label' => __('Add WordPress email to advanced search?','woffice'),
	        'value' => false,
	        'desc' => __('If checked the field will be aded to the advanced search form in the members page.','woffice'),
        ),
        'buddypress_wordpress_email_icon' => array(
            'type'  => 'icon',
            'value' => null,
            'label' => __('Field\'s icon','woffice'),
        )
    )
    )
));

$options = array(
	'buddypress' => array(
		'title'   => __( 'BuddyPress', 'woffice' ),
		'type'    => 'tab',
		'options' => array(
			'buddy-box' => array(
				'title'   => __( 'Main options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'profile_layout'    => array(
						'label' => __( 'Profile/Group layout', 'woffice' ),
						'desc'  => __( 'Choose the profile layout whether there is a left sidebar and the content on the right side or the sidebar is displayed horizontally.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'horizontal',
							'label' => __( 'Horizontal', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'vertical',
							'label' => __( 'Vertical', 'woffice' )
						),
						'value'        => 'vertical',
					),
					'buddy_new_roles' => array(
						'label' => __('New Member Types', 'woffice'),
					    'type'  => 'html',
						'html'  => __('This option is not longer available within Woffice Theme Settings, now you need to use this external plugin to manage user roles: ', 'woffice'). '<a href="https://wordpress.org/plugins/user-role-editor/" target="_blank">User Role Editor</a>.',
					),
					'buddy_calendar'    => array(
						'label' => __( 'Personal Calendar', 'woffice' ),
						'desc'  => __( 'Show the personal calendar tab on user\'s profile.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'show',
							'label' => __( 'Show', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'hide',
							'label' => __( 'Hide', 'woffice' )
						),
						'value'        => 'hide',
					),
					'buddy_notes'    => array(
						'label' => __( 'Personal Note', 'woffice' ),
						'desc'  => __( 'Show the personal note tab on user\'s profile.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'show',
							'label' => __( 'Show', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'hide',
							'label' => __( 'Hide', 'woffice' )
						),
						'value'        => 'hide',
					),
                    'buddy_social'    => array(
                        'label' => __( 'Social Fields', 'woffice' ),
                        'desc'  => __( 'Show the social fields in the profile.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'show',
                            'label' => __( 'Show', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'hide',
                            'label' => __( 'Hide', 'woffice' )
                        ),
                        'value'        => 'show',
                    ),
					'buddy_directory_name'    => array(
						'label' => __( 'User member title', 'woffice' ),
						'desc'  => __( 'This is what will be displayed for each user in the members directory.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'name',
							'label' => __( 'First & Last Names', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'username',
							'label' => __( 'Username', 'woffice' )
						),
						'value'        => 'username',
					),
					'buddy_filter'    => array(
						'label' => __( 'Roles filter', 'woffice' ),
						'desc'  => __( 'Show the dropdown filter on the BuddyPress members directory.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'show',
							'label' => __( 'Show', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'hide',
							'label' => __( 'Hide', 'woffice' )
						),
						'value'        => 'show',
					),
					'buddy_advanced_search'    => array(
						'label' => __( 'Members advanced search', 'woffice' ),
						'desc'  => __( 'Show the extra fields to filter members (Fields have to be enabled below).', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => true,
							'label' => __( 'Show', 'woffice' )
						),
						'left-choice'  => array(
							'value' => false,
							'label' => __( 'Hide', 'woffice' )
						),
						'value'        =>  false,
					),
					'buddy_sort_members_by'    => array(
						'label' => __( 'Sort Members by', 'woffice' ),
						'type'         => 'select',
						'choices'      => array(
							'active' => esc_html__('Activity', 'woffice'),
							'alphabetical' => esc_html__('Alphabetically', 'woffice'),
							'newest' => esc_html__('Newest', 'woffice'),
							'online' => esc_html__('Online', 'woffice'),
							'popular' => esc_html__('Popular', 'woffice'),
							'random' => esc_html__('Random', 'woffice'),
						),
						'value' => 'active'
					),
					'buddy_members_layout'    => array(
						'label' => __( 'Members layout', 'woffice' ),
						'desc'  => __( 'Layout of your members directory.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'cards',
							'label' => __( 'Cards', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'table',
							'label' => __( 'Table', 'woffice' )
						),
						'value'        => 'cards',
					),
					'buddypress_members_per_page' => array(
						'label' => __( 'Members per page', 'woffice' ),
						'type'         => 'text',
						'value' => '12',
					),
					'buddy_excluded_directory'    => array(
						'label' => __( 'Members Excluded (Directory)', 'woffice' ),
						'desc'  => __( 'Do you want to exclude a role from the Members directory, they won\'t be displayed on the page.', 'woffice' ),
						'type'         => 'multi-select',
						'choices'      => $tt_roles_tmp
					),
                    'buddy_directory_autolink'    => array(
                        'label' => __( 'Members Directory autolinks', 'woffice' ),
                        'desc'  => __( 'Auto search for BuddyPress fields displayed in the directory.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yup',
                            'label' => __( 'Yup', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                    'member_matching_fields'    => array(
                        'label'        => __( 'Member Matching Field', 'woffice' ),
                        'desc'         => __('If a field is selected, the current member will only see members having the same choice. Example, if there are two choices: A & B. If the current members select A, he\'ll see members having selected A as well. Otherwise A & B.', 'woffice'),
                        'type'         => 'select',
                        'choices'      => $matching_field_options
                    ),
				),
			),
			'buddy_fields' => array(
				'title'   => __( 'Display fields in BuddyPress members directory', 'woffice' ),
				'type'  => 'box',
				'options' => $fields_options
			),
			'tab-creator-box' => array(
				'title'   => __( 'BuddyPress tab creator', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'buddypress-tabs' => array(
						'type' => 'addable-popup',
						'popup-title' => null,
						'size' => 'small',
						'limit' => 0,
						'add-button-text' => __('Add', 'woffice'),
						'label' => __('Tabs', 'woffice'),
						'sortable' => true,
						'template' => 'Tab: {{- name }}',
						'popup-options' => array(
							'name' => array(
								'label' => __('Name', 'woffice'),
								'type' => 'text',
							),
							'content' => array(
								'type'  => 'wp-editor',
								'label' => __( 'Content', 'woffice' ),
								'media_buttons' => false,
								'teeny' => false,
								'wpautop' => false,
								'editor_css' => '',
								'reinit' => false,
							),
							'icon' => array(
								'label' => __('Icon', 'woffice'),
								'type' => 'icon',
							),
							'action' => array(
								'label' => __('PHP Action', 'woffice'),
								'type' => 'text',
								'value' => 'woffice_bp_tab_',
								'desc' =>
									__('Like: <b>woffice_bp_tab_my_tab</b>. Must be unique. This advanced option let you attach any PHP function to the tab content by calling:','woffice').
									' <span class="highlight">add_action("woffice_bp_tab_my_tab", "your_callback_function")</span> '.
									__('Please see the official WordPress documentation for more details here:', 'woffice').
									' <a href="https://developer.wordpress.org/reference/functions/add_action/" target="_blank">developer.wordpress.org/reference/functions/add_action/</a>'
							),
						),
					)
				)
			),
		)
	)
);
