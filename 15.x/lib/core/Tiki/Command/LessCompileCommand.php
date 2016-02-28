<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CacheClearCommand.php 50250 2014-03-07 14:39:32Z lphuberdeau $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LessCompileCommand  extends Command
{
	protected function configure()
	{
		$this
			->setName('less:compile')
			->setDescription('Compile LESS theme files into CSS')
			->addArgument(
				'location',
				InputArgument::OPTIONAL,
				'Location of less files to compile (themes)',
				'themes'
			)
			->addOption(
				'all',
				null,
				InputOption::VALUE_NONE,
				'Build all less files, including all built-in "Tiki" themes'
			)
			->addOption(
				'only',
				null,
				InputOption::VALUE_OPTIONAL,
				'Only compile named theme or themes, separated by commas'
			)
			->addOption(
				'without-options',
				null,
				InputOption::VALUE_NONE,
				'Do not compile the theme options if present'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$all = $input->getOption('all');
		$only = array_filter(explode(',', $input->getOption('only')));
		if (empty($only)) {
			$all = true;
		}
		$type = $input->getArgument('location');

		$cachelib = \TikiLib::lib('cache');

		switch ($type)
		{
			case 'themes':
				$output->writeln('Compiling less files from themes');
				foreach (new \DirectoryIterator('themes') as $fileInfo)
				{
					if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
						continue;
					}
					$themename = $fileInfo->getFilename();
                    if (!empty($only) && ! in_array($themename, $only) && ! $all) {
                        continue;
                    }

					if ($themename === 'base_files') {
						$less_file = "themes/$themename/less/tiki_base.less";
						$css_file = "themes/$themename/css/tiki_base.css";
					} else {
						$less_file = "themes/$themename/less/$themename.less";
						$css_file = "themes/$themename/css/$themename.css";
					}
                    if (! file_exists($less_file)) {
                        continue;
                    }
                    if (file_exists($css_file && filemtime($css_file) >= filemtime($less_file))) {
                        continue;
                    }
					$files = [];
					$files[] = ['less' => $less_file, 'css' => $css_file];

					if (! $input->getOption('without-options') && is_dir("themes/$themename/options")) {

						foreach (new \DirectoryIterator("themes/$themename/options") as $fileInfo2) {
							if ($fileInfo2->isDot() || !$fileInfo2->isDir()) {
								continue;
							}
							$optionname = $fileInfo2->getFilename();
							$less_file = "themes/$themename/options/$optionname/less/$optionname.less";
							if (! file_exists($less_file)) {
								continue;
							}
							$css_file = "themes/$themename/options/$optionname/css/$optionname.css";
							if (file_exists($css_file && filemtime($css_file) >= filemtime($less_file))) {
								continue;
							}
							$files[] = ['less' => $less_file, 'css' => $css_file];
						}
					}

					foreach ($files as $file) {
						$command = "php vendor/oyejorge/less.php/bin/lessc {$file['less']} {$file['css']}";
						$output->writeln($command);
						$result = shell_exec($command);
						$result = str_replace(array("\r", "\n"), '', $result);
						$output->writeln($result);
					}
				}
				break;
			case '':
				return $output->writeln('Missing parameter. Try: php -f console.php less:compile themes');
			default:
				$output->writeln('<error>Invalid location for less files requested. Try: php -f console.php less:compile themes</error>');
		}

		$output->writeln('Clearing all caches');
		$cachelib->empty_cache();
	}
}
