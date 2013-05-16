<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Finalize extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:finalize')
			->setDescription('Clean-up the working profile of intermediate data')
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Write static references even if they are unknown'
			)
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force = $input->getOption('force');

		$writer = $this->getProfileWriter($input);
		$remaining = $writer->getUnknownObjects();

		if ($force) {
			foreach ($remaining as $entry) {
				$writer->removeUnknown($entry['type'], $entry['id'], $entry['id']);
			}
		} else {
			$objects = implode("\n", array_map(function ($entry) {
				return "* {$entry['type']} - {$entry['id']}";
			}, $remaining));
			$output->writeln("<error>Some of the remaining objects are unknown:\n$objects\n\nConsider adding them to the profile or use --force to write them directly (profile may not work in all environments).</error>");
			return;
		}

		$writer->clean();
		$writer->save();

		unlink("profiles/info.ini");
	}
}
