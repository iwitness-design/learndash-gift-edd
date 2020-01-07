<?php

/**
 * Represents the view of buy as gift template on checkout page.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   NeonCRMIntegration
 * @author    WisdmLabs <wisdmlabs@info.com>
 * @license   GPL-2.0+
 * @link      https://wisdmlabs.com
 * @copyright 2016 WisdmLabs or Company Name
 */

?>

<fieldset id = "edd_checkout_buy_as_gift_info">
	<div class = "buy_as_gift_checkbox_div">
		<p>
			<input type = "checkbox" class="buy_as_gift_checkbox" name = "buy_as_gift_checkbox" value = "on" style="cursor:pointer;" title="Enable to purchase as a gift">
			<label class="wdm_label buy_as_gift_checkbox_label textinput"><?php echo apply_filters( 'edd_ld_buy_as_gift_info_text', $gift_label ); ?></label>
		</p>
	</div>
	<div class = "buy_as_gift_section" style="display:none">
		<?php do_action( 'edd_ld_buy_as_gift_before_email' ); ?>
		<p id="edd-ld-gift-email-wrap">
			<p>
				<label class="edd-label" for="edd-ld-gift-first-name">
					<?php esc_html_e( 'First Name', 'learndash-gift-edd' ); ?>
				</label>
				<span class="edd-description" id="edd-ld-gift-first-name-description"><?php esc_html_e( 'Recipient first name', 'learndash-gift-edd' ); ?></span>
				<input class="edd-input" type="text" name="edd_ld_gift_first_name" placeholder="<?php esc_html_e( 'First name', 'learndash-gift-edd' ); ?>" id="edd-ld-gift-first-name" value="" aria-describedby="edd-email-first-name-description" />
			</p>
			<p>
				<label class="edd-label" for="edd-ld-gift-last-name">
					<?php esc_html_e( 'Last Name', 'learndash-gift-edd' ); ?>
				</label>
				<span class="edd-description" id="edd-ld-gift-last-name-description"><?php esc_html_e( 'Recipient last name', 'learndash-gift-edd' ); ?></span>
				<input class="edd-input" type="text" name="edd_ld_gift_last_name" placeholder="<?php esc_html_e( 'Last name', 'learndash-gift-edd' ); ?>" id="edd-ld-gift-last-name" value="" aria-describedby="edd-email-last-name-description" />
			</p>
			<p>
				<label class="edd-label" for="edd-ld-gift-email">
					<?php esc_html_e( 'Email Address', 'learndash-gift-edd' ); ?>
						<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description" id="edd-ld-gift-email-description"><?php esc_html_e( 'We will create the user using this email and enroll in the purchased courses.', 'learndash-gift-edd' ); ?></span>
				<input class="edd-input required" type="email" name="edd_ld_gift_email" placeholder="<?php esc_html_e( 'Email address', 'learndash-gift-edd' ); ?>" id="edd-ld-gift-email" value="" aria-describedby="edd-email-description" />
			</p>
			<p>
				<label class="edd-label" for="edd-ld-gift-message">
					<?php esc_html_e( 'Message', 'learndash-gift-edd' ); ?>
				</label>
				<span class="edd-description" id="edd-ld-gift-message-description"><?php esc_html_e( 'This message will included in the gift receiver email', 'learndash-gift-edd' ); ?></span>
				<textarea rows="4" cols="50" name="edd_ld_gift_message" placeholder="Message to gift receiver"></textarea>
			<p>
			<?php
			if ( $send_later_status ) {
				?>
				<p>
					<label class="edd-label" for="edd-ld-gift-date">
						<?php esc_html_e( 'Select the date', 'learndash-gift-edd' ); ?>
							<span class="edd-required-indicator">*</span>
					</label>
					<span class="edd-description" id="edd-ld-gift-date-description"><?php esc_html_e( 'On selected date the user will be enrolled in the purchased course', 'learndash-gift-edd' ); ?></span>
					<input class="edd-input edd_ld_gift_date required" type="text" name="edd_ld_gift_date" placeholder="<?php esc_html_e( 'Select date', 'learndash-gift-edd' ); ?>" id="edd-ld-gift-date" value="" aria-describedby="edd-email-description" autocomplete="off" required readonly/>
				</p>
				<input type="hidden" name="send_later_status" value = "true">
				<?php
			}
			?>
			<input type="hidden" name="buy_as_gift_status" value = "true">
		</p>
		<?php do_action( 'edd_ld_buy_as_gift_after_email' ); ?>
	</div>
</fieldset>
