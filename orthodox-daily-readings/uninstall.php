<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

include_once 'orthodox-daily-readings.php';

// Remove the plugin's data from the Wordpress options table
delete_option(ODR_LocalDataStoreInterface::DATA_KEY);
?>