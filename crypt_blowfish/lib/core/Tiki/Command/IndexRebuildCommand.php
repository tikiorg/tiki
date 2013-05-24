<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexRebuildCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('index:rebuild')
			->setDescription('Fully rebuild the unified search index')
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Destroy failed indexes prior to rebuild'
			)
			->addOption(
				'log',
				null,
				InputOption::VALUE_NONE,
				'Generate a log of the indexed documents, useful to track down failures or memory issues'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force = $input->getOption('force');
		$log = $input->getOption('log');

		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');

		if ($force && $unifiedsearchlib->rebuildInProgress()) {
			$output->writeln('<info>Removing leftovers...</info>');
			$unifiedsearchlib->stopRebuild();
		}

		$output->writeln('Started rebuilding index...');

		$result = $unifiedsearchlib->rebuild($log);

		if ($result) {
			$output->writeln('Rebuilding index done');
		} else {
			$errlib = \TikiLib::lib('errorreport');

			foreach ($errlib->get_errors() as $message) {
				$output->writeln("<error>$message</error>");
			}
		}
	}
}
