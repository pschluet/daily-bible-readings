<?php
/**
 * Tests DBR_Scheduler Functionality
 *
 * Tests related to scheduling WordPress cron jobs.
 */
class DBR_SchedulerTest extends WP_UnitTestCase {

	private $scheduler;

	public function setUp() {
		parent::setUp();
		$this->scheduler = new DBR_Scheduler();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test to make sure registration of cron callbacks adds WordPress
	 * action hooks.
	 */
	public function test_that_cron_registration_adds_wp_actions() {
		$callback = array($this, 'sample_function');
		$this->scheduler->register_data_sync_callback($callback);

		$this->assertTrue(has_action(DBR_Scheduler::CRON_NAME, $callback) != False && has_action(DBR_Scheduler::SINGLE_EVENT_NAME, $callback) != False);
	}

	/**
	 * Test to make sure the recurring cron job is unscheduledscheduled
	 */
	public function test_that_recurring_cron_is_unscheduled() {
		$this->assert_that_recurring_cron_is_scheduled();
		$this->scheduler->unschedule_data_sync();

		$cronNames = $this->get_scheduled_cron_names();
		$this->assertNotContains(DBR_Scheduler::CRON_NAME, $cronNames);
	}

	/**
	 * Test to make sure the recurring cron job is scheduled
	 */
	public function assert_that_recurring_cron_is_scheduled() {
		$this->scheduler->schedule_recurring_data_sync();

		$cronNames = $this->get_scheduled_cron_names();
		$this->assertContains(DBR_Scheduler::CRON_NAME, $cronNames);
	}

	/**
	 * Placeholder method for passing to the following test:
	 * test_that_cron_registration_adds_wp_actions
	 */
	public function sample_function() {
		// Does nothing
	}

	/**
	 * Get the names of the WP CRON jobs that are currently scheduled
	 *
	 * @return array An array of strings (names of the the jobs)
	 */
	private function get_scheduled_cron_names() {
		$crons = _get_cron_array();
		$names = array();

		foreach ($crons as $time => $cron) {
			$names = array_merge($names, array_keys($cron));
		}

		return $names;
	}
}
