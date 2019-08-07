<?php global $wpdb;
// var_dump(LEARNDASH_VERSION);
// die();
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps') == 'yes') ? false : true;
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/lesson_listing';
if(isset($_GET['lessonid'])){
	if(!is_numeric( $_GET[ 'lessonid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table = $wpdb->posts;
$id = $_GET['lessonid'];

$sql = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-lessons' AND post_author = ".get_current_user_id();
$results = $wpdb->get_results($sql);
if(count($results) == 0 ){
	echo __("Sorry, Something went wrong",'fcc');
	return;
}
}
$title = "";
$content = "";
$featured_image = "";
$sfwd_lessons_course = "";
$sfwd_lessons_lesson_materials = "";
$sfwd_lessons_forced_lesson_time = "";
$sfwd_lessons_lesson_assignment_upload = "";
$sfwd_lessons_lesson_assignment_points_enabled = "";
$sfwd_lessons_lesson_assignment_points_amount = "";
$sfwd_lessons_auto_approve_assignment = "";
$sfwd_lessons_assignment_upload_limit_count = "";
$sfwd_lessons_lesson_assignment_deletion_enabled = "";
$sfwd_lessons_lessons_assignment_upload_limit_extensions = "";
$sfwd_lessons_lessons_assignment_upload_limit_size = "";
$sfwd_lessons_sample_lesson = "";
$sfwd_lessons_visible_after_specific_date = "";
$sfwd_lessons_visible_after = "";
$sfwd_lessons_lesson_video_enabled = "";
$sfwd_lessons_lesson_video_url = "";
$sfwd_lessons_lesson_video_auto_start = "";
$sfwd_lessons_lesson_video_show_controls = "";
$sfwd_lessons_lesson_video_shown = "";
$sfwd_lessons_lesson_video_auto_complete = "";
$sfwd_lessons_lesson_video_auto_complete_delay = "";
$sfwd_lessons_lesson_video_hide_complete_button = "";
$preview_url = '';
$menu_order	= 0;

$table = $wpdb->prefix."posts";
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$course_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$course_list[] = $v->ID;
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
if ( isset( $_GET[ 'lessonid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'lessonid' ], 'category');
	if ( count( $results ) > 0 ) {
		foreach ( $results as $value ) {
			$selected_category[] = $value->term_taxonomy_id;
		}
	}
}

$selected_tag	 = array();
if ( isset( $_GET[ 'lessonid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'lessonid' ], 'post_tag');
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
if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=") && class_exists('LearnDash_Settings_Section')) {
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_lesson_category',
					'hide_empty' => false,
				));

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_category[ $value->term_taxonomy_id ] = $value->name;
			}
		}
		if ( isset( $_GET[ 'lessonid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'lessonid' ], 'ld_lesson_category');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_category[] = $value->term_taxonomy_id;
				}
			}
		}
	}

	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_tag' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_lesson_tag',
					'hide_empty' => false,
				));
		$ld_tag	 = array();

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_tag[ $value->term_taxonomy_id ] = $value->name;
			}
		}


		if ( isset( $_GET[ 'lessonid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'lessonid' ], 'ld_lesson_tag');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_tag[] = $value->term_taxonomy_id;
				}
			}
		}
	}
}
//End: LearnDash Categories & Tags--------------------------------------

