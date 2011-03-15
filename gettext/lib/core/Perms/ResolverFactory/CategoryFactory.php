<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/ResolverFactory.php';

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
 */
class Perms_ResolverFactory_CategoryFactory implements Perms_ResolverFactory
{
	private $knownObjects = array();
	private $knownCategories = array();

	/**
	 * Provides a hash matching the full list of ordered categories
	 * applicable to the context.
	 */
	function getHash( array $context ) {
		if( ! isset( $context['type'], $context['object'] ) ) {
			return '';
		}

		$this->bulk( $context, 'object', array( $context['object'] ) );

		$key = $this->objectKey( $context );

		if( count( $this->knownObjects[$key] ) > 0 ) {
			return 'category:' . implode( ':', $this->knownObjects[$key] );
		}
	}

	function bulk( array $baseContext, $bulkKey, array $values ) {
		if( ! isset($baseContext['type']) || $bulkKey != 'object' ) {
			return $values;
		}

		$newCategories = $this->bulkLoadCategories( $baseContext, $bulkKey, $values );
		if( count( $newCategories ) != 0 ) {
			$this->bulkLoadPermissions( $newCategories );
		}

		$remaining = array();

		foreach( $values as $v ) {
			$key = $this->objectKey( array_merge( $baseContext, array( 'object' => $v ) ) );
			if( count( $this->knownObjects[$key] ) == 0 ) {
				$remaining[] = $v;
			} else {
				$add = true;
				foreach( $this->knownObjects[$key] as $categ ) {
					if( count( $this->knownCategories[$categ] ) > 0 ) {
						$add = false;
						break;
					}
				}

				if( $add ) {
					$remaining[] = $v;
				}
			}
		}

		return $remaining;
	}

	private function bulkLoadCategories( $baseContext, $bulkKey, $values ) {
		$objects = array();
		$keys = array();

		// Reset the internal object cache when it becomes too large
		// Leave the internal category cache intact as it should eventually stabilize
		if (count($this->knownObjects) > 128) {
			$this->knownObjects = array();
		}

		foreach( $values as $v ) {
			$key = $this->objectKey( array_merge( $baseContext, array( 'object' => $v ) ) );

			if( ! isset( $this->knownObjects[$key] ) ) {
				$objects[strtolower($v)] = $key;
				$this->knownObjects[$key] = array();
			}
		}

		if( count( $objects ) == 0 ) {
			return array();
		}

		$db = TikiDb::get();
		$bindvars = array( $baseContext['type'] );
		$result = $db->fetchAll( 'SELECT `categId`, `itemId` FROM `tiki_category_objects` INNER JOIN `tiki_objects` ON `catObjectId` = `objectId` WHERE `type` = ? AND ' . $db->in( 'itemId', array_keys( $objects ), $bindvars ) . ' ORDER BY `catObjectId`, `categId`', $bindvars );

		$categories = array();

		foreach( $result as $row ) {
			$category = (int) $row['categId'];
			$object = strtolower($row['itemId']);
			$key = $objects[$object];
			
			$this->knownObjects[$key][] = $category;

			if( ! isset( $this->knownCategories[$category] ) ) {
				$categories[$category] = true;
			}
		}

		return array_keys( $categories );
	}

	private function bulkLoadPermissions( $categories ) {
		$objects = array();

		foreach( $categories as $categ ) {
			$objects[md5( 'category' . $categ )] = $categ;
			$this->knownCategories[$categ] = array();
		}

		$db = TikiDb::get();

		$bindvars = array();
		$result = $db->fetchAll( 'SELECT `objectId`, `groupName`, `permName` FROM `users_objectpermissions` WHERE `objectType` = \'category\' AND ' . $db->in( 'objectId', array_keys( $objects ), $bindvars ), $bindvars );

		foreach( $result as $row ) {
			$object = $row['objectId'];
			$group = $row['groupName'];
			$categ = $objects[$object];

			$perm = $this->sanitize( $row['permName'] );

			if( ! isset( $this->knownCategories[$categ][$group] ) ) {
				$this->knownCategories[$categ][$group] = array();
			}

			$this->knownCategories[$categ][$group][] = $perm;
		}
	}

	/** 
	 * Merges the permissions available on groups from all categories
	 * that apply to the context. A permission granted on any of the 
	 * categories will be added to the pool.
	 */
	function getResolver( array $context ) {
		if( ! isset( $context['type'], $context['object'] ) ) {
			return null;
		}

		$this->bulk( $context, 'object', array( $context['object'] ) );

		$key = $this->objectKey( $context );

		$categories = $this->knownObjects[$key];

		$perms = array();

		foreach( $categories as $categ ) {
			foreach( $this->knownCategories[$categ] as $group => $partialPerms ) {
				if( ! isset( $perms[$group] ) ) {
					$perms[$group] = array();
				}

				$perms[$group] = array_merge( $perms[$group], array_combine( $partialPerms, $partialPerms ) );
			}
		}

		foreach( $perms as & $p ) {
			$p = array_values( $p );
		}

		if( count( $perms ) === 0 ) {
			return null;
		} else {
			return new Perms_Resolver_Static( $perms, 'category' );
		}
	}

	private function sanitize( $name ) {
		if( strpos( $name, 'tiki_p_' ) === 0 ) {
			return substr( $name, strlen( 'tiki_p_' ) );
		} else {
			return $name;
		}
	}

	private function objectKey( $context ) {
		return $context['type'] . strtolower( $context['object'] );
	}
}
