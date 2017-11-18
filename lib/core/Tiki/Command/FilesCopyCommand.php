<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
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

class FilesCopyCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('files:copy')
			->setDescription(tra('Copy files from file galleries to a regular directory on the file system'))
			->addArgument(
				'galleryId',
				InputArgument::REQUIRED,
				'Gallery to copy files from'
			)
			->addArgument(
				'destinationPath',
				InputArgument::REQUIRED,
				'Path to copy files to'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y') {
			throw new \Exception(tra('Feature Galleries not set up'));
		}

		$filegallib = \TikiLib::lib('filegal');
		$filegalcopylib = \TikiLib::lib('filegalcopy');

		$galleryId = (int) $input->getArgument('galleryId');

		$gal_info = $filegallib->get_file_gallery($galleryId);
		if (! $gal_info || empty($gal_info['name'])) {
			throw new \Exception(tr('File Copy: Gallery #%0 not found', $galleryId));
		}

		$destinationPath = $input->getArgument('destinationPath');
		if (empty($destinationPath)) {
			throw new \Exception(tra('File Copy: Destination path required'));
		}

		$sourcePath = $filegallib->get_gallery_save_dir($galleryId);
		// in the unlikely case where fgal_use_db was once !== 'y' and then became == 'y'
		if (empty($sourcePath)) {
			$sourcePath = $prefs['fgal_use_dir'];
		}

		$files = $filegallib->get_files_info_from_gallery_id($galleryId);
		if (! $files) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>' . tra('No files to copy') . '</comment>');
			}
			return;
		}

		if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
			$output->writeln('<comment>' . tra('File Copy starting...') . '</comment>');
		}

		$feedback = $filegalcopylib->processCopy($files, $destinationPath, $sourcePath);
		foreach ($feedback as $message) {
			$error = strpos($message, '<span class="text-danger">') !== false;
			$message = strip_tags(str_replace('<br>', ' : ', $message));
			if ($error) {
				$message = "<error>$message</error>";
				$output->writeln($message);
			} elseif ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$message = "<info>$message</info>";
				$output->writeln($message);
			}
		}

		if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
			$output->writeln('<comment>' . tra('File Copy complete') . '</comment>');
		}
	}
}
