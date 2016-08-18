<?php

class WSU_25_by_2030_Theme {
	/**
	 * @var WSU_25_by_2030_Theme
	 */
	private static $instance;
	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @return \WSU_25_by_2030_Theme
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSU_25_by_2030_Theme;
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 */
	public function setup_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the scripts used in the theme.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wsu-25-by-2030', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ) );
	}

}

add_action( 'after_setup_theme', 'WSU_25_by_2030_Theme' );
/**
 * Start things up.
 *
 * @return \WSU_25_by_2030_Theme
 */
function WSU_25_by_2030_Theme() {
	return WSU_25_by_2030_Theme::get_instance();
}
