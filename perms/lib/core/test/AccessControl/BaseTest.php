<?php

class AccessControl_BaseTest extends PHPUnit_Framework_TestCase
{
	function testSimplePermissionAccess()
	{
		$resolver = new AccessControl_Resolver_Static( array(
			'tiki_p_view' => 'y',
			'tiki_p_edit' => 'n',
		) );
		
		$accessor = AccessControl::createAccessor( $resolver );

		$this->assertTrue( $accessor->tiki_p_view );
		$this->assertTrue( $accessor->view );

		$this->assertFalse( $accessor->tiki_p_edit );
		$this->assertFalse( $accessor->edit );
	}

	/**
	 * @expectedException AccessControl_Exception
	 */
	function testAccessorUndefined()
	{
		$resolver = new AccessControl_Resolver_Static( array() );

		$accessor = AccessControl::createAccessor( $resolver );
		$accessor->foo;
	}

	function testGlobalResolverIsStack()
	{
		$this->assertTrue( AccessControl::globalResolver() instanceof AccessControl_Resolver_Stack );
	}
}

?>
