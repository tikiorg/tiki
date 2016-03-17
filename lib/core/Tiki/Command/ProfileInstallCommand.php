<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileInstallCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('profile:apply')
			->setDescription('Apply a profile')
			->addArgument(
				'profile',
				InputArgument::REQUIRED,
				'Profile name'
			)
			->addArgument(
				'repository',
				InputArgument::OPTIONAL,
				'Repository',
				'profiles.tiki.org'
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Re-apply profiles when already applied.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$profileName = $input->getArgument('profile');
		$repository = $input->getArgument('repository');
		$force = $input->getOption('force');

		$profile = \Tiki_Profile::fromNames($repository, $profileName);

		if (! $profile) {
			$output->writeln('<error>Profile not found.</error>');
			return;
		}

		$tikilib = \TikiLib::lib('tiki');

		$installer = new \Tiki_Profile_Installer;
		$isInstalled = $installer->isInstalled($profile);

		if ($isInstalled && $force) {
			$installer->forget($profile);
			$isInstalled = false;
		}

		if (! $isInstalled) {
			$transaction = $tikilib->begin();
			if ($installer->install($profile)) {
				$transaction->commit();
				$output->writeln('Profile applied.');
			} else {
				$output->writeln("<error>Installation failed:</error>");

				foreach ($installer->getFeedback() as $error) {
					$output->writeln("<error>$error</error>");
				}
			}
		} else {
			$output->writeln('<info>Profile was already applied. Nothing happened.</info>');
		}
	}
}
