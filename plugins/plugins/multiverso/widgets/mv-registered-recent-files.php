<?php
/*
Widget Name: MV Registered Recent Files
Description: Displays only registered recent files
Author: Alessio Marzo & Andrea Onori
Version: 1.0
Author URI: http://www.webself.it
*/

class multiverso_mv_registered_recent_files extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'Multiverso_Registered_Recent_File', 'description' => __( 'Displays only registered recent files', 'mvafsp') );
		parent::__construct('registered_recent_file', __('MV Registered Recent Files', 'mvafsp'), $widget_ops);
		$this->alt_option_name = 'Multiverso_Registered_Recent_File';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_mv_registered_recent_files', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Registered Recent Files', 'mvafsp') : $instance['title'], $instance, $this->id_base);
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'post_type' => 'multiverso' , 'posts_per_page' => '-1', 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php 
			
			$i = 0;
			
			while ( $r->have_posts() ) : $r->the_post(); 
		
		  	$mv_access = get_post_meta(get_the_ID(), 'mv_access', true);
			$mv_user = get_post_meta(get_the_ID(), 'mv_user', true);
			global $current_user;
			get_currentuserinfo();
			$mv_logged = $current_user->user_login;
			
          if ($mv_access == 'registered' && !empty($mv_logged) && $i < $number ) { ?>
			<li>
            	<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
			<?php if ( $show_date ) : ?>
				<span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			</li>
          	
		<?php 
		
			$i++;
		
			} endwhile; 
			
			if ($i==0) { echo '<li>'.__('No Registered File Found','mvafsp').'</li>'; }
						
			?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_mv_registered_recent_files', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (bool) $new_instance['show_date'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['Multiverso_Recent_File']) )
			delete_option('Multiverso_Recent_File');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_mv_registered_recent_files', 'widget');
	}

	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mvafsp' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'mvafsp' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'mvafsp' ); ?></label></p>
<?php
	}
}

function multiverso_mv_registered_recent_files_widgets()
{
	register_widget( 'multiverso_mv_registered_recent_files' );
}

add_action( 'widgets_init', 'multiverso_mv_registered_recent_files_widgets' );



?>