<?php

namespace TwentySixB\Translations\Clients\Service;

use Exception;
use Loco\Http\ApiClient;
use TwentySixB\Translations\Exceptions\AuthorizationFailed;

/**
 * Client class for handling requests for Localise.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Localise extends Client {

	/**
	 * Localise/Loco client.
	 *
	 * @since 0.0.0
	 * @var   ApiClient|null
	 */
	private $client = null;

	/**
	 * Create a Localise client and try to authenticate it using the arguments given, assuming there
	 * is a value for 'key' and its value is the api key for localise.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for authentication
	 * @return mixed       Authenticate result.
	 * @throws AuthorizationFailed
	 */
	public function authenticate( array $args ) {
		// Clean previous client.
		$this->client = null;

		// Make a new client.
		$client = ApiClient::factory( $args );

		// Try to authenticate it.
		try {
			$result = $client->authVerify();
			printf( "Authenticated as '%s'.\n", $result['user']['name'] );
		} catch ( Exception $e ) {
			throw new AuthorizationFailed( 'Authorization was not successful.' );
		}

		// Save client and return result.
		$this->client = $client;
		return $result;
	}

	/**
	 * Export locale data from Localise.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for exporting
	 * @return string      Export result.
	 * @throws Exception
	 */
	public function export( array $args ) : string {
		if ( $this->client === null ) {
			throw new Exception( 'Authenticate should be called first' );
		}

		return $this->client->exportLocale( $args );
	}

	/**
	 * Import data to Localise.
	 *
	 * @since  0.0.0
	 * @param  array $args Arguments for exporting
	 * @return string      Export result.
	 * @throws Exception
	 */
	public function import( array $args ) {
		if ( $this->client === null ) {
			throw new Exception( 'Authenticate should be called first' );
		}

		return $this->client->import( $args );
	}

	/**
	 * Get the suffix for the api key for localise.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_api_key_prefix() : string {
		return 'LOCALISE_';
	}
}
