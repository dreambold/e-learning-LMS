<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/* Roles array ready for options */
global $wp_roles;
$tt_roles = array();
foreach ($wp_roles->roles as $key=>$value){
$tt_roles[$key] = $value['name']; }
$tt_roles_tmp = array('nope' => __("No one","woffice")) + $tt_roles;
/* End */

/**
 * Filter to customize the default posts options
 *
 * @param array
 */
$options = apply_filters('woffice_options_posts', array(
	'wiki' => array(
		'title'   => __( 'Posts/Wiki/Projects', 'woffice' ),
		'type'    => 'tab',
		'options' => array(
			'wiki-box' => array(
				'title'   => __( 'Wiki Options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
                    'enable_wiki_like'    => array(
						'label' => __( 'Display like button', 'woffice' ),
						'desc'  => __( 'Do you want display wiki button and counter for wiki elements?', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'yep',
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'nope',
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => 'yep',
					),
                    'enable_wiki_accordion'    => array(
                        'label' => __( 'Enable collapsing of sub categories', 'woffice' ),
                        'desc'  => __( 'Do you want enable an accordion for subcategories of wiki? (they will be closed by default)', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                    'wiki_sortbylike'    => array(
                        'label' => __( 'Enable Sorting of wiki by likes', 'woffice' ),
                        'desc'  => __( 'Do you want add a button to wiki list that allow to sort the result by likes?', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                    'wiki_excluded_categories'    => array(
	                    'label' => __( 'Wiki Exclude categories', 'woffice' ),
	                    'desc'  => __( 'Do you want to exclude categories from the Wiki page ?', 'woffice' ),
	                    'type'         => 'multi-select',
	                    'population' => 'taxonomy',
	                    'source' => 'wiki-category',
                    ),
				),
			),
			'projects-box' => array(
				'title'   => __( 'Projects Options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'projects_layout'    => array(
						'label' => __( 'Projects layout', 'woffice' ),
						'desc'  => __( 'This is the layout for the projects directory', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'classic',
							'label' => __( 'Classic', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'masonry',
							'label' => __( 'Masonry', 'woffice' )
						),
						'value'        => 'classic',
					),
					'projects_masonry_columns' => array(
						'label' => __( 'Number of columns in the masonry layout', 'woffice' ),
						'type'  => 'select',
						'value' => '3',
						'desc' => __('This is only for non-mobiles devices, because it is responsive.','woffice'),
						'choices' => array(
							'1' => __( '1 Columns', 'woffice' ),
							'2' => __( '2 Columns', 'woffice' ),
							'3' => __( '3 Columns', 'woffice' )
						)
					),
					'projects_filter'    => array(
						'label' => __( 'Enable category filter', 'woffice' ),
						'desc'  => __( 'This is a dropdown button to filter projects by category', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => true,
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => false,
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => false,
					),
					'projects_date_filter'    => array(
						'label' => __( 'Enable date sorting', 'woffice' ),
						'desc'  => __( 'This is a dropdown button to sort the projects by publication date', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
                            'value' => true,
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
                            'value' => false,
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => true,
					),
					'projects_status_filter'    => array(
						'label' => __( 'Enable the status filter', 'woffice' ),
						'desc'  => __( 'This is a toggle button to show/hide the projects based on its status.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
                            'value' => true,
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
                            'value' => false,
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => true,
					),
					'hide_projects_completed'    => array(
						'label' => __( 'Hide archived projects by default', 'woffice' ),
						'desc'  => __( 'If the archived projects have to be displayed in the listing or not by default.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => true,
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => false,
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => true,
					),
					'projects_groups'    => array(
						'label' => __( 'Include in Buddypress Groups ?', 'woffice' ),
						'desc'  => __( 'A new project category will be created for each Buddypress group and all members set as members of the project.', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'yep',
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'nope',
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => 'nope',
					),
					'projects_assigned_email'    => array(
						'label' => __( 'Notice user on task assignment ?', 'woffice' ),
						'desc'  => __( 'Do you want to notice the user by email when a project task is assigned to the user ?', 'woffice' ),
						'type'         => 'switch',
						'right-choice' => array(
							'value' => 'yep',
							'label' => __( 'Yep', 'woffice' )
						),
						'left-choice'  => array(
							'value' => 'nope',
							'label' => __( 'Nope', 'woffice' )
						),
						'value'        => 'nope',
					),
					'projects_assigned_email_content'    => array(
						'label' => __( 'Email\'s content', 'woffice' ),
						'desc'  => __( 'This is the content of the email before the task name. Dynamic variables that will be replaced automatically: {user_name}, {project_url}, {project_title}, {todo_title}', 'woffice' ),
						'type'         => 'textarea',
						'value'        => 'Hey {user_name} You have a new task: {todo_title} in this project: {project_url}',
					),
				),
			),
			'blog-box' => array(
				'title'   => __( 'Blog/Pages Options', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'page_comments'    => array(
						'label' => __( 'Show comments on pages', 'woffice' ),
						'desc'  => __( 'Do you want to display the comments to allow user to comment on pages ? If it you choose "show" you will still be able to override it on every page.', 'woffice' ),
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
				),
			),
            'learndash-box' => array(
                'title'   => __( 'LearnDash Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'hide_learndash_meta'    => array(
                        'label' => __( 'Hide meta below LearnDash pages', 'woffice' ),
                        'desc'  => __( 'Meta below LearnDash pages contains: author, date, category, comments', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'nope',
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'yep',
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'value'        => 'nope',
                    ),
                ),
            ),
            'posts-other-box' => array(
                'title'   => __( 'Other Options', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'like_engine'    => array(
                        'label' => __( 'How likes are saved ?', 'woffice' ),
                        'desc'  => __( 'If you choose to do it by users, you need Buddypress. It\'s for both the blog and the wiki.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'ips',
                            'label' => __( 'IPs', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'members',
                            'label' => __( 'Members IDs', 'woffice' )
                        ),
                        'value'        => 'ips',
                    ),
                ),
            ),
		)
	)
));