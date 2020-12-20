<?php

namespace TwentySixB\Translations\Clients\Service;

use TwentySixB\Translations\Clients\Client as BaseClient;

/**
 * Base client class for handling services.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
abstract class Client extends BaseClient {

	/**
	 * Authenticate the client for the service using the provided arguments.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for authentication
	 * @return mixed       Authenticate result.
	 */
	public function authenticate( array $args ) {
		return true; //TODO: what to return by default
	}

	/**
	 * Export data from the service.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for exporting
	 * @return mixed       Export result.
	 */
	public function export( array $args ) {
		return true; //TODO: what to return by default
	}

	/**
	 * Import data into the service.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for importing
	 * @return mixed       Import result.
	 */
	public function import( array $args ) {
		return true; //TODO: what to return by default
	}

	/**
	 * Get the suffix for the api key for the service.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_api_key_prefix() : string {
		return '';
	}
}
