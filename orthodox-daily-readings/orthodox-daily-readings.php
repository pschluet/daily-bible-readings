<?php
/*
Plugin Name: Orthodox Daily Readings
Plugin URI: https://github.com/pschluet/orthodox-daily-readings
Description: This plugin allows you to post the current day's readings and fasting rule from antiochian.org on your own website.
Author: Paul Schlueter
Version: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: https://github.com/pschluet/
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'ODR_VERSION_NUMBER', '1.0.0' );

/**
 * Represents a single reading
 */
class ODR_Reading {
	private $title;
	private $shortText;
	private $fullText;

	/**
	 * Get the full text of the bible reading.
	 * 
	 * @return string the text of the bible reading
	 */
	public function get_full_text() {
		return $this->fullText;
	}

	/**
	 * Get the title of the bible reading.
	 * 
	 * @return string the title of the bible reading
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the short "teaser" text of the bible reading.
	 * 
	 * @return string the short "teaser" text of the bible reading
	 */
	public function get_short_text() {
		return $this->shortText;
	}

	/**
	 * Set the full text of the bible reading.
	 *
	 * @param string $value The full text of the bible reading
	 */
	public function set_full_text(string $value) {
		$this->fullText = $value;
	}

	/**
	 * Set the short "teaser" text of the bible reading.
	 *
	 * @param string $value The short "teaser" text of the bible reading
	 */
	public function set_short_text(string $value) {
		$this->shortText = $value;
	}

	/**
	 * Set the title of the bible reading.
	 *
	 * @param string $value The title of the bible reading
	 */
	public function set_title(string $value) {
		$this->title = $value;
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
	 * Get the date of the readings.
	 * 
	 * @return string The date of the readings
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * Set the date of the readings.
	 *
	 * @param string $value The date of the readings
	 */
	public function set_date(string $value) {
		$this->date = $value;
	}

	/**
	 * Get the readings.
	 * 
	 * @return array An array of ODR_Reading items
	 */
	public function get_readings() {
		return $this->readings;
	}

	/**
	 * Set the bible readings.
	 *
	 * @param array $value An array of ODR_Reading items
	 */
	public function set_readings(array $value) {
		$this->readings = $value;
	}

	/**
	 * Get the fasting rule text.
	 * 
	 * @return string the fasting rule text
	 */
	public function get_fasting_text() {
		return $this->fastingText;
	}

	/**
	 * Set the fasting rule text.
	 *
	 * @param string $value The fasting rule text
	 */
	public function set_fasting_text(string $value) {
		$this->fastingText = $value;
	}
}

/**
 * Handles activation and deactivation of the plugin
 */
class ODR_ActivationHandler {
	const SCRIPT_NAME = 'my_javascript';
	const READMORE_JS_LIB = 'readmore_lib';

	/**
	 * Constructor.
	 * 
	 * @return ODR_ActivationHandler
	 */
	public function __construct() {
		register_activation_hook(__FILE__, array(__CLASS__, 'on_activate'));
		register_deactivation_hook(__FILE__, array(__CLASS__, 'on_deactivate'));

		// Add the hook for the cron job callback
		ODR_Scheduler::add_action_hooks();

		// Add the hook for javascript for dynamic expanding/contracting of reading text
		add_action('wp_enqueue_scripts', array(__CLASS__, 'setup_javascript'));

	}

	/**
	 * Register the javascript files needed for the plugin.
	 */
	public static function setup_javascript() {
		// Register javascript scripts for dynamic expanding/contracting of reading text
		wp_register_script(ODR_ActivationHandler::SCRIPT_NAME, plugins_url('public/js/scripts.js', __FILE__), array('jquery'), ODR_VERSION_NUMBER);
		wp_enqueue_script(ODR_ActivationHandler::SCRIPT_NAME);

		wp_register_script(ODR_ActivationHandler::READMORE_JS_LIB, plugins_url('public/js/readmore_v2.2.0.min.js', __FILE__), array(), ODR_VERSION_NUMBER);
		wp_enqueue_script(ODR_ActivationHandler::READMORE_JS_LIB);
	}

	/**
	 * Runs on plugin activation.
	 *
	 * Get data from antiochian.org immediately on activation and schedule recurring retrieval.
	 */
	public static function on_activate() {
		// Fetch data from antiochian.org right now
		ODR_Scheduler::schedule_single_data_sync();

		// Schedule the cron job for getting data from antiochian.org daily
		ODR_Scheduler::schedule_recurring_data_sync();		
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * Unschedule recurring data retrieval from antiochian.org.
	 */
	public static function on_deactivate() {
		ODR_Scheduler::unschedule_data_sync();
	}
}

/**
 * Handles scheduling the data sync events in which the plugin grabs
 * data from antiochian.org and puts it in the Wordpress database
 */
class ODR_Scheduler {
	const CRON_NAME = 'odr_sync_data';
	const SINGLE_EVENT_NAME = 'odr_sync_data_once';

