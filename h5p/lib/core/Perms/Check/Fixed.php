<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Check_Fixed implements Perms_Check
{
	private $permissions;
	private $resolver;

	/*
	 * Initialize internal permissions array and set each permission to true.
	 * @params array $permissions 
	 */
	function __construct($permissions) 
	{
		$this->permissions = array_fill_keys($permissions, true);
	}

	
	/*
	 * Check a specific permission against those given by the constructor for a specific list of groups
	 * This function requires that $this->setResolver($resolver) has been set before. Otherwise it will always return false. 
	 * @param Perms_Resolver $resolver - not used
	 * @param array $context - not used
	 * @param string $name - permission name to check
	 * @param array $groups - list of groups to check permission against
	 * @return boolean $hasPermission- true|false  
	 */
	function check(Perms_Resolver $resolver, array $context, $name, array $groups) 
	{
		if ($this->resolver && isset($this->permissions[$name])) {
			return $this->resolver->check($name, $groups);
		} else {
			return false;
		}
	}
	
	
	/*
	 * Set the type of resolver to use. Resets the internal cache for applicable groups.
	 * @param Perms_Resolver $resolver
	 */	
	function setResolver( $resolver ) 
	{
		$this->resolver = $resolver;
	}

	
	/*
	 * Get the applicable groups
	 * This function requires that $this->setResolver($resolver) has been set before. Otherwise it will always return an empty list.
	 * @params Perms_Resolver $resolver - not used
	 * @return array $applicableGroups - List of groups
	 */	
	function applicableGroups( Perms_Resolver $resolver ) 
	{
		if ($this->resolver) {
			return $this->resolver->applicableGroups();
		} else {
			return array();
		}
		
	}
}
