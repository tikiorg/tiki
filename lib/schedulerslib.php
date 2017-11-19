<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class SchedulersLib extends TikiLib
{

	/**
	 * Let a list of schedulers
	 *
	 * @param null $schedulerId
	 * @param null $status
	 * @return array|mixed
	 */
	function get_scheduler($schedulerId = null, $status = null)
	{

		$schedulersTable = $this->table('tiki_scheduler');

		$conditions = [];

		if ($status) {
			$conditions['status'] = $status;
		}

		if ($schedulerId) {
			$conditions['id'] = $schedulerId;
			return $schedulersTable->fetchRow([], $conditions);
		}

		return $schedulersTable->fetchAll([], $conditions);
	}

	/**
	 * Save scheduler details
	 *
	 * @param $name
	 * @param $description (optional)
	 * @param $task
	 * @param $params (optional)
	 * @param $run_time
	 * @param $status
	 * @param $re_run
	 * @param $scheduler_id (optional)
	 */
	function set_scheduler($name, $description = null, $task, $params = null, $run_time, $status, $re_run, $scheduler_id = null)
	{

		$values = [
			'name' => $name,
			'description' => $description,
			'task' => $task,
			'params' => $params,
			'run_time' => $run_time,
			'status' => $status,
			're_run' => $re_run,
		];

		$schedulersTable = $this->table('tiki_scheduler');

		if (! $scheduler_id) {
			$schedulersTable->insert($values);
		} else {
			$schedulersTable->update($values, ['id' => $scheduler_id]);
		}
	}

	/**
	 * Get the info of the last scheduler runs
	 *
	 * @param $scheduler_id
	 *   The scheduler id
	 * @param int $limit
	 *   The number of runs to return
	 * @return array
	 */
	function get_scheduler_runs($scheduler_id, $limit = 10)
	{
		if (! is_numeric($limit)) {
			$limit = -1;
		}

		$schedulersRunTable = $this->table('tiki_scheduler_run');

		return $schedulersRunTable->fetchAll([], ['scheduler_id' => $scheduler_id], $limit, -1, ['id' => 'DESC']);
	}

	/**
	 * Get scheduler last run status
	 *
	 * @param $scheduler_id
	 * @return bool|mixed
	 */
	function get_run_status($scheduler_id)
	{
		$schedulersRunTable = $this->table('tiki_scheduler_run');
		return $schedulersRunTable->fetchOne('status', ['scheduler_id' => $scheduler_id], ['id' => 'DESC']);
	}

	/**
	 * Mark scheduler run as active (running)
	 *
	 * @param $scheduler_id
	 * @param null $start_time
	 * @return int
	 *   Start time
	 */
	function start_scheduler_run($scheduler_id, $start_time = null)
	{

		if (empty($start_time)) {
			$start_time = time();
		}

		$schedulersRunTable = $this->table('tiki_scheduler_run');
		$schedulersRunTable->insert([
			'scheduler_id' => $scheduler_id,
			'start_time' => $start_time,
			'status' => 'running'
		]);

		return $start_time;
	}

	/**
	 * Mark scheduler run as finished
	 *
	 * @param $scheduler_id
	 * @param $executionStatus
	 * @param $errorMessage
	 * @param $start_time
	 * @param null $end_time
	 * @return int|null
	 */
	function end_scheduler_run($scheduler_id, $executionStatus, $errorMessage, $start_time, $end_time = null)
	{

		if (empty($end_time)) {
			$end_time = time();
		}

		$schedulersRunTable = $this->table('tiki_scheduler_run');
		$schedulersRunTable->update([
			'status' => $executionStatus,
			'output' => $errorMessage,
			'end_time' => $end_time
		], [
			'scheduler_id' => $scheduler_id,
			'status' => 'running',
			'start_time' => $start_time
		]);

		return $end_time;
	}

	/**
	 * Remove the scheduler and its runs/logs
	 *
	 * @param $scheduler_id
	 */
	function remove_scheduler($scheduler_id)
	{

		$schedulersRunTable = $this->table('tiki_scheduler_run');
		$schedulersRunTable->delete(['scheduler_id' => $scheduler_id]);

		$schedulersTable = $this->table('tiki_scheduler');
		$schedulersTable->delete(['id' => $scheduler_id]);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action('Removed', $scheduler_id, 'scheduler');
	}
}
