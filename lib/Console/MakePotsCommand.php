<?php

namespace TwentySixB\Translations\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakePotsCommand extends Command {

	protected static $defaultName = 'make_pots';

	protected function configure() {
		$this->setDescription( 'Make pot files for translations.' )
			->setHelp( 'Make pot files for translations according to the configuration in i18n-midoru.json.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$t = new \TwentySixB\Translations\Translations();
		$t->make_pots();
		return 0;
	}
}
