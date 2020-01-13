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
 * @package learndash-gift-edd/public/modules/
 * @author    WisdmLabs <wisdmlabs@info.com>
 */

class EddCourseGiftingCheckoutMod {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;


	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		add_action( 'edd_checkout_form_top', array( $this, 'render_buy_as_gift_html' ), 10 );
		add_action( 'edd_payment_saved', array( $this, 'save_purchaser_gift_data' ), 10, 1 );
		add_action( 'edd_checkout_error_checks', array( $this, 'purchaser_gift_data_validation' ), 10, 2 );
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
	 * Funtion to render the gift frontend view.
	 *
	 * @since     1.0.0
	 *
	 */
	public function render_buy_as_gift_html() {
		$cart_items = edd_get_cart_contents();
		$gift_label = '';
		$send_later_status = false;
		$dbagstatus = $this->get_display_buy_as_gift_option_status( $cart_items );
		if ( $dbagstatus ) {
			$settings = get_plugin_setting_data();
			$gift_label = ! empty( $settings['buy_as_gift_label'] ) ? $settings['buy_as_gift_label'] : __( 'Buy as a gift', 'learndash-gift-edd' );
			$send_later_status = ( ! empty( $settings['enable_send_later'] ) && 'checked' == $settings['enable_send_later'] ) ? true : false;
			include_once( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'public/views/edd-ld-course-gift-template.php' );
		}
	}
	/**
	 * Funtion which checks whether cart product has associated courses or not.
	 *
	 * @since     1.0.0
	 *
	 * @param array $cart_items Array of cart items
	 *
	 * @return    boolean.
	 */

	public function get_display_buy_as_gift_option_status( $cart_items ) {
		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $cart_item ) {
				$assgined_status = get_course_assigned_to_downloads_status( $cart_item['id'] );
				if ( $assgined_status ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Funtion to save the gift data.
	 *
	 * @since     1.0.0
	 *
	 */

	public function save_purchaser_gift_data( $payment_id ) {
		if ( isset( $_POST['buy_as_gift_checkbox'] ) && $_POST['buy_as_gift_checkbox'] ) {
			$email = filter_var( $_POST['edd_ld_gift_email'], FILTER_SANITIZE_EMAIL );
			$message = filter_var( $_POST['edd_ld_gift_message'], FILTER_SANITIZE_STRING );
			$first_name = filter_var( $_POST['edd_ld_gift_first_name'], FILTER_SANITIZE_STRING );
			$last_name = filter_var( $_POST['edd_ld_gift_last_name'], FILTER_SANITIZE_STRING );
			update_post_meta( $payment_id, 'buy_as_gift_status', true );
			update_post_meta( $payment_id, 'edd_ld_gift_email', $email );
			update_post_meta( $payment_id, 'edd_ld_gift_message', $message );
			update_post_meta( $payment_id, 'edd_ld_gift_first_name', $first_name );
			update_post_meta( $payment_id, 'edd_ld_gift_last_name', $last_name );
			if ( isset( $_POST['send_later_status'] ) && $_POST['send_later_status'] ) {
				update_post_meta( $payment_id, 'send_later_status', true );
				update_post_meta( $payment_id, 'edd_ld_gift_date', $_POST['edd_ld_gift_date'] );
			}
		}
	}

	/**
	 * Funtion to validate gift data.
	 *
	 * @since     1.0.0
	 *
	 */

	public function purchaser_gift_data_validation( $valid_data, $posted ) {
		$cart_items = edd_get_cart_contents();
		$p_a_g_status = false;
		if ( check_isset_and_not_empty( $posted, 'buy_as_gift_checkbox' ) ) {
			$p_a_g_status = true;
		}
		$valid_data = $valid_data;
		$dbagstatus = $this->get_display_buy_as_gift_option_status( $cart_items );
		if ( $dbagstatus && $p_a_g_status ) {
			$settings = get_plugin_setting_data();
			if ( ! check_isset_and_not_empty( $posted, 'buy_as_gift_status' ) ) {
				edd_set_error( 'unexpected_error', __( 'Some things went wrong, please try again.', 'learndash-gift-edd' ) );
			}
			if ( ! check_isset_and_not_empty( $posted, 'edd_ld_gift_email', true ) ) {
				edd_set_error( 'empty_gift_receiver_email', __( 'Please enter the gift recevier email address', 'learndash-gift-edd' ) );
			}
			$send_later_status = ( ! empty( $settings['enable_send_later'] ) && 'checked' == $settings['enable_send_later'] ) ? true : false;
			if ( $send_later_status ) {
				if ( ! check_isset_and_not_empty( $posted, 'send_later_status' ) ) {
					edd_set_error( 'unexpected_error', __( 'Some things went wrong, please try again.', 'learndash-gift-edd' ) );
				}
				if ( ! check_isset_and_not_empty( $posted, 'edd_ld_gift_date' ) ) {
					edd_set_error( 'empty_gift_course_enrollment_date', __( 'Please set the date for gift course enrollment.', 'learndash-gift-edd' ) );
				}
			}
		}
	}
}
