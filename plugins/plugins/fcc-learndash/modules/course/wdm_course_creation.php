<?php
global $wpdb;
$is_ver_greater = version_compare(LEARNDASH_VERSION, "2.4.0", ">=");
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/';
if(isset($_GET['redirect'])){
	$back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}
if ( isset( $_GET[ 'courseid' ] ) ) {
	if(!is_numeric( $_GET[ 'courseid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table	 = $wpdb->posts;
	$id		 = $_GET[ 'courseid' ];

	$sql	 = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-courses' AND post_author = " . get_current_user_id();
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) == 0 ) {
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
}
$title											 = "";
$content										 = "";
$featured_image									 = "";
$sfwd_courses_course_materials					 = "";
$sfwd_courses_course_price_type					 = "";
$sfwd_courses_custom_button_url					 = "";
$sfwd_courses_course_price						 = "";
$course_price_billing_p3						 = "";
$course_price_billing_t3						 = "";
$sfwd_courses_course_access_list				 = "";
$sfwd_courses_lessons_per_page				 	 = "";
$sfwd_courses_custom_lessons_per_page			 = "";
$sfwd_courses_course_lesson_orderby				 = "";
$sfwd_courses_course_lesson_order				 = "";
$sfwd_courses_course_prerequisite_enabled		 = "";
$sfwd_courses_course_prerequisite				 = "";
$sfwd_courses_course_prerequisite_compare		 = "";
$sfwd_courses_course_points_enabled				 = "";
$sfwd_courses_course_points 					 = "";
$sfwd_courses_course_points_access 				 = "";
$sfwd_courses_course_disable_lesson_progression	 = "";
$sfwd_courses_expire_access						 = "";
$sfwd_courses_expire_access_days				 = "";
$sfwd_courses_expire_access_delete_progress		 = "";
$sfwd_courses_course_disable_content_table		 = "";
$menu_order										 = 0;
$cerficates = array();
$preview_url = '';
$sfwd_courses_certificate = "";
$table	 = $wpdb->prefix . "posts";
$sql	 = "SELECT ID FROM $table WHERE post_author = " . get_current_user_id() . " AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";
if ( isset( $_GET[ 'courseid' ] ) ) {
	$sql .= " AND ID != " . $_GET[ 'courseid' ];
}
$results	 = $wpdb->get_results( $sql );
$course_list = array();
if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$course_list[] = $v->ID;
	}
}
$sql	 = "SELECT ID FROM $table WHERE post_type like 'sfwd-certificates' AND post_status IN ('publish','draft')";
$results	 = $wpdb->get_results( $sql );
if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$cerficates[] = $v->ID;
	}
}

//Start: WordPress Categories & Tags----------------------------------------
$category	 = array();
$results=get_terms(array(
			'taxonomy' => 'category',
			'hide_empty' => false,
		));

if ( count( $results ) > 0 ) {
	foreach ( $results as $value ) {
		$category[ $value->term_taxonomy_id ] = $value->name;
	}
}

$results=get_terms(array(
			'taxonomy' => 'post_tag',
			'hide_empty' => false,
		));
$tag	 = array();

if ( count( $results ) > 0 ) {
	foreach ( $results as $value ) {
		$tag[ $value->term_taxonomy_id ] = $value->name;
	}
}

$selected_category	 = array();
if ( isset( $_GET[ 'courseid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'courseid' ], 'category');
	if ( count( $results ) > 0 ) {
		foreach ( $results as $value ) {
			$selected_category[] = $value->term_taxonomy_id;
		}
	}
}

$selected_tag	 = array();
if ( isset( $_GET[ 'courseid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'courseid' ], 'post_tag');
	if ( count( $results ) > 0 ) {
		foreach ( $results as $value ) {
			$selected_tag[] = $value->term_taxonomy_id;
		}
	}
}
//End: WordPress Categories & Tags--------------------------------------

//Start: LearnDash Categories-------------------------------------------
$ld_category	 = array();
$selected_ld_category	 = array();
$selected_ld_tag	 = array();
if ($is_ver_greater && class_exists('LearnDash_Settings_Section')) {
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_course_category',
					'hide_empty' => false,
				));

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_category[ $value->term_taxonomy_id ] = $value->name;
			}
		}
		if ( isset( $_GET[ 'courseid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'courseid' ], 'ld_course_category');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_category[] = $value->term_taxonomy_id;
				}
			}
		}
	}

	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_course_tag',
					'hide_empty' => false,
				));
		$ld_tag	 = array();

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_tag[ $value->term_taxonomy_id ] = $value->name;
			}
		}


		if ( isset( $_GET[ 'courseid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'courseid' ], 'ld_course_tag');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_tag[] = $value->term_taxonomy_id;
				}
			}
		}
	}
}
//End: LearnDash Categories & Tags--------------------------------------

