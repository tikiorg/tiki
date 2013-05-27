<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RecentChanges extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:recent-changes')
			->setDescription('List the recent changes in prevision of export')
			->addOption(
				'since',
				null,
				InputOption::VALUE_REQUIRED,
				'Date from which the actions should be read in the log, can either be a date or a relative time period'
			)
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if ($since = $input->getOption('since')) {
			$since = strtotime($since);
		}

		$since = $since ?: 0;

		$logs = \TikiDb::get()->table('tiki_actionlog');
		$actions = $logs->fetchAll(array(
			'timestamp' => 'lastModif',
			'action',
			'type' => 'objectType',
			'object',
			'detail' => 'comment',
		), array(
			'lastModif' => $logs->greaterThan($since),
		), -1, -1, 'lastModif_asc');

		$queue = new \Tiki_Profile_Writer_Queue;
		foreach ($actions as $action) {
			$queue->add($action);
		}

		$writer = $this->getProfileWriter($input);
		$queue->filterIncluded($writer);
		$queue->filterInstalled(new \Tiki_Profile_Writer_ProfileFinder);

		$output->writeln((string) $queue);
	}
}
