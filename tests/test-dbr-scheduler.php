<?php
/**
 * Tests DBR_Scheduler Functionality
 *
 * Tests related to scheduling WordPress cron jobs.
 */
class DBR_SchedulerTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test to make sure the recurring cron job is scheduled
	 */
	public function test_that_recurring_cron_is_scheduled() {
		$scheduler = new DBR_Scheduler();
		$scheduler->schedule_recurring_data_sync();

		$cronNames = $this->get_scheduled_cron_names();
		$this->assertContains(DBR_Scheduler::CRON_NAME, $cronNames);
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
