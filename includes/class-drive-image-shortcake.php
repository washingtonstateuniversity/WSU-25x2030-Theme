<?php

class Drive_Image_Shortcake {

	/**
	 * Setup the hooks used with image shortcake.
	 */
	public function __construct() {
		add_filter( 'img_shortcode_ui_args', array( $this, 'add_mobile_image_args' ) );
		add_filter( 'img_shortcode_output_img_tag', array( $this, 'img_tag_output' ), 10, 2 );
	}

	/**
	 * Adjust the arguments passed to Shortcake UI when presenting the screen
	 * for shortcode creation.
	 *
	 * @param array $args An array of default arguments from the Image Shortcake plugin.
	 *
	 * @return array The modified list of arguments.
	 */
	public function add_mobile_image_args( $args ) {
		$args['attrs'] = array(

			array(
				'label' => esc_html__( 'Choose Attachment', 'image-shortcake' ),
				'attr'  => 'attachment',
				'type'  => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_attr__( 'Select Image', 'image-shortcake' ),
				'frameTitle'  => esc_attr__( 'Select Image', 'image-shortcake' ),
			),

			array(
				'label' => esc_html__( 'Choose Mobile Image', 'image-shortcake' ),
				'attr'  => 'attachment-mobile',
				'type'  => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_attr__( 'Select Image', 'image-shortcake' ),
				'frameTitle'  => esc_attr__( 'Select Image', 'image-shortcake' ),
			),

			array(
				'label'       => esc_html__( 'Class', 'image-shortcake' ),
				'attr'        => 'classes',
				'type'        => 'text',
				'encode'      => true,
				'placeholder' => esc_attr__( 'Space delimited class names to add to the img elements', 'image-shortcake' ),
			),

			array(
				'label'       => esc_html__( 'Alt', 'image-shortcake' ),
				'attr'        => 'alt',
				'type'        => 'text',
				'encode'      => true,
				'placeholder' => esc_attr__( 'Alt text for the image', 'image-shortcake' ),
			),
		);

		return $args;
	}

	/**
	 * Parse the image shortcode attributes into HTML output.
	 *
	 * @param string $image_html The original HTML output for an image shortcode.
	 * @param array  $attr       Attributes passed to the image shortcode.
	 *
	 * @return string The final HTML output for an image shortcode.
	 */
	public function img_tag_output( $image_html, $attr ) {
		$image_html = '';
		$mobile_image_html = '';
		$attachment = wp_get_attachment_image_src( (int) $attr['attachment'], 'spine-medium_size' );
		$attachment_mobile = wp_get_attachment_image_src( (int) $attr['attachment-mobile'], 'spine-medium_size' );

		if ( isset( $attr['attachment'] ) && $attachment ) {
			$image_html = '<figure class="standard-image"><img ';
			$image_classes = explode( ' ', $attr['classes'] );
			$image_classes[] = 'size-' . $attr['size'];
			$image_classes[] = $attr['align'];

			$image_attr = array(
				'alt' => $attr['alt'],
				'class' => trim( implode( ' ', $image_classes ) ),
			);

			$image_attr['src'] = esc_url( $attachment[0] );
			$image_attr['width'] = '';
			$image_attr['height'] = '';

			foreach ( $image_attr as $attr_name => $attr_value ) {
				if ( ! empty( $attr_value ) ) {
					$image_html .= sanitize_key( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
				}
			}
			$image_html .= ' /></figure>';
		}

		if ( isset( $attr['attachment-mobile'] ) && $attachment_mobile ) {
			$mobile_image_html = '<figure class="mobile-image"><img ';
			$image_classes = explode( ' ', $attr['classes'] );
			$image_classes[] = 'size-' . $attr['size'];
			$image_classes[] = $attr['align'];

			$image_attr = array(
				'alt' => $attr['alt'],
				'class' => trim( implode( ' ', $image_classes ) ),
			);

			$image_attr['src'] = esc_url( $attachment_mobile[0] );
			$image_attr['width'] = '';
			$image_attr['height'] = '';

			foreach ( $image_attr as $attr_name => $attr_value ) {
				if ( ! empty( $attr_value ) ) {
					$mobile_image_html .= sanitize_key( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
				}
			}
			$mobile_image_html .= ' /></figure>';
		}

		return $image_html . "\n" . $mobile_image_html;
	}
}
