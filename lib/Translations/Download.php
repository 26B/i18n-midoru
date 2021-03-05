<?php
// phpcs:disable
namespace TwentySixB\Translations\Translations;

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
	 * Keys that are accepted for exporting/downloading a locale.
	 *
	 * @since 0.0.0
	 * @var   array
	 */
	const ACCEPTED_EXPORT_KEYS = [
		'__project_name', // The project from the config.
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
	 * @throws Exception
	 */
	public function download() : array {
		$this->authenticate();
		$downloads = [];

		foreach ( $this->config->get_locales() as $locale ) {
			$export = $this->config->get_client()->export( $this->make_export_config( $locale ) );

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
	private function make_export_config( string $locale ) : array {
		$config                   = $this->config->get_config();
		$config['locale']         = $locale;
		$config['__project_name'] = $this->config->get_name();
		return array_filter(
			$config,
			function ( $key ) {
				return in_array( $key, self::ACCEPTED_EXPORT_KEYS );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
