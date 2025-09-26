<?php

namespace TwentySixB\Translations\Clients\Generator;

use TwentySixB\Translations\Clients\Client as BaseClient;

/**
 * Base client class for handling generation of files.
 *
 * @since      1.0.0
 * @package    TwentySixB\Translations
 * @subpackage TwentySixB\Translations\Clients
 * @author     26B <hello@26b.io>
 */
abstract class Client extends BaseClient {

	/**
	 * Generate files according to the arguments.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments for generation.
	 * @return mixed       Result of the generation.
	 */
	abstract public function generate( array $args );
}
