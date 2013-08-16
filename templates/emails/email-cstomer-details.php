<?php
/**
 * Billing Customer details & Shipping Recipient details
 *
 * @author 		Kidberries team
 * @package 	WooCommerce/Templates/Emails
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?><table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
	<tr>
<?php

// Shipping Recipient details
$shipping_details = array();
$shipping_name = array();

if( $order->shipping_first_name )
    $shipping_name[] = $order->shipping_first_name;
if( $order->shipping_last_name )
    $shipping_name[] = $order->shipping_last_name;
    
if( !empty($shipping_name) )
    $shipping_details[] = '<strong>' . ( (string) implode(' ', $shipping_name) ) . '</strong>';

if ($order->shipping_phone)
    $shipping_details[] = '<a href="tel:' . preg_replace("/([^0-9]+)/", "", $order->shipping_phone) . '">' . $order->shipping_phone . '</a>';
    
if ($order->shipping_email)
    $shipping_details[] = '<a href="mailto:' . $order->shipping_email . '">' . $order->shipping_email . '</a>';
    
if( !empty( $shipping_details ) ) {

    echo '<td valign="top" width="50%">';
    echo '<h2>' . __( 'Recipient details', 'woocommerce' ) . '</h2>';
    echo '<p>' . implode( ", ",  $shipping_details ) . '</p>';
    echo '</td>';
}


// Billing Customer details
$billing_details = array();
$billing_name = array();

if( $order->billing_first_name )
    $billing_name[] = $order->billing_first_name;
if( $order->billing_last_name )
    $billing_name[] = $order->billing_last_name;
    
if( !empty($billing_name) )
    $billing_details[] = '<strong>' . ( (string) implode(' ', $billing_name) ) . '</strong>';

if ($order->billing_phone)
    $billing_details[] = '<a href="tel:' . preg_replace("/([^0-9]+)/", "", $order->billing_phone) . '">' . $order->billing_phone . '</a>';
    
if ($order->billing_email)
    $billing_details[] = '<a href="mailto:' . $order->billing_email . '">' . $order->billing_email . '</a>';
    
if( !empty( $billing_details ) ) {

    echo '<td valign="top" width="50%">';
    echo '<h2>' . __( 'Customer details', 'woocommerce' ) . '</h2>';
    echo '<p>' . implode( ", ",  $billing_details ) . '</p>';
    echo '</td>';
}

?>
	</tr>
</table>
