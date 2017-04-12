<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: NotificationDigestCommand.php 57969 2016-03-17 20:07:40Z jonnybradley $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListExecuteCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('list:execute')
			->setDescription('Performs Plugin ListExecute command on a particular page')
			->addArgument(
				'page',
				InputArgument::REQUIRED,
				'Page name where Plugin ListExecute is setup'
			)
			->addArgument(
				'action',
				InputArgument::REQUIRED,
				'Name of the action to be executed as defined on the target page'
			)
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$page = $input->getArgument('page');
		$action = $input->getArgument('action');

		$tikilib = \TikiLib::lib('tiki');
		if (! $pageInfo = $tikilib->get_page_info($page)) {
			$output->writeln("Page $page not found.");
			return false;
		}

		$_POST['list_action'] = $action;
		$_POST['objects'] = array('ALL');

		$tikilib->parse_data($pageInfo['data']);

		$output->writeln("Action $action executed on page $page.");
	}
}
