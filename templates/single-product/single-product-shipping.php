<?php
global $woocommerce;

$available_methods = $woocommerce->shipping->get_available_shipping_methods();

if ( $available_methods ) {

	// Prepare text labels with price for each shipping method
	foreach ( $available_methods as $method ) {
		if( $woocommerce->session->chosen_shipping_method == $method->id ) {

			if( ! isset($method->cost) )
				$method->full_label = $method->label . '<br/><div class="extra">' . $method->label_extra . '</div>';
			elseif( $method->cost == 0 )
				$method->full_label = $method->label . ' - ' . __( 'Free!', 'woocommerce' ) . '<br/><div class="extra">' . $method->label_extra . '</div>';
			else
				$method->full_label = $method->label . ' - ' . woocommerce_price($method->cost) . '<br/><div class="extra">' . $method->label_extra . '</div>';
			
		} else {
			$method->full_label = $method->label;

			if ( ! isset($method->cost) ) {
				$method->full_label = $method->label . ' <span class="marked">' . __( 'Узнать', 'woocommerce' ) . '</span>';

            } elseif ( $method->cost == 0 ) {
                $method->full_label .= ' - <span class="free shipping">' . __( 'Free!', 'woocommerce' ) . '</span>';

            } elseif ( $method->cost > 0 ) {
                if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
                    $method->full_label .= ' - ' . woocommerce_price( $method->cost );
                    if ( $method->get_shipping_tax() > 0 && $woocommerce->cart->prices_include_tax ) {
                        $method->full_label .= ' <small>' . $woocommerce->countries->ex_tax_or_vat() . '</small>';
                    }
                } else {
                    $method->full_label .= ' - ' . woocommerce_price( $method->cost + $method->get_shipping_tax() );
                    if ( $method->get_shipping_tax() > 0 && ! $woocommerce->cart->prices_include_tax ) {
                        $method->full_label .= ' <small>' . $woocommerce->countries->inc_tax_or_vat() . '</small>';
                    }
                }
            }           
		}
		$method->full_label = apply_filters( 'woocommerce_cart_shipping_method_full_label', $method->full_label, $method );
	}

	// Print a single available shipping method as plain text
	if ( 1 === count( $available_methods ) ) {
		$checked_id = $method->method_id;
		echo wp_kses_post( $method->full_label ) . '<input type="hidden" name="shipping_method" id="shipping_method" value="' . esc_attr( $method->id ) . '" />';

	// Show select boxes for methods
	} else {

		echo '<ul id="shipping_method">';
		foreach ( $available_methods as $method ) {
			$checked = checked( $method->id, $woocommerce->session->chosen_shipping_method, false);
			if( $checked ) $checked_id = $method->method_id;

			echo '<li class="level1' . ( $checked ? ' checked"' : '' ) . '"><input class="radio" type="radio" class="shipping_method ' . sanitize_title( $method->id ) . '" name="shipping_method" id="shipping_method_' . sanitize_title( $method->id ) . '" value="' . esc_attr( $method->id ) . '" ' . $checked . ' /> <label for="shipping_method_' . sanitize_title( $method->id ) . '">' . $method->full_label . '</label></li>';
		}
		echo '</ul>';
	}

// No shipping methods are available
} else {

	if ( ! $woocommerce->customer->get_shipping_country() || ! $woocommerce->customer->get_shipping_state() || ! $woocommerce->customer->get_shipping_postcode() ) {

		echo '<p>' . __( 'Please fill in your details to see available shipping methods.', 'woocommerce' ) . '</p>';

	} else {

		$customer_location = $woocommerce->countries->countries[ $woocommerce->customer->get_shipping_country() ];

		echo apply_filters( 'woocommerce_no_shipping_available_html',
			'<p>' .
			sprintf( __( 'Sorry, it seems that there are no available shipping methods for your location (%s).', 'woocommerce' ) . ' ' . __( 'If you require assistance or wish to make alternate arrangements please contact us.', 'woocommerce' ), $customer_location ) .
			'</p>'
		);
	}
}
?>
<script type="text/javascript">
    jQuery(document).ready(function(){ 
        jQuery('#russianpost_places')
            .chosen({no_results_text: "Ничего не найдено!"})
            .ajaxChosen({
                minTermLength: 2,
                afterTypeDelay: 500,
                keepTypingMsg: "Продолжайте набирать...",
                lookingForMsg: "Ищем в базе",
                type: 'POST',
                url: woocommerce_params.ajax_url,
                data: {action:'get_russianpost_places'},
                jsonTermKey: "place",
                dataType: 'json'
            }, function (data) {
                var results = [];
                jQuery.each(data, function (i, item) {
                    results.push({ value: item.value, text: item.text });
                });
                return results;
            });
    });
</script>


