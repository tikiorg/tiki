<?php

require_once 'AccessControl/Resolver.php';

class AccessControl
{
	const ANONYMOUS = '';

	private static $resolver;

	public static function globalResolver() // {{{
	{
		if( self::$resolver )
			return self::$resolver;

		require_once 'AccessControl/Resolver/Stack.php';
		return self::$resolver = new AccessControl_Resolver_Stack;
	} // }}}

	public static function createAccessor( AccessControl_Resolver $resolver = null ) // {{{
	{
		if( is_null( $resolver ) )
			$resolver = self::globalResolver();

		require_once 'AccessControl/Accessor.php';
		return new AccessControl_Accessor( $resolver );
	} // }}}
}

?>
