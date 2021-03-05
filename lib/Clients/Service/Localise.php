<?php

namespace TwentySixB\Translations\Clients\Service;

use Exception;
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
				'headers' => [
					'Authorization' => 'Loco ' . $args['key'],
				],
			]
		);

		$res = $client->request(
			'GET',
			'https://localise.biz/api/auth/verify',
			[]
		);

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

		$lock      = LockHandler::get_instance();
		$proj_name = $args['__project_name'];

		$url = sprintf(
			'https://localise.biz/api/export/locale/%s.%s',
			$args['locale'],
			$args['ext']
		);
		unset( $args['locale'], $args['ext'], $args['__project_name'] );

		$url .= $this->get_query_string( $args );

		$res = $this->client->request(
			'GET',
			$url,
			[
				'headers' => [
					'If-Modified-Since' => $lock['localise'][ $proj_name ]['Last-Modified'] ?? '',
				],
			]
		);

		$lock['localise'][ $proj_name ]['Last-Modified'] = $res->getHeaders()['Last-Modified'][0];

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

		$url  = sprintf( 'https://localise.biz/api/import/%s', $args['ext'] );
		$body = $args['data'];
		unset( $args['ext'], $args['data'] );

		$url .= $this->get_query_string( $args );

		$res = $this->client->request( 'POST', $url, [ 'body' => $body ] );

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
	 * Get the query string from the arguments.
	 *
	 * @since 0.0.0
	 * @param array $args
	 * @return string
	 */
	private function get_query_string( array $args ) : string {
		$query_string = [];
		foreach ( $args as $arg_name => $arg_value ) {
			$query_string[] = "{$arg_name}={$arg_value}";
		}

		if ( empty( $query_string ) ) {
			return '';
		}

		return '?' . implode( '&', $query_string );
	}
}
