<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tiki\Command\Application;

class Scheduler_Task_ConsoleCommandTask extends Scheduler_Task_CommandTask
{

	public function execute($params = null)
	{
		if (empty($params['console_command'])) {
			$this->errorMessage = tra('Missing parameters to run the command.');
			return false;
		}

		$consoleParams = 'console.php ' . $params['console_command'];
		$args = $this->parseConsoleParams($consoleParams);

		$commandName = $args[1];

		try {
			$console = Application::getInstance();
			$command = $console->find($commandName);

			$input = new ArgvInput($args);
			$input->setInteractive(false);

			$output = new BufferedOutput();
			$statusCode = $command->run($input, $output);

			$content = $output->fetch();
			$this->errorMessage = $content;

			return $statusCode === 0;
		} catch (Exception $e) {
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	private function parseConsoleParams($params) {

		preg_match_all ('/(?<=^|\s)([\'"]?)(.+?)(?<!\\\\)\1(?=$|\s)/', $params, $args);

		return $args[2];
	}

	public function getParams() {
		return array(
			'console_command' => array(
				'name' => tra('Console command'),
				'type' => 'text'
			),
		);
	}

}
