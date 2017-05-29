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
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LogLevel;

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

		$verbosityLevelMap = array(
			LogLevel::ERROR => OutputInterface::OUTPUT_NORMAL,
			LogLevel::NOTICE => OutputInterface::OUTPUT_NORMAL,
			LogLevel::INFO   => OutputInterface::VERBOSITY_VERY_VERBOSE,
			LogLevel::DEBUG   => OutputInterface::VERBOSITY_DEBUG,
		);

		$logger = new ConsoleLogger($output, $verbosityLevelMap);

		$manager = new \Scheduler_Manager($logger);
		$manager->run();
	}
}
