<?php
// phpcs:disable
namespace TwentySixB\Translations\Translations;

use TwentySixB\Translations\LockHandler;

/**
 * Class for dealing with the export of tranlations from localise.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class Download extends ServiceBase {

	/**
	 * Default cooldown time in microseconds.
	 *
	 * @var int
	 */
	const DEFAULT_COOLDOWN = 100000;

	/**
	 * Keys that are accepted for exporting/downloading a locale.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	const ACCEPTED_EXPORT_KEYS = [
		'__project_name', // The project from the config.
		'__last_modified',
		'locale',
		'ext',
		'format',
		'source', //TODO: check if we actually want this, see localize API
		'index', //TODO: check if we actually want this, see localize API
		//TODO: do we put every URI parameter that localize can receive for export?
	];

	/**
	 * Download and return the exports given the config, projects and locales.
	 *
	 * @since  0.0.0
	 * @return array
	 * @throws AuthorizationFailed
	 * @throws FilenameArgumentNotAvailable
	 */
	public function download() : array {
		$this->authenticate();
		$downloads = [];

		$lock          = LockHandler::get_instance();
		$last_modified = $lock->get( $this->config->get_name(), 'Last-Modified', '' );
		$cooldown      = $this->config->get_cooldown();
		$cooldown_time = is_bool( $cooldown ) ? self::DEFAULT_COOLDOWN : $cooldown;
		$first         = true;

		foreach ( $this->config->get_locales() as $locale ) {
			if ( $cooldown && ! $first ) {
				usleep( $cooldown_time );
			}
			$first = false;

			$export = $this->config->get_client()->export( $this->make_export_config( $locale, $last_modified ) );

			if ( $export === '' ) {
				printf( "Download for language '{$locale}' was returned empty and was not saved.\n" );
				continue;
			}
			printf( "Got download for language '{$locale}'.\n" );

			$downloads[ $locale ] = $export;
		}
		return $downloads;
	}

	/**
	 * Save the downloads receive given the path in the config for each locale.
	 *
	 * @since  0.0.0
	 * @param  array $downloads Downloads to save (keys are locales and values are the data to save)
	 * @return void
	 * @throws Exception
	 */
	public function save( array $downloads ) : void {
		foreach( $downloads as $locale => $export ) {
			file_put_contents(
				$this->config->get_path( $locale ),
				$export
			);
		}
	}

	/**
	 * Make the config for exporting a locale for a specific project.
	 *
	 * @since  0.0.0
	 * @param  string $locale  Locale to export.
	 * @return array
	 */
	private function make_export_config( string $locale, string $last_modified ) : array {
		$config                    = $this->config->get_config();
		$config['locale']          = $locale;
		$config['__project_name']  = $this->config->get_name();
		$config['__last_modified'] = $last_modified;
		return array_filter(
			$config,
			function ( $key ) {
				return in_array( $key, self::ACCEPTED_EXPORT_KEYS );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
