<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use TikiLib;

class GoalCheckCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('goal:check')
			->setDescription('Reviews all active goals and assigns rewards.')
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $prefs;

		if ($prefs['goal_enabled'] != 'y') {
			$output->writeln('<error>Goals not enabled.</error>');
			return;
		}

		// Set-up reporting for achieved goals
		TikiLib::events()->bind('tiki.goal.reached', function ($args) use ($output) {
			$output->writeln(tr('%0 reached for %1 (%2)', $args['name'], $args['user'] ?: $args['group'], $args['goalType']));
		});

		$goallib = TikiLib::lib('goal');
		$goallib->evaluateAllGoals();
	}
}

