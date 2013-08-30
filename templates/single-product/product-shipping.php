<?php
/**
 * Single Product Shipping
 *
 * @author      Andrey
 * @package     WooCommerce/Templates
 * @version     1.0
 */

?>


<!-- Shipping methods -->

<div class="delivery-box">
    <span class="title"><span class="big">Доставка:</span></span>
    <ul id="shipping_method">
    </ul>
    <small>* <em>стоимость доставки вашей корзины <strong>вместе с этим товаром</strong></em></small>
</div>

<script type="text/javascript">
    jQuery(document).ready( function() {
        var data = {
            action:         "get_dynamic_shipping",
            security:       woocommerce_params.update_shipping_method_nonce,
            product_id:     jQuery("form.cart").data("product_id"),
            variation_id:   jQuery("input[name=variation_id]").val()
        };
        jQuery("#shipping_method").block({message: null, overlayCSS: {background: "#fff url(" + woocommerce_params.ajax_loader_url + ") no-repeat center", backgroundSize: "16px 16px", opacity: 0.6}});
        jQuery.post( woocommerce_params.ajax_url, data, function(response) { jQuery("#shipping_method").replaceWith( response ); });
    });
</script>
<!-- /Shipping methods -->

