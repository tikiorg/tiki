<?php

class AccessControl_Resolver_StackTest extends PHPUnit_Framework_TestCase
{
	function testDefault()
	{
		$resolver = new AccessControl_Resolver_Stack;

		$this->assertEquals( 
			AccessControl_Resolver::UNDEFINED, 
			$resolver->hasPermission( 'tiki_p_view', array() )
		);
	}

	function testWithMultipleResolver()
	{
		$resolver = new AccessControl_Resolver_Stack;
		$resolver->push( new AccessControl_Resolver_Static( array(
			'tiki_p_edit' => 'n',
			'tiki_p_admin' => 'n',
		) ) );
		$resolver->push( new AccessControl_Resolver_Static( array(
			'tiki_p_edit' => 'y',
		) ) );

		// Never defined
		$this->assertEquals( 
			AccessControl_Resolver::UNDEFINED, 
			$resolver->hasPermission( 'tiki_p_view', array() )
		);

		// Overloaded by second push
		$this->assertEquals( 
			AccessControl_Resolver::ALLOWED, 
			$resolver->hasPermission( 'tiki_p_edit', array() )
		);

		// Only defined in first push
		$this->assertEquals( 
			AccessControl_Resolver::DENIED, 
			$resolver->hasPermission( 'tiki_p_admin', array() )
		);
	}
}

?>
