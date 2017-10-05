<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Command to approve a list of plugin usages
 */
class PluginApproveRunCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('plugin:approve')
			->setDescription(tr('Approve a list of plugin invocations/calls'))
			->addArgument(
				'pluginFingerprints',
				InputArgument::OPTIONAL,
				tr('List fingerprints of the plugin invocations/calls to approve separated by commas')
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				tr('Approve all plugin invocations/calls')
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$logger = new ConsoleLogger($output);

		$parserLib = \TikiLib::lib('parser');
		$pluginFingerprints = $input->getArgument('pluginFingerprints');
		$all = $input->getOption('all');

		if ($all) {
			$logger->info(tr('Approving all pending plugins'));
			$parserLib->approve_all_pending_plugins();
		} elseif ($pluginFingerprints) {
			$logger->info(tr('Approving a list of plugins'));
			foreach (explode(',', $pluginFingerprints) as $fingerprint) {
				$logger->debug(tr('Approving plugin %0', $fingerprint));
				$parserLib->approve_selected_pending_plugings($fingerprint);
			}
		}
		$logger->info(tr('Plugins approved with success'));
	}
}