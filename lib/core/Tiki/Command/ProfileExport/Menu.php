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

class Menu extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:menu')
			->setDescription('Export a menu definition')
			->addArgument(
				'menu',
				InputArgument::REQUIRED,
				'Menu ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$menuId = $input->getArgument('menu');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_Menu::export($writer, $menuId);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("Menu not found: $menuId");
		}
	}
}
