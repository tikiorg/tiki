<?php

require_once 'AccessControl/Resolver.php';

class AccessControl_Resolver_Stack implements AccessControl_Resolver
{
	private $stack = array();

	/**
	 * Adds a resolver to the stack. Last resolvers added will
	 * be executed first.
	 */
	function push( AccessControl_Resolver $resolver ) // {{{
	{
		// Push to the beginning to preserve linear order
		// of application in the array
		array_unshift( $this->stack, $resolver );
	} // }}}

	function hasPermission( $permission, array $arguments ) // {{{
	{
		foreach( $this->stack as $resolver ) {
			$ret = $resolver->hasPermission( $permission, $arguments );
			if( self::UNDEFINED !== $ret )
				return $ret;
		}

		return self::UNDEFINED;
	} // }}}
}

?>
