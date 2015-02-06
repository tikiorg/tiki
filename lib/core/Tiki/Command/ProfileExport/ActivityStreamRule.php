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

class ActivityStreamRule extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:activity-stream-rule')
			->setDescription('Export an activity stream rule')
			->addArgument(
				'rule',
				InputArgument::REQUIRED,
				'Rule ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$rule = $input->getArgument('rule');

		$writer = $this->getProfileWriter($input);

		if (\Tiki_Profile_InstallHandler_ActivityStreamRule::export($writer, $rule)) {
			$writer->save();
		} else {
			$output->writeln("<error>Rule not found: $rule</error>");
			return;
		}
	}
}
