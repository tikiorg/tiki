<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Process\Process;

class Scheduler_Task_ShellCommandTask extends Scheduler_Task_CommandTask
{

	public function execute($params = null)
	{
		if (empty($params['shell_command'])) {
			$this->errorMessage = tra('Missing shell command to execute.');
			return false;
		}

		$process = new Process($params['shell_command']);
		$process->run();

		if ($success = $process->isSuccessful()) {
			$this->errorMessage = $process->getOutput();
		} else {
			$this->errorMessage = $process->getErrorOutput();
		}

		return $success;
	}

	public function getParams() {
		return array(
			'shell_command' => array(
				'name' => tra('Shell command'),
				'type' => 'text'
			),
		);
	}

}
