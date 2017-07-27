<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Writer;

class TrackerWriter
{
	function sendHeaders()
	{
	}

	function write(\Tracker\Tabular\Source\SourceInterface $source)
	{
		$schema = $source->getSchema();

		$definition = $schema->getDefinition();
		$columns = $schema->getColumns();
		$utilities = new \Services_Tracker_Utilities;

		$tx = \TikiDb::get()->begin();

		$lookup = $this->getItemIdLookup($schema);

		if ($schema->isImportTransaction()) {
			$errors = array();
			foreach ($source->getEntries() as $line => $entry) {
				$info = [
					'itemId' => false,
					'fields' => [],
				];

				foreach ($columns as $column) {
					$entry->parseInto($info, $column);
				}
				
				$info['itemId'] = $lookup($info);

				if (!$schema->canImportUpdate() && $info['itemId']) {
					continue;
				}
				
				$lineErrors = $utilities->validateItem($definition, $info);
				foreach ($lineErrors as $error) {
					$errors[] = tr('Line %0:', $line+1).' '.$error;
				}
			}
			if (count($errors) > 0) {
				\Feedback::error(array(
					'title' => tr('Import file contains errors. Please review and fix before importing.'),
					'mes' => $errors
				));
				$tx->commit();
				return false;
			}
		}

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

			if (!$schema->canImportUpdate() && $info['itemId']) {
				continue;
			}
			
			if ($info['itemId']) {
				$utilities->updateItem($definition, $info);
			} else {
				$utilities->insertItem($definition, $info);
			}
		}

		$tx->commit();
		return true;
	}

	private function getItemIdLookup($schema)
	{
		$pk = $schema->getPrimaryKey();
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
			$definition = $schema->getDefinition();
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

