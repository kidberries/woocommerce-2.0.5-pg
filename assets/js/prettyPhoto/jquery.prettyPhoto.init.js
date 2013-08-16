(function($) {
$(document).ready(function() {



	// Lightbox
	$("a.zoom").unbind().prettyPhoto({
		social_tools: false,
		theme: 'pp_woocommerce',
		horizontal_padding: 40,
		opacity: 0.9,
		deeplinking: false
	}).addClass('lightboxed');

	// Hide review form - it will be in a lightbox
	$('#review_form_wrapper').hide();
	$("a.show_review_form").unbind().prettyPhoto({
		social_tools: false,
		theme: 'pp_woocommerce',
		horizontal_padding: 40,
		opacity: 0.9,
		deeplinking: false
	});
	$("a[rel^='prettyPhoto']").unbind().prettyPhoto({
		social_tools: false,
		theme: 'pp_woocommerce',
		horizontal_padding: 40,
		opacity: 0.9,
		deeplinking: false
	}).addClass('lightboxed');;

	// Open review form lightbox if accessed via anchor
	if( window.location.hash == '#review_form' ) {
		$('a.show_review_form').trigger('click');
	}

});
})(jQuery);