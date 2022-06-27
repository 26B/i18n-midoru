<?php

namespace TwentySixB\Translations\Clients\Generator;

use Gettext\Scanner\PhpScanner;
use Gettext\Generator\PoGenerator;
use Gettext\Translation;
use Gettext\Translations;
use KKomelin\TranslatableStringExporter\Core\StringExtractor;

/**
 * Client class for handling generation of files using gettext for php files and a string extractor
 * for blade files.
 */
class Laravel_Gettext extends Client {

	public function generate( array $args ) {
		$php_translations   = $this->get_php_translations( $args );
		$blade_translations = $this->get_blade_translations( $args );
		foreach ( $blade_translations as $blade_translation => $empty ) {
			$php_translations->add( Translation::create( null, $blade_translation ) );
		}

		// Save the translations in .po files
		$generator = new PoGenerator();

		$generator->generateFile($php_translations, "{$args['destination']}");
	}

	private function get_php_translations( $args ) : Translations {
		$translations = Translations::create( $args['domain'] );
		$headers = $translations->getHeaders();
		$headers->set( 'POT-Creation-Date', date( 'c' ) );
		$headers->set( 'Content-Type', 'text/plain; charset=UTF-8' );
		$headers->set( 'Content-Transfer-Encoding', '8bit' );

		// Create a new scanner, adding a translation for each domain we want to get:
		$phpScanner = new PhpScanner( $translations );

		// Set a default domain, so any translations with no domain specified, will be added to that domain
		if ( $args['is_default'] ) {
			$phpScanner->setDefaultDomain( $args['domain'] );
		}

		// Extract all comments starting with 'i18n:' and 'Translators:'
		$phpScanner->extractCommentsStartingWith('i18n:', 'Translators:');

		// Scan files.
		$sources = is_array( $args['source'] ) ? $args['source'] : [ $args['source'] ];
		foreach ( $sources as $source ) {
			foreach ($this->glob_recursive("{$source}**/*.php") as $file) {
				$phpScanner->scanFile($file);
			}
		}

		return current( $phpScanner->getTranslations() );
	}

	private function get_blade_translations( $args ) : array {
		// TODO: Handle domains.
		// TODO: Optimize for only getting blade files.
		$translations = ( new StringExtractor() )->extract();
		return array_map( fn() => '', $translations );
	}

	private function glob_recursive( $pattern, $flags = 0 ) {
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->glob_recursive($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}
}
