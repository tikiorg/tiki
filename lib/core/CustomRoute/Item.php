<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute;

use \TikiLib;

/**
 * Custom route item
 */
class Item
{
	public $id;
	public $type;
	public $from;
	public $redirect;
	public $description;
	public $active;

	/**
	 * Item constructor.
	 *
	 * @param $type
	 * @param $from
	 * @param $redirect
	 * @param $description
	 * @param int $active
	 * @param null $id
	 */
	public function __construct($type, $from, $redirect, $description, $active = 1, $id = null)
	{
		$this->type = $type;
		$this->from = $from;
		$this->redirect = is_array($redirect) ? json_encode($redirect) : $redirect;
		$this->description = $description;
		$this->active = $active;
		$this->id = $id;
	}

	/**
	 * Save item in database
	 */
	public function save()
	{
		/** @var \CustomRouteLib $routeLib */
		$routeLib = TikiLib::lib('custom_route');
		$routeLib->setRoute($this->type, $this->from, $this->redirect, $this->description, $this->active, $this->id);
	}

	/**
	 * Load a custom route by ID
	 *
	 * @param $id
	 * @return array|null|Item
	 */
	public static function load($id)
	{
		/** @var \CustomRouteLib $routeLib */
		$routeLib = TikiLib::lib('custom_route');
		$details = $routeLib->getRoute($id);

		if (empty($details)) {
			return null;
		}

		return new self(
			$details['type'],
			$details['from'],
			$details['redirect'],
			$details['description'],
			$details['active'],
			$details['id']
		);
	}

	/**
	 * Check if a given path matches a custom route
	 *
	 * @param $path
	 * @return bool|string
	 */
	public function getRedirectPath($path)
	{
		global $tikilib;

		switch ($this->type) {
			case 'Direct':
				if ($path === $this->from) {
					$redirectDetails = json_decode($this->redirect, true);
					return $redirectDetails['to'];
				}
				break;

			case 'Object':
				if ($path === $this->from) {
					$redirectDetails = json_decode($this->redirect, true);

					$type = $redirectDetails['type'];
					$objectId = $redirectDetails['object'];

					if ($type == 'wiki page') {
						$pageName = $tikilib->get_page_name_from_id($objectId);
						$pageSlug = TikiLib::lib('wiki')->get_slug_by_page($pageName);

						if (empty($pageSlug)) {
							return false;
						}

						$objectId = $pageSlug;
					}

					require_once('tiki-sefurl.php');
					$smarty = TikiLib::lib('smarty');
					$smarty->loadPlugin('smarty_modifier_sefurl');
					$url = smarty_modifier_sefurl($objectId, $type);

					return $url;
				}
				break;

			case 'TrackerField':
				$fromRegex = '|' . $this->from . '|';
				preg_match($fromRegex, $path, $matches);

				if (empty($matches[1])) {
					return false;
				}

				$redirectDetails = json_decode($this->redirect, true);

				$trklib = TikiLib::lib('trk');
				$itemId = $trklib->get_item_id(
					$redirectDetails['tracker'],
					$redirectDetails['tracker_field'],
					$matches[1]
				);

				if (empty($itemId)) {
					return false;
				}

				return 'item' . $itemId;

				break;
			default:
				break;
		}

		return false;
	}

	/**
	 * Validate the route requirements are met.
	 *
	 * @return array
	 */
	public function validate()
	{
		$errors = [];

		if (empty($this->from)) {
			$errors[] = tr('From is required');
		}

		$routeLib = TikiLib::lib('custom_route');
		if ($routeLib->checkRouteExists($this->from, $this->id)) {
			$errors[] = tr('There is a route with the same From path already defined.');
		}

		if (empty($this->type)) {
			$errors[] = tr('Type is required');
		}

		/** @var Type $class */
		$className = 'Tiki\\CustomRoute\\Type\\' . $this->type;
		if (class_exists($className)) {
			$class = new $className();

			$params = json_decode($this->redirect, true);
			$errors += $class->validateParams($params);
		} else {
			$errors[] = tr('Selected type is not supported');
		}

		return $errors;
	}

	/**
	 * Converts the Item object into a array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => $this->id,
			'type' => $this->type,
			'from' => $this->from,
			'params' => json_decode($this->redirect, true),
			'description' => $this->description,
			'active' => $this->active,
		];
	}
}
