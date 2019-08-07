<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Billing address', 'woocommerce' ),
		'shipping' => __( 'Shipping address', 'woocommerce' ),
	), $customer_id );
} else {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Billing address', 'woocommerce' ),
	), $customer_id );
}

$oldcol = 1;
$col    = 1;
?>

	<p>
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', __( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); ?>
	</p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	<div class="u-columns woocommerce-Addresses row addresses" data-template="woffice">
<?php endif; ?>

<?php foreach ( $get_addresses as $name => $title ) : ?>

	<div class="col-md-6 woocommerce-Address" data-template="woffice">
		<header class="woocommerce-Address-title title">
			<h3><?php echo $title; ?></h3>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit d-block" data-template="woffice"><?php _e( 'Edit', 'woocommerce' ); ?></a>
		</header>
		<address><?php
			$address = wc_get_account_formatted_address( $name );
			echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
			?></address>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
<?php endif;
