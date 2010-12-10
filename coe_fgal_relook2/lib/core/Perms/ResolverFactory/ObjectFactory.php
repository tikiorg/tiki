<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/ResolverFactory.php';

/**
 * Obtains the object permissions for each object. Bulk loading provides
 * loading for multiple objects in a single query.
 */
class Perms_ResolverFactory_ObjectFactory implements Perms_ResolverFactory
{
	private $known = array();

	function getHash( array $context ) {
		if( isset( $context['type'], $context['object'] ) ) {
			return 'object:' . $context['type'] . ':' . strtolower( $context['object'] );
		} else {
			return '';
		}
	}

	function bulk( array $baseContext, $bulkKey, array $values ) {
		if( $bulkKey != 'object' || ! isset( $baseContext['type'] ) ) {
			return $values;
		}
		
		$objects = array();
		$hashes = array();

		// Limit the amount of hashes preserved to reduce memory consumption
		if (count($this->known) > 128) {
			$this->known = array();
		}

		foreach( $values as $v ) {
			$hash = $this->getHash( array_merge( $baseContext, array( 'object' => $v ) ) );
			if( ! isset( $this->known[$hash] ) ) {
				$this->known[$hash] = array();
				$key = md5( $baseContext['type'] . strtolower( $v ) );
				$objects[$key] = $v;
				$hashes[$key] = $hash;
			}
		}

		if( count( $objects ) == 0 ) {
			return array();
		}

		$db = TikiDb::get();

		$bindvars = array( $baseContext['type'] );
		$result = $db->fetchAll( 'SELECT `objectId`, `groupName`, `permName` FROM users_objectpermissions WHERE `objectType` = ? AND ' . $db->in( 'objectId', array_keys( $objects ), $bindvars ), $bindvars );
		$found = array();

		foreach( $result as $row ) {
			$object = $row['objectId'];
			$group = $row['groupName'];
			$perm = $this->sanitize( $row['permName'] );
			$hash = $hashes[$object];
			$found[] = $objects[$object];

			if( ! isset( $this->known[$hash][$group] ) ) {
				$this->known[$hash][$group] = array();
			}

			$this->known[$hash][$group][] = $perm;
		}

		return array_values( array_diff( $values, $found ) );
	}

	function getResolver( array $context ) {
		if( ! isset( $context['type'], $context['object'] ) ) {
			return null;
		}

		$hash = $this->getHash( $context );

		$this->bulk( $context, 'object', array( $context['object'] ) );

		$perms = $this->known[$hash];

		if( count( $perms ) == 0 ) {
			return null;
		} else {
			require_once 'lib/core/Perms/Resolver/Static.php';
			return new Perms_Resolver_Static( $perms, 'object' );
		}
	}

	private function sanitize( $name ) {
		if( strpos( $name, 'tiki_p_' ) === 0 ) {
			return substr( $name, strlen( 'tiki_p_' ) );
		} else {
			return $name;
		}
	}
}
