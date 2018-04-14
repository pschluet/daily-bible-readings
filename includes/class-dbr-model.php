<?php
/** 
 * Interfaces with the Wordpress database
 */
class DBR_Model {
	const DATA_KEY = "dbr_daily_readings_data";

	/**
	 * Save the readings data in the database
	 *
	 * @param DBR_ReadingsDataModel $value The reading data
	 */
	public function set_data(DBR_ReadingsDataModel $value) {
		// Store it in our database
		update_option(DBR_Model::DATA_KEY, $value);
	}

	/**
	 * Retrieve the readings data from the database
	 *
	 * @return DBR_ReadingsDataModel the reading data
	 */
	public function get_data() {
		return get_option(DBR_Model::DATA_KEY);
	}
}