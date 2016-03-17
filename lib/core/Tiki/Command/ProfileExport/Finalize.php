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
			->addOption(
				'dry-run',
				null,
				InputOption::VALUE_NONE,
				'Do not save changes. Only verify integrity.'
			)
			->addOption(
				'dump',
				null,
				InputOption::VALUE_NONE,
				'Write the profile content to the output.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force = $input->getOption('force');

		$writer = $this->getProfileWriter($input);
		$remaining = $writer->getUnknownObjects();

		$process = true;

		if ($force) {
			foreach ($remaining as $entry) {
				$writer->removeUnknown($entry['type'], $entry['id'], $entry['id']);
			}
		} elseif (count($remaining)) {
			$objects = implode(
				"\n",
				array_map(
					function ($entry) {
						return "* {$entry['type']} - {$entry['id']}";
					},
					$remaining
				)
			);
			$output->writeln("<error>Some of the remaining objects are unknown:\n$objects\n\nConsider adding them to the profile or use --force to write them directly (profile may not work in all environments).\n</error>");

			$profileFinder = new \Tiki_Profile_Writer_ProfileFinder;
			foreach ($remaining as $entry) {
				$profileFinder->lookup($entry['type'], $entry['id']);
			}
			$profiles = $profileFinder->getProfiles();

			if (count($profiles)) {
				$commands = implode(
					"\n",
					array_map(
						function ($profile) {
							return "* <info>profile:export:include-profile</info> {$profile['repository']} {$profile['profile']}";
						},
						$profiles
					)
				);
				$output->writeln("\n<info>It would seem like some pre-installed profiles cover unknown objects.</info>\n\nYou can run the following commands to include the references (try one at a time):\n$commands\n\nAdd the <info>--full-references</info> flag if the current profile will not be hosted in the same repository.");
			}
			$process = false;
		}

		if ($process && ! $input->getOption('dry-run')) {
			$writer->clean();
			$writer->save();

			unlink("profiles/info.ini");
		}

		if ($input->getOption('dump')) {
			$output->writeln($writer->dump());
		}
	}
}
