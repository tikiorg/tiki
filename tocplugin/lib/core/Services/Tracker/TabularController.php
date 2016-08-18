<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		Services_Exception_Denied::checkGlobal('tiki_p_tabular_admin');

		$lib = TikiLib::lib('tabular');

		return [
			'title' => tr('Tabular Formats'),
			'list' => $lib->getList(),
		];
	}

	function action_delete($input)
	{
		$tabularId = $input->tabularId->int();

		Services_Exception_Denied::checkObject('tiki_p_tabular_admin', 'tabular', $tabularId);

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
		Services_Exception_Denied::checkGlobal('tiki_p_tabular_admin');

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

		Services_Exception_Denied::checkObject('tiki_p_tabular_admin', 'tabular', $info['tabularId']);

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

		Services_Exception_Denied::checkObject('tiki_p_view_trackers', 'tracker', $trackerId);

		$schema = new \Tracker\Tabular\Schema($tracker);
		$local = $schema->getFieldSchema($permName);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$column = $schema->addColumn($permName, $input->mode->text());

			$return = [
				'field' => $column->getField(),
				'mode' => $column->getMode(),
				'label' => $column->getLabel(),
				'isReadOnly' => $column->isReadOnly(),
				'isPrimary' => $column->isPrimaryKey(),
			];
			if ($input->offsetExists('columnIndex')) {
				$return['columnIndex'] = $input->columnIndex->int();
			}

			return $return;
		}

		$return = [
			'title' => tr('Fields in %0', $tracker->getConfiguration('name')),
			'trackerId' => $trackerId,
			'permName' => $permName,
			'schema' => $local,
		];
		if ($input->offsetExists('columnIndex')) {
			$return['columnIndex'] = $input->columnIndex->int();
		}
		if ($input->offsetExists('mode')) {
			$return['mode'] = $input->mode->text();
		}
		return $return;
	}

	function action_select_filter($input)
	{
		$permName = $input->permName->word();
		$trackerId = $input->trackerId->int();

		$tracker = \Tracker_Definition::get($trackerId);

		if (! $tracker) {
			throw new Services_Exception_NotFound;
		}

		Services_Exception_Denied::checkObject('tiki_p_view_trackers', 'tracker', $trackerId);

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

		Services_Exception_Denied::checkObject('tiki_p_tabular_export', 'tabular', $info['tabularId']);

		$schema = $this->getSchema($info);
		$schema->validate();

		$source = new \Tracker\Tabular\Source\TrackerSource($schema);
		$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');

		$name = TikiLib::lib('tiki')->remove_non_word_characters_and_accents($info['name']);
		$writer->sendHeaders($name . '_export_full.csv');

		TikiLib::lib('tiki')->allocate_extra(
			'tracker_export_items',
			function () use ($writer, $source) {
				$writer->write($source);
			}
		);
		exit;
	}

	function action_export_partial_csv($input)
	{
		$tabularId = $input->tabularId->int();

		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($tabularId);
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_tabular_export', 'tabular', $tabularId);

		$schema = $this->getSchema($info);
		$collection = $schema->getFilterCollection();

		$collection->applyInput($input);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' || $input->confirm->word() === 'export') {
			$search = TikiLib::lib('unifiedsearch');
			$query = $search->buildQuery([
				'type' => 'trackeritem',
				'tracker_id' => $trackerId,
			]);

			$collection->applyConditions($query);

			$source = new \Tracker\Tabular\Source\QuerySource($schema, $query);
			$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');

			$name = TikiLib::lib('tiki')->remove_non_word_characters_and_accents($info['name']);
			$writer->sendHeaders($name . '_export_partial.csv');

			TikiLib::lib('tiki')->allocate_extra(
				'tracker_export_items',
				function () use ($writer, $source) {
					$writer->write($source);
				}
			);
			exit;
		}

		return [
			'FORWARD' => [
				'controller' => 'tabular',
				'action' => 'filter',
				'tabularId' => $tabularId,
				'target' => 'export',
			],
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

			Services_Exception_Denied::checkObject('tiki_p_tabular_export', 'tabular', $tabularId);

			$search = TikiLib::lib('unifiedsearch');
			$query = $search->buildQuery($input->filter->none() ?: []);

			// Force filters
			$query->filterType('trackeritem');
			$query->filterContent($trackerId, 'tracker_id');

			$source = new \Tracker\Tabular\Source\QuerySource($schema, $query);
			$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');

			$name = TikiLib::lib('tiki')->remove_non_word_characters_and_accents($info['name']);
			$writer->sendHeaders($name . '_export_search.csv');

			TikiLib::lib('tiki')->allocate_extra(
				'tracker_export_items',
				function () use ($writer, $source) {
					$writer->write($source);
				}
			);
			exit;
		} elseif (count($formats) === 0) {
			throw new Services_Exception(tr('No formats available.'));
		} else {
			if ($trackerId) {
				Services_Exception_Denied::checkObject('tiki_p_view_trackers', 'tracker', $trackerId);
			} else {
				Services_Exception_Denied::checkGlobal('tiki_p_tabular_admin');
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

		Services_Exception_Denied::checkObject('tiki_p_tabular_import', 'tabular', $info['tabularId']);

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

	function action_filter($input)
	{
		$tabularId = $input->tabularId->int();

		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($tabularId);
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_tabular_list', 'tabular', $tabularId);

		$schema = $this->getSchema($info);
		$collection = $schema->getFilterCollection();

		$collection->applyInput($input);

		$search = TikiLib::lib('unifiedsearch');
		$query = $search->buildQuery([
			'type' => 'trackeritem',
			'tracker_id' => $trackerId,
		]);
		$query->setRange(1);
		$collection->applyConditions($query);
		$resultset = $query->search($search->getIndex());
		$collection->setResultSet($resultset);

		$target = $input->target->word();

		if ($target == 'list') {
			$title = tr('Filter %0', $info['name']);
			$method = 'get';
			$action = 'list';
			$label = tr('Filter');
		} elseif ($target = 'export') {
			$title = tr('Export %0', $info['name']);
			$method = 'post';
			$action = 'export_partial_csv';
			$label = tr('Export');
		} else {
			throw new Services_Exception_NotFound;
		}

		return [
			'title' => $title,
			'tabularId' => $tabularId,
			'method' => $method,
			'action' => $action,
			'label' => $label,
			'filters' => array_map(function ($filter) {
				if (! $filter->getControl()->isUsable()) {
					return false;
				}
				return [
					'id' => $filter->getControl()->getId(),
					'label' => $filter->getLabel(),
					'help' => $filter->getHelp(),
					'control' => $filter->getControl(),
				];
			}, $collection->getFilters()),
		];
	}

	function action_list($input)
	{
		$tabularId = $input->tabularId->int();

		$lib = TikiLib::lib('tabular');
		$info = $lib->getInfo($tabularId);
		$trackerId = $info['trackerId'];

		Services_Exception_Denied::checkObject('tiki_p_tabular_list', 'tabular', $tabularId);

		$schema = $this->getSchema($info);
		$collection = $schema->getFilterCollection();

		$collection->applyInput($input);

		$search = TikiLib::lib('unifiedsearch');
		$query = $search->buildQuery([
			'type' => 'trackeritem',
			'tracker_id' => $trackerId,
		]);
		$query->setRange($input->offset->int());

		$collection->applyConditions($query);

		$source = new \Tracker\Tabular\Source\PaginatedQuerySource($schema, $query);
		$writer = new \Tracker\Tabular\Writer\HtmlWriter();

		$columns = array_values(array_filter($schema->getColumns(), function ($c) {
			return ! $c->isExportOnly();
		}));
		$arguments = $collection->getQueryArguments();

		$collection->setResultSet($source->getResultSet());

		$template = ['controls' => [], 'usable' => false, 'selected' => false];
		$filters = ['default' => $template, 'primary' => $template, 'side' => $template];
		foreach ($collection->getFilters() as $filter) {
			// Exclude unusable controls
			if (! $filter->getControl()->isUsable()) {
				continue;
			}

			$pos = $filter->getPosition();

			$filters[$pos]['controls'][] = [
				'id' => $filter->getControl()->getId(),
				'label' => $filter->getLabel(),
				'help' => $filter->getHelp(),
				'control' => $filter->getControl(),
				'description' => $filter->getControl()->getDescription(),
				'selected' => $filter->getControl()->hasValue(),
			];

			$filters[$pos]['usable'] = true;
			if ($filter->getControl()->hasValue()) {
				$filters[$pos]['selected'] = true;
			}
		}

		return [
			'title' => tr($info['name']),
			'tabularId' => $tabularId,
			'filters' => $filters,
			'columns' => $columns,
			'data' => $writer->getData($source),
			'resultset' => $source->getResultSet(),
			'baseArguments' => $arguments,
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
