<?php
/*
Widget Name: MV Categories
Description: A list or dropdown of Multiverso categories
Author: Alessio Marzo & Andrea Onori
Version: 1.0
Author URI: http://www.webself.it
*/

/**
 * Categories widget class
 *
 * @since 2.8.0
 */
class multiverso_mv_category_files extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_multiverso_categories', 'description' => __( 'A list or dropdown of Multiverso categories', 'mvafsp' ) );
		parent::__construct('multiverso_categories', __('MV Categories', 'mvafsp'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories', 'mvafsp' ) : $instance['title'], $instance, $this->id_base);
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('parent' => 0, 'orderby' => 'name', 'hierarchical' => $h, 'taxonomy' => 'multiverso-categories');

		$cat_args['title_li'] = '';
	
		$categories = get_categories($cat_args);
		
		echo '<ul class="mv-cat-list">';
		
		foreach($categories as $category) {
			
				$category_link = esc_url( add_query_arg( 'catid', $category->term_id, get_permalink( get_option('mv_category_page') ) ) );		
				
				echo '<li>';
				echo '<a href="'.$category_link.'">'.$category->name.'</a>';
				mv_get_subcats($category->term_id);
				echo '</li>';
			
		}
		
		echo '</ul>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'mvafsp' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy', 'mvafsp' ); ?></label></p>
<?php
	}

}

function multiverso_mv_category_files_widgets()
{
	register_widget( 'multiverso_mv_category_files' );
}

add_action( 'widgets_init', 'multiverso_mv_category_files_widgets' );



?>