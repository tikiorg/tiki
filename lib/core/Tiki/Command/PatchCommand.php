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

class PatchCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('patch')
			->setDescription('Apply a specific database schema patch')
			->addArgument('name',InputArgument::REQUIRED, 'Name of the patch applied'
			)
			->addOption(
				'force-application',
				null,
				InputOption::VALUE_NONE,
				'Force application, even if the patch was already applied'
			)->addOption(
				'force-mark',
				null,
				InputOption::VALUE_NONE,
				'Forcibly mark the patch as applied, regardless of errors'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument('name');
		$forceApplication = $input->getOption('force-application');
		$forceMark = $input->getOption('force-mark');

		$installer = \Installer::getInstance();
		if (! $installer->isInstalled()) {
			$output->writeln('<error>Database not found</error>');
			return false;
		}
		try {
			$installer->installPatch($name, $forceApplication);
			$output->writeln('Patch applied');
		} catch (\Exception $e) {
			switch ($e->getCode()) {
				case 1:
					$output->writeln("<error>Unknown patch</error>");
					return;
				case 2:
					$output->writeln("<error>Application failed</error>");
					foreach ( $installer->queries['failed'] as $key => $error ) {
						list( $query, $message, $patch ) = $error;
						$output->writeln("<error>Error $key in $patch\n\t$query\n\t$message</error>");
					}
					if ($forceMark) {
						$output->writeln("Patch forcibly marked as applied");
						\Patch::$list[$name]->record();
					}
					break;
				case 3:
					$output->writeln('Already applied patch; not applying');
					break;
				default:
					throw $e;
			}
		}
		$output->writeln('<info>Queries executed successfully: ' . count($installer->queries['successful']) . '</info>');

		\TikiLib::lib('cache')->empty_cache();
	}
}
