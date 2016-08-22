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

	// Doormat section navigation
	window.Doormat.prototype.go_to = function (index) {
		var panels = document.querySelectorAll('.panel');

		if (panels[index - 1]) {
			var pos = panels[index - 1].STARTING_POS;

			if (pos !== undefined) {
				$('body').animate({scrollTop: pos});
			};
		} else {
			throw Error('Doormat: no panel available at that index!');
		}
	}

	$('.site-menu, .spine-sitenav').on('click', 'a', function (e) {
		e.preventDefault();

		var li = $(this).parent('li'),
			section = li.index() + 1;

		drive_doormat.go_to(section);
		li.addClass('dogeared').siblings().removeClass('dogeared');
	});

	// Add the `dogeared` class to a nav item when its respective section has the `current` class.
	window.addEventListener('scroll', function () {
		var panels = $('.panel'),
			nav_li = $('.site-menu li');

		$.each(panels, function(index) {
			if ($(this).hasClass('current')) {
				nav_li.eq(index).addClass('dogeared').siblings().removeClass('dogeared');
			}
		});
	}, 250);

}(jQuery));
