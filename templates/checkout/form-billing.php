<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$shiptobilling = ( get_option('woocommerce_ship_to_same_address') == 'yes' ) ? 1 : 0;
$shiptobilling = apply_filters('woocommerce_shiptobilling_default', $shiptobilling);

if ( ! empty( $_POST ) ) {
    $shiptobilling = $checkout->get_value('shiptobilling') ? $checkout->get_value('shiptobilling') : $shiptobilling;
}

foreach ( array('billing','shipping') as $section ) {
	foreach ( array('address_2','company','state') as $field ) {
		unset( $checkout->checkout_fields[ $section ][ join('_', array( $section, $field ) ) ] );
	}
}
$checkout->checkout_fields['shipping']['shipping_phone'] = $checkout->checkout_fields['billing']['billing_phone'];

?>
<div class="billing_address" id="checkout_billing_address">
<?php if ( $woocommerce->cart->ship_to_billing_address_only() && $woocommerce->cart->needs_shipping() ) : ?>

	<h3><?php _e( 'Shipping', 'woocommerce' ); ?></h3>

<?php else : ?>
	<h3>
		<span><?php _e( 'My Address', 'woocommerce' ); ?></span>
        <span></span>
		<span id="shiptopayer" style="float: right; font-weight: normal;">
			<input id="shiptobilling-checkbox" class="input-checkbox" checked="checked" type="checkbox" name="shiptobilling" />
			<label for="shiptobilling-checkbox" class="checkbox"><?php _e( 'Ship to my address?', 'woocommerce' ); ?></label>
		</span>
	</h3>



<?php endif; ?>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout ); ?>

<?php foreach ($checkout->checkout_fields['billing'] as $key => $field) : ?>

	<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

<?php endforeach; ?>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>



<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

	<?php if ( $checkout->enable_guest_checkout ) : ?>

		<p class="form-row">
			<input class="input-checkbox" id="createaccount" <?php checked($checkout->get_value('createaccount'), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
		</p>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

	<div class="create-account">

		<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></p>

		<?php foreach ($checkout->checkout_fields['account'] as $key => $field) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<div class="clear"></div>

	</div>

	<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

<?php endif; ?>
</div>
