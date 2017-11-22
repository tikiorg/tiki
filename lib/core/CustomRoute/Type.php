<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute;

/**
 * Abstract class for custom route types
 */
abstract class Type
{
	protected $errorMessage;

	/**
	 * Get the param definitions required for the route
	 * @return array
	 */
	abstract protected function getParams();

	/**
	 * Get route type name
	 * @return mixed
	 */
	public function getRouteType()
	{
		return (new \ReflectionClass($this))->getShortName();
	}

	/**
	 * Parse the type specific params sent in the custom route form
	 *
	 * @param $routeParams
	 * @return string
	 */
	public function parseParams(array $routeParams)
	{
		$params = [];
		$inputParams = $this->getParams();
		$taskName = strtolower($this->getRouteType());

		foreach ($inputParams as $key => $input) {
			$inputName = $taskName . '_' . $key;
			$params[$key] = $routeParams[$inputName];
		}

		return json_encode($params);
	}

	/**
	 * Checks for errors in the required fields
	 *
	 * @param array $params
	 * @return array
	 */
	public function validateParams(array $params)
	{
		$errors = [];
		$inputParams = $this->getParams();

		foreach ($inputParams as $key => $input) {
			if (empty($input['required'])) {
				continue;
			}

			if (empty($params[$key])) {
				$errors[] = sprintf(tr('%s is required'), $input['name']);
			}
		}

		return $errors;
	}
}
