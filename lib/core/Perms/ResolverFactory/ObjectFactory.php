<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Obtains the object permissions for each object. Bulk loading provides
 * loading for multiple objects in a single query.
 *
 * Parent parameter can be passed during initialization to configure
 * Factory to return parent object permissions. Currently only supports
 * TrackerItem parents (i.e. Trackers) object permissions.
 */
class Perms_ResolverFactory_ObjectFactory implements Perms_ResolverFactory
{
	private $known = array();
	private $parent = '';

	public function __construct($parent = '') {
		$this->parent = $parent;
	}

	function getHash( array $context )
	{
		if (isset($context['type'], $context['object'])) {
			// parent permissions of trackeritems should all go in one hash key, so they share the cache
			// they are essentially the same for all trackeritems since they are tracker permissions
			if ($context['type'] === 'trackeritem' && $this->parent && isset($context['parentId'])) {
				return 'object:tracker:' . $this->cleanObject($context['parentId']);
			} else {
				return 'object:' . $context['type'] . $this->parent . ':' . $this->cleanObject($context['object']);
			}
		} else {
			return '';
		}
	}

	function bulk( array $baseContext, $bulkKey, array $values )
	{
		if ( $bulkKey != 'object' || ! isset($baseContext['type']) ) {
			return $values;
		}

		// only trackeritem parents supported for now
		if( $this->parent && $baseContext['type'] !== 'trackeritem' ) {
			return $values;
		}

		$objects = array();
		$hashes = array();

		// Limit the amount of hashes preserved to reduce memory consumption
		if (count($this->known) > 1024) {
			$this->known = array();
		}

		foreach ( $values as $v ) {
			$hash = $this->getHash(array_merge($baseContext, array( 'object' => $v )));
			if ( ! isset($this->known[$hash]) ) {
				$this->known[$hash] = array();
				$key = md5($baseContext['type'] . $this->cleanObject($v));
				$objects[$key] = $v;
				$hashes[$key] = $hash;
			}
		}

		if ( count($objects) == 0 ) {
			return array();
		}

		$db = TikiDb::get();

		if( $baseContext['type'] === 'trackeritem' && $this->parent ) {
			$bindvars = array();
			$result = $db->fetchAll(
				"SELECT md5(concat('trackeritem', LOWER(tti.`itemId`))) as `objectId`, op.`groupName`, op.`permName`
				FROM `tiki_tracker_items` tti, `users_objectpermissions` op
				WHERE op.`objectType` = 'tracker' AND op.`objectId` = md5(concat('tracker', LOWER(tti.`trackerId`))) AND " .
				$db->in('tti.itemId', array_values($objects), $bindvars),
				$bindvars
			);
		} else {
			$bindvars = array( $baseContext['type'] );
			$result = $db->fetchAll(
				'SELECT `objectId`, `groupName`, `permName` FROM users_objectpermissions WHERE `objectType` = ? AND ' .
				$db->in('objectId', array_keys($objects), $bindvars),
				$bindvars
			);
		}
		$found = array();

		foreach ( $result as $row ) {
			$object = $row['objectId'];
			$group = $row['groupName'];
			$perm = $this->sanitize($row['permName']);
			$hash = $hashes[$object];
			$found[] = $objects[$object];

			if ( ! isset($this->known[$hash][$group] )) {
				$this->known[$hash][$group] = array();
			}

			$this->known[$hash][$group][] = $perm;
		}

		return array_values(array_diff($values, $found));
	}

	function getResolver( array $context )
	{
		if ( ! isset($context['type'], $context['object'] )) {
			return null;
		}

		$hash = $this->getHash($context);

		$this->bulk($context, 'object', array( $context['object'] ));

		if( isset($this->known[$hash]) ) {
			$perms = $this->known[$hash];
		} else {
			$perms = array();
		}

		if ( count($perms) == 0 ) {
			return null;
		} else {
			return new Perms_Resolver_Static($perms, 'object');
		}
	}

	private function sanitize( $name )
	{
		if ( strpos($name, 'tiki_p_') === 0 ) {
			return substr($name, strlen('tiki_p_'));
		} else {
			return $name;
		}
	}

	private function cleanObject($name)
	{
		return TikiLib::strtolower(trim($name));
	}
}
