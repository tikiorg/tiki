<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/Check.php';

class Perms_Check_Indirect implements Perms_Check
{
	private $map;

	function __construct( array $map ) {
		$this->map = $map;
	}

	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) {
		if( isset( $this->map[$name] ) ) {
			return $resolver->check( $this->map[$name], $groups );
		} else {
			return false;
		}
	}

	function applicableGroups( Perms_Resolver $resolver ) {
		return $resolver->applicableGroups();
	}
}
