<?php

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
			->setDescription('Copy files from file galleries to a regular directory on the file system')
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
			->addOption(
				'confirm',
				null,
				InputOption::VALUE_NONE,
				'Perform the copy'
			)
		;
	}	

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $prefs;

		if ($prefs['feature_file_galleries'] != 'y' ) {
			throw new \Exception("Feature Galleries not set up.");
		}

		if ($prefs['fgal_use_db'] == 'y' ) {
			throw new \Exception("Not impĺemented for preference fgal_use_db.");
		}

		$filegallib = \TikiLib::lib('filegal');
		$filegalcopylib = \TikiLib::lib('filegalcopy');

		$galleryId = (int) $input->getArgument('galleryId');

		$gal_info = $filegallib->get_file_gallery($galleryId);
		if (! $gal_info || trim($gal_info['name']) ==  '') {
			throw new \Exception("Files Copy: Gallery #$galleryId not found");
		}

		$destinationPath = $input->getArgument('destinationPath');
		if (empty($destinationPath)) {
			throw new \Exception("Files Copy: Destination path required");
		}

		$sourcePath = $filegallib->get_gallery_save_dir($galleryId);


		$confirm = $input->getOption('confirm');

		$files = $filegallib->get_files_info_from_gallery_id($galleryId);

		if (! $files) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>No files to copy</comment>');
			}
			return;
		}

		if ($confirm) {
			if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
				$output->writeln('<comment>Files Copy starting...</comment>');
			}

			$feedback = $filegalcopylib->processCopy($files, [
					'sourcePath' => $sourcePath,
					'destinationPath' => $destinationPath,
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
				$output->writeln('<comment>Files Copy complete</comment>');
			}
		} else {
			$output->writeln("<info>Use the --confirm option to proceed with the copy operation.</info>");
		}

	}
}
