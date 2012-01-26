<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_ShareGroup
{
	var $name;

	var $selectedValues;

	var $groupPerm;
	var $categPerm;
	var $objectPerm;

	function Tiki_ShareGroup( $name ) // {{{
	{
		$this->name = $name;
		$this->groupPerm = array();
		$this->categPerm = array();
		$this->objectPerm = array();
		$this->selectedValues = array();
	} // }}}

	function addGroupPermission( $permission ) // {{{
	{
		$this->groupPerm[$permission] = 'y';
	} // }}}

	function addCategoryPermission( $source, $permission ) // {{{
	{
		if( ! array_key_exists( $permission, $this->categPerm ) )
			$this->categPerm[$permission] = array();

		$this->categPerm[$permission][] = $source;
	} // }}}

	function addObjectPermission( $permission ) // {{{
	{
		$this->objectPerm[$permission] = 'y';
		$this->selectedValues[] = $permission;
	} // }}}

	function getSourceCategory( $permission ) // {{{
	{
		if( array_key_exists( $permission, $this->categPerm ) )
			return implode( ', ', $this->categPerm[$permission] );

		return '';
	} // }}}

	function getLevel( $permission ) // {{{
	{
		$ret = 'object';

		if( array_key_exists( $permission, $this->categPerm ) )
			$ret = 'category';
		if( array_key_exists( $permission, $this->groupPerm ) )
			$ret = 'group';

		return $ret;
	} // }}}

	function isSelected( $permission ) // {{{
	{
		return in_array( $permission, $this->selectedValues );
	} // }}}

	function hasSelection() // {{{
	{
		return count( $this->selectedValues ) != 0;
	} // }}}

	function setObjectPermissions( $permissions ) // {{{
	{
		// Make sure view is present
		if( in_array( 'tiki_p_edit', $permissions ) && ! in_array( 'tiki_p_view', $permissions ) )
			$permissions[] = 'tiki_p_view';

		// Remove redundant permissions
		$permissions = array_diff( $permissions, array_keys( $this->groupPerm ) );
		$permissions = array_diff( $permissions, array_keys( $this->categPerm ) );
		
		$this->objectPerm = array();
		foreach( $permissions as $p )
			$this->objectPerm[$p] = 'y';

		$this->selectedValues = $permissions;
	} // }}}

	function hasObjectPermission( $name ) // {{{
	{
		return isset( $this->objectPerm[$name] );
	} // }}}
}

class Tiki_ShareObject
{
	var $objectHash;
	var $objectId;
	var $objectType;

	var $loadedPermission;
	var $validGroups;

	function __construct( $objectType, $objectId ) // {{{
	{
		global $Tiki_ShareObject__groups;

		$this->objectHash = md5( $objectType . strtolower( $objectId ) );
		$this->objectType = $objectType;
		$this->objectId = $objectId;

		$this->loadedPermission = array();
		$this->validGroups = array();
		
		if( $Tiki_ShareObject__groups == null )
			$this->loadGroups();
	} // }}}

	function loadGroups() // {{{
	{
		global $tikilib;
		global $Tiki_ShareObject__groups;

		$result = $tikilib->query( "SELECT groupName FROM users_groups ORDER BY groupName" );
		$Tiki_ShareObject__groups = array();

		foreach( $result as $row )
			$Tiki_ShareObject__groups[] = $row['groupName'];
	} // }}}

	function loadPermission( $permissionName ) // {{{
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT groupName FROM users_grouppermissions WHERE permName = ?", array( $permissionName ) );

		while( $row = $result->fetchRow() )
		{
			$group = $this->getGroup( $row['groupName'] );
			$group->addGroupPermission( $permissionName );
		}

		$result = $tikilib->query( "
			SELECT
				groupName,
				tiki_categories.name
			FROM
				tiki_objects
				INNER JOIN tiki_category_objects ON tiki_category_objects.catObjectId = tiki_objects.objectId
				INNER JOIN tiki_categories USING(categId)
				INNER JOIN users_objectpermissions ON objectType = 'category' AND users_objectpermissions.objectId = MD5( CONCAT('category', categId) )
			WHERE
				tiki_objects.type = ?
				AND tiki_objects.itemId = ?
				AND permName = ?
			", array( $this->objectType, $this->objectId, $permissionName ) );

		while( $row = $result->fetchRow() )
		{
			$group = $this->getGroup( $row['groupName'] );
			$group->addCategoryPermission( $row['name'], $permissionName );
		}

		$result = $tikilib->query( "SELECT groupName FROM users_objectpermissions WHERE permName = ? AND objectType = ? AND objectId = ?", array( $permissionName, $this->objectType, $this->objectHash ) );

		while( $row = $result->fetchRow() )
		{
			$group = $this->getGroup( $row['groupName'] );
			$group->addObjectPermission( $permissionName );
		}
	} // }}}

	function getGroup( $name ) // {{{
	{
		global $Tiki_ShareObject__groups;

		if( ! array_key_exists( $name, $this->validGroups ) )
		{
			if( in_array( $name, $Tiki_ShareObject__groups ) )
				$this->validGroups[$name] = new Tiki_ShareGroup( $name );
			else
				return;
		}

		return $this->validGroups[$name];
	} // }}}

	function getValidGroups() // {{{
	{
		ksort( $this->validGroups );
		return array_values( $this->validGroups );
	} // }}}

	function getOtherGroups() // {{{
	{
		global $Tiki_ShareObject__groups;
		return array_diff( $Tiki_ShareObject__groups, array_keys( $this->validGroups ) );
	} // }}}

	function isValid( $name ) // {{{
	{
		return array_key_exists( $name, $this->validGroups );
	} // }}}

	function saveObjectPermissions( $validPermission ) // {{{
	{
		global $tikilib;
		foreach( $validPermission as $permission )
			$tikilib->query( "DELETE FROM users_objectpermissions WHERE objectType = ? AND objectId = ? AND permName = ?",
				array( $this->objectType, $this->objectHash, $permission ) );

		foreach( $this->validGroups as $group )
			foreach( $validPermission as $permission )
				if( $group->hasObjectPermission( $permission ) )
					$tikilib->query( "INSERT INTO users_objectpermissions ( groupName, permName, objectType, objectId ) VALUES( ?, ?, ?, ? )",
						array( $group->name, $permission, $this->objectType, $this->objectHash ) );
	} // }}}
}
