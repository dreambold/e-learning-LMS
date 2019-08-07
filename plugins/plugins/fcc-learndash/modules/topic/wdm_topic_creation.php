<?php
global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/topic_listing';
if(isset($_GET['topicid'])){
	if(!is_numeric( $_GET[ 'topicid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table = $wpdb->posts;
	$id = $_GET['topicid'];

	$sql = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-topic' AND post_author = ".get_current_user_id();
	$results = $wpdb->get_results($sql);
	if(count($results) == 0 ){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
}
$sharedCourse=(LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps') == 'yes') ? false : true;


$title = "";
$content = "";
$featured_image = "";
$sfwd_topic_course = "";
$sfwd_topic_topic_materials = "";
$sfwd_topic_lesson = "";
$sfwd_topic_forced_lesson_time = "";
$sfwd_topic_lesson_assignment_upload = "";
$sfwd_topic_auto_approve_assignment = "";
$sfwd_topic_assignment_points_enabled = "";
$sfwd_topic_assignment_points_amount = "";
$sfwd_topic_lesson_video_enabled = "";
$sfwd_topic_lesson_video_url = "";
$sfwd_topic_lesson_video_auto_start = "";
$sfwd_topic_lesson_video_show_controls = "";
$sfwd_topic_lesson_video_shown = "";
$sfwd_topic_lesson_video_auto_complete = "";
$sfwd_topic_lesson_video_auto_complete_delay = "";
$sfwd_topic_lesson_video_hide_complete_button = "";
$sfwd_topics_assignment_upload_limit_count = "";
$sfwd_topics_topic_assignment_deletion_enabled = "";
$sfwd_topics_topics_assignment_upload_limit_extensions = "";
$sfwd_topics_topics_assignment_upload_limit_size = "";


$menu_order	= 0;
$preview_url = '';
$table = $wpdb->prefix."posts";
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$course_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$course_list[] = $v->ID;
		
	}
	
}

$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$lesson_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$lesson_list[] = $v->ID;
		
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
if ( isset( $_GET[ 'topicid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'topicid' ], 'category');
	if ( count( $results ) > 0 ) {
		foreach ( $results as $value ) {
			$selected_category[] = $value->term_taxonomy_id;
		}
	}
}

$selected_tag	 = array();
if ( isset( $_GET[ 'topicid' ] ) ) {
	$results = wp_get_post_terms($_GET[ 'topicid' ], 'post_tag');
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
if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=") && class_exists('LearnDash_Settings_Section')) {
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_topic_category',
					'hide_empty' => false,
				));

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_category[ $value->term_taxonomy_id ] = $value->name;
			}
		}

		if ( isset( $_GET[ 'topicid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'topicid' ], 'ld_topic_category');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_category[] = $value->term_taxonomy_id;
				}
			}
		}
	}

	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_tag' ) == 'yes') {
		$results=get_terms(array(
					'taxonomy' => 'ld_topic_tag',
					'hide_empty' => false,
				));
		$ld_tag	 = array();

		if ( count( $results ) > 0 ) {
			foreach ( $results as $value ) {
				$ld_tag[ $value->term_taxonomy_id ] = $value->name;
			}
		}


		$selected_ld_tag	 = array();
		if ( isset( $_GET[ 'topicid' ] ) ) {
			$results = wp_get_post_terms($_GET[ 'topicid' ], 'ld_topic_tag');
			if ( count( $results ) > 0 ) {
				foreach ( $results as $value ) {
					$selected_ld_tag[] = $value->term_taxonomy_id;
				}
			}
		}
	}
}
//End: LearnDash Categories & Tags--------------------------------------

