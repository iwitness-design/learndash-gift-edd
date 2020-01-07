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
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package learndash-gift-edd/admin
 * @author    WisdmLabs <wisdmlabs@info.com>
 */
class LearndashEddGiftAdmin {


	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_scrn_hook_suf = null;

	protected static $plugin_s_instance = null;

	protected static $plugin_setting_name = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
		$this->init_classes();
	}

	/**
	 * Includes public and admin files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		/*--------------------------------------------------------------------*
		 * Add settings module files
		 *---------------------------------------------------------------------*/

		include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'admin/class-learndasheddgiftsettings.php' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_admin_settings' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . LEARNDASH_EDD_GIFT_PLUGIN_SLUG . '.php' );

		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Initialize public and admin functionalities.
	 *
	 * @since 1.0.0
	 */
	private function init_classes() {
		if ( class_exists( 'LearndashEddGiftSettings' ) ) {
			self::$plugin_s_instance = LearndashEddGiftSettings::get_instance();
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_scrn_hook_suf ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_scrn_hook_suf == $screen->id ) {
			//
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_scrn_hook_suf ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_scrn_hook_suf == $screen->id ) {
			//
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		self::$plugin_setting_name = __( 'Learndash Gift Courses', 'learndash-gift-edd' );
		$this->plugin_scrn_hook_suf = add_options_page(
			self::$plugin_setting_name,
			self::$plugin_setting_name,
			'manage_options',
			LEARNDASH_EDD_GIFT_PLUGIN_SLUG,
			array( self::$plugin_s_instance, 'display_admin_page' )
		);
	}

	/**
	 * Register the plugin settings.
	 *
	 * @since    1.0.0
	 */

	public function register_admin_settings() {
		register_setting( 'learndash_edd_gift_options', 'learndash_edd_gift_data' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . LEARNDASH_EDD_GIFT_PLUGIN_SLUG ) . '">' . __( 'Settings', 'learndash-gift-edd' ) . '</a>',
			),
			$links
		);
	}
}
