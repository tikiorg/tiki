<?php

require_once 'lib/core/lib/Perms/Resolver.php';

class Perms_Resolver_Default implements Perms_Resolver
{
	private $value;

	function __construct( $value ) {
		$this->value = (bool) $value;
	}

	function check( $name, array $groups ) {
		return $this->value;
	}
}

