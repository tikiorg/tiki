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
		$schedLib = \TikiLib::lib('scheduler');
		$activeSchedulers = $schedLib->get_scheduler(null, 'active');

		$runTasks = array();
		$reRunTasks = array();

		foreach ($activeSchedulers as $scheduler) {
			// Check which tasks should run on time
			if ($this->is_time_cron($start_time, $scheduler['run_time'])) {
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

			$schedulerTask = new \Scheduler_Item(
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

	// From: http://www.binarytides.com/php-check-if-a-timestamp-matches-a-given-cron-schedule/
	private function is_time_cron($time, $cron)
	{
		$cron_parts = explode(' ', $cron);
		if (count($cron_parts) != 5) {
			return false;
		}

		list($min, $hour, $day, $mon, $week) = explode(' ', $cron);

		$to_check = array('min' => 'i', 'hour' => 'G', 'day' => 'j', 'mon' => 'n', 'week' => 'w');

		$ranges = array(
			'min' => '0-59',
			'hour' => '0-23',
			'day' => '1-31',
			'mon' => '1-12',
			'week' => '0-6',
		);

		foreach ($to_check as $part => $c) {
			$val = $$part;
			$values = array();

			/*
				For patters like 0-23/2
			*/
			if (strpos($val, '/') !== false) {
				//Get the range and step
				list($range, $steps) = explode('/', $val);

				//Now get the start and stop
				if ($range == '*') {
					$range = $ranges[$part];
				}
				list($start, $stop) = explode('-', $range);

				for ($i = $start; $i <= $stop; $i = $i + $steps) {
					$values[] = $i;
				}
			} /*
				For patters like :
				2
				2,5,8
				2-23
				*/
			else {
				$k = explode(',', $val);

				foreach ($k as $v) {
					if (strpos($v, '-') !== false) {
						list($start, $stop) = explode('-', $v);

						for ($i = $start; $i <= $stop; $i++) {
							$values[] = $i;
						}
					} else {
						$values[] = $v;
					}
				}
			}

			if (!in_array(date($c, $time), $values) and (strval($val) != '*')) {
				return false;
			}
		}

		return true;
	}
}
