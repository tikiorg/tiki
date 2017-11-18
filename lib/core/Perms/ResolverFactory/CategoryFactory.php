<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * The category ResolverFactory acts in two steps to resolve the permissions
 * for the object contexts. It first loads the categories for the provided
 * contexts, then load the permissions applicable to each individual category.
 * It then assebles the resolver for the context based on all applicable
 * category and the permissions that apply to them.
 *
 * In bulk load, only two queries are perfomed for all contexts.
 *
 * The category list of each object use within runtime as well as the
 * permissions that apply for each of them is preserved internally. On
 * sequential calls to obtain resolvers, obtaining the list of categories
 * for each object will likely be required, however the query to obtain the
 * permissions on those categories may not be required if those categories
 * were already known.
 *
 * Category permissions apply from the moment one of the categories affected
 * to the object contains one permission. All categories are equal and permissions
 * from all categories are cumulated.
 *
 * Because permissions are applied to all decendents, only the direct categories
 * are considered when resolving permissions.
 *
 * Parent parameter can be passed during initialization to configure
 * Factory to return parent object permissions. Currently only supports
 * TrackerItem parents (i.e. Trackers) category permissions. Parent
 * perissions are retrieved by loading categories for the parent and then
 * checking their permissions.
 */

/**
 * Perms_ResolverFactory_CategoryFactory
 *
 * @uses Perms_ResolverFactory
 */
class Perms_ResolverFactory_CategoryFactory implements Perms_ResolverFactory
{
	private $knownObjects = [];
	private $knownCategories = [];
	private $parent = '';

	public function __construct($parent = '')
	{
		$this->parent = $parent;
	}

	function clear()
	{
		$this->knownObjects = [];
		$this->knownCategories = [];
	}

	/**
	 * Provides a hash matching the full list of ordered categories
	 * applicable to the context.
	 */
	function getHash(array $context)
	{
		if (! isset($context['type'], $context['object'])) {
			return '';
		}

		if ($context['type'] == 'category') {
			// Categories cannot be categorized
			return '';
		}

		$this->bulk($context, 'object', [ $context['object'] ]);

		$key = $this->objectKey($context);

		if (isset($this->knownObjects[$key]) && count($this->knownObjects[$key]) > 0) {
			return 'category:' . implode(':', $this->knownObjects[$key]);
		}
	}

	function bulk(array $baseContext, $bulkKey, array $values)
	{
		if (! isset($baseContext['type']) || $bulkKey != 'object') {
			return $values;
		}

		// only trackeritem parents supported for now
		if ($this->parent && $baseContext['type'] !== 'trackeritem') {
			return $values;
		}

		$newCategories = $this->bulkLoadCategories($baseContext, $bulkKey, $values);
		if (count($newCategories) != 0) {
			$this->bulkLoadPermissions($newCategories);
		}

		$remaining = [];

		foreach ($values as $v) {
			$key = $this->objectKey(array_merge($baseContext, [ 'object' => $v ]));
			if (! isset($this->knownObjects[$key]) || count($this->knownObjects[$key]) == 0) {
				$remaining[] = $v;
			} else {
				$add = true;
				foreach ($this->knownObjects[$key] as $categ) {
					if (count($this->knownCategories[$categ]) > 0) {
						$add = false;
						break;
					}
				}

				if ($add) {
					$remaining[] = $v;
				}
			}
		}

		return $remaining;
	}

