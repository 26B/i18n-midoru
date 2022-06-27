<?php

namespace TwentySixB\Translations\Config;

use TwentySixB\Translations\Clients\Client;
use TwentySixB\Translations\Exceptions\FilenameArgumentNotAvailable;
use TwentySixB\Translations\Exceptions\NoFilenameAvailableForPotFile;
use TwentySixB\Translations\Exceptions\NoApiKeyAvailable;

/**
 * Class for handling a specific purpose config for a Project.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Project {

	/**
	 * Config data.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	private $config = [];

	/**
	 * @since 0.0.0
	 * @param array $config Config for project. Expected to be the config for a specific purpose and
	 *                      have the field 'name' defined. The field 'key', wiht value for api key,
	 *                      is optional as the value can be retrieved from an environment variable
	 *                      with the name 'TRANSLATE_26B_API_KEY_{name_of_project}'.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
	}

	/**
	 * Get the api key for this project.
	 *
	 * Api key can be passed inside the config under the field 'key' but this is optional as the
	 * environment variable '{$prefix}_{name_of_project}' has priority if it exists.
	 *
	 * @since  0.0.0
	 * @param  string $prefix Prefix for the api key name.
	 * @return string         API key value.
	 * @throws NoApiKeyAvailable When key cannot be retrieved.
	 */
	public function get_api_key( string $prefix ) : string {
		$proj_name = $this->get_name();
		$key_name  = $prefix . strtoupper( str_replace( '-', '_', $proj_name ) );

		$key = getenv( $key_name );
		if ( $key !== false ) {
			return $key;
		}

		if ( defined( $key_name ) ) {
			return constant( $key_name );
		}

		if ( isset( $this->config['key'] ) ) {
			return $this->config['key'];
		}

		throw new NoApiKeyAvailable( "Expecting key `{$key_name}` for project `{$proj_name}`." );
	}

	/**
	 * Get an array of the locales defined inside the 'locale' field in the config.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function get_locales() : array {
		if ( is_string( $this->config['locale'] ) ) {
			return [ $this->config['locale'] ];
		}
		return $this->config['locale'];
	}

	/**
	 * Get the config that was passed in the constructor.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function get_config() : array {
		return $this->config;
	}

	/**
	 * Get the name under the field 'name' in the config.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_name() : string {
		return $this->config['name'];
	}

	/**
	 * Get the domain under the field 'domain' in the config.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_domain() : string {
		return $this->config['domain'];
	}

	/**
	 * Get the format under the field 'format' in the config.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_format() : string {
		return $this->config['format'];
	}

	/**
	 * Get the Client under the field 'client' in the config.
	 *
	 * @since  0.0.0
	 * @return Client
	 */
	public function get_client() : Client {
		return $this->config['client'];
	}

	/**
	 * Get the value under the field 'skip-js' in the config.
	 *
	 * @since  0.0.0
	 * @return string
	 */
	public function get_skip_js() : bool {
		// TODO: Maybe the default depends on the the format. If 'jed' then true, else false.
		return $this->config['skip-js'] ?? true;
	}

	/**
	 * Get the value under the field 'wrap-jed' in the config. Default value is true if not defined.
	 *
	 * @since  0.0.0
	 * @return bool
	 */
	public function get_wrap_jed() : bool {
		return $this->config['wrap-jed'] ?? ( $this->config['format'] === 'jed' );
	}

	/**
	 * Get the path for the file.
	 *
	 * @since  0.0.0
	 * @param  string $locale Code for the locale for the name of the file.
	 * @return string         Path for the file.
	 * @throws FilenameArgumentNotAvailable
	 */
	public function get_path( string $locale ) : string {
		$path = $this->config['path'] ?? './';

		if ( isset( $this->config['filename'] ) ) {
			return $path . $this->parse_filename( $this->config['filename'], $this->config, $locale );
		}

		// TODO: This should disappear with the filename
		if ( isset( $this->config['type'] ) && $this->config['type'] === 'theme' ) {
			return $path . "{$locale}.{$this->config['ext']}";
		}

		$filename_parts = [];

		if ( isset( $this->config['domain'] ) ) {
			$filename_parts[] = "{$this->config['domain']}";
		}

		if ( ! empty( $locale ) ) {
			$filename_parts[] = "{$locale}";
		}

		// TODO: Should we only add this if the format/ext is also jed/json?
		// TODO: this parameter can probably die since it can be hardcoded into filename
		if ( isset( $this->config['js-handle'] ) ) {
			$filename_parts[] = "{$this->config['js-handle']}";
		}

		$path .= implode( '-', $filename_parts );
		return "{$path}.{$this->config['ext']}";
	}

	/**
	 * Get value for 'source_path' in config.
	 *
	 * @since  0.0.0
	 * @return string|array
	 */
	public function get_source_path() {
		return $this->config['source'];
	}

	/**
	 * Get path for .pot file given the config.
	 *
	 * TODO: similar to get_path (Maybe join the two, but would require more arguments)
	 *
	 * @since  0.0.0
	 * @return string
	 * @throws NoFilenameAvailableForPotFile
	 * @throws FilenameArgumentNotAvailable
	 */
	public function get_pot_path() : string {
		$path = "{$this->config['destination']}";

		if ( isset( $this->config['filename'] ) ) {
			return $path . $this->parse_filename(
				$this->config['filename'],
				array_merge(
					$this->config,
					[ 'ext' => 'pot' ],
				)
			);

		} elseif ( isset( $this->config['domain'] ) ) {
			$path .= "{$this->config['domain']}";

		} else {
			//TODO: maybe a default name?
			// File needs to have a name
			throw new NoFilenameAvailableForPotFile(
				"Neither 'filename' nor 'domain' is set for 'make-pot' for project" .
				" '{$this->config['name']}', but at least one of them is needed for the pot file" .
				' to have a filename.'
			);
		}
		return "{$path}.pot";
	}

	/**
	 * Parse filename in config and replace requested arguments with their values.
	 *
	 * @since 0.0.0
	 *
	 * @param  string $filename
	 * @param  array  $arg_values
	 * @param  string $locale
	 * @return string
	 * @throws FilenameArgumentNotAvailable
	 */
	private function parse_filename(
		string $filename,
		array $arg_values,
		string $locale = ''
	) : string {

		// Replace locale and locale with the first 2 characters.
		$filename = str_replace( [ '{$locale}', '{$locale_2c}' ], [ $locale, str_split( $locale, 2 )[0] ], $filename );

		$matches = [];
		preg_match_all( '/\{\$([a-zA-Z0-9_-]*)\}/', $filename, $matches );

		foreach ( $matches[1] as $string_arg ) {

			if ( ! isset( $arg_values[ $string_arg ] ) ) {
				// TODO: make into a more specific argument.
				throw new FilenameArgumentNotAvailable(
					sprintf( 'Value for argument {$%s} in filename is not available.', $string_arg ),
				);
			}

			$filename = str_replace(
				sprintf( '{$%s}', $string_arg ),
				$arg_values[ $string_arg ],
				$filename
			);
		}

		return $filename;
	}
}
