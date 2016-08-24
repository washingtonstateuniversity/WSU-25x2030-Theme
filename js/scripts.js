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
	 * Progress indicator - based on https://css-tricks.com/reading-position-indicator/
	 */
	var progress_indicator = function() {
		var total = function () {
				return $(document).height() - $(window).height();
			},
			position = function () {
				return $(window).scrollTop();
			},
			max = total(),
			width,
			set_width = function () {
				if ($(window).width() > 791) {
					width = (position() / max) * 100 + '%';
					$('.progress').css({ width: width });
				}
			};

		$(document).on('scroll', set_width);

		$(window).on('resize', function () {
			max = total();
			set_width();
		});
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

	// Doormat section navigation
	window.Doormat.prototype.go_to = function (index) {
		var panels = document.querySelectorAll('.panel');

		if (panels[index - 1]) {
			var pos = panels[index - 1].STARTING_POS;

			if (pos !== undefined) {
				$('body').animate({scrollTop: pos});
			}
		} else {
			throw Error('Doormat: no panel available at that index!');
		}
	};

	var initialize_doormat = function() {
		$('.site-menu, .spine-sitenav').on('click', 'a', function (e) {
			e.preventDefault();

			var li = $(this).parent('li'),
				section = li.index() + 2;

			drive_doormat.go_to(section);
			li.addClass('dogeared').siblings().removeClass('dogeared');
		});
	};

	/**
	 * Add the `dogeared` class to a nav item when its respective section has the `current` class
	 */
	var dogear_nav_items = function() {
		window.addEventListener('scroll', function () {
			if ($(window).width() > 791) {
				var panels = $('.panel'),
					nav_li = $('.site-menu li');

				$.each(panels, function (index) {
					if ($(this).hasClass('current')) {
						nav_li.eq(index - 1).addClass('dogeared').siblings().removeClass('dogeared');
					}
				});
			}
		}, 250);
	};

	var story_element_parallax = function() {
		// Story elements parallax
		var story_pieces = $('#stories .column');

		$(document).on('scroll', function () {
			if ($(window).width() > 791) {
				$.each(story_pieces, function (index) {
					var piece = $(this),
						velocity = (piece.hasClass('excerpt')) ? -2 + index : 2 + index,
						y = -($(window).scrollTop() - piece.offset().top) / parseInt(velocity);

					$(this).css('transform', 'translateY(' + y + 'px)');
				});
			}
		});
	};

	$(document).ready(function () {
		if ( ! is_iOS() && ! is_Android() ) {
			progress_indicator();
			cursor_parallax();
			story_element_parallax();
			initialize_doormat();
			dogear_nav_items();
		}
	});

}(jQuery));
