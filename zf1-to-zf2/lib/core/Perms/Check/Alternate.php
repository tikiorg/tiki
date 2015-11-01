<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Check_Alternate implements Perms_Check
{
	private $permission;
	private $resolver;
	private $applicableCache = null;

	
	/*
	 * Set the permission to check
	 * @param string $permission - name of the permnission like 'add_object'
	 */
	function __construct( $permission )
	{
		$this->permission = $permission;
	}

	
	/*
	 * Check permission as given by the constructor for a specific list of groups
	 * This function requires that $this->setResolver($resolver) has been set before. Otherwise it will always return false. 
	 * @param Perms_Resolver $resolver - not used
	 * @param array $context - not used
	 * @param string $name - not used
	 * @param array $groups - list of groups to check permission against
	 * @return boolean $hasPermission- true|false  
	 */
	function check( Perms_Resolver $resolver, array $context, $name, array $groups )
	{
		if ( $this->resolver ) {
			return $this->resolver->check($this->permission, $groups);
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
		$this->applicableCache = null;
	}

	
	/*
	 * Get the applicable groups, that is a list of groups that have the permission that is set in the constructor. 
	 * The list is build only once and the result is cached inside the class.
	 * This function requires that $this->setResolver($resolver) has been set before. Otherwise it will always return an empty list. 
	 * @params Perms_Resolver $resolver - not used
	 * @return array $applicableGroups - List of groups  
	 */
	function applicableGroups( Perms_Resolver $resolver ) 
	{
		if ( ! is_null($this->applicableCache) ) {
			return $this->applicableCache;
		}

		$this->applicableCache = array();

		if ($this->resolver) {
			$groups = $this->resolver->applicableGroups();

			foreach ( $groups as $group ) {
				if ( $this->resolver->check($this->permission, array($group)) ) {
					$this->applicableCache[] = $group;
				}
			}
		}

		return $this->applicableCache;
	}
}
