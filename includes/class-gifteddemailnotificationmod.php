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
 * Class to later the email notification functionlaity
 *
 * @package learndash-gift-edd/include/
 * @author    WisdmLabs <wisdmlabs@info.com>
 */

class GiftEddEmailNotificationMod {

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
		add_filter( 'edd_sale_notification', array( $this, 'wdm_edd_sale_notification_handler' ), 11, 2 );
		add_filter( 'edd_email_tags', array( $this, 'wdm_add_extra_email_tags' ), 11, 1 );
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

	public function wdm_edd_sale_notification_handler( $email_body, $payment_id ) {
		$payment = edd_get_payment( $payment_id );

		if ( $payment->user_id > 0 ) {
			$user_data = get_userdata( $payment->user_id );
			$name = $user_data->display_name;
		} elseif ( ! empty( $payment->first_name ) && ! empty( $payment->last_name ) ) {
			$name = $payment->first_name . ' ' . $payment->last_name;
		} else {
			$name = $payment->email;
		}

		$download_list = '';

		if ( is_array( $payment->downloads ) ) {
			foreach ( $payment->downloads as $item ) {
				$download = new EDD_Download( $item['id'] );
				$title    = $download->get_name();
				if ( isset( $item['options'] ) ) {
					if ( isset( $item['options']['price_id'] ) ) {
						$title .= ' - ' . edd_get_price_option_name( $item['id'], $item['options']['price_id'], $payment_id );
					}
				}
				$download_list .= html_entity_decode( $title, ENT_COMPAT, 'UTF-8' ) . "\n";
			}
		}

		$gateway = edd_get_gateway_admin_label( $payment->gateway );

		$default_email_body = __( 'Hello', 'learndash-gift-edd' ) . "\n\n" . sprintf( __( 'A %s purchase has been made', 'learndash-gift-edd' ), edd_get_label_plural() ) . ".\n\n";
		$default_email_body .= sprintf( __( '%s sold:', 'learndash-gift-edd' ), edd_get_label_plural() ) . "\n\n";
		$default_email_body .= $download_list . "\n\n";
		$default_email_body .= __( 'Purchased by: ', 'learndash-gift-edd' ) . ' ' . html_entity_decode( $name, ENT_COMPAT, 'UTF-8' ) . "\n";
		$default_email_body .= __( 'Amount: ', 'learndash-gift-edd' ) . ' ' . html_entity_decode( edd_currency_filter( edd_format_amount( $payment->total ) ), ENT_COMPAT, 'UTF-8' ) . "\n";
		$default_email_body .= __( 'Payment Method: ', 'learndash-gift-edd' ) . ' ' . $gateway . "\n\n";
		$default_email_body .= $this->ld_edd_gift_admin_notification_email_body( $payment_id );
		$default_email_body .= __( 'Thank you', 'learndash-gift-edd' );

		$message = edd_get_option( 'sale_notification', false );
		$message   = $message ? stripslashes( $message ) : $default_email_body;

		$email_body = edd_do_email_tags( $message, $payment_id );

		$email_body = apply_filters( 'edd_email_template_wpautop', true ) ? wpautop( $email_body ) : $email_body;

		return $email_body;
	}

	public function ld_edd_gift_admin_notification_email_body( $payment_id ) {
		$buy_as_gift_status = get_post_meta( $payment_id, 'buy_as_gift_status', true );

		ob_start();
		if ( $buy_as_gift_status ) {

			$customer_email = get_post_meta( $payment_id, 'edd_ld_gift_email', true );
			$customer_first_name = get_post_meta( $payment_id, 'edd_ld_gift_first_name', true );
			$customer_last_name = get_post_meta( $payment_id, 'edd_ld_gift_last_name', true );

			echo __( 'Purchase as a gift : <b>Yes</b>', 'learndash-gift-edd' ) . "\n\n";
			echo sprintf( __( ' Gift recipient email : %s.', 'learndash-gift-edd' ), $customer_email ) . "\n\n";
			if ( $customer_first_name || $customer_last_name ) {
				echo sprintf( __( ' Gift recipient name : %1$s %2$s.', 'learndash-gift-edd' ), $customer_first_name, $customer_last_name ) . "\n\n";
			}
		}
		return ob_get_clean();
	}

	public function wdm_add_extra_email_tags( $email_tags ) {
		$email_tags[] = array(
			'tag'         => 'purchase_as_gift',
			'description' => __( 'Purchase as gift', 'learndash-gift-edd' ),
			'function'    => 'purchase_as_gift_tag',
		);
		$email_tags[] = array(
			'tag'         => 'gift_receiver_first_name',
			'description' => __( 'Gift receiver first name', 'learndash-gift-edd' ),
			'function'    => 'gift_receiver_first_name_tag',
		);
		$email_tags[] = array(
			'tag'         => 'gift_receiver_last_name',
			'description' => __( 'Gift receiver last name', 'learndash-gift-edd' ),
			'function'    => 'gift_receiver_last_name_tag',
		);
		$email_tags[] = array(
			'tag'         => 'gift_receiver_email',
			'description' => __( 'Gift receiver email', 'learndash-gift-edd' ),
			'function'    => 'gift_receiver_email_tag',
		);
		return $email_tags;
	}
}
