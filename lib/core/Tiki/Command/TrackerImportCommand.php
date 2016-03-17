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

class TrackerImportCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('tracker:import')
			->setDescription('Import a CSV file into a tracker using a tracker tabular format')
			->addArgument(
				'tabularId',
				InputArgument::REQUIRED,
				'ID of tracker tabular format to use'
			)
			->addArgument(
				'filename',
				InputArgument::REQUIRED,
				'Location of CSV file to import'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$output->writeln('Importing tracker...');

		$lib = \TikiLib::lib('tabular');
		$info = $lib->getInfo($input->getArgument('tabularId'));

		$perms = \Perms::get('tabular', $info['tabularId']);
		if (! $info || !$perms->tabular_import) {
			throw new \Exception('Tracker Import: Tabular Format not found');
		}

		$fileName = $input->getArgument('filename');
		if (! file_exists($fileName)) {
			throw new \Exception('Tracker Import: File not found');
		}

		// from \Services_Tracker_TabularController::getSchema TODO refactor?
		$tracker = \Tracker_Definition::get($info['trackerId']);

		if (! $tracker) {
			throw new \Exception('Tracker Import: Tracker not found');
		}

		$schema = new \Tracker\Tabular\Schema($tracker);
		$schema->loadFormatDescriptor($info['format_descriptor']);
		$schema->loadFilterDescriptor($info['filter_descriptor']);

		$schema->validate();

		if (! $schema->getPrimaryKey()) {
			throw new \Exception(tr('Primary Key required'));
		}

		// this will throw exceptions and not return if there's a problem
		$source = new \Tracker\Tabular\Source\CsvSource($schema, $fileName);
		$writer = new \Tracker\Tabular\Writer\TrackerWriter;
		$writer->write($source);

		$output->writeln('Import done');

		return(0);
	}
}
