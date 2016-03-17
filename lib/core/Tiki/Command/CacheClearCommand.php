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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('cache:clear')
			->setDescription('Clear Tiki caches')
			->addArgument(
				'cache',
				InputArgument::OPTIONAL,
				'Type of cache to clear (public, private, templates, modules, all)',
				'all'
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				'Clear all caches'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$all = $input->getOption('all');
		$type = $input->getArgument('cache');

		$cachelib = \TikiLib::lib('cache');

		if ($all) {
			$output->writeln('Clearing all caches');
			$cachelib->empty_cache();
		} else {
			switch ($type) {
			case 'public':
				$output->writeln('Clearing public caches');
				return $cachelib->empty_cache('temp_public');
			case 'private':
				$output->writeln('Clearing private caches');
				return $cachelib->empty_cache('temp_cache');
			case 'templates':
				$output->writeln('Clearing template caches');
				return $cachelib->empty_cache('templates_c');
			case 'modules':
				$output->writeln('Clearing module caches');
				return $cachelib->empty_cache('modules_cache');
			case 'all':
				$output->writeln('Clearing all caches');
				return $cachelib->empty_cache();
			case '':
				return $output->writeln('Missing parameter.');
			default:
				$output->writeln('<error>Invalid cache requested.</error>');
			}
		}
	}
}
