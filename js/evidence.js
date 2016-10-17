(function ($, window, document, evidence) {

	'use strict';

	var $filters = $('#story-filters'),
		$wrapper = $('#filter-options'),
		$options = $wrapper.find('select'),
		offset = ($('body').hasClass('admin-bar')) ? 82 : 50,
		fetching = false,
		all_stories = false,
		$story_container = $('.evidence-stories-container');

	/**
	 * Look for any sections with background images stored as data attributes
	 * and convert the data attribute into inline CSS for that section.
	 */
	function process_column_backgrounds() {
		$('.column.one').each(function () {
			var background_image = $(this).data('background'),
				mobile_background_image = $(this).data('background-mobile');

			if (990 > $(window).width() && mobile_background_image) {
				$(this).css('background-image', 'url(' + mobile_background_image + ')');
			} else if (background_image) {
				$(this).css('background-image', 'url(' + background_image + ')');
			}
		});
	}

	/**
	 * Apply `fixed` class to the filter drop-down if it has been scrolled to/past.
	 */
	function fix_filters() {
		var $wrapper_offset = $wrapper.offset().top - offset;

		$(document).on('scroll', function () {
			if ($(window).scrollTop() >= $wrapper_offset) {
				$wrapper.addClass('fixed');
			} else {
				$wrapper.removeClass('fixed');
			}
		});
	}

	/**
	 * Check if the footer is in the viewport.
	 */
	function footer_is_visible() {
		var footer = $('.site-footer');

		if (footer instanceof jQuery) {
			footer = footer[0];
		}

		var rect = footer.getBoundingClientRect();

		return rect.bottom > 0 &&
			rect.right > 0 &&
			rect.left < (window.innerWidth || document.documentElement.clientWidth) &&
			rect.top < (window.innerHeight || document.documentElement.clientHeight);
	}

	/**
	 * Make an AJAX call and display the response.
	 */
	function fetch_and_display_stories(data, type) {
		$.ajax({
			type: 'post',
			url: evidence.ajax_url,
			data: data,
			beforeSend: function () {
				fetching = true;
				if ('scrolled' === type) {
					$story_container.append('<div class="loading"></div>');
				} else if ('filtered' === type) {
					$story_container.find('.section-wrapper').hide(400);
				}
			}
		}).done(function (response) {
			if ('scrolled' === type) {
				$('.loading').remove();
				$story_container.append($.parseJSON(response));
				process_column_backgrounds();
				$story_container.data('page', data.page + 1);
			} else if ('filtered' === type) {
				$story_container.html($.parseJSON(response));
				process_column_backgrounds();
				$story_container.data('page', 1);
			}
			$('.new').show(400);
		}).always(function () {
			fetching = false;
		});
	}

	/**
	 * Load more posts when the footer is reached.
	 */
	function infinite_scroll() {
		window.addEventListener('scroll', function () {
			if (fetching ||
					all_stories ||
					!footer_is_visible() ||
					$story_container.data('page') === $story_container.data('total-pages') ||
					'' !== $('#filter-options').find('option:selected').val()) {
				return;
			}

			var data = {
					action: 'evidence_stories',
					nonce: evidence.nonce,
					page: $story_container.data('page')
				};

			fetch_and_display_stories(data, 'scrolled');
		}, 500);
	}

	/**
	 * Scroll to the filter wrapper when an option is selected.
	 */
	function scroll_to_filter() {
		var filters_top = Math.round($filters.offset().top - offset);

		if ($(window).scrollTop() !== filters_top) {
			$('html, body').animate({
				scrollTop: filters_top
			}, 750);
		}
	}

	/**
	 * Update the header text and document title when an option is selected from the filter drop-down.
	 */
	function update_title(name, value) {
		var heading = value ? name : ' ',
			doc_title = value ? name + ' | ' + evidence.default_title : evidence.default_title;

		$('.topic-title').find('h3').html(heading);

		document.title = doc_title;
	}

	/**
	 * Update the history state.
	 */
	function update_history(name, value) {
		var state = { name: name, value: value },
			url = value ? evidence.default_url + 'category/' + value + '/' : evidence.default_url;

		history.pushState(state, null, url);
	}

	$(document).ready(function () {
		var option = $(this).find('option:selected'),
			value = option.val(),
			name = value ? option.text() : '&nbsp;',
			state = { name: name, value: value };

		history.replaceState(state, null, null);
		process_column_backgrounds();
		fix_filters();
		infinite_scroll();
	});

	$options.on('change', function () {
		var option = $(this).find('option:selected'),
			name = option.text(),
			value = option.val(),
			data = {
				action: 'evidence_stories',
				nonce: evidence.nonce,
				category: value
			};

		scroll_to_filter();
		update_title(name, value);
		update_history(name, value);
		fetch_and_display_stories(data, 'filtered');
	});

	window.onpopstate = function(event) {
		var name = event.state.name,
			value = event.state.value,
			data = {
				action: 'evidence_stories',
				nonce: evidence.nonce,
				category: value
			};

		$options.val(value);
		update_title(name, value);
		fetch_and_display_stories(data, 'filtered');
	};

}(jQuery, window, document, evidence));
