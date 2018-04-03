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
	private $shortText;
	private $fullText;

	/**
	 * Constructor
	 * @param string $title the title for the reading
	 * @param string $shortText the preview text for the reading
	 * @param string $fullText the full text of the reading
	 * @return ODR_Reading
	 */
	public function __construct(string $title, string $shortText, string $fullText) {
		$this->title = $title;
		$this->shortText = $shortText;
		$this->fullText = $fullText;
	}

	public function get_full_text() {
		return $this->fullText;
	}

	public function get_title() {
		return $this->title;
	}

	public function get_short_text() {
		return $this->shortText;
	}
}

/**
 * Class to hold all of the readings data
 */
class ODR_ReadingsDataModel {
	private $date;
	private $readings;
	private $fastingText;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	public function get_date() {
		return $this->date;
	}

	public function set_date($value) {
		$this->date = $value;
	}

	public function get_readings() {
		return $this->readings;
	}

	public function set_readings($value) {
		$this->readings = $value;
	}

	public function get_fasting_text() {
		return $this->fastingText;
	}

	public function set_fasting_text($value) {
		$this->fastingText = $value;
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

/**
 * Handles display of the readings via shortlink
 */
class ODR_View {
	/**
	 * Constructor
	 * @return ODR_View the view
	 */
	public function __construct() {

		// Register shortcodes
		add_shortcode('daily_readings_teaser', array($this, 'get_teaser_display'));
		add_shortcode('daily_readings_full', array($this, 'get_full_display'));
	}

	public function get_teaser_display() {
		$data = ODR_DataSourceInterface::get_data();

		echo "<div>" . $data->get_date() . "</div>" .
		     "<div>" . $data->get_fasting_text() . "</div>";

		foreach ($data->get_readings() as $reading) {
			echo "<div>" . $reading->get_title() . "</div>" .
			     "<div>" . $reading->get_short_text() . "</div>";
		}
	}

	public function get_full_display() {
		$data = ODR_DataSourceInterface::get_data();

		echo "<div>" . $data->get_date() . "</div>" .
		     "<div>" . $data->get_fasting_text() . "</div>";

		foreach ($data->get_readings() as $reading) {
			echo "<div>" . $reading->get_title() . "</div>" .
			     "<div>" . $reading->get_full_text() . "</div>";
		}
	}
}

/** 
 * Interfaces with antiochian.org to get the reading data
 */
class ODR_DataSourceInterface {
	private const DATA_SOURCE_URL = "http://antiochian-api-prod-wa.azurewebsites.net/api/data/RetrieveLiturgicalDaysRss";

	public static function get_data() {
		$readings = array(new ODR_Reading("title1", "shortText1", "fullText1"),
			new ODR_Reading("title2", "shortText2", "fullText2"),
			new ODR_Reading("title3", "shortText3", "fullText3"));

		$out = new ODR_ReadingsDataModel();

		$xml = simplexml_load_string(ODR_DataSourceInterface::get_data_from_source());
		$item = $xml->channel->item;

		$out->set_date($item->title);
		$out->set_fasting_text($item->FastDesignation);
		$out->set_readings($readings);

		return $out;
	}

	private static function get_data_from_source() {
		$curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, ODR_DataSourceInterface::DATA_SOURCE_URL);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	$data = curl_exec($curl);
    	curl_close($curl);
    	return $data;
	}
}

// Instantiate the activation/deactivation handler
$activator = new ODR_ActivationHandler();

// Instantiate the view
$view = new ODR_View();
?>
