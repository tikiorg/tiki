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

class Article extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:article')
			->setDescription('Export an article definition')
			->addOption(
				'with-topic',
				null,
				InputOption::VALUE_NONE,
				'Includes article topic'
			)
			->addOption(
				'with-type',
				null,
				InputOption::VALUE_NONE,
				'Includes article type'
			)
			->addArgument(
				'article',
				InputArgument::REQUIRED,
				'Article ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('article');
		$withTopic = $input->getOption('with-topic');
		$withType = $input->getOption('with-type');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_Article::export($writer, $id, $withTopic, $withType);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("Article not found: $id");
		}
	}
}
