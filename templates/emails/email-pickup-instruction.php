<?php
    $instruction = get_post_meta( $order->id, '_order_shipping_customer_instruction', true );
    if( $instruction ) {
        if( is_array($instruction) ) 
            $instruction = implode("\n", array_map( 'esc_html', $instruction ) );
        else
            $instruction = esc_html( $instruction );

        echo "<h3>" . __( 'Pickup Instruction', 'woocommerce' ) . "</h3>";
        echo "<p>" . preg_replace("/(\n\r?)/", "<br/>", $instruction) . "</p>";
    }

?>
