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

class FilesBatchuploadCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('files:batchupload')
			->setDescription('Batch upload files into the file galleries')
			->addArgument(
				'galleryId',
				InputArgument::OPTIONAL,
				'Destination gallery for uploads (optional, uses file gallery root if not supplied)'
			)
			->addOption(
				'confirm',
				null,
				InputOption::VALUE_NONE,
				'Perform the batch upload'
			)
			->addOption(
				'subToDesc',
				null,
				InputOption::VALUE_NONE,
				'Use last sub-directory name as description'
			)
			->addOption(
				'subdirToSubgal',
				's',
				InputOption::VALUE_NONE,
				'Move the file into a gallery matching the subdirectory name'
			)
			->addOption(
				'subdirIntegerToSubgalId',
				'i',
				InputOption::VALUE_NONE,
				'Move the file into a gallery matching the subdirectory as galleryId'
			)
			->addOption(
				'createSubgals',
				'c',
				InputOption::VALUE_NONE,
				'Create missing sub galleries'
			)
			->addOption(
				'deleteAfter',
				'd',
				InputOption::VALUE_REQUIRED,
				'Delete file after a certain number of seconds. e.g. "86400" for one day, "604800" for a week etc'
			)
			->addOption(
				'fileUser',
				'u',
				InputOption::VALUE_REQUIRED,
				'User name (or id) to "own" the uploaded files (e.g. www-data, nobody, apache etc)'
			)
			->addOption(
				'fileGroup',
				'g',
				InputOption::VALUE_REQUIRED,
				'Group name (or id) to "own" the uploaded files (e.g. www-data)'
			)
			->addOption(
				'fileMode',
				'm',
				InputOption::VALUE_REQUIRED,
				'Octal file mode to set on the uploaded files (e.g. 0755)'
			)
			->addOption(
				'filesPath',
				'p',
				InputOption::VALUE_REQUIRED,
				'Path to files to upload'
			)
		;
	}	

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y' || $prefs['feature_file_galleries_batch'] != 'y' || ! is_dir($prefs['fgal_batch_dir'])) {
			throw new \Exception("Feature Batch Uploading not set up, please refer to https://doc.tiki.org/Batch+Upload for instructions");
		}

		$filegallib = \TikiLib::lib('filegal');
		$filegalbatchlib = \TikiLib::lib('filegalbatch');

		$galleryId = (int) $input->getArgument('galleryId');
		if (empty($galleryId)) {
			$galleryId = $prefs['fgal_root_id'];
		}

		$gal_info = $filegallib->get_file_gallery($galleryId);
		if (! $gal_info) {
			throw new \Exception("Files Batch Upload: Gallery #$galleryId not found");
		}

		$confirm = $input->getOption('confirm');

		$filesPath = $input->getOption('filesPath');
		$files = $filegalbatchlib->batchUploadFileList($filesPath);

		if (! $files) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>No files to upload</comment>');
			}
			return;
		}

		$subdirToSubgal = $input->getOption('subdirToSubgal');
		if ($subdirToSubgal) {
			$subdirIntegerToSubgalId = $input->getOption('subdirIntegerToSubgalId');
			if (! $subdirIntegerToSubgalId) {
				$createSubgals = $input->getOption('createSubgals');
			} else {
				$createSubgals = false;
			}
		} else {
			$createSubgals = false;
			$subdirIntegerToSubgalId = false;
		}

		if ($confirm) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>Files Batch Upload starting...</comment>');
			}

			foreach ($files as & $file) {
				$file = $file['file'];
			}

			$feedback = $filegalbatchlib->processBatchUpload($files, $galleryId, [
					'subToDesc' => $input->getOption('subToDesc'),
					'subdirToSubgal' => $subdirToSubgal,
					'subdirIntegerToSubgalId' => $subdirIntegerToSubgalId,
					'createSubgals' => $createSubgals,
					'deleteAfter' => $input->getOption('deleteAfter'),
					'fileUser' => $input->getOption('fileUser'),
					'fileGroup' => $input->getOption('fileGroup'),
					'fileMode' => $input->getOption('fileMode'),
					'filesPath' => $filesPath,
			]);

			foreach ($feedback as $message) {
				if (strpos($message, '<span class="text-danger">') !== false) {
					$error = true;
				} else {
					$error = false;
				}
				$message = strip_tags(str_replace('<br>', ' : ', $message));
				if ($error) {
					$message = "<error>$message</error>";
					$output->writeln($message);
				} else if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
					$message = "<info>$message</info>";
					$output->writeln($message);
				}
			}

			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>Files Batch Upload complete</comment>');
			}
		} else {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln("<comment>Files to upload from {$prefs['fgal_batch_dir']} to gallery #$galleryId</comment>");
			}

			foreach($files as $file) {
				$fname = substr($file['file'], strlen($prefs['fgal_batch_dir']));
				if (! $file['writable']) {
					$fname = "<error>$fname</error>";
				}
				$output->writeln("  {$fname} ({$file['size']} bytes)");
			}
			$output->writeln("<info>Use the --confirm option to proceed with the upload operation.</info>");
		}

	}
}
