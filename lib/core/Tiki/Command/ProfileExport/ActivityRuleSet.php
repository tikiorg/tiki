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

class ActivityRuleSet extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:activity-rule-set')
			->setDescription('Export all activity stream rules into a set')
			;

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$writer = $this->getProfileWriter($input);

		if (\Tiki_Profile_InstallHandler_ActivityRuleSet::export($writer)) {
			$writer->save();
		}
	}
}
