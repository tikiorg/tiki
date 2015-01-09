<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_TabularController
{
	function setUp()
	{
		Services_Exception_Disabled::check('tracker_tabular_enabled');
	}

	function action_manage($input)
	{
		Services_Exception_Denied::checkGlobal('tiki_p_admin_trackers');

		$lib = TikiLib::lib('tabular');

		return [
			'title' => tr('Tabular Formats'),
			'list' => $lib->getList(),
		];
	}

	function action_delete($input)
	{
		Services_Exception_Denied::checkGlobal('tiki_p_admin_trackers');
		$tabularId = $input->tabularId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lib = TikiLib::lib('tabular');
			$lib->remove($tabularId);
		}

		return [
			'title' => tr('Remove Format'),
			'tabularId' => $tabularId,
		];
	}

	function action_create($input)
	{
		Services_Exception_Denied::checkGlobal('tiki_p_admin_trackers');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$lib = TikiLib::lib('tabular');

			$tabularId = $lib->create($input->name->text(), $input->trackerId->int());

			return [
				'FORWARD' => [
					'controller' => 'tabular',
					'action' => 'edit',
					'tabularId' => $tabularId,
				],
			];
		}

		return [
			'title' => tr('Create Tabular Format'),
		];
	}

	function action_edit($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($input->tabularId->int());
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$info['format_descriptor'] = json_decode($input->fields->none(), true);
			$info['filter_descriptor'] = json_decode($input->filters->none(), true);
			$schema = $this->getSchema($info);

			// FIXME : Blocks save and back does not restore changes, ajax validation required
			// $schema->validate();

			$lib->update($info['tabularId'], $input->name->text(), $schema->getFormatDescriptor(), $schema->getFilterDescriptor());

			return [
				'FORWARD' => [
					'controller' => 'tabular',
					'action' => 'manage',
				],
			];
		}

		$schema = $this->getSchema($info);

		return [
			'title' => tr('Edit Format: %0', $info['name']),
			'tabularId' => $info['tabularId'],
			'trackerId' => $info['trackerId'],
			'name' => $info['name'],
			'schema' => $schema,
			'filterCollection' => $schema->getFilterCollection(),
		];
	}

	function action_select($input)
	{
		$permName = $input->permName->word();
		$trackerId = $input->trackerId->int();

		$tracker = \Tracker_Definition::get($trackerId);

		if (! $tracker) {
			throw new Services_Exception_NotFound;
		}

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = new \Tracker\Tabular\Schema($tracker);
		$local = $schema->getFieldSchema($permName);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$column = $schema->addColumn($permName, $input->mode->text());
			return [
				'field' => $column->getField(),
				'mode' => $column->getMode(),
				'label' => $column->getLabel(),
				'isReadOnly' => $column->isReadOnly(),
				'isPrimary' => $column->isPrimaryKey(),
			];
		}

		return [
			'title' => tr('Fields in %0', $tracker->getConfiguration('name')),
			'trackerId' => $trackerId,
			'permName' => $permName,
			'schema' => $local,
		];
	}

	function action_select_filter($input)
	{
		$permName = $input->permName->word();
		$trackerId = $input->trackerId->int();

		$tracker = \Tracker_Definition::get($trackerId);

		if (! $tracker) {
			throw new Services_Exception_NotFound;
		}

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = new \Tracker\Filter\Collection($tracker);
		$local = $schema->getFieldCollection($permName);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$column = $schema->addFilter($permName, $input->mode->text());
			return [
				'field' => $column->getField(),
				'mode' => $column->getMode(),
				'label' => $column->getLabel(),
			];
		}

		return [
			'title' => tr('Fields in %0', $tracker->getConfiguration('name')),
			'trackerId' => $trackerId,
			'permName' => $permName,
			'collection' => $local,
		];
	}

	function action_export_full_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($input->tabularId->int());
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = $this->getSchema($info);
		$schema->validate();

		$source = new \Tracker\Tabular\Source\TrackerSource($schema);
		$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');
		$writer->sendHeaders();
		$writer->write($source);
		exit;
	}

	function action_export_partial_csv($input)
	{
		$tabularId = $input->tabularId->int();

		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($tabularId);
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = $this->getSchema($info);
		$collection = $schema->getFilterCollection();

		$collection->applyInput($input);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$search = TikiLib::lib('unifiedsearch');
			$query = $search->buildQuery([
				'type' => 'trackeritem',
				'tracker_id' => $trackerId,
			]);

			$collection->applyConditions($query);

			$source = new \Tracker\Tabular\Source\QuerySource($schema, $query);
			$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');
			$writer->sendHeaders();
			$writer->write($source);
			exit;
		}

		return [
			'title' => tr('Export'),
			'tabularId' => $tabularId,
			'filters' => array_map(function ($filter) {
				return [
					'id' => $filter->getControl()->getId(),
					'label' => $filter->getLabel(),
					'help' => $filter->getHelp(),
					'control' => $filter->getControl(),
				];
			}, $collection->getFilters()),
		];
	}

	function action_export_search_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$trackerId = $input->trackerId->int();
		$tabularId = $input->tabularId->int();
		$conditions = array_filter([
			'trackerId' => $trackerId,
			'tabularId' => $tabularId,
		]);

		$formats = $lib->getList($conditions);

		if ($tabularId) {
			$info = $lib->getInfo($tabularId);
			$schema = $this->getSchema($info);
			$schema->validate();

			$trackerId = $info['trackerId'];

			Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

			$search = TikiLib::lib('unifiedsearch');
			$query = $search->buildQuery($input->filter->none() ?: []);

			// Force filters
			$query->filterType('trackeritem');
			$query->filterContent($trackerId, 'tracker_id');

			$source = new \Tracker\Tabular\Source\QuerySource($schema, $query);
			$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');
			$writer->sendHeaders();
			$writer->write($source);
			exit;
		} elseif (count($formats) === 0) {
			throw new Services_Exception(tr('No formats available.'));
		} else {
			if ($trackerId) {
				Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);
			} else {
				Services_Exception_Denied::checkGlobal('tiki_p_admin_trackers');
			}

			return [
				'title' => tr('Select Format'),
				'formats' => $formats,
				'filters' => $input->filter->none(),
			];
		}
	}

	function action_import_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($input->tabularId->int());
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = $this->getSchema($info);
		$schema->validate();

		if (! $schema->getPrimaryKey()) {
			throw new Services_Exception_NotAvailable(tr('Primary Key required'));
		}

		$done = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_uploaded_file($_FILES['file']['tmp_name'])) {
			$source = new \Tracker\Tabular\Source\CsvSource($schema, $_FILES['file']['tmp_name']);
			$writer = new \Tracker\Tabular\Writer\TrackerWriter;
			$writer->write($source);

			unlink($_FILES['file']['tmp_name']);
			$done = true;
		}

		return [
			'title' => tr('Import'),
			'tabularId' => $info['tabularId'],
			'completed' => $done,
		];
	}

	private function getSchema(array $info)
	{
		$tracker = \Tracker_Definition::get($info['trackerId']);

		if (! $tracker) {
			throw new Services_Exception_NotFound;
		}

		$schema = new \Tracker\Tabular\Schema($tracker);
		$schema->loadFormatDescriptor($info['format_descriptor']);
		$schema->loadFilterDescriptor($info['filter_descriptor']);

		return $schema;
	}
}
