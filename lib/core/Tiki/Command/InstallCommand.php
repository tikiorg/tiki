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
			->setDescription('Clean Tiki install')
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Force installation. Overwrite any current database.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force = $input->getOption('force');
		$installer = new \Installer;
		$installed = $installer->tableExists('users_users');

		if (! $installed || $force) {
			$installer->cleanInstall();
			$output->writeln('Installation completed.');
			$output->writeln('<info>Queries executed successfully: ' . count($installer->success) . '</info>');

			if ( count($installer->failures) ) {
				foreach ( $installer->failures as $key => $error ) {
					list( $query, $message, $patch ) = $error;

					$output->writeln("<error>Error $key in $patch\n\t$query\n\t$message</error>");
				}
			}

			include_once 'tiki-setup.php';
			\TikiLib::lib('cache')->empty_cache();
			initialize_prefs(true);
			\TikiLib::lib('unifiedsearch')->rebuild();
		} else {
			$output->writeln('<error>Database already exists.</error>');
		}
	}
}
