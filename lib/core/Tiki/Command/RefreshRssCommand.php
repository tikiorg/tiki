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

class RefreshRssCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('rss:refresh')
			->setDescription('Refresh incoming RSS feeds')
			->addArgument(
				'rssId',
				InputArgument::OPTIONAL,
				'ID of RSS module to refresh'
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				'Refresh all modules'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$all = $input->getOption('all');
		$rssId = $input->getArgument('rssId');

		$rsslib = \TikiLib::lib('rss');

		if ($all) {
			$modules = $rsslib->list_rss_modules(0, -1, '', '');
			foreach ($modules['data'] as $feed) {
				$output->writeln('<info>Starting.</info>');
				$rsslib->refresh_rss_module($feed['rssId']);
				$output->writeln('<info>Refreshed ' . $feed['rssId'] . ': ' . $feed['name'] . '.</info>');
			}
		} elseif ($rssId) {
			$output->writeln('<info>Starting.</info>');
			$rsslib->refresh_rss_module($rssId);
			$output->writeln('<info>Refreshed Feed ID ' . $rssId . '.</info>');
		}

		$output->writeln('<info>Done.</info>');
	}
}
