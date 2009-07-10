<?php

class Perms_Resolver_Static implements Perms_Resolver
{
	private $known;

	function __construct( array $known ) {
		$this->known = $known;
	}

	function check( $name, array $groups ) {
		foreach( $groups as $groupName ) {
			if( isset( $this->known[$groupName] ) ) {
				if( in_array( $name, $this->known[$groupName] ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

