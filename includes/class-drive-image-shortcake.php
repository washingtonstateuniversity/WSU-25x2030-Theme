<?php

class Drive_Image_Shortcake {
	public function __construct() {
		add_filter( 'img_shortcode_ui_args', array( $this, 'add_mobile_image_args' ) );
		add_filter( 'img_shortcode_output_img_tag', array( $this, 'img_tag_output' ), 10, 2 );
	}

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
				'label'       => esc_html__( 'Alt', 'image-shortcake' ),
				'attr'        => 'alt',
				'type'        => 'text',
				'encode'      => true,
				'placeholder' => esc_attr__( 'Alt text for the image', 'image-shortcake' ),
			),
		);

		return $args;
	}

	public function img_tag_output( $image_html, $attr ) {
		$image_html = '';
		$mobile_image_html = '';

		if ( isset( $attr['attachment'] ) && $attachment = wp_get_attachment_image_src( (int) $attr['attachment'], $attr['size'] ) ) {
			$image_html = '<figure class="standard-image"><img ';
			$image_classes = explode( ' ', $attr['classes'] );
			$image_classes[] = 'size-' . $attr['size'];
			$image_classes[] = $attr['align'];

			$image_attr = array(
				'alt' => $attr['alt'],
				'class' => trim( implode( ' ', $image_classes ) ),
			);

			$image_attr['src'] = esc_url( $attachment[0] );
			$image_attr['width'] = intval( $attachment[1] );
			$image_attr['height'] = intval( $attachment[2] );

			foreach ( $image_attr as $attr_name => $attr_value ) {
				if ( ! empty( $attr_value ) ) {
					$image_html .= sanitize_key( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
				}
			}
			$image_html .= ' /></figure>';
		}

		if ( isset( $attr['attachment-mobile'] ) && $attachment = wp_get_attachment_image_src( (int) $attr['attachment-mobile'], $attr['size'] ) ) {
			$mobile_image_html = '<figure class="mobile-image"><img ';
			$image_classes = explode( ' ', $attr['classes'] );
			$image_classes[] = 'size-' . $attr['size'];
			$image_classes[] = $attr['align'];

			$image_attr = array(
				'alt' => $attr['alt'],
				'class' => trim( implode( ' ', $image_classes ) ),
			);

			$image_attr['src'] = esc_url( $attachment[0] );
			$image_attr['width'] = intval( $attachment[1] );
			$image_attr['height'] = intval( $attachment[2] );

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
