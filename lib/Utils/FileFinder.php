<?php

namespace TwentySixB\Translations\Utils;

use KKomelin\TranslatableStringExporter\Core\FileFinder as CoreFileFinder;
use Symfony\Component\Finder\Finder;


/**
 * File finder, used for making pots in Laravel.
 *
 * Extended to avoid use of `config` and `base_path` in order to be able to run the `make_pots` command via bin without laravel.
 */
class FileFinder extends CoreFileFinder {
	public function __construct( array $directories, array $excluded_directories = [], array $patterns = [ '*.php', '*.js' ] ) {
		$this->directories         = $directories;
		$this->excludedDirectories = $excluded_directories;
		$this->patterns            = $patterns;
	}

	/**
	 * Find all files that can contain translatable strings.
	 *
	 * @return Finder|null
	 */
	public function find() {
		$directories = $this->directories;

		$excludedDirectories = $this->excludedDirectories;

		$finder = new Finder();

		$finder = $finder->in( $directories );
		$finder = $finder->exclude( $excludedDirectories );

		foreach ( $this->patterns as $pattern ) {
			$finder->name( $pattern );
		}

		return $finder->files();
	}
}
