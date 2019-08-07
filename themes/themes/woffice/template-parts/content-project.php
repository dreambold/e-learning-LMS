<?php
$project_status = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_current_status') : 'planned';

if ($project_status == 'archived') {
	$project_status_icon = 'fa-archive';
	$project_status_label = __('Archived','woffice');
} else if ($project_status == 'done') {
	$project_status_icon = 'fa-check-square';
	$project_status_label = __('Done','woffice');
} else if ($project_status == 'in_progress') {
	$project_status_icon = 'fa-sync';
	$project_status_label = __('In progress','woffice');
} else if ($project_status == 'in_review') {
	$project_status_icon = 'fa-cog';
	$project_status_label = __('In review','woffice');
} else {
	$project_status_icon = 'fa-book';
	$project_status_label = __('Planned','woffice');
}

?>

<li class="box content <?php echo $archived_class; ?>">
	<div class="intern-padding">
		
		<a href="<?php the_permalink(); ?>" rel="bookmark" class="project-head">

			<h2 class="project-title"><i class="fa fa-cubes pr-3"></i><?php the_title() ?></h2>

			<?php
			if (get_comment_count(get_the_ID()) > 0):
				echo '<span class="project-comments"><i class="fa fa-comments"></i> '.get_comments_number( '0', '1', '%' ).'</span>';
			endif;
			?>

			<?php
			// CATEGORY
			if( has_term('', 'project-category')):
				echo '<span class="project-category"><i class="fa fa-tag"></i>';
				echo wp_strip_all_tags(get_the_term_list( $post->ID, 'project-category', '', ', ' ));
				echo '</span>';
			endif;
			?>

			<?php
			// MEMBERS
			$project_members = woffice_get_project_members();
			echo '<span class="project-members"><i class="fa fa-users"></i> '.count($project_members).'</span>';
			?>

            <?php
            // DATE
            woffice_projects_loop_render_dates( $post->ID );
            ?>

			<?php
			// PROJECT STATUS
			echo '<span class="project-status badge badge-pill '. esc_attr($project_status) . '"><i class="fa '. esc_attr($project_status_icon) .' pr-2"></i>'. esc_html($project_status_label) . '</span>';
			?>

		</a>

		<?php
		// THE PROGRESS BAR
		woffice_project_progressbar();
		?>

		<p class="project-excerpt"><?php the_excerpt() ?></p>

		<div class="text-right">
			<a href="<?php the_permalink(); ?>" class="btn btn-default"><?php esc_html_e("See Project","woffice")?> <i class="fa fa-arrow-right"></i></a>
		</div>
	</div> <!-- .intern-padding -->
</li>