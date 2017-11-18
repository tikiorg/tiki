<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Scheduler_Controller
{

	/**
	 * @var SchedulersLib
	 */
	private $lib;

	/**
	 * @var TikiAccessLib
	 */
	private $access;

	function setUp()
	{
		$this->lib = TikiLib::lib('scheduler');
		$this->access = TikiLib::lib('access');
	}


	/**
	 * Admin user "perform with checked" action to remove selected users
	 *
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

		$schedulerId = $input->schedulerId->int();
		$confirm = $input->confirm->int();

		$scheduler = $this->lib->get_scheduler($schedulerId);

		if (! $scheduler) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$this->lib->remove_scheduler($schedulerId);

			return [
				'schedulerId' => 0,
			];
		}

		return [
			'schedulerId' => $schedulerId,
			'name' => $scheduler['name'],
		];
	}
}
