<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute;

use TikiLib;

/**
 * Custom Route
 */
class CustomRoute
{
	/**
	 * Checks if the $path matches a custom route
	 * If so, the user is redirected to the "go to" destination.
	 *
	 * @param $path
	 */
	public static function match($path)
	{
		$access = TikiLib::lib('access');
		$routeLib = TikiLib::lib('custom_route');
		$routes = $routeLib->getRouteByType(null, ['type' => 'asc']);

		foreach ($routes as $row) {
			$route = new Item($row['type'], $row['from'], $row['redirect'], $row['active'], $row['id']);
			if ($redirect = $route->getRedirectPath($path)) {
				$access->redirect($redirect);
			};
		}
	}
}
