<?php

namespace TwentySixB\Translations\Utils;

use KKomelin\TranslatableStringExporter\Core\CodeParser as CoreCodeParser;

/**
 * Code Parser, used for extracting strings from Laravel projects.
 *
 * Extended to avoid use of `config` in order to be able to run the `make_pots` command via bin without laravel.
 */
class CodeParser extends CoreCodeParser {

	public function __construct(
		array $functions = [ '__', '_t', '@lang' ],
		bool $allow_newlines = false
	) {
		$this->functions = $functions;
		$this->patterns  = [];
		foreach ( $this->functions as $func ) {
			$pattern = str_replace('[FUNCTIONS]', $func, $this->basePattern);
			if ( $allow_newlines ) {
				$pattern .= 's';
			}
			$this->patterns[ $pattern ] = null;
		}
	}
}
