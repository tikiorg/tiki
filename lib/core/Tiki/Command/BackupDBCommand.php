<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
				'Format to use for the date part of the backup file. Defaults to "Y-m-d_H:i:s" and uses the PHP date function format'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('path');
		if (substr($path, -1) == '/') {
			$path = substr($path, 0, strlen($path) - 1);
		}

		if (!is_dir($path)) {
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
			$dateFormat = 'Y-m-d_H:i:s';
		}

		$user_tiki = $pass_tiki = $host_tiki = $dbs_tiki = '';

		require $local;

		$args = array();
		if( $user_tiki ) {
			$args[] = "-u" . escapeshellarg( $user_tiki );
		}
		if( $pass_tiki ) {
			$args[] = "-p" . escapeshellarg( $pass_tiki );
		}
		if( $host_tiki ) {
			$args[] = "-h" . escapeshellarg( $host_tiki );
		}
		$args[] = $dbs_tiki;
	
		$args = implode( ' ', $args );
		$outputFile = $path . '/' . $dbs_tiki . '_' . date($dateFormat) . '.sql.gz';
		$command = "mysqldump --quick $args | gzip -5 > " . escapeshellarg( $outputFile );
		exec( $command );
		$output->writeln('<comment>Database backup completed: '.$outputFile.'</comment>');
	}
}
