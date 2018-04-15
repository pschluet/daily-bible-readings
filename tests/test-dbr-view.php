<?php
/**
 * Tests the DBR_View class
 *
 * Tests functions that render the content on the public-facing pages
 */
class DBR_ViewTest extends WP_UnitTestCase {

	private $model;
	private $controller;
	private $view;

	public function setUp() {
		parent::setUp();
		$this->model = new DBR_Model();
		$this->controller = new DBR_Controller($this->model);
		$this->view = new DBR_View($this->controller);

		$this->controller->sync_data();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test to make sure plugin defaults to all content if it is given an invalid 
	 * parameter ([daily-bible-readings parameter="argument"])
	 */
	public function test_show_all_content_if_invalid_shortcode_parameter() {
		$this->is_match_string_in_output_for_shortcode_parameter('dog', 'all', 'dbr_reading_title');
	}

	/**
	 * Show error on public-facing page if argument to shortcode is incorrect
	 * [daily-bible-readings parameter="argument"]
	 */
	public function test_show_error_if_invalid_shortcode_argument() {
		$this->is_match_string_in_output_for_shortcode_parameter('content', 'unicorn', 'dbr_shortcode_error');
	}

	/**
	 * Ensure that the correct content is being displayed based on the
	 * particular shortcode "content" argument that is passed in
	 */
	public function test_show_correct_content_if_valid_shortcode() {
		$params['all'] = 'dbr_reading_title';
		$params['date'] = 'dbr_date';
		$params['fasting'] = 'dbr_fast_rule';
		$params['readings'] = 'dbr_reading_title';

		foreach ($params as $argument => $matchString) {
			$this->is_match_string_in_output_for_shortcode_parameter('content', $argument, $matchString);
		}
	}

	/** 
	 * Given a particular shortcode parameter and argument pair, ensure that the 
	 * output contains the given string ($matchString)
	 * 
	 *     [daily-bible-readings parameter="argument"]
	 *
	 * @param string $param The shortcode parameter
	 * @param string $argument The shortcode argument
	 * @param string $matchString The string to look for in the HTML that will be
	 *     created as a result of this shortcode
	 */
	private function is_match_string_in_output_for_shortcode_parameter(string $param, string $argument, string $matchString) {

		// Build attributes array
		$attributes[$param] = $argument;

		$output = $this->view->shortcode_handler($attributes);

		$this->assertContains($matchString, $output);
	}
}
