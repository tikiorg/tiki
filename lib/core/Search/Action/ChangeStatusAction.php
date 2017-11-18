<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_ChangeStatusAction implements Search_Action_Action
{
	function getValues()
	{
		return [
			'object_type' => true,
			'object_id' => true,
			'from' => true,
			'to' => true,
		];
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();
		$object_id = $data->object_id->int();
		$from = $data->from->alpha();
		$to = $data->to->alpha();

		if ($object_type != 'trackeritem') {
			throw new Search_Action_Exception(tr('Cannot apply change_status action to an object type %0.', $object_type));
		}

		$valid = ['o', 'p', 'c'];
		if (! in_array($from, $valid)) {
			throw new Search_Action_Exception(tr('Invalid "from" status specified for change_status action: %0. Should be one of "o", "p" or "c".', $from));
		}
		if (! in_array($to, $valid)) {
			throw new Search_Action_Exception(tr('Invalid "to" status specified for change_status action: %0. Should be one of "o", "p" or "c".', $to));
		}

		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_item_info($object_id);

		if (! $info) {
			throw new Search_Action_Exception(tr('Tracker item %0 not found.', $object_id));
		}

		if ($info['status'] != $from) {
			throw new Search_Action_Exception(tr('Tracker item %0 status %1 is different than the "from" status %2.', $object_id, $info['status'], $from));
		}

		return true;
	}

	function execute(JitFilter $data)
	{
		$trklib = TikiLib::lib('trk');
		$trklib->change_status([$data->object_id->int()], $data->to->alpha());

		return true;
	}

	function requiresInput(JitFilter $data)
	{
		return false;
	}
}
