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
		$utilities = new \Services_Tracker_Utilities;
		$schema = $source->getSchema();

		$iterate = function($callback) use ($source, $schema) {
			$columns = $schema->getColumns();

			$tx = \TikiDb::get()->begin();

			$lookup = $this->getItemIdLookup($schema);

			$result = array();

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

				if ($schema->ignoreImportBlanks()) {
					$info['fields'] = array_filter($info['fields']);
				}

				$result[] = $callback($line, $info);
			}

			$tx->commit();

			return call_user_func_array('array_merge', $result);
		};

		if ($schema->isImportTransaction()) {
			$errors = $iterate(function($line, $info) use ($errors, $utilities, $schema) {
				return array_map(
					function($error) use ($line) {
						return tr('Line %0:', $line+1).' '.$error;
					},
					$utilities->validateItem($schema->getDefinition(), $info)
				);
			});

			if (count($errors) > 0) {
				\Feedback::error(array(
					'title' => tr('Import file contains errors. Please review and fix before importing.'),
					'mes' => $errors
				));
				return false;
			}
		}

		$iterate(function($line, $info) use ($utilities, $schema) {
			$definition = $schema->getDefinition();
			if ($info['itemId']) {
				$success = $utilities->updateItem($definition, $info);
			} else {
				$success = $utilities->insertItem($definition, $info);
			}
			return $success;
		});

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

