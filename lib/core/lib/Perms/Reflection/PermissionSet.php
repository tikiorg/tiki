<?php

class Perms_Reflection_PermissionSet
{
	private $set = array();

	function add( $group, $permission ) {
		if( is_array( $permission ) ) {
			foreach( $permission as $p ) {
				$this->addOne( $group, $p );
			}
		} else {
			$this->addOne( $group, $permission );
		}
	}

	private function addOne( $group, $permission ) {
		if( ! $this->has( $group, $permission ) ) {
			if( ! isset( $this->set[ $group ] ) ) {
				$this->set[ $group ] = array();
			}

			$this->set[ $group ][] = $permission;
		}
	}

	function has( $group, $permission ) {
		return isset( $this->set[ $group ] )
			&& in_array( $permission, $this->set[ $group ] );
	}

	function getPermissionArray() {
		return $this->set;
	}
}
