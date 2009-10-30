<?php

require_once 'lib/core/lib/Perms/Check.php';

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
