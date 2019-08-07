<?php
// This function displays the admin page content
function dpProEventCalendar_special_page() {
	global $wpdb, $table_prefix, $dpProEventCalendar;
	$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
	
	if ($_POST['add']) {
		
		foreach($_POST as $key=>$value) { $$key = $value; }
		
		$title = strip_tags(str_replace("'", '"', $title));
		
		$sql = "INSERT INTO $table_name ";
		$sql .= "(title, color) ";
		$sql .= "VALUES ";
		$sql .= "('$title', '$color');";
		$result = $wpdb->query($sql);
		
		wp_redirect( admin_url('admin.php?page=dpProEventCalendar-special&settings-updated=1') );
		exit;
	}
	
	if ($_POST['edit']) {
		
		foreach($_POST as $key=>$value) { $$key = $value; }
		
		$title = strip_tags(str_replace("'", '"', $title));
		
		$sql = "UPDATE $table_name SET ";
		$sql .= "title = '$title', ";
		$sql .= "color = '$color' ";
		$sql .= "WHERE id = $id;";
		$result = $wpdb->query($sql);
		
		wp_redirect( admin_url('admin.php?page=dpProEventCalendar-special&settings-updated=1') );
		exit;
	}
	
	if ($_GET['delete_sp_date']) {
	   $sp_date_id = $_GET['delete_sp_date'];
	   
	   $sql = "DELETE FROM $table_name WHERE id = $sp_date_id;";
	   $result = $wpdb->query($sql);
	   	   
	   wp_redirect( admin_url('admin.php?page=dpProEventCalendar-special&settings-updated=1') );
	   exit;
	}
	?>

    <div class="wrap" style="clear:both;" id="dp_options">
    
    <h2></h2>
    <div style="clear:both;"></div>
     <!--end of poststuff --> 
        <div id="dp_ui_content">
            
            <div id="leftSide">
                <div id="dp_logo"></div>
                <p>
                    Version: <?php echo DP_PRO_EVENT_CALENDAR_VER?><br />
                </p>
                <ul id="menu" class="nav">
                	<li><a href="admin.php?page=dpProEventCalendar-settings" title=""><span><?php _e('General Settings','dpProEventCalendar'); ?></span></a></li>
                    <li><a href="admin.php?page=dpProEventCalendar-admin" title=""><span><?php _e('Calendars','dpProEventCalendar'); ?></span></a></li>
                    <li><a href="edit.php?post_type=pec-events" title=""><span><?php _e('Events','dpProEventCalendar'); ?></span></a></li>
                    <li><a href="edit.php?post_type=pec-venues" title=""><span><?php _e('Venues','dpProEventCalendar'); ?></span></a></li>
                    <li><a href="javascript:void(0);" title="" class="active"><span><?php _e('Special Dates / Event Color','dpProEventCalendar'); ?></span></a></li>
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
                                <h2><?php _e('Special Dates / Event Color','dpProEventCalendar'); ?></h2>
                                <span><?php _e('Add special dates to use in the calendars. Such as holidays, company events, personal events, etc... Assign them to calendars and events.','dpProEventCalendar'); ?></span>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                    
                    <div class="wrapper">
                    	<div id="dpProEventCalendar_SpecialDates" class="dpProEventCalendar_ModalManager" title="<?php echo __( 'Add new special date', 'dpProEventCalendar' )?> / <?php echo __( 'Event Color', 'dpProEventCalendar' )?>">
                        <form method="post" action="<?php echo admin_url('admin.php?page=dpProEventCalendar-special&noheader=true'); ?>" onsubmit="return special_checkform();">
                        <input type="hidden" name="add" value="1" />
                        <div class="option option-checkbox no_border">
                            <div class="option-inner">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr height="50" class="row">
                                        <td width="20%"><span><?php _e('Title','dpProEventCalendar'); ?></span></td>
                                        <td><input type="text" class="regular-text" name="title" id="dpEventsCalendar_title" /></td>
                                    </tr>
									
                                    <tr height="50" class="row">
                                        <td width="20%"><span><?php _e('Color','dpProEventCalendar'); ?></span></td>
                                        <td><div id="specialDate_colorSelector" class="colorSelector"><div style="background-color: #fff"></div></div>
                                        <input type="hidden" name="color" id="dpProEventCalendar_color" value="#fff" /></td>
                                    </tr>
                                    
                                    <tr height="50">
                                    	<td colspan="2" align="center">
                                    		<input type="submit" class="button" value="<?php _e('Submit') ?>" />
                                    	</td>
                                    </tr>
                            	</table>
                        	</div>
                        </div>
                        <div class="clear"></div>
                        </form>
                        </div>
                        
                        <div id="dpProEventCalendar_SpecialDatesEdit" class="dpProEventCalendar_ModalManager" title="Edit Special Date">
                        <form method="post" action="<?php echo admin_url('admin.php?page=dpProEventCalendar-special&noheader=true'); ?>" onsubmit="return special_checkform_edit();">
                        <input type="hidden" name="edit" value="1" />
                        <input type="hidden" name="id" id="dpPEC_special_id" value="" />
                        <div class="option option-checkbox no_border">
                            <div class="option-inner">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr height="50" class="row">
                                        <td width="20%"><span><?php _e('Title','dpProEventCalendar'); ?></span></td>
                                        <td><input type="text" class="regular-text" name="title" id="dpPEC_special_title" /></td>
                                    </tr>
									
                                    <tr height="50" class="row">
                                        <td width="20%"><span><?php _e('Color','dpProEventCalendar'); ?></span></td>
                                        <td><div id="specialDate_colorSelector_Edit" class="colorSelector"><div></div></div>
                                        <input type="hidden" name="color" id="dpPEC_special_color" value="" /></td>
                                    </tr>
                                    
                                    <tr height="50">
                                    	<td colspan="2" align="center">
                                    		<input type="submit" class="button" value="<?php _e('Submit') ?>" />
                                    	</td>
                                    </tr>
                            	</table>
                        	</div>
                        </div>
                        <div class="clear"></div>
                        </form>
                        </div>
                        
                        <div class="submit">
                        
                        <input type="button" value="<?php echo __( 'Add new special date', 'dpProEventCalendar' )?> / <?php echo __( 'Event Color', 'dpProEventCalendar' )?>" class="btn_add_special_date button" />
                        
                        </div>
                        <table class="widefat" cellpadding="0" cellspacing="0" id="sort-table">
                        	<thead>
                        		<tr style="cursor:default !important;">
                                	<th width="10%"><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th width="40%"><?php _e('Title','dpProEventCalendar'); ?></th>
                                    <th width="30%"><?php _e('Color','dpProEventCalendar'); ?></th>
                                    <th width="20%"><?php _e('Actions','dpProEventCalendar'); ?></th>
                                 </tr>
                            </thead>
                            <tbody>
                        <?php 
						$counter = 0;
                        $querystr = "
                        SELECT *
                        FROM $table_name 
                        ORDER BY title ASC
                        ";
                        $sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
                        foreach($sp_dates_obj as $sp_dates) {
                            echo '<tr id="'.$sp_dates->id.'">
									<td>'.$sp_dates->id.'</td>
									<td>'.$sp_dates->title.'</td>
									<td><div style="background-color: '.$sp_dates->color.'; height: 10px; width: 30px; padding-top: 4px; margin-top: 2px; border: 2px solid #333;"></div></td>
									<td>
										<input type="button" value="'.__( 'Edit', 'dpProEventCalendar' ).'" name="edit_special" data-special-date-id="'.$sp_dates->id.'" data-special-date-title=\''.$sp_dates->title.'\' data-special-date-color="'.$sp_dates->color.'" class="btn_edit_special_date button-secondary" />
										<input type="button" value="'.__( 'Delete', 'dpProEventCalendar' ).'" name="delete_special" class="button-secondary" onclick="if(confirmSpecialDelete()) { location.href=\''.admin_url('admin.php?page=dpProEventCalendar-special&delete_sp_date='.$sp_dates->id.'&noheader=true').'\'; }" />
									</td>
								</tr>'; 
							$counter++;
                        }
                        ?>
                        
                    		</tbody>
                            <tfoot>
                            	<tr style="cursor:default !important;">
                                	<th><?php _e('ID','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Title','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Color','dpProEventCalendar'); ?></th>
                                    <th><?php _e('Actions','dpProEventCalendar'); ?></th>
                                 </tr>
                            </tfoot>
                        </table>
                        
                        <div class="submit">
                        
                        <input type="button" value="<?php echo __( 'Add new special date', 'dpProEventCalendar' )?> / <?php echo __( 'Event Color', 'dpProEventCalendar' )?>" class="btn_add_special_date button" />
                        
                        </div>
                    </div>
                </div>           
            </div>
        </div>

                    
</div> <!--end of float wrap -->


<?php	
}
?>