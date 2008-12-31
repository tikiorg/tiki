<?php

require_once 'AccessControl/Resolver.php';

class AccessControl_Accessor
{
	private $resolver;
	private $arguments = array();

	function __construct( AccessControl_Resolver $resolver )
	{
		$this->resolver = $resolver;
	}

	function __get( $name )
	{
		if( strpos( $name, 'tiki_p_' ) !== 0 )
			$name = 'tiki_p_' . $name;

		$val = $this->resolver->hasPermission( $name, $this->arguments );

		if( $val === AccessControl_Resolver::UNDEFINED ) {
			require_once 'AccessControl/Exception.php';
			throw new AccessControl_Exception( "Permission $name undefined." );
		}

		return $val === AccessControl_Resolver::ALLOWED;
	}
}

?>
