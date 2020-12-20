<?php

namespace TwentySixB\Translations\Input;

/**
 * Input class for dealing with arrays.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Dataset implements Input {

	/**
	 * @since 0.0.0
	 * @param array $values
	 */
	public function __construct( array $values ) {
		$this->values = $values;
	}

	/**
	 * Get the values for this input.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function get() : array {
		return $this->values;
	}
}
