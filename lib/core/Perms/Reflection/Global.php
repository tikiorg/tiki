<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Reflection_Global implements Perms_Reflection_Container
{
	private $permissions;
	private $factory;

	function __construct( $factory, $type, $object )
	{
		$this->factory = $factory;

		$db = TikiDb::get();
		$this->permissions = new Perms_Reflection_PermissionSet;

		$all = $db->fetchAll('SELECT `groupName`, `permName` FROM `users_grouppermissions`');
		foreach ( $all as $row ) {
			$this->permissions->add($row['groupName'], $row['permName']);
		}
	}

	function add( $group, $permission )
	{
		$userlib = TikiLib::lib('user');
		$userlib->assign_permission_to_group($permission, $group);
	}

	function remove( $group, $permission )
	{
		$userlib = TikiLib::lib('user');
		if ($group != 'Admins' || $permission != 'tiki_p_admin') {
			$userlib->remove_permission_from_group($permission, $group);
		}
	}

	function getDirectPermissions()
	{
		return $this->permissions;
	}

	function getParentPermissions()
	{
	}
}
