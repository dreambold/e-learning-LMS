<?php
/**
 * Cross-sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $cross_sells ) : ?>

	<div class="cross-sells">
		
		<div class="heading">
			<h4><?php _e( 'You may be interested in&hellip;', 'woffice' ) ?></h4>
		</div>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $cross_sells as $cross_sell ) : ?>

			<?php
			$post_object = get_post( $cross_sell->get_id() );

			setup_postdata( $GLOBALS['post'] =& $post_object );

			wc_get_template_part( 'content', 'product' ); ?>

		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
