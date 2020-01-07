<?php
/**
 * @package   learndash-gift-edd
 * @author    WisdmLabs <wisdmlabs@info.com>
 * @license   GPL-2.0+
 * @link      https://wisdmlabs.com
 * @copyright 2016 WisdmLabs or Company Name
 *
 * @wordpress-plugin
 * Plugin Name:       Gift LearnDash Courses
 * Description:       Gift LearnDash courses using EDD platform. Required LearnDash, EDD, and LearnDash EDD Integration.
 * Version:           1.0.0
 * Author:            Bloop Animation Studios
 * Text Domain:       learndash-gift-edd
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

/**
 *If this file is called directly, abort.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_PATH' ) ) {
	define( 'LEARNDASH_EDD_GIFT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_URL' ) ) {
	define( 'LEARNDASH_EDD_GIFT_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}
if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_BASENAME' ) ) {
	define( 'LEARNDASH_EDD_GIFT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'LEARNDASH_EDD_GIFT_PLUGIN_FILEPATH' ) ) {
	define( 'LEARNDASH_EDD_GIFT_PLUGIN_FILEPATH', __FILE__ );
}

/**
 * Include routing file.
 */

include_once( plugin_dir_path( __FILE__ ) . 'includes/class-learndasheddgift.php' );

/**
 * Main instance of LearndashEddGift.
 *
 * Returns the main instance of learndashEddGiftClassInstance to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return LearndashEddGift
 */

function learndash_edd_gift_class_instance() {
	return LearndashEddGift::instance();
}

// Global for backwards compatibility.
$GLOBALS['LearndashEddGift'] = learndash_edd_gift_class_instance();
