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

class ArticleType extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:article-type')
			->setDescription('Export an article type definition')
			->addArgument(
				'type',
				InputArgument::REQUIRED,
				'Type Name'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument('type');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_ArticleType::export($writer, $name);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("Type not found: $name");
		}
	}
}
