<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ProfileInstallCommand.php 47279 2013-08-26 14:48:36Z changi67 $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddonUpgradeCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('addon:upgrade')
			->setDescription('Upgrade profiles for addon to newer version')
			->addArgument(
				'addon',
				InputArgument::REQUIRED,
				'Addon package vendor/name'
			)
			->addOption(
				'ignoredepends',
				null,
				InputOption::VALUE_NONE,
				'Ignore dependencies.'
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
		$ignoredepends = $input->getOption('ignoredepends');
		$confirm = $input->getOption('confirm');

		if (empty(glob(TIKI_PATH . '/addons/' . $folder . '/profiles/*.yml'))) {
			$output->writeln("<error>No profiles found.</error>");
			return;
		}

		if (!$ignoredepends) {
			$addon_utilities->checkDependencies($folder);
		}

		$upgradeInfo = json_decode(file_get_contents(TIKI_PATH . '/addons/' . $folder . '/upgrade.json'));
		$validVersions = array();
		foreach ($upgradeInfo as $version => $info) {
			$validVersions[] = $version;
		}
		$config = null;
		$lastVersionInstalled = $addon_utilities->getLastVersionInstalled($folder);
		$reapplyProfiles = array();
		$forgetProfiles = array();
		$removeItems = array();
		foreach ($validVersions as $v) {
			if ($addon_utilities->checkVersionMatch($lastVersionInstalled,$v)) {
				$config = $upgradeInfo->$v;
				$removeItems = $config->remove;
				$forgetProfiles = $config->forget;
				$reapplyProfiles = $config->reapply;
				break;
			}
		}

		$addons = \TikiAddons::getInstalled();

		if (!$config) {
			if (strnatcmp($lastVersionInstalled, $addons[$package]->version) <= 0) {
				$output->writeln("<error>Currently installed version ($lastVersionInstalled) is already up to date.</error>");
			} else {
				$output->writeln("<error>No valid versions currently installed to upgrade found.</error>");
			}
			return false;
		}

		$installedProfiles = $addon_utilities->getInstalledProfiles($folder);
		$installedProfileNames = array_keys($installedProfiles);

		$willRemove = false;
		foreach ($removeItems as $remove) {
			if (empty($remove->profile)) {
				$profile = '';
			} else {
				$profile = $remove->profile;
			}
			$objectId = $addon_utilities->getObjectId($folder, $remove->ref, $profile);
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

		$tikilib = \TikiLib::lib('tiki');
		$installer = new \Tiki_Profile_Installer;

		// First forget profiles that need to be forgotten
		foreach ($forgetProfiles as $toForget) {
			if (in_array($toForget, $installedProfileNames)) {
				if ($confirm || (!$willRemove)) {
					$addon_utilities->forgetProfileAllVersions($folder, $toForget);
					$profile = \Tiki_Profile::fromNames($repository, $toForget);
					if (! $profile) {
						$output->writeln("<error>Profile $toForget not found.</error>");
					} else {
						$installer->forget($profile);
					}
				} else {
					$output->writeln("<info>The installed profile $toForget will be forgotten.</info>");
				}
			}
		}

		if (!$confirm && ($willRemove)) {
			$output->writeln("<error>There will be NO undo, and all data in the above objects will be deleted as part of the upgrade.</error>");
			$output->writeln("<info>Use the --confirm option to proceed with removal and upgrade.</info>");
		}

		if ($confirm || (!$willRemove)) {
			// Finally install profiles
			foreach (glob(TIKI_PATH . '/addons/' . $folder . '/profiles/*.yml') as $file) {

				$profileName = str_replace('.yml', '', basename($file));
				$profile = \Tiki_Profile::fromNames($repository, $profileName);

				if (in_array($profileName, $reapplyProfiles)) {
					$reapply = true;
				} else {
					$reapply = false;
				}

				if (! $profile) {
					$output->writeln("<error>Profile $profileName not found.</error>");
					continue;
				}

				$isInstalled = $installer->isInstalled($profile);

				if ($isInstalled && $reapply) {
					$installer->forget($profile);
					$isInstalled = false;
				}

				if (! $isInstalled) {
					$transaction = $tikilib->begin();
					if ($installer->install($profile)) {
						$addon_utilities->updateProfile($folder, $addons[$package]->version, $profileName);
						$transaction->commit();
						$output->writeln("Profile $profileName applied.");
					} else {
						$output->writeln("<error>Profile $profileName installation failed:</error>");

						foreach ($installer->getFeedback() as $error) {
							$output->writeln("<error>$error</error>");
						}
					}
				} else {
					$output->writeln("<info>Profile $profileName was already applied. Nothing happened.</info>");
				}
			}
		}
	}
}
