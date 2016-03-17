<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ProfileForgetCommand.php 45724 2013-04-26 17:33:23Z changi67 $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddonRemoveCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('addon:remove')
			->setDescription('Remove objects created by addon profile creation')
			->addArgument(
				'addon',
				InputArgument::REQUIRED,
				'Addon package vendor/name'
			)
			->addOption(
				'confirm',
				null,
				InputOption::VALUE_NONE,
				'Confirm deletion of objects'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$addon_utilities = new \TikiAddons_Utilities;

		$addonName = $input->getArgument('addon');
		if (strpos($addonName, '/') !== false && strpos($addonName, '_') === false) {
			$package = $addonName;
			$folder = str_replace('/', '_', $addonName);
		} else {
			$package = str_replace('_', '/', $addonName);
			$folder = $addonName;
		}

		$repository = 'file://addons/' . $folder . '/profiles';
		$confirm = $input->getOption('confirm');

		$uninstallInfo = json_decode(file_get_contents(TIKI_PATH . '/addons/' . $folder . '/uninstall.json'));
		$removeItems = $uninstallInfo->remove;
		$willRemove = false;
		foreach ($removeItems as $remove) {
			if (empty($remove->profile)) {
				$profile = '';
			} else {
				$profile = $remove->profile;
			}

			if (empty($remove->domain)) {
				$domain = '';
			} else {
				$domain = $remove->domain;
			}

			$objectId = $addon_utilities->getObjectId($folder, $remove->ref, $profile, $domain);
			if (is_array($objectId)) {
				$objectIds = $objectId;
			} else {
				$objectIds = [$objectId];
			}
			foreach ($objectIds as $objectId) {
				$objectType = $remove->type;
				if ($objectId) {
					if ($confirm) {
						$addon_utilities->removeObject($objectId, $objectType);
						$output->writeln("$objectType '$objectId' has been deleted.");
					} else {
						$output->writeln("<info>$objectType '$objectId' will be deleted.</info>");
					}
					$willRemove = true;
				}
			}
		}

		$installedProfiles = $addon_utilities->getInstalledProfiles($folder);

		if (!$confirm && ($willRemove || !empty($installedProfiles))) {
			$output->writeln("<error>There will be NO undo, and all data in the above objects will be deleted.</error>");
			$output->writeln("<info>Use the --confirm option to proceed with removal.</info>");
		} elseif (!$willRemove) {
			$output->writeln("<info>It looks like the objects for this addon have been removed already.</info>");
		}
		if (empty($installedProfiles)) {
			$output->writeln("<info>It looks like the profiles for this addon have been removed from addon registry already.</info>");
		}

		$tikilib = \TikiLib::lib('tiki');
		$installer = new \Tiki_Profile_Installer;

		if ($confirm) {
			foreach (array_keys($installedProfiles) as $profileName) {
				$profile = \Tiki_Profile::fromNames($repository, $profileName);

				if (! $profile) {
					$output->writeln("<error>Profile $profileName not found.</error>");
					continue;
				}

				$isInstalled = $installer->isInstalled($profile);

				if ($isInstalled) {
					$transaction = $tikilib->begin();
					$installer->forget($profile);
					$transaction->commit();
					$output->writeln("Profile $profileName forgotten.");
				} else {
					$output->writeln("<info>Profile $profileName was not installed or did not create any objects.</info>");
				}
				$addon_utilities->forgetProfileAllVersions($folder, $profileName);
				$output->writeln("Profile $profileName forgotten from addon registry.");
			}
		}
	}
}
