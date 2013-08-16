<?php
/**
 * Checkout shipping information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php if ( ( $woocommerce->cart->needs_shipping() || get_option('woocommerce_require_shipping_address') == 'yes' ) && ! $woocommerce->cart->ship_to_billing_address_only() ) : ?>

	<div class="shipping_address" id="checkout_shipping_address">
		<h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
		<br clear="all" />

		<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

		<?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

	</div>

<?php endif; ?>
