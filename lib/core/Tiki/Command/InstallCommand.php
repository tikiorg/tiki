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

class InstallCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('database:install')
			->setDescription(tr('Clean Tiki install'))
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				tr('Force installation. Overwrite any current database.')
			)
			->addOption(
				'useInnoDB',
				i,
				InputOption::VALUE_REQUIRED,
				tr('Use InnoDb as storage engine: 1 - InnoDb, 0 - MyISAM.'),
				1
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force = $input->getOption('force');
		$installer = new \Installer;
		$installed = $installer->tableExists('users_users');

		$optionUseInnoDB = $input->getOption('useInnoDB');
		if ($optionUseInnoDB !== null) {
			$installer->useInnoDB = ($optionUseInnoDB == 1) ? true : false;
		}

		if (! $installed || $force) {
			$installer->cleanInstall();
			$output->writeln(tr('Installation completed.'));
			$output->writeln('<info>' . tr('Queries executed successfully: %0', count($installer->queries['successful'])) . '</info>');

			if (count($installer->queries['failed'])) {
				foreach ($installer->queries['failed'] as $key => $error) {
					list($query, $message, $patch) = $error;

					$output->writeln("<error>" . tr('Error %0 in', $key) . " $patch\n\t$query\n\t$message</error>");
				}
			}

			include_once 'tiki-setup.php';
			\TikiLib::lib('cache')->empty_cache();
			initialize_prefs(true);
			\TikiLib::lib('unifiedsearch')->rebuild();
			\TikiLib::lib('prefs')->rebuildIndex();
		} else {
			$output->writeln('<error>' . tr('Database already exists.') . '</error>');
		}
	}
}
