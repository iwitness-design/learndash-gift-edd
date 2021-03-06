<?php
/**
 * Gift LearnDash Courses.
 *
 * @package   learndash-gift-edd
 * @author    MissionLab <missionlab.dev>
 * @license   GPL-2.0+
 * @link      https://missionlab.dev
 *
 */

/**
 * Class that handles licensing for EDD
 *
 * @package learndash-gift-edd/include/
 * @author  MissionLab
 */
class GiftEddLicense {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	protected static $_prefix = 'learndash-gift-edd';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'activate_license' ), 100 );
		add_action( 'admin_init', array( $this, 'deactivate_license' ), 100 );
		add_action( 'admin_init', array( $this, 'check_license' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ), 50 );
		add_action( 'admin_init', array( $this, 'plugin_updater' ) );
		add_action( 'admin_notices', array( $this, 'license_admin_notice' ) );
	}


	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 * @since     1.0.0
	 *
	 */
	public static function get_instance() {

        // If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the plugin prefix
	 *
	 * @return string
	 *
	 */

	public static function get_prefix() {
		return self::$_prefix;
	}

	/**
	 * Initialize plugin license key option
	 */

	public function register_settings() {
		register_setting( 'learndash_edd_gift_options', self::$_prefix . '_license_key', array( $this, 'sanitize_license' ) );
    }

	/**
	 * Check if license key needs to be reactivate
	 */

	public function sanitize_license( $new ) {
		$old = get_option( self::$_prefix . '_license_key' );
		if ( $old && $old != $new ) {
			delete_option( self::$_prefix . '_license_key' ); // new license has been entered, so must reactivate
		}

		return $new;
	}

	/**
	 * Return the license key
	 *
	 * @return string
	 * @author Tanner Moushey
	 */
	public static function get_license_key() {
		return get_option( self::$_prefix . '_license_key' );
	}

	/**
	 * Return the license status
	 *
	 * @return string
	 * @author Tanner Moushey
	 */
	public static function get_license_status() {
		return trim( get_option( self::$_prefix . '_license_status' ) );
	}

	/**
	 * Handle License activation
	 */
	public function activate_license() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST[ self::$_prefix . '_license_activate' ]) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST[self::$_prefix . '_license_activate'], self::$_prefix . '_license_activate')) {
			return;
		}

		// retrieve the license from form or the database
        if ( isset( $_POST[self::$_prefix . '_license_key'] ) ) {
	        $license = $_POST[self::$_prefix . '_license_key'];
        } else {
	        $license = $this->get_license_key();
        }

        update_option( self::$_prefix . '_license_key', sanitize_text_field( $license ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => wp_strip_all_tags($license),
			'item_id'    => absint( LGE_ITEM_ID ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( LGE_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'missing' :
					case 'item_name_mismatch' :

						$message = __( 'This appears to be an invalid license.' );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}
		}

		delete_transient( self::$_prefix . '_license_check' );

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'options-general.php?page=' . LGE_SETTINGS_PAGE );
			$redirect = add_query_arg( array(
				'edd_ld_gift_activation' => 'false',
				'message'               => urlencode( $message )
			), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		update_option( self::$_prefix . '_license_status', $license_data->license );
		wp_redirect( admin_url( 'options-general.php?page=' . LGE_SETTINGS_PAGE ) );
		exit();
	}

	/**
	 * Handle License deactivation
	 */
	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST[ self::$_prefix . '_license_deactivate' ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST[self::$_prefix . '_deactivate_license'], self::$_prefix . '_deactivate_license')) {
		    return;
        }

		// retrieve the license from the database
		$license = $this->get_license_key();

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_id'    => absint( LGE_ITEM_ID ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( LGE_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			update_option( self::$_prefix . '_license_status', 'deactivated' );
			delete_transient( self::$_prefix . '_license_check' );
		}

		wp_redirect( admin_url( 'options-general.php?page=' . LGE_SETTINGS_PAGE ) );
		exit();
	}

	/**
	 * Check license
	 *
	 * @since       1.0.0
	 */
	public function check_license() {

		// Don't fire when saving settings
		if ( ! empty( $_POST[ self::$_prefix . '_nonce' ] ) ) {
			return;
		}

		$license = $this->get_license_key();
		$status  = $this->get_license_status();

		if ( $license && ! $status && ! get_transient( self::$_prefix . '_license_check' ) ) {

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => trim( $license ),
				'item_id'    => absint( LGE_ITEM_ID ),
				'url'        => home_url()
			);

			$response = wp_remote_post( LGE_STORE_URL, array(
				'timeout'   => 35,
				'sslverify' => false,
				'body'      => $api_params
			) );

			if ( is_wp_error( $response ) ) {
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$status = $license_data->license;

			update_option( self::$_prefix . '_license_status', $status );

			set_transient( self::$_prefix . '_license_check', $license_data->license, DAY_IN_SECONDS );

			if ( $status !== 'valid' ) {
				delete_option( self::$_prefix . '_license_status' );
			}
		}

	}

	/**
	 * Plugin Updater
	 */
	public function plugin_updater() {
		// load our custom updater
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( LEARNDASH_EDD_GIFT_PLUGIN_PATH . 'includes/updater.php' );
		}

		// retrieve our license key from the DB
		$license_key = $this->get_license_key();

		// setup the updater
		new \EDD_SL_Plugin_Updater( LGE_STORE_URL, LEARNDASH_EDD_GIFT_PLUGIN_FILEPATH, array(
				'version' => LEARNDASH_EDD_GIFT_PLUGIN_VERSION,    // current version number
				'license' => $license_key,     // license key (used get_option above to retrieve from DB)
				'item_id' => absint( LGE_ITEM_ID ),
				'author'  => 'Tanner Moushey'  // author of this plugin
			)
		);

	}

	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	public function license_admin_notice() {
		if ( isset( $_GET['edd_ld_gift_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['edd_ld_gift_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}

}