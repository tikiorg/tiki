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

class FilesDeleteoldCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('files:deleteold')
			->setDescription('Remove expired files which were uploaded using the deleteAfter option')
			->addOption(
				'confirm',
				null,
				InputOption::VALUE_NONE,
				'Perform the deletes'
			)
		;
	}	

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$confirm = $input->getOption('confirm');

		$perms = \Perms::get();
		if (! $perms->admin_file_galleries) {
			throw new \Exception('Tracker Clear: Admin permission required');
		}

		if ($confirm) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<info>Deleting old filegal files...</info>');
			}

			\TikiLib::lib('filegal')->deleteOldFiles();

			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<info>Deleting old filegal files done</info>');
			}
		} else {
			$query = 'select * from `tiki_files` where `deleteAfter` < ? - `lastModif` and `deleteAfter` is not NULL and `deleteAfter` != \'\' order by galleryId asc';
			$now = time();
			$files = \TikiDb::get()->query($query, array($now));

			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				if ($files->numrows) {
					$output->writeln("<comment>Files to delete:</comment>");

					foreach ($files->result as $file) {
						$old = ceil(abs($now - $file['lastModif']) / 86400);
						$days = $old > 1 ? 'days' : 'day';
						$deleteAfter = \TikiLib::lib('tiki')->get_short_datetime($file['deleteAfter']);
						$output->writeln("<info>    \"{$file['name']}\" is $old $days old in gallery #{$file['galleryId']} (id #{$file['fileId']} deleteAfter $deleteAfter)</info>");
					}
				} else {
					$output->writeln("<comment>No files to delete</comment>");
				}

			}
		}

	}
}
