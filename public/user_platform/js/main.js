var $document = $(document),
		$element = $('#main-sidemenu'),
		className = 'hasScrolled';

	$document.scroll(function() {
		if ($document.scrollTop() >= 45 ) {
			$('#main-sidemenu').addClass( 'hasScrolled' );
			// $( '.scrolltop-wrapper' ).fadeIn();
		} else {
			$('#main-sidemenu').removeClass( 'hasScrolled' );
			// $( '.scrolltop-wrapper' ).fadeOut();
		}
	});