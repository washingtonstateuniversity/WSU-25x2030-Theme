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
}(jQuery));
