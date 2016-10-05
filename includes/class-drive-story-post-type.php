<?php

class Drive_Story_Post_Type {
	/**
	 * @since 0.1.0
	 *
	 * @var Drive_Story_Post_Type
	 */
	private static $instance;

	/**
	 * @since 0.1.0
	 *
	 * @var string Slug for tracking the post type of a story.
	 */
	public $content_type_slug = 'drive_story';

	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @since 0.1.0
	 *
	 * @return \Drive_Story_Post_Type
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Drive_Story_Post_Type();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Adds the hooks used to create and manage the story post type.
	 *
	 * @since 0.1.0
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'register_content_type' ), 12 );
	}

	/**
	 * Register a content type to track information about magazine issues.
	 */
	public function register_content_type() {
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
		);
		register_post_type( $this->content_type_slug, $args );
		register_taxonomy_for_object_type( 'wsuwp_university_category', $this->content_type_slug );

		if ( class_exists( 'MultiPostThumbnails' ) ) {
			$thumbnail_args = array(
				'label' => 'Mobile Image',
				'id' => 'mobile-image',
				'post_type' => $this->content_type_slug,
			);
			new MultiPostThumbnails( $thumbnail_args );
		}
	}

	/**
	 * Check to see if a custom post thumbnail has been added to a post.
	 *
	 * @param string $type Type of added thumbnail to check for.
	 *
	 * @return bool True if thumbnail exists. False if not.
	 */
	public function has_post_thumbnail( $type ) {
		if ( class_exists( 'MultiPostThumbnails' ) ) {
			return MultiPostThumbnails::has_post_thumbnail( 'drive_story', $type );
		}

		return false;
	}

	/**
	 * Retrieve the source of an image added through multiple post thumbnails.
	 *
	 * @param string $type Type of thumbnail being requested.
	 * @param string $size Size of thumbnail being requested.
	 *
	 * @return bool|string URL of the image if available. False if not.
	 */
	public function get_thumbnail_image_src( $type, $size = null ) {
		if ( class_exists( 'MultiPostThumbnails' ) ) {
			return MultiPostThumbnails::get_post_thumbnail_url( 'drive_story', $type, get_the_ID(), $size );
		}

		return false;
	}
}

add_action( 'after_setup_theme', 'Drive_Story_Post_Type', 11 );
/**
 * Starts things up.
 *
 * @since 0.1.0
 *
 * @return \Drive_Story_Post_Type
 */
function Drive_Story_Post_Type() {
	return Drive_Story_Post_Type::get_instance();
}

/**
 * Wrapper to determine if the displayed story has a mobile image assigned.
 *
 * @return bool True if background exists. False if not.
 */
function drive_story_has_mobile_image() {
	return Drive_Story_Post_Type::get_instance()->has_post_thumbnail( 'mobile-image' );
}

/**
 * Retrieve the source URL for a mobile image attached to a post.
 *
 * @param string $size Size of the thumbnail to retrieve.
 *
 * @return bool|string URL of the image if available. False if not.
 */
function drive_story_get_mobile_image_src( $size = 'full' ) {
	return Drive_Story_Post_Type::get_instance()->get_thumbnail_image_src( 'mobile-image', $size );
}
