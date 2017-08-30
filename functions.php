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
		include_once( __DIR__ . '/includes/class-drive-image-shortcake.php' );
		include_once( __DIR__ . '/includes/class-drive-story-post-type.php' );

		new Drive_Image_Shortcake();
	}

	/**
	 * Setup hooks to include.
	 */
	public function setup_hooks() {
		add_filter( 'spine_child_theme_version', array( $this, 'theme_version' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'make_the_builder_content', array( $this, 'replace_p_with_figure' ), 99 );
		add_action( 'wsu_register_inline_svg', array( $this, 'spirit_mark' ) );
		add_action( 'wsu_register_inline_svg', array( $this, 'shield_mark' ) );
		add_filter( 'comment_form_fields', array( $this, 'comment_form_fields' ) );
		add_filter( 'notify_moderator', '__return_false' );
		add_shortcode( 'drive_section', array( $this, 'display_drive_section' ) );
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
				//'ajax_url' => admin_url( 'admin-ajax.php' ),
				//'nonce' => wp_create_nonce( 'fetch_articles' ),
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

	public function shield_mark() {
		ob_start();
		?>
		<svg id="shield-mark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 82.7 101.6">
			<path fill="#9c9c9c" d="M55.9,85.6L41.3,96L26.7,85.6C16.3,78.5,7.4,72.4,2.7,63.2C6.6,80.5,23.5,87,41.3,100.9
		C59,87,76,80.5,79.8,63.2C75.2,72.4,66.3,78.5,55.9,85.6z"/>
			<g>
				<path fill="#9c9c9c" d="M55.8,22.4c-2.2-1.2-7.5-0.8-9.3,0.5c-3,2-2.5,5.6-2.4,5.7c0.6-1,1.6-2.5,3.1-3.5c1.9-1.3,5.1-2.3,8.1-2.1
			C56,23,56.6,22.8,55.8,22.4z"/>
				<path fill="#9c9c9c" d="M0.7,0.9v1.8c1.1,0.4,1.5,0.7,1.6,2.4V47c0,23.3,19,29.4,38.9,45.1c20-15.7,39-21.8,39-45.1V4.6
			c0.1-1.3,0.6-1.6,1.6-1.9V0.9H0.7z M15.2,66.5c4-6.1,4-12.7,4-12.7c-3.3,1.1-9.1,2.1-9.1,2.1s5.4-4.1,9.5-16.7l3.9,3.5l-0.7,2.4
			c0,0,1.8,2.6,2.3,4.4c0,0,2.6-4.9-0.2-10.2l-0.5,1.5l-1.5-1.4l-2.6-2.4c0,0,1.2-4.1,4.9-9.2l0.3,0.4l3,3.5l-0.9,1.5
			c0,0,2.1,2.2,3.8,4.8c0,0,0.9-4.4-0.4-9.2l-1.4,1.3l-3.3-3.9c4.7-4.7,9.3-7.6,14.8-9.4c-0.3,0.4-0.7,0.8-0.9,1.2
			c-1.9,2.6-3.7,7.4-2.2,15c0.2,1.2,0.6,2.9,1,4.7c0.8,3.5,1.7,7.4,2,9.9c0.7,5.2,0.1,8.6-1.6,10.4c-1.2,1.3-3.2,1.8-5.9,1.7
			c0-0.4,0-0.8,0-1.2c0-3.7-0.7-6.4-0.8-6.5L32,49.4l-1.1,2.3C29.1,55.5,23.1,64.7,15.2,66.5C15.2,66.5,15.2,66.5,15.2,66.5z
			 M55.3,68.9c-1.4,0.3-6.2,0.1-6.2,0.1s3.2-1.4,5-6.7C55.7,65.6,55.3,68.9,55.3,68.9z M62.7,58.3c-9.5,1.3-11.1-18.5-11.1-18.5
			s3.2,10.2,9.9,9.8c7-0.4,5-11.1,5-11.1S73.4,56.8,62.7,58.3z M74.2,20.9l-9.3,0.4c0,1.1-0.1,2.2-0.5,3.3c0.9,3.8,0.3,5.9,0.3,5.9
			c-1.6-3.6-2.8-4.6-2.8-4.6C53.3,22,48.4,30.6,49,36.2c0.4,4.6,2.1,10.6,3.4,20.1c1.1,8-3,11.5-7.6,12.3c-0.1,0-0.2,0-0.3,0.1
			c0,0,0,0-0.1,0c-0.7,0.1-1.6,0.1-2.4,0.1h-0.1c-0.1,0-0.2,0-0.3,0c-0.4,0-0.7-0.1-1.1-0.1c-3.6-0.5-11.7-2-20.1-2.2
			c4.9-2.9,8.8-7.9,10.9-11.4c0.1,1,0.2,2.2,0.2,3.5c0,0.7,0,1.3-0.1,2.1l-0.1,1l1,0.1c3.9,0.3,6.6-0.4,8.4-2.2
			c2.2-2.3,2.9-6.1,2.1-12c-0.3-2.6-1.3-6.6-2.1-10.1c-0.4-1.8-0.8-3.4-1-4.6c-1.4-7,0.3-11.2,1.9-13.5c1.3-1.8,3.1-3.1,5-3.8
			c0,0,0.1,0,0.1,0l3.2-9.8h1L48.7,15c0.5-0.1,1-0.2,1.5-0.2L53,6.1h1l-1.8,8.4c2.8-0.4,5.9-0.6,9.4-0.9c0.6,0.2,1.1,0.9,1.7,1.8
			l9-2.8l0.3,1L64,16.9c0.1,0.2,0.1,0.4,0.2,0.5l9.4-1.2l0.2,1l-9.1,1.8c0,0.2,0.1,0.3,0.1,0.5l9.5,0.3L74.2,20.9z"/>
			</g>
		</svg>
		<?php
		$shield_mark = ob_get_contents();

		ob_end_clean();

		wsu_register_inline_svg( 'shield-mark', $shield_mark );
	}
	/**
	 * Register the spirit mark SVG data for the WSU Inline SVG plugin.
	 */
	public function spirit_mark() {
		ob_start();
		?>
			<svg id="spirit-mark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 179.1 171.4">
				<path fill="#919191" d="M107.4,166.9c0,0,8.5-3.6,13.2-17.7c4.1,8.6,3,17.5,3,17.5C119.9,167.4,107.4,166.9,107.4,166.9z"/>
				<path fill="#919191" d="M153.2,86.2c0,0,5.2,28.3-13.2,29.3c-17.7,0.9-26.1-25.8-26.1-25.8s4.3,52.3,29.1,48.8	C171,134.7,153.2,86.2,153.2,86.2z"/>
				<path fill="#919191" d="M18.1,160.1C18.1,160.1,18.2,160.1,18.1,160.1c20.8-4.7,36.6-29,41.3-38.7l3-6.2l1.8,6.6
		c0.1,0.4,2,7.6,2,17.3c0,1.1,0,2.2-0.1,3.3c7.1,0.3,12.2-1.2,15.4-4.5c4.5-4.7,5.9-13.7,4.2-27.4c-0.8-6.6-3.2-17-5.4-26.2
		c-1.1-4.7-2.1-9.1-2.7-12.3c-4-20.1,1-32.6,5.8-39.5c0.8-1.1,1.6-2.1,2.5-3.1c-14.3,4.8-26.3,12.4-38.8,24.8l8.7,10.2l3.6-3.3
		c3.4,12.6,1,24.2,1,24.2c-4.6-7-10-12.7-10-12.7l2.4-4l-7.9-9.2l-0.9-1c-9.7,13.3-12.8,24.2-12.8,24.2l6.9,6.3l4,3.6l1.3-3.9
		c7.4,13.8,0.7,26.8,0.7,26.8c-1.2-4.7-6.1-11.7-6.1-11.7l1.9-6.3l-10.2-9.3c-10.6,33.3-24.8,44.1-24.8,44.1s15.4-2.8,23.9-5.7
		C28.8,126.6,28.5,144.2,18.1,160.1z"/>
				<path fill="#919191" d="M173.1,40.1l-24.5,1c0,2.9-0.4,5.8-1.3,8.6c2.3,10,0.7,15.5,0.7,15.5c-4.1-9.6-7.3-12.2-7.3-12.2
		c-22.5-10.1-35.2,12.6-33.8,27.3c1.1,12,5.5,28,8.9,53c2.9,21.1-7.8,30.2-20,32.3c-0.3,0-0.5,0.1-0.8,0.1c-0.1,0-0.1,0-0.2,0
		c-2,0.3-4.1,0.3-6.3,0.3c-0.1,0-0.2,0-0.2,0c-0.2,0-0.5,0-0.7,0c-1-0.1-1.9-0.2-2.8-0.3c-9.5-1.3-30.8-5.2-52.9-5.7
		c13-7.6,23.1-20.7,28.7-30c0.3,2.6,0.6,5.7,0.6,9.2c0,1.7-0.1,3.5-0.2,5.4l-0.2,2.5l2.5,0.2c10.1,0.9,17.3-1.1,22-5.9
		c5.8-6,7.5-16,5.6-31.5c-0.9-6.8-3.3-17.4-5.5-26.7c-1.1-4.7-2.1-9.1-2.7-12.1c-3.6-18.3,0.7-29.4,5-35.5c3.4-4.8,8-8.3,13.1-10
		c0.1,0,0.2,0,0.3,0l8.4-23.7h2.6l-5.6,22.7c1.3-0.2,2.6-0.4,4-0.6l7.2-20.8h2.6l-4.7,20.1c7.5-1,15.6-1.7,24.6-2.4
		c1.5,0.6,3,2.3,4.4,4.6l23.6-7.3l0.9,2.5l-22.5,8.8c0.2,0.5,0.3,1,0.5,1.4l24.7-3.1l0.4,2.7L148,35.3c0.1,0.5,0.2,0.9,0.2,1.3
		l24.9,0.8L173.1,40.1z M102.1,51.2c4.9-3.5,13.4-6.2,21.2-5.5c2,0.2,3.7-0.4,1.5-1.5c-5.9-3.1-19.7-2.1-24.5,1.3
		c-7.8,5.3-6.5,14.8-6.3,15.1C95.6,57.7,98.2,53.9,102.1,51.2z"/>
				</g>
			</svg>
		<?php
		$spirit_mark = ob_get_contents();

		ob_end_clean();

		wsu_register_inline_svg( 'spirit-mark', $spirit_mark );
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
	 * Display a sequence of pages passed via shortcode as a comma separated
	 * string of page IDs.
	 *
	 * @param array $atts
	 *
	 * @return mixed|string|void
	 */
	public function display_drive_section( $atts ) {
		if ( ! isset( $atts['ids'] ) ) {
			return '';
		}

		$ids = explode( ',', $atts['ids'] );
		$ids = array_map( 'trim', $ids );
		$ids = array_map( 'absint', $ids );
		$content = '';

		foreach ( $ids as $id ) {
			if ( 0 === $id ) {
				continue;
			}

			$post = get_post( $id );

			$content .= $post->post_content;
		}

		$content = apply_filters( 'the_content', $content );

		return $content;
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
