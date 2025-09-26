<?php
// phpcs:disable
namespace TwentySixB\Translations\Translations;

use Exception;
use TwentySixB\Translations\Exceptions\SourceFileNotFound;

/**
 * Class for dealing with the import of tranlations to localise.
 *
 * @since      1.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Upload extends ServiceBase {

	/**
	 * Keys that are accepted for importing/uploading a locale.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	const ACCEPTED_IMPORT_KEYS = [
		'__project_name', // The project from the config.
		'locale',
		'ext',
		'data',
		'async',
		'source', //TODO: check if we actually want this, see localize API
		'index', //TODO: check if we actually want this, see localize API
		'tag-new',
		'tag-updated',
		'tag-absent',
		'delete-absent',
		//TODO: do we put every URI parameter that localize can receive for import?
	];

	/**
	 * Import files into localize.
	 *
	 * @since 1.0.0
	 * @return void
	 * @throws AuthorizationFailed
	 * @throws FilenameArgumentNotAvailable
	 * @throws Exception
	 */
	public function upload() : void {
		$this->authenticate();

		$name = $this->config->get_name();

		foreach ( $this->config->get_locales() as $locale ) {
			try {
				$data = $this->get_data_to_import( $locale );

			} catch ( SourceFileNotFound $e ) {
				printf( $e->getMessage() );
				continue;
			}
			$import = $this->config->get_client()->import(
				$this->make_import_config( $locale, $data )
			);
			printf("{$name}: operation completed with message: '%s'\n", $import['message']);
		}
	}

	/**
	 * Make the config for importing a locale for a specific project.
	 *
	 * @since  1.0.0
	 * @param  string $locale  Locale to import.
	 * @return array
	 */
	private function make_import_config( string $locale, string $data ) : array {
		$config                    = $this->config->get_config();
		$config['__project_name']  = $this->config->get_name();
		$config['locale']          = $locale;
		$config['data']            = $data;
		return array_filter(
			$config,
			function ( $key ) {
				return in_array( $key, self::ACCEPTED_IMPORT_KEYS );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Load data from files to import.
	 *
	 * @since 1.0.0
	 * @param  string $locale  Locale to import.
	 * @return string
	 * @throws SourceFileNotFound
	 * @throws FilenameArgumentNotAvailable
	 */
	private function get_data_to_import( string $locale ) : string {
		$path = $this->config->get_path( $locale );
		if ( ! file_exists( $path ) ) {
			throw new SourceFileNotFound(
				"File for data to import for '{$locale}' in path '{$path}' does not exist.\n"
			);
		}
		$data = file_get_contents( $path );
		return $data;
	}
}
