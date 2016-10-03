(function ($, window) {

	//'use strict';

	/**
	 * Look for any sections with background images stored as data attributes
	 * and convert the data attribute into inline CSS for that section.
	 */
	process_column_backgrounds = function() {
		$('.column.one').each( function() {
			var background_image = $(this).data('background'),
				mobile_background_image = $(this).data('background-mobile');

			if ( 990 > $(window).width() && mobile_background_image ) {
				$(this).css('background-image', 'url(' + mobile_background_image + ')' );
			} else if ( background_image ) {
				$(this).css('background-image', 'url(' + background_image + ')' );
			}
		});
	};

	$(document).ready( function() {
		process_column_backgrounds();
	});

}(jQuery, window));
