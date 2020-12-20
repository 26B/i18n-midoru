<?php

namespace TwentySixB\Translations\Input;

use Exception;
use TwentySixB\Translations\Exceptions\ParameterMissing;

/**
 * Input class for handling input via command line.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class CLI implements Input {

	/**
	 * Long options (i.e. --option) to get from CLI.
	 *
	 * TODO: infer name from domain or reverse
	 *
	 * @since 0.0.0
	 * @var array
	 */
	const LONG_OPTIONS = [
		'name::', //Not optional
		'locale::',
		'ext::',
		'format::',
		'path::',
		'domain::',
		'client::',
		'filename::',
		'js-handle::',
		'wrap-jed::',

		// Make pots
		'source::',
		'destination::',
		'skip-js::',
	];

	/**
	 * Options that need to be checked whether they have multiple values, in order to divide these
	 * values for the various projects, if there are more than one.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	const CHECK_FOR_MULTIPLE_VALUES = [
		'ext',
		'format',
		'path',
		'domain',
		'filename',
		'js-handle',
		'wrap-jed',
		'source',
		'destination',
		'skip-js',
	];

	/**
	 * Get the project options passed through the command line.
	 *
	 * @since  0.0.0
	 * @return array
	 * @throws ParameterMissing
	 * @throws Exception
	 */
	public function get() : array {

		$opts = getopt( null, self::LONG_OPTIONS );

		if ( ! isset( $opts['name'] ) ) {
			throw new ParameterMissing( 'CLI Option `name` was not passed and is required.' );
		}

		// If single project, just returned it in a named wrapper.
		if ( ! is_array( $opts['name'] ) ) {
			return [ $opts['name'] => $opts ];
		}

		// If more then one project, then we need to separate the options into the various projects.
		$n_projects = count( $opts['name'] );
		$projects   = [];
		foreach ( $opts['name'] as $idx => $name ) {
			$project         = $opts;
			$project['name'] = $name;

			// These might have multiple values, and if so, they will be divided by the projects.
			foreach ( self::CHECK_FOR_MULTIPLE_VALUES as $opt_name ) {
				if ( isset( $opts[ $opt_name ] ) && is_array( $opts[ $opt_name ] ) ) {
					if ( count( $opts[ $opt_name ] ) !== $n_projects ) {
						throw new Exception( "The number of {$opt_name}'s are different than the number of names." );
					}
					$project[ $opt_name ] = $opts[ $opt_name ][ $idx ];
				}
			}
			$projects[ $name ] = $project;
		}
		return $projects;
	}
}
