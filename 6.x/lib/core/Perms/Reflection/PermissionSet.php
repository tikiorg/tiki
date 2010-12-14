<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
	
	function remove( $group, $permission) {
		if( is_array( $permission ) ) {
			foreach( $permission as $p ) {
				$this->removeOne( $group, $p );
			}
		} else {
			$this->removeOne( $group, $permission );
		}
	}

	private function removeOne( $group, $permission ) {
		if( $this->has( $group, $permission ) ) {
			$k = array_search($permission, $this->set[$group]);
			unset($this->set[$group][$k]);
		}
	}

	function getPermissionArray() {
		return $this->set;
	}
}
