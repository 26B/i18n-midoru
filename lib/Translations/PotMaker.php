<?php

namespace TwentySixB\Translations\Translations;

use Exception;
use TwentySixB\Translations\Config\Project;
use TwentySixB\Translations\Exceptions\DirectoryDoesntExist;
use TwentySixB\Translations\Clients\Generator\Client as GeneratorClient;

/**
 * Class for handling the creation of .pot files.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class PotMaker {

	protected Project $config;

	/**
	 * @since 0.0.0
	 * @param Project $config Project config.
	 * @throws Exception
	 */
	public function __construct( Project $config ) {
		$this->config = $config;

		// Verify client in config is of type Generator.
		if ( ! $this->config->get_client() instanceof GeneratorClient ) {
			$name = $this->config->get_name();
			throw new Exception(
				"Client given for project '{$name}' is not a generator type client."
			);
		}
	}

	/**
	 * Make a single pot file given the config received in the constructor.
	 *
	 * @since 0.0.0
	 * @return void
	 * @throws DirectoryDoesntExist
	 * @throws FilenameArgumentNotAvailable
	 * @throws Exception
	 */
	public function make_pot() : void {
		$source_path = $this->config->get_source_path();
		$pot_path    = $this->config->get_pot_path();
		$sources = is_array( $source_path ) ? $source_path : [ $source_path ];
		foreach ( $sources as $source ) {
			if ( is_dir( $source ) ) {
				continue;
			}
			throw new DirectoryDoesntExist(
				'Source directory for generating pots does not exist. Path received was:' .
				" {$source}\n"
			);
		}
		$matches = [];
		preg_match( '/.*\//', $pot_path, $matches );
		if ( ! is_dir( $matches[0] ) ) {
			throw new DirectoryDoesntExist(
				"Directory for .pot path does not exist. Path received was: {$pot_path}\n"
			);
		}

		$this->config->get_client()->generate(
			[
				'source'      => $source_path,
				'destination' => $pot_path,
				'domain'      => $this->config->get_domain(),
				'skip-js'     => $this->config->get_skip_js(),
				'is_default'  => $this->config->get_config()['is_default'] ?? false,
			]
		);
	}
}
