<?php

class Perms_BaseTest extends TikiTestCase
{
	function testWithoutConfiguration() {
		$accessor = Perms::get();

		$expect = new Perms_Accessor;

		$this->assertEquals( $expect, $accessor );
	}

	function testGroupsPropagateToAccessor() {
		$perms = new Perms;
		$perms->setGroups( array( 'Registered', 'Administrator' ) );
		Perms::set( $perms );

		$expect = new Perms_Accessor;
		$expect->setGroups( array( 'Registered', 'Administrator' ) );

		$this->assertEquals( $expect, Perms::get() );
	}

	/**
	 * @dataProvider resolverMatches
	 */
	function testResolverFactoryChaining( $context, $expectedResolver ) {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_TestFactory( array( 'object' ), array(
				'a' => $rA = new Perms_Resolver_Default( true ),
				'b' => $rB = new Perms_Resolver_Default( true ),
			) ),
			new Perms_ResolverFactory_TestFactory( array( 'category' ), array(
				'1' => $r1 = new Perms_Resolver_Default( true ),
				'2' => $r2 = new Perms_Resolver_Default( true ),
			) ),
			new Perms_ResolverFactory_TestFactory( array(), array(
				'' => $rG = new Perms_Resolver_Default( true ),
			) ),
		) );
		Perms::set( $perms );

		$this->assertSame( $$expectedResolver, Perms::get( $context )->getResolver() );
	}

	function resolverMatches() {
		return array(
			'testObjectA' => array( array( 'object' => 'a' ), 'rA' ),
			'testObjectB' => array( array( 'object' => 'b' ), 'rB' ),
			'testCategoryIgnoredWhenObjectMatches' => array( array( 'object' => 'b', 'category' => '1' ), 'rB' ),
			'testCategoryObtainOnObjectMiss' => array( array( 'object' => 'c', 'category' => '1' ), 'r1' ),
			'testCategoryOnly' => array( array( 'category' => 2 ), 'r2' ),
			'testObjectAndCategoryMiss' => array( array( 'object' => 'd', 'category' => '3' ), 'rG' ),
			'testNoContext' => array( array(), 'rG' ),
		);
	}

	function testResolverNotCalledTwiceWhenFound() {
		$mock = $this->getMock( 'Perms_ResolverFactory' );
		$mock->expects($this->exactly(2))
			->method( 'getHash' )
			->will( $this->returnValue( '123' ) );
		$mock->expects($this->once())
			->method( 'getResolver' )
			->will( $this->returnValue( new Perms_Resolver_Default(true) ) );

		$perms = new Perms;
		$perms->setResolverFactories( array(
			$mock,
		) );
		Perms::set( $perms );
		
		Perms::get();
		Perms::get();
	}

	function testResolverNotCalledTwiceWhenNotFound() {
		$mock = $this->getMock( 'Perms_ResolverFactory' );
		$mock->expects($this->exactly(2))
			->method( 'getHash' )
			->will( $this->returnValue( '123' ) );
		$mock->expects($this->once())
			->method( 'getResolver' )
			->will( $this->returnValue( null ) );

		$perms = new Perms;
		$perms->setResolverFactories( array(
			$mock,
		) );
		Perms::set( $perms );
		
		Perms::get();
		Perms::get();
	}
}

