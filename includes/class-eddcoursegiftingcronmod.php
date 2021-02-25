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

class EddCourseGiftingCronMod {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

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
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */

	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'wdm_cron_cchedules' ) );
		add_action( 'emails_gift_reminder', array( $this, 'email_gift_reminder' ) );
		add_action( 'gift_emails_handler', array( $this, 'gift_email_handler' ) );
	}

	public function wdm_cron_cchedules( $schedules ) {
		if ( ! isset( $schedules['wdm_emails_gift_reminder'] ) ) {
			$schedules['wdm_emails_gift_reminder'] = array(
				'interval' => 1800,
				'display' => __( 'After 30 min', 'learndash-gift-edd' ),
			);
		}
		if ( ! isset( $schedules['wdm_gift_emails_handler'] ) ) {
			$schedules['wdm_gift_emails_handler'] = array(
				'interval' => 300,
				'display' => __( 'After 5 min', 'learndash-gift-edd' ),
			);
		}
		return $schedules;
	}

	public function email_gift_reminder() {
		$enrollment_record = get_option( 'buy_as_gift_user_enrollment_track' );
		$later_emails = get_option( 'buy_as_gift_user_emails_record' );
		$later_emails = ( is_array( $later_emails ) && ! empty( $later_emails ) ) ? $later_emails : array();
		if ( is_array( $enrollment_record ) && ! empty( $enrollment_record ) ) {
			foreach ( $enrollment_record as $transaction_id => $transaction_data ) {
				$payment = get_post( $transaction_id );
				if ( 'publish' != $payment->post_status && 'edd_subscription' != $payment->post_status ) {
					unset( $enrollment_record[ $transaction_id ] );
					continue;
				}
				$b_a_g_message = get_post_meta( $transaction_id, 'edd_ld_gift_message', true );
				$customer_first_name = get_post_meta( $transaction_id, 'edd_ld_gift_first_name', true );
				$customer_last_name = get_post_meta( $transaction_id, 'edd_ld_gift_last_name', true );
				$edd_ld_gift_date = $transaction_data['date'];
				$customer_email = $transaction_data['email'];
				$purchaser_user_id = $transaction_data['purchaser_user_id'];
				$todays_date = gmdate( 'd-m-Y H:i:s' );
				$todays_date_obj = DateTime::createFromFormat( 'd-m-Y H:i:s', $todays_date );
				$todays_d_timestamp = $todays_date_obj->getTimestamp();
				if ( $edd_ld_gift_date <= $todays_d_timestamp ) {
					$enrolled_c_array = array();
					$customer_id = get_gift_receiver_user_id( $customer_email, $customer_first_name, $customer_last_name );
					foreach ( $transaction_data['courses'] as $course_id ) {
						ld_update_course_access( (int) $customer_id, (int) $course_id );
						$enrolled_c_array[ $course_id ] = $course_id;
					}
					$later_emails[ $transaction_id ] = array(
						'purchaser_user_id' => $purchaser_user_id,
						'customer_id' => $customer_id,
						'b_a_g_message' => $b_a_g_message,
						'enrolled_c_array' => $enrolled_c_array,
					);
					unset( $enrollment_record[ $transaction_id ] );
				}
			}
			if ( ! is_array( $enrollment_record ) && empty( $enrollment_record ) ) {
				$enrollment_record = array();
			}
			update_option( 'buy_as_gift_user_enrollment_track', $enrollment_record );
			update_option( 'buy_as_gift_user_emails_record', $later_emails );
		}
	}
	public function gift_email_handler() {
		$later_emails = get_option( 'buy_as_gift_user_emails_record' );
		if ( ! empty( $later_emails ) && is_array( $later_emails ) ) {
			$email_once_status = false;
			foreach ( $later_emails as $transaction_id => $transaction_data ) {
				if ( $email_once_status ) {
					return;
				} else {
					send_gift_email( $transaction_data['purchaser_user_id'], $transaction_data['customer_id'], false, $transaction_data['b_a_g_message'], $transaction_data['enrolled_c_array'] );
					unset( $later_emails[ $transaction_id ] );
					update_option( 'buy_as_gift_user_emails_record', $later_emails );
					$email_once_status = true;

					/**
					 * Send Giftee information to Convertkit
					 */
					$user_info = [
						'first_name'    => get_post_meta( $transaction_id, 'edd_ld_gift_first_name', true),
						'last_name'     => get_post_meta( $transaction_id, 'edd_ld_gift_last_name', true),
						'email'         => get_post_meta( $transaction_id, 'edd_ld_gift_email', true)
					];
					$ck_obj = new EDD_ConvertKit();
					$response = $ck_obj->subscribe_email($user_info);
					if ($response) {
						$payment = new EDD_Payment( $transaction_id );
						$payment->update_meta( 'convertkit_subscription', true );
					}
				}
			}
		}
	}
}
