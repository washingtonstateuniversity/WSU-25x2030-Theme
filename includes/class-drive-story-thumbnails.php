<?php
/**
 * Configure and create image related functionality in the parent theme.
 *
 * Class Drive_Story_Thumbnails
 */
class Drive_Story_Thumbnails {

	/**
	 * Add hooks.
	 */
	public function __construct() {
		if ( class_exists( 'MultiPostThumbnails' ) ) {
			add_action( 'after_setup_theme', array( $this, 'setup_additional_story_thumbnails' ), 11 );
		}
	}

	/**
	 * Use the Multiple Post Thumbnails plugin to generate additional post
	 * thumbnails for the story post type.
	 */
	public function setup_additional_story_thumbnails() {
		$mobile_args = array(
			'label' => 'Mobile Image',
			'id' => 'mobile-image',
			'post_type' => 'drive_story',
		);

		new MultiPostThumbnails( $mobile_args );
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

$drive_theme_image = new Drive_Story_Thumbnails();

/**
 * Wrapper to determine if the displayed story has a mobile image assigned.
 *
 * @return bool True if background exists. False if not.
 */
function drive_story_has_mobile_image() {
	//global $drive_theme_image;
	$drive_theme_image = new Drive_Story_Thumbnails();
	return $drive_theme_image->has_post_thumbnail( 'mobile-image' );
}

/**
 * Retrieve the source URL for a mobile image attached to a post.
 *
 * @param string $size Size of the thumbnail to retrieve.
 *
 * @return bool|string URL of the image if available. False if not.
 */
function drive_story_get_mobile_image_src( $size = 'full' ) {
	//global $drive_theme_image;
	$drive_theme_image = new Drive_Story_Thumbnails();
	return $drive_theme_image->get_thumbnail_image_src( 'mobile-image', $size );
}