if ( isset( $_GET[ 'courseid' ] ) ) {
	$id												 = $_GET[ 'courseid' ];
	$title											 = get_the_title( $id );
	$content_post									 = get_post( $id );
	$content										 = $content_post->post_content;
	$content										 = apply_filters( 'the_content', $content );
	$menu_order										 = $content_post->menu_order;
	$course_meta									 = maybe_unserialize( get_post_meta( $id, '_sfwd-courses' ) );
	//echo "<pre>";print_R($course_meta);echo "</pre>";
	$course_price_billing_p3						 = get_post_meta( $id, 'course_price_billing_p3', true );
	$course_price_billing_t3						 = get_post_meta( $id, 'course_price_billing_t3', true );
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_materials' ] ) ) {
		$sfwd_courses_course_materials					 = $course_meta[ 0 ][ 'sfwd-courses_course_materials' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_price_type' ] ) ) {
		$sfwd_courses_course_price_type					 = $course_meta[ 0 ][ 'sfwd-courses_course_price_type' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_custom_button_url' ] ) ) {
		$sfwd_courses_custom_button_url					 = $course_meta[ 0 ][ 'sfwd-courses_custom_button_url' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_price' ] ) ) {
		$sfwd_courses_course_price						 = $course_meta[ 0 ][ 'sfwd-courses_course_price' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_access_list' ] ) ) {
		$sfwd_courses_course_access_list				 = $course_meta[ 0 ][ 'sfwd-courses_course_access_list' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_per_page' ] ) ) {
		$sfwd_courses_lessons_per_page				 	 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_per_page' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_per_page_custom' ] ) ) {
		$sfwd_courses_custom_lessons_per_page			 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_per_page_custom' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_orderby' ] ) ) {
		$sfwd_courses_course_lesson_orderby				 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_orderby' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_order' ] ) ) {
		$sfwd_courses_course_lesson_order				 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_order' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite_enabled' ] ) ) {
		$sfwd_courses_course_prerequisite_enabled		 = $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite_enabled' ];
	}
	if (isset($course_meta[0]['sfwd-courses_course_prerequisite'])) {
		$sfwd_courses_course_prerequisite				 = $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite_compare' ] ) ) {
		$sfwd_courses_course_prerequisite_compare		 = $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite_compare' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_points_enabled' ] ) ) {
		$sfwd_courses_course_points_enabled 			 = $course_meta[ 0 ][ 'sfwd-courses_course_points_enabled' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_points' ] ) ) {
		$sfwd_courses_course_points		 				 = $course_meta[ 0 ][ 'sfwd-courses_course_points' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_points_access' ] ) ) {
		$sfwd_courses_course_points_access		 = $course_meta[ 0 ][ 'sfwd-courses_course_points_access' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_disable_lesson_progression' ] ) ) {
		$sfwd_courses_course_disable_lesson_progression	 = $course_meta[ 0 ][ 'sfwd-courses_course_disable_lesson_progression' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access' ] ) ) {
		$sfwd_courses_expire_access						 = $course_meta[ 0 ][ 'sfwd-courses_expire_access' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access_days' ] ) ) {
		$sfwd_courses_expire_access_days				 = $course_meta[ 0 ][ 'sfwd-courses_expire_access_days' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access_delete_progress' ] ) ) {
		$sfwd_courses_expire_access_delete_progress		 = $course_meta[ 0 ][ 'sfwd-courses_expire_access_delete_progress' ];
	}
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_disable_content_table' ] ) ) {
		$sfwd_courses_course_disable_content_table		 = $course_meta[ 0 ][ 'sfwd-courses_course_disable_content_table' ];
	}
	if(isset($course_meta[0]['sfwd-courses_certificate'])) {
		$sfwd_courses_certificate = $course_meta[0]['sfwd-courses_certificate'];
	}
	$preview_url = add_query_arg(array('wdm_preview'=>1),get_permalink($id));
}
?>
<?php //session_start();
//echo "<prE>";print_r($_SESSION);echo "</pre>";  ?>
<?php if ( isset( $_SESSION['update'] ) ) { ?>
	<?php if ( $_SESSION['update'] == 2 ) { ?>
		<div class="wdm-update-message"><?php echo sprintf(__('%s Updated Successfully.','fcc'), LearnDash_Custom_Label::get_label('course')); ?></div>
	<?php } else if ( $_SESSION['update'] == 1 ) { ?>
		<div class="wdm-update-message"><?php echo sprintf( __('%s Added Successfully.','fcc'), LearnDash_Custom_Label::get_label('course')); ?></div>

	<?php }
	unset($_SESSION['update']);
}
?>
<?php if (defined('WDM_ERROR')) { ?>

		<div class="wdm-error-message"><?php echo WDM_ERROR; ?>
		</div>

	

<?php
	
}
if(isset($_SESSION['wdm_error'])){
			if($_SESSION['wdm_error'] != '') {  ?>
				<div class="wdm-error-message"><?php echo $_SESSION['wdm_error']; ?>
		</div>
			<?php }
 unset($_SESSION['wdm_error']);
			} ?>
		<input type="button" value="<?php echo __('Back','fcc'); ?>" onclick="location.href = '<?php echo $back_url; ?>';" style="float: right;">
			<?php if($preview_url != ''){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo __('Preview','fcc'); ?>" style="float:right;margin-right: 2%;"onclick="window.open('<?php echo $preview_url; ?>')">
			<?php }
			?>
<br><br><br>
<div class="fcc-course-container">
	<div class="fcc-ajax-overlay" style="display: none;">
		<img src="<?php echo plugins_url('/images/ajax-loader.gif', FCC_PLUGIN_PATH); ?>" alt="Loading..."/>
	</div>
	<div id="sfwd-header" style="display: none !important;"></div>
	<form method="post" enctype="multipart/form-data" id="wdm_course_form">
	<input type="hidden" name="wdm_custom_post" value="<?php echo $this->course_id; ?>">
		<div id="accordion">
			<h3><?php _e('Content', 'fcc');?></h3>
			<div>
				<span><?php echo __('Title','fcc'); ?></span><br>
				<input type="text" name="title" style="width:100%;" value = "<?php echo $title; ?>"><br><br>
				<span><?php echo __('Description','fcc'); ?></span>
	<?php
	///$content	 = '';
	$editor_id = 'wdm_content';

	wp_editor( $content, $editor_id );

	// do_action('admin_print_scripts');
	//Start: WordPress Categories & tags-------------------------------------------


	if ($is_ver_greater) {
		if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_category' ) == 'yes') {
		?>

					<br>
					<?php if ( count( $category ) > 0 ) { ?>
						<span><?php echo __('Categories:','fcc'); ?></span><br>
						<select name="category[]" multiple>
			<?php foreach ( $category as $k => $v ) { ?>
							<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>

						<?php }//End foreach loop ?>
							</select>
						<br>
					<?php }//End if count($category) condition
		}//End if wp_post_category condition
		if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_tag' ) == 'yes') {
		?>
					<br>
						<span><?php echo __('Tags:','fcc'); ?></span><br>
						<div id='wdm_tag_list'>
							<select name="tag[]" multiple>
							<?php if ( count( $tag ) > 0 ) { ?>
			<?php foreach ( $tag as $k => $v ) { ?>
								<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_tag ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
			<?php }//End foreach loop ?>
						<?php }//End if count($tag) condition ?>
							</select>
						</div>
						<br>
					<input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc');?>">
					<br>
		<?php }//End if wp_post_tag condition?>
		<!--End: WordPress Categories & tags________________________________________-->


		<!--Start: LearnDash Categories & tags________________________________________-->
		<?php
		if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {
		?>
						<br>
						<?php if ( count( $ld_category ) > 0 ) { ?>
							<span><?php echo sprintf(__('%s Categories:','fcc'), LearnDash_Custom_Label::get_label('course')); ?></span><br>
							<select name="ld_category[]" multiple>
				<?php foreach ( $ld_category as $k => $v ) { ?>
								<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_ld_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>

							<?php } ?>
								</select>
							<br>
						<?php } ?>
						<br>
		<?php }//End if ld_course_category condition
		if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) == 'yes') {
		?>
							<span><?php echo sprintf(__('%s Tags:', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></span><br>
							<div id='wdm_ld_tag_list'>
								<select name="ld_tag[]" multiple>
								<?php if ( count( $ld_tag ) > 0 ) { ?>
				<?php foreach ( $ld_tag as $k => $v ) { ?>
									<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_ld_tag ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
				<?php } ?>
							<?php } ?>
								</select>
							</div>
							<br>
						<input type='text' name='wdm_ld_tag' id='wdm_ld_tag'><input type='button' id='wdm_add_ld_tag' data-cat_type="course" value="<?php echo sprintf(__('Add %s Tag', 'fcc'), LearnDash_Custom_Label::get_label('course'));?>">
						<br><br>
		<?php }//End if ld_course_tag condition
	}else{
		?>
		<br>
					<?php if ( count( $category ) > 0 ) { ?>
						<span><?php echo __('Categories:','fcc'); ?></span><br>
						<select name="category[]" multiple>
			<?php foreach ( $category as $k => $v ) { ?>
							<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>

						<?php }//End foreach loop ?>
							</select>
						<br>
					<?php }//End if count($category) condition

					if ( count( $tag ) > 0 ) { ?>
						<span><?php echo __('Tags:','fcc'); ?></span><br>
						<div id='wdm_tag_list'>
							<select name="tag[]" multiple>
							<?php if ( count( $tag ) > 0 ) { ?>
			<?php foreach ( $tag as $k => $v ) { ?>
								<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_tag ) ? 'selected' : ''; ?>><?php echo $v; ?></option>
			<?php } ?>
						<?php } ?>
							</select>
						</div>
						<br>
					<?php }?>
					<input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc');?>">
		<?php
	}
				do_action('fcc_add_to_course_content_box');
	?>
	<!--End: LearnDash Categories & tags________________________________________-->

				<div>
					<label for="order_number"><?php _e('Order','fcc');?></label>
					<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
				</div>
			</div>
			<h3><?php echo __('Features','fcc'); ?></h3>
			<div>
				<?php if (version_compare(LEARNDASH_VERSION, "3.0", ">=")) : ?>
					<?php $this->loadMetaBoxes(); ?>
					<?php foreach ($this->metaboxes as $metabox): ?>
					<div class="panel panel-bordered">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo fccGetMetaboxProperty($metabox, 'settings_section_label');?></h3>
							<div class="panel-actions">
							<a class="panel-action icon md-minus" aria-expanded="false" data-toggle="panel-collapse"
								aria-hidden="true"></a>
							</div>
						</div>
						<div class="panel-body">
							<?php echo $metabox->show_meta_box(get_post($this->course_id)); ?>
						</div>
					</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="sfwd sfwd_options sfwd-courses_settings">
						<div class="sfwd_input " id="sfwd-courses_course_materials">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_materials_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Materials','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><textarea name="sfwd-courses_course_materials" rows="2" cols="57"><?php echo $sfwd_courses_course_materials; ?></textarea></div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_materials_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Options for %s Materials', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_course_price_type">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_type_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Price Type','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<select name="sfwd-courses_course_price_type">
										<option value="open" <?php echo ($sfwd_courses_course_price_type == 'open' ) ? 'selected' : ''; ?>><?php echo __('Open','fcc'); ?></option>
										<option value="closed" <?php echo ($sfwd_courses_course_price_type == 'closed' ) ? 'selected' : ''; ?>><?php echo __('Closed','fcc'); ?></option>
										<option value="free" <?php echo ($sfwd_courses_course_price_type == 'free' ) ? 'selected' : ''; ?>><?php echo __('Free','fcc'); ?></option>
										<option value="paynow" <?php echo ($sfwd_courses_course_price_type == 'paynow' ) ? 'selected' : ''; ?>><?php echo __('Buy Now','fcc'); ?></option>
										<option value="subscribe" <?php echo ($sfwd_courses_course_price_type == 'subscribe' ) ? 'selected' : ''; ?>><?php echo __('Recurring','fcc'); ?></option>
									</select>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_type_tip"><label class="sfwd_help_text"><?php echo __('Is it open to all, free join, one time purchase, or a recurring subscription?','fcc'); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_custom_button_url" style="display: none;">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_custom_button_url_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Custom Button URL','fcc'); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_custom_button_url" type="text" size="57" placeholder="Optional" value="<?php echo $sfwd_courses_custom_button_url; ?>">
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_custom_button_url_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Entering a URL in this field will enable the "%s" button. The button will not display if this field is left empty', 'fcc'), LearnDash_Custom_Label::get_label('button_take_this_course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_course_price" style="display: none;">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Price','fcc'); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_course_price" type="number" size="57" value="<?php echo $sfwd_courses_course_price; ?>">
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Enter %s price here. Leave empty if the %s is free.', 'fcc'), LearnDash_Custom_Label::get_label('course'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_course_price_billing_cycle" style="display: none;">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_billing_cycle_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Billing Cycle','fcc'); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<input name="course_price_billing_p3" type="text" value="<?php echo $course_price_billing_p3; ?>" size="2"> 
									<select class="select_course_price_billing_p3" name="course_price_billing_t3">
										<option value="D" <?php echo ($course_price_billing_t3 == 'D' ) ? 'selected' : ''; ?>><?php echo __('day(s)','fcc'); ?></option>
										<option value="W" <?php echo ($course_price_billing_t3 == 'W' ) ? 'selected' : ''; ?>><?php echo __('week(s)','fcc'); ?></option>
										<option value="M" <?php echo ($course_price_billing_t3 == 'M' ) ? 'selected' : ''; ?>><?php echo __('month(s)','fcc'); ?></option>
										<option value="Y" <?php echo ($course_price_billing_t3 == 'Y' ) ? 'selected' : ''; ?>><?php echo __('year(s)','fcc'); ?></option>
									</select>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_billing_cycle_tip"><label class="sfwd_help_text"><?php echo __('Billing Cycle for the recurring payments in case of a subscription.','fcc'); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_course_access_list">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_access_list_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Access List','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><textarea name="sfwd-courses_course_access_list" rows="2" cols="57"><?php echo $sfwd_courses_course_access_list; ?></textarea></div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_access_list_tip"><label class="sfwd_help_text"><?php echo sprintf( __('This field is auto-populated with the UserIDs of those who have access to this %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>

						<div class="sfwd_input " id="sfwd-courses_course_lesson_per_page">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_per_page_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Per Page','fcc'), LearnDash_Custom_Label::get_label('lessons')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<select name="sfwd-courses_course_lesson_per_page">
										<option <?php echo $sfwd_courses_lessons_per_page=='' ? 'selected' : ''; ?> value=""><?php echo __("Use Default ( 25 )","fcc") ?> </option>
										<option <?php echo $sfwd_courses_lessons_per_page=='CUSTOM' ? 'selected' : ''; ?> value="CUSTOM"><?php echo __("Custom","fcc") ?> </option>
									</select>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_per_page_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Choose the per page of %s in this %s.', 'fcc'), LearnDash_Custom_Label::get_label('lessons'),LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>

						<div class="sfwd_input " id="sfwd-courses_course_lesson_per_page_custom">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_per_page_custom_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Custom %s Per Page','fcc'), LearnDash_Custom_Label::get_label('lessons')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<input type="text" name="sfwd-courses_course_lesson_per_page_custom" value="<?php echo $sfwd_courses_custom_lessons_per_page; ?>" />
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_per_page_custom_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Enter %s per page value. Set to zero for no paging', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>

						<?php
						if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=")) {
							?>
							<div class="sfwd_input " id="sfwd-courses_course_prerequisite_enabled">
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_prerequisite_enabled_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Enable %s Prerequisites', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div"><input name="sfwd-courses_course_prerequisite_enabled" type="checkbox" <?php echo ($sfwd_courses_course_prerequisite_enabled == 'on' ) ? 'checked' : ''; ?>>
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_prerequisite_enabled_tip"><label class="sfwd_help_text"><?php echo __('Leave this field unchecked if prerequisite not used.','fcc'); ?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
							<div class="sfwd_input " id="sfwd-courses_course_prerequisite" <?php echo ($sfwd_courses_course_prerequisite_enabled == 'on' ) ? '' : 'style="display: none;"'; ?>>
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_prerequisite_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Prerequisites','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div">
										<select name="sfwd-courses_course_prerequisite[]" multiple="true">
											<option value="0"><?php echo sprintf(__('-- Select a %s --', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></option>
											<?php if ( count( $course_list ) > 0 ) { ?>
												<?php foreach ( $course_list as $k => $v ) { ?>
													<option value="<?php echo $v; ?>" <?php echo @((in_array($v, $sfwd_courses_course_prerequisite)) ? 'selected' : ''); ?>><?php echo get_the_title( $v ); ?></option>	
												<?php } ?>
											<?php } ?>
										</select>
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_prerequisite_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Select a %s as prerequisites to view this %s', 'fcc'), LearnDash_Custom_Label::get_label('course'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
							<div class="sfwd_input " id="sfwd-courses_course_prerequisite_compare" <?php echo ($sfwd_courses_course_prerequisite_enabled == 'on' ) ? '' : 'style="display: none;"'; ?>>
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_prerequisite_compare_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Prerequisites Compare','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div">
										<select name="sfwd-courses_course_prerequisite_compare">
											<option value="ANY"><?php _e('ANY (default) - The student must complete at least one of the prerequisites', 'fcc');?></option>
											<option value="ALL" <?php echo ($sfwd_courses_course_prerequisite_compare == 'ALL' ) ? 'selected' : ''; ?>><?php _e('ALL - The student must complete all the prerequisites', 'fcc');?></option>
										</select>
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_prerequisite_compare_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Select how to compare the selected prerequisite %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
							<div class="sfwd_input " id="sfwd-courses_course_points_enabled">
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_points_enabled_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Enable %s Points', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div"><input name="sfwd-courses_course_points_enabled" type="checkbox" <?php echo ($sfwd_courses_course_points_enabled == 'on' ) ? 'checked' : ''; ?>>
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_points_enabled_tip"><label class="sfwd_help_text"><?php echo __('Leave this field unchecked if points not used.','fcc'); ?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
							<div class="sfwd_input " id="sfwd-courses_course_points" <?php echo ($sfwd_courses_course_points_enabled == 'on' ) ? '' : 'style="display: none;"'; ?>>
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_points_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Points','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div"><input name="sfwd-courses_course_points" type="number" size="57" value="<?php echo $sfwd_courses_course_points; ?>">
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_points_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Enter the number of points a user will receive for this %s','fcc'),LearnDash_Custom_Label::get_label('course'));?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
							<div class="sfwd_input " id="sfwd-courses_course_points_access" <?php echo ($sfwd_courses_course_points_enabled == 'on' ) ? '' : 'style="display: none;"'; ?>>
								<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_points_access_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Points Access','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
								<span class="sfwd_option_input">
									<div class="sfwd_option_div"><input name="sfwd-courses_course_points_access" type="number" size="57" min="0" value="<?php echo $sfwd_courses_course_points_access; ?>">
									</div>
									<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_points_access_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Enter the number of points a user must have to access this %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
								</span>
								<p style="clear:left"></p>
							</div>
						<?php }else{
						?>
						<div class="sfwd_input " id="sfwd-courses_course_prerequisite">
						<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_prerequisite_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Prerequisites','fcc'), LearnDash_Custom_Label::get_label('course') ); ?></label></a></span>
						<span class="sfwd_option_input">
							<div class="sfwd_option_div">
								<select name="sfwd-courses_course_prerequisite">
									<option value="0"><?php echo sprintf( __('-- Select a %s --', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></option>
									<?php if ( count( $course_list ) > 0 ) { ?>
										<?php foreach ( $course_list as $k => $v ) { ?>
											<option value="<?php echo $v; ?>" <?php echo ($sfwd_courses_course_prerequisite == $v ) ? 'selected' : ''; ?>><?php echo get_the_title( $v ); ?></option>	
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_prerequisite_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Select a %s as prerequisites to view this %s', 'fcc'), LearnDash_Custom_Label::get_label('course'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
						</span>
						<p style="clear:left"></p>
						</div>
						<?php
						}
						?>
						<div class="sfwd_input " id="sfwd-courses_course_disable_lesson_progression">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_disable_lesson_progression_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Disable %s Progression', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_course_disable_lesson_progression" type="checkbox" <?php echo ($sfwd_courses_course_disable_lesson_progression != '' ) ? 'checked' : ''; ?>>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_disable_lesson_progression_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Disable the feature that allows attempting %s only in allowed order', 'fcc'), LearnDash_Custom_Label::get_label('lessons')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_certificate">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-courses_certificate_tip');"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>"><label class="sfwd_label textinput"><?php echo __('Associated Certificate','fcc'); ?></label></a></span>
							<span class="sfwd_option_input"><div class="sfwd_option_div">
								<select name="sfwd-courses_certificate">
									<option value="0"><?php echo __('-- Select a Certificate --','fcc'); ?></option>
									<?php if(!  empty($cerficates)){
										foreach($cerficates as $k=>$v){
										?>
										<option value="<?php echo $v; ?>" <?php echo ($v == $sfwd_courses_certificate) ? 'selected' : ''; ?>><?php echo get_the_title($v); ?></option>
										<?php 
										}
									} ?>
								</select>
							</div>
							<div class="sfwd_help_text_div" style="display: none;" id="sfwd-courses_certificate_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Select a certificate to be awarded upon %s completion (optional).', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div></span><p style="clear:left"></p></div>						
						</div>
				<?php endif; ?>

			</div>
			<?php if (version_compare(LEARNDASH_VERSION, "3.0", "<=")) : ?>
				<h3><?php echo __('Settings','fcc'); ?></h3>
				<div>
					<span><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" id="featured_image"></span>
					<?php if ( isset( $_GET[ 'courseid' ] ) && has_post_thumbnail($_GET['courseid']) ) { ?>
						<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
					<?php } ?>
					<br><br>
					<div class="sfwd_input " id="sfwd-courses_expire_access">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php _e('Expire Access','fcc');?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_expire_access" type="checkbox" <?php echo ($sfwd_courses_expire_access != '' ) ? 'checked' : ''; ?>>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_tip"><label class="sfwd_help_text"><?php echo __('Leave this field unchecked if access never expires','fcc'); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
					<div class="sfwd_input " id="sfwd-courses_expire_access_days" style="display: none;">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_days_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Expire Access After (days)','fcc'); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_expire_access_days" type="number" size="57" value="<?php echo $sfwd_courses_expire_access_days; ?>">
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_days_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Enter the number of days a user has access to this %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_expire_access_delete_progress" style="display: none;">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_delete_progress_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Delete %s and %s Data After Expiration', 'fcc'), LearnDash_Custom_Label::get_label('course'),LearnDash_Custom_Label::get_label('quiz')); ?></label></a></span>

							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_expire_access_delete_progress" type="checkbox" <?php echo ($sfwd_courses_expire_access_delete_progress != '' ) ? 'checked' : ''; ?>>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_delete_progress_tip"><label class="sfwd_help_text"><?php echo sprintf(__("Select this option if you want the user's %s progress to be deleted when their access expires",'fcc'),LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
					<?php if (version_compare(LEARNDASH_VERSION, "2.2.0", ">=")) {
						?>
						<div class="sfwd_input " id="sfwd-courses_course_disable_content_table">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_disable_content_table_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Hide %s Content Table', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div"><input name="sfwd-courses_course_disable_content_table" type="checkbox" <?php echo ($sfwd_courses_course_disable_content_table != '' ) ? 'checked' : ''; ?>>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_disable_content_table_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Hide %s Content table when user is not enrolled.', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
					<?php }
					if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) != 'yes' ) { ?>
					<div class="sfwd_input " id="sfwd-courses_course_lesson_orderby">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_orderby_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Sort %s By ', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<select name="sfwd-courses_course_lesson_orderby">
										<option  value=""><?php echo __('Use Default','fcc'); ?></option>
										<option value="title" <?php echo ($sfwd_courses_course_lesson_orderby == 'title' ) ? 'selected' : ''; ?>><?php echo __('Title','fcc'); ?></option>
										<option value="date" <?php echo ($sfwd_courses_course_lesson_orderby == 'date' ) ? 'selected' : ''; ?>><?php echo __('Date','fcc'); ?></option>
										<option value="menu_order" <?php echo ($sfwd_courses_course_lesson_orderby == 'menu_order' ) ? 'selected' : ''; ?>><?php echo __('Menu Order','fcc'); ?></option>
									</select>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_orderby_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Choose the sort order of %s in this %s', 'fcc'), LearnDash_Custom_Label::get_label('lessons'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<div class="sfwd_input " id="sfwd-courses_course_lesson_order">
							<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_order_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Sort %s Direction ', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
							<span class="sfwd_option_input">
								<div class="sfwd_option_div">
									<select name="sfwd-courses_course_lesson_order">
										<option  value=""><?php echo __('Use Default','fcc'); ?></option>
										<option value="ASC" <?php echo ($sfwd_courses_course_lesson_order == 'ASC' ) ? 'selected' : ''; ?>><?php echo __('Ascending','fcc'); ?></option>
										<option value="DESC" <?php echo ($sfwd_courses_course_lesson_order == 'DESC' ) ? 'selected' : ''; ?>><?php echo __('Descending','fcc'); ?></option>
									</select>
								</div>
								<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_order_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Choose the sort order of %s in this %s', 'fcc'), LearnDash_Custom_Label::get_label('lessons'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
							</span>
							<p style="clear:left"></p>
						</div>
						<?php } ?>
				</div>
			<?php endif; ?>
			<?php
			$custom_style = '';
			if(! isset($_GET['courseid'])){ 
				$custom_style = 'style="display:none;';
			}
			?>
			<h3 <?php // echo $custom_style; ?>><?php echo __('Associated contents','fcc'); ?></h3>
			<div <?php // echo $custom_style; ?>>
				<?php
				$course_id = learndash_get_course_id( @$_GET['courseid'] );

				if ( !empty( $course_id ) ) {
						$course = get_post( $course_id );
						$course_settings = learndash_get_setting( $course );
						$lessons = learndash_get_course_lessons_list( $course );
						if (( isset( $course_id ) ) && ( !empty( $course_id ) )) {

							// Normally this will be called on a Course/Lesson/Topic/Quiz admin page or front-end where the post var is available.
							if ( isset( $_GET['courseid'] ) ) {
								$post_id = intval( $_GET['courseid'] );
								$post = get_post( $post_id );

								if ( $post->post_type == 'sfwd-topic' || $post->post_type == 'sfwd-quiz' ) {
									$lesson_id = learndash_get_setting( $post, 'lesson' );
								} else {
									$lesson_id = $post->ID;
								}
							} else {
								$post_id = 0;
								$lesson_id = 0;
							}

							include_once trailingslashit(dirname(dirname(__FILE__))) . 'templates/associated-contents.php';
						}
				}

				if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
					// learndash_course_switcher_admin( $course_id );
					$sfwd_post=isset($_GET['courseid']) ? $_GET['courseid'] : '0';
					include trailingslashit(dirname(dirname(__FILE__))) . 'templates/course_navigation_switcher_admin.php';
				}
				?>
			</div>
			<?php 
			if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=") && version_compare(LEARNDASH_VERSION, "2.6.0", "<") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes') { ?>
			<h3><?php echo sprintf(__('%s Builder', 'fcc'), LearnDash_Custom_Label::get_label('course') ); ?></h3>
			<div>
				<?php $this->course_builder->course_builder_box(get_post($this->course_id));?>
			</div>
			<?php }
			if (version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes'){
				?>
				<h3><?php echo sprintf(__('%s Builder', 'fcc'), LearnDash_Custom_Label::get_label('course') ); ?></h3>
				<div>
					<div>
						<?php $this->course_builder->show_builder_box(get_post($this->course_id)); ?>
					</div>
					<?php if (version_compare(LEARNDASH_VERSION, "3.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes') : ?>
					<hr>
					<div>
						<span class="fcc-builder-labels"><?php _e(LearnDash_Custom_Label::get_label('lessons')); ?></span>
						<div id ="sfwd-lessons-app"></div>
						<span class="fcc-builder-labels"><?php _e(LearnDash_Custom_Label::get_label('topics')); ?></span>
						<div id ="sfwd-topics-app"></div>
						<span class="fcc-builder-labels"><?php _e(LearnDash_Custom_Label::get_label('quizzes')); ?></span>
						<div id ="sfwd-quizzes-app"></div>
					</div>
					<?php endif; ?>
				</div>
				<?php
			}
			?>
	</div>
	<br><br>
	<input type ="hidden" name="wdm_course_action" value="<?php echo isset( $_GET[ 'courseid' ] ) ? 'edit' : 'add'; ?>">
	<input type ="hidden" name="fcc-post-type" value="sfwd-courses" />
	<?php if ( isset( $_GET[ 'courseid' ] ) ) { ?>
			<input type ="hidden" name ="courseid" value ="<?php echo $_GET[ 'courseid' ]; ?>">
	<?php } ?>
			<div id="wdm_editor_tp"></div>
		<input type="submit" value="<?php _e('Speichern', 'fcc'); ?>" id="wdm_course_submit">
	</form>
</div>