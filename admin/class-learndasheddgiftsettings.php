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
 * @package learndash-gift-edd/admin/modules/plugin-settings/
 * @author    WisdmLabs <wisdmlabs@info.com>
 */

class LearndashEddGiftSettings {


	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	protected static $plugin_s_option = array();

	private function __construct() {
		//
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
	public static function display_admin_page() {
		include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . '/admin/views/admin.php' );
	}
	public static function get_notification_email( $type ) {
		$value = '';
		if ( empty( self::$plugin_s_option ) ) {
			$plugin_s_option = get_plugin_setting_data();
			self::$plugin_s_option = is_array( $plugin_s_option ) ? $plugin_s_option : array();
		}
		$value = isset( self::$plugin_s_option[ $type ] ) ? self::$plugin_s_option[ $type ] : '';
		return $value;
	}
}
