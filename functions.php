<?php

class WSU_25_By_2030_Theme {

	/**
	 * @var string String used for busting cache on scripts.
	 */
	var $script_version = '0.3.0';

	/**
	 * @var int Comments to display per page.
	 */
	var $comments_per_page = 5;

	/**
	 * @var WSU_25_By_2030_Theme
	 */
	private static $instance;
	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @return \WSU_25_By_2030_Theme
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSU_25_By_2030_Theme();
			self::$instance->include_extensions();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Additional files to include as the theme boots.
	 */
	public function include_extensions() {
		include_once( __DIR__ . '/includes/class-drive-story-post-type.php' );
	}

	/**
	 * Setup hooks to include.
	 */
	public function setup_hooks() {
		add_filter( 'spine_child_theme_version', array( $this, 'theme_version' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'make_the_builder_content', array( $this, 'replace_p_with_figure' ), 99 );

		add_filter( 'comment_form_fields', array( $this, 'comment_form_fields' ) );
		add_filter( 'notify_moderator', '__return_false' );

		add_shortcode( 'comments_template', array( $this, 'display_comments_template' ) );
		add_action( 'init', array( $this, 'apply_comment_filter' ) );
		add_action( 'wp_ajax_nopriv_comment_navigation', array( $this, 'ajax_comments' ) );
		add_action( 'wp_ajax_comment_navigation', array( $this, 'ajax_comments' ) );
		add_action( 'init', array( $this, 'evidence_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'evidence_query_vars' ) );
		add_filter( 'spine_get_title', array( $this, 'evidence_title' ) );

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		add_filter( 'wp_resource_hints', array( $this, 'remove_s_w_org_dns_prefetch' ), 10, 2 );
	}

	/**
	 * Provide a theme version for use in cache busting.
	 *
	 * @since 0.0.16
	 *
	 * @return string
	 */
	public function theme_version() {
		return $this->script_version;
	}

	/**
	 * Enqueue the scripts used in the theme.
	 */
	public function enqueue_scripts() {
		$post = get_post();

		wp_enqueue_script( 'wsu-25-by-2030-typekit', 'https://use.typekit.net/roi0hte.js', array(), false );
		wp_add_inline_script( 'wsu-25-by-2030-typekit', 'try{Typekit.load();}catch(e){};' );

		if ( is_front_page() ) {
			wp_enqueue_script( 'wsu-25-by-2030-home', get_stylesheet_directory_uri() . '/js/home.js', array( 'jquery' ), $this->script_version, true );
		}

		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'comments_template' ) ) {
			wp_enqueue_script( 'wsu-25-by-2030-comments', get_stylesheet_directory_uri() . '/js/comments.js', array( 'jquery' ), $this->script_version, true );
			wp_localize_script( 'wsu-25-by-2030-comments', 'comments', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'comments-paging' ),
			) );
		}

