<?php
namespace TwentySixB\Translations\Transformers;

use TwentySixB\Translations\Config\Project;

/**
 * Transformer for Jed formatted data.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
final class Jed implements Transformer {

	/**
	 * Wrap the json data with a `locale_data` key.
	 *
	 * @since  0.0.0
	 * @param  mixed $data
	 * @param  Project $config
	 * @return mixed
	 */
	public function transform( $data, Project $config ) {
		if ( $config->get_wrap_jed() ) {
			foreach ( $data as $key => $json ) {
				$data[ $key ] = json_encode(
					[
						'locale_data' => json_decode( $json, true ),
					],
					JSON_PRETTY_PRINT
				);
			}
		}
		return $data;
	}
}
