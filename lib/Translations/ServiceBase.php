<?php
// phpcs:disable
namespace TwentySixB\Translations\Translations;

use Exception;
use TwentySixB\Translations\Clients\Service\Client as ServiceClient;
use TwentySixB\Translations\Config\Project;

/**
 * Base class for dealing with Service requests, like Download and Upload.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
abstract class ServiceBase {

	/**
	 * @var Project
	 */
	protected Project $config;

	/**
	 * @param Project $config
	 * @throws Exception
	 */
	public function __construct( Project $config ) {
		$this->config = $config;

		// Verify client in config is of type Service.
		if ( ! $this->config->get_client() instanceof ServiceClient ) {
			$name = $this->config->get_name();
			throw new Exception(
				"Client given for project '{$name}' is not a service type client."
			);
		}
	}

	/**
	 * Autenticate via the client using the config's api key.
	 *
	 * @since  0.0.0
	 * @return mixed Client's authenticate output
	 * @throws AuthorizationFailed
	 */
	protected function authenticate() {
		$client = $this->config->get_client();
		return $client->authenticate(
			[
				'key'            => $this->config->get_api_key( $client->get_api_key_prefix() ),
				'__project_name' => $this->config->get_name(),
			]
		);
	}
}
