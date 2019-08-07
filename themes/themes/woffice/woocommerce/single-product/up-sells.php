<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $upsells ) : ?>

	<div class="upsells products">

		<h2><?php _e( 'You may also like&hellip;', 'woocommerce' ) ?></h2>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $upsells as $upsell ) : ?>

			<?php
			$post_object = get_post( $upsell->get_id() );

			setup_postdata( $GLOBALS['post'] =& $post_object );

			wc_get_template_part( 'content', 'product' ); ?>

		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
