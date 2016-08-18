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
		return array(
			'object_type' => true,
			'object_id' => true,
			'from' => true,
			'to' => true,
		);
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();
		$object_id = $data->object_id->int();
		$from = $data->from->alpha();
		$to = $data->to->alpha();

		if ($object_type != 'trackeritem') {
			return false;
		}

		$valid = array('o', 'p', 'c');
		if (! in_array($from, $valid) || ! in_array($to, $valid)) {
			return false;
		}

		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_item_info($object_id);

		if (! $info || $info['status'] != $from) {
			return false;
		}

		return true;
	}

	function execute(JitFilter $data)
	{
		$trklib = TikiLib::lib('trk');
		$trklib->change_status(array($data->object_id->int()), $data->to->alpha());

		return true;
	}
}

