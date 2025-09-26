<?php

namespace TwentySixB\Translations\Config;

use Exception;
use TwentySixB\Translations\Clients\Client;
use TwentySixB\Translations\Clients\Generator\Client as GeneratorClient;
use TwentySixB\Translations\Clients\Service\Client as ServiceClient;
use TwentySixB\Translations\Exceptions\ConfigFileNotFound;
use TwentySixB\Translations\LockHandler;

/**
 * Class for dealing with the config files.
 *
 * @since      1.0.0
 * @package    TwentySixB\Translations
 * @subpackage TwentySixB\Translations\Config
 * @author     26B <hello@26b.io>
 */
class Config {

	/**
	 * Array of the loaded config.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $config = [];

	/**
	 * @since 1.0.0
	 * TODO: be able to read from custom path.
	 */
	public function __construct() {
		$path = getcwd() . '/i18n-midoru.json';
		if ( ! file_exists( $path ) ) {
			throw new ConfigFileNotFound( "Config file in {$path} not found." );
		}
		$this->config = json_decode( file_get_contents( $path ), true, 512, JSON_THROW_ON_ERROR );
		LockHandler::get_instance()->validate( $this->config );
	}

	/**
	 * Get the merged data from all the input sources provided in the constructor.
	 *
	 * @since  1.0.0
	 * @param  string   $purpose         Name of the purpose.
	 * @param  string[] $wanted_projects Array of project names to get configuration for. Default is
	 *                                   an empty array, all projects are retrieved.
	 * @return array                     Array of Projects.
	 */
	public function get( string $purpose, array $wanted_projects = [] ) : array {
		$project_configs = [];
		foreach ( $this->config as $project_name => $config ) {
			if (
				! isset( $config[ $purpose ] )
				|| ( ! empty( $wanted_projects ) && ! in_array( $project_name, $wanted_projects, true ) )
			) {
				continue;
			}
			$project_configs[] = new Project(
				$this->prepare_config( $project_name, $config, $purpose )
			);
		}
		return $project_configs;
	}

	/**
	 * Get the client for a specific config.
	 *
	 * If the config does not have the 'client' key, then an exception is thrown.
	 *
	 * If the config has a 'client' key, the value is expected to be the name of a class or the path
	 * to a class that extends Client, and it will be used to create a client which will be returned.
	 * First it checks if the value is a path to a class (akin to something given by using ::class),
	 * and if so, it uses that to create the class. Next it checks if it is one of our Clients and
	 * creates the path to the classes assuming that the value is just the name of the class (i.e.
	 * 'localise' or 'Localise'). If that class exists, it creates using that. If not, then an
	 * exception is thrown.
	 *
	 * @since  1.0.0
	 * @param  string $name   Project Name
	 * @param  array  $config Config for a Project
	 * @return Client
	 * @throws Exception
	 */
	private function get_client( string $name, array $config ) : Client {
		//TODO: we no longer need to handle classes (i.e. Localise::class), since its all by json.
		if ( isset( $config['client'] ) ) {
			if ( class_exists( $config['client'] ) ) {
				return new $config['client']();
			}

			$uc_client = ucfirst( $config['client'] );
			$matches   = [];
			preg_match( '/.*\\\/', GeneratorClient::class, $matches );
			$class = $matches[0] . $uc_client;
			if ( class_exists( $class ) ) {
				return new $class();
			}

			$matches = [];
			preg_match( '/.*\\\/', ServiceClient::class, $matches );
			$class = $matches[0] . $uc_client;
			if ( class_exists( $class ) ) {
				return new $class();
			}
			// TODO: verify both paths (warn if same name in both places)
			throw new Exception( "Client '{$config['client']}' given in config for project '{$name}' does not exist." );
		}
		throw new Exception( "Config for project '{$name}' does not have a value for 'client'." );
	}

	private function prepare_config( $name, $proj_config, $purpose ) : array {
		$config = $proj_config[ $purpose ];
		if ( isset( $proj_config['key'] ) ) {
			$config['key'] = $proj_config['key'];
		}
		$config['name']   = $name;
		$config['client'] = $this->get_client( $name, $config );
		return $config;
	}
}
