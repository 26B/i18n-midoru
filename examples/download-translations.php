<?php

// FIXME: Update autoload path.
$basedir = \dirname( __DIR__ );
require_once $basedir . '/vendor/autoload.php';

use TwentySixB\Translations\Input\CLI;
use TwentySixB\Translations\Input\Dataset;
use TwentySixB\Translations\Input\File;
use TwentySixB\Translations\Translations;

$array = new Dataset(
	[
		'plugin-content' => [
			'export' => [
				'locale'      => [ 'pt_PT', 'fr_FR' ],
				'type'        => 'plugin',
				'domain'      => 'plugin-content',
				'output_path' => './lib/',
				'ext'         => 'po',
				'format'      => 'gettext',
			],
		],
		'plugin-agenda' => [
			'export' => [
				'locale'      => [ 'pt_PT' ],
				'type'        => 'plugin',
				'domain'      => 'plugin-agenda',
				'output_path' => './lib/',
				'ext'         => 'po',
				'format'      => 'gettext',
			],
		],
	]
);
$cli = new CLI();

$t = new Translations( $array, $cli );
$t->download();
