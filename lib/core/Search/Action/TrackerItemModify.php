<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
			'value' => true,
		);
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();
		$object_id = $data->object_id->int();
		$field = $data->field->word();
		$value = $data->value->text();

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

		return true;
	}

	function execute(JitFilter $data)
	{
		$object_id = $data->object_id->int();
		$field = $data->field->word();
		$value = $data->value->text();

		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_item_info($object_id);

		$definition = Tracker_Definition::get($info['trackerId']);

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

