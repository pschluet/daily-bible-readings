<?php
interface DBR_iWebServiceDataSource
{
	/**
	 * All web service data sources must implement this interface
	 * @param DateTime $date The date for which we want to get the readings data
	 *
	 * @return DBR_ReadingsDataModel The readings data
	 */
	public function get_data_for_date(DateTime $date);
}
