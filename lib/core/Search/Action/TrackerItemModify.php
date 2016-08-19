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
		);
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();
		$object_id = $data->object_id->int();
		$field = $data->field->word();
		$value = $data->value->text();
		$calc = $data->calc->text();

		if ($object_type != 'trackeritem') {
			return false;
		}

		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_item_info($object_id);

		if (! $info) {
			return false;
		}

		$definition = Tracker_Definition::get($info['trackerId']);

		if (! $definition->getFieldFromPermName($field)) {
			return false;
		}

		if( empty($value) && empty($calc) ) {
			# TODO: make actions return more meaningful error messages
			return false;
		}

		return true;
	}

	function execute(JitFilter $data)
	{
		$object_id = $data->object_id->int();
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
						$field = $definition->getFieldFromPermName($fieldName);
						if( $field && isset($info[$field['fieldId']]) ) {
							$data[$fieldName] = $info[$field['fieldId']];
						}
					}
				}
				$runner->setVariables($data);
				$value = $runner->evaluate();
			} catch( Math_Formula_Exception $e ) {
				# TODO: make actions return more meaningful error messages
				return false;
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

		return true;
	}
}

