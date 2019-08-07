<?php
/**
 * Class Woffice_WooCommerce
 *
 * Customizations to WooCommerce required by Woffice
 *
 * @since 2.4.5
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_WooCommerce' ) ) {
    class Woffice_WooCommerce
    {

        /**
         * Woffice_WooCommerce constructor
         */
        public function __construct()
        {

            if (!function_exists('is_woocommerce'))
                return;

            // Single product change
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

            add_filter( 'woocommerce_output_related_products_args', array($this,'woffice_related_products_args'));

            // Remove up sell display
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

            add_action( 'woocommerce_after_single_product_summary', array($this, 'woffice_woocommerce_output_upsells'), 15 );

            add_filter( 'lostpassword_url',  array($this,'woffice_preserve_lostpassword_link'), 11, 0 );

            add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'woffice_woocommerce_header_add_to_cart_fragment'));

        }

        /**
         * Related products custom arguments
         *
         * @return mixed
         */
        public function woffice_related_products_args() {
            $args['posts_per_page'] = 4; // 4 related products
            $args['columns'] = 4; // arranged in 2 columns
            return $args;
        }

        /**
         * Custom output for the upsells
         */
        public function woffice_woocommerce_output_upsells() {
            woocommerce_upsell_display( 4,4 ); // Display 3 products in rows of 3
        }

        /**
         * We preserve the default lost password page
         *
         * @return string
         */
        public function woffice_preserve_lostpassword_link() {
            return get_option('siteurl') .'/wp-login.php?action=lostpassword';
        }

        /**
         * Custom Mini Cart
         *
         * @return void
         */
        static function print_mini_cart() {
            ?>
            <div id="woffice-minicart-top">
                <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
                    <ul class="woffice-minicart-top-products">
                        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                            $_product = $cart_item['data'];
                            // Only display if allowed
                            if ( ! apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $_product->exists() || $cart_item['quantity'] == 0 ) continue;
                            // Get price
                            $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax($_product) : wc_get_price_including_tax($_product);
                            $product_price = apply_filters( 'woocommerce_cart_item_price_html', wc_price( $product_price ), $cart_item, $cart_item_key );
                            ?>
                            <li class="woffice-mini-cart-product clearfix">
						<span class="woffice-mini-cart-thumbnail">
							<?php echo $_product->get_image(); ?>
						</span>
                                <span class="woffice-mini-cart-info">
							<a class="woffice-mini-cart-title" href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">
								<h4><?php echo apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product ); ?></h4>
							</a>
                                    <?php echo apply_filters( 'woocommerce_widget_cart_item_price', '<span class="woffice-mini-cart-price">' . __('Unit Price', 'woffice') . ':' . $product_price . '</span>', $cart_item, $cart_item_key ); ?>
                                    <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="woffice-mini-cart-quantity">' . __('Quantity', 'woffice') . ':' . $cart_item['quantity'] . '</span>', $cart_item, $cart_item_key ); ?>
						</span>
                            </li>
                        <?php endforeach; ?>
                    </ul><!-- end .tee-mini-cart-products -->
                <?php else : ?>
                    <p class="woffice-mini-cart-product-empty"><?php _e( 'No products in the cart.', 'woffice' ); ?></p>
                <?php endif; ?>
                <?php if (sizeof( WC()->cart->get_cart()) > 0) : ?>
                    <h4 class="text-center woffice-mini-cart-subtotal"><?php _e( 'Cart Subtotal', 'woffice' ); ?>: <?php echo WC()->cart->get_cart_subtotal(); ?></h4>
                    <div class="text-center">
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart btn btn-default">
                            <i class="fa fa-shopping-cart"></i> <?php _e( 'Cart', 'woffice' ); ?>
                        </a>
                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="alt checkout btn btn-default">
                            <i class="fa fa-credit-card"></i> <?php _e( 'Checkout', 'woffice' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }

        /**
         * Ensure cart contents update when products are added to the cart via AJAX
         *
         * @param array $fragments
         * @return array
         */
        public function woffice_woocommerce_header_add_to_cart_fragment( $fragments ) {
            ob_start();
            ?>
            <a href="javascript:void(0)" id="nav-cart-trigger" title="<?php _e( 'View your shopping cart', 'woffice' ); ?>" class="<?php echo sizeof(WC()->cart->get_cart()) > 0 ? 'active' : ''; ?> cart-contents">
                <i class="fa fa-shopping-cart"></i>
                <?php echo (sizeof( WC()->cart->get_cart()) > 0) ? WC()->cart->get_cart_subtotal() : ''; ?>
            </a>
            <?php

            $fragments['a.cart-contents'] = ob_get_clean();

            return $fragments;
        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_WooCommerce();



