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
 * Plugin class. This is the routing file.
 *
 * @package learndash-gift-edd/include
 * @author    WisdmLabs <wisdmlabs@info.com>
 */

class LearndashEddGift {





	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	protected static $_instance = null;

	/**
	 * Instance of "LearndashEddGiftPublic" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	public static $public_ld_edd = null;

	/**
	 * Instance of "LearndashEddGiftAdmin" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	public static $admin_ld_edd = null;


	/**
	 * Easy digital download plugin activation status.
	 *
	 * @since    1.0.0
	 *
	 * @var      bollean
	 */
	protected static $edd_active_status = false;

	/**
	 * Learndash plugin activation status.
	 *
	 * @since    1.0.0
	 *
	 * @var      bollean
	 */
	protected static $ld_active_status = false;

	/**
	 * Easy digital download - Learndash Integration plugin activation status.
	 *
	 * @since    1.0.0
	 *
	 * @var      bollean
	 */
	protected static $edd_ld_active_status = false;

	/**
	 * Instance of "EddCourseGiftingEnrollmentMod" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	protected static $ecge_module_instance = null;

	/**
	 * Instance of "EddCourseGiftingCronMod" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	protected static $ecgr_module_instance = null;

	/**
	 * Instance of "GiftEddEmailNotificationMod" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	protected static $genm_module_instance = null;

	/**
	 * Instance of "GiftEddLicense" class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */

