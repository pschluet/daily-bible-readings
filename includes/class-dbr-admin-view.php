<?php
/**
 * Handles display of the readings via shortcode
 */
class DBR_Admin_View {
	private $controller;
	/**
	 * Constructor
	 *
	 * @param DBR_Controller $controller The controller for this view
	 *
	 * @return DBR_View the view
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'dbr_settings_page'));
	}

	public function dbr_settings_page()
	{
		add_options_page(
			'Daily Bible Readings',
			'Daily Bible Readings',
			'manage_options',
			'daily-bible-readings',
			array($this, 'dbr_settings_page_html')
		);
		error_log('Hit 1');
	}

	public function dbr_settings_page_html() {
		// check user capabilities
	    if (!current_user_can('manage_options')) {
	    	error_log('Hit 2');
	        return;
	    }
	    echo '<div class="wrap">' .
	    '<h1>' . esc_html(get_admin_page_title()) . '</h1>' .
	    '</div>';
	    error_log('Hit 3');
	}
}