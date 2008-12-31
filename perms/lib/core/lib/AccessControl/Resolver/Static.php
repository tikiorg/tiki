<?php

require_once 'AccessControl/Resolver.php';

class AccessControl_Resolver_Static implements AccessControl_Resolver
{
	private $defined;

	function __construct( $defined ) // {{{
	{
		$this->defined = $defined;
	} // }}}

	function hasPermission( $permission, array $arguments ) // {{{
	{
		if( isset( $this->defined[$permission] ) )
			return ( $this->defined[$permission] == 'y' ) ? self::ALLOWED : self::DENIED;
		else
			return self::UNDEFINED;
	} // }}}
}

?>
