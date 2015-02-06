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

class AllModules extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:all-modules')
			->setDescription('Export all module definitions');

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$writer = $this->getProfileWriter($input);

		$list = \TikiDb::get()->table('tiki_modules')->fetchColumn('moduleId', array());

		foreach ($list as $moduleId) {
			\Tiki_Profile_InstallHandler_Module::export($writer, $moduleId);
		}

		$writer->save();
	}
}
