<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Category extends ObjectWriter
{
	protected function configure()
	{
		$this
			->setName('profile:export:category')
			->setDescription('Export a category')
			->addArgument(
				'category',
				InputArgument::REQUIRED,
				'Category ID'
			)
			->addOption(
				'deep',
				null,
				InputOption::VALUE_NONE,
				'Also export sub-categories'
			)
			->addOption(
				'include-objects',
				null,
				InputOption::VALUE_NONE,
				'Include references to objects contained in the profile'
			)
			->addOption(
				'include-all-objects',
				null,
				InputOption::VALUE_NONE,
				'Include references to all objects associated to the category'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$category = $input->getArgument('category');
		$deep = $input->getOption('deep');

		$writer = $this->getProfileWriter($input);

		$includeObject = function ($type, $id) {
			return false;
		};

		if ($input->getOption('include-objects')) {
			$includeObject = function ($type, $id) use ($writer) {
				return $writer->isKnown($type, $id);
			};
		}

		if ($input->getOption('include-all-objects')) {
			$includeObject = function ($type, $id) {
				return true;
			};
		}

		if (\Tiki_Profile_InstallHandler_Category::export($writer, $category, $deep, $includeObject)) {
			$writer->save();
		} else {
			$output->writeln("<error>Category not found: $category</error>");
			return;
		}
	}
}
