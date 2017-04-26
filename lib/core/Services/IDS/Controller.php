<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_IDS_Controller
{

	/**
	 * @var TikiAccessLib
	 */
	private $access;

	function setUp()
	{
		$this->access = TikiLib::lib('access');
	}


	/**
	 * @param $input JitFilter
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_remove($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');

		$ruleId = $input->ruleId->int();
		$confirm = $input->confirm->int();

		$rule = IDS_Rule::getRule($ruleId);

		if (!$rule) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$rule->delete();

			return array(
				'ruleId' => 0,
			);
		}

		return array(
			'ruleId' => $ruleId,
		);
	}
}
