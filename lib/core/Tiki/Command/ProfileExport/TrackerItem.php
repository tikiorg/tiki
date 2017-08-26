<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


namespace Tiki\Command\ProfileExport;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class TrackerItem extends ObjectWriter
{

	protected function configure()
	{
		$this
			->setName('profile:export:tracker-item')
			->setDescription('Export a tracker item definition')
			->addArgument(
				'tracker',
				InputArgument::REQUIRED,
				'Tracker ID to export'
			)->addOption(
				'items',
				'i',
				InputOption::VALUE_OPTIONAL,
				'Export only items with these IDs'
			)->addOption(
				'fields',
				'f',
				InputOption::VALUE_OPTIONAL,
				'Export only fields with these IDs'
			);

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$trackerId = $input->getArgument('tracker');
		$itemFilterList = $input->getOption('items');
		$fieldFilterList = $input->getOption('fields');

		/** @var \TrackerLib $trackerLib */
		$trackerLib = \TikiLib::lib('trk');

		$trackerDefinition = \Tracker_Definition::get($trackerId);
		if (! $trackerDefinition) {
			$output->writeln('<error>' . tr('Tracker not found') . '</error>');
			return;
		}

		$exportFields = $trackerDefinition->getFields();

		$exportFields = $this->filterFields($exportFields, $fieldFilterList);
		$listItemsFilter = $this->generateListItemsFilter($itemFilterList);

		$items = $trackerLib->list_items($trackerId, 0, -1, 'itemId_asc', $exportFields, '', '', '', '', '', $listItemsFilter);

		if (! $items || empty($items['data']) || ! is_array($items['data'])) {
			$output->writeln('<error>' . tr('No Items found to export') . '</error>');
			return;
		}

		$writer = $this->getProfileWriter($input);

		foreach ($items['data'] as $item) {
			$result = \Tiki_Profile_InstallHandler_TrackerItem::export($writer, $item, $exportFields);
			if ($result) {
				$output->writeln('<info>' . tr("Tracker item %0 exported", $item['itemId']) . '</info>');
			} else {
				$output->writeln('<error>' . tr("Tracker item %0 failed to exported", $item['itemId']) . '</error>');
			}
		}

		$writer->save();

		$output->writeln('<info>' . tr("Tracker items for tracker %0 exported", $trackerId) . '</info>');
	}

	/**
	 * Filter the list of fields, if needed, based on a given coma separated id list
	 *
	 * @param array $exportFields
	 * @param string $fieldFilterList
	 * @return array
	 */
	protected function filterFields($exportFields, $fieldFilterList)
	{
		if (empty(trim($fieldFilterList))) {
			return $exportFields;
		}

		$fieldFilterList = array_map('trim', explode(',', $fieldFilterList));

		$exportFields = array_filter(
			$exportFields,
			function ($field) use ($fieldFilterList) {
				if (in_array($field['fieldId'], $fieldFilterList)) {
					return true;
				}
				return false;
			}
		);
		$exportFields = array_values($exportFields);
		return $exportFields;
	}

	/**
	 * Generates the parameter to use as filter in list_items
	 *
	 * @param $itemFilterList
	 * @return array|string
	 */
	protected function generateListItemsFilter($itemFilterList)
	{
		$listItemsFilter = '';
		if (! empty(trim($itemFilterList))) {
			$itemFilterList = array_map('trim', explode(',', $itemFilterList));

			$listItemsFilter = ['tti.`itemId`' => $itemFilterList];
		}
		return $listItemsFilter;
	}
}