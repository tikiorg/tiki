<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Applier
{
	private $objects = array();
	private $restriction = false;

	function addObject( Perms_Reflection_Container $object ) {
		$this->objects[] = $object;
	}

	function apply( Perms_Reflection_PermissionSet $set ) {
		foreach( $this->objects as $object ) {
			$this->applyOnObject( $object, $set );
		}
	}

	function restrictPermissions( array $permissions ) {
		$this->restriction = array_fill_keys( $permissions, true );
	}

	private function applyOnObject( $object, $set ) {
		$current = $object->getDirectPermissions();
		$parent = $object->getParentPermissions();

		if( $parent ) {
			$comparator = new Perms_Reflection_PermissionComparator( $set, $parent );

			if( $comparator->equal() && $this->isPossible( $current, $set ) ) {
				$null = new Perms_Reflection_PermissionSet;

				$this->realApply( $object, $current, $null );
				return;
			}
		}

		$this->realApply( $object, $current, $set );
	}

	private function isPossible( $current, $target ) {
		if( $this->restriction === false ) {
			return true;
		}

		$comparator = new Perms_Reflection_PermissionComparator( $current, $target );
		$changes = array_merge( $comparator->getAdditions(), $comparator->getRemovals() );
		
		foreach( $changes as $addition ) {
			list( $group, $permission ) = $addition;
			if( ! isset( $this->restriction[$permission] ) ) {
				return false;
			}
		}

		return true;
	}

	private function realApply( $object, $current, $target ) {
		$comparator = new Perms_Reflection_PermissionComparator( $current, $target );

		foreach( $comparator->getAdditions() as $addition ) {
			list( $group, $permission ) = $addition;
			$this->attempt( $object, 'add', $group, $permission );
		}

		foreach( $comparator->getRemovals() as $removal ) {
			list( $group, $permission ) = $removal;
			$this->attempt( $object, 'remove', $group, $permission );
		}
	}

	private function attempt( $object, $method, $group, $permission ) {
		if( $this->restriction === false || isset( $this->restriction[$permission] ) ) {
			call_user_func( array( $object, $method ), $group, $permission );
		}
	}
}
