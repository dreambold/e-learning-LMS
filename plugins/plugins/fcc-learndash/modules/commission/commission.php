<?php

class WDM_FCC_COMM
{
    public function __construct()
    {
        add_action('admin_head', array($this, 'wdm_course_author_table_setup'));
        add_filter('admin_menu', array($this, 'course_author_menu'), 2000);
        add_action('admin_head', array($this, 'wdm_fcc_pop_js'));
        add_action('wp_ajax_wdm_update_course_author_commission', array($this, 'wdm_update_course_author_commission'));
        add_action('woocommerce_order_status_completed', array($this, 'wdm_fcc_add_record_to_db'),10,1);
        add_action('added_post_meta', array($this, 'wdm_course_author_updated_postmeta'), 10, 4);
        add_action('wp_ajax_wdm_amount_paid_course_author', array($this, 'wdm_amount_paid_course_author'));
        add_action('init', array($this, 'wdm_fcc_export_commission_report'));
        add_action('init', array($this, 'wdm_fcc_export_csv_date_filter'));
        add_action('admin_enqueue_scripts', array($this, 'wdm_enqueue_extension_style'));
    }

    /*
     * Creating wdm_course_author_commission table
     *
     *
     */

    public function wdm_course_author_table_setup()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'wdm_course_author_commission';
        $sql = 'CREATE TABLE '.$table_name.' (
                id INT NOT NULL AUTO_INCREMENT,
		user_id int,
		order_id int,
		product_id int,
		actual_price float,
		commission_price float,
		transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
                );';
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /*
     * adding Istructor commission menu inside learndash-lms menu
     *
     */

    public function wdm_fcc_pop_js()
    {
        wp_enqueue_style('wdm_fcc_admin_datatable_css', plugins_url('css/wdm_admin.css', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm_fcc_pop_up_css', plugins_url('css/wdm_popup.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm_fcc_pop_up', plugins_url('js/wdm_popup.js', dirname(dirname(__FILE__))));
        ?>
		<div id="blanket" style="display:none;"></div>
	<?php

    }

    public function course_author_menu()
    {
        //global $wdmir_plugin_data;
        //include_once 'includes/class-wdm-get-plugin-data.php';
        //$get_data_from_db = Wdm_Get_Plugin_Data::get_data_from_db($wdmir_plugin_data);
        if(is_plugin_active('sfwd-lms/sfwd_lms.php')){
            add_submenu_page('learndash-lms', sprintf(__('%s Author Commission', 'fcc'),LearnDash_Custom_Label::get_label('course')), sprintf(__('%s Author Commission', 'fcc'),LearnDash_Custom_Label::get_label('course')), 'manage_options', 'course_author', array($this, 'course_author_page_callback'));
        }
    }

    /*
     * Adding tabs inside intructor commission page
     *
     */
    public function course_author_page_callback()
    {
        $current_tab = isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'course_author';
        if (!is_super_admin() && $current_tab != 'export') {
            $current_tab = 'commission_report';
        }
        //$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        ?>
		<h2 class="nav-tab-wrapper">
		<?php if (is_super_admin()) {
    ?>
				<a class="nav-tab <?php echo(($current_tab == 'course_author') ? 'nav-tab-active' : '') ?> " href="?page=course_author&tab=course_author"><?php echo sprintf(__('%s Author', 'fcc'),LearnDash_Custom_Label::get_label('course'));
    ?></a>
		<?php
}
        ?>
			<a class="nav-tab <?php echo(($current_tab == 'commission_report') ? 'nav-tab-active' : '') ?>" href="?page=course_author&tab=commission_report"><?php echo __('Commission Report', 'fcc');
        ?></a>
			<a class="nav-tab <?php echo(($current_tab == 'export') ? 'nav-tab-active' : '') ?>" href="?page=course_author&tab=export"><?php echo __('Export', 'fcc');
        ?></a>
        <a class="nav-tab <?php echo(($current_tab == 'other_extensions') ? 'nav-tab-active' : '') ?>" href="?page=course_author&tab=other_extensions"><?php echo __('Other Extensions', 'fcc');
        ?></a>
		<?php do_action('course_author_tab_add', $current_tab);
        ?>
		</h2>
		<?php
        //echo '<pre>';print_R($_SERVER);echo '</pre>';

        switch ($current_tab) {
            case 'course_author':
                $this->wdm_course_author_first_tab();
                break;
            case 'commission_report':
                $this->wdm_course_author_second_tab();
                break;
            case 'export':
                $this->wdm_course_author_third_tab();
                break;
            case 'other_extensions' :
                    $this->promotionPage();
        }

        do_action('course_author_tab_checking', $current_tab);
    }

    /*
     * Displaying table for allocating course_author commission percentage
     */
    public function promotionPage()
    {
        if (false === ($extensions = get_transient('_fcc_extensions_data'))) {
            $extensions_json = wp_remote_get(
                'https://wisdmlabs.com/products-thumbs/ld_extensions.json',
                array(
                    'user-agent' => 'FCC Extensions Page'
                )
            );

            if (!is_wp_error($extensions_json)) {
                $extensions = json_decode(wp_remote_retrieve_body($extensions_json));

                if ($extensions) {
                    set_transient('_fcc_extensions_data', $extensions, 72 * HOUR_IN_SECONDS);
                }
            }
        }
        include_once('promotion/other-extensions.php');
        unset($extensions);
    }

    public function wdm_course_author_first_tab()
    {
        wp_enqueue_script('wdm-jquery-script', plugins_url('js/jquery.js', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))), array('wdm-jquery-script'));
        wp_localize_script('wdm-datatable-script', 'wdm_datatable_object',
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn'  => __('Previous', 'fcc'),
                'wdm_next_btn'  => __('Next', 'fcc'),
                'wdm_search_bar'    => __('Search','fcc'),
                'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table'   => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching'   => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf( __('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );
        wp_enqueue_script('wdm_commission_js', plugins_url('/js/commission.js', dirname(dirname(__FILE__))), array('jquery'));
        $data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'invalid_percentage' => __('Invalid percentage', 'fcc'),
        );
        wp_localize_script('wdm_commission_js', 'wdm_commission_data', $data);
