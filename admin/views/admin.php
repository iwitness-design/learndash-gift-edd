<?php

/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 */
/**
 * @package   learndash-gift-edd
 * @author    WisdmLabs <wisdmlabs@info.com>
 * @license   GPL-2.0+
 * @link      https://wisdmlabs.com
 * @copyright 2016 WisdmLabs or Company Name
 */

$license = get_option( 'learndash-gift-edd_license_key', '' );
$status  = get_option( 'learndash-gift-edd_license_status', '' );

?>

<div id="wdm-learndash-gift-edd-setting" class="wrap">
	<form method="post" action="options.php">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<?php settings_fields( 'learndash_edd_gift_options' ); ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="learndash-gift-edd_license_key"><?php _e( 'License Key', 'learndash-gift-edd' ); ?></label>
                </th>
                <td>
                    <p><input class="regular-text" type="text" id="learndash-gift-edd_license_key"
                              name="learndash-gift-edd_license_key" value="<?php echo esc_attr( $license ); ?>"/>
						<?php if ( $status == 'valid' ) : ?>
							<?php wp_nonce_field( 'learndash-gift-edd_deactivate_license', 'learndash-gift-edd_deactivate_license' ); ?>
							<?php submit_button( 'Deactivate License', 'secondary', 'learndash-gift-edd_license_deactivate', false ); ?>
                            <span style="color:green">&nbsp;&nbsp;<?php _e( 'active', 'learndash-gift-edd' ); ?></span>
						<?php elseif ( $license ) : ?>
							<?php submit_button( 'Activate License', 'secondary', 'learndash-gift-edd_license_activate', false ); ?>
						<?php endif; ?></p>

                    <p class="description"><?php printf( __( 'Enter your LearDash EDD Gift license key. This is required for automatic updates and <a href="%s">support</a>.', 'learndash-gift-edd' ), 'https://www.bloopanimation.com/support/' ); ?></p>
                </td>
            </tr>

        </table>
        <?php wp_nonce_field( 'learndash-gift-edd_nonce', 'learndash-gift-edd_nonce' ); ?>
		<div class = "wdm-learndash-gift-edd-email-settings">
			<br>
			<i>
				<b><?php _e( 'Note:', 'learndash-gift-edd' ); ?></b>
				<?php _e( 'The following placeholders can be used in email body :', 'learndash-gift-edd' ); ?>
				<p>
					<b><?php echo '{site_name}, {purchaser_user_first_name}, {purchaser_user_last_name}, {purchaser_user_email}, {course_list}, {gift_receiver_user_first_name}, {gift_receiver_user_last_name}, {gift_receiver_user_email}, {purchaser_message}'; ?></b>
				</p>
				<!-- <p style='margin-left: 2%;'>
					<b><?php echo __( 'For Reattempts', 'learndash-gift-edd' ); ?> : <?php _e( '{reattempt_details}', 'learndash-gift-edd' ); ?></b>
				</p> -->
			</i>
			<br>
			<h2><?php _e( 'Gift Email Template', 'learndash-gift-edd' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php _e( 'Email Subject', 'learndash-gift-edd' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input type="text" name="learndash_edd_gift_data[learndash_edd_gift_email_subject]" value="<?php echo self::get_notification_email( 'learndash_edd_gift_email_subject' ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php _e( 'Email Body', 'learndash-gift-edd' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<?php
							wp_editor(
								self::get_notification_email( 'learndash_edd_gift_email_body' ),
								'wdm_learndash_edd_gift_email_body',
								array(
									'textarea_rows' => 100,
									'editor_height' => 200,
									'media_buttons' => true,
									'wpautop' => true,
									'textarea_name' => 'learndash_edd_gift_data[learndash_edd_gift_email_body]',
								)
							);
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class = "wdm-learndash-gift-edd-checkout-settings">
			<h2><?php _e( 'Checkout Page Gift Settings', 'learndash-gift-edd' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php _e( 'Set "Buy as a gift" label', 'learndash-gift-edd' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input type="text" name="learndash_edd_gift_data[buy_as_gift_label]" value="<?php echo self::get_notification_email( 'buy_as_gift_label' ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php _e( 'Enable send gift later option', 'learndash-gift-edd' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input type="checkbox" name="learndash_edd_gift_data[enable_send_later]" value="checked" <?php echo self::get_notification_email( 'enable_send_later' ); ?>/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php _e( 'Enable purchaser enrollment to course', 'learndash-gift-edd' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input type="checkbox" name="learndash_edd_gift_data[enable_purchaser_enrollment_to_course]" value="checked" <?php echo self::get_notification_email( 'enable_purchaser_enrollment_to_course' ); ?>/>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php submit_button(); ?>
	</form>
</div>


