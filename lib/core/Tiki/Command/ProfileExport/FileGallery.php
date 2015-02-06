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

class FileGallery extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:file-gallery')
			->setDescription('Export a file gallery definition')
			->addOption(
				'with-parents',
				null,
				InputOption::VALUE_NONE,
				'Includes all parents'
			)
			->addOption(
				'deep',
				null,
				InputOption::VALUE_NONE,
				'Includes all children'
			)
			->addArgument(
				'fileGallery',
				InputArgument::REQUIRED,
				'File Gallery ID'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$galId = $input->getArgument('fileGallery');
		$withParents = $input->getOption('with-parents');
		$deep = $input->getOption('deep');

		$writer = $this->getProfileWriter($input);

		$result = \Tiki_Profile_InstallHandler_FileGallery::export($writer, $galId, $withParents, $deep);

		if ($result) {
			$writer->save();
		} else {
			$output->writeln("File gallery not found: $galId");
		}
	}
}