if(isset($_GET['lessonid'])){
	 $id = $_GET['lessonid'];
	$title = get_the_title($id);
	$content_post = get_post($id);
	$content = $content_post->post_content;
	$menu_order = $content_post->menu_order;
	$content = apply_filters('the_content', $content);
	$lesson_meta = maybe_unserialize(get_post_meta($id,'_sfwd-lessons'));
	//echo "<pre>";print_r($lesson_meta);echo "</pre>";
	if(isset($lesson_meta[0]['sfwd-lessons_lesson_materials']))
		$sfwd_lessons_lesson_materials = $lesson_meta[0]['sfwd-lessons_lesson_materials'];
	if(isset($lesson_meta[0]['sfwd-lessons_course']))
		$sfwd_lessons_course = $lesson_meta[0]['sfwd-lessons_course'];
	if(isset($lesson_meta[0]['sfwd-lessons_forced_lesson_time']))
		$sfwd_lessons_forced_lesson_time = $lesson_meta[0]['sfwd-lessons_forced_lesson_time'];
	if(isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_upload']))
		$sfwd_lessons_lesson_assignment_upload = $lesson_meta[0]['sfwd-lessons_lesson_assignment_upload'];
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_points_enabled']))
		$sfwd_lessons_lesson_assignment_points_enabled = $lesson_meta[0]['sfwd-lessons_lesson_assignment_points_enabled'];

	if (isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_points_amount']))
		$sfwd_lessons_lesson_assignment_points_amount = $lesson_meta[0]['sfwd-lessons_lesson_assignment_points_amount'];

	if(isset($lesson_meta[0]['sfwd-lessons_auto_approve_assignment']))
		$sfwd_lessons_auto_approve_assignment = $lesson_meta[0]['sfwd-lessons_auto_approve_assignment'];
	if(isset($lesson_meta[0]['sfwd-lessons_assignment_upload_limit_count']))
		$sfwd_lessons_assignment_upload_limit_count = $lesson_meta[0]['sfwd-lessons_assignment_upload_limit_count'];
	if(isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_deletion_enabled']))
		$sfwd_lessons_lesson_assignment_deletion_enabled = $lesson_meta[0]['sfwd-lessons_lesson_assignment_deletion_enabled'];
	if(isset($lesson_meta[0]['sfwd-lessons_assignment_upload_limit_extensions']))
		$sfwd_lessons_lessons_assignment_upload_limit_extensions = $lesson_meta[0]['sfwd-lessons_assignment_upload_limit_extensions'];
	if(isset($lesson_meta[0]['sfwd-lessons_assignment_upload_limit_size']))
		$sfwd_lessons_lessons_assignment_upload_limit_size = $lesson_meta[0]['sfwd-lessons_assignment_upload_limit_size'];
	if(isset($lesson_meta[0]['sfwd-lessons_sample_lesson']))
		$sfwd_lessons_sample_lesson = $lesson_meta[0]['sfwd-lessons_sample_lesson'];
	if(isset($lesson_meta[0]['sfwd-lessons_visible_after']))
		$sfwd_lessons_visible_after = $lesson_meta[0]['sfwd-lessons_visible_after'];
	if(isset($lesson_meta[0]['sfwd-lessons_visible_after_specific_date'])){
		$sfwd_lessons_visible_after_specific_date = $lesson_meta[0]['sfwd-lessons_visible_after_specific_date'];
		if(ctype_digit($sfwd_lessons_visible_after_specific_date)){
			$date = new DateTime('@'.$sfwd_lessons_visible_after_specific_date);
			$timezone_string=get_option('timezone_string');
			if($timezone_string && strlen($timezone_string)!=0){
				$date->setTimeZone(new DateTimeZone($timezone_string));
			}else{
				$gmt_offset=get_option('gmt_offset');
				if($gmt_offset && $gmt_offset != '0'){
					if (strpos($gmt_offset, '-') === false) {
						$gmt_offset='+'.$gmt_offset;
					}
					if (strpos($gmt_offset, '.') !== false) {
						$time=explode('.', $gmt_offset);
						if($time[1]==5){
							$time[1]=30;
						}elseif($time[1]==75){
							$time[1]=45;
						}
						$gmt_offset=$time[0].':'.$time[1];
					}
					$date->setTimeZone(new DateTimeZone($gmt_offset));
				}
			}
			$sfwd_lessons_visible_after_specific_date = $date->format('Y/m/d, H:i');
		}
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_enabled'])) {
		$sfwd_lessons_lesson_video_enabled = ($lesson_meta[0]['sfwd-lessons_lesson_video_enabled'] == 'on')?true:false;
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_url'])) {
		$sfwd_lessons_lesson_video_url = $lesson_meta[0]['sfwd-lessons_lesson_video_url'];
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_auto_start'])) {
		$sfwd_lessons_lesson_video_auto_start = ($lesson_meta[0]['sfwd-lessons_lesson_video_auto_start'] == 'on')?true:false;
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_show_controls'])) {
		$sfwd_lessons_lesson_video_show_controls = ($lesson_meta[0]['sfwd-lessons_lesson_video_show_controls'] == 'on')?true:false;
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_shown'])) {
		$sfwd_lessons_lesson_video_shown = $lesson_meta[0]['sfwd-lessons_lesson_video_shown'];
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_auto_complete'])) {
		$sfwd_lessons_lesson_video_auto_complete = ($lesson_meta[0]['sfwd-lessons_lesson_video_auto_complete'] == 'on')?true:false;
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_auto_complete_delay'])) {
		$sfwd_lessons_lesson_video_auto_complete_delay = $lesson_meta[0]['sfwd-lessons_lesson_video_auto_complete_delay'];
	}
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_video_hide_complete_button'])) {
		$sfwd_lessons_lesson_video_hide_complete_button = ($lesson_meta[0]['sfwd-lessons_lesson_video_hide_complete_button'] == 'on')?true:false;
	}
	$preview_url = add_query_arg(array('preview'=>1),get_permalink($id));
}


?>
<?php if(isset($_SESSION['update'])){ ?>
<?php if($_SESSION['update'] == 2) { ?>
<div class="wdm-update-message"><?php echo sprintf(__('%s Updated Successfully','fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></div>
	
<?php }else if($_SESSION['update'] == 1){ ?>
	<div class="wdm-update-message"><?php echo sprintf(__('%s Added Successfully.','fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></div>
	
 <?php }
 unset($_SESSION['update']);

} 
if (defined('WDM_ERROR')) { ?>

		<div class="wdm-error-message"><?php echo WDM_ERROR; ?>
		</div>

	

<?php
	
} ?>
	
	
	<input type="button" value="<?php echo __('Back','fcc'); ?>" onclick="location.href = '<?php echo $back_url; ?>';" style="float: right;">
			<?php if($preview_url != ''){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo __('Preview','fcc'); ?>" style="float:right;margin-right: 2%;"onclick="window.open('<?php echo $preview_url; ?>')">
			<?php }
			?>
<br><br><br>	
<form method="post" enctype="multipart/form-data">
<div id="accordion">
	<h3><?php echo __('Content','fcc'); ?></h3>
	<div>
		<span><?php echo __('Title','fcc'); ?></span><br>
		<input type="text" name="title" required="required"	style="width:100%;" value = "<?php echo $title; ?>"><br><br>
		<span><?php echo __('Content','fcc'); ?></span>
		<?php
   ///$content	 = '';
   $editor_id	 = 'wdm_content';
   
   wp_editor( $content, $editor_id );
      
  // do_action('admin_print_scripts');
if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=")) {
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'wp_post_category' ) == 'yes') {
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
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'wp_post_tag' ) == 'yes') {
	?>
			<?php if ( count( $tag ) > 0 ) { ?>
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
	}//End if wp_post_tag condition
	//Start: LearnDash Categories & tags________________________________________-->
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) == 'yes') {
	?>
					<br>
					<?php if ( count( $ld_category ) > 0 ) { ?>
						<span><?php echo sprintf(__('%s Categories:','fcc'), LearnDash_Custom_Label::get_label('lesson') ); ?></span><br>
						<select name="ld_category[]" multiple>
			<?php foreach ( $ld_category as $k => $v ) { ?>
							<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_ld_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>

						<?php } ?>
							</select>
						<br>
					<?php } ?>
					<br>
	<?php }//End if ld_course_category condition
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_tag' ) == 'yes') {
	?>
						<span><?php echo sprintf( __('%s Tags:','fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></span><br>
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
					<input type='text' name='wdm_ld_tag' id='wdm_ld_tag'><input type='button' id='wdm_add_ld_tag' data-cat_type="lesson" value="<?php echo sprintf( __('Add %s Tag', 'fcc'), LearnDash_Custom_Label::get_label('lesson'));?>">
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
?>
		<br>
		<span ><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" ></span>
		<?php if ( isset( $_GET[ 'lessonid' ] ) && has_post_thumbnail($_GET['lessonid']) ) { ?>
				<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
<?php } ?>
		<br>
			<div>
				<label for="order_number"><?php _e('Order','fcc');?></label>
				<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
			</div>
	</div>
	<h3><?php echo __('Features','fcc'); ?></h3>
	<div>
		<?php if (version_compare(LEARNDASH_VERSION, "3.0", ">=") && isset($_GET['lessonid'])) : ?>
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
					<?php echo $metabox->show_meta_box(get_post($_GET['lessonid'])); ?>
				</div>
			</div>
			<?php endforeach; ?>
		<?php elseif(version_compare(LEARNDASH_VERSION, "3.0", "<")) : ?>

		<div class="sfwd sfwd_options sfwd-lessons_settings">
		<?php
		if($sharedCourse){
		?>
		<div class="sfwd_input " id="sfwd-lessons_course">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_course_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf('%s %s', __('Associated', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div">
					<select name="sfwd-lessons_course">
					<option value="0"><?php echo sprintf( __('Select a %s', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></option>
					<?php if(count($course_list) > 0){ ?>
								<?php foreach($course_list as $k=>$v){ ?>
									
								<option value="<?php echo $v; ?>" <?php echo ($sfwd_lessons_course == $v ) ? 'selected' : ''; ?>><?php echo get_the_title($v); ?></option>	
									
								<?php } ?>
								<?php } ?>
					</select>
				</div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_course_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Associate with a %s','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<div class="sfwd_input " id="sfwd-lessons_lesson_materials">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_materials_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('%s Materials', 'fcc'), LearnDash_Custom_Label::get_label('lesson') ); ?></label></a></span>
			<span class="sfwd_option_input">
			<div class="sfwd_option_div"><textarea name="sfwd-lessons_lesson_materials" rows="2" cols="57"><?php echo $sfwd_lessons_lesson_materials; ?></textarea></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_materials_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Options for %s materials', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-lessons_forced_lesson_time">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_forced_lesson_time_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Forced %s Timer', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_forced_lesson_time" type="text" size="57" value="<?php echo $sfwd_lessons_forced_lesson_time; ?>"></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_forced_lesson_time_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Minimum time a user has to spend on %s page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-lessons_lesson_assignment_upload">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_upload_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Upload Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_upload" type="checkbox" <?php echo ($sfwd_lessons_lesson_assignment_upload != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_upload_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to make it mandatory to upload assignment','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-lessons_auto_approve_assignment">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_auto_approve_assignment_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Approve Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_auto_approve_assignment" type="checkbox" <?php echo ($sfwd_lessons_auto_approve_assignment != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_auto_approve_assignment_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to auto-approve the uploaded assignment','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=")) {
		?>
		<div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_count">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_count_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Limit number of uploaded files','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_count" type="number" value="<?php echo ($sfwd_lessons_assignment_upload_limit_count == '' ) ? '1' : $sfwd_lessons_assignment_upload_limit_count; ?>"></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_assignment_upload_limit_count_tip"><label class="sfwd_help_text"><?php echo __('Enter the maximum number of assignment uploads allowed. Default is 1. Use 0 to unlimited.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-lessons_lesson_assignment_deletion_enabled">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_deletion_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allow Student to Delete own Assignment(s)','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_deletion_enabled" type="checkbox" <?php echo ($sfwd_lessons_lesson_assignment_deletion_enabled != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_deletion_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow Student to Delete own Assignment(s).','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_enabled">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Award Points for Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_enabled" type="checkbox" <?php echo ($sfwd_lessons_lesson_assignment_points_enabled != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_points_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow this assignment to be assigned points when it is approved.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_amount" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_amount_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Set Number of Points for Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_amount" type="number" min='0' size="57" value="<?php echo $sfwd_lessons_lesson_assignment_points_amount; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_points_amount_tip"><label class="sfwd_help_text"><?php echo __('Assign the max amount of points someone can earn for this assignment.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=")) {
		?>
		<div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_extensions">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_extensions_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allowed File Extensions','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_extensions" type="text" placeholder="Example: pdf,xls,zip" value="<?php echo $sfwd_lessons_lessons_assignment_upload_limit_extensions; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_assignment_upload_limit_extensions_tip"><label class="sfwd_help_text"><?php echo __('Enter comma-separated list of allowed file extensions: pdf,xls,zip or leave blank for any.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-lessons_assignment_upload_limit_size">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_assignment_upload_limit_size_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allowed File Size','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_assignment_upload_limit_size" type="text" placeholder="Maximum upload file size: 2M" value="<?php echo $sfwd_lessons_lessons_assignment_upload_limit_size; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_assignment_upload_limit_size_tip"><label class="sfwd_help_text"><?php echo __('Enter maximim file upload size. Example: 100KB, 2M, 2MB, 1G. Maximum upload file size: 2M','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<div class="sfwd_input " id="sfwd-lessons_sample_lesson">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_sample_lesson_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Sample %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_sample_lesson" type="checkbox" <?php echo ($sfwd_lessons_sample_lesson != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_sample_lesson_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Check this if you want this %s and all its %s to be available for free','fcc'),LearnDash_Custom_Label::get_label('lesson'), LearnDash_Custom_Label::get_label('topics'));?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-lessons_visible_after">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_visible_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Make %s visible X days after sign-up', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_visible_after" type="number" size="57" value="<?php echo $sfwd_lessons_visible_after; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_visible_after_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Make %s visible ____ days after sign-up', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-lessons_visible_after_specific_date">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_visible_after_specific_date_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf(__('Make %s visible on specific date', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_visible_after_specific_date" type="text" size="57" value="<?php echo $sfwd_lessons_visible_after_specific_date; ?>" id="dp1424081713948" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_visible_after_specific_date_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Set the date that you would like this %s to become available', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php if (version_compare(LEARNDASH_VERSION, "2.4.5", ">=")) {
		?>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_enabled">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_enabled_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Enable Video Progression','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_enabled" type="checkbox" <?php echo !empty($sfwd_lessons_lesson_video_enabled) ? 'checked' : ''; ?>></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_enabled_after_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to show a video as part of the progression.','fcc'); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_url" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_url_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Video URL','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_url" type="text" size="57" value="<?php echo $sfwd_lessons_lesson_video_url; ?>" ></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_url_after_tip"><label class="sfwd_help_text"><?php echo sprintf(__("URL to video. The video will be added above the %s content. Use the shortcode [ld_video] to position the player within content. Supported URL formats are YouTube (youtu.be, youtube.com), Vimeo (vimeo.com), Wistia (wistia.com), or Local videos. The value for this field can be a simple URL to the video, an iframe or either [video] or [embed] shortcodes.", 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_start" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_start_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Start Video','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_start" type="checkbox" <?php echo !empty($sfwd_lessons_lesson_video_auto_start) ? 'checked' : ''; ?>></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_auto_start_after_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want the video to auto-start on page load.','fcc'); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_show_controls" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_show_controls_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Show Video Controls','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_show_controls" type="checkbox" <?php echo !empty($sfwd_lessons_lesson_video_show_controls) ? 'checked' : ''; ?>></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_show_controls_after_tip"><label class="sfwd_help_text"><?php echo __('Show Video Controls. By default controls are disabled. Only used for YouTube and local videos.','fcc'); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_shown" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_shown_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('When to show video','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div">
						<select name="sfwd-lessons_lesson_video_shown">
							<option value="AFTER" <?php echo ($sfwd_lessons_lesson_video_shown == "AFTER") || empty($sfwd_lessons_lesson_video_shown) ? 'selected' : ''; ?>><?php _e('After (default) - Video is shown after completing sub-steps', 'fcc'); ?>
							</option>
							<option value="BEFORE" <?php echo ($sfwd_lessons_lesson_video_shown == "BEFORE" ) ? 'selected' : ''; ?>><?php _e('Before - Video is shown before completing sub-steps', 'fcc'); ?>
							</option>
						</select>
					</div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_shown_tip"><label class="sfwd_help_text"><?php echo __('Select when to show video in relation to sub-steps.','fcc'); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_complete" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_complete_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('Auto Complete %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_complete" type="checkbox" <?php echo !empty($sfwd_lessons_lesson_video_auto_complete) ? 'checked' : ''; ?>></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_auto_complete_after_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Check this if you want the %s to auto-complete after the video completes.', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
				</span>
				<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-lessons_lesson_video_auto_complete_delay" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_auto_complete_delay_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Complete Delay','fcc'); ?></label></a></span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_auto_complete_delay" type="number" size="57" value="<?php echo $sfwd_lessons_lesson_video_auto_complete_delay; ?>" ></div>
					<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_auto_complete_delay_after_tip"><label class="sfwd_help_text"><?php echo __("Time delay in second between the time the video finishes and the auto complete occurs. Example 0 no delay, 5 for five seconds.",'fcc'); ?></label></div>
				</span>
				<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-lessons_lesson_video_hide_complete_button" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_video_hide_complete_button_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Hide Complete Button','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-lessons_lesson_video_hide_complete_button" type="checkbox" <?php echo !empty($sfwd_lessons_lesson_video_hide_complete_button) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_video_hide_complete_button_after_tip"><label class="sfwd_help_text"><?php echo __('Check this to hide the complete button.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
			</div>
			<?php
		}
		?>
		</div>
	<?php endif; ?>
	</div>
			<?php if(isset($_GET['lessonid'])){ ?>
			<h3><?php echo __('Associated Contents','fcc'); ?></h3>
			<div>
				<?php
				$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : learndash_get_course_id( @$_GET['lessonid'] );
	
	if ( !empty( $course_id ) ) {
		$course = get_post( $course_id );
		$course_settings = learndash_get_setting( $course );
		$lessons = learndash_get_course_lessons_list( $course );
		if (( isset( $course_id ) ) && ( !empty( $course_id ) )) {

			// Normally this will be called on a Course/Lesson/Topic/Quiz admin page or front-end where the post var is available.
			if ( isset( $_GET['lessonid'] ) ) {
				$post_id = intval( $_GET['lessonid'] );
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
		$sfwd_post=isset($_GET['lessonid']) ? $_GET['lessonid'] : '0';
		include trailingslashit(dirname(dirname(__FILE__))) . 'templates/course_navigation_switcher_admin.php';
	}
				?>
			</div>
		<?php } ?>
</div>
	<input type ="hidden" name="wdm_lesson_action" value="<?php echo isset($_GET['lessonid']) ? 'edit' : 'add'; ?>">
	<input type ="hidden" name="fcc-post-type" value="sfwd-lessons" />
<?php if(isset($_GET['lessonid'])) { ?>
<input type ="hidden" name ="lessonid" value ="<?php echo $_GET['lessonid']; ?>">
<?php } ?>
<input type="submit" value="<?php _e('Speichern', 'fcc');?>" id='wdm_lesson_submit'>
</form>
