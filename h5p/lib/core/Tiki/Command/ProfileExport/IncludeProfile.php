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

class IncludeProfile extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:include-profile')
			->setDescription('Includes references from an other profile as valid objects')
			->addArgument(
				'repository',
				InputArgument::REQUIRED,
				'Profile repository'
			)
			->addArgument(
				'profile',
				InputArgument::REQUIRED,
				'Profile name'
			)
			->addOption(
				'full-references',
				null,
				InputOption::VALUE_NONE,
				'Include the repository path in the reference'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$repository = $input->getArgument('repository');
		$profile = $input->getArgument('profile');
		$full = $input->getOption('full-references');

		$writer = $this->getProfileWriter($input);

		$finder = new \Tiki_Profile_Writer_ProfileFinder;
		$symbols = $finder->getSymbols($repository, $profile);

		foreach ($symbols as $entry) {
			if ($full) {
				$reference = $writer->formatExternalReference($entry['symbol'], $profile, $repository);
			} else {
				$reference = $writer->formatExternalReference($entry['symbol'], $profile);
			}

			$writer->removeUnknown($entry['type'], $entry['id'], $reference);
		}

		$writer->save();
	}
}
