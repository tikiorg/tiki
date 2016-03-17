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

class AddonInstallCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('addon:install')
			->setDescription('Apply profiles for addon')
			->addArgument(
				'addon',
				InputArgument::REQUIRED,
				'Addon package vendor/name'
			)
			->addOption(
				'reapply',
				null,
				InputOption::VALUE_NONE,
				'Re-apply profiles when already applied.'
			)
			->addOption(
				'ignoredepends',
				null,
				InputOption::VALUE_NONE,
				'Ignore dependencies.'
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
		$reapply = $input->getOption('reapply');
		$ignoredepends = $input->getOption('ignoredepends');

		if (empty(glob(TIKI_PATH . '/addons/' . $folder . '/profiles/*.yml'))) {
			$output->writeln("<error>No profiles found.</error>");
			return false;
		}

		if (!$ignoredepends) {
			$addon_utilities->checkDependencies($folder);
		}

		$addons = \TikiAddons::getInstalled();
		$tikilib = \TikiLib::lib('tiki');
		$installer = new \Tiki_Profile_Installer;

		foreach (glob(TIKI_PATH . '/addons/' . $folder . '/profiles/*.yml') as $file) {
			$profileName = str_replace('.yml', '', basename($file));
			$profile = \Tiki_Profile::fromNames($repository, $profileName);

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
