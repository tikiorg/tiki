<?php

/**
 * @group unit
 * 
 */

class Perms_Check_IndirectTest extends TikiTestCase
{
	function testUnknownIndirectionIsFalse() {
		$indirect = new Perms_Check_Indirect( array(
			'view' => 'admin_wiki',
		) );

		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->never() )
			->method( 'check' );

		$this->assertFalse( $indirect->check( $mock, array(), 'edit', array( 'Admins', 'Anonymous' ) ) );
	}

	function testCallForwarded() {
		$indirect = new Perms_Check_Indirect( array(
			'view' => 'admin_wiki',
		) );

		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->once() )
			->method( 'check' )
			->with( $this->equalTo( 'admin_wiki' ), $this->equalTo( array( 'Admins', 'Anonymous' ) ) )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $indirect->check( $mock, array(), 'view', array( 'Admins', 'Anonymous' ) ) );
	}

	function testCallForwardedWhenFalseToo() {
		$indirect = new Perms_Check_Indirect( array(
			'view' => 'admin_wiki',
		) );

		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->once() )
			->method( 'check' )
			->with( $this->equalTo( 'admin_wiki' ), $this->equalTo( array( 'Admins', 'Anonymous' ) ) )
			->will( $this->returnValue( false ) );

		$this->assertFalse( $indirect->check( $mock, array(), 'view', array( 'Admins', 'Anonymous' ) ) );
	}
}
