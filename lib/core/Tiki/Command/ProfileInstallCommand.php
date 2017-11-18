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
				tr('Profile name')
			)
			->addArgument(
				'repository',
				InputArgument::OPTIONAL,
				'Repository',
				tr('profiles.tiki.org')
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				tr('Re-apply profiles when already applied.')
			)
			->addOption(
				'dry-run',
				null,
				InputOption::VALUE_NONE,
				tr('Return to the user information about what is going to be applied')
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$profileName = $input->getArgument('profile');
		$repository = $input->getArgument('repository');
		$force = $input->getOption('force');
		$dryRun = $input->getOption('dry-run');

		$profile = \Tiki_Profile::fromNames($repository, $profileName);

		if (! $profile) {
			$output->writeln('<error>Profile not found.</error>');
			return;
		}

		if (! $profile->validateNamedObjectsReferences()) { // sanity check on the Named Objects references
			$output->writeln('<error>' . tr('Some of the named object references in the profile are invalid') . '</error>');
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
			if ($installer->install($profile, 'all', $dryRun) && ! $dryRun) {
				$transaction->commit();
				$output->writeln(tr('Profile applied.'));
			} else {
				if (! $dryRun) {
					$output->writeln("<error>" . tr('Installation failed:') . "</error>");

					foreach ($installer->getFeedback() as $error) {
						$output->writeln("<error>$error</error>");
					}
				} else {
					$output->writeln(tr('Dry-run for profile: ') . $profile->profile);

					foreach ($installer->getTrackProfileChanges() as $profileChanges) {
						if (is_array($profileChanges)) {
							$type = $profileChanges['type'];
							$newValue = ! empty($profileChanges['new']) ? $profileChanges['new'] : 'n';
							$oldValue = ! empty($profileChanges['old']) ? $profileChanges['old'] : 'n';
							$description = ! empty($profileChanges['description']) ? $profileChanges['description'] : '';

							switch ($type) {
								case 'permission':
									$msg = tr('Permission removed: ') . $description[0];

									if ($newValue == 'y') {
										$msg = tr('Permission added: ') . $description[0];
									}

									$output->writeln($msg);
									break;

								case 'user':
									$msg = tr('User modified: ') . $description;

									if ($oldValue == 'n') {
										$msg = tr('User added: ') . $description;
									}

									$output->writeln($msg);
									break;

								case 'group':
									$msg = tr('Group modified: ') . $description;

									if ($oldValue == 'n') {
										$msg = tr('Group added: ') . $description;
									}

									$output->writeln($msg);
									break;

								case 'preference':
									$output->writeln(tr('Preference set: %0= %1 old value= %2', $description, $newValue, $oldValue));
									break;

								case 'installer':
									$output->writeln(tr('Installer added: ') . $description);
									break;
							}
						} else {
							$output->writeln($profileChanges);
						}
					}
				}
			}
		} else {
			$output->writeln('<info>' . tr('Profile was already applied. Nothing happened.') . '</info>');
		}
	}
}