	private function bulkLoadCategories($baseContext, $bulkKey, $values)
	{
		$objects = [];
		$keys = [];

		// Reset the internal object cache when it becomes too large
		// Leave the internal category cache intact as it should eventually stabilize
		if (count($this->knownObjects) > 128) {
			$this->knownObjects = [];
		}

		foreach ($values as $v) {
			$key = $this->objectKey(array_merge($baseContext, ['object' => $v]));

			if (! isset($this->knownObjects[$key]) && $baseContext['type'] != 'category') {
				$objects[$this->cleanObject($v)] = $key;
				$this->knownObjects[$key] = [];
			}
		}

		if (count($objects) == 0) {
			return [];
		}

		$db = TikiDb::get();

		if ($baseContext['type'] === 'trackeritem' && $this->parent) {
			$bindvars = [];
			$result = $db->fetchAll(
				"SELECT co.`categId`, ti.`itemId` FROM `tiki_tracker_items` ti
				INNER JOIN `tiki_objects` o ON ti.`trackerId` = o.`itemId` AND o.`type` = 'tracker'
				INNER JOIN `tiki_category_objects` co ON co.`catObjectId` = o.`objectId` WHERE " .
				$db->in('ti.itemId', array_keys($objects), $bindvars) . " ORDER BY co.`catObjectId`, co.`categId`",
				$bindvars
			);
		} else {
			$bindvars = [$baseContext['type']];
			$result = $db->fetchAll(
				'SELECT `categId`, `itemId` FROM `tiki_category_objects` INNER JOIN `tiki_objects` ON `catObjectId` = `objectId` WHERE `type` = ? AND ' .
				$db->in('itemId', array_keys($objects), $bindvars) . ' ORDER BY `catObjectId`, `categId`',
				$bindvars
			);
		}

		$categories = [];

		foreach ($result as $row) {
			$category = (int) $row['categId'];
			$object = $this->cleanObject($row['itemId']);

			if (! isset($objects[$object])) {
				continue; // Some DB corruption combined with MySQL strange casting causes notices
			}

			$key = $objects[$object];
			$this->knownObjects[$key][] = $category;

			if (! isset($this->knownCategories[$category])) {
				$categories[$category] = true;
			}
		}

		return array_keys($categories);
	}

	private function bulkLoadPermissions($categories)
	{
		$objects = [];

		foreach ($categories as $categ) {
			$objects[md5('category' . $categ)] = $categ;
			$this->knownCategories[$categ] = [];
		}

		$db = TikiDb::get();

		$bindvars = [];
		$result = $db->fetchAll(
			'SELECT `objectId`, `groupName`, `permName` FROM `users_objectpermissions` WHERE `objectType` = \'category\' AND ' .
			$db->in('objectId', array_keys($objects), $bindvars),
			$bindvars
		);

		foreach ($result as $row) {
			$object = $row['objectId'];
			$group = $row['groupName'];
			$categ = $objects[$object];

			$perm = $this->sanitize($row['permName']);

			if (! isset($this->knownCategories[$categ][$group])) {
				$this->knownCategories[$categ][$group] = [];
			}

			$this->knownCategories[$categ][$group][] = $perm;
		}
	}

	/**
	 * Merges the permissions available on groups from all categories
	 * that apply to the context. A permission granted on any of the
	 * categories will be added to the pool.
	 */
	function getResolver(array $context)
	{
		if (! isset($context['type'], $context['object'])) {
			return null;
		}

		$this->bulk($context, 'object', [$context['object']]);

		$key = $this->objectKey($context);

		if (isset($this->knownObjects[$key])) {
			$categories = $this->knownObjects[$key];
		} else {
			$categories = [];
		}

		$perms = [];

		foreach ($categories as $categ) {
			foreach ($this->knownCategories[$categ] as $group => $partialPerms) {
				if (! isset($perms[$group])) {
					$perms[$group] = [];
				}

				$perms[$group] = array_merge($perms[$group], array_combine($partialPerms, $partialPerms));
			}
		}

		foreach ($perms as & $p) {
			$p = array_values($p);
		}

		if (count($perms) === 0) {
			return null;
		} else {
			return new Perms_Resolver_Static($perms, 'category');
		}
	}

	private function sanitize($name)
	{
		if (strpos($name, 'tiki_p_') === 0) {
			return substr($name, strlen('tiki_p_'));
		} else {
			return $name;
		}
	}

	private function objectKey($context)
	{
		return $context['type'] . $this->parent . $this->cleanObject($context['object']);
	}

	private function cleanObject($name)
	{
		return trim($name);
	}
}