// To show values in Commission column
        $course_author_commissions = get_option('course_author_commissions', '');
        // To get user Ids of course_authors
        $args = array('fields' => array('ID', 'display_name', 'user_email'), 'role' => 'wdm_course_author');
        $course_authors = get_users($args);
        ?>
		<br/>
		<div id="reports_table_div" style="padding-right: 5px">

			<!--Table shows Name, Email, etc-->
			<table id="wdm_fcc_report_tbl" >
				<thead>
					<tr>
						<th data-sort-initial="descending" data-class="expand">
		<?php echo __('Name', 'fcc');
        ?>
						</th>
						<th>
		<?php echo __('User email', 'fcc');
        ?>
						</th>
						<th>
					<?php echo __('Commission %', 'fcc');
        ?>
						</th>
						<th data-hide="phone" >
					<?php echo __('Update', 'fcc');
        ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
                    if (!empty($course_authors)) {
                        foreach ($course_authors as $course_author) {
                            $commission_percentage = get_user_meta($course_author->ID, 'wdm_commission_percentage', true);
                            if ($commission_percentage == '') {
                                $commission_percentage = 0;
                            }
                            //echo '<pre>';print_R($course_author);echo '</pre>';
                            ?>
							<tr>
								<td><center><?php echo $course_author->display_name;
                            ?></center></td>
						<td><center><?php echo $course_author->user_email;
                            ?></center></td>
						<td><center><input name="commission_input" size="5" value="<?php echo $commission_percentage;
                            ?>" min="0" max="100" type="number" id="input_<?php echo $course_author->ID;
                            ?>"></center></td>
						<td><center><a name="update_<?php echo $course_author->ID;
                            ?>" class="update_commission button button-primary" href="#"><?php echo __('Update', 'fcc');
                            ?></a><img class="wdm_ajax_loader"src="<?php echo plugins_url('/images/ajax-loader.gif', dirname(dirname(__FILE__)));
                            ?>" style="display:none;"></center></td>
						</tr>
				<?php

                        }
                    }
        ?>
				</tbody>

			</table>
		</div>
		<br/>
		<div id="update_commission_message"></div>


		<?php

    }

    /*
     * Updating course_author commission using ajax
     */

    public function wdm_update_course_author_commission()
    {
        $percentage = $_POST[ 'commission' ];
        $course_author_id = $_POST[ 'course_author_id' ];
        if (wdm_is_course_author($course_author_id)) {
            update_user_meta($course_author_id, 'wdm_commission_percentage', $percentage);
            echo __('Updated successfully', 'fcc');
        } else {
            echo __('Oops something went wrong', 'fcc');
        }
        die();
    }

    /*
     * On woocommerce order complete, adding commission percentage in custom table
     */
    public function wdm_fcc_add_record_to_db($order_id)
    {
       
        $integrate = get_option('wdm_woocommerce_integration', true);
        if($integrate == 'enable'){
            // echo $integrate;
            // die;
        $order = new WC_Order($order_id);
        global $wpdb;
        
       
        $items = $order->get_items();
        foreach ($items as $item) {

            //echo 'item <pre>';print_R($item);echo '</pre>';
            $product_id = $item[ 'product_id' ];
            $total = $item[ 'line_total' ];

            $related_course=get_post_meta($product_id,'_related_course',true);

            if(isset($related_course)){

                $course_id=$related_course[0];
                echo $course_id;
                $assigned_course = get_post($course_id);
           
                // echo "<pre>";
                // var_dump($assigned_course);
                // echo "</pre>";


                $author_id = $assigned_course->post_author;
           

                if (wdm_is_course_author($author_id)) {
                    $commission_percentage = get_user_meta($author_id, 'wdm_commission_percentage', true);
                    if ($commission_percentage == '') {
                        $commission_percentage = 0;
                    }
                    $commission_price = ($total * $commission_percentage) / 100;
                    // echo $commission_price;
                    // die;
                    $sql = "SELECT id FROM {$wpdb->prefix}wdm_course_author_commission WHERE user_id = $author_id AND order_id = $order_id AND product_id = $product_id";
                    $id = $wpdb->get_var($sql);
                    $data = array(
                        'user_id' => $author_id,
                        'order_id' => $order_id,
                        'product_id' => $product_id,
                        'actual_price' => $total,
                        'commission_price' => $commission_price,
                    );
                    if ($id == '') {
                        $wpdb->insert($wpdb->prefix.'wdm_course_author_commission', $data);
                    } else {
                        $wpdb->update($wpdb->prefix.'wdm_course_author_commission', $data, array('id' => $id));
                    }
                }
            }
            //echo '<pre>';print_R($product_post);echo '</pre>';exit;
            //get all courses ( level ) from produuct
        }
    }
}

    /*
     * Adding transaction details after LD transaction
     */

    public function wdm_course_author_updated_postmeta($meta_id, $object_id, $meta_key, $meta_value)
    {
        global $wpdb;
        $post_type = get_post_type($object_id);
        if ($post_type == 'sfwd-transactions' && $meta_key == 'course_id') {
            $course_id = $meta_value;
            $course_post = get_post($course_id);
            $author_id = $course_post->post_author;
            if (wdm_is_course_author($author_id)) {
                $commission_percentage = get_user_meta($author_id, 'wdm_commission_percentage', true);
                if ($commission_percentage == '') {
                    $commission_percentage = 0;
                }
                $total = get_post_meta($object_id, 'mc_gross', true);
                if ($total == '') {
                	$total = get_post_meta($object_id, 'stripe_price', true);
                    $total = ($total=='') ? 0 : $total;
                }
                if($total!='-1'){
                    $commission_price = ($total * $commission_percentage) / 100;
                }else{
                    $commission_price = 0;
                }
                $data = array(
                    'user_id' => $author_id,
                    'order_id' => $object_id,
                    'product_id' => $course_id,
                    'actual_price' => $total,
                    'commission_price' => $commission_price,
                );
                $wpdb->insert($wpdb->prefix.'wdm_course_author_commission', $data);
            }
        }
    }

    /*
     * Commission report page
     */

    public function wdm_course_author_second_tab()
    {
        if (!is_super_admin()) {
            $course_author_id = get_current_user_id();
        } else {
            $args = array('fields' => array('ID', 'display_name'), 'role' => 'wdm_course_author');
            $course_authors = get_users($args);
            //echo '<pre>';print_R($course_authors);echo '</pre>';
            $course_author_id = '';
            if (isset($_REQUEST[ 'wdm_course_author_id' ])) {
                $course_author_id = $_REQUEST[ 'wdm_course_author_id' ];
            }
            if (empty($course_authors)) {
                echo '<h3>'.sprintf(__(' No %s Author found', 'fcc'),LearnDash_Custom_Label::get_label('course')).'</h3>';

                return;
            }
            ?>
			<form method="post" action="?page=course_author&tab=commission_report">
				<table>
					<tr>
						<th><?php echo sprintf(__('Select %s Author:', 'fcc'),LearnDash_Custom_Label::get_label('course'));
            ?></th>
						<td>
							<select name="wdm_course_author_id">
			<?php foreach ($course_authors as $course_author) {
    ?>
									<option value="<?php echo $course_author->ID;
    ?>" <?php echo(($course_author_id == $course_author->ID) ? 'selected' : '');
    ?>><?php echo $course_author->display_name;
    ?></option>

			<?php
}
            ?>
							</select>
						</td>

						<td>
							<input type="submit" value="<?php echo __('Submit', 'fcc');
            ?>" class="button-primary">
						</td>
					</tr>
				</table>
			</form>
			<?php

        }
        if ($course_author_id != '') {
            $this->wdm_fcc_commission_report($course_author_id);
        }
    }

    /*
     * Commission Report page
     *
     */

    public function wdm_fcc_commission_report($course_author_id)
    {
        global $wpdb;
        wp_enqueue_script('wdm-jquery-script', plugins_url('js/jquery.js', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))), array('wdm-jquery-script'));
        wp_localize_script('wdm-datatable-script', 'wdm_datatable_object',
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn'  => __('Previous', 'fcc'),
                'wdm_next_btn'  => __('Next', 'fcc'),
                'wdm_search_bar'    => __('Search','fcc'),
                'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table'   => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching'   => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf( __('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );
        wp_enqueue_script('wdm_course_author_report_js', plugins_url('/js/commission_report.js', dirname(dirname(__FILE__))), array('jquery'));
        $data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'enter_amount' => __('Please Enter amount', 'fcc'),
            'enter_amount_less_than' => __('Please enter amount less than amount to be paid', 'fcc'),
            'added_successfully' => __('Record added successfully', 'fcc'),
        );
        wp_localize_script('wdm_course_author_report_js', 'wdm_commission_data', $data);
        ?>
		<br>
		<div id="reports_table_div" style="padding-right: 5px">

			<table id="wdm_fcc_report_tbl" >
				<thead>
					<tr>
						<th data-sort-initial="descending" data-class="expand">
					<?php echo __('Order ID', 'fcc');
        ?>
						</th>
						<th data-sort-initial="descending" data-class="expand">
					<?php echo sprintf(__('Product / %s Name', 'fcc'),LearnDash_Custom_Label::get_label('course'));
        ?>
						</th>
						<th>
					<?php echo __('Actual Price', 'fcc');
        ?>
						</th>
						<th>
					<?php echo __('Commission Price', 'fcc');
        ?>
						</th>

					</tr>
		<?php do_action('wdm_fcc_commission_report_table_header', $course_author_id);
        ?>
				</thead>
				<tbody>
		<?php
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_content like '%[wdm_course_creation]%' AND post_status like 'publish'";
        $course_result = $wpdb->get_var($sql);
        $link = get_permalink($course_result);
        $sql = "SELECT * FROM {$wpdb->prefix}wdm_course_author_commission WHERE user_id = $course_author_id";
        $results = $wpdb->get_results($sql);

        if (!empty($results)) {
            $amount_paid = 0;
            foreach ($results as $k => $v) {
                $amount_paid += $v->commission_price;
                ?>
							<tr>
								<td><center>
				<?php if (is_super_admin()) {
    ?>
								<a href="<?php echo(is_super_admin() ? site_url('wp-admin/post.php?post='.$v->order_id.'&action=edit') : '#');
    ?>" target="<?php echo(is_super_admin() ? '_new_blank' : '');
    ?>"><?php echo $v->order_id;
    ?></a>

						<?php

} else {
    echo $v->order_id;
}
                ?>
						</center></td>
						<td><center>
							<?php if (is_super_admin()) {
    ?><a target="_new_blank"href="<?php echo site_url('wp-admin/post.php?post='.$v->product_id.'&action=edit');
    ?>"><?php echo get_the_title($v->product_id);
    ?>
							<?php

} else {
    ?>
								<a target="_new_blank"href="<?php echo add_query_arg(array('courseid' => $v->product_id, 'redirect' => 1), $link);
    // ?>"><?php echo get_the_title($v->product_id);
    ?></a>
							<?php
}
                ?></a></center></td>
						<td><center><?php echo $v->actual_price;
                ?></center></td>
						<td><center><?php echo $v->commission_price;
                ?></center></td>

						</tr>
							<?php

            }
        }
        do_action('wdm_fcc_commission_report_table', $course_author_id);
        ?>
				</tbody>
				<tfoot >
							<?php
                            if (!empty($results)) {
                                //echo 'amount paid before '.$amount_paid.'<br>';
                                $paid_total = get_user_meta($course_author_id, 'wdm_total_amount_paid', true);
                                if ($paid_total == '') {
                                    $paid_total = 0;
                                }
                                //echo $paid_total;
                                //echo $amount_paid.'   '.$paid_total;
                                $amount_paid = round(($amount_paid - $paid_total), 2);
                                $amount_paid = max($amount_paid, 0);
                                //echo 'amount paid after '.$amount_paid;
                                ?>
						<tr>
							<td></td>
							<th style="color:black;font-weight: bold;">
			<?php echo __('Paid Earnings', 'fcc');
                                ?>
							</th>
							<td><span id="wdm_total_amount_paid"><?php echo $paid_total;
                                ?></span></a></td>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<th style="color:black;font-weight: bold;">
			<?php echo __('Unpaid Earnings', 'fcc');
                                ?>
							</th>
							<td>

								<span id="wdm_amount_paid"><?php echo $amount_paid;
                                ?></span>    <?php if ($amount_paid != 0 && is_super_admin()) {
    ?>
									<a href="#" class="button-primary" id="wdm_pay_amount"><?php echo __('Pay', 'fcc');
    ?></a>
			<?php
}
                                ?>
							</td>
							<td></td>
						</tr>

		<?php
                            }
        ?>

				</tfoot>
			</table>
		</div>
		<!-- popup div starts -->
		<div id="popUpDiv" style="display: none; top: 627px; left: 17%;">
			<div style="clear:both"></div>
			<table class="widefat" id="wdm_tbl_staff_mail">
				<thead>
					<tr>
						<th colspan="2">
							<strong><?php echo __('Transaction', 'fcc');
        ?></strong>

				<p id="wdm_close_pop" colspan="1" onclick="popup( 'popUpDiv' )"><span>X</span></p>
				</th>
				</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<strong><?php echo __('Paid Earnings', 'fcc');
        ?></strong>
						</td>
						<td>
							<input type="number" id="wdm_total_amount_paid_price" value="" readonly="readonly">
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo __('Unpaid Earnings', 'fcc');
        ?></strong>
						</td>
						<td>
							<input type="number" id="wdm_amount_paid_price" value="" readonly="readonly">
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo __('Enter amount', 'fcc');
        ?></strong>
						</td>
						<td>
							<input type="number" id="wdm_pay_amount_price" value="" >
						</td>
					</tr>
		<?php do_action('wdm_fcc_commisssion_report_popup_table', $course_author_id);
        ?>
					<tr>
						<td colspan="2">
							<input type="hidden" id="course_author_id" value="<?php echo $course_author_id;
        ?>">
							<input class="button-primary" type="button" name="wdm_btn_send_mail" value="<?php echo __('Pay', 'fcc');
        ?>" id="wdm_pay_click"><img src="<?php echo plugins_url('/images/ajax-loader.gif', dirname(dirname(__FILE__)));
        ?>" style="display: none" class="wdm_ajax_loader">
						</td>
					</tr>
				</tbody>
			</table>
		</div> <!-- popup div ends -->
		<?php

    }

    /*
     * Update user meta of course_author for amount paid
     *
     */

    public function wdm_amount_paid_course_author()
    {
        if (!is_super_admin()) {
            die();
        }
        $course_author_id = filter_input(INPUT_POST, 'course_author_id', FILTER_SANITIZE_NUMBER_INT);
        if ($course_author_id == '') {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        }
        if (!wdm_is_course_author($course_author_id)) {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        }
        $total_paid = filter_input(INPUT_POST, 'total_paid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $amount_tobe_paid = filter_input(INPUT_POST, 'amount_tobe_paid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $enter_amount = filter_input(INPUT_POST, 'enter_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $user_course_author_total = get_user_meta($course_author_id, 'wdm_total_amount_paid', true);
        if ($user_course_author_total == '') {
            $user_course_author_total = 0;
        }
        if ($amount_tobe_paid == '' || $enter_amount == '') {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        }
        if ($total_paid != $user_course_author_total) {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        }
        if ($enter_amount > $amount_tobe_paid) {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        }
        global $wpdb;
        $sql = "SELECT commission_price FROM {$wpdb->prefix}wdm_course_author_commission WHERE user_id = $course_author_id";
        $results = $wpdb->get_col($sql);
        if (empty($results)) {
            echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
            die();
        } else {
            $validate_amount_tobe_paid = 0;
            foreach ($results as $k => $v) {
                $validate_amount_tobe_paid += $v;
            }
            $validate_amount_tobe_paid = round(($validate_amount_tobe_paid - $total_paid), 2);
            if ($validate_amount_tobe_paid != $amount_tobe_paid) {
                echo json_encode(array('error' => __('Oops something went wrong', 'fcc')));
                die();
            }
        }

        $new_paid_amount = round(($total_paid + $enter_amount), 2);
        update_user_meta($course_author_id, 'wdm_total_amount_paid', $new_paid_amount);

        /*
         * course_author_id is id of the course_author
         * enter_amount is amount entered by admin to pay
         * total_paid is the total amount paid by admin to insturctor before current transaction
         * amount_tobe_paid is the amount required to be paid by admin
         * new_paid_amount is the total amount paid to course_author after current transaction
         */
        do_action('wdm_fcc_commission_amount_paid', $course_author_id, $enter_amount, $total_paid, $amount_tobe_paid, $new_paid_amount);
        $new_amount_tobe_paid = round(($amount_tobe_paid - $enter_amount), 2);

        $data = array(
            'amount_tobe_paid' => $new_amount_tobe_paid,
            'total_paid' => $new_paid_amount,
        );
        echo json_encode(array('success' => $data));
        die();
    }

    /*
     * Export functionality for admin as well as course_author
     *
     */

    public function wdm_fcc_export_commission_report()
    {
        if (isset($_GET[ 'wdm_fcc_commission_report_frontend' ]) && $_GET[ 'wdm_fcc_commission_report_frontend' ] == 'wdm_fcc_commission_report_frontend') {
            global $wpdb;
            $course_author_id = $_REQUEST[ 'wdm_course_author_id' ];
            $user_data = get_user_by('id', $course_author_id);
            //echo '<pre>';print_R($user_data);echo '</pre>';
            $start_date = isset($_GET[ 'start_date' ]) ? $_GET['start_date'] : '';
            $end_date = isset($_GET[ 'end_date' ]) ? $_GET['end_date'] : '';
            $sql = "SELECT * FROM {$wpdb->prefix}wdm_course_author_commission WHERE user_id=$course_author_id";
            if ($start_date != '') {
                $start_date = Date('Y-m-d', strtotime($start_date));
                $sql .= " AND transaction_time >='$start_date 00:00:00'";
            }
            if ($end_date != '') {
                $end_date = Date('Y-m-d', strtotime($end_date));
                $sql .= " AND transaction_time <='$end_date 23:59:59'";
            }
            $results = $wpdb->get_results($sql);
            //echo '<pre>';print_R($results);echo '</pre>';
            $course_progress_data = array();
            $amount_paid = 0;
            if (empty($results)) {
                $row = array(
                    'Status' => __('No data found', 'fcc'),
                );
                $course_progress_data[] = $row;
            } else {
                foreach ($results as $k => $v) {
                    $row = array(
                        'order id' => $v->order_id,
                        'course_author name' => $user_data->display_name,
                        'actual price' => $v->actual_price,
                        'commission price' => $v->commission_price,
                        'product name' => get_the_title($v->product_id),
                        'transaction time' => $v->transaction_time,
                    );
                    $amount_paid = $amount_paid + $v->commission_price;
                    $course_progress_data[] = $row;
                }
                $paid_total = get_user_meta($course_author_id, 'wdm_total_amount_paid', true);
                if ($paid_total == '') {
                    $paid_total = 0;
                }
                $amount_paid = round(($amount_paid - $paid_total), 2);
                $amount_paid = max($amount_paid, 0);
                if ($start_date == '' && $end_date == '') {
                    $row = array(
                    'order id' => __('Paid Earnings', 'fcc'),
                    'course_author name' => $paid_total,
                    'actual price' => '',
                    'commission price' => '',
                    'product name' => '',
                    'transaction time' => '',
                );
                    $course_progress_data[] = $row;
                    $row = array(
                    'order id' => __('Unpaid Earnings', 'fcc'),
                    'course_author name' => $amount_paid,
                    'actual price' => '',
                    'commission price' => '',
                    'product name' => '',
                    'transaction time' => '',
                );
                    $course_progress_data[] = $row;
                }
            }
            //echo '<pre>';print_R($course_progress_data);echo '</pre>';exit;
            if (file_exists((dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php')) {
                require_once(dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php';
                $csv = new fcclmsParseCSV();

                $csv->output(true, 'commission_report.csv', $course_progress_data, array_keys(reset($course_progress_data)));

                die();
            }
        }
    }

    /*
     * Export tab for insturctor and admin
     *
     */

    public function wdm_course_author_third_tab()
    {
        if (!is_super_admin()) {
            $course_author_id = get_current_user_id();
        } else {
            $args = array('fields' => array('ID', 'display_name'), 'role' => 'wdm_course_author');
            $course_authors = get_users($args);
            //echo '<pre>';print_R($course_authors);echo '</pre>';
            $course_author_id = '';
            if (isset($_REQUEST[ 'wdm_course_author_id' ])) {
                if ($_REQUEST[ 'wdm_course_author_id' ] == '-1') {
                    $course_author_id = '-1';
                } else {
                    $course_author_id = $_REQUEST[ 'wdm_course_author_id' ];
                }
            }
            if (empty($course_authors)) {
                echo '<h3>'.sprintf(__('No %s Author found', 'fcc'),LearnDash_Custom_Label::get_label('course')).'</h3>';

                return;
            }
        }
        wp_enqueue_script('wdm-jquery-script', plugins_url('js/jquery.js', dirname(dirname(__FILE__))));
        wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
        wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))), array('wdm-jquery-script'));
        wp_localize_script('wdm-datatable-script', 'wdm_datatable_object',
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn'  => __('Previous', 'fcc'),
                'wdm_next_btn'  => __('Next', 'fcc'),
                'wdm_search_bar'    => __('Search','fcc'),
                'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table'   => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching'   => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf( __('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );
        $url = plugins_url('/js/jquery-ui.js', dirname(dirname(__FILE__)));
        wp_enqueue_script('wdm-date-js', $url, array('jquery'), true);
        $url = plugins_url('/css/jquery-ui.css', dirname(dirname(__FILE__)));
        wp_enqueue_style('wdm-date-css', $url);
        wp_enqueue_script('wdm-datepicker-js', plugins_url('/js/wdm_datepicker.js', dirname(dirname(__FILE__))), array('jquery'));
        $translate_array=array(
            'message'=>__('Start date cannot be greater than end date','fcc')
        );
        wp_localize_script('wdm-datepicker-js','wdm_datepicker',$translate_array);
        $start_date = isset($_POST[ 'wdm_start_date' ]) ? $_POST[ 'wdm_start_date' ] : '';
        $end_date = isset($_POST[ 'wdm_end_date' ]) ? $_POST[ 'wdm_end_date' ] : '';
        ?>
		<form method="post" action="<?php echo is_admin() ? '?page=course_author&tab=export' : '#';
        ?>">
			<table>
		<?php if (is_super_admin()) {
    ?>
					<tr>
						<th style="float:left;"><?php echo sprintf(__('Select %s Author:', 'fcc'),LearnDash_Custom_Label::get_label('course'));
    ?></th>
						<td>
							<select name="wdm_course_author_id">
								<option value="-1"><?php echo __('All', 'fcc');
    ?></option>
			<?php foreach ($course_authors as $course_author) {
    ?>
									<option value="<?php echo $course_author->ID;
    ?>" <?php echo(($course_author_id == $course_author->ID) ? 'selected' : '');
    ?>><?php echo $course_author->display_name;
    ?></option>

			<?php
}
    ?>
							</select>
						</td>
					</tr>
		<?php
}
        ?>
				<tr>
					<th style="float:left;"><?php echo __('Start Date:', 'fcc');
        ?></th>
					<td>
						<input type="text" name="wdm_start_date" id="wdm_start_date" value="<?php echo $start_date;
        ?>">
					</td>
				</tr>
				<tr>
					<th style="float:left;"><?php echo __('End Date:', 'fcc');
        ?></th>
					<td>
						<input type="text" name="wdm_end_date" id="wdm_end_date" value="<?php echo $end_date;
        ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" class="button-primary" value="<?php echo __('Submit', 'fcc');
        ?>" id="wdm_submit">
					</td>
				</tr>
			</table>
		</form>
		<?php
        //}
        if ($course_author_id != '') {
            $this->wdm_fcc_export_csv_report($course_author_id, $start_date, $end_date);
        }
    }

    /*
     * Report filtered by course_author, start and end date
     */

    public function wdm_fcc_export_csv_report($course_author_id, $start_date, $end_date)
    {
        global $wpdb;
        ?>
		<script>
		    jQuery( 'document' ).ready( function () {
		        jQuery( '#wdm_fcc_report_tbl' ).dataTable();
		    } );
		</script>
		<div id="reports_table_div" style="padding-right: 5px">

							<?php
                            //echo dirname( dirname( dirname( __FILE__ ) ) ) . '/includes/parsecsv.lib.php';
                            if (file_exists((dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php')) {
                                if (is_admin()) {
                                    $url = admin_url('admin.php?page=course_author&tab=export&wdm_fcc_export_report=wdm_export_report&wdm_course_author_id='.$course_author_id.'&start_date='.$start_date.'&end_date='.$end_date);
                                } else {
                                    $url = add_query_arg(array('wdm_fcc_commission_report_frontend' => 'wdm_fcc_commission_report_frontend', 'wdm_course_author_id' => $course_author_id, 'start_date' => $start_date, 'end_date' => $end_date), get_permalink());
                                }
                                ?>
				<a href="<?php echo $url;
                                ?>" class="button button-primary" style="float:right"><?php echo __('Export CSV', 'fcc');
                                ?></a>
							<?php
                            }
        ?>
			<!--Table shows Name, Email, etc-->
			<br><br>
			<table  id="wdm_fcc_report_tbl">
				<thead>
					<tr>
						<th data-sort-initial="descending" data-class="expand">
					<?php echo __('Order ID', 'fcc');
        ?>
						</th>
						<th data-sort-initial="descending" data-class="expand">
					<?php echo __('Username', 'fcc');
        ?>
						</th>
						<th data-sort-initial="descending" data-class="expand">
					<?php echo sprintf(__('Product / %s Name', 'fcc'),LearnDash_Custom_Label::get_label('course'));
        ?>
						</th>
						<th>
					<?php echo __('Actual Price', 'fcc');
        ?>
						</th>
						<th>
					<?php echo __('Commission Price', 'fcc');
        ?>
						</th>

					</tr>
					<?php do_action('wdm_fcc_commission_report_table_header', $course_author_id);
        ?>
				</thead>
				<tbody>
					<?php
                    $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_content like '%[wdm_course_creation]%' AND post_status like 'publish'";
        $course_result = $wpdb->get_var($sql);
        $link = get_permalink($course_result);
        $sql = "SELECT * FROM {$wpdb->prefix}wdm_course_author_commission WHERE 1=1 ";
                    //echo $start_date;exit;
                    if ($course_author_id != '-1') {
                        $sql .= "AND user_id = $course_author_id ";
                    }
        if ($start_date != '') {
            $start_date = Date('Y-m-d', strtotime($start_date));
            $sql .= "AND transaction_time >='$start_date 00:00:00'";
        }
        if ($end_date != '') {
            $end_date = Date('Y-m-d', strtotime($end_date));
            $sql .= " AND transaction_time <='$end_date 23:59:59'";
        }
                    //echo $sql;exit;
                    $results = $wpdb->get_results($sql);

        if (!empty($results)) {
            //$amount_paid = 0;
                        foreach ($results as $k => $v) {
                            $user_details = get_user_by('id', $v->user_id);
                            //echo '<pre>';print_R($user_details);echo '</pre>';exit;
                            //$amount_paid += $v->commission_price;
                            ?>
							<tr>
								<td><center>
						<?php if (is_super_admin()) {
    ?>
								<a href="<?php echo(is_super_admin() ? site_url('wp-admin/post.php?post='.$v->order_id.'&action=edit') : '#');
    ?>" target="<?php echo(is_super_admin() ? '_new_blank' : '');
    ?>"><?php echo $v->order_id;
    ?></a>

						<?php

} else {
    echo $v->order_id;
}
                            ?>
						</center>
						</td>
						<td><center><?php echo $user_details->display_name;
                            ?></center></td>
						<td><center><?php if (is_super_admin()) {
    ?>
								<a target="_new_blank"href="<?php echo site_url('wp-admin/post.php?post='.$v->product_id.'&action=edit');
    ?>"><?php echo get_the_title($v->product_id);
    ?></a>
				<?php

} else {
    $course_product_link=add_query_arg(array('courseid' => $v->product_id, 'redirect' => 1), $link);
    $course_product_link = get_post_type($v->product_id)=='product' ? get_permalink($v->product_id) : $course_product_link;
    ?>
					<a target="_new_blank"href="<?php echo $course_product_link;
    ?>"><?php echo get_the_title($v->product_id);
    ?></a>
				<?php
}
                            ?>
						</center></td>
						<td><center><?php echo $v->actual_price;
                            ?></center></td>
						<td><center><?php echo $v->commission_price;
                            ?></center></td>

						</tr>
				<?php

                        }
        }
        do_action('wdm_fcc_commission_report_table', $course_author_id);
        ?>
				</tbody>

			</table>
		</div>
		<br>
		<?php
        if (file_exists((dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php')) {
            if (is_admin()) {
                $url = admin_url('admin.php?page=course_author&tab=export&wdm_fcc_export_report=wdm_export_report&wdm_course_author_id='.$course_author_id.'&start_date='.$start_date.'&end_date='.$end_date);
            } else {
                $url = add_query_arg(array('wdm_fcc_commission_report_frontend' => 'wdm_fcc_commission_report_frontend', 'wdm_course_author_id' => $course_author_id, 'start_date' => $start_date, 'end_date' => $end_date), get_permalink());
            }
            ?>
			<a href="<?php echo $url;
            ?>" class="button button-primary" style="float:right"><?php echo __('Export CSV', 'fcc');
            ?></a>
		<?php

        }
    }

    /*
     * Export data filter wise
     */

    public function wdm_fcc_export_csv_date_filter()
    {
        if (isset($_GET[ 'wdm_fcc_export_report' ]) && $_GET[ 'wdm_fcc_export_report' ] == 'wdm_export_report') {
            global $wpdb;
            $course_author_id = $_REQUEST[ 'wdm_course_author_id' ];
            $start_date = $_GET[ 'start_date' ];
            $end_date = $_GET[ 'end_date' ];
            //echo '<pre>';print_R($user_data);echo '</pre>';
            $sql = "SELECT * FROM {$wpdb->prefix}wdm_course_author_commission WHERE 1=1";
            if ($course_author_id != '' && $course_author_id != '-1') {
                $sql .= ' AND user_id='.$course_author_id;
            }
            if ($start_date != '') {
                $start_date = Date('Y-m-d', strtotime($start_date));
                $sql .= " AND transaction_time >='$start_date 00:00:00'";
            }
            if ($end_date != '') {
                $end_date = Date('Y-m-d', strtotime($end_date));
                $sql .= " AND transaction_time <='$end_date 23:59:59'";
            }
            //echo $sql;
            $results = $wpdb->get_results($sql);
            //echo '<pre>';print_R($results);echo '</pre>';exit;
            $course_progress_data = array();
            $amount_paid = 0;
            if (empty($results)) {
                $row = array('Status' => __('No data found', 'fcc'));
                $course_progress_data[] = $row;
            } else {
                foreach ($results as $k => $v) {
                    $user_data = get_user_by('id', $v->user_id);
                    $row = array(
                        'order id' => $v->order_id,
                        'course_author name' => $user_data->display_name,
                        'actual price' => $v->actual_price,
                        'commission price' => $v->commission_price,
                        'product name' => get_the_title($v->product_id),
                        'transaction time' => $v->transaction_time,
                    );

                    $course_progress_data[] = $row;
                }
            }

            if (file_exists((dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php')) {
                require_once(dirname(dirname(__FILE__))).'/includes/parsecsv.lib.php';
                $csv = new fcclmsParseCSV();

                $csv->output(true, 'commission_report.csv', $course_progress_data, array_keys(reset($course_progress_data)));

                die();
            }
        }
    }

    public function wdm_enqueue_extension_style()
    {
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'course_author' && isset($_GET['tab']) && $_GET['tab'] == 'other_extensions') {
                wp_enqueue_style('wdm-extension-style', plugins_url('promotion/assets/css/extension.css', __FILE__));
            }
        }
    }
}

new WDM_FCC_COMM();
