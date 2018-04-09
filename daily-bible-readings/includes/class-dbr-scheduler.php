<?php
/**
 * Handles scheduling the data sync events in which the plugin grabs
 * data from antiochian.org and puts it in the Wordpress database
 */
class DBR_Scheduler {
	const CRON_NAME = 'dbr_sync_data';
	const SINGLE_EVENT_NAME = 'dbr_sync_data_once';

	// The local time that the data should refresh based on the Wordpress
	// installation's currently selected time zone. Add 5 minutes so
	// we don't try to grab the data exactly at midnight in case antiochian.org
	// hasn't updated yet.
	const REFRESH_TIME_LOCAL = 'today 00:05:00'; 

	/**
	 * Set the callback function for the CRON jobs
	 *
	 * @param callable $actionToPerform The callback function
	 */
	public function register_data_sync_callback(callable $actionToPerform)
	{
		// Add hooks for cron job callbacks
		add_action(DBR_Scheduler::CRON_NAME, $actionToPerform);
		add_action(DBR_Scheduler::SINGLE_EVENT_NAME, $actionToPerform);
	}

	/**
	 * Schedule a recurring data retrieval to occur the NEXT time REFRESH_TIME_LOCAL occurs
	 * and daily thereafter
	 */
	public function schedule_recurring_data_sync() {
		if (!wp_next_scheduled(DBR_Scheduler::CRON_NAME)) {	
			$currentTimeTodayUTC = time();
			// Convert refresh local time to UTC time based on the Wordpress 
			// installation's currently selected time zone.
			$refreshTimeUTC = strtotime(DBR_Scheduler::REFRESH_TIME_LOCAL) - get_option('gmt_offset') * 60 * 60;

			// If the scheduled time is in the past, wp_schedule_event will fire immediately.
			// We don't want that.
			if ($refreshTimeUTC < $currentTimeTodayUTC) {
				$refreshTimeUTC += 24 * 60 * 60;
			}		

			// Schedule a recurring retrieval for each day at midnight local time based
			// on the Wordpress installation's currently selected time zone.
    		wp_schedule_event($refreshTimeUTC, 'daily', DBR_Scheduler::CRON_NAME);
		}
	}

	/**
	 * Schedule one antiochian.org data retrieval right now.
	 */
	public function schedule_single_data_sync() {
		if (!wp_next_scheduled(DBR_Scheduler::CRON_NAME)) {	
			wp_schedule_single_event(time(), DBR_Scheduler::SINGLE_EVENT_NAME);
		}
	}

	/**
	 * Unschedule all antiochian.org data retrieval jobs.
	 */
	public function unschedule_data_sync() {
		// Unschedule the cron jobs
		$timestamp = wp_next_scheduled(DBR_Scheduler::CRON_NAME);
		wp_unschedule_event($timestamp, DBR_Scheduler::CRON_NAME);

		$timestamp = wp_next_scheduled(DBR_Scheduler::SINGLE_EVENT_NAME);
		wp_unschedule_event($timestamp, DBR_Scheduler::SINGLE_EVENT_NAME);
	}
}