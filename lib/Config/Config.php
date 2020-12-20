<?php

namespace TwentySixB\Translations\Config;

use Exception;
use TwentySixB\Translations\Clients\Client;
use TwentySixB\Translations\Clients\Generator\Client as GeneratorClient;
use TwentySixB\Translations\Clients\Service\Client as ServiceClient;
use TwentySixB\Translations\Input\CLI;
use TwentySixB\Translations\Input\Dataset;
use TwentySixB\Translations\Input\File;
use TwentySixB\Translations\Input\Input;

/**
 * Class for dealing with the config files.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Config {

	/**
	 * Default Client for the configs.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	private $inputs;

	/**
	 * Array of the loaded config.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	private $config = [];

	/**
	 * @since 0.0.0
	 * @param Input $inputs,... Inputs sources for the Config.
	 */
	public function __construct( Input ...$inputs ) {
		$this->inputs = $inputs;
	}

	/**
	 * Get the merged data from all the input sources provided in the constructor.
	 *
	 * @since  0.0.0
	 * @param  string $purpose Name of the purpose.
	 * @return array           Array of Projects.
	 */
	public function get( string $purpose ) : array {
		$this->merge_inputs( $purpose );

		$project_configs = [];
		foreach ( $this->config as $config ) {
			$project_configs[] = new Project( $config );
		}
		return $project_configs;
	}

	/**
	 * Merge the data in all the received inputs for a specific purpose.
	 *
	 * @since  0.0.0
	 * @param  string $purpose Name of the purpose.
	 * @return void
	 */
	private function merge_inputs( string $purpose ) : void {
		$config = [];
		foreach ( $this->inputs as $input ) {
			//TODO: verify what happens when multiple projects are supplied to CLI
			$values = $input->get();
			if ( $input instanceof File || $input instanceof Dataset ) {
				$values = $this->filter_and_clean_array_configs( $values, $purpose );

			} elseif ( $input instanceof CLI ) {
				foreach ( $values as $name => $proj_config ) {
					$values[ $name ]['client'] = $this->get_client( $name, $proj_config );
				}
			}
			//TODO: have a more generic way to deal with other inputs.

			$config = array_merge( $config, $values );
		}
		$this->config = $config;
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
	 * @since  0.0.0
	 * @param  string $name   Project Name
	 * @param  array  $config Config for a Project
	 * @return Client
	 * @throws Exception
	 */
	private function get_client( string $name, array $config ) : Client {
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

	/**
	 * Filter, clean and return array of project configs for a specific purpose.
	 *
	 * @since  0.0.0
	 * @param  array  $configs Array of arrays (project configs).
	 * @param  string $purpose Name of the purpose.
	 * @return array  Filtered and cleaned configs.
	 */
	private function filter_and_clean_array_configs( array $configs, string $purpose ) : array {
		// Filter configs by purpose
		$configs = array_filter(
			$configs,
			function ( $proj_config ) use ( $purpose ) {
				return isset( $proj_config[ $purpose ] );
			}
		);

		// Get only purpose specific configs
		array_walk(
			$configs,
			function ( &$proj_config, $name ) use ( $purpose ) {
				$config = $proj_config[ $purpose ];
				if ( isset( $proj_config['key'] ) ) {
					$config['key'] = $proj_config['key'];
				}
				$config['name']   = $name;
				$config['client'] = $this->get_client( $name, $config );
				$proj_config      = $config;
			},
		);
		return $configs;
	}
}
