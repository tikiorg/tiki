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
			'list' => $lib->get_list(),
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
		$info = $lib->get_info($input->tabularId->int());

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$info['format_descriptor'] = json_decode($input->fields->none(), true);
			$schema = $this->getSchema($info);

			// FIXME : Blocks save and back does not restore changes, ajax validation required
			// $schema->validate();

			$lib->update($info['tabularId'], $input->name->text(), $schema->getFormatDescriptor());

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

	function action_export_full_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->get_info($input->tabularId->int());

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$schema = $this->getSchema($info);
		$schema->validate();

		$source = new \Tracker\Tabular\Source\TrackerSource($schema);
		$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');
		$writer->sendHeaders();
		$writer->write($source);
		exit;
	}

	function action_import_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->get_info($input->tabularId->int());

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

		return $schema;
	}
}
