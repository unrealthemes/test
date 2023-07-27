<?php

/**
 * Get instance of helper
 */
function ut_help() {
  	return UT_Theme_Helper::get_instance();
}

/**
 * Main class of all tehe,e settings
 */
class UT_Theme_Helper {

  	private static $_instance = null;

  	public $event;

  	public $event_filter;

  	private function __construct() {

  	}

  	protected function __clone() {

  	}

  	static public function get_instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
  	}

	/**
	 * Load files, plugins, add hooks and filters and do all magic
	 */
	function init() {

		// load needed files
		$this->import();
		$this->load_classes();
		$this->register_hooks();

		return $this;
	}

	function load_classes() {

		$this->event = UT_Event::get_instance();
		$this->event_filter = UT_Event_Filter::get_instance();
	}

	/**
	 * Register all needed hooks
	 */
	public function register_hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts_n_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts_n_styles' ] );
		add_action( 'after_setup_theme',  [ $this, 'register_menus' ] );
		add_action( 'after_setup_theme',  [ $this, 'add_theme_support' ] );
	}

	function register_menus() {

		register_nav_menus( [
			'menu_1' => esc_html__( 'Header', 'unreal-theme' ),
			'menu_2' => esc_html__( 'Footer', 'unreal-theme' ),
		] );
	}

	public function add_theme_support() {

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		] );
	}

	function load_scripts_n_styles() {
		// ========================================= CSS ========================================= //
		wp_enqueue_style( 'ut-style', get_stylesheet_uri() );
		wp_enqueue_style( 'ut-style.min', THEME_URI . '/css/style.min.css' );
		// ========================================= JS ========================================= //
		//////////////////////////////////////
		wp_deregister_script('jquery-core');
		wp_register_script('jquery-core', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js', false, null, true);
		wp_deregister_script('jquery');
		wp_register_script('jquery', false, ['jquery-core'], null, true);
		//////////////////////////////////////
		wp_enqueue_script( 'ut-scripts', THEME_URI . '/js/scripts.js', ['jquery'], date("Ymd"), true );
		wp_localize_script( 
			'ut-scripts', 
			'ut_params', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ut_check' ),
			] 
		);
	}

	function load_admin_scripts_n_styles() {
		// ========================================= CSS ========================================= //
		wp_enqueue_style( 'ut-admin', THEME_URI . '/admin.css' );
		// ========================================= JS ========================================= //
	}

	public function import() {

		include_once 'helpers.php';
		include_once 'class.event.php';
		include_once 'class.event-filter.php';
	}

}
