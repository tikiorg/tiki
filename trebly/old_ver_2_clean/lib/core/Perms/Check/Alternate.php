<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/Check.php';

class Perms_Check_Alternate implements Perms_Check
{
	private $permission;
	private $resolver;

	function __construct( $permission ) {
		$this->permission = $permission;
	}

	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) {
		if( $this->resolver ) {
			return $this->resolver->check( $this->permission, $groups );
		} else {
			return false;
		}
	}

	function setResolver( $resolver ) {
		$this->resolver = $resolver;
	}
}
