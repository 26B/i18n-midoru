<?php
$basedir = \dirname( __DIR__ );
require_once $basedir . '/vendor/autoload.php';

use TwentySixB\Translations\Input\Dataset;
use TwentySixB\Translations\Translations;

$array = new Dataset(
	[
		'plugin-content' => [
			'make_pots' => [
				'domain'      => 'plugin-content',
				'path'        => './wp-content/mu-plugins/plugin-content/language/',
				'source_path' => './wp-content/mu-plugins/plugin-content/',
			],
		],
	]
);

$t = new Translations( $array );
$t->make_pots();
