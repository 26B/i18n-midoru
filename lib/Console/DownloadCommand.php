<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for downloading translations.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class DownloadCommand extends Command {

	/**
	 * Command name.
	 *
	 * @since 0.0.0
	 * @var   string
	 */
	protected static $defaultName = 'download';

	/**
	 * Configure command.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	protected function configure() {
		$this->setDescription( 'Download translations.' )
			->setHelp( 'Downloads translations according to the configuration in i18n-midoru.json.' );
	}

	/**
	 * Execute command.
	 *
	 * @since  0.0.0
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$t = new \TwentySixB\Translations\Translations();
		$t->download();
		return 0;
	}
}
