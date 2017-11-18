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

class BackupDBCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('database:backup')
			->setDescription('Create a database backup (with mysqldump)')
			->addArgument(
				'path',
				InputArgument::REQUIRED,
				'Path to save backup (relative to console.php, or absolute)'
			)
			->addArgument(
				'dateFormat',
				InputArgument::OPTIONAL,
				'Format to use for the date part of the backup file. Defaults to "Y-m-d_H-i-s" and uses the PHP date function format'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('path');
		if (substr($path, -1) == '/') {
			$path = substr($path, 0, strlen($path) - 1);
		}

		if (! is_dir($path)) {
			$output->writeln('<error>Error: Provided path not found</error>');
			return;
		}

		$local = \TikiInit::getCredentialsFile();
		if (! is_readable($local)) {
			$output->writeln('<error>Error: "' . $local . '" not readable.</error>');
			return;
		}

		$dateFormat = $input->getArgument('dateFormat');
		if (! $dateFormat) {
			$dateFormat = 'Y-m-d_H-i-s';
		}

		$user_tiki = $pass_tiki = $host_tiki = $dbs_tiki = '';

		require $local;

		$args = [];
		if ($user_tiki) {
			$args[] = "-u" . escapeshellarg($user_tiki);
		}
		if ($pass_tiki) {
			$args[] = "-p" . escapeshellarg($pass_tiki);
		}
		if ($host_tiki) {
			$args[] = "-h" . escapeshellarg($host_tiki);
		}

		// Find out how many non-InnoDB tables exist in the schema
		$db = \TikiDb::get();
		$query = "SELECT count(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbs_tiki' AND engine <> 'InnoDB'";
		$numTables = $db->getOne($query);

		if ($numTables === '0') {
			$args[] = "--single-transaction";
		} else {
			$dbOpenFilesLimit = 0;
			$result = $db->fetchAll('SHOW GLOBAL VARIABLES LIKE "open_files_limit"');
			if (count($result) > 0) {
				$dbOpenFilesLimit = (int)$result[0]['Value'];
			}
			if ($dbOpenFilesLimit > 0 && $dbOpenFilesLimit < 2000) {
				// some distributions bring a lower limit of open files, so lock all tables during backup might fail the backup
				$output->writeln('<info>Mysql database has open_files_limit=' . $dbOpenFilesLimit . ', skipping lock tables to avoid failing the backup</info>');
			} else {
				$args[] = "--lock-tables";
			}
		}

		$args[] = $dbs_tiki;

		$args = implode(' ', $args);
		$outputFile = $path . '/' . $dbs_tiki . '_' . date($dateFormat) . '.sql.gz';
		$command = "mysqldump --quick --create-options --extended-insert $args | gzip -5 > " . escapeshellarg($outputFile);
		exec($command);
		$output->writeln('<comment>Database backup completed: ' . $outputFile . '</comment>');
	}
}
