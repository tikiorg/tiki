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

class WikiPage extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:wiki-page')
			->setDescription('Export a wiki page')
			->addArgument(
				'page',
				InputArgument::REQUIRED,
				'Page name'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$page = $input->getArgument('page');

		$writer = $this->getProfileWriter($input);
		if (\Tiki_Profile_InstallHandler_WikiPage::export($writer, $page)) {
			$writer->save();
		} else {
			$output->writeln("<error>Page not found: $page</error>");
			return;
		}
	}
}
