<?php 
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_theme_test extends WPBakeryShortCode {
    protected function content( $atts, $content = null ) {
        $output = '';

        extract( shortcode_atts( array(
            'title' => '',
        ), $atts ) );

        $width_class = '';
        $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $width_class, $this->settings['base'], $atts );

        ob_start(); ?>


        <video class="video" controls="controls">
                    <?php if( $title ) : ?>
                    <?php $title = preg_replace('/\s+/', '', $title);?>
                    <?php $title = str_replace('"', '', $title);?>
                    <?php $title = stristr($title, 'http');?>
                    <?php $title = current(explode(']', $title));?>
                    <source src="<?php echo $title ?>" type="video/mp4">
                    <?php endif ?>
        Your browser does not support the video tag.
        </video>

        <?php $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}

vc_map( array(
    'base' => 'theme_test',
    'name' => __( 'Video', 'text-domain' ),
    'class' => '',
    'category' => __( 'GWE Video' ),
    'icon' => 'icon-heart',
    'params' => array(
        array(
            'type' => 'textarea_html',
            'heading' => __( 'Select or upload video', 'text-domain' ),
            'param_name' => 'title',
            'value' => '',
        ),
    ),
) );

?>