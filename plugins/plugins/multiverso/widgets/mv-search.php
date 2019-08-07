<?php
/*
Widget Name: Mv Search
Description: A search form for your files
Author: Alessio Marzo & Andrea Onori
Version: 1.0
Author URI: http://www.webself.it
*/



class multiverso_search extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_search', 'description' => __( 'A search form for your Multiverso files', 'mvafsp') );
		parent::__construct('mvsearch', __('MV Search', 'mvafsp'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		
		function mv_search_form() {
			$form = '<form role="search" method="post" id="searchform" action="' . get_permalink( get_option('mv_search_page') ).'" >
			<div><label class="screen-reader-text" for="mvs">' . __( 'Search for:','mvafsp' ) . '</label>
			<input type="text" value="' . get_search_query() . '" name="mvs" id="mvs" />
			<input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search','mvafsp' ) .'" />
			</div>
			</form>';
			return $form;
   		}
					
		// Use current theme search form if it exists
		echo mv_search_form();
		
		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mvafsp'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

}

function multiverso_search_widgets()
{
	register_widget( 'multiverso_search' );
}

add_action( 'widgets_init', 'multiverso_search_widgets' );
?>
