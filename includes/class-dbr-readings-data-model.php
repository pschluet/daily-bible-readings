<?php
/**
 * Class to hold all of the readings data
 */
class DBR_ReadingsDataModel {
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
	 * @return array An array of DBR_Reading items
	 */
	public function get_readings() {
		return $this->readings;
	}

	/**
	 * Set the bible readings.
	 *
	 * @param array $value An array of DBR_Reading items
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