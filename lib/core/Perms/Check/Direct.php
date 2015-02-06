<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Check_Direct implements Perms_Check
{
	
	/*
	 * Check permission for a specific list of groups 
	 * @param Perms_Resolver $resolver 
	 * @param array $context - not used
	 * @param string $name - name of the permission to check
	 * @param array $groups - list of groups to check permission against
	 * @return boolean $hasPermission- true|false  
	 */
	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) 
	{
		return $resolver->check($name, $groups);
	}

	/*
	 * Get the applicable groups.  
	 * @params Perms_Resolver $resolver - not used
	 * @return array $applicableGroups - List of groups  
	 */
	function applicableGroups( Perms_Resolver $resolver ) 
	{
		return $resolver->applicableGroups();
	}
}
