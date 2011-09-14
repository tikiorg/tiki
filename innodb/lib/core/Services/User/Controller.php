<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_Controller
{
	function action_list_users($input)
	{
		$groupIds = $input->groupIds->int();
		$offset = $input->offset->int();
		$maxRecords = $input->maxRecords->int();
		
		$groupFilter = '';

		if (is_array($groupIds)) {
			$table = TikiDb::get()->table('users_groups');
			$groupFilter = $table->fetchColumn('groupName', array(
				'id' => $table->in($groupIds),
			));
		}

		$result = TikiLib::lib('user')->get_users($offset, $maxRecords, 'login_asc', '', '', false, $groupFilter);

		return array(
			'result' => $result['data'],
			'count' => $result['cant'],
		);
	}
}

