<?php
/*
Plugin Name: Orthodox Daily Readings
Plugin URI: http://paulschlueter.com
Description: This plugin allows you to post the current day's readings and fasting rule from antiochian.org on your own website.
Author: Paul Schlueter
Version: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://paulschlueter.com
*/

function odr_links() {
	echo "<div>These are the links.</div>";
}

add_shortcode('daily_readings_links', 'odr_links');

function odr_page() {
	echo "<div>This is the page.</div>";
}

add_shortcode('daily_readings_page', 'odr_page');

?>
