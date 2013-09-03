(function($) {
	var $carousel = $( '#carousel' );
	var $wrapper = $( '#carousel-wrapper' );
	var $window = $( window );
 
	$carousel.carouFredSel({
		width: '100%',
		scroll: 1,
		items: {
			visible: 'odd+2',
			start: -1
		},
		auto: {
			timeoutDuration: 8000
		},
		pagination: {
			container: $( '#carousel-pagination' )
		}
	});
})(jQuery);