(function ($) {

	'use strict';

	/**
	 * Handle comment form submissions through AJAX.
	 */
	var ajax_comment_submission = function() {
		var comment_form = $('#commentform'),
			action = comment_form.attr('action'),
			processing = $('.comment-processing'),
			success = $('.comment-success'),
			data = '';

		comment_form.submit(function() {
			data = comment_form.serialize();

			processing.html('<div class="processing-indicator"></div>');

			$.ajax({
				type: 'post',
				url: action,
				data: data,
				success: function () {
					processing.html('');
					success.show();
					comment_form.find('textarea, input:not([type=submit])').val('');
				}
			});

			return false;
		});
	};

	/**
	 * Toggle comment text display.
	 */
	var toggle_comment_text = function() {
		$('.comment-list').on('click', '.remainder-toggle', function (e) {
			e.preventDefault();

			var link = $(this),
				ellipsis = link.closest('.comment-content').find('.ellipsis'),
				remainder = link.closest('.comment-content').find('.comment-remainder');

			if ( link.hasClass('close') ) {
				link.removeClass('close');
				remainder.hide();
				ellipsis.show();
				link.text('» Show more');
			} else {
				link.addClass('close');
				ellipsis.hide();
				remainder.show();
				link.text('« Show less');
			}

		});
	};

	/**
	 * Comment pagination.
	 */
	var comment_pagination = function() {
		$('.comment-nav').on('click', 'a', function (e) {
			e.preventDefault();

			$('html, body').animate({
				scrollTop: $('.comment-list').offset().top - 50
			}, 100);

			var data = {
					action: 'comment_navigation',
					url: $(this).attr('href'),
					nonce: comments.nonce
				};

			$.post(comments.ajax_url, data, function (response) {
				var response_data = $.parseJSON(response);

				$('.comment-list').html(response_data.comments);
				$('.comment-nav').html(response_data.navigation);
			});
		});
	};

	$(document).ready(function () {
		ajax_comment_submission();
		toggle_comment_text();
		comment_pagination();
	});

}(jQuery));
