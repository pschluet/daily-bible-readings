<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

include_once 'daily-bible-readings.php';

// Remove the plugin's data from the Wordpress options table
delete_option(DBR_Model::DATA_KEY);
?>