<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'dpProEventCalendar_settings');
  add_action('admin_init', 'dpProEventCalendar_register_mysettings'); 
} 

//Adds settings to Network Settings
add_filter( 'wpmu_options'       , 'dpProEventCalendar_show_network_settings' );
add_action( 'update_wpmu_options', 'dpProEventCalendar_save_network_settings' );

// function for adding settings page to wp-admin
function dpProEventCalendar_settings() {
global $dpProEventCalendar, $current_user;
    
	if(!is_array($dpProEventCalendar['user_roles'])) { $dpProEventCalendar['user_roles'] = array(); }
	if(!in_array(dpProEventCalendar_get_user_role(), $dpProEventCalendar['user_roles']) && dpProEventCalendar_get_user_role() != "administrator" && !is_super_admin($current_user->ID)) { return; }
    // Add a new submenu under Options:

    $args = array( 'posts_per_page' => 21, 'post_type'=> 'pec-events', 'post_status' => 'pending' );

    $pending = get_posts( $args );
    $pending_counter = 0;
    $pending_counter = count($pending);

	add_menu_page( 'Event Calendar', __('Event Calendar', 'dpProEventCalendar'). ( $pending_counter > 0 ? '<span class="update-plugins count-'.($pending_counter > 20 ? '20+' : $pending_counter).'"><span class="plugin-count">'.($pending_counter > 20 ? '20+' : $pending_counter).'</span></span>' : '' ), 'edit_posts','dpProEventCalendar-admin', 'dpProEventCalendar_calendars_page', 'dashicons-calendar-alt', '139.2' );
	add_submenu_page('dpProEventCalendar-admin', __('Categories', 'dpProEventCalendar'), __('Categories', 'dpProEventCalendar'), 'edit_posts', 'edit-tags.php?taxonomy=pec_events_category');
	if(dpProEventCalendar_get_user_role() != 'editor' && dpProEventCalendar_get_user_role() != 'contributor' && dpProEventCalendar_get_user_role() != 'author') {
		add_submenu_page('dpProEventCalendar-admin', __('Calendars', 'dpProEventCalendar'), __('Calendars', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-admin', 'dpProEventCalendar_calendars_page');
		add_submenu_page('dpProEventCalendar-admin', __('Special Dates', 'dpProEventCalendar'), __('Special Dates / Event Color', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-special', 'dpProEventCalendar_special_page');
		add_submenu_page('dpProEventCalendar-admin', __('Settings', 'dpProEventCalendar'), __('Settings', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-settings', 'dpProEventCalendar_settings_page');
		add_submenu_page('dpProEventCalendar-admin', __('Custom Shortcodes', 'dpProEventCalendar'), __('Custom Shortcodes', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-custom-shortcodes', 'dpProEventCalendar_custom_shortcodes_page');
	}
	//add_submenu_page('dpProEventCalendar-admin', __('Documentation', 'dpProEventCalendar'), __('Documentation', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-documentation', 'dpProEventCalendar_documentation_page');
	
	//add_submenu_page('dpProEventCalendar-admin', __('Display Data in Event Page', 'dpProEventCalendar'), __('Display Data in Event Page', 'dpProEventCalendar'), 'edit_posts', 'dpProEventCalendar-eventdata', 'dpProEventCalendar_eventdata_page');
}

function dpProEventCalendar_get_user_role() {
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	return $user_role;
}

function dpProEventCalendar_show_network_settings() {
    $settings = dpProEventCalendar_get_network_settings();
?>
    <h3><?php _e( 'Pro Event Calendar Settings' ); ?></h3>
    <table id="menu" class="form-table">
        <?php
            foreach ( $settings as $setting ) :
        ?>

        <tr valign="top">
            <th scope="row"><?php echo $setting['name']; ?></th>
            <td>
                <input type="<?php echo $setting['type'];?>" name="dpProEventCalendar[<?php echo $setting['id']; ?>]" value="<?php echo esc_attr( get_site_option( $setting['id'] ) ); ?>" />
                <br /><?php echo $setting['desc']; ?>
            </td>
        </tr>
        <?php
    endforeach;
    echo '</table>';
}

function dpProEventCalendar_save_network_settings() {
    $posted_settings  = array_map( 'sanitize_text_field', $_POST['dpProEventCalendar'] );

    foreach ( $posted_settings as $name => $value ) {
        update_site_option( $name, $value );
    }
}

function dpProEventCalendar_get_network_settings() {

    $settings[] = array(
                'id'   => 'pec-purchase-code',
                'name' => __( 'Purchase Code', 'dpProEventCalendar' ),
                'desc' => __('Introduce the purchase code to get automatic updates.','dpProEventCalendar'),
                'type' => 'text',
                'size' => 'regular'
    );

    return apply_filters( 'plugin_settings', $settings );
}

include(dirname(__FILE__) . '/calendars.php');
include(dirname(__FILE__) . '/events-meta.php');
include(dirname(__FILE__) . '/special.php');
include(dirname(__FILE__) . '/custom_shortcodes.php');

function dpProEventCalendar_documentation_page() {
	wp_redirect('http://wpsleek.com/pro-event-calendar-documentation/');
	exit;
}

// This function displays the page content for the Settings submenu
function dpProEventCalendar_settings_page() {
global $dpProEventCalendar, $wpdb;

$max_upload = (ini_get('upload_max_filesize'));
$max_upload_kb = (int)(convertBytes($max_upload) / 1000);

if(!isset($dpProEventCalendar['year_from'])) {
    $dpProEventCalendar['year_from'] = 2;
}
if(!isset($dpProEventCalendar['year_until'])) {
    $dpProEventCalendar['year_until'] = 3;
}
?>

<div class="wrap" style="clear:both;" id="dp_options">

<h2></h2>
<?php $url = dpProEventCalendar_admin_url( array( 'page' => 'dpProEventCalendar-admin' ) );?>

<form method="post" id="dpProEventCalendar_events_meta" action="options.php" enctype="multipart/form-data">
<?php settings_fields('dpProEventCalendar-group'); ?>
<div style="clear:both;"></div>
 <!--end of poststuff --> 
	
    <div id="dp_ui_content">
    	
        <div id="leftSide">
        	<div id="dp_logo"></div>
            <p>
                Version: <?php echo DP_PRO_EVENT_CALENDAR_VER?><br />
            </p>
            <ul id="menu" class="nav">
                <li><a href="javascript:void(0);" class="active" title=""><span><?php _e('General Settings','dpProEventCalendar'); ?></span></a></li>
                <li><a href="admin.php?page=dpProEventCalendar-admin" title=""><span><?php _e('Calendars','dpProEventCalendar'); ?></span></a></li>
                <li><a href="edit.php?post_type=pec-events" title=""><span><?php _e('Events','dpProEventCalendar'); ?></span></a></li>
                <li><a href="edit.php?post_type=pec-venues" title=""><span><?php _e('Venues','dpProEventCalendar'); ?></span></a></li>
                <li><a href="admin.php?page=dpProEventCalendar-special" title=""><span><?php _e('Special Dates','dpProEventCalendar'); ?></span></a></li>
                <li><a href="admin.php?page=dpProEventCalendar-custom-shortcodes" title=""><span><?php _e('Custom Shortcodes','dpProEventCalendar'); ?></span></a></li>
                <?php
				if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
				?>
				<li><a href="admin.php?page=dpProEventCalendar-payments" title=""><span><?php _e('Payments Options','dpProEventCalendar'); ?></span></a></li>
				<?php }?>
                <li><a href="http://wpsleek.com/pro-event-calendar-documentation/" target="_blank" title=""><span><?php _e('Documentation','dpProEventCalendar'); ?></span></a></li>
            </ul>
            
            <a href="http://codecanyon.net/downloads" target="_blank" class="rate_plugin">
                <?php _e('Rate this plugin!','dpProEventCalendar'); ?>
                <br>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
            </a>

            <div class="clear"></div>
		</div>     
        
        <div id="rightSide">
        	<div id="menu_general_settings">
                <div class="titleArea">
                    <div class="wrapper">
                        <div class="pageTitle">
                            <h5><?php _e('General Settings','dpProEventCalendar'); ?></h5>
                            <span></span>
                        </div>
                        
                        <div class="clear"></div>
                    </div>
                </div>
                
                <div class="wrapper">
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Purchase Code:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" value="<?php echo $dpProEventCalendar['purchase_code']?>" name='dpProEventCalendar_options[purchase_code]' class="large-text"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Introduce the purchase code to get automatic updates.','dpProEventCalendar'); ?> <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php _e('Where is my purchase code?.','dpProEventCalendar'); ?></a>
                                    <br>
                                    <?php _e('<strong>Note: </strong>Every License is for a single domain, if the same purchase code is used from different domains it will be blocked and won\'t receive automatic updates', 'dpProEventCalendar')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('User Roles:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name='dpProEventCalendar_options[user_roles][]' multiple="multiple" class="multiple">
                                    	<option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                                       <?php 
									   $user_roles = '';
                                       $editable_roles = get_editable_roles();

								       foreach ( $editable_roles as $role => $details ) {
								           $name = translate_user_role($details['name'] );
								           if(esc_attr($role) == "administrator" || esc_attr($role) == "subscriber") { continue; }
										   if ( in_array($role, $dpProEventCalendar['user_roles']) ) // preselect specified role
								               $user_roles .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
								           else
								               $user_roles .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
								       }
									   echo $user_roles;
									   ?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the user role that will manage the plugin.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Events Slug:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" value="<?php echo $dpProEventCalendar['events_slug']?>" name='dpProEventCalendar_options[events_slug]' class="large-text"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Introduce the events URL slug. Be sure that there is not any other post type using it already. <br>(Default: pec-events)','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Categories Slug:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" value="<?php echo $dpProEventCalendar['categories_slug']?>" name='dpProEventCalendar_options[categories_slug]' class="large-text"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Introduce the categories URL slug. Be sure that there is not any other post type using it already. <br>(Default: pec_events_category)','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Redirect archive page:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" value="<?php echo $dpProEventCalendar['redirect_archive']?>" name='dpProEventCalendar_options[redirect_archive]' class="large-text"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Introduce a custom url if you want to redirect the default archive page. ('.get_post_type_archive_link('pec-events').')') ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Email to send emails from:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="text" value="<?php echo $dpProEventCalendar['wp_mail_from']?>" name='dpProEventCalendar_options[wp_mail_from]' class="large-text" placeholder="wordpress@<?php echo str_replace("www.", "", $_SERVER['HTTP_HOST'])?>"/>
                                    <br>
                                </div>
                                <div class="desc"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Custom CSS:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <textarea name='dpProEventCalendar_options[custom_css]' rows="10" placeholder=".classname {
    background: #333;
}"><?php echo $dpProEventCalendar['custom_css']?></textarea>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Add your custom CSS code.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('RTL (Right-to-left) Support','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['rtl_support'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[rtl_support]' class="checkbox"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Add RTL support for the calendars.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Exclude Google Maps JS file?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['exclude_gmaps'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[exclude_gmaps]' class="checkbox"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Check this option if you have a conflict with other plugins related to the Google Maps feature.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Disable URL Rewrite Rules?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['disable_rewrite_rules'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[disable_rewrite_rules]' class="checkbox"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select this option if you have unexpected 404 error pages in your site.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Disable Reminders?','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['disable_reminders'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[disable_reminders]' class="checkbox"/>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Disable booking reminders for all calendars.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Send reminders','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="1" maxlength="2" max="99" value="<?php echo ($dpProEventCalendar['days_reminders'] == "" ? 3 : $dpProEventCalendar['days_reminders'])?>" name='dpProEventCalendar_options[days_reminders]' class="large-text" style="width:50px;" /> <?php _e('Days before the event starts','dpProEventCalendar'); ?>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Amount of days prior to the event to send the reminders.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Terms & Conditions Page','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <select name="dpProEventCalendar_options[terms_conditions]">
                                    	<option value=""></option>
                                        <?php 
										  $pages = get_pages(); 
										  foreach ( $pages as $page ) {
											$option = '<option value="' . $page->ID . '" ' . ($page->ID == $dpProEventCalendar['terms_conditions'] ? 'selected="selected"' : '') . '>';
											$option .= $page->post_title;
											$option .= '</option>';
											echo $option;
										  }
										 ?>
                                    </select>
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Select the Terms & Conditions page for booking events','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Pagination:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="1" maxlength="2" max="99" value="<?php echo ($dpProEventCalendar['pagination'] == "" ? 10 : $dpProEventCalendar['pagination'])?>" name='dpProEventCalendar_options[pagination]' class="large-text" style="width:50px;" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Set the number of items to display per page.','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Max image size:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp">
                                    <input type="number" min="1" maxlength="10" max="<?php echo $max_upload_kb?>" value="<?php echo $dpProEventCalendar['max_file_size']?>" name='dpProEventCalendar_options[max_file_size]' placeholder='<?php echo $max_upload_kb?>' class="large-text" />
                                    <br>
                                </div>
                                <div class="desc"><?php _e('Set the max size in kb for the image upload in frontend forms. Leave blank to use server limit.','dpProEventCalendar'); ?> <?php _e('Max','dpProEventCalendar'); ?>: <?php echo $max_upload_kb?>kb</div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="option option-select option_w">
                        <div class="option-inner">
                            <label class="titledesc"><?php _e('Year range:','dpProEventCalendar'); ?></label>
                            <div class="formcontainer">
                                <div class="forminp" id="div_year_range">
                                    <select name='dpProEventCalendar_options[year_from]'>
                                    <?php for($i = 10; $i >= 1; $i--) {?>
                                        <option value="<?php echo $i?>" <?php echo ($dpProEventCalendar['year_from'] == $i ? 'selected="selected"' : '')?>><?php echo (date('Y') - $i) . ' (-'.$i.')'?></option>
                                    <?php }?>
                                    </select>
                                    &nbsp;
                                    <select name='dpProEventCalendar_options[year_until]'>
                                    <?php for($i = 1; $i <= 10; $i++) {?>
                                        <option value="<?php echo $i?>" <?php echo ($dpProEventCalendar['year_until'] == $i ? 'selected="selected"' : '')?>><?php echo (date('Y') + $i) . ' (+'.$i.')'?></option>
                                    <?php }?>
                                    </select>
                                </div>
                                <div class="desc"><?php _e('Set the year range used in the calendar layouts.','dpProEventCalendar'); ?> <?php _e('Max','dpProEventCalendar'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    
                    <h2 class="subtitle accordion_title" id="settings_maps_title" onclick="showAccordion('div_maps', this);"><?php _e('Google Maps','dpProEventCalendar'); ?></h2>
                    <div id="div_maps" style="display:none;">
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('API Key:','dpProEventCalendar'); ?>
                                </label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo $dpProEventCalendar['google_map_key']?>" name='dpProEventCalendar_options[google_map_key]' class="large-text" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Create an API key as per instructions here:.','dpProEventCalendar'); ?>  <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com/maps/documentation/javascript/get-api-key</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Google Maps Zoom:','dpProEventCalendar'); ?>
                                <span class="pec_info dashicons dashicons-info"><span><?php _e('Higher number means a closer view.','dpProEventCalendar'); ?></span></span>
                                </label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="1" maxlength="2" max="99" value="<?php echo ($dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom'])?>" name='dpProEventCalendar_options[google_map_zoom]' class="large-text" style="width:50px;" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Set the Google Map Zoom number.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Google Map Custom Marker:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input id="dpProEventCalendar_options_map_marker" style="width: 350px;" type="text" class="large-text" name="dpProEventCalendar_options[map_marker]" value="<?php echo $dpProEventCalendar['map_marker']?>" placeholder="<?php _e('Enter an URL or upload an image to use a custom marker.','dpProEventCalendar'); ?>" />
                                    </div>
                                    <input id="upload_image_button" type="button" class="button-secondary" value="<?php _e('Upload Image','dpProEventCalendar'); ?>" style="width:auto; padding:auto; font-weight:normal;" />
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Default Ubication:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input id="pec_map" style="width: 350px;" type="text" class="large-text" name="dpProEventCalendar_options[map_default_ubication]" value="<?php echo $dpProEventCalendar['map_default_ubication']?>" placeholder="<?php _e('Enter a city / country to set as the default ubication.','dpProEventCalendar'); ?>" />
                                        <input id="pec_map_lnlat" type="text" name="dpProEventCalendar_options[map_default_latlng]" value="<?php echo $dpProEventCalendar['map_default_latlng']?>" />
                                         <div class="dp_pec_date_event_map_overlay" onclick="style.pointerEvents='none'" style="height:400px; margin-top: -400px; top: 400px;"></div>
                                         <?php
                                        $map_lat = 0;
                                        $map_lng = 0;

                                        if($dpProEventCalendar['map_default_latlng'] != "") {
                                            
                                            $map_lnlat = explode(",", $dpProEventCalendar['map_default_latlng']);
                                            $map_lat = $map_lnlat[0];
                                            $map_lng = $map_lnlat[1];
                                        }
                                         ?>
                                        <div id="mapCanvas" data-map-lat="<?php echo $map_lat?>" data-map-lng="<?php echo $map_lng?>" style="height: 400px;"></div>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_fb_api', this);"><?php _e('Facebook API Keys','dpProEventCalendar'); ?></h2>
                    <div id="div_fb_api" style="display:none;">

                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('App ID:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type='text' name='dpProEventCalendar_options[facebook_app_id]' value="<?php echo $dpProEventCalendar['facebook_app_id']?>"/>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select no_border">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('App Secret:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type='text' name='dpProEventCalendar_options[facebook_app_secret]' value="<?php echo $dpProEventCalendar['facebook_app_secret']?>"/>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        
                        <strong><?php _e('Instructions to get the Facebook API keys','dpProEventCalendar'); ?></strong>
                        <ol>
                            <li>If you are not registered as a developer in Facebook, you will have to register in <a href="https://developers.facebook.com/">https://developers.facebook.com/</a>, go to Apps -> Register as a Developer</li>
                            <li>Once you are registered go to <a href="https://developers.facebook.com/">https://developers.facebook.com/</a> Apps -> Create a new App and fill the form</li>
                            <li>If you created the App succesfully, you will see the new App ID and Secret keys in the dashboard</li>
                        </ol>

                    </div>
                    
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_recaptcha', this);"><?php _e('ReCaptcha','dpProEventCalendar'); ?></h2>
					
                    <div id="div_recaptcha" style="display: none;">
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Enable ReCaptcha for frontend forms','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['recaptcha_enable'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[recaptcha_enable]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('A captcha will be displayed in some of the frontend forms. Useful to block spam bots.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Site Key:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type='text' name='dpProEventCalendar_options[recaptcha_site_key]' value="<?php echo $dpProEventCalendar['recaptcha_site_key']?>"/>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select no_border">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Secret Key:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type='text' name='dpProEventCalendar_options[recaptcha_secret_key]' value="<?php echo $dpProEventCalendar['recaptcha_secret_key']?>"/>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <p><strong><?php _e('Get the Keys adding your domain in the recaptcha site:','dpProEventCalendar'); ?> <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a></strong></p>
                        
                    </div>
                    
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_expired', this);"><?php _e('Expired Events','dpProEventCalendar'); ?></h2>
                    
                    <div id="div_expired" style="display: none;">

                        <div class="option option-select option_w no_border">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Remove expired events automatically','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['remove_expired_enable'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[remove_expired_enable]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Events which End date field has passed or the Start date has passed and it is not a recurrent event. This will also prevent importing old events.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Status of events','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="dpProEventCalendar_options[remove_expired_status]">
                                            <option value="publish"><?php _e('Only published Events','dpProEventCalendar'); ?></option>
                                            <option value="any" <?php echo ('any' == $dpProEventCalendar['remove_expired_status'] ? 'selected="selected"' : '')?>><?php _e('Events with any status','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select if you want to remove expired events with published / any status.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Trash / Remove','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="dpProEventCalendar_options[remove_expired_completly]">
                                            <option value="trash"><?php _e('Send to trash','dpProEventCalendar'); ?></option>
                                            <option value="remove" <?php echo ('remove' == $dpProEventCalendar['remove_expired_completly'] ? 'selected="selected"' : '')?>><?php _e('Remove completly','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select if you want to remove expired events completly or send them to the trash.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Expire after:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="0" maxlength="3" max="999" value="<?php echo ($dpProEventCalendar['remove_expired_days'] == "" ? 10 : $dpProEventCalendar['remove_expired_days'])?>" name='dpProEventCalendar_options[remove_expired_days]' class="large-text" style="width:50px;" /> <?php _e('Days','dpProEventCalendar'); ?>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <!--
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_landing', this);"><?php _e('Event Landing Page (beta)','dpProEventCalendar'); ?></h2>
					
                    <div id="div_landing" style="display: none;">

                        <div class="option option-select option_w no_border">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Enable Event Landing Page Template','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo ($dpProEventCalendar['event_single_enable'] ? "checked='checked'" : "")?> name='dpProEventCalendar_options[event_single_enable]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('The events single pages will be displayed with a different theme.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    -->

                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_custom_fields', this);"><?php _e('Event Custom Fields','dpProEventCalendar'); ?></h2>
					
                    <div id="div_custom_fields" style="display: none;">
                        <table class="widefat" cellpadding="0" cellspacing="0" id="custom_fields_list">
                            <thead>
                                <tr style="cursor:default !important;">
                                    <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Placeholder','dpProEventCalendar'); ?></th>
                                    <th>&nbsp;</th>
                                 </tr>
                            </thead>
                            <tbody>
                                <tr id="custom_field_new" style="display:none;">
                                	<input type="hidden" name="dpProEventCalendar_options_replace[custom_fields_counter][]" value="1" />
                                    <td><input type="text" name="dpProEventCalendar_options_replace[custom_fields][id][]" class="pec_custom_field_id" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                    <td><input type="text" name="dpProEventCalendar_options_replace[custom_fields][name][]" class="" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                    <td style="overflow: visible;">
                                    	<select name="dpProEventCalendar_options_replace[custom_fields][type][]">
                                        	<option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                            <option value="checkbox"><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                    	</select>
                                    </td>
                                    <td>
                                        <select name="dpProEventCalendar_options_replace[custom_fields][optional][]">
                                            <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                            <option value="0">><?php _e('No','dpProEventCalendar'); ?></option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="dpProEventCalendar_options_replace[custom_fields][placeholder][]" class="" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                    <td>
    									<input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                    </td>
                                </tr>
                                <?php
    							if(is_array($dpProEventCalendar['custom_fields_counter'])) {
    								$counter = 0;
    								foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
    								?>
    								<tr>
                                    	<input type="hidden" name="dpProEventCalendar_options[custom_fields_counter][]" value="1" />
    									<td><input type="text" name="dpProEventCalendar_options[custom_fields][id][]" class="pec_custom_field_id" value="<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
    									<td><input type="text" name="dpProEventCalendar_options[custom_fields][name][]" class="" value="<?php echo htmlentities($dpProEventCalendar['custom_fields']['name'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
    									<td style="overflow: visible;">
    										<select name="dpProEventCalendar_options[custom_fields][type][]">
    											<option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                <option value="checkbox" <?php echo ($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox' ? 'selected="selected"' : '')?>><?php _e('Checkbox','dpProEventCalendar'); ?></option>
    										</select>
    									</td>
    									<td>
                                            <select name="dpProEventCalendar_options[custom_fields][optional][]">
                                                <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                <option value="0" <?php echo ($dpProEventCalendar['custom_fields']['optional'][$counter] == '0' ? 'selected="selected"' : '')?>><?php _e('No','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
    									<td><input type="text" name="dpProEventCalendar_options[custom_fields][placeholder][]" class="" value="<?php echo htmlentities($dpProEventCalendar['custom_fields']['placeholder'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
    									<td>
    										<input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
    									</td>
    								</tr>
    								<?php 
    									$counter++;
    								}
    							} else {
    								
    							}?>
                            </tbody>
                            <tfoot>
                                <tr style="cursor:default !important;">
                                    <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Placeholder','dpProEventCalendar'); ?></th>
                                    <th>&nbsp;</th>
                                 </tr>
                            </tfoot>
                    	</table>

                        <div class="submit">
                            <input type="button" class="button-primary" value="<?php echo __( 'Add New', 'dpProEventCalendar' )?>" onclick="jQuery('#custom_fields_list tbody').append('<tr>'+jQuery('#custom_field_new').html().replace(/dpProEventCalendar_options_replace/g, 'dpProEventCalendar_options')+'</tr>'); jQuery('#custom_fields_list tbody select').selectric('refresh');" />
                        </div>
                    </div>
                            
                    <h2 class="subtitle accordion_title" onclick="showAccordion('div_booking_fields', this);"><?php _e('Booking Extra Fields','dpProEventCalendar'); ?></h2>
                    
                    <div id="div_booking_fields" style="display:none;">

                        <table class="widefat" cellpadding="0" cellspacing="0" id="booking_custom_fields_list">
                            <thead>
                                <tr style="cursor:default !important;">
                                    <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Placeholder','dpProEventCalendar'); ?></th>
                                    <th>&nbsp;</th>
                                 </tr>
                            </thead>
                            <tbody>
                                <tr id="booking_custom_field_new" style="display:none;">
                                    <input type="hidden" name="dpProEventCalendar_options_replace[booking_custom_fields_counter][]" value="1" />
                                    <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][id][]" class="pec_booking_custom_field_id" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                    <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][name][]" class="" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                    <td style="overflow: visible;">
                                        <select name="dpProEventCalendar_options_replace[booking_custom_fields][type][]">
                                            <option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                            <option value="checkbox"><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="dpProEventCalendar_options_replace[booking_custom_fields][optional][]">
                                            <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                            <option value="0"><?php _e('No','dpProEventCalendar'); ?></option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][placeholder][]" class="" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                    <td>
                                        <input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_booking_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                    </td>
                                </tr>
                                <?php
                                if(is_array($dpProEventCalendar['booking_custom_fields_counter'])) {
                                    $counter = 0;
                                    foreach($dpProEventCalendar['booking_custom_fields_counter'] as $key) {
                                    ?>
                                    <tr>
                                        <input type="hidden" name="dpProEventCalendar_options[booking_custom_fields_counter][]" value="1" />
                                        <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][id][]" class="pec_booking_custom_field_id" value="<?php echo $dpProEventCalendar['booking_custom_fields']['id'][$counter]?>" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                        <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][name][]" class="" value="<?php echo htmlentities($dpProEventCalendar['booking_custom_fields']['name'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                        <td style="overflow: visible;">
                                            <select name="dpProEventCalendar_options[booking_custom_fields][type][]">
                                                <option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                <option value="checkbox" <?php echo ($dpProEventCalendar['booking_custom_fields']['type'][$counter] == 'checkbox' ? 'selected="selected"' : '')?>><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="dpProEventCalendar_options[booking_custom_fields][optional][]">
                                                <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                <option value="0" <?php echo ($dpProEventCalendar['booking_custom_fields']['optional'][$counter] == '0' ? 'selected="selected"' : '')?>><?php _e('No','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][placeholder][]" class="" value="<?php echo htmlentities($dpProEventCalendar['booking_custom_fields']['placeholder'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                        <td>
                                            <input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_booking_booking_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                        </td>
                                    </tr>
                                    <?php 
                                        $counter++;
                                    }
                                } else {
                                    
                                }?>
                            </tbody>
                            <tfoot>
                                <tr style="cursor:default !important;">
                                    <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Placeholder','dpProEventCalendar'); ?></th>
                                    <th>&nbsp;</th>
                                 </tr>
                            </tfoot>
                        </table>
                        
                        <div class="submit">
    	                    <input type="button" class="button-primary" value="<?php echo __( 'Add New', 'dpProEventCalendar' )?>" onclick="jQuery('#booking_custom_fields_list tbody').append('<tr>'+jQuery('#booking_custom_field_new').html().replace(/dpProEventCalendar_options_replace/g, 'dpProEventCalendar_options')+'</tr>'); jQuery('#booking_custom_fields_list tbody select').selectric('refresh');" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
	
    <p align="right">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>

<script type="text/javascript">
jQuery(document).on('keyup', '.pec_custom_field_id, .pec_booking_custom_field_id', function() {
	
	jQuery(this).val(jQuery(this).val().replace(/ /g, "").toLowerCase());
	
});
</script>

                    
</div> <!--end of float wrap -->


<?php	
}
function dpProEventCalendar_register_mysettings() { // whitelist options
  register_setting( 'dpProEventCalendar-group', 'dpProEventCalendar_options', 'dpProEventCalendar_validate' );
}

function dpProEventCalendar_validate($input) {
	global $dpProEventCalendar;
	
	//if ( isset($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], 'dpProEventCalendar-settings') > 0) ) {
		//return $input;
	//}
	//die(print_r($input));
	//die(print_r($input));
	if(!$input['rtl_support']) 
		$input['rtl_support'] = 0;
	
	if(!$input['exclude_from_search']) 
		$input['exclude_from_search'] = 0;
		
	if(!$input['exclude_gmaps']) 
		$input['exclude_gmaps'] = 0;

    if(!$input['disable_rewrite_rules']) 
        $input['disable_rewrite_rules'] = 0;

    if(!$input['disable_reminders']) 
        $input['disable_reminders'] = 0;
	
    if(!$input['remove_expired_enable']) 
        $input['remove_expired_enable'] = 0;

    if(!$input['recaptcha_enable']) 
        $input['recaptcha_enable'] = 0;

	if(!$input['event_single_enable']) 
		$input['event_single_enable'] = 0;
		
	if(!$input['paypal_enable']) 
		$input['paypal_enable'] = 0;
		
	if(!$input['paypal_testmode']) 
		$input['paypal_testmode'] = 0;
		
	if(!$input['stripe_enable']) 
		$input['stripe_enable'] = 0;
		
	if(!$input['stripe_testmode']) 
		$input['stripe_testmode'] = 0;
		
	$dpProEventCalendar['custom_fields_counter'] = '';
    $dpProEventCalendar['booking_custom_fields_counter'] = '';
		
	$input = dpProEventCalendar_array_merge($dpProEventCalendar, $input);
    return $input;
}

function dpProEventCalendar_array_merge($paArray1, $paArray2)
{
    if (!is_array($paArray1) or !is_array($paArray2)) { return $paArray2; }
    foreach ($paArray2 AS $sKey2 => $sValue2)
    {
		if($sKey2 == "user_roles") {
			$paArray1[$sKey2] = array(); 	
		}
        $paArray1[$sKey2] = dpProEventCalendar_array_merge(@$paArray1[$sKey2], $sValue2);
    }
    return $paArray1;
}
?>