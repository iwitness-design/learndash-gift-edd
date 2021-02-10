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


	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

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

}