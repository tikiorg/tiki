<?php

/**
 * @group unit
 * 
 */

class Perms_Check_DirectTest extends TikiTestCase
{
	function testCallForwarded() {
		$direct = new Perms_Check_Direct;

		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->once() )
			->method( 'check' )
			->with( $this->equalTo( 'view' ), $this->equalTo( array( 'Admins', 'Anonymous' ) ) )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $direct->check( $mock, array(), 'view', array( 'Admins', 'Anonymous' ) ) );
	}

	function testCallForwardedWhenFalseToo() {
		$direct = new Perms_Check_Direct;

		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->once() )
			->method( 'check' )
			->with( $this->equalTo( 'view' ), $this->equalTo( array( 'Admins', 'Anonymous' ) ) )
			->will( $this->returnValue( false ) );

		$this->assertFalse( $direct->check( $mock, array(), 'view', array( 'Admins', 'Anonymous' ) ) );
	}
}
