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

<?php do_action('woocommerce_before_order_notes', $checkout); ?>

<?php if (get_option('woocommerce_enable_order_comments')!='no') : ?>
	<div id="checkout_order_notes">
	<?php if ($woocommerce->cart->ship_to_billing_address_only()) : ?>

		<h3><?php _e( 'Additional Information', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>
	</div>

<?php endif; ?>

<?php do_action('woocommerce_after_order_notes', $checkout); ?>