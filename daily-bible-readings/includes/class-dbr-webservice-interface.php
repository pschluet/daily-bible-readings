<?php
/** 
 * Interfaces with antiochian.org to get the reading data
 */
class DBR_WebServiceInterface {
	const DATA_SOURCE_URL = "http://antiochian-api-prod-wa.azurewebsites.net/api/data/RetrieveLiturgicalDaysRss";

	/**
	 * Get the readings data from the antiochian.org web-service
	 * 
	 * @return DBR_ReadingsDataModel All of the reading data.
	 */
	public function get_data() {
		$out = new DBR_ReadingsDataModel();

		// Grab the content from antiochian.org
		$xml = new SimpleXMLElement($this->get_data_from_source());
		$item = $xml->channel->item;

		// Set data model properties while sanitizing data from web service
		$out->set_date(sanitize_text_field($item->title));
		$out->set_fasting_text(sanitize_text_field($item->FastDesignation));

		// Parse the readings tags to account for multiple readings
		$out->set_readings($this->parse_readings($item));

		return $out;
	}

	/**
	 * Get all of the readings from the XML returned by the antiochian.org web-service
	 *
	 * @param SimpleXMLElement $xml the XML "item" tag data from antiochian.org
	 *
	 * @return array of DBR_Reading objects
	 */
	private function parse_readings(SimpleXMLElement $item) {
		$out = array();
		$reading = new DBR_Reading();

		// Set data model properties and sanitize data from web service
		foreach ($item->children() as $tag) {
			$tagName = $tag->getName();
			if (strpos($tagName,'Reading') !== false) {
				if (strpos($tagName,'Title') !== false) {					
					$reading->set_title(sanitize_text_field($item->$tagName));
				}
				elseif (strpos($tagName,'Teaser') !== false) {
					$reading->set_short_text(sanitize_text_field($item->$tagName));
				}
				elseif (strpos($tagName,'FullText') !== false) {
					$reading->set_full_text(sanitize_text_field($item->$tagName));
					$out[] = clone $reading;
				}
			}
		}

		return $out;
	}

	/**
	 * Query antiochian.org for the data
	 * 
	 * @return string the XML data from antiochian.org
	 */
	private function get_data_from_source() {
		return wp_remote_retrieve_body(wp_remote_get(DBR_WebServiceInterface::DATA_SOURCE_URL));
	}
}