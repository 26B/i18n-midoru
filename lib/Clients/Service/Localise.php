<?php

namespace TwentySixB\Translations\Clients\Service;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use TwentySixB\Translations\Exceptions\AuthorizationFailed;
use TwentySixB\Translations\LockHandler;

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

		$client = new \GuzzleHttp\Client(
			[
				'base_uri' => 'https://localise.biz/api/',
				'headers'  => [
					'Authorization' => 'Loco ' . $args['key'],
				],
			]
		);

		try {
			$res = $client->request( 'GET', 'auth/verify', [] );
		} catch ( GuzzleException $e ) {
			print( "\nException thrown while authenticating for project '{$args['__project_name']}'." );
			throw $e;
		}

		if ( $res->getStatusCode() !== 200 ) {
			throw new AuthorizationFailed( 'Authorization was not successful.' );
		}

		$body = json_decode( $res->getBody()->__toString(), true );

		printf( "Authenticated as '%s' in project '%s'.\n", $body['user']['name'], $body['project']['name'] );

		// Save client and return result.
		$this->client = $client;
		return $body;
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

		$proj_name     = $args['__project_name'];
		$last_modified = $args['__last_modified'];

		$url = sprintf( 'export/locale/%s.%s', $args['locale'], $args['ext'] );
		unset( $args['locale'], $args['ext'], $args['__project_name'], $args['__last_modified'] );

		$url .= empty( $args ) ? '' : '?' . http_build_query( $args );

		try {
			$res = $this->client->request( 'GET', $url, $this->get_export_options( $last_modified ) );
		} catch ( GuzzleException $e ) {
			print( "\nException thrown while downloading for project '{$proj_name}'." );
			throw $e;
		}

		LockHandler::get_instance()->set( $proj_name, 'Last-Modified', $res->getHeaders()['Last-Modified'][0] );

		return $res->getBody()->__toString();
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

		$proj_name = $args['__project_name'];
		$url       = sprintf( 'import/%s', $args['ext'] );
		$body      = $args['data'];
		unset( $args['__project_name'], $args['ext'], $args['data'], );

		$url .= empty( $args ) ? '' : '?' . http_build_query( $args );

		try {
			$res = $this->client->request( 'POST', $url, [ 'body' => $body ] );
		} catch ( GuzzleException $e ) {
			print( "\nException thrown while uploading for project '{$proj_name}'." );
			throw $e;
		}

		return json_decode( $res->getBody()->__toString(), true );
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

	/**
	 * Get options for export.
	 *
	 * Check env or constant value for not checking if export has been modified since.
	 *
	 * @since  0.0.0
	 * @param  string $last_modified
	 * @return array
	 */
	private function get_export_options( string $last_modified ) : array {
		if (
			getenv( 'DONT_CHECK_MODIFIED' ) === 'true'
			|| ( defined( 'DONT_CHECK_MODIFIED' ) && constant( 'DONT_CHECK_MODIFIED' ) )
		) {
			return [];
		}

		return [
			'headers' => [
				'If-Modified-Since' => $last_modified,
			],
		];
	}
}
