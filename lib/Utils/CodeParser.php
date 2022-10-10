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
		$this->pattern   = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);
		if ( $allow_newlines ) {
			$this->pattern .= 's';
		}
	}
}
