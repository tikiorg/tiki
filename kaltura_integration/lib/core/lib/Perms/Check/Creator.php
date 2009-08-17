<?php

require_once 'lib/core/lib/Perms/Check.php';

class Perms_Check_Creator implements Perms_Check
{
	private $user;
	private $key;
	private $suffix;
	
	function __construct( $user, $key = 'creator', $suffix = '_own' ) {
		$this->user = $user;
		$this->key = $key;
		$this->suffix = $suffix;
	}

	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) {
		if( isset( $context[$this->key] ) && $context[$this->key] == $this->user ) {
			return $resolver->check( $name . $this->suffix, $groups );
		}

		return false;
	}
}
