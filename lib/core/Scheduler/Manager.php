<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Psr\Log\LoggerInterface;

class Scheduler_Manager
{

	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function run() {

		$start_time = time();

		// Get all active schedulers
		$schedLib = TikiLib::lib('scheduler');
		$activeSchedulers = $schedLib->get_scheduler(null, 'active');

		$this->logger->info(sprintf("Found %d active scheduler(s).", sizeof($activeSchedulers)));

		$runTasks = array();
		$reRunTasks = array();

		foreach ($activeSchedulers as $scheduler) {
			try {
				// Check which tasks should run on time
				if (Scheduler_Utils::is_time_cron($start_time, $scheduler['run_time'])) {
					$runTasks[] = $scheduler;
					$this->logger->info(sprintf("Run scheduler %s", $scheduler['name']));
					continue;
				}
			} catch (\Scheduler\Exception\CrontimeFormatException $e) {
				$this->logger->error(sprintf(tra("Skip scheduler %s - %s"), $scheduler['name'], $e->getMessage()));
				continue;
			}

			// Check which tasks should run if they failed previously (last execution)
			if ($scheduler['re_run']) {
				$reRunTasks[] = $scheduler;
				continue;
			}

			$this->logger->info(sprintf("Skip scheduler %s - Not scheduled to run at this time", $scheduler['name']));
		}

		foreach ($reRunTasks as $task) {

			$status = $schedLib->get_run_status($task['id']);
			if ($status == 'failed') {
				$this->logger->info(sprintf("Re-run scheduler %s - Last run has failed", $scheduler['name']));
				$runTasks[] = $task;
			}

			$this->logger->info(sprintf("Skip scheduler %s - Last run failed but not marked to re-run", $scheduler['name']));
		}

		if (empty($runTasks)) {
			$this->logger->notice("No active schedulers were found to run at this time.");
		} else {
			//$output->writeln(sprintf("Total of %d schedulers to run.", sizeof($runTasks)), OutputInterface::VERBOSITY_VERY_VERBOSE);
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
				$runTask['re_run'],
				$this->logger
			);

			$this->logger->notice(sprintf(tra('***** Running scheduler %s *****'), $schedulerTask->name));
			$result = $schedulerTask->execute();

			if ($result['status'] == 'failed') {
				$this->logger->error(sprintf(tra("***** Scheduler %s - FAILED *****\n%s"), $schedulerTask->name, $result['message']));
			} else {
				$this->logger->notice(sprintf(tra("***** Scheduler %s - OK *****"), $schedulerTask->name));
			}
		}
	}


}
