<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
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
			)
			->addOption(
				'cron',
				null,
				InputOption::VALUE_NONE,
				'Only output error messages'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $num_queries;

		$force = $input->getOption('force');
		if ($input->getOption('log')) {
			$log = 2;
		} else {
			$log = 0;
		}
		$cron = $input->getOption('cron');

		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');

		if ($force && $unifiedsearchlib->rebuildInProgress()) {
			if (! $cron) {
				$output->writeln('<info>Removing leftovers...</info>');
			}
			$unifiedsearchlib->stopRebuild();
		}

		if (! $cron) {
			$output->writeln('Started rebuilding index...');
		}

		if (! $cron) {
			list($engine, $version) = $unifiedsearchlib->getEngineAndVersion();
			if (!empty($engine)) {
				$engineMessage = 'Unified search engine: '. $engine;
				if (!empty($version)){
					$engineMessage .= ', version ' . $version;
				}
				$output->writeln($engineMessage);
			}
		}

		$timer = new \timer();
		$timer->start();

		$memory_peak_usage_before = memory_get_peak_usage();

		$num_queries_before = $num_queries;

		$result = $unifiedsearchlib->rebuild($log);

		// Also rebuild admin index
		\TikiLib::lib('prefs')->rebuildIndex();

		$queries_after = $num_queries;

		if ($result) {
			if (!$cron) {
				$output->writeln("Indexed");
				foreach($result as $key => $val) {
					$output->writeln("  $key: $val");
				}
				$output->writeln('Rebuilding index done');
				$output->writeln('Execution time: ' . FormatterHelper::formatTime($timer->stop()));
				$output->writeln('Current Memory usage: ' . FormatterHelper::formatMemory(memory_get_usage()));
				$output->writeln('Memory peak usage before indexing: ' . FormatterHelper::formatMemory($memory_peak_usage_before));
				$output->writeln('Memory peak usage after indexing: ' . FormatterHelper::formatMemory(memory_get_peak_usage()));
				$output->writeln('Number of queries: ' . ($queries_after - $num_queries_before));
			}
			return(0);
		} else {
			$errors = \Feedback::get();
			if (is_array($errors)) {
				foreach ($errors as $message) {
					$output->writeln("<info>$message</info>");
				}
				$output->writeln("\n<error>Search index rebuild failed. Last messages shown above.</error>");
				\TikiLib::lib('logs')->add_action('rebuild indexes', 'Search index rebuild failed.', 'system');
			}
			return(1);
		}
	}
}
