<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		foreach ($removeItems as $remove) {
			if (empty($remove->profile)) {
				$profile = '';
			} else {
				$profile = $remove->profile;
			}
			$objectId = $addon_utilities->getObjectId($folder, $remove->type, $remove->ref, $profile);
			$objectType = $remove->type;
			if ($confirm) {
				$addon_utilities->removeObject($folder, $remove->type, $remove->ref, $profile);
				$output->writeln("$objectType '$objectId' has been deleted.");
			} else {
				$output->writeln("<info>$objectType '$objectId' will be deleted.</info>");
			}
		}
		if (!$confirm) {
			$output->writeln("<error>There will be NO undo. Use the confirm option to proceed with removal.</error>");
		}

		if ($confirm) {
			$installedProfiles = $addon_utilities->getInstalledProfiles($folder);
			foreach (array_keys($installedProfiles) as $profileName) {
				$profile = \Tiki_Profile::fromNames($repository, $profileName);

				if (! $profile) {
					$output->writeln("<error>Profile $profileName not found.</error>");
					continue;
				}

				$tikilib = \TikiLib::lib('tiki');

				$installer = new \Tiki_Profile_Installer;
				$isInstalled = $installer->isInstalled($profile);

				$addons = \TikiAddons::getInstalled();

				if ($isInstalled) {
					$transaction = $tikilib->begin();
					$installer->forget($profile);
					$transaction->commit();
					$output->writeln("Profile $profileName forgotten.");
				} else {
					$output->writeln("<info>Profile $profileName was not installed or did not create any objects.</info>");
				}
				$addon_utilities->forgetProfile($folder, $addons[$package]->version, $profileName);
				$output->writeln("Profile $profileName forgotten from addon registry.");
			}
		}
	}
}