$term_relationship = $wpdb->prefix."term_relationships";
$selected_category = array();
if(isset($_GET['topicid'])){
	$sql = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = ".$_GET['topicid'];
	$results = $wpdb->get_results($sql);
	if(count($results) >0 ){
		foreach($results as $k => $v){
			$selected_category[] = $v->term_taxonomy_id;
		}
		
	}
	
}
$selected_tag	 = array();
if ( isset( $_GET[ 'topicid' ] ) ) {
	$sql	 = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = " . $_GET[ 'topicid' ];
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) > 0 ) {
		foreach ( $results as $k => $v ) {
			$selected_tag[] = $v->term_taxonomy_id;
		}
	}
}
if(isset($_GET['topicid'])){
	 $id = $_GET['topicid'];
	$title = get_the_title($id);
	$content_post = get_post($id);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$menu_order = $content_post->menu_order;
	$topic_meta = maybe_unserialize(get_post_meta($id,'_sfwd-topic'));
	//echo "<pre>";print_r($topic_meta);echo "</pre>";
	if(isset($topic_meta[0]['sfwd-topic_topic_materials']))
		$sfwd_topic_topic_materials = $topic_meta[0]['sfwd-topic_topic_materials'];
	if(isset($topic_meta[0]['sfwd-topic_course']))
		$sfwd_topic_course = $topic_meta[0]['sfwd-topic_course'];
	if(isset($topic_meta[0]['sfwd-topic_lesson']))
		$sfwd_topic_lesson = $topic_meta[0]['sfwd-topic_lesson'];
	if(isset($topic_meta[0]['sfwd-topic_forced_lesson_time']))
		$sfwd_topic_forced_lesson_time = $topic_meta[0]['sfwd-topic_forced_lesson_time'];
	if(isset($topic_meta[0]['sfwd-topic_lesson_assignment_upload']))
		$sfwd_topic_lesson_assignment_upload = $topic_meta[0]['sfwd-topic_lesson_assignment_upload'];
	if(isset($topic_meta[0]['sfwd-topic_auto_approve_assignment']))
		$sfwd_topic_auto_approve_assignment = $topic_meta[0]['sfwd-topic_auto_approve_assignment'];
	if (isset($topic_meta[0]['sfwd-topic_lesson_assignment_points_enabled']))
		$sfwd_topic_assignment_points_enabled = $topic_meta[0]['sfwd-topic_lesson_assignment_points_enabled'];
	if (isset($topic_meta[0]['sfwd-topic_lesson_assignment_points_amount']))
		$sfwd_topic_assignment_points_amount = $topic_meta[0]['sfwd-topic_lesson_assignment_points_amount'];
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_enabled'])) {
		$sfwd_topic_lesson_video_enabled = ($topic_meta[0]['sfwd-topic_lesson_video_enabled'] == 'on')?true:false;
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_url'])) {
		$sfwd_topic_lesson_video_url = $topic_meta[0]['sfwd-topic_lesson_video_url'];
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_auto_start'])) {
		$sfwd_topic_lesson_video_auto_start = ($topic_meta[0]['sfwd-topic_lesson_video_auto_start'] == 'on')?true:false;
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_show_controls'])) {
		$sfwd_topic_lesson_video_show_controls = ($topic_meta[0]['sfwd-topic_lesson_video_show_controls'] == 'on')?true:false;
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_shown'])) {
		$sfwd_topic_lesson_video_shown = $topic_meta[0]['sfwd-topic_lesson_video_shown'];
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_auto_complete'])) {
		$sfwd_topic_lesson_video_auto_complete = ($topic_meta[0]['sfwd-topic_lesson_video_auto_complete'] == 'on')?true:false;
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_auto_complete_delay'])) {
		$sfwd_topic_lesson_video_auto_complete_delay = $topic_meta[0]['sfwd-topic_lesson_video_auto_complete_delay'];
	}
	if (isset($topic_meta[0]['sfwd-topic_lesson_video_hide_complete_button'])) {
		$sfwd_topic_lesson_video_hide_complete_button = ($topic_meta[0]['sfwd-topic_lesson_video_hide_complete_button'] == 'on')?true:false;
	}
	if(isset($topic_meta[0]['sfwd-topic_assignment_upload_limit_count']))
		$sfwd_topics_assignment_upload_limit_count = $topic_meta[0]['sfwd-topic_assignment_upload_limit_count'];
	if(isset($topic_meta[0]['sfwd-topic_lesson_assignment_deletion_enabled']))
		$sfwd_topics_topic_assignment_deletion_enabled = $topic_meta[0]['sfwd-topic_lesson_assignment_deletion_enabled'];
	if(isset($topic_meta[0]['sfwd-topic_assignment_upload_limit_extensions']))
		$sfwd_topics_topics_assignment_upload_limit_extensions = $topic_meta[0]['sfwd-topic_assignment_upload_limit_extensions'];
	if(isset($topic_meta[0]['sfwd-topic_assignment_upload_limit_size']))
		$sfwd_topics_topics_assignment_upload_limit_size = $topic_meta[0]['sfwd-topic_assignment_upload_limit_size'];
	$preview_url = add_query_arg(array('wdm_preview'=>1),get_permalink($id));
}
?>
<?php if(isset($_SESSION['update'])){ ?>
<?php if($_SESSION['update'] == 2) { ?>
<div class="wdm-update-message"><?php echo sprintf(__('%s Updated Successfully','fcc'), LearnDash_Custom_Label::get_label('topic')); ?></div>
	
<?php }else if($_SESSION['update'] == 1){ ?>
	<div class="wdm-update-message"><?php echo sprintf(__('%s Updated Successfully','fcc'), LearnDash_Custom_Label::get_label('topic')); ?></div>
	
 <?php }
 unset($_SESSION['update']);

}
if (defined('WDM_ERROR')) { ?>

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
<form method="post" enctype="multipart/form-data">
<div id="accordion">
	<h3><?php echo __('Content','fcc'); ?></h3>
	<div>
		<span><?php echo __('Title','fcc'); ?></span><br>
		<input type="text" name="title" style="width:100%;" value = "<?php echo $title; ?>"><br><br>
		<span><?php echo __('Content','fcc'); ?></span>
		<?php
   ///$content	 = '';
   $editor_id	 = 'wdm_content';
   
   wp_editor( $content, $editor_id );
      
  // do_action('admin_print_scripts');
if (version_compare(LEARNDASH_VERSION, "2.4.0", ">=")) {
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'wp_post_category' ) == 'yes') {
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
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'wp_post_tag' ) == 'yes') {
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
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes') {
	?>
					<br>
					<?php if ( count( $ld_category ) > 0 ) { ?>
						<span><?php echo sprintf(__('%s Categories:','fcc'), LearnDash_Custom_Label::get_label('topic')); ?></span><br>
						<select name="ld_category[]" multiple>
			<?php foreach ( $ld_category as $k => $v ) { ?>
							<option value="<?php echo $k; ?>" <?php echo in_array( $k, $selected_ld_category ) ? 'selected' : ''; ?>><?php echo $v; ?></option>

						<?php } ?>
							</select>
						<br>
					<?php } ?>
					<br>
	<?php }//End if ld_course_category condition
	if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_tag' ) == 'yes') {
	?>
						<span><?php echo sprintf(__('%s Tags:','fcc'), LearnDash_Custom_Label::get_label('topic') ); ?></span><br>
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
					<input type='text' name='wdm_ld_tag' id='wdm_ld_tag'><input type='button' id='wdm_add_ld_tag' data-cat_type="topic" value="<?php echo sprintf(__('Add %s Tag', 'fcc'), LearnDash_Custom_Label::get_label('topic'));?>">
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
?><br>
		<span><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" ></span>
		<?php if ( isset( $_GET[ 'topicid' ] ) && has_post_thumbnail($_GET['topicid']) ) { ?>
				<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
<?php } ?>
			<div>
				<label for="order_number"><?php _e('Order','fcc');?></label>
				<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
			</div>
	</div>
	<h3><?php echo __('Features','fcc'); ?></h3>
	<div>
		<?php if (version_compare(LEARNDASH_VERSION, "3.0", ">=") && isset($_GET['topicid'])) : ?>
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
					<?php echo $metabox->show_meta_box(get_post($_GET['topicid'])); ?>
				</div>
			</div>
			<?php endforeach; ?>
		<?php elseif(version_compare(LEARNDASH_VERSION, "3.0", "<")) : ?>

		<div class="sfwd sfwd_options sfwd-topic_settings">
		<?php if($sharedCourse){?>
		<div class="sfwd_input " id="sfwd-topic_course">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_course_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo sprintf( __('Associated %s', 'fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div">
					<select name="sfwd-topic_course">
					<option value="0"><?php echo sprintf( __('-- Select a %s --', 'fcc'), LearnDash_Custom_Label::get_label('course'));?></option>
					<?php if(count($course_list) > 0){ ?>
								<?php foreach($course_list as $k=>$v){ ?>
									
								<option value="<?php echo $v; ?>" <?php echo ($sfwd_topic_course == $v ) ? 'selected' : ''; ?>><?php echo get_the_title($v); ?></option>	
									
								<?php } ?>
								<?php } ?>
					</select>
				</div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_course_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Associate with a %s','fcc'), LearnDash_Custom_Label::get_label('course')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo sprintf( __('Associated %s', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div">
					<select name="sfwd-topic_lesson">
					<option value="0"><?php echo sprintf( __('-- Select a %s --', 'fcc'), LearnDash_Custom_Label::get_label('lesson'));?></option>
					<?php if(count($lesson_list) > 0){ ?>
								<?php foreach($lesson_list as $k=>$v){ ?>
									
								<option value="<?php echo $v; ?>" <?php echo ($sfwd_topic_lesson == $v ) ? 'selected' : ''; ?>><?php echo get_the_title($v); ?></option>	
									
								<?php } ?>
								<?php } ?>
					</select>
				</div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Optionally associate a %s with a %s', 'fcc'), LearnDash_Custom_Label::get_label('quiz'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<div class="sfwd_input " id="sfwd-topic_topic_materials">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_topic_materials_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf( __('%s Materials', 'fcc'), LearnDash_Custom_Label::get_label('topic')); ?></label></a></span>
			<span class="sfwd_option_input">
			<div class="sfwd_option_div"><textarea name="sfwd-topic_topic_materials" rows="2" cols="57"><?php echo $sfwd_topic_topic_materials; ?></textarea></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_topic_materials_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Options for %s materials', 'fcc'), LearnDash_Custom_Label::get_label('topic')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_forced_lesson_time">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_forced_lesson_time_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo sprintf( __('Forced %s Timer', 'fcc'), LearnDash_Custom_Label::get_label('topic')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_forced_lesson_time" type="text" size="57" value="<?php echo $sfwd_topic_forced_lesson_time; ?>"></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_forced_lesson_time_tip"><label class="sfwd_help_text"><?php echo sprintf( __('Minimum time a user has to spend on %s page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec', 'fcc'), LearnDash_Custom_Label::get_label('quiz')); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson_assignment_upload">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_assignment_upload_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Upload Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_assignment_upload" type="checkbox" <?php echo ($sfwd_topic_lesson_assignment_upload != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_assignment_upload_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to make it mandatory to upload assignment','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_auto_approve_assignment">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_auto_approve_assignment_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Auto Approve Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_auto_approve_assignment" type="checkbox" <?php echo ($sfwd_topic_auto_approve_assignment != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_auto_approve_assignment_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to auto-approve the uploaded assignment','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=")) {
		?>
		<div class="sfwd_input " id="sfwd-topic_assignment_upload_limit_count">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_assignment_upload_limit_count_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Limit number of uploaded files','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_assignment_upload_limit_count" type="number" value="<?php echo ($sfwd_topics_assignment_upload_limit_count == '' ) ? '1' : $sfwd_topics_assignment_upload_limit_count; ?>"></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_assignment_upload_limit_count_tip"><label class="sfwd_help_text"><?php echo __('Enter the maximum number of assignment uploads allowed. Default is 1. Use 0 to unlimited.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-topic_lesson_assignment_deletion_enabled">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_assignment_deletion_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allow Student to Delete own Assignment(s)','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_assignment_deletion_enabled" type="checkbox" <?php echo ($sfwd_topics_topic_assignment_deletion_enabled != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_assignment_deletion_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow Student to Delete own Assignment(s).','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<div class="sfwd_input " id="sfwd-topic_lesson_assignment_points_enabled">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_assignment_points_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Award Points for Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_assignment_points_enabled" type="checkbox" <?php echo ($sfwd_topic_assignment_points_enabled != '' ) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_assignment_points_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow this assignment to be assigned points when it is approved.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-topic_lesson_assignment_points_amount">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_assignment_points_amount_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Set Number of Points for Assignment','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_assignment_points_amount" type="number" min='0' size="57" value="<?php echo $sfwd_topic_assignment_points_amount; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_assignment_points_amount_tip"><label class="sfwd_help_text"><?php echo __('Assign the max amount of points someone can earn for this assignment.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php if (version_compare(LEARNDASH_VERSION, "2.5.0", ">=")) {
		?>
		<div class="sfwd_input " id="sfwd-topic_assignment_upload_limit_extensions">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_assignment_upload_limit_extensions_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allowed File Extensions','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_assignment_upload_limit_extensions" type="text" placeholder="Example: pdf,xls,zip" value="<?php echo $sfwd_topics_topics_assignment_upload_limit_extensions; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_assignment_upload_limit_extensions_tip"><label class="sfwd_help_text"><?php echo __('Enter comma-separated list of allowed file extensions: pdf,xls,zip or leave blank for any.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>

		<div class="sfwd_input " id="sfwd-topic_assignment_upload_limit_size">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_assignment_upload_limit_size_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Allowed File Size','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_assignment_upload_limit_size" type="text" placeholder="Maximum upload file size: 2M" value="<?php echo $sfwd_topics_topics_assignment_upload_limit_size; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_assignment_upload_limit_size_tip"><label class="sfwd_help_text"><?php echo __('Enter maximim file upload size. Example: 100KB, 2M, 2MB, 1G. Maximum upload file size: 2M','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<?php } ?>
		<?php if (version_compare(LEARNDASH_VERSION, "2.4.5", ">=")) {
		?>
			<div class="sfwd_input " id="sfwd-topic_lesson_video_enabled">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_enabled_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Enable Video Progression','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_enabled" type="checkbox" <?php echo !empty($sfwd_topic_lesson_video_enabled) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_enabled_after_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to show a video as part of the progression.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson_video_url" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_url_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Video URL','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_url" type="text" size="57" value="<?php echo $sfwd_topic_lesson_video_url; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_url_after_tip"><label class="sfwd_help_text"><?php echo __("URL to video. The video will be added above the Lesson content. Use the shortcode [ld_video] to position the player within content. Supported URL formats are YouTube (youtu.be, youtube.com), Vimeo (vimeo.com), Wistia (wistia.com), or Local videos. The value for this field can be a simple URL to the video, an iframe or either [video] or [embed] shortcodes.",'fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson_video_auto_start" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_auto_start_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Start Video','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_auto_start" type="checkbox" <?php echo !empty($sfwd_topic_lesson_video_auto_start) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_auto_start_after_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want the video to auto-start on page load.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson_video_show_controls" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_show_controls_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Show Video Controls','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_show_controls" type="checkbox" <?php echo !empty($sfwd_topic_lesson_video_show_controls) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_show_controls_after_tip"><label class="sfwd_help_text"><?php echo __('Show Video Controls. By default controls are disabled. Only used for YouTube and local videos.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
		<div class="sfwd_input " id="sfwd-topic_lesson_video_shown" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_shown_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('When to show video','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div">
					<select name="sfwd-topic_lesson_video_shown">
						<option value="AFTER" <?php echo ($sfwd_topic_lesson_video_shown == "AFTER") || empty($sfwd_topic_lesson_video_shown) ? 'selected' : ''; ?>><?php _e('After (default) - Video is shown after completing sub-steps', 'fcc'); ?>
						</option>
						<option value="BEFORE" <?php echo ($sfwd_topic_lesson_video_shown == "BEFORE" ) ? 'selected' : ''; ?>><?php _e('Before - Video is shown before completing sub-steps', 'fcc'); ?>
						</option>
					</select>
				</div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_shown_tip"><label class="sfwd_help_text"><?php echo __('Select when to show video in relation to sub-steps.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
			<div class="sfwd_input " id="sfwd-topic_lesson_video_auto_complete" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_auto_complete_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo sprintf('%s %s', __('Auto Complete', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_auto_complete" type="checkbox" <?php echo !empty($sfwd_topic_lesson_video_auto_complete) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_auto_complete_after_tip"><label class="sfwd_help_text"><?php echo sprintf(__('Check this if you want the %s to auto-complete after the video completes.', 'fcc'), LearnDash_Custom_Label::get_label('lesson')); ?></label></div>
			</span>
			<p style="clear:left"></p>
			</div>
			<div class="sfwd_input " id="sfwd-topic_lesson_video_auto_complete_delay" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_auto_complete_delay_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Complete Delay','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_auto_complete_delay" type="number" size="57" value="<?php echo $sfwd_topic_lesson_video_auto_complete_delay; ?>" ></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_auto_complete_delay_after_tip"><label class="sfwd_help_text"><?php echo __("Time delay in second between the time the video finishes and the auto complete occurs. Example 0 no delay, 5 for five seconds.",'fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
		</div>
			<div class="sfwd_input " id="sfwd-topic_lesson_video_hide_complete_button" style="display: none;">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_video_hide_complete_button_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Hide Complete Button','fcc'); ?></label></a></span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div"><input name="sfwd-topic_lesson_video_hide_complete_button" type="checkbox" <?php echo !empty($sfwd_topic_lesson_video_hide_complete_button) ? 'checked' : ''; ?>></div>
				<div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_video_hide_complete_button_after_tip"><label class="sfwd_help_text"><?php echo __('Check this to hide the complete button.','fcc'); ?></label></div>
			</span>
			<p style="clear:left"></p>
			</div>
			<?php
			}
		?>
		</div>
		<?php endif; ?>
	</div>
	<?php if(isset($_GET['topicid'])){ ?>
			<h3><?php echo __('Associated contents','fcc'); ?></h3>
			<div>
				<?php
				$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : learndash_get_course_id( @$_GET['topicid'] );
	
	if ( !empty( $course_id ) ) {
		$course = get_post( $course_id );
			$course_settings = learndash_get_setting( $course );
			$lessons = learndash_get_course_lessons_list( $course );
			if (( isset( $course_id ) ) && ( !empty( $course_id ) )) {

				// Normally this will be called on a Course/Lesson/Topic/Quiz admin page or front-end where the post var is available.
				if ( isset( $_GET['topicid'] ) ) {
					$post_id = intval( $_GET['topicid'] );
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
		$sfwd_post=isset($_GET['topicid']) ? $_GET['topicid'] : '0';
		include trailingslashit(dirname(dirname(__FILE__))) . 'templates/course_navigation_switcher_admin.php';
	}
				?>
			</div>
		<?php } ?>
</div>
	<input type ="hidden" name="wdm_topic_action" value="<?php echo isset($_GET['topicid']) ? 'edit' : 'add'; ?>">
	<input type ="hidden" name="fcc-post-type" value="sfwd-topic" />
<?php if(isset($_GET['topicid'])) { ?>
<input type ="hidden" name ="topicid" value ="<?php echo $_GET['topicid']; ?>">
<?php } ?>
<input type="submit" value="<?php _e('Speichern', 'fcc');?>" id='wdm_topic_submit'>
</form>
