<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Check_Creator implements Perms_Check
{
	private $user;
	private $key;
	private $suffix;
	
	
	/*
	 * Initilize Class
	 * @param string $user
	 * @param string $key - the key used in the context array as the user
	 * @params string $suffix - suffix appended to the permission name in $this->check() 
	 */
	function __construct( $user, $key = 'creator', $suffix = '_own' ) 
	{
		$this->user = $user;
		$this->key = $key;
		$this->suffix = $suffix;
	}

	
	
	/*
	 * Check a specific permission against those given by the constructor for a specific list of groups
	 * This function requires that $this->setResolver($resolver) has been set before. Otherwise it will always return false.
	 * @param Perms_Resolver $resolver
	 * @param array $context - context must have a key $key and with the value $user as set in the constructor. Otherwise check will fail. 
	 * @param string $name - permission name to check
	 * @param array $groups - list of groups to check permission against
	 * @return boolean $hasPermission- true|false
	 */
	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) 
	{
		if ( isset( $context[$this->key] ) && $context[$this->key] == $this->user ) {
			return $resolver->check($name . $this->suffix, $groups);
		}

		return false;
	}

	
	/*
	 * Get the applicable groups
	 * @params Perms_Resolver $resolver
	 * @return array $applicableGroups - List of groups
	 */	
	function applicableGroups( Perms_Resolver $resolver ) 
	{
		return $resolver->applicableGroups();
	}
}
