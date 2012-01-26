<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/Reflection/PermissionSet.php';

class Perms_Reflection_Quick
{
	private $configured = array();
	private $inheritance = array(
		'registered' => array( 'basic' ),
		'editors' => array( 'basic', 'registered' ),
	);

	function configure( $name, array $permissions ) {
		if( $name != 'none' && $name != 'userdefined' ) {
			$this->configured[$name] = $permissions;
		}
	}

	function getPermissions( Perms_Reflection_PermissionSet $current, array $groupMap ) {
		$out = new Perms_Reflection_PermissionSet;

		foreach( $groupMap as $group => $quick ) {
			$this->addPermissions( $out, $current, $group, $quick );
		}

		return $out;
	}

	private function addPermissions( $out, $current, $group, $quick ) {
		if( $quick == 'userdefined' ) {
			$array = $current->getPermissionArray();

			if( isset( $array[$group] ) ) {
				$out->add( $group, $array[$group] );
			}
		} else {
			$out->add( $group, $this->getTypePermissions( $quick ) );
		}
	}

	private function getTypePermissions( $quick ) {
		$out = array();

		if( isset( $this->inheritance[$quick] ) ) {
			foreach( $this->inheritance[$quick] as $parent ) {
				$out = array_merge( $out, $this->getDirectTypePermissions( $parent ) );
			}
		}

		$out = array_merge( $out, $this->getDirectTypePermissions( $quick ) );
		return $out;
	}

	private function getDirectTypePermissions( $type ) {
		if( isset( $this->configured[$type] ) ) {
			return $this->configured[$type];
		} else {
			return array();
		}
	}

	function getAppliedPermissions( Perms_Reflection_PermissionSet $current, array $groupList ) {
		$out = array();
		$permissions = $current->getPermissionArray();

		foreach( $groupList as $group ) {
			if( isset( $permissions[$group] ) ) {
				$out[$group] = $this->getType( $permissions[$group] );
			} else {
				$out[$group] = 'none';
			}
		}

		return $out;
	}

	private function getType( $permissions ) {
		foreach( array_keys( $this->configured ) as $name ) {
			$candidate = $this->getTypePermissions( $name );

			if( count( array_diff( $candidate, $permissions ) ) == 0 
				&& count( array_diff( $permissions, $candidate ) ) == 0 ) {
				return $name;
			}
		}

		return 'userdefined';
	}
}
