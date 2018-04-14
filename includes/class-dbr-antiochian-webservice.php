<?php
/** 
 * Interfaces with antiochian.org to get the reading data
 */
class DBR_AntiochianWebService implements DBR_iWebServiceDataSource {
	const ITEM_ID_URL = "http://antiochian-api-prod-wa.azurewebsites.net/api/data/RetrieveEnabledDates";
	const CONTENT_URL = "http://antiochian-api-prod-wa.azurewebsites.net/api/data/RetrieveLiturgicDayByItemId?itemId=";

	/**
	 * Get the readings data from the antiochian.org web-service
	 * 
	 * @param DateTime $date The date that you want to get the readings data for
	 *
	 * @return DBR_ReadingsDataModel All of the reading data.
	 */
	public function get_data_for_date(DateTime $date) {
		$out = new DBR_ReadingsDataModel();

		// Grab the content from antiochian.org
		$itemID = $this->get_itemID_for_date($date);
		$json = wp_remote_retrieve_body(wp_remote_get(DBR_AntiochianWebService::CONTENT_URL . $itemID));
		$data = json_decode($json, true);

		$liturgicalDay = $data['LiturgicalDay'];

		// Set data model properties while sanitizing data from web service
		$readingDate = DateTime::createFromFormat('Y-m-d', sanitize_text_field($liturgicalDay['OriginalCalendarDate']));
		$out->set_date($readingDate);
		$out->set_fasting_text(sanitize_text_field($liturgicalDay['FastDesignation']));

		// Parse the readings tags to account for multiple readings
		$out->set_readings($this->parse_readings($liturgicalDay));

		return $out;
	}

	/**
	 * In order to get the readings data, you have to determine the ItemID that the
	 * desired date corresponds to. This uses one of antiochian.org's API endpoints
	 * to determine the ItemID given a date.
	 * 
	 * @param DateTime $date The date that you want to get the readings data for
	 *
	 * @return int The ItemID that corresponds with the date
	 */
	private function get_itemID_for_date(DateTime $date) {
		// Get date/itemid lookup table from web service
		$json = wp_remote_retrieve_body(wp_remote_get(DBR_AntiochianWebService::ITEM_ID_URL));
		$data = json_decode($json);

		// Get the itemID that matches the desired date
		$itemID = 0;
		$desiredYear = $date->format('Y') + 0;
		$desiredMonth = $date->format('m') + 0;
		$desiredDay = $date->format('d') + 0;
		foreach ($data as $enabledDate) {
			$lookupYear = $enabledDate->year + 0;
			$lookupMonth = $enabledDate->month + 0;
			$lookupDay = $enabledDate->day + 0;
			if ($desiredYear == $lookupYear and $desiredMonth == $lookupMonth and $desiredDay == $lookupDay) {
				$itemID = $enabledDate->ItemId + 0; 
				break;
			} 
		}

		return $itemID;
	}

	/**
	 * Get all of the readings from the JSON returned by the antiochian.org web-service
	 *
	 * @param array $liturgicalDay An associative array with the readings data
	 *
	 * @return array of DBR_Reading objects
	 */
	private function parse_readings(array $liturgicalDay) {
		$out = array();
		$reading = new DBR_Reading();

		// Set data model properties and sanitize data from web service
		foreach ($liturgicalDay as $name => $value) {
			if (strpos($name,'Reading') !== false and $value !== '') {
				if (strpos($name,'Title') !== false and $value !== '') {					
					$reading->set_title(sanitize_text_field($value));
				}
				elseif (strpos($name,'Teaser') !== false and $value !== '') {
					$reading->set_short_text(sanitize_text_field($value));
				}
				elseif (strpos($name,'FullText') !== false and $value !== '') {
					$reading->set_full_text(sanitize_text_field($value));
					$out[] = clone $reading;
				}
			}
		}

		return $out;
	}
}