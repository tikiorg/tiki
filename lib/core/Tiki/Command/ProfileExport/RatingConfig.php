<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RatingConfig extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:rating-config')
			->setDescription('Export an advanced rating configuration')
			->addArgument(
				'config',
				InputArgument::REQUIRED,
				'Configuration ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('config');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_RatingConfig::export($writer, $id);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("Configuration not found: $id");
		}
	}
}
