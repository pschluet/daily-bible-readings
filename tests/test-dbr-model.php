<?php
/**
 * Tests DBR_Model Functionality
 *
 * Tests related to interacting with the Wordpress database.
 */
class DBR_Model_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Add data to the database and make sure we can read it back
	 */
	function test_set_and_get_model_data() {
		$inputData = "my_data";

		$readingData = new DBR_ReadingsDataModel();
		$readingData->set_fasting_text($inputData);

		// Put data into the database
		$model = new DBR_Model();
		$model->set_data($readingData);

		// Read it back out
		$outputData = $model->get_data()->get_fasting_text();

		$this->assertEquals($inputData, $outputData);
	}
}
