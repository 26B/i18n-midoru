<?php

namespace TwentySixB\Translations\Input;

use TwentySixB\Translations\Exceptions\ConfigFileNotFound;
use TwentySixB\Translations\Exceptions\ConfigFileNotValid;
use Exception;
use JsonException;
use Throwable;

/**
 * Read the configuration from a File.
 *
 * @todo Maybe also support YAML.
 */
class File implements Input {

	public function __construct( string $path ) {
		$this->path = $path;
	}

	/**
	 * TODO:
	 *
	 * @since 0.0.0
	 *
	 * @return array
	 * @throws ConfigFileNotFound
	 * @throws ConfigFileNotValid
	 * @throws Throwable
	 */
	public function get() : array {
		if ( ! file_exists( $this->path ) ) {
			throw new ConfigFileNotFound( 'Config file not found' );
		}

		try {
			return json_decode( file_get_contents( $this->path ), true, 512, JSON_THROW_ON_ERROR );

		} catch ( Throwable $th ) {

			if ( $th instanceof JsonException ) {
				throw new ConfigFileNotValid( $th->getMessage(), $th->getCode(), $th );
			}

			throw $th;
		}
	}
}
