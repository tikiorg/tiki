<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute;

use TikiLib;

/**
 * Class CustomRouteLib
 */
class CustomRouteLib extends TikiLib
{

	protected $tableName = 'tiki_custom_route';

	/**
	 * List all routes or fetch one route
	 *
	 * @param null $routeId
	 * @return mixed
	 */
	public function getRoute($routeId = null)
	{

		$routeTable = $this->table($this->tableName);

		$conditions = [];

		if ($routeId) {
			$conditions['id'] = $routeId;
			return $routeTable->fetchRow([], $conditions);
		}

		return $routeTable->fetchAll([], $conditions);
	}

	/**
	 * Insert or Update a route
	 * @param $type
	 * @param $from
	 * @param $redirect
	 * @param $description
	 * @param $active
	 * @param $id
	 */
	public function setRoute($type, $from, $redirect, $description, $active, $id)
	{

		$values = [
		'type' => $type,
		'from' => $from,
		'redirect' => $redirect,
		'description' => $description,
		'active' => $active
		];

		$routeTable = $this->table($this->tableName);

		if (! $id) {
			$routeTable->insert($values);
		} else {
			$routeTable->update($values, ['id' => $id]);
		}
	}

	/**
	 * Remove the route
	 *
	 * @param $routeId
	 */
	public function removeRoute($routeId)
	{

		$routeTable = $this->table($this->tableName);
		$routeTable->delete(['id' => $routeId]);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action('Removed', $routeId, 'custom_route');
	}

	public function getTikiPath($path)
	{

		$routeTable = $this->table($this->tableName);
		$conditions = [];

		$conditions['sef_url'] = $path;
		return $routeTable->fetchRow(['tiki_url'], $conditions);
	}

	/**
	 * Get the available routes for a specific type
	 *
	 * @param array $type
	 * @param array $orderClause
	 * @param int $onlyActive
	 * @return mixed
	 */
	public function getRouteByType($type = [], $orderClause = [], $onlyActive = 1)
	{

		$routeTable = $this->table($this->tableName);

		$conditions = [];

		if (! empty($type)) {
			$conditions['type'] = $routeTable->in($type);
		}

		if ($onlyActive) {
			$conditions['active'] = 1;
		}

		return $routeTable->fetchAll([], $conditions, -1, -1, $orderClause);
	}

	/**
	 * Checks if there is a router already defined
	 *
	 * @param $from
	 * @param $id
	 *
	 * @return bool
	 * 	True router is already defined, false otherwise.
	 */
	public function checkRouteExists($from, $id = null)
	{

		$routeTable = $this->table($this->tableName);

		$conditions = [
		'from' => $from,
		];

		if (! empty($id)) {
			$conditions['id'] = $routeTable->not($id);
		}

		$duplicateId = $routeTable->fetchOne('id', $conditions);

		return $duplicateId !== false;
	}
}
