<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Psr\Log\LoggerInterface;

class Scheduler_Item
{

	public $id;
	public $name;
	public $description;
	public $task;
	public $params;
	public $run_time;
	public $status;
	public $re_run;
	private $logger;

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';

	static $availableTasks = [
		'ConsoleCommandTask' => 'ConsoleCommand',
		'ShellCommandTask' => 'ShellCommand',
		'HTTPGetCommandTask' => 'HTTPGetCommand',
	];

	public function __construct($id, $name, $description, $task, $params, $run_time, $status, $re_run, LoggerInterface $logger)
	{
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->task = $task;
		$this->params = $params;
		$this->run_time = $run_time;
		$this->status = $status;
		$this->re_run = $re_run;
		$this->logger = $logger;
	}

	public static function getAvailableTasks()
	{
		return self::$availableTasks;
	}

	/**
	 * @return array
	 */
	public function execute()
	{

		$schedlib = TikiLib::lib('scheduler');

		$status = $schedlib->get_run_status($this->id);

		$this->logger->info('Scheduler last run status: ' . $status);

		if ($status == 'running') {
			return [
				'status' => 'failed',
				'message' => tra('Scheduler task already running.')
			];
		}

		$this->logger->info('Task: ' . $this->task);

		$class = 'Scheduler_Task_' . $this->task;
		if (! class_exists($class)) {
			return [
				'status' => 'failed',
				'message' => $class . ' not found.',
			];
		}

		$startTime = $schedlib->start_scheduler_run($this->id);
		$this->logger->debug("Start time: " . $startTime);

		$params = json_decode($this->params, true);
		$this->logger->debug("Task params: " . $this->params);

		if ($params === null && ! empty($this->params)) {
			return [
				'status' => 'failed',
				'message' => tra('Unable to decode task params.')
			];
		}

		$task = new $class($this->logger);
		$result = $task->execute($params);

		$executionStatus = $result ? 'done' : 'failed';
		$outputMessage = $task->getOutput();

		$endTime = $schedlib->end_scheduler_run($this->id, $executionStatus, $outputMessage, $startTime);
		$this->logger->debug("End time: " . $endTime);

		return [
			'status' => $executionStatus,
			'message' => $outputMessage,
		];
	}
}
