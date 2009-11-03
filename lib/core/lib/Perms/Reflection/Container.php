<?php

interface Perms_Reflection_Container
{
	function add( $group, $permission );
	function remove( $group, $permission );

	function getDirectPermissions();
	function getParentPermissions();
}
