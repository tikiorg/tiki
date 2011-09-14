<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Reflection_PermissionComparator
{
	private $additions;
	private $removals;

	function __construct( $left, $right ) {
		$this->additions = $this->compare( $right, $left );
		$this->removals = $this->compare( $left, $right );
	}

	function equal() {
		return empty( $this->additions ) && empty( $this->removals );
	}

	function getAdditions() {
		return $this->additions;
	}

	function getRemovals() {
		return $this->removals;
	}

	private function compare( $left, $right ) {
		$out = array();

		$all = $left->getPermissionArray();
		foreach( $all as $group => $permissions ) {
			foreach( $permissions as $perm ) {
				if( ! $right->has( $group, $perm ) ) {
					$out[] = array( $group, $perm );
				}
			}
		}

		return $out;
	}
}
