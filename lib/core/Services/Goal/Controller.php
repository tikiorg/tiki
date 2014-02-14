<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Goal_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('goal_enabled');
		if (! $GLOBALS['user']) {
			throw new Services_Exception_Denied(tr('Authentication required'));
		}
	}

	function action_show($input)
	{
		global $user;
		$goallib = TikiLib::lib('goal');
		$info = $goallib->fetchGoal($input->goalId->int());

		$info = $goallib->evaluateConditions($info, [
			'user' => $user,
		]);

		return array(
			'title' => $info['name'],
			'goal' => $info,
		);
	}
}

