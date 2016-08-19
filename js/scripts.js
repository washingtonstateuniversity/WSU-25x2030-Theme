(function ($) {

	'use strict';

	// Progress indicator - based on https://css-tricks.com/reading-position-indicator/
	$(document).ready(function () {
		var total = function () {
				return $(document).height() - $(window).height();
			},
			position = function () {
				return $(window).scrollTop();
			},
			max = total(),
			width,
			set_width = function () {
				width = (position() / max) * 100 + '%';
				$('.progress').css({ width: width });
			};

		$(document).on('scroll', set_width);

		$(window).on('resize', function () {
			max = total();
			set_width();
		});
	});

	// Cursor parallax - based on http://codepen.io/chrisboon27/pen/rEDIC
	// Could add http://matthewlehner.net/ios-7-style-parallax-background-based-on-device-orientation/
	// for mobile device support, though https://github.com/eklimcz/Mobile-Parallax-Effect/
	// might be a better option in general.
	$(document).ready(function () {
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
	});

	// Typekit
	try{Typekit.load();}catch(e){};
}(jQuery));
