<?php
/**
 * Email Order Totals
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( $totals = $order->get_order_item_totals() ) {
	$i = 0;
	foreach ( $totals as $total ) {
		$i++;
		?><tr>
			<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top: 1px solid #bbb;'; ?>"><?php echo $total['label']; ?></th>
			<td style="text-align:right; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top: 1px solid #bbb;'; ?>"><?php echo $total['value']; ?></td>
		</tr><?php
	}
}
?>
