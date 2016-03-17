<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
			->addOption(
				'ignore',
				null,
				InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Adds an object to the ignore list. Format: object_type:object_id'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if ($since = $input->getOption('since')) {
			$since = strtotime($since);
		}

		$ignoreList = array();
		foreach ($input->getOption('ignore') as $object) {
			if (preg_match("/^(?P<type>\w+):(?P<object>.+)$/", $object, $parts)) {
				$ignoreList[] = $parts;
			}
		}

		$since = $since ?: 0;

		$logs = \TikiDb::get()->table('tiki_actionlog');
		$actions = $logs->fetchAll(
			array(
				'timestamp' => 'lastModif',
				'action',
				'type' => 'objectType',
				'object',
				'detail' => 'comment',
			), array(
				'lastModif' => $logs->greaterThan($since),
			), -1, -1, 'lastModif_asc'
		);

		$queue = new \Tiki_Profile_Writer_Queue;
		foreach ($actions as $action) {
			$queue->add($action);
		}

		$writer = $this->getProfileWriter($input);

		if (count($ignoreList)) {
			foreach ($ignoreList as $entry) {
				$writer->addFake($entry['type'], $entry['object']);
			}

			$writer->save();
		}

		$queue->filterIncluded($writer);
		$queue->filterInstalled(new \Tiki_Profile_Writer_ProfileFinder);

		$output->writeln((string) $queue);
	}
}
