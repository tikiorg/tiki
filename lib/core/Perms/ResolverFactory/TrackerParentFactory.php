<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ObjectFactory.php 57971 2016-03-17 20:09:05Z jonnybradley $

/**
 * Obtains the parent tracker object permissions for each object of type trackeritem.
 * Bulk loading provides loading for multiple objects in a single query.
 */
class Perms_ResolverFactory_TrackerParentFactory implements Perms_ResolverFactory
{
	private $known = array();

	function getHash( array $context )
	{
		if ( isset( $context['type'], $context['object'] ) && $context['type'] === 'trackeritem' ) {
			return 'object:trackeritemparent:' . $this->cleanObject($context['object']);
		} else {
			return '';
		}
	}

	function bulk( array $baseContext, $bulkKey, array $values )
	{
		if ( $bulkKey != 'object' || ! isset($baseContext['type']) || $baseContext['type'] !== 'trackeritem' ) {
			return $values;
		}

		// Limit the amount of hashes preserved to reduce memory consumption
		if (count($this->known) > 128) {
			$this->known = array();
		}

		if ( count($values) == 0 ) {
			return array();
		}

		foreach( $values as $v ) {
			$hash = $this->getHash(array_merge($baseContext, array( 'object' => $v )));
			if ( ! isset($this->known[$hash]) ) {
				$this->known[$hash] = array();
			}
		}


		$db = TikiDb::get();

		$bindvars = array();
		$result = $db->fetchAll(
			"SELECT tti.`itemId`, op.`groupName`, op.`permName` FROM `tiki_tracker_items` tti, `users_objectpermissions` op
			WHERE op.`objectType` = 'tracker' AND op.`objectId` = md5(concat('tracker', LOWER(tti.`trackerId`))) AND " .
			$db->in('tti.itemId', $values, $bindvars),
			$bindvars
		);
		$found = array();

		foreach ( $result as $row ) {
			$itemId = $row['itemId'];
			$group = $row['groupName'];
			$perm = $this->sanitize($row['permName']);
			$hash = $this->getHash(array_merge($baseContext, array( 'object' => $itemId )));
			
			$found[] = $itemId;

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
