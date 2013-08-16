jQuery(document).ready(function($) {
    // Chosen
    if( jQuery().chosen )
        $(".chzn-select").chosen({no_results_text: "Ничего не найдено!"});

	// Shipping calculator
	$(document).on( 'click', '.shipping-calculator-button', function() {
		$('.shipping-calculator-form').slideToggle('slow');
		return false;
	}).on( 'change', 'select#shipping_method, input[name="shipping_method"], .shipping_method_variant, .shipping_method_sub_variant, select.russianpost_places.ajax-chzn-select', function() {

        var data = {
            action: 		            'woocommerce_update_shipping_method',
            security: 		             woocommerce_params.update_shipping_method_nonce
        };

        data.shipping_method = $( '#' + $(this).closest('label[for]').attr('for') ).val() || $(this).closest('select#shipping_method, input[name="shipping_method"]').val();
        

        if( $(this).hasClass('shipping_method_sub_variant') ) {
            data.shipping_method_variant = $('.shipping_method_variant').val() || $('.shipping_method_variant option:selected').val();
            data.shipping_method_sub_variant = $('.shipping_method_sub_variant:checked, .shipping_method_sub_variant:selected').val();

        } else if( $(this).hasClass('shipping_method_variant') ) {
            data.shipping_method_variant = $('.shipping_method_variant').val();
        }
        /*
        console.log( data.shipping_method );
        console.log( data.shipping_method_variant );
        console.log( data.shipping_method_sub_variant );
*/
	        $('div.cart_totals').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});


                if( $('select.russianpost_places.ajax-chzn-select').val() ) {
                        data.shipping_method_variant = $('select.russianpost_places.ajax-chzn-select').val();
                        //data.hidden_city     = $('select.russianpost_places.ajax-chzn-select option[value="'+data.hidden_postcode+'"]').html();
                        data.s_city          = $('select.russianpost_places.ajax-chzn-select option[value="'+data.shipping_method_variant+'"]').html();
               
                }

		$.post( woocommerce_params.ajax_url, data, function(response) {

			$('div.cart_totals').replaceWith( response );

                        if( jQuery().chosen ) {
                                $(".chzn-select")
                                    .chosen({no_results_text: "Ничего не найдено! "})
                                    .change(function(e){
                                        var This = $(this);
                                        var Val  = $(this).val();
                                        var Text = This.find('option[value="' + Val + '"]').html();
                                        
                                        if( This.hasClass('s_city') ) {
                                            $('#shipping_method').val( $( '#' + $(this).closest('label[for]').attr('for') ).val() );

                                            This.find('option[value="' + Val + '"]').attr('selected','selected');
                                            
                                            if( This.hasClass('shipping_city') ) {
                                                if( $('input[name="shiptobilling"]:checked') ) {
                                                    $('#billing_city').val( Text );
                                                    data.city = Text;
                                                }
                                                $('#shipping_city').val( Text );
                                                data.s_city = Text;
                                            }
                                        }
                                    });
                        }
			if( jQuery().ajaxChosen ) {
				jQuery('select.ajax-chzn-select.russianpost_places')
					.chosen({no_results_text: "Ничего не найдено! "})
					.change(function(e){
						var This = $(this);
						var Val  = $(this).val();
						var Text = This.find('option[value="' + Val + '"]').html();
						var custom_data = {};

						This.find('option[value="' + Val + '"]').attr('selected','selected');

						if( $('input[name="shiptobilling"]:checked') ) {
							$('#billing_city').val( Text );
							custom_data.city = Text;
						}
						$('#shipping_city').val( Text );
						custom_data.s_city = Text;
						custom_data.shipping_method_variant = Val;

						$.extend(data,custom_data);
					})
					.ajaxChosen({
						minTermLength: 2,
						afterTypeDelay: 500,
						keepTypingMsg: "Продолжайте набирать...",
						lookingForMsg: "Ищем в базе ",
						type: 'POST',
						url: woocommerce_params.ajax_url,
						data: {action:'get_russianpost_places'},
						jsonTermKey: "place",
						dataType: 'json'
					}, function (data) {
						var results = [];
						jQuery.each(data, function (i, item) { results.push({ value: item.value, text: item.text }); });
						return results;
					});
			}
            
			$('body').trigger('updated_shipping_method');

		});
	});

	$('.shipping-calculator-form').hide();
});
