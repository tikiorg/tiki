<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';

	static $availableTasks = array(
		'ConsoleCommandTask' => 'ConsoleCommand',
		'ShellCommandTask' => 'ShellCommand',
		'HTTPGetCommandTask' => 'HTTPGetCommand',
	);

	public function __construct($id, $name, $description, $task, $params, $run_time, $status, $re_run)
	{
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->task = $task;
		$this->params = $params;
		$this->run_time = $run_time;
		$this->status = $status;
		$this->re_run = $re_run;
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

		if ($status == 'running') {
			// @todo add register to log
			return array(
				'status' => 'failed',
				'message' => tra('Scheduler task already running.')
			);
		}

		$class = 'Scheduler_Task_' . $this->task;
		if (class_exists($class)) {
			$task = new $class();

			$start_time = $schedlib->start_scheduler_run($this->id);

			$params = json_decode($this->params, true);
			$result = $task->execute($params);

			$executionStatus = $result ? 'done' : 'failed';
			$outputMessage = $task->getOutput();

			$schedlib->end_scheduler_run($this->id, $executionStatus, $outputMessage, $start_time);

			return array(
				'status' => $executionStatus,
				'message' => $outputMessage,
			);
		}

		return array(
			'status' => 'failed',
			'message' => $class . ' not found.',
		);
	}
}