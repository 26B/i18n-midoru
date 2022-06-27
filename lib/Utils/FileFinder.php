<?php

namespace TwentySixB\Translations\Utils;

use KKomelin\TranslatableStringExporter\Core\FileFinder as CoreFileFinder;

/**
 * File finder.
 */
class FileFinder extends CoreFileFinder {
	public function __construct( array $directories, array $excluded_directories = [], array $patterns = [ '*.php', '*.js' ] ) {
		$this->directories         = $directories;
		$this->excludedDirectories = $excluded_directories;
		$this->patterns            = $patterns;
	}
}
