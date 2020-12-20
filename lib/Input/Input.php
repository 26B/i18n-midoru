<?php

namespace TwentySixB\Translations\Input;

/**
 * Input Interface.
 *
 * All Input classes should implement this class.
 */
interface Input {

	public function get() : array;
}
