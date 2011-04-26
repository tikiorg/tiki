<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Check_Fixed implements Perms_Check
{
	private $permissions;
	private $resolver;

	function __construct($permissions) {
		$this->permissions = array_fill_keys($permissions, true);
	}

	function check(Perms_Resolver $resolver, array $context, $name, array $groups) {
		if ($this->resolver && isset($this->permissions[$name])) {
			return $this->resolver->check($name, $groups);
		} else {
			return false;
		}
	}

	function setResolver( $resolver ) {
		$this->resolver = $resolver;
	}
}
