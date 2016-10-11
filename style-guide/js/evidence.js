(function ($, window) {

	//'use strict';

	var $filters = $('#story-filters'),
		$options = $('#filter-options'),
		offset = ($('body').hasClass('admin-bar')) ? 82 : 50;

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

	/**
	 * Apply `fixed` class to the filter drop-down if it has been scrolled to/past.
	 */
	fix_filters = function() {
		var $options_offset = $options.offset().top - offset;

		$(document).on('scroll', function () {
			if ($(window).scrollTop() >= $options_offset) {
				$options.addClass('fixed');
			} else {
				$options.removeClass('fixed');
			}
		});
	};

	/**
	 * Scroll to the filter drop-down when an option is selected.
	 */
	scroll_to_filter = function() {
		var filters_top = Math.round($filters.offset().top - offset);

		if ($(window).scrollTop() !== filters_top) {
			$('html, body').animate({
				scrollTop: filters_top
			}, 750);
		}
	}

	/**
	 * Update the header text when an option is selected from the filter drop-down.
	 */
	update_title = function( name, value ) {
		var $title_heading = $('.topic-title').find('h3');

		if ( value ) {
			$title_heading.html(name);
		} else {
			$title_heading.html('&nbsp;');
		}
	}

	$(document).ready( function() {
		process_column_backgrounds();
		fix_filters();
	});

	$options.on( 'change', function() {
		var option = $(this).find('option:selected'),
			name = option.text(),
			value = option.val();

		scroll_to_filter();
		update_title( name, value );
	});

}(jQuery, window));
