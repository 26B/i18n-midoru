<?php

// FIXME: Update autoload path.
$basedir = \dirname( __DIR__ );
require_once $basedir . '/vendor/autoload.php';

use TwentySixB\Translations\Translations;

// Uses the i18n-midoru.json for the config. See i18n-midoru.json.example for a config example.
$t = new Translations();
$t->upload();
