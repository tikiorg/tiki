<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;

/**
 * Command to list all plugins usages approved/requiring approval
 */
class PluginListRunCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('plugin:list')
			->setDescription(tr('List all plugin invocations/calls'))
			->addOption(
				'pending',
				null,
				InputOption::VALUE_NONE,
				tr('Shows only invocations/calls pending approval')
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$onlyPending = $input->getOption('pending');

		$status = $onlyPending ? ['pending'] : ['accept', 'pending'];

		$parserLib = \TikiLib::lib('parser');

		$pluginList = $parserLib->listPluginsByStatus($status);
		$pluginTotal = count($pluginList);

		$table = new Table($output);
		$table->setHeaders(['Plugin', 'Location', 'Added by', 'Status']);
		$rows = [];

		if ($pluginTotal > 0) {
			foreach ($pluginList as $plugin) {
				$location = '';
				if (! empty($plugin['last_objectType'])) {
					$location = ucfirst($plugin['last_objectType']) . ": ";
				}
				$location .= $plugin['last_objectId'];
				$rows[] = [
					$plugin['fingerprint'],
					$location,
					$plugin['added_by'],
					$plugin['status'],
				];
			}
			$table->setRows($rows);
			$table->render();
		}
	}
}
