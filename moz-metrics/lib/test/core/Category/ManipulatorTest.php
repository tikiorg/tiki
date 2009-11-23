<?php

class Category_ManipulatorTest extends TikiTestCase
{
	function testSimpleManipulation() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 10 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 3, 7 ), $manip->getRemovedCategories() );
	}

	function testManipulationWithoutSpecifyingManaged() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 3, 7 ), $manip->getRemovedCategories() );
	}

	function testLimitationOnRange() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 3 ), $manip->getRemovedCategories() );
	}

	function testNotAllowedToModifyObject() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_TestFactory( array( 'type', 'object' ), array(
				'wiki page:Hello World' => new Perms_Resolver_Default( false ),
			) ),
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array(), $manip->getAddedCategories() );
		$this->assertEquals( array(), $manip->getRemovedCategories() );
	}

	function testCannotAddAny() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_TestFactory( array( 'type', 'object' ), array(
				'category:4' => new Perms_Resolver_Default( false ),
			) ),
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array(), $manip->getAddedCategories() );
		$this->assertEquals( array( 3 ), $manip->getRemovedCategories() );
	}

	function testCannotRemoveAny() {
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_TestFactory( array( 'type', 'object' ), array(
				'category:3' => new Perms_Resolver_Default( false ),
			) ),
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4 ), $manip->getAddedCategories() );
		$this->assertEquals( array(), $manip->getRemovedCategories() );
	}
}
