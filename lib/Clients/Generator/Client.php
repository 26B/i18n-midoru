<?php

namespace TwentySixB\Translations\Clients\Generator;

use TwentySixB\Translations\Clients\Client as BaseClient;

/**
 * Base client class for handling generation of files.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
abstract class Client extends BaseClient {

	/**
	 * Generate files according to the arguments.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for generation.
	 * @return mixed       Result of the generation.
	 */
	abstract public function generate( array $args );
}
