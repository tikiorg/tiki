<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TrackerField extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:tracker-field')
			->setDescription('Export a tracker field definition')
			->addArgument(
				'tracker-field',
				InputArgument::REQUIRED,
				'Tracker field ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$fieldId = $input->getArgument('tracker-field');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_TrackerField::export($writer, $fieldId);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("Tracker field not found: $fieldId");
		}
	}
}
