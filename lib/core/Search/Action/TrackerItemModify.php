<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_TrackerItemModify implements Search_Action_Action
{
	function getValues()
	{
		return array(
			'object_type' => true,
			'object_id' => true,
			'field' => true,
			'value' => false,
			'calc' => false,
			'aggregate_fields' => false,
		);
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();
		$object_id = $data->object_id->int();
		$field = $data->field->word();
		$value = $data->value->text();
		$calc = $data->calc->text();
		$aggregateFields = $data->aggregate_fields->array();

		if ($aggregateFields && $object_type != 'aggregate') {
			throw new Search_Action_Exception(tr('Cannot apply tracker_item_modify action to an aggregation type %0.', $object_type));
		}

		if (!$aggregateFields && $object_type != 'trackeritem') {
			throw new Search_Action_Exception(tr('Cannot apply tracker_item_modify action to an object type %0.', $object_type));
		}

		$trklib = TikiLib::lib('trk');

		if ($aggregateFields) {
			foreach ($aggregateFields as $agField => $_) {
				if (! $trklib->get_field_by_perm_name(str_replace('tracker_field_', '', $agField))) {
					throw new Search_Action_Exception(tr('Tracker field %0 not found.', $agField));
				}
			}
			if (! $trklib->get_field_by_perm_name($field)) {
				throw new Search_Action_Exception(tr('Tracker field %0 not found.', $field));
			}
		} else {
			$info = $trklib->get_item_info($object_id);
			if (! $info) {
				throw new Search_Action_Exception(tr('Tracker item %0 not found.', $object_id));
			}
			$definition = Tracker_Definition::get($info['trackerId']);
			if (! $definition->getFieldFromPermName($field)) {
				throw new Search_Action_Exception(tr('Tracker field %0 not found for tracker %1.', $field, $info['trackerId']));
			}
		}

		if( empty($value) && empty($calc) ) {
			throw new Search_Action_Exception(tr('tracker_item_modify action missing value or calc parameter.'));
		}

		return true;
	}

	function execute(JitFilter $data)
	{
		$object_id = $data->object_id->int();
		$aggregateFields = $data->aggregate_fields->array();

		if ($aggregateFields) {
			$unifiedsearchlib = TikiLib::lib('unifiedsearch');
			$index = $unifiedsearchlib->getIndex();
			$query = new Search_Query;
			$unifiedsearchlib->initQuery($query);
			foreach ($aggregateFields as $agField => $value) {
				$query->filterIdentifier($value, $agField);
			}
			$result = $query->search($index);
			foreach ($result as $entry) {
				$this->executeOnItem($entry['object_id'], $data);
			}
		} else {
			$this->executeOnItem($object_id, $data);
		}
		

		return true;
	}

	function requiresInput(JitFilter $data) {
		return empty($data->value->text()) && empty($data->calc->text());
	}

	private function executeOnItem($object_id, $data) {
		$field = $data->field->word();
		$value = $data->value->text();
		$calc = $data->calc->text();

		$trklib = TikiLib::lib('trk');

		$info = $trklib->get_tracker_item($object_id);
		$definition = Tracker_Definition::get($info['trackerId']);

		if( !empty($calc) ) {
			$runner = new Math_Formula_Runner(
				array(
					'Math_Formula_Function_' => '',
					'Tiki_Formula_Function_' => '',
				)
			);
			try {
				$runner->setFormula($calc);
				$data = [];
				foreach ($runner->inspect() as $fieldName) {
					if( is_string($fieldName) || is_numeric($fieldName) ) {
						$data[$fieldName] = $trklib->field_render_value(array('trackerId' => $info['trackerId'], 'permName' => $fieldName, 'item' => $info, 'process' => 'y'));
					}
				}
				$runner->setVariables($data);
				$value = $runner->evaluate();
			} catch( Math_Formula_Exception $e ) {
				throw new Search_Action_Exception(tr('Error applying tracker_item_modify calc formula to item %0: %1', $object_id, $e->getMessage()));
			}
		}

		$utilities = new Services_Tracker_Utilities;
		$utilities->updateItem(
			$definition,
			array(
				'itemId' => $object_id,
				'status' => $info['status'],
				'fields' => array(
					$field => $value,
				),
			)
		);
	}
}

