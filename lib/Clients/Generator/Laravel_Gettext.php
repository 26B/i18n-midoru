<?php

namespace TwentySixB\Translations\Clients\Generator;

use KKomelin\TranslatableStringExporter\Core\CodeParser;
use TwentySixB\Translations\Utils\FileFinder;

/**
 * Client class for handling generation of pot files using a string extractor for php and json files.
 */
class Laravel_Gettext extends Client {

	public function generate( array $args ) {
		// TODO: Remove domain from necessary args.
		$translations = $this->extract_strings( $args );
		$this->generate_file( $translations, (string) $args['destination'], $args );
	}

	private function extract_strings( $args ) : array {
		$finder  = new FileFinder( $args['source'] );  // TODO: Handle excluded directories.
		$parser  = new CodeParser();
		$strings = [];

        $files = $finder->find();
        foreach ( $files as $file ) {
            $strings = array_merge( $strings, $parser->parse( $file ) );
        }

        return array_unique( $strings );
	}

	private function glob_recursive( $pattern, $flags = 0 ) {
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->glob_recursive($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}

	private function generate_file( array $translations, string $file_path, array $args ) : void {
		$file_string = $this->file_headers( $args );
		foreach ( $translations as $translation ) {
			$file_string .= sprintf(
				"msgid \"%s\"\nmsgstr \"\"\n\n",
				strtr(
					$translation,
					[
						"\x00" => '',
						'\\' => '\\\\',
						"\t" => '\t',
						"\r" => '\r',
						"\n" => '\n',
						'"' => '\\"',
					]
				)
			);
		}

		file_put_contents( $file_path, $file_string );
	}

	private function file_headers( $args ) : string {
		return sprintf(
			"msgid \"\"
msgstr \"\"
\"Content-Transfer-Encoding: 8bit\\n\"
\"Content-Type: text/plain; charset=UTF-8\\n\"
\"POT-Creation-Date: %s\\n\"
\"X-Domain: %s\\n\"\n\n",
			date( 'c' ),
			$args['domain']
	);
	}
}
