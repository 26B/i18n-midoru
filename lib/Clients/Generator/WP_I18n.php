<?php

namespace TwentySixB\Translations\Clients\Generator;

use Exception;

/**
 * Client class for handling generation of files using wp i18n.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class WP_I18n extends Client {

	/**
	 * Template for making the wp i18n make-pot command for creating the .pot files.
	 *
	 * @since 0.0.0
	 * @var   string
	 *
	 * TODO: This is specific for make-pot right now.
	 */
	const EXEC = "wp i18n make-pot '%s' '%s' --domain=%s %s 2>&1 ";

	/**
	 * Generate files according to the arguments.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for generation.
	 * @return null
	 * @throws Exception
	 */
	public function generate( array $args ) {
		$output        = [];
		$return_status = false;
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
		exec(
			sprintf(
				self::EXEC,
				$args['source'],
				$args['destination'],
				$args['domain'],
				$args['skip-js'] ? '--skip-js' : ''
			),
			$output,
			$return_status
		);
		if ( $return_status === 1 ) {
			throw new Exception( implode( "\n", $output ) );
		}
		echo "\nPot for domain {$args['domain']} was made successfully.\n";
		echo "Output:\n";
		echo implode( "\n", $output ) . "\n";
		echo "\n";
	}
}
