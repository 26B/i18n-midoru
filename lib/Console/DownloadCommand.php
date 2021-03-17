<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command {

	protected static $defaultName = 'download';

	protected function configure() {
		$this->setDescription( 'Download translations.' )
			->setHelp( 'Downloads translations according to the configuration in i18n-midoru.json.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$t = new \TwentySixB\Translations\Translations();
		$t->download();
		return 0;
	}
}