	// The local time that the data should refresh based on the Wordpress
	// installation's currently selected time zone. Add 5 minutes so
	// we don't try to grab the data exactly at midnight in case antiochian.org
	// hasn't updated yet.
	const REFRESH_TIME_LOCAL = 'today 00:05:00'; 

	/**
	 * Add the Wordpress action hooks for the cron job callbacks
	 */
	public static function add_action_hooks() {
		add_action(ODR_Scheduler::CRON_NAME, 'ODR_LocalDataStoreInterface::sync_data');
		add_action(ODR_Scheduler::SINGLE_EVENT_NAME, 'ODR_LocalDataStoreInterface::sync_data');
	}

	/**
	 * Schedule a recurring data retrieval to occur the NEXT time REFRESH_TIME_LOCAL occurs
	 * and daily thereafter
	 */
	public static function schedule_recurring_data_sync() {
		if (!wp_next_scheduled(ODR_Scheduler::CRON_NAME)) {	
			$currentTimeTodayUTC = time();
			// Convert refresh local time to UTC time based on the Wordpress 
			// installation's currently selected time zone.
			$refreshTimeUTC = strtotime(ODR_Scheduler::REFRESH_TIME_LOCAL) - get_option('gmt_offset') * 60 * 60;

			// If the scheduled time is in the past, wp_schedule_event will fire immediately.
			// We don't want that.
			if ($refreshTimeUTC < $currentTimeTodayUTC) {
				$refreshTimeUTC += 24 * 60 * 60;
			}		

			// Schedule a recurring retrieval for each day at midnight local time based
			// on the Wordpress installation's currently selected time zone.
    		wp_schedule_event($refreshTimeUTC, 'daily', ODR_Scheduler::CRON_NAME);
		}
	}

	/**
	 * Schedule one antiochian.org data retrieval right now.
	 */
	public static function schedule_single_data_sync() {
		if (!wp_next_scheduled(ODR_Scheduler::CRON_NAME)) {	
			wp_schedule_single_event(time(), ODR_Scheduler::SINGLE_EVENT_NAME);
		}
	}

	/**
	 * Unschedule all antiochian.org data retrieval jobs.
	 */
	public static function unschedule_data_sync() {
		// Unschedule the cron jobs
		$timestamp = wp_next_scheduled(ODR_Scheduler::CRON_NAME);
		wp_unschedule_event($timestamp, ODR_Scheduler::CRON_NAME);

		$timestamp = wp_next_scheduled(ODR_Scheduler::SINGLE_EVENT_NAME);
		wp_unschedule_event($timestamp, ODR_Scheduler::SINGLE_EVENT_NAME);
	}
}

/**
 * Handles display of the readings via shortcode
 */
class ODR_View {
	/**
	 * Constructor
	 *
	 * @return ODR_View the view
	 */
	public function __construct() {

		// Register shortcode
		add_shortcode('orthodox-daily-readings', array($this, 'shortcode_handler'));
	}

	/**
	 * Handles parsing of the user shortcodes
	 *
	 * @return string The rendered html content for the shortcode
	 */
	public function shortcode_handler($atts = []) {
		// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);

		// override default attributes with user attributes
    	$ord_atts = shortcode_atts(
			array(
				'content' => 'all',
			),
			$atts);

    	// Render view based on which shortcode argument was passed in
    	switch (strtolower($ord_atts['content'])) {
    		case 'all':
    			return ODR_View::get_readings_all_display();
    		case 'date':
    			return ODR_View::get_date_display();
    		case 'fasting':
    			return ODR_View::get_fast_rule_display();
    		case 'readings':
    			return ODR_View::get_readings_text_display();
    		default:
    			return '<div class="odr_shortcode_error"><h5>Orthodox Daily Readings Plugin Error</h5> <p>[orthodox-daily-readings content="' . 
    				esc_html($ord_atts['content']) . '"] is not a valid shortcode. "' .  esc_html($ord_atts['content']) . 
    				'" is an invalid content argument. Acceptable values are "all", "date", "fasting", or "readings". ' .
    				'For example, the following is valid: ' . '[orthodox-daily-readings content="all"]</p></div>';
    	}
	}

