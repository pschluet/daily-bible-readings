<?php
 /**
 * Represents a single reading
 */
class DBR_Reading {
	private $title;
	private $shortText;
	private $fullText;

	/**
	 * Get the full text of the bible reading.
	 * 
	 * @return string the text of the bible reading
	 */
	public function get_full_text() {
		return $this->fullText;
	}

	/**
	 * Get the title of the bible reading.
	 * 
	 * @return string the title of the bible reading
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get the short "teaser" text of the bible reading.
	 * 
	 * @return string the short "teaser" text of the bible reading
	 */
	public function get_short_text() {
		return $this->shortText;
	}

	/**
	 * Set the full text of the bible reading.
	 *
	 * @param string $value The full text of the bible reading
	 */
	public function set_full_text(string $value) {
		$this->fullText = $value;
	}

	/**
	 * Set the short "teaser" text of the bible reading.
	 *
	 * @param string $value The short "teaser" text of the bible reading
	 */
	public function set_short_text(string $value) {
		$this->shortText = $value;
	}

	/**
	 * Set the title of the bible reading.
	 *
	 * @param string $value The title of the bible reading
	 */
	public function set_title(string $value) {
		$this->title = $value;
	}
}