	protected static $gel_module_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->set_dependencies_plugin_activation_status();
		$this->includes();
		$this->init_hooks();
		$this->init_classes();
	}

	/*--------------------------------------------------------------------*
	 * Define constants
	 *--------------------------------------------------------------------*/
	private function define_constants() {
		if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_VERSION' ) ) {
			define( 'LEARNDASH_EDD_GIFT_PLUGIN_VERSION', '1.0.0' );
		}
		if ( ! defined( 'LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME' ) ) {
			define( 'LEARNDASH_EDD_GIFT_PUBLIC_HANDLE_NAME', 'learndash-gift-edd-public-handler' );
		}
		if ( ! defined( 'LEARNDASH_EDD_GIFT_ADMIN_HANDLE_NAME' ) ) {
			define( 'LEARNDASH_EDD_GIFT_ADMIN_HANDLE_NAME', 'learndash-gift-edd-admin-handler' );
		}
		if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_SLUG' ) ) {
			define( 'LEARNDASH_EDD_GIFT_PLUGIN_SLUG', 'learndash-gift-edd' );
		}
		if ( ! defined( 'LEARNDASH_EDD_GIFT_TEXT_DOMAIN' ) ) {
			define( 'LEARNDASH_EDD_GIFT_TEXT_DOMAIN', 'learndash-gift-edd' );
		}
	}
	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'registered_text_domain' ), 9 );
		register_activation_hook( LEARNDASH_EDD_GIFT_PLUGIN_FILEPATH, array( $this, 'plugin_activation_handler' ) );
		register_deactivation_hook( LEARNDASH_EDD_GIFT_PLUGIN_FILEPATH, 'plugin_deactivation_handler' );
		add_action( 'admin_notices', array( $this, 'wdm_dependency_admin_notice' ) );
	}

	/**
	 * Includes public and admin files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		if ( self::$edd_active_status && self::$ld_active_status && self::$edd_ld_active_status ) {
			/*--------------------------------------------------------------------*
			 * Include common function file.
			 *-------------------------------------------------------------------
			 */

			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/functions.php' );

			/*--------------------------------------------------------------------*
			 * Public-Facing Functionality
			 *---------------------------------------------------------------------*/

			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . '/public/class-learndasheddgiftpublic.php' );

			/*--------------------------------------------------------------------*
			 * Admin-Facing Functionality
			 *---------------------------------------------------------------------*/

			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'admin/class-learndasheddgiftadmin.php' );

			/*--------------------------------------------------------------------*
			 * Add common gifting module files
			 *---------------------------------------------------------------------*/

			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/class-eddcoursegiftingenrollmentmod.php' );
			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/class-eddcoursegiftingcronmod.php' );
			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/class-gifteddemailnotificationmod.php' );

			/*--------------------------------------------------------------------*
			 * Licensing functionality
			 *---------------------------------------------------------------------*/

			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/class-eddlicense.php' );
		}
	}

	/**
	 * Initialize public and admin functionalities.
	 *
	 * @since 1.0.0
	 */
	private function init_classes() {
		if ( class_exists( 'LearndashEddGiftAdmin' ) ) {
			self::$admin_ld_edd = LearndashEddGiftAdmin::get_instance();
		}
		if ( class_exists( 'LearndashEddGiftPublic' ) ) {
			self::$public_ld_edd = LearndashEddGiftPublic::get_instance();
		}
		if ( class_exists( 'EddCourseGiftingEnrollmentMod' ) ) {
			self::$ecge_module_instance = EddCourseGiftingEnrollmentMod::get_instance();
		}
		if ( class_exists( 'EddCourseGiftingCronMod' ) ) {
			self::$ecgr_module_instance = EddCourseGiftingCronMod::get_instance();
		}
		if ( class_exists( 'GiftEddEmailNotificationMod' ) ) {
			self::$genm_module_instance = GiftEddEmailNotificationMod::get_instance();
		}
		if ( class_exists( 'GiftEddLicense' ) ) {
			self::$gel_module_instance = GiftEddLicense::get_instance();
		}
	}

	/**
	 * Function registeredTextDomain() to load plugins text domain.
	 *
	 * @since 1.0.0
	 */

	public function registered_text_domain() {
		load_plugin_textdomain( 'learndash-gift-edd', false, LEARNDASH_EDD_GIFT_PLUGIN_PATH . '/languages' );
	}

	/**
	 * Plugin activation.
	 *
	 * @since     1.0.0
	 *
	 */

	public function plugin_activation_handler() {
		if ( ! wp_next_scheduled( 'emails_gift_reminder' ) ) {
			wp_schedule_event( time(), 'wdm_emails_gift_reminder', 'emails_gift_reminder' );
		}
		if ( ! wp_next_scheduled( 'gift_emails_handler' ) ) {
			wp_schedule_event( time(), 'wdm_gift_emails_handler', 'gift_emails_handler' );
		}
	}

	/**
	 * Plugin deactivation.
	 *
	 * @since     1.0.0
	 *
	 */

	public function plugin_deactivation_handler() {
		wp_clear_scheduled_hook( 'emails_gift_reminder' );
		wp_clear_scheduled_hook( 'gift_emails_handler' );
	}

	/**
	 * Check plugin dependencies.
	 *
	 * @since     1.0.0
	 *
	 */
	private function set_dependencies_plugin_activation_status() {
		self::$edd_active_status    = $this->wdm_check_plugin_activation_status( 'easy-digital-downloads.php' );
		self::$ld_active_status     = $this->wdm_check_plugin_activation_status( 'sfwd_lms.php' );
		self::$edd_ld_active_status = $this->wdm_check_plugin_activation_status( 'learndash-edd.php' );
	}

	/**
	 * Display admin notice.
	 *
	 * @since     1.0.0
	 *
	 */

	public function wdm_dependency_admin_notice() {
		if ( ! self::$edd_active_status || ! self::$ld_active_status || ! self::$edd_ld_active_status ) {
			printf( '<div class="error"><p>%s</p></div>', __( 'LearnDash or Easy digital download or LearnDash EDD integration plugin is not active. In order to make Gift LearnDash Courses plugin work, you need to install and activate LearnDash, Easy digital download & LearnDash EDD integration plugin first.', 'learndash-gift-edd' ) );
		}
	}

	/**
	 * Check plugin activation status.
	 *
	 * @since     1.0.0
	 *
	 * @param string $plugin_dir_name plugin directoery name
	 * @param string $plugin_slug plugin main file name
	 *
	 * @return    boolean.
	 */
	public function wdm_check_plugin_activation_status( $plugin_slug = '' ) {

		foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
			if ( strpos( $plugin, $plugin_slug ) ) {
				return true;
			}
		}

		return false;
	}
}
