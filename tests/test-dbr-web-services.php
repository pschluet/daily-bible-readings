<?php
/**
 * Tests classes that implement DBR_iWebServiceDataSource
 *
 * Tests functions that grab data from 3rd party APIs
 */
class DBR_iWebServiceDataSourceTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Grab data from the antiochian.org web service
	 */
	public function test_antiochian_readings_data_date_should_match_desired_date() {
		$webService = new DBR_AntiochianWebService();

		$currentLocalTime = new DateTime(current_time('Y-m-d'));
		$readingsData = $webService->get_data_for_date($currentLocalTime);

		// Make sure readings date is equal to the desired date
		$this->assertEquals($currentLocalTime->format('Y-m-d'), $readingsData->get_date()->format('Y-m-d'));
	}
}
