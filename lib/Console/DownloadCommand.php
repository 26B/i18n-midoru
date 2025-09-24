<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'download')]

/**
 * Command for downloading translations.
 *
 * @since      0.0.0
 * @package    TwentySixB\Translations\Console
 * @subpackage TODO:
 * @author     TODO:
 */
class DownloadCommand extends Command {

	/**
	 * Configure command.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	protected function configure() {
		$this->setDescription( 'Download translations.' )
			->setHelp( 'Downloads translations according to the configuration in i18n-midoru.json.' )
			->addArgument(
				'project_names',
				InputArgument::IS_ARRAY,
				'Wanted project names. By default, every project'
			);
	}

	/**
	 * Execute command.
	 *
	 * @since  0.0.0
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$t = new \TwentySixB\Translations\Translations();
		$t->download( $input->getArgument( 'project_names' ) );
		return 0;
	}
}
