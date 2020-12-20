<?php

// FIXME: Update autoload path.
$basedir = \dirname( __DIR__ );
require_once $basedir . '/vendor/autoload.php';

use TwentySixB\Translations\Clients\Service\Localise;
use TwentySixB\Translations\Input\CLI;
use TwentySixB\Translations\Input\Dataset;
use TwentySixB\Translations\Input\File;
use TwentySixB\Translations\Translations;

//TODO: make this example correct for upload
$array = new Dataset(
	[
		'plugin-content' => [
			'export' => [
				'client' => Localise::class,
				'locale' => [ 'pt_PT', 'fr_FR' ],
				'type'   => 'plugin',
				'domain' => 'plugin-content',
				'path'   => './lib/',
				'ext'    => 'po',
				'format' => 'gettext',
			],
			'make_pots' => [
				'client' => WP_I18n::class,
			]
		],
		// 'plugin-agenda' => [
		// 	'export' => [
		// 		'locale' => [ 'pt_PT' ],
		// 		'type'   => 'plugin',
		// 		'domain' => 'plugin-agenda',
		// 		'path'   => './lib/',
		// 		'ext'    => 'po',
		// 		'format' => 'gettext',
		// 	],
		// ],
	]
);
$cli = new CLI();

$t = new Translations( $array ); //, $cli );
$t->download();
