<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Calendar.php 57969 2016-03-17 20:07:40Z jonnybradley $

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Calendar extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:calendar')
			->setDescription('Export a calendar')
			->addArgument(
				'calendar',
				InputArgument::REQUIRED,
				'Calendar ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$calendarId = $input->getArgument('calendar');

		$writer = $this->getProfileWriter($input);
		if (\Tiki_Profile_InstallHandler_Calendar::export($writer, $calendarId)) {
			$writer->save();
		} else {
			$output->writeln("<error>Calendar not found: $calendarId</error>");
			return;
		}
	}
}
