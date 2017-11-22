<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Controller for custom routes
 */
class Services_CustomRoute_Controller
{
	/**
	 * @var \Tiki\CustomRoute\CustomRouteLib
	 */
	protected $lib;

	/**
	 * @var TikiAccessLib
	 */
	protected $access;

	public function setUp()
	{
		$this->lib = TikiLib::lib('custom_route');
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
	public function action_remove($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');

		$routeId = $input->routeId->int();
		$confirm = $input->confirm->int();

		$route = $this->lib->getRoute($routeId);

		if (! $route) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$this->lib->removeRoute($routeId);

			return [
				'routeId' => 0,
			];
		}

		return [
			'routeId' => $routeId,
			'from_path' => $route['from'],
		];
	}
}
