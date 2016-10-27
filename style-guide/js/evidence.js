(function ($, window, document, evidence) {

	'use strict';

	var $filter_column = $('.intro').find('.two'),
		$filter_wrapper = $('#filter-options'),
		$filter = $filter_wrapper.find('select'),
		offset = ($('body').hasClass('admin-bar') && $(window).width() > 990) ? 82 : 50,
		filter_top = $filter_column.offset().top - offset,
		fetching = false,
		$story_container = $('.evidence-stories-container');

	/**
	 * Recalculate `filter_top` value on resize.
	 */
	$(window).resize(function() {
		offset = ($('body').hasClass('admin-bar') && $(window).width() > 990) ? 82 : 50;
		filter_top = $filter_column.offset().top - offset;
	});

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
		$(document).on('scroll', function () {
			if ($(window).scrollTop() >= filter_top) {
				$filter_wrapper.addClass('fixed');
			} else {
				$filter_wrapper.removeClass('fixed');
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
	 * Output the markup for a story.
	 */
	function build_story(title, content, featured_img, mobile_img, even) {
		var reverse = even ? ' reverse' : '',
			story = '<div class="section-wrapper new">' +
						'<section class="row side-left' + reverse + '">' +
							'<div class="column one" data-background="' + featured_img + '" data-background-mobile="' + mobile_img + '">' +
							'</div>' +
							'<div class="column two">' +
								'<header>' +
									'<h2>' + title + '</h2>' +
								'</header>' +
								content +
							'</div>' +
						'</section>' +
					'</div>';

		return story;
	}

	/**
	 * Make an AJAX call and display the response.
	 */
	function fetch_and_display_stories(data, type) {
		$.ajax({
			url: evidence.request_url_base,
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
			var fetched_stories = '';
			$.each(response, function (index, story) {
				var title = story.title.rendered,
					content = story.content.rendered,
					featured_img = story._embedded['wp:featuredmedia'][0].source_url,
					mobile_img = story._embedded['wp:mobilemedia'][0].source_url,
					even = (0 === index % 2) ? false : true;

				fetched_stories += build_story(title, content, featured_img, mobile_img, even);
			});
			if ('scrolled' === type) {
				$('.loading').remove();
				$story_container.append(fetched_stories);
				$story_container.data().page++;
			} else if ('filtered' === type) {
				$story_container.html(fetched_stories);
				$story_container.data('page', 1);
			}
			process_column_backgrounds();
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
					!footer_is_visible() ||
					$story_container.data('page') >= $story_container.data('total-pages') ||
					'' !== $('#filter-options').find('option:selected').val()) {
				return;
			}
			var data = {
					per_page: 10,
					offset: $story_container.data('page') * 10
				};

			fetch_and_display_stories(data, 'scrolled');
		}, 500);
	}

	/**
	 * Scroll to the filter wrapper when an option is selected.
	 */
	function scroll_to_filter() {
		if ($(window).scrollTop() !== filter_top) {
			$('html, body').animate({
				scrollTop: filter_top
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

		history.pushState(state, '', url);
	}

	$(document).ready(function () {
		var option = $filter.find('option:selected'),
			value = option.val(),
			name = value ? option.text() : '&nbsp;',
			state = { name: name, value: value };

		history.replaceState(state, '');
		process_column_backgrounds();
		fix_filters();
		infinite_scroll();
	});

	$filter.on('change', function () {
		var option = $(this).find('option:selected'),
			name = option.text(),
			value = option.val(),
			data = {
				'filter[wsuwp_university_category]': value
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
				'filter[wsuwp_university_category]': value
			};

		$filter.val(value);
		update_title(name, value);
		fetch_and_display_stories(data, 'filtered');
	};

}(jQuery, window, document, evidence));
