<?php

namespace TwentySixB\Translations;

use Exception;
use TwentySixB\Translations\Config\Config;
use TwentySixB\Translations\Translations\Download;
use TwentySixB\Translations\Translations\PotMaker;
use TwentySixB\Translations\Translations\Upload;

/**
 * Main operation class.
 */
class Translations {

	/**
	 * @since 0.0.0
	 */
	public function __construct() {
		$this->config = new Config();
		printf( "Config loaded successfully.\n" );
	}

	/**
	 * Download translations.
	 *
	 * @param  string[] $wanted_projects Array of project names to make_pots for. Default is an
	 *                                   empty array, all projects with `export` are considered.
	 * @return void
	 * @throws AuthorizationFailed
	 * @throws FilenameArgumentNotAvailable
	 * @throws Exception
	 */
	public function download( array $wanted_projects = [] ) {
		$projects_config = $this->config->get( 'export', $wanted_projects );

		// Maybe handle project iteration here.
		foreach ( $projects_config as $config ) {
			$downloader = new Download( $config );
			print( "Downloader created successfully.\n" );

			$downloads = $downloader->download();
			if ( $config->get_format() === 'jed' && $config->get_wrap_jed() ) {
				$downloads = $this->wrap_jsons( $downloads );
			}

			$downloader->save( $downloads );
			print( "Translation files downloaded and saved successfully.\n" );
		}

		LockHandler::get_instance()->write();
	}

	/**
	 * Upload files for translation.
	 *
	 * @param  string[] $wanted_projects Array of project names to make_pots for. Default is an
	 *                                   empty array, all projects with `import` are considered.
	 * @return void
	 * @throws AuthorizationFailed
	 * @throws FilenameArgumentNotAvailable
	 * @throws Exception
	 */
	public function upload( array $wanted_projects = [] ) {
		$projects_config = $this->config->get( 'import', $wanted_projects );

		// Maybe handle project iteration here.
		foreach ( $projects_config as $config ) {
			$uploader = new Upload( $config );
			print( "Uploader created successfully.\n" );

			// Maybe breakdown into get AND upload.
			$uploader->upload();
			print( "Translation files uploaded successfully.\n" );
		}
	}

	/**
	 * Make pot files.
	 *
	 * @param  string[] $wanted_projects Array of project names to make_pots for. Default is an
	 *                                   empty array, all projects with `make_posts` are considered.
	 * @return void
	 * @throws DirectoryDoesntExist
	 * @throws NoFilenameAvailableForPotFile
	 * @throws FilenameArgumentNotAvailable
	 * @throws Exception
	 */
	public function make_pots( array $wanted_projects = [] ) {
		$projects_config = $this->config->get( 'make_pots', $wanted_projects );

		// Maybe handle project iteration here.
		foreach ( $projects_config as $config ) {
			$pot_maker = new PotMaker( $config );
			print( "Pot Maker was created successfully.\n" );

			// Maybe breakdown into get AND upload.
			$pot_maker->make_pot();
			print( "Translation pot files made successfully.\n" );
		}
	}

	/**
	 * Wrap jsons with a layer with the key 'locale_data'.
	 *
	 * @since  0.0.0
	 * @param  array $jsons
	 * @return array
	 */
	private function wrap_jsons( array $jsons ) : array {
		foreach ( $jsons as $key => $json ) {
			$jsons[ $key ] = json_encode(
				[
					'locale_data' => json_decode( $json, true ),
				],
				JSON_PRETTY_PRINT
			);
		}
		return $jsons;
	}
}
