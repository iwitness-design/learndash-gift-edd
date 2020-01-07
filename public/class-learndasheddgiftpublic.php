<?php

/**
 * Gift learndash Courses.
 *
 * @package   learndash-gift-edd
 * @author    WisdmLabs <wisdmlabs@info.com>
 * @license   GPL-2.0+
 * @link      https://wisdmlabs.com
 * @copyright 2016 WisdmLabs or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * @package learndash-gift-edd/public
 * @author    WisdmLabs <wisdmlabs@info.com>
 */

class LearndashEddGiftPublic {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Instance of "EddCourseGiftingCheckoutMod" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $ecgc_module_instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$this->includes();
		$this->check_dependencies();
		$this->init_hooks();
		$this->init_classes();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check dependencies.
	 *
	 * @since     1.0.0
	 *
	 */

	public function check_dependencies() {
		//
	}

	/**
	 * Includes public and admin files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		/*--------------------------------------------------------------------*
		 * Add public gifting module files
		 *---------------------------------------------------------------------*/

		include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'public/class-eddcoursegiftingcheckoutmod.php' );

	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Initialize public functionalities.
	 *
	 * @since 1.0.0
	 */
	private function init_classes() {
		if ( class_exists( 'EddCourseGiftingCheckoutMod' ) ) {
			self::$ecgc_module_instance = EddCourseGiftingCheckoutMod::get_instance();
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		if ( edd_is_checkout() ) {
			wp_enqueue_style( LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME . '-jquery-ui-styles', LEARNDASH_EDD_GIFT_PLUGIN_URL . 'public/assets/css/jquery-ui.css', array(), LEARNDASH_EDD_GIFT_PLUGIN_VERSION );
			wp_enqueue_style( LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME . '-styles', LEARNDASH_EDD_GIFT_PLUGIN_URL . 'public/assets/css/public.css', array(), LEARNDASH_EDD_GIFT_PLUGIN_VERSION );
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( edd_is_checkout() ) {
			wp_enqueue_script( LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME . '-jquery-ui-script', LEARNDASH_EDD_GIFT_PLUGIN_URL . 'public/assets/js/jquery-ui.js', array( 'jquery' ), LEARNDASH_EDD_GIFT_PLUGIN_VERSION );
			wp_enqueue_script( LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME . '-script', LEARNDASH_EDD_GIFT_PLUGIN_URL . 'public/assets/js/public.js', array( 'jquery' ), LEARNDASH_EDD_GIFT_PLUGIN_VERSION );
		}
	}
}
