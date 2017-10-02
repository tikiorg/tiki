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
			->setDescription('List all plugins usages approved/requiring approval')
			->addOption(
				'pending',
				null,
				InputOption::VALUE_NONE,
				'Shows only pending approval'
			);

	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$onlyPending = $input->getOption('pending');

		$status = $onlyPending ? ['pending'] : ['accept', 'pending'];

		$parserLib = \TikiLib::lib('parser');

		$pluginList = $parserLib->list_plugins_by_status($status);
		$pluginTotal = count($pluginList);

		$table = new Table($output);
		$table->setHeaders(array('Plugin', 'Location', 'Added by', 'Status'));
		$rows = array();

		if ($pluginTotal > 0) {
			foreach ($pluginList as $plugin) {
				$rows[] = array(
					$plugin['fingerprint'],
					ucfirst($plugin['last_objectType']).(empty($plugin['last_objectType']) ? "" : ": ").$plugin['last_objectId'],
					$plugin['added_by'],
					$plugin['status'],
				);
			}
			$table->setRows($rows);
			$table->render();
		}
	}
}