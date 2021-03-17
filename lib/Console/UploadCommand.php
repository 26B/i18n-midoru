<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends Command {

	protected static $defaultName = 'upload';

	protected function configure() {
		$this->setDescription( 'Upload translations.' )
			->setHelp( 'Uploads translations according to the configuration in i18n-midoru.json.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$t = new \TwentySixB\Translations\Translations();
		$t->upload();
		return 0;
	}
}
