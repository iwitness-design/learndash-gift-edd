<?php

if ( ! function_exists( 'wdm_check_plugin_activation_status' ) ) {
	function wdm_check_plugin_activation_status( $plugin_dir_name = '', $plugin_slug = '' ) {
		if ( empty( $plugin_slug ) && empty( $plugin_dir_name ) ) {
			return false;
		} else {
			$plugin_slug = $plugin_slug . '.php';
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active( $plugin_dir_name . '/' . $plugin_slug ) ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
}
if ( ! function_exists( 'get_plugin_setting_data' ) ) {
	function get_plugin_setting_data() {
		return get_option( 'learndash_edd_gift_data' );
	}
}
if ( ! function_exists( 'get_course_assigned_to_downloads_status' ) ) {
	function get_course_assigned_to_downloads_status( $download_id ) {
		$download_c_status = get_post_meta( $download_id, '_edd_learndash_is_course', true );
		$download_courses = get_post_meta( $download_id, '_edd_learndash_course', true );
		if ( $download_c_status && is_array( $download_courses ) && ! empty( $download_courses ) ) {
			return true;
		}
		return false;
	}
}
if ( ! function_exists( 'check_isset_and_not_empty' ) ) {
	function check_isset_and_not_empty( $array = array(), $key = '', $val_email = false ) {
		if ( isset( $array[ $key ] ) ) {
			$value = $array[ $key ];
			if ( ! $val_email ) {
				$value = filter_var( $value, FILTER_SANITIZE_EMAIL );
			}
			if ( $value ) {
				return true;
			}
		}
		return false;
	}
}
if ( ! function_exists( 'get_gift_receiver_user_id' ) ) {
	function get_gift_receiver_user_id( $customer_email, $customer_first_name = '', $customer_last_name = '' ) {
		$exists = email_exists( $customer_email );
		if ( $exists ) {
			update_user_meta( $exists, 'edd_gift_first_name', $customer_first_name );
			update_user_meta( $exists, 'edd_gift_firstlast_name', $customer_last_name );
			return $exists;
		}
		$user_name = $customer_email;
		if ( username_exists( $customer_email ) ) {
			$user_name = $customer_email . wp_generate_password( 4, false );
		}

		$random_password = wp_generate_password( 12, false );
		$user_id = wp_create_user( $user_name, $random_password, $customer_email );
		update_user_meta( $user_id, 'edd_gift_first_name', $customer_first_name );
		update_user_meta( $user_id, 'edd_gift_last_name', $customer_last_name );
		update_user_meta( $user_id, 'first_name', $customer_first_name );
		update_user_meta( $user_id, 'last_name', $customer_last_name );
		return $user_id;
	}
}
if ( ! function_exists( 'send_gift_email' ) ) {
	function send_gift_email( $user_id, $customer_id, $remove, $b_a_g_message, $courses = array() ) {
		if ( ! $remove ) {
			$course_outline = '';
			$settings_data = get_plugin_setting_data();
			$user_details = get_userdata( $customer_id );
			$customer_first_name = get_user_meta( $customer_id, 'edd_gift_first_name', true );
			$customer_last_name = get_user_meta( $customer_id, 'edd_gift_last_name', true );
			$purchaser_user_data = get_userdata( $user_id );
			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'MIME-Version: 1.0' );
			$site_title = get_bloginfo( 'name' );
			$admin_email = get_option( 'admin_email' );
			$headers[] = 'FROM: ' . $site_title . ' <' . $admin_email . '>';
			$subject = $settings_data['learndash_edd_gift_email_subject'];
			$message = wpautop( $settings_data['learndash_edd_gift_email_body'] );
			$message = apply_filters( 'the_content', $message );
			$search = array( '{site_name}', '{purchaser_user_first_name}', '{purchaser_user_last_name}', '{purchaser_user_email}', '{course_list}', '{gift_receiver_user_first_name}', '{gift_receiver_user_last_name}', '{gift_receiver_user_email}', '{purchaser_message}' );
			$course_outline .= '<ul>';
			foreach ( $courses as $course_id ) {
				$course_outline .= '<li><a href = "' . get_permalink( $course_id ) . '" target = "_blank">' . get_the_title( $course_id ) . '</a></li>';
			}
			$course_outline .= '</ul>';
			$replace = array(
				$site_title,
				$purchaser_user_data->first_name,
				$purchaser_user_data->last_name,
				$purchaser_user_data->user_email,
				$course_outline,
				$customer_first_name,
				$customer_last_name,
				$user_details->user_email,
				$b_a_g_message,
			);
			$body = str_replace( $search, $replace, $message );
			wp_mail( $user_details->user_email, $subject, $body, $headers );
		}
	}
}
if ( ! function_exists( 'purchase_as_gift_tag' ) ) {
	function purchase_as_gift_tag( $payment_id ) {
		$buy_as_gift_status = get_post_meta( $payment_id, 'buy_as_gift_status', true );
		$buy_as_gift = __( 'No', 'learndash-gift-edd' );
		if ( $buy_as_gift_status ) {
			$buy_as_gift = __( 'Yes', 'learndash-gift-edd' );
		}
		return $buy_as_gift;
	}
}

if ( ! function_exists( 'gift_receiver_email_tag' ) ) {
	function gift_receiver_email_tag( $payment_id ) {
		$customer_email = get_post_meta( $payment_id, 'edd_ld_gift_email', true );
		return $customer_email;
	}
}
if ( ! function_exists( 'gift_receiver_first_name_tag' ) ) {
	function gift_receiver_first_name_tag( $payment_id ) {
		$customer_first_name = get_post_meta( $payment_id, 'edd_ld_gift_first_name', true );
		return $customer_first_name;
	}
}
if ( ! function_exists( 'gift_receiver_last_name_tag' ) ) {
	function gift_receiver_last_name_tag( $payment_id ) {
		$customer_last_name = get_post_meta( $payment_id, 'edd_ld_gift_last_name', true );
		return $customer_last_name;
	}
}
