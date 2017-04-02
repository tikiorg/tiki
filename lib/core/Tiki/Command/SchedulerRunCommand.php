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
use Scheduler_Utils;
use Scheduler_Item;
use TikiLib;

class SchedulerRunCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('scheduler:run')
			->setDescription('Run scheduled tasks');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$start_time = time();

		// Get all active schedulers
		$schedLib = TikiLib::lib('scheduler');
		$activeSchedulers = $schedLib->get_scheduler(null, 'active');

		$runTasks = array();
		$reRunTasks = array();

		foreach ($activeSchedulers as $scheduler) {
			// Check which tasks should run on time
			if (Scheduler_Utils::is_time_cron($start_time, $scheduler['run_time'])) {
				$runTasks[] = $scheduler;
				continue;
			}

			// Check which tasks should run if they failed previously (last execution)
			if ($scheduler['re_run']) {
				$reRunTasks[] = $scheduler;
				continue;
			}
		}

		foreach ($reRunTasks as $task) {

			$status = $schedLib->get_run_status($task['id']);
			if ($status == 'failed') {
				$runTasks[] = $task;
			}
		}

		foreach ($runTasks as $runTask) {

			$schedulerTask = new Scheduler_Item(
				$runTask['id'],
				$runTask['name'],
				$runTask['description'],
				$runTask['task'],
				$runTask['params'],
				$runTask['run_time'],
				$runTask['status'],
				$runTask['re_run']
			);

			$output->writeln('Start running ' . $runTask['name']);

			$result = $schedulerTask->execute();

			if ($result['status'] == 'failed') {
				$output->writeln("<error>Failed running:\n{$result['message']}</error>");
			} else {
				$output->writeln("<info>Finish running {$runTask['name']}</info>");
			}
		}
	}
}
