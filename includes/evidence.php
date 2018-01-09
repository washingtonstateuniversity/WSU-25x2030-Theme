<?php

namespace WSU\D25\Evidence;

function post_type_slug() {
	return 'drive_story';
}

add_action( 'init', 'WSU\D25\Evidence\register_post_type', 12 );
add_action( 'wp_enqueue_scripts','WSU\D25\Evidence\enqueue_scripts' );
add_action( 'init', 'WSU\D25\Evidence\rewrite_rules' );
add_filter( 'query_vars', 'WSU\D25\Evidence\query_vars' );
add_filter( 'spine_get_title', 'WSU\D25\Evidence\page_title' );
add_filter( 'rest_prepare_' . post_type_slug(), 'WSU\D25\Evidence\include_mobile_image', 10, 2 );

/**
 * Registers the Evidence story post type.
 */
function register_post_type() {
	$labels = array(
		'name' => 'Story',
		'singular_name' => 'Story',
		'all_items' => 'All Stories',
		'view_item' => 'View Story',
		'add_new_item' => 'Add New Story',
		'add_new' => 'Add New',
		'edit_item' => 'Edit Story',
		'update_item' => 'Update Story',
		'search_items' => 'Search Stories',
		'not_found' => 'No stories found',
		'not_found_in_trash' => 'No stories found in Trash',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Stories for The Evidence page.',
		'public' => false,
		'show_ui' => true,
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-book-alt',
		'supports' => array(
			'title',
			'editor',
			'revisions',
			'thumbnail',
		),
		'has_archive' => false,
		'show_in_rest' => true,
	);

	\register_post_type( post_type_slug(), $args );

	register_taxonomy_for_object_type( 'wsuwp_university_category', post_type_slug() );

	if ( class_exists( 'MultiPostThumbnails' ) ) {
		$thumbnail_args = array(
			'label' => 'Mobile Image',
			'id' => 'mobile-image',
			'post_type' => post_type_slug(),
		);

		new \MultiPostThumbnails( $thumbnail_args );
	}
}

/**
 * Enqueues the scripts and styles used on the Evidence page.
 */
function enqueue_scripts() {
	if ( is_page_template( 'template-evidence.php' ) ) {
		wp_enqueue_script( 'd25-evidence', get_stylesheet_directory_uri() . '/js/evidence.js', array( 'jquery' ), d25_theme_version(), true );
		wp_localize_script( 'd25-evidence', 'evidence', array(
			'default_title' => get_the_title() . ' | ' . get_bloginfo( 'name' ) . ' | Washington State University',
			'default_url' => get_permalink(),
			'request_url_base' => esc_url( rest_url( '/wp/v2/drive_story?_embed' ) ),
		) );
	}
}

/**
 * Adds a rewrite rule for handling views of University Categories on The Evidence page.
 */
function rewrite_rules() {
	add_rewrite_rule(
		'^the-evidence/category/([^/]*)/?',
		'index.php?pagename=the-evidence&category=$matches[1]',
		'top'
	);
}

/**
 * Filters the query vars that the rewrite rule expects to be available.
 *
 * @param array $query_vars Current list of query_vars passed.
 *
 * @return array
 */
function query_vars( $query_vars ) {
	$query_vars[] = 'category';

	return $query_vars;
}

/**
 * Builds appropriate titles for the Evidence page.
 *
 * @param string $title Original title.
 *
 * @return string
 */
function page_title( $title ) {
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

/**
 * Embeds the mobile image link in the REST API response containing Evidence story data.
 *
 * @param WP_REST_Response $response The current REST response object.
 * @param WP_Post          $post     The current WP_Post object.
 *
 * @return WP_REST_Response
 */
function include_mobile_image( $response, $post ) {
	$mobile_image_id = esc_html( get_post_meta( $post->ID, 'drive_story_mobile-image_thumbnail_id', true ) );

	$response->add_link( 'https://api.w.org/mobilemedia', esc_url( rest_url( '/wp/v2/media/' . $mobile_image_id ) ), array(
		'embeddable' => true,
	) );

	return $response;
}

/**
 * Checks if a mobile image has been added to a post.
 *
 * @return bool
 */
function has_mobile_image() {
	if ( class_exists( 'MultiPostThumbnails' ) ) {
		return \MultiPostThumbnails::has_post_thumbnail( 'drive_story', 'mobile-image' );
	}

	return false;
}

/**
 * Retrieves the source of the posts mobile image.
 *
 * @return bool|string
 */
function get_mobile_image_src() {
	if ( class_exists( 'MultiPostThumbnails' ) ) {
		return \MultiPostThumbnails::get_post_thumbnail_url( 'drive_story', 'mobile-image', get_the_ID(), 'full' );
	}

	return false;
}


