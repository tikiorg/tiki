<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class Scheduler_Task_CommandTask
{
	protected $errorMessage;

	abstract protected function execute($params = null);

	abstract protected function getParams();

	public function getOutput()
	{
		return $this->errorMessage;
	}

	public function getTaskName()
	{
		return str_replace('Scheduler_Task_', '', get_class($this));
	}

	/**
	 * Parse the POST params sent in the scheduler form
	 *
	 * @return string
	 */
	public function parseParams()
	{
		$params = [];
		$inputParams = $this->getParams();
		$taskName = strtolower($this->getTaskName());

		foreach ($inputParams as $key => $input) {
			$inputName = $taskName . '_' . $key;
			$params[$key] = $_POST[$inputName];
		}

		return json_encode($params);
	}
}
