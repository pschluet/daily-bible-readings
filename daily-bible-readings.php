<?php
/*
Plugin Name: Daily Bible Readings
Plugin URI: https://github.com/pschluet/daily-bible-readings
Description: This plugin allows you to post the current day's readings and fasting rule from antiochian.org on your own website.
Author: Paul Schlueter
Version: 1.0.2
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
define( 'DBR_VERSION_NUMBER', '1.0.2' );

/**
 * Handles activation/deactivation and communicating between
 * model and view
 */
class DBR_Controller {
	const SCRIPT_NAME = 'my_javascript';
	const READMORE_JS_LIB = 'readmore_lib';

	private $scheduler;
	private $webServiceInterface;
	private $model;

	/**
	 * Constructor
	 *
	 * Register hooks and actions
	 *
	 * @param DBR_Model $model The "model" of the MVC paradaigm
	 */
	public function __construct(DBR_Model $model) {
		$this->model = $model;
		$this->scheduler = new DBR_Scheduler();
		$this->webServiceInterface = new DBR_AntiochianWebService();

		// Register activation/deactivation hooks
		register_activation_hook(__FILE__, array($this, 'on_activate'));
		register_deactivation_hook(__FILE__, array($this, 'on_deactivate'));

		// Add the hook for javascript for dynamic expanding/contracting of reading text
		add_action('wp_enqueue_scripts', array($this, 'setup_javascript'));

		// Add hooks for the data sync callbacks
		$this->scheduler->register_data_sync_callback(array($this, 'sync_data'));
	}

	/**
	 * Load all the dependencies
	 */
	public static function load_dependencies() {
		require_once 'includes/class-dbr-model.php';
		require_once 'includes/class-dbr-public-view.php';
		require_once 'includes/class-dbr-reading.php';
		require_once 'includes/class-dbr-readings-data-model.php';
		require_once 'includes/class-dbr-scheduler.php';
		require_once 'includes/interface-dbr-iWebServiceDataSource.php';
		require_once 'includes/class-dbr-antiochian-webservice.php';	
		require_once 'includes/class-dbr-admin-view.php';
	}

	/**
	 * Register the javascript files needed for the plugin.
	 */
	public function setup_javascript() {
		// Register javascript scripts for dynamic expanding/contracting of reading text
		wp_register_script(DBR_Controller::SCRIPT_NAME, plugins_url('public/js/scripts.js', __FILE__), array('jquery'), DBR_VERSION_NUMBER);
		wp_enqueue_script(DBR_Controller::SCRIPT_NAME);

		wp_register_script(DBR_Controller::READMORE_JS_LIB, plugins_url('public/js/readmore_v2.2.0.min.js', __FILE__), array(), DBR_VERSION_NUMBER);
		wp_enqueue_script(DBR_Controller::READMORE_JS_LIB);
	}

	/**
	 * Runs on plugin activation.
	 *
	 * Get data from antiochian.org immediately on activation and schedule recurring retrieval.
	 */
	public function on_activate() {
		// Fetch data from antiochian.org right now
		$this->scheduler->schedule_single_data_sync();

		// Schedule the cron job for getting data from antiochian.org daily
		$this->scheduler->schedule_recurring_data_sync();		
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * Unschedule recurring data retrieval from antiochian.org.
	 */
	public function on_deactivate() {
		$this->scheduler->unschedule_data_sync();
	}

	/**
	 * Get the data from antiochian.org and store it in the Wordpress database
	 */
	public function sync_data() {
		// Get data from antiochian.org
		$currentLocalTime = new DateTime(current_time('Y-m-d'));
		$data = $this->webServiceInterface->get_data_for_date($currentLocalTime);

		// Store it in our database
		$this->model->set_data($data);
	}

	/**
	 * Get readings data from the model
	 *
	 * @return DBR_ReadingsDataModel The readings data
	 */
	public function get_data() {
		return $this->model->get_data();
	}
}

DBR_Controller::load_dependencies();

$adminView = new DBR_Admin_View();

// Instantiate the model, view, and controller
$model = new DBR_Model();
$controller = new DBR_Controller($model);
$view = new DBR_View($controller);
?>
