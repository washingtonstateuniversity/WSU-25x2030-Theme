<?php

require_once __DIR__ . '/includes/evidence.php';
require_once __DIR__ . '/includes/comments.php';

add_filter( 'spine_child_theme_version', 'd25_theme_version' );
add_action( 'wp_enqueue_scripts','d25_enqueue_scripts' );
add_filter( 'make_the_builder_content', 'd25_replace_p_with_figure', 99 );
add_filter( 'wp_resource_hints', 'd25_remove_s_w_org_dns_prefetch', 10, 2 );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Provides a theme version for use in cache busting.
 *
 * @since 0.0.16
 *
 * @return string
 */
function d25_theme_version() {
	return '0.3.0';
}

/**
 * Enqueues the scripts and styles used in the theme.
 */
function d25_enqueue_scripts() {
	wp_enqueue_script( 'wsu-25-by-2030-typekit', 'https://use.typekit.net/roi0hte.js', array(), false );
	wp_add_inline_script( 'wsu-25-by-2030-typekit', 'try{Typekit.load();}catch(e){};' );

	if ( is_front_page() ) {
		wp_enqueue_script( 'wsu-25-by-2030-home', get_stylesheet_directory_uri() . '/js/home.js', array( 'jquery' ), d25_theme_version(), true );
	}
}

/**
 * Replaces paragraphs wrapped around lone images with figure.
 *
 * @param string $content Original content being stored.
 *
 * @return string Modified content.
 */
function d25_replace_p_with_figure( $content ) {
	$content = preg_replace( '/<p[^>]*>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\/p>/', '<figure class=\"wsu-p-replaced\">$1</figure>', $content );

	return $content;
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
function d25_remove_s_w_org_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}

	return $urls;
}
