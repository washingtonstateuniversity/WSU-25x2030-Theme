(function ($) {

	'use strict';

	/**
	 * Determine if the current view is an iOS device.
	 *
	 * @returns {boolean}
	 */
	var is_iOS = function() {
		return ( window.navigator.userAgent.match( /(iPad|iPhone|iPod)/ig ) ? true : false );
	};

	/**
	 * Determine if the current view is an Android device.
	 *
	 * @returns {boolean}
	 */
	var is_Android = function() {
		return ( window.navigator.userAgent.match( /(Android)/ig ) ? true : false );
	};

	/**
	 * Cursor parallax - based on http://codepen.io/chrisboon27/pen/rEDIC
	 */
	var cursor_parallax = function() {
		var strength = 25,
			height = strength / $(window).height(),
			width = strength / $(window).width();

		$('#top').on('mousemove', function (e) {
			var x = e.pageX - ($(window).width() / 2),
				y = e.pageY - ($(window).height() / 2),
				new_x = -(width * x) - 25,
				new_y = -(height * y) - 50;

			$('.image').css('transform', 'matrix(1, 0, 0, 1, ' + new_x + ', ' + new_y + ')');
		});
	};

	$(document).ready(function () {
		if ( ! is_iOS() && ! is_Android() ) {
			cursor_parallax();
		}
	});

}(jQuery));