	/**
	 * Create the HTML content to display the date
	 *
	 * @return string The rendered html content for the date
	 */
	public function get_date_display() {
		$data = ODR_LocalDataStoreInterface::get_data();
		$dateText = ucwords(strtolower(esc_html($data->get_date())));

		// Strip out the year
		$tokens = explode(',', $dateText);
		return '<h2 class="odr_date">' . $tokens[0] . $tokens[1] . '</h2>';
	}

	/**
	 * Create the HTML content to display the fasting rule
	 *
	 * @return string The rendered html content for the fasting rule
	 */
	public function get_fast_rule_display() {
		$data = ODR_LocalDataStoreInterface::get_data();
		return '<div class="odr_fast_rule">' . ucwords(strtolower(esc_html($data->get_fasting_text()))) . '</div>';
	}

	/**
	 * Create the HTML content to display the full readings with titles
	 *
	 * @return string The rendered html content for the full readings with titles
	 */
	public function get_readings_text_display() {
		$data = ODR_LocalDataStoreInterface::get_data();
		$out = '';
		foreach ($data->get_readings() as $reading) {
			$out .= '<h3 class="odr_reading_title">' . ucwords(strtolower(esc_html($reading->get_title()))) . '</h3>' .
			     '<p class="odr_reading_text">' . esc_html($reading->get_full_text()) . '</p>';
		}
		return $out;
	}

	/**
	 * Create the HTML content to display all display components together
	 *
	 * @return string The rendered html content for all display components together
	 */
	public function get_readings_all_display() {
		return ODR_View::get_date_display() . ODR_View::get_fast_rule_display() . ODR_View::get_readings_text_display();
	}
}

/** 
 * Interfaces with the Wordpress database
 */
class ODR_LocalDataStoreInterface {
	const DATA_KEY = "odr_daily_readings_data";

	/**
	 * Get the data from antiochian.org and store it in the Wordpress database
	 */
	public static function sync_data() {
		// Get data from antiochian.org
		$data = ODR_DataSourceInterface::get_data();

		// Store it in our database
		update_option(ODR_LocalDataStoreInterface::DATA_KEY, $data);
	}

	/**
	 * Retrieve the readings data from the database
	 *
	 * @return ODR_ReadingsDataModel the reading data
	 */
	public static function get_data() {
		return get_option(ODR_LocalDataStoreInterface::DATA_KEY);
	}
}

/** 
 * Interfaces with antiochian.org to get the reading data
 */
class ODR_DataSourceInterface {
	const DATA_SOURCE_URL = "http://antiochian-api-prod-wa.azurewebsites.net/api/data/RetrieveLiturgicalDaysRss";

	/**
	 * Get the readings data from the antiochian.org web-service
	 * 
	 * @return ODR_ReadingsDataModel All of the reading data.
	 */
	public static function get_data() {
		$out = new ODR_ReadingsDataModel();

		// Grab the content from antiochian.org
		$xml = new SimpleXMLElement(ODR_DataSourceInterface::get_data_from_source());
		$item = $xml->channel->item;

		// Set data model properties while sanitizing data from web service
		$out->set_date(sanitize_text_field($item->title));
		$out->set_fasting_text(sanitize_text_field($item->FastDesignation));

		// Parse the readings tags to account for multiple readings
		$out->set_readings(ODR_DataSourceInterface::parse_readings($item));

		return $out;
	}

	/**
	 * Get all of the readings from the XML returned by the antiochian.org web-service
	 *
	 * @param SimpleXMLElement $xml the XML "item" tag data from antiochian.org
	 *
	 * @return array of ODR_Reading objects
	 */
	private static function parse_readings(SimpleXMLElement $item) {
		$out = array();
		$reading = new ODR_Reading();

		// Set data model properties and sanitize data from web service
		foreach ($item->children() as $tag) {
			$tagName = $tag->getName();
			if (strpos($tagName,'Reading') !== false) {
				if (strpos($tagName,'Title') !== false) {					
					$reading->set_title(sanitize_text_field($item->$tagName));
				}
				elseif (strpos($tagName,'Teaser') !== false) {
					$reading->set_short_text(sanitize_text_field($item->$tagName));
				}
				elseif (strpos($tagName,'FullText') !== false) {
					$reading->set_full_text(sanitize_text_field($item->$tagName));
					$out[] = clone $reading;
				}
			}
		}

		return $out;
	}

	/**
	 * Query antiochian.org for the data
	 * 
	 * @return string the XML data from antiochian.org
	 */
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
