<?php

class AccessControl_DataSource
{
	/**
	 * For a list of provided user IDs, provides a list of permission
	 * names granted to each user. Sample returned array:
	 *
	 * array(
	 *     42 => array( 'tiki_p_view', 'tiki_p_edit' )
	 * )
	 *
	 * @param $userId single user id or list of user id
	 * @return array
	 */
	function getUserGlobalPermissions( $userId ) // {{{
	{
		$userId = (array) $userId;

		$groups = $this->getUserGroups( $userId );
		$permissions = $this->getGroupPermissions( $this->getUniqueElements($groups) );

		$result = array();

		foreach( $userId as $user ) {
			$result[$user] = array();
			foreach( $groups[$user] as $group )
				$result[$user] = array_merge( $result[$user], $permissions[$group] );
		}

		return $result;
	} // }}}

	function getGroupPermissions( $groupname ) // {{{
	{
		$groupname = (array) $groupname;

		$db = TikiDb::get();
		$result = $db->query( 'SELECT groupName, permName FROM users_grouppermissions WHERE ' . $db->in( 'groupName', $groupname ) );

		$out = array_fill_keys( $groupname, array() );

		while( $row = $result->fetchRow() ) {
			$out[ $row['groupName'] ][] = $row['permName'];
		}

		return $out;
	} // }}} 

	function getUserGroups( $userId ) // {{{
	{
		$userId = (array) $userId;

		$db = TikiDb::get();
		$result = $db->query( 'SELECT userId, groupName FROM users_usergroups WHERE ' . $db->in( 'userId', $userId ) );

		// All users have registered by default
		$out = array_fill_keys( $userId, array( 'Registered' ) );

		// Handle exceptional case for unregistered users
		if( isset( $out[0] ) ) {
			$out[0] = array( 'Anonymous' );
		}

		while( $row = $result->fetchRow() ) {
			$out[ $row['userId'] ][] = $row['groupName'];
		}

		$inclusionMap = $this->getGroupExpansion( $this->getUniqueElements( $out ) );

		foreach( $out as & $groupList ) {
			$groupList = array_merge( $groupList, $this->gatherParents( $inclusionMap, $groupList ) );
			$groupList = array_unique( $groupList );
		}

		return $out;
	} // }}}

	function getGroupExpansion( $group ) // {{{
	{
		$group = (array) $group;

		$map = array_fill_keys( $group, array() );

		$db = TikiDb::get();

		while( count( $group ) > 0 ) {
			$result = $db->query( 'SELECT includeGroup, groupName FROM tiki_group_inclusion WHERE ' . $db->in( 'includeGroup', $group ) );
			$group = array();

			while( $row = $result->fetchRow() ) {
				if( ! array_key_exists( $row['groupName'], $map ) ) {
					$group[] = $row['groupName'];
					$map[ $row['groupName'] ] = array();
				}

				$map[ $row['includeGroup'] ][] = $row['groupName'];
			}
		}

		return $map;
	} // }}}

	private function gatherParents( $map, $list ) // {{{
	{
		$result = array();

		foreach( $list as $group ) {
			$result = array_merge( $result, $map[$group] );
		}

		if( count( $result ) > 0 )
			return array_merge( $result, $this->gatherParents( $map, $result ) );
		else
			return array();
	} // }}}
	
	function getObjectPermissions( $group, $objectType, $objectId )
	{
		return array();
	}

	function getObjectCategories( $objectType, $objectId )
	{
		return array();
	}

	private function getUniqueElements( $array ) // {{{
	{
		$result = array();

		foreach( $array as $sub ) {
			if( is_array( $sub ) )
				$result = array_merge( $result, $this->getUniqueElements( $sub ) );
			else
				$result[] = $sub;
		}

		return array_unique( $result );
	} // }}}
}

?>
