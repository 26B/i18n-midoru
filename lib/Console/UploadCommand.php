<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'upload')]

/**
 * Command for uploading translations.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class UploadCommand extends Command {

	/**
	 * Configure command.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	protected function configure() {
		$this->setDescription( 'Upload translations.' )
			->setHelp( 'Uploads translations according to the configuration in i18n-midoru.json.' )
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
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$t = new \TwentySixB\Translations\Translations();
		$t->upload( $input->getArgument( 'project_names' ) );
		return 0;
	}
}
