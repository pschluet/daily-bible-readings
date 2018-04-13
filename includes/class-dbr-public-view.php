<?php
/**
 * Handles display of the readings via shortcode
 */
class DBR_View {
	private $controller;
	/**
	 * Constructor
	 *
	 * @param DBR_Controller $controller The controller for this view
	 *
	 * @return DBR_View the view
	 */
	public function __construct(DBR_Controller $controller) {
		$this->controller = $controller;

		// Register shortcode
		add_shortcode('daily-bible-readings', array($this, 'shortcode_handler'));
	}

	/**
	 * Handles parsing of the user shortcodes
	 *
	 * @return string The rendered html content for the shortcode
	 */
	public function shortcode_handler($atts = []) {
		// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);

		// override default attributes with user attributes
    	$ord_atts = shortcode_atts(
			array(
				'content' => 'all',
			),
			$atts);

    	// Render view based on which shortcode argument was passed in
    	switch (strtolower($ord_atts['content'])) {
    		case 'all':
    			return $this->get_readings_all_display();
    		case 'date':
    			return $this->get_date_display();
    		case 'fasting':
    			return $this->get_fast_rule_display();
    		case 'readings':
    			return $this->get_readings_text_display();
    		default:
    			return '<div class="dbr_shortcode_error"><h5>Orthodox Daily Readings Plugin Error</h5> <p>[orthodox-daily-readings content="' . 
    				esc_html($ord_atts['content']) . '"] is not a valid shortcode. "' .  esc_html($ord_atts['content']) . 
    				'" is an invalid content argument. Acceptable values are "all", "date", "fasting", or "readings". ' .
    				'For example, the following is valid: ' . '[orthodox-daily-readings content="all"]</p></div>';
    	}
	}

	/**
	 * Create the HTML content to display the date
	 *
	 * @return string The rendered html content for the date
	 */
	public function get_date_display() {
		$data = $this->controller->get_data();
		$dateText = ucwords(strtolower(esc_html($data->get_date())));

		// Strip out the year
		$tokens = explode(',', $dateText);
		return '<h2 class="dbr_date">' . $tokens[0] . $tokens[1] . '</h2>';
	}

	/**
	 * Create the HTML content to display the fasting rule
	 *
	 * @return string The rendered html content for the fasting rule
	 */
	public function get_fast_rule_display() {
		$data = $this->controller->get_data();
		return '<div class="dbr_fast_rule">' . ucwords(strtolower(esc_html($data->get_fasting_text()))) . '</div>';
	}

	/**
	 * Create the HTML content to display the full readings with titles
	 *
	 * @return string The rendered html content for the full readings with titles
	 */
	public function get_readings_text_display() {
		$data = $this->controller->get_data();
		$out = '';
		foreach ($data->get_readings() as $reading) {
			$out .= '<h3 class="dbr_reading_title">' . ucwords(strtolower(esc_html($reading->get_title()))) . '</h3>' .
			     '<p class="dbr_reading_text">' . esc_html($reading->get_full_text()) . '</p>';
		}
		return $out;
	}

	/**
	 * Create the HTML content to display all display components together
	 *
	 * @return string The rendered html content for all display components together
	 */
	public function get_readings_all_display() {
		return $this->get_date_display() . $this->get_fast_rule_display() . $this->get_readings_text_display();
	}
}
?>