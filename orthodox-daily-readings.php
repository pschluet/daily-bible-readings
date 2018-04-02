<?php
/*
Plugin Name: Orthodox Daily Readings
Plugin URI: http://paulschlueter.com
Description: This plugin allows you to post the current day's readings and fasting rule from antiochian.org on your own website.
Author: Paul Schlueter
Version: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://paulschlueter.com
*/

/**
 * Represents a single reading
 */
class ODR_Reading {
	private $title;
	private $text;

	public function __construct(string $title, string $text) {
		$this->title = $title;
		$this->text = $text;
	}

	public function get_text() {
		return $this->text;
	}

	public function get_title() {
		return $this->title;
	}
}

/**
 * Class to hold all of the readings data
 */
class ODR_ReadingsData {
	private $date;
	private $readings;
	private $fastingText;

	/**
	 * Constructor
	 * @param string $date the date for these readings
	 * @param array $readings an array of ODR_Reading objects (the actual readings)
	 * @param string $fastingText the text with the fasting rule for the day
	 * @return ODR_ReadingsData
	 */
	public function __construct(string $date, array $readings, string $fastingText) {
		$this->date = $date;
		$this->readings = $readings;
		$this->fastingText = $fastingText;
	}
}

/**
 * Handles activation and deactivation of the plugin
 */
class ODR_ActivationHandler {

	/**
	 * Constructor
	 * @return ODR_ActivationHandler
	 */
	public function __construct() {
		register_activation_hook(__FILE__, 'ODR_ActivationHandler::on_activate');
		register_deactivation_hook(__FILE__, 'ODR_ActivationHandler::on_deactivate');
	}

	public static function on_activate() {
		// Add options to the database if they don't already exist
		update_option('odr_readings', 0);
	}

	public static function on_deactivate() {
		// Do nothing
	}
}

// function odr_links() {
// 	echo "<div>These are the links.</div>";
// }

// add_shortcode('daily_readings_links', 'odr_links');

// function odr_page() {
// 	echo "<div>This is the page.</div>";
// }

// add_shortcode('daily_readings_page', 'odr_page');

// Instantiate the activation/deactivation handler
$activator = new ODR_ActivationHandler();
?>
