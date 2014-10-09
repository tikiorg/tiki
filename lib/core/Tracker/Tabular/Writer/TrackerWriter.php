<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Writer;

class TrackerWriter
{
	private $schema;

	function __construct(\Tracker\Tabular\Schema $schema)
	{
		$this->schema = $schema;
	}

	function sendHeaders()
	{
	}

	function write(\Tracker\Tabular\Source\SourceInterface $source)
	{
		$definition = $this->schema->getDefinition();
		$columns = $this->schema->getColumns();
		$utilities = new \Services_Tracker_Utilities;

		$tx = \TikiDb::get()->begin();

		$lookup = $this->getItemIdLookup();

		foreach ($source->getEntries() as $entry) {
			$info = [
				'itemId' => false,
				'status' => '',
				'fields' => [],
			];

			foreach ($columns as $column) {
				$entry->parseInto($info, $column);
			}
			
			$info['itemId'] = $lookup($info);
			
			if ($info['itemId']) {
				$utilities->updateItem($definition, $info);
			} else {
				$utilities->insertItem($definition, $info);
			}
		}

		$tx->commit();
	}

	private function getItemIdLookup()
	{
		$pk = $this->schema->getPrimaryKey();
		if (! $pk) {
			throw new \Exception(tr('Primary Key not defined'));
		}

		$pkField = $pk->getField();

		if ($pkField == 'itemId') {
			return function ($info) {
				return $info['itemId'];
			};
		} else {
			$table = \TikiDb::get()->table('tiki_tracker_item_fields');
			$definition = $this->schema->getDefinition();
			$f = $definition->getFieldFromPermName($pkField);
			$fieldId = $f['fieldId'];

			return function ($info) use ($table, $pkField, $fieldId) {
				return $table->fetchOne('itemId', [
					'fieldId' => $fieldId,
					'value' => $info['fields'][$pkField],
				]);
			};
		}
	}
}

