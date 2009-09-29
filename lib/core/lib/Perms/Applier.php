<?php

require_once 'lib/core/lib/Perms/Reflection/Container.php';
require_once 'lib/core/lib/Perms/Reflection/PermissionSet.php';
require_once 'lib/core/lib/Perms/Reflection/PermissionComparator.php';

class Perms_Applier
{
	private $objects = array();

	function addObject( Perms_Reflection_Container $object ) {
		$this->objects[] = $object;
	}

	function apply( Perms_Reflection_PermissionSet $set ) {
		foreach( $this->objects as $object ) {
			$this->applyOnObject( $object, $set );
		}
	}

	private function applyOnObject( $object, $set ) {
		$current = $object->getDirectPermissions();
		$parent = $object->getParentPermissions();

		if( $parent ) {
			$comparator = new Perms_Reflection_PermissionComparator( $set, $parent );

			if( $comparator->equal() ) {
				$this->realApply( $object, $current, new Perms_Reflection_PermissionSet );
				return;
			}
		}

		$this->realApply( $object, $current, $set );
	}

	private function realApply( $object, $current, $target ) {
		$comparator = new Perms_Reflection_PermissionComparator( $current, $target );

		foreach( $comparator->getAdditions() as $addition ) {
			list( $group, $permission ) = $addition;
			$object->add( $group, $permission );
		}

		foreach( $comparator->getRemovals() as $removal ) {
			list( $group, $permission ) = $removal;
			$object->remove( $group, $permission );
		}
	}
}

?>
