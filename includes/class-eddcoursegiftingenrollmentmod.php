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

class EddCourseGiftingEnrollmentMod {

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
		add_action( 'plugins_loaded', array( $this, 'remove_learndash_edd_hooks' ), 11 );
		// Update courses
		add_action( 'edd_updated_edited_purchase', array( $this, 'updated_edited_purchase' ) );
		add_action( 'edd_complete_purchase', array( $this, 'complete_purchase' ) );
		add_action( 'edd_recurring_update_subscription', array( $this, 'update_subscription' ), 10, 3 );

		// Remove courses
		add_action( 'edd_subscription_cancelled', array( $this, 'cancel_subscription' ), 10, 2 );
		add_action( 'edd_subscription_expired', array( $this, 'cancel_subscription' ), 10, 2 );
		add_action( 'edd_update_payment_status', array( $this, 'remove_access_on_payment_update' ), 10, 2 );
		add_action( 'edd_payment_delete', array( $this, 'remove_access_on_payment_delete' ), 10, 1 );
	}

	public function remove_learndash_edd_hooks() {
		$learndash_instance = LearnDash_EDD::instance();
		remove_action( 'edd_updated_edited_purchase', array( $learndash_instance, 'updated_edited_purchase' ) );
		remove_action( 'edd_complete_purchase', array( $learndash_instance, 'complete_purchase' ) );
		remove_action( 'edd_recurring_update_subscription', array( $learndash_instance, 'update_subscription' ) );
		remove_action( 'edd_subscription_cancelled', array( $learndash_instance, 'cancel_subscription' ) );
		remove_action( 'edd_subscription_expired', array( $learndash_instance, 'cancel_subscription' ) );
		remove_action( 'edd_update_payment_status', array( $learndash_instance, 'remove_access_on_payment_update' ) );
		remove_action( 'edd_payment_delete', array( $learndash_instance, 'remove_access_on_payment_delete' ) );
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
	 * Give course access when payment is updated to complete on payment edit screen
	 *
	 * @param  int  $payment_id   ID of a payment
	 * @since  1.1.0
	 */
	public function updated_edited_purchase( $payment_id ) {
		$payment = new EDD_Payment( $payment_id );

		if ( 'publish' != $payment->status && 'edd_subscription' != $payment->status ) {
			return;
		}

		$this->update_course_access( $payment_id );
	}

	/**
	 * Give course access when user complete a purchase
	 *
	 * @param  int  $payment_id   ID of a transaction
	 * @since  1.0.0
	 */
	public function complete_purchase( $payment_id ) {
		$this->update_course_access( $payment_id );
	}

	/**
	 * Update LearnDash course access
	 *
	 * @since       1.0.0
	 * @return      void
	 */
	public function update_course_access( $transaction_id, $remove = false ) {
		$enroll_courses_array = array();
		$gifted_courses_array = array();
		// Get transaction data
		$settings = get_plugin_setting_data();
		$epet_course = false;
		if ( isset( $settings['enable_purchaser_enrollment_to_course'] ) && 'checked' == $settings['enable_purchaser_enrollment_to_course'] ) {
			$epet_course = true;
		}
		$transaction = get_post( $transaction_id );
		if ( $transaction ) {
			$buy_as_gift_status = get_post_meta( $transaction_id, 'buy_as_gift_status', true );
			$send_later_status = get_post_meta( $transaction_id, 'send_later_status', true );
			$customer_email = get_post_meta( $transaction_id, 'edd_ld_gift_email', true );
			$customer_first_name = get_post_meta( $transaction_id, 'edd_ld_gift_first_name', true );
			$customer_last_name = get_post_meta( $transaction_id, 'edd_ld_gift_last_name', true );
			$customer_user_id = get_post_meta( $transaction_id, '_edd_payment_user_id', true );
			$b_a_g_message = get_post_meta( $transaction_id, 'edd_ld_gift_message', true );
			$courses = $this->get_transaction_courses( $transaction_id );
			if ( is_array( $courses ) && ! empty( $courses ) ) {
				if ( $epet_course || ! $buy_as_gift_status ) {
					// Get customer ID.

					if ( ! empty( $customer_user_id ) ) {
						foreach ( $courses as $course_id ) {
							ld_update_course_access( (int) $customer_user_id, (int) $course_id, $remove );
							$enroll_courses_array[ $course_id ] = $course_id;
						}
					}
					//send_gift_email($customer_id, $remove, $enroll_courses_array);
				}
				if ( $buy_as_gift_status && ! empty( $customer_email ) ) {

					if ( $send_later_status && ! $remove ) {
						$edd_ld_gift_date = get_post_meta( $transaction_id, 'edd_ld_gift_date', true );
						$c_u_e_status = $this->current_user_enrollment_status( $transaction_id, $edd_ld_gift_date, $customer_email, $courses, $customer_user_id );
						if ( $c_u_e_status ) {
							$customer_id = get_gift_receiver_user_id( $customer_email, $customer_first_name, $customer_last_name );
							foreach ( $courses as $course_id ) {
								ld_update_course_access( (int) $customer_id, (int) $course_id, $remove );
								$gifted_courses_array[ $course_id ] = $course_id;
							}
							send_gift_email( $customer_user_id, $customer_id, $remove, $b_a_g_message, $gifted_courses_array );
						}
					} else {
						$customer_id = get_gift_receiver_user_id( $customer_email, $customer_first_name, $customer_last_name );
						foreach ( $courses as $course_id ) {
							ld_update_course_access( (int) $customer_id, (int) $course_id, $remove );
							$gifted_courses_array[ $course_id ] = $course_id;
						}
						if ( ! $remove ) {
							send_gift_email( $customer_user_id, $customer_id, $remove, $b_a_g_message, $gifted_courses_array );
						}
					}
				}
			}
		}
	}

	public function get_transaction_courses( $transaction_id ) {
		$courses_array = array();
		$edd_payment_meta = get_post_meta( $transaction_id, '_edd_payment_meta', true );
		if ( ( isset( $edd_payment_meta['downloads'] ) )
				 && ( is_array( $edd_payment_meta['downloads'] ) )
				 && ( ! empty( $edd_payment_meta['downloads'] ) ) ) {
			foreach ( $edd_payment_meta['downloads'] as $download ) {
				if ( isset( $download['id'] ) ) {

					$download_id = intval( $download['id'] );
					if ( ! empty( $download_id ) ) {
						// Get the Courses
						$courses = get_post_meta( $download_id, '_edd_learndash_course', true );
						if ( ( is_array( $courses ) ) && ( ! empty( $courses ) ) ) {
							$courses_array = array_merge( $courses_array, $courses );
						}
					}
				}
			}
		}
		$courses_array = array_unique( $courses_array );
		return $courses_array;
	}

	public function current_user_enrollment_status( $transaction_id, $edd_ld_gift_date, $customer_email, $courses, $purchaser_user_id ) {
		$date = DateTime::createFromFormat( 'd-m-Y', $edd_ld_gift_date );
		$elgd_timestamp = $date->getTimestamp();
		$todays_date = gmdate( 'd-m-Y' );
		$todays_date_obj = DateTime::createFromFormat( 'd-m-Y', $todays_date );
		$td_timestamp = $todays_date_obj->getTimestamp();
		if ( $elgd_timestamp <= $td_timestamp ) {
			return true;
		} else {
			$enrollment_record = get_option( 'buy_as_gift_user_enrollment_track' );
			if ( empty( $enrollment_record ) ) {
				$enrollment_record = array();
			}
			$enrollment_record[ $transaction_id ]['date'] = $edd_ld_gift_date;
			$enrollment_record[ $transaction_id ]['email'] = $customer_email;
			$enrollment_record[ $transaction_id ]['purchaser_user_id'] = $purchaser_user_id;
			$enrollment_record[ $transaction_id ]['courses'] = $courses;
			update_option( 'buy_as_gift_user_enrollment_track', $enrollment_record );
			return false;
		}
		return false;
	}

	/**
	 * Update course access when subscription is completed
	 *
	 * @param  int    $subscription_id  ID of a subscription
	 * @param  object $subscription     EDD_Subscription object
	 * @since  1.1.0
	 */
	public function update_subscription( $subscription_id, $args, $subscription ) {
		$subscription_id = $subscription_id;
		$args = $args;
		if ( 'active' != $args['status'] && 'completed' != $args['status'] ) {
			return;
		}

		$transaction_id = $subscription->parent_payment_id;

		$this->update_course_access( $transaction_id );
	}

	/**
	 * Remove course access when subscription is cancelled
	 *
	 * @param  int    $subscription_id  ID of a subscription
	 * @param  object $subscription     EDD_Subscription object
	 * @since  1.1.0
	 */
	public function cancel_subscription( $subscription_id, $subscription ) {
		$subscription_id = $subscription_id;
		$transaction_id = $subscription->parent_payment_id;

		$this->update_course_access( $transaction_id, true );
	}

	/**
	 * Remove user course access when EDD payment status is updated to
	 * other than completed and renewal
	 *
	 * @param  int    $payment_id ID of the payment
	 * @param  string $status     New status
	 * @param  string $old_status Old status
	 */
	public function remove_access_on_payment_update( $payment_id, $status ) {
		if ( 'complete' == $status || 'edd_subscription' == $status ) {
			return;
		}

		$this->update_course_access( $payment_id, true );
	}

	/**
	 * Remove user course access when EDD payment status is deleted
	 *
	 * @param  int    $payment_id ID of the payment
	 */
	public function remove_access_on_payment_delete( $payment_id ) {
		$this->update_course_access( $payment_id, true );
	}
}
