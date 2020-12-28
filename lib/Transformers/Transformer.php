<?php
namespace TwentySixB\Translations\Transformers;

use TwentySixB\Translations\Config\Project;

/**
 * Interface for data transformers.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
interface Transformer {

	/**
	 * Transform the data according to the Project config.
	 *
	 * @since  0.0.0
	 * @param  mixed $data
	 * @param  Project $config
	 * @return mixed
	 */
	public function transform( $data, Project $config );
}
