<?php

/**
 * @group unit
 * 
 */

class Perms_Check_CreatorTest extends TikiTestCase
{
	function testNoActionTakenWhenNoCreator() {
		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->never() )
			->method( 'check' );

		$creator = new Perms_Check_Creator( 'foobar' );
		$this->assertFalse( $creator->check( $mock, array(), 'view', array( 'Registered' ) ) );
	}

	function testNoActionTakenWhenWrongCreator() {
		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->never() )
			->method( 'check' );

		$creator = new Perms_Check_Creator( 'foobar' );
		$this->assertFalse( $creator->check( $mock, array( 'creator' => 'barbaz' ), 'view', array( 'Registered' ) ) );
	}

	function testCallForwarded() {
		$mock = $this->getMock( 'Perms_Resolver' );
		$mock->expects( $this->once() )
			->method( 'check' )
			->with( $this->equalTo( 'view_own' ), $this->equalTo( array( 'Registered' ) ) )
			->will( $this->returnValue( true ) );

		$creator = new Perms_Check_Creator( 'foobar' );
		$this->assertTrue( $creator->check( $mock, array( 'creator' => 'foobar' ), 'view', array( 'Registered' ) ) );
	}
}
