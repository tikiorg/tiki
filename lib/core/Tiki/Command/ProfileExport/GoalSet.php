<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ActivityRuleSet.php 47430 2013-09-12 17:45:17Z lphuberdeau $

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GoalSet extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:goal-set')
			->setDescription('Export all goals into a set')
			;

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$writer = $this->getProfileWriter($input);

		if (\Tiki_Profile_InstallHandler_GoalSet::export($writer)) {
			$writer->save();
		}
	}
}
