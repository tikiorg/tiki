<?php

// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute;

use \Feedback;
use \TikiLib;

/**
 * Custom Route controller
 */
class Controller
{
	/**
	 * Populate a custom route item from the request
	 *
	 * @param array $request
	 * @return Item
	 */
	public function populateFromRequest($request)
	{
		$id = ! empty($request['route']) ? $request['route'] : '';
		$type = isset($request['router_type']) ? $request['router_type'] : '';
		$from = isset($request['router_from']) ? $request['router_from'] : '';
		$description = isset($request['router_description']) ? $request['router_description'] : '';
		$active = empty($request['router_active']) ? 0 : 1;
		$params = [];

		if (! empty($type)) {
			$className = 'Tiki\\CustomRoute\\Type\\' . $type;
			if (! class_exists($className)) {
				Feedback::error(tr('An error occurred; please contact the administrator.'), 'session');
				$this->redirectToAdmin();
			}

			/** @var Type $class */
			$class = new $className();
			$params = $class->parseParams($request);
		}

		return new Item($type, $from, $params, $description, $active, $id);
	}

	/**
	 * Handle the saving the item
	 *
	 * @param array $request
	 * @return array
	 */
	public function saveRequest($request)
	{

		$item = $this->populateFromRequest($request);
		$errors = $item->validate();

		if (empty($errors)) {
			$id = $item->id;
			$item->save();
			$feedback = $id ? tr('Route was updated.') : tr('Route was created.');

			Feedback::success($feedback, 'session');
			$this->redirectToAdmin();
		}

		Feedback::error(['mes' => $errors], 'session');

		return $item->toArray();
	}

	/**
	 * Redirect to the Custom Route admin page
	 */
	private function redirectToAdmin()
	{
		TikiLib::lib('access')->redirect('tiki-admin_routes.php');
		die;
	}
}
