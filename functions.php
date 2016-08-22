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
		add_filter( 'make_the_builder_content', array( $this, 'replace_p_with_figure' ), 99 );
	}

	/**
	 * Enqueue the scripts used in the theme.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wsu-25-by-2030', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery', 'wsu-25-by-2030-doormat' ), false, true );
		wp_enqueue_script( 'wsu-25-by-2030-typekit', 'https://use.typekit.net/roi0hte.js', array(), false );
		wp_add_inline_script( 'wsu-25-by-2030-typekit', 'try{Typekit.load();}catch(e){};' );
		wp_enqueue_script( 'wsu-25-by-2030-doormat', get_stylesheet_directory_uri() . '/js/doormat.js', array( 'jquery' ), false, true );
		wp_add_inline_script( 'wsu-25-by-2030-doormat', 'var drive_doormat = new Doormat({ debounce: 150, snapping: { travel: false, viewport: true, threshold: 30, duration: ".25s" } });' );
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
