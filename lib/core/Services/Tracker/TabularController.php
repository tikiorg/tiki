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
			$text = $input->fields->text();
			$text = explode("\n", $text);
			$values = array_filter($text);

			$lib->update($info['tabularId'], $input->name->text(), array_map(function ($item) {
				list($field, $mode) = explode(':', trim($item));
				return ['field' => $field, 'mode' => $mode];
			}, $values));

			return [
				'FORWARD' => [
					'controller' => 'tabular',
					'action' => 'manage',
				],
			];
		}

		$info = $lib->get_info($input->tabularId->int());

		$fields = [];
		foreach ($info['format_descriptor'] as $item) {
			$fields[] = $item['field'] . ':' . $item['mode'];
		}

		return [
			'title' => tr('Edit Format: %0', $info['name']),
			'tabularId' => $info['tabularId'],
			'name' => $info['name'],
			'fields' => implode("\n", $fields),
		];
	}

	function action_export_full_csv($input)
	{
		$lib = TikiLib::lib('tabular');
		$info = $lib->get_info($input->tabularId->int());

		Services_Exception_Denied::checkObject('tiki_p_admin_trackers', 'tracker', $trackerId);

		$tracker = \Tracker_Definition::get($info['trackerId']);
		$schema = new \Tracker\Tabular\Schema($tracker);

		foreach ($info['format_descriptor'] as $column) {
			$schema->addColumn($column['field'], $column['mode']);
		}

		$schema->validate();

		$source = new \Tracker\Tabular\Source\TrackerSource($schema);
		$writer = new \Tracker\Tabular\Writer\CsvWriter('php://output');
		$writer->sendHeaders();
		$writer->write($source);
		exit;
	}
}
