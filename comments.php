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
			'author' => '<p class="comment-form-author"><label for="author">Name</label> <input id="author" name="author" value="" maxlength="245" aria-required="true" required="required" type="text"></p>',
			'email' => '<p class="comment-form-email"><label for="email">Email Address</label> <input id="email" name="email" value="" maxlength="100" aria-describedby="email-notes" aria-required="true" required="required" type="email"></p>',
		),
		'comment_field' => '<p class="comment-form-comment"><label for="comment">Comments</label> <textarea id="comment" name="comment" maxlength="65525" aria-required="true" required="required"></textarea></p>',
		'comment_notes_before' => '',
		'class_form' => 'drive-comment-form',
		'title_reply' => '',
		'cancel_reply_before' => '',
		'cancel_reply_after' => '',
		'cancel_reply_link' => '',
		'label_submit' => 'Submit Feedback',
		'format' => 'html5',
	) );

	if ( have_comments() ) {
		?>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style' => 'ol',
				'type' => 'comment',
				'avatar_size' => 99, // 198/2
				'format' => 'html5',
			) );
			?>
		</ol>

		<?php the_comments_navigation(); ?>

		<?php
	}

	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		?><p class="no-comments">Comments are closed.</p><?php
	}
	?>

</div>
