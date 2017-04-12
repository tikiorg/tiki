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

class RssClearCacheCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('rss:clear')
			->setDescription('Clear incoming RSS feed cache')
			->addArgument(
				'rssId',
				InputArgument::OPTIONAL,
				'ID of RSS module cache to clear'
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				'Clear all modules caches'
			)
			->addOption(
				'olderthan',
				'o',
				InputOption::VALUE_OPTIONAL,
				'Oldest item in seconds to keep'
			)
			->addOption(
				'olderthandays',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Oldest item in days to keep'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$all = $input->getOption('all');
		$rssId = $input->getArgument('rssId');
		$olderthan = $input->getOption('olderthandays');
		if ($olderthan) {
			$olderthan = $olderthan * 24 * 3600;
		} else {
			$olderthan = $input->getOption('olderthan');
		}

		$rsslib = \TikiLib::lib('rss');

		if ($all) {
			$modules = $rsslib->list_rss_modules(0, -1, '', '');
			foreach ($modules['data'] as $feed) {
				$output->writeln('<info>Starting.</info>');
				$rsslib->clear_rss_cache($feed['rssId'], $olderthan);
				$output->writeln('<info>Cleared Feed ID ' . $feed['rssId'] . ': ' . $feed['name'] . '.</info>');
			}
		} elseif ($rssId) {
			$output->writeln('<info>Starting.</info>');
			$rsslib->clear_rss_cache($rssId, $olderthan);
			$output->writeln('<info>Cleared Feed ID ' . $rssId . '.</info>');
		} else {
			$output->writeln('<error>No rssId or --all parameter specified</error>');
		}

		$output->writeln('<info>Done.</info>');
	}
}