		if ( is_page_template( 'template-evidence.php' ) ) {
			wp_enqueue_script( 'wsu-25-by-2030-evidence', get_stylesheet_directory_uri() . '/js/evidence.js', array( 'jquery' ), $this->script_version, true );
			wp_localize_script( 'wsu-25-by-2030-evidence', 'evidence', array(
				'default_title' => get_the_title() . ' | ' . get_bloginfo( 'name' ) . ' | Washington State University',
				'default_url' => get_permalink(),
				'request_url_base' => esc_url( rest_url( '/wp/v2/drive_story?_embed' ) ),
			) );
		}
	}

	/**
	 * Replace paragraphs wrapped around lone images with figure.
	 *
	 * @param string $content Original content being stored.
	 *
	 * @return string Modified content.
	 */
	public function replace_p_with_figure( $content ) {
		$content = preg_replace( '/<p[^>]*>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\/p>/', '<figure class=\"wsu-p-replaced\">$1</figure>', $content );
		return $content;
	}

	/**
	 * Remove the URL field from the comment form.
	 * Reposition the comment text area below the name and email fields.
	 *
	 * @param array $fields The default comment form fields.
	 * @return array Modified comment form fields.
	 */
	public function comment_form_fields( $fields ) {
		$comment_field = $fields['comment'];

		unset( $fields['url'] );
		unset( $fields['comment'] );

		$fields['comment'] = $comment_field;

		return $fields;
	}

	/**
	 * A shortcode for displaying the comments template.
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function display_comments_template( $atts, $content = '' ) {
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

	/**
	 * Apply our comment text filter if we're not in the admin.
	 */
	public function apply_comment_filter() {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		add_filter( 'comment_text', array( $this, 'comment_text' ), 10, 2 );
	}

	/**
	 * If a comment exceeds 250 bytes, wrap the remainder in a span and
	 * add a link that can be clicked to toggle visibility of the remainder.
	 *
	 * @param string     $comment_text
	 * @param WP_Comment $comment
	 *
	 * @return string Filtered comment text.
	 */
	function comment_text( $comment_text, $comment = null ) {
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
	 * Removes the s.w.org DNS prefetch.
	 *
	 * Code in this method is originally from GPL licensed https://wordpress.org/plugins/disable-emojis/
	 *
	 * @since 0.0.18
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array                 Difference between the two arrays.
	 */
	public function remove_s_w_org_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}

	/**
	 * Retrieve the navigation markup for the current page of comments.
	 *
	 * @since 0.0.19
	 *
	 * @param int   $page     The page of comments to retrieve.
	 * @param array $comments Array of comment objects.
	 *
	 * @return string Navigation markup.
	 */
	public function comment_navigation( $page, $comments ) {
		$navigation = '';

		$comment_pages_count = get_comment_pages_count( $comments, $this->comments_per_page );

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
	public function ajax_comments() {
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
			'per_page' => $this->comments_per_page,
			'avatar_size' => 0,
			'format' => 'html5',
			'reverse_top_level' => false,
			'echo' => false,
		), $comments );

		$results['comments'] = $comment_list;

		$results['navigation'] = $this->comment_navigation( $page, $comments );

		$results['page'] = $page;

		echo wp_json_encode( $results );

		exit();
	}

	/**
	 * Add a rewrite rule for handling views of University Categories on The Evidence page.
	 */
	public function evidence_rewrite_rules() {
		add_rewrite_rule(
			'^the-evidence/category/([^/]*)/?',
			'index.php?pagename=the-evidence&category=$matches[1]',
			'top'
		);
	}

	/**
	 * Make WordPress aware of the category query variable in our rewrite rule.
	 *
	 * @param array $query_vars Current list of query_vars passed.
	 *
	 * @return array Modified list of query_vars.
	 */
	function evidence_query_vars( $query_vars ) {
		$query_vars[] = 'category';
		return $query_vars;
	}

	/**
	 * Build appropriate titles for The Evidence page.
	 *
	 * @param string $title Original title.
	 *
	 * @return string Modified title.
	 */
	function evidence_title( $title ) {
		$category = get_query_var( 'category' );

		if ( ! $category ) {
			return $title;
		}

		$categories = array_values( get_terms( array(
			'taxonomy' => 'wsuwp_university_category',
			'hierarchical' => false,
			'fields' => 'id=>slug',
		) ) );
		$category = ( $category && in_array( $category, $categories, true ) ) ? $category : false;
		$heading = ( $category ) ? get_term_by( 'slug', $category, 'wsuwp_university_category' )->name : '';
		$heading = explode( ', Academic', $heading );

		return $heading[0] . ' | ' . $title;
	}
}

add_action( 'after_setup_theme', 'WSU_25_By_2030_Theme' );
/**
 * Start things up.
 *
 * @return \WSU_25_By_2030_Theme
 */
function WSU_25_By_2030_Theme() {
	return WSU_25_By_2030_Theme::get_instance();
}
