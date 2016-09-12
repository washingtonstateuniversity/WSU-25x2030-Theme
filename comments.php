<?php
/**
 * The template for displaying comments.
 * Modified from Twenty Sixteen's comment template.
 *
 * The area of the page that contains both comments and the comment form.
 */

/*
 * If the current post is protected by a password and the visitor has not
 * yet entered the password, return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">

	<?php
	comment_form( array(
		'fields' => array(
			'author' => '<p class="comment-form-author"><label for="author">Name <span class="optional">(Optional)</span></label> <input id="author" name="author" value="" maxlength="245" aria-required="true" required="required" type="text"></p>',
			'email' => '<p class="comment-form-email"><label for="email">Email Address <span class="optional">(Optional)</span></label> <input id="email" name="email" value="" maxlength="100" aria-describedby="email-notes" aria-required="true" required="required" type="email"></p>',
		),
		'comment_field' => '<p class="comment-form-comment"><label for="comment">Comments</label> <textarea id="comment" name="comment" maxlength="65525" aria-required="true" required="required"></textarea></p>',
		'comment_notes_before' => '',
		'class_form' => 'drive-comment-form',
		'title_reply' => '',
		'title_reply_to' => '',
		'title_reply_before' => '',
		'title_reply_after' => '',
		'cancel_reply_before' => '',
		'cancel_reply_after' => '',
		'cancel_reply_link' => '',
		'label_submit' => 'Submit feedback',
		'format' => 'html5',
	) );

	if ( have_comments() ) {
		$comments = get_comments( array(
			'post_id' => get_option( 'page_on_front' ),
			'status' => 'approve',
		) );

		?><div class="comment-list"><?php

		wp_list_comments( array(
			'max_depth' => 1,
			'style' => 'div',
			'type' => 'comment',
			'avatar_size' => 0,
			'format' => 'html5',
			'reverse_top_level' => false,
		), $comments );

		?>
		</div>
		<?php

		if ( get_comment_pages_count() > 1 ) {

			?><div class="comment-nav"><?php

			$comment_navigation_args = array(
				'prev_text' => 'Newer comments',
				'next_text' => 'Older comments',
			);

			the_comments_navigation( $comment_navigation_args );

			?></div><?php
		}
	}
	?>

</div>
