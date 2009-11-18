<?php
require_once 'lib/core/lib/Perms/Check.php';

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
}
