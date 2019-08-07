<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_slug = 'vc_assigned_tasks';

$atts = vc_map_get_attributes( $shortcode_slug, $atts );

$css_class = '';
$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->settings['base'], $atts );
$css_class .= ' ' .$this->getCSSAnimation( $atts['css_animation'] );
$css_class .= ' ' . $atts['el_class'];

$ext_instance = fw()->extensions->get( 'woffice-projects' );
$array_result = $ext_instance->woffice_projects_assigned_tasks(get_current_user_id());

$message = __("You have","woffice") . ' <span class="woffice-colored">'. $array_result['number'] .'</span> '. __("tasks","woffice");
?>

<div id="<?php echo $atts['el_id']; ?>" class="project-assigned-container project-assigned-shortcode <?php echo $css_class; ?>">
    <div class="project-assigned-head"><h3><i class="fa fa-tasks"></i> <?php echo $message; ?></h3>
    </div>

	<?php /* We get the tasks */
	$tasks = $array_result['tasks'];
	if (!empty($tasks)) { ?>
        <ul class="assigned-tasks-list">
			<?php
			foreach ($tasks as $task){
				echo '<li class="assigned-task">';
				echo '<a href="'.$task['task_project'].'?#project-content-todo">';
				if (!empty($task['task_date'])) {
					echo '<span class="badge badge-primary badge-pill">'.date(get_option('date_format'),strtotime(esc_html($task['task_date']))).'</span>';
				}
				echo $task['task_name'];
				echo'</a>';
				echo '</li>';
			}
			?>
        </ul>
	<?php } else { ?>
        <div class="assigned-tasks-empty">
            <i class="fa fa-check-circle fa-4x mb-3"></i>
			<?php
			if($atts['user'] == "current")
				echo '<p><strong>' . esc_html__("Well done! You don't have any task from your projects.","woffice") .'</strong></p>';
			else
				echo '<p><strong>' . esc_html__("No task found.","woffice") . '</strong></p>';
			?>
        </div>
	<?php } ?>

</div>

