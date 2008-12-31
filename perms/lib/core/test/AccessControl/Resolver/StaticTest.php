<?php

class AccessControl_Resolver_StaticTest extends PHPUnit_Framework_TestCase
{
	function testUndefined()
	{
		$resolver = new AccessControl_Resolver_Static( array() );

		$this->assertEquals( 
			AccessControl_Resolver::UNDEFINED, 
			$resolver->hasPermission( 'tiki_p_view', array() )
		);
	}

	function testAllowed()
	{
		$resolver = new AccessControl_Resolver_Static( array(
			'tiki_p_view' => 'y',
		) );

		$this->assertEquals( 
			AccessControl_Resolver::ALLOWED, 
			$resolver->hasPermission( 'tiki_p_view', array() )
		);
	}

	function testDenied()
	{
		$resolver = new AccessControl_Resolver_Static( array(
			'tiki_p_view' => 'n'
		) );

		$this->assertEquals( 
			AccessControl_Resolver::DENIED, 
			$resolver->hasPermission( 'tiki_p_view', array() )
		);
	}
}

?>
