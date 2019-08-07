<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;
?>
	<!-- WIDGET -->
	<?php 
    	$widget_text = empty($instance['form']) ? '' : stripslashes($instance['form']);
		echo do_shortcode('[contact-form-7 id="' . $widget_text . '"]');
	?>
	
<?php echo $after_widget ?>