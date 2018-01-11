<?php

namespace WSU\D25\Comments;

add_filter( 'notify_moderator', '__return_false' );
add_filter( 'comment_form_fields', 'WSU\D25\Comments\form_fields' );
add_action( 'init', 'WSU\D25\Comments\apply_comment_filter' );
add_action( 'wp_ajax_nopriv_comment_navigation', 'WSU\D25\Comments\ajax_callback' );
add_action( 'wp_ajax_comment_navigation', 'WSU\D25\Comments\ajax_callback' );
add_action( 'wp_enqueue_scripts', 'WSU\D25\Comments\enqueue_scripts' );
add_shortcode( 'comments_template', 'WSU\D25\Comments\display_template' );

/**
 * Returns the number of comments to display per page.
 *
 * @return int
 */
function per_page() {
	return 5;
}

/**
 * Removes the URL field from the comment form.
 * Repositions the comment text area below the name and email fields.
 *
 * @param array $fields The default comment form fields.
 *
 * @return array
 */
function form_fields( $fields ) {
	$comment_field = $fields['comment'];

	unset( $fields['url'] );
	unset( $fields['comment'] );

	$fields['comment'] = $comment_field;

	return $fields;
}

/**
 * Applies comment text filter if not in the admin.
 */
function apply_comment_filter() {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	add_filter( 'comment_text', 'WSU\D25\Comments\comment_filter', 10, 2 );
}

/**
 * If a comment exceeds 250 bytes, wrap the remainder in a span and
 * add a link that can be clicked to toggle visibility of the remainder.
 *
 * @param string     $comment_text
 * @param WP_Comment $comment
 *
 * @return string
 */
function comment_filter( $comment_text, $comment = null ) {
	if ( null === $comment ) {
		return $comment_text;
	}

	$length = strlen( $comment_text );
	$excerpt_length = 250;

	if ( $length > $excerpt_length ) {
		$excerpt = substr( $comment_text, 0, strpos( $comment_text, ' ', $excerpt_length ) );

		if ( '' !== $excerpt ) {
			$comment_remainder = substr( $comment_text, strlen( $excerpt ), $length );
			$toggle_link = '<p><a href="#" class="remainder-toggle">&raquo; Show more</a></p>';

			$paragraphs = preg_split( "/\\r\\n|\\r|\\n/", $comment_remainder );

			if ( 1 < count( $paragraphs ) ) {
				$new_remainder = '<span class="ellipsis">&hellip;</span><span class="comment-remainder">' . $paragraphs[0] . '</span>';
				unset( $paragraphs[0] );
				$new_remainder .= '<div class="comment-remainder">' . "\n\n" . implode( "\n\n", $paragraphs ) . "\n\n" . '</div>';
				$comment_text = $excerpt . $new_remainder . $toggle_link;
			} else {
				$comment_text = $excerpt . '<span class="ellipsis">&hellip;</span><span class="comment-remainder">' . $comment_remainder . '</span>' . "\n" . $toggle_link;
			}
		}
	}

	return $comment_text;
}

/**
 * Retrieve the navigation markup for the current page of comments.
 *
 * @since 0.0.19
 *
 * @param int   $page     The page of comments to retrieve.
 * @param array $comments Array of comment objects.
 *
 * @return string
 */
function navigation( $page, $comments ) {
	$navigation = '';

	$comment_pages_count = get_comment_pages_count( $comments, per_page() );

	if ( $comment_pages_count > 1 ) {
		$prev = intval( $page ) - 1;

		if ( intval( $page ) > 1 ) {
			$url = get_comments_pagenum_link( $prev );
			$navigation .= '<div class="nav-previous"><a href="' . esc_url( $url ) . '">Newer comments</a></div>';
		}

		$next = intval( $page ) + 1;

		if ( intval( $page ) < $comment_pages_count ) {
			$url = get_comments_pagenum_link( $next, $comment_pages_count );
			$navigation .= '<div class="nav-next"><a href="' . esc_url( $url ) . '">Older comments</a></div>';
		}

		$navigation = _navigation_markup( $navigation, 'comment-navigation', 'Comments navigation' );
	}

	return $navigation;
}

/**
 * Retrieve the requested page of comments.
 *
 * @since 0.0.19
 */
function ajax_callback() {
	check_ajax_referer( 'comments-paging', 'nonce' );

	$results = array();

	if ( isset( $_POST['url'] ) ) {
		$url = esc_url( $_POST['url'] );
	} else {
		$url = '';
	}

	$page = ( strpos( $url, 'comment-page-' ) ) ? substr( $url, strpos( $url, 'comment-page-' ) + 13, -10 ) : 1;

	$comments = get_comments( array(
		'post_id' => get_option( 'page_on_front' ),
		'status' => 'approve',
	) );

	$comment_list = wp_list_comments( array(
		'max_depth' => 1,
		'style' => 'div',
		'type' => 'comment',
		'page' => $page,
		'per_page' => per_page(),
		'avatar_size' => 0,
		'format' => 'html5',
		'reverse_top_level' => false,
		'echo' => false,
	), $comments );

	$results['comments'] = $comment_list;

	$results['navigation'] = navigation( $page, $comments );

	$results['page'] = $page;

	echo wp_json_encode( $results );

	exit();
}

/**
 * Enqueues the scripts and styles used by the comments template.
 */
function enqueue_scripts() {
	$post = get_post();

	if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'comments_template' ) ) {
		wp_enqueue_script( 'wsu-25-by-2030-comments', get_stylesheet_directory_uri() . '/js/comments.js', array( 'jquery' ), d25_theme_version(), true );
		wp_localize_script( 'wsu-25-by-2030-comments', 'comments', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'comments-paging' ),
		) );
	}
}

/**
 * Displays the comments template.
 *
 * @param array  $atts
 * @param string $content
 *
 * @return string
 */
function display_template( $atts, $content = '' ) {
	if ( is_singular() && post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		ob_start();

		?>
		<div class="comment-processing"></div>
		<div class="comment-success">
			<?php
			if ( $content ) {
				echo wp_kses_post( wpautop( $content ) );
			}
			?>
		</div>
		<?php

		comments_template();

		return ob_get_clean();
	}

	return '';
}
