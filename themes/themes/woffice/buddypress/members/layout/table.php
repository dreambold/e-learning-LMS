<?php
/**
 * Woffice custom member template
 *
 * @since 2.8.0
 */

/**
 * You can hide the role of users displayed in the members loop page
 *
 * @param bool
 */
$members_role_enabled = apply_filters('woffice_enable_member_role_on_members_page', true);

/**
 * You can hide the last activity of users displayed in the members loop page
 *
 * @param bool
 */
$last_activity_enabled = apply_filters('woffice_enable_member_last_activity_on_members_page', true);

/*
 * We build the table data
 */
$social_fields_available = woffice_get_social_fields_available();
$fields_values = array();

if (bp_is_active( 'xprofile' )) {
	$groups = bp_xprofile_get_groups( array(
		'user_id'                => 0,
		'hide_empty_groups'      => true,
		'hide_empty_fields'      => true,
		'fetch_fields'           => true,
	) );

	foreach ( (array) $groups as $group ) {
		if ( empty( $group->fields ) ) {
			continue;
		}

		foreach ( (array) $group->fields as $field ) {

			$fields_values[ $field->name ] = array(
				'field_id'         => $field->id,
				'field_type'       => $field->type,
			);
		}
	}

	//Add wordpress email to the array of fields fields
	$wordpress_email_field               = array();
	$wordpress_email_field['field_id']   = null;
	$wordpress_email_field['name']       = 'wordpress_email';
	$wordpress_email_field['field_type'] = 'email';
	$wordpress_email_field_label         = esc_html_x('Email', 'Label of the WordPress email field', 'woffice');

	$fields_values = array('wordpress_email' => $wordpress_email_field) + $fields_values;
}
?>
<div class="table-responsive">
	<table id="members-list-table" class="members table table-hover table-striped">
		<thead>
		<th><?php _e('Name', 'woffice'); ?></th>
		<?php if( $members_role_enabled ): ?>
			<th><?php _e('Role', 'woffice'); ?></th>
		<?php endif; ?>
		<?php if( $last_activity_enabled ): ?>
			<th><?php _e('Activity', 'woffice'); ?></th>
		<?php endif; ?>
		<?php
		foreach ($fields_values as $field_name => &$field) {

			if ($field_name == 'user_login' || $field_name == 'user_nicename' || $field_name == 'user_email')
				continue;

			// Skip displayname used by buddypress
			if ($field['field_id'] == 1 && !apply_filters('woffice_include_display_name_in_members_loop_fields', false))
				continue;

			$field_type =  $field['field_type'];
			$field['field_show'] = (bool)woffice_get_settings_option('buddypress_' . $field_name . '_display');
			$field['field_icon'] = woffice_get_settings_option('buddypress_' . $field_name . '_icon');

			// We check if the field have to be displayed
			if ( ! $field['field_show'] )
				continue;

			$field['social_field'] = false;
			$field_name_lower = strtolower( $field_name );
			foreach ( $social_fields_available as $socials_detectable_key => $socials_detectable_field ) {

				if ( strpos( $field_name_lower, $socials_detectable_key ) !== false ) {

					if ( empty( $field['field_icon'] ) ) {
						$field['field_icon'] = $socials_detectable_field['icon'];
					}

					$field['social_field'] = true;
					break;
				}

			}

			// We try to set a default icon
			if ( empty($field['field_icon']) && !$field['social_field'] ) {
				$field['field_icon'] = 'fa-arrow-right';
				if ($field_type == 'datebox') {
					$field['field_icon'] = 'fa-calendar';
				} elseif ($field_type == 'email') {
					$field['field_icon'] = 'fa-envelope';
				}
			}

			// Print the table column headings for each XProfile field
			if($field_name != 'wordpress_email')
				echo '<th><i class="' . woffice_convert_fa4_to_fa5($field['field_icon']) . ' text-light pr-1"></i> ' . $field_name . '</th>';
			else
				echo '<th><i class="' . woffice_convert_fa4_to_fa5($field['field_icon']) . '" text-light pr-1></i> ' . __('Email', 'woffice') . '</th>';

		}
		?>
		<?php if (bp_is_active('friends') && is_user_logged_in()) { ?>
			<th><?php _e('Friendship', 'woffice'); ?></th>
		<?php } ?>
		</thead>
		<tbody>
		<?php while ( bp_members() ) : bp_the_member(); ?>
			<tr>
				<td>
					<a href="<?php bp_member_permalink(); ?>" class="clearfix">
						<?php bp_member_avatar('type=full&width=40&height=40&class=rounded-circle'); ?>
						<?php
						// USERNAME OR NAME DISPLAYED
						$user_ID = bp_get_member_user_id();
						$ready_display = woffice_get_name_to_display($user_ID);
						echo '<span class="font-weight-bold">'. $ready_display .'</span>';
						?>
					</a>
				</td>
				<?php if ($members_role_enabled): ?>
					<td>
						<span class="member-role badge badge-primary badge-pill"><?php echo woffice_get_user_role($user_id); ?></span>
					</td>
				<?php endif; ?>
				<?php if( $last_activity_enabled ): ?>
					<td>
						<span class="activity"><?php bp_member_last_active(); ?></span>
					</td>
				<?php endif; ?>
				<?php
				foreach ($fields_values as $field_name => &$field) {

					if( !isset($field['field_show']) || (isset($field['field_show']) && !$field['field_show']) )
						continue;

					$field_type = $field['field_type'];

					if ($field_name != 'wordpress_email') {
						$field_value = bp_get_profile_field_data('field=' . $field_name . '&user_id=' . $user_ID);
					} else {
						$user_info = get_userdata($user_ID);
						$field_value = "<a href='mailto:" . $user_info->user_email . "' rel='nofollow'>$user_info->user_email</a>";
					}

					// We check if the field is empty
					if (empty($field_value)) {
						echo '<td></td>';
						continue;
					}

					// Print content of the XProfile fields
					if ( isset($field['social_field']) && $field['social_field']) {
						// A social field
						$field_string = '<a href="' . $field_value . '" target="_blank" ><i class="' . woffice_convert_fa4_to_fa5($field['field_icon']). ' text-light"></i></a>';
						echo '<td>' . $field_string . '</td>';
					} else {
						echo '<td>';
						if ( is_array( $field_value ) ) {
							echo implode( ", ", $field_value );
						} else {
							echo $field_value;
						}

						echo '</td>';
					}

				}

				?>
				<?php if (bp_is_active('friends') && is_user_logged_in()){ ?>
					<td>
						<?php do_action( 'bp_directory_members_actions' ); ?>
					</td>
				<?php } ?>
			</tr>
		<?php endwhile; ?>
		</tbody>
	</table>
</div>