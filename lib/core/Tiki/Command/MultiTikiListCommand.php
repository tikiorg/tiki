<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MultiTikiListCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('multitiki:list')
			->setDescription('List MultiTikis in a path')
			->addArgument('path',
				InputArgument::OPTIONAL,
				'path to the Tiki instance to list (defaults to this one if absent)'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('path');

		if (! $path) {
			$path = getcwd();
		}

		$virtuals = $path . '/db/virtuals.inc';

		$list = [];

		if (is_file($virtuals)) {
			$list = file($virtuals);
		}
		if ($list) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln("<info>Multitikis in $path</info>");
			}
			foreach ($list as $multi) {
				$output->writeln(trim($multi));
			}
		} else {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln("<info>No multitikis found in $path</info>");
			}
		}
	}
}
