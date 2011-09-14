<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 * 
 */

class Category_ManipulatorTest extends TikiTestCase
{
	function testSimpleManipulation()
	{
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

	function testManipulationWithoutSpecifyingManaged() 
	{
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

	function testLimitationOnRange() 
	{
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

	function testNotAllowedToModifyObject() 
	{
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

	function testCannotAddAny() 
	{
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

	function testCannotRemoveAny() 
	{
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

	function testDefaultSet() 
	{
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 10 ) );

		$manip->addRequiredSet( range( 6, 10 ), 10 );
		$manip->addRequiredSet( range( 1, 5 ), 5 );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4, 10 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 3, 7 ), $manip->getRemovedCategories() );
	}

	function testConstraintAppliesBeyondPermissions() 
	{
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_TestFactory( array( 'type', 'object' ), array(
				'category:10' => new Perms_Resolver_Default( false ),
			) ),
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setManagedCategories( range( 1, 10 ) );

		$manip->addRequiredSet( range( 6, 10 ), 10 );
		$manip->addRequiredSet( range( 1, 5 ), 5 );

		$manip->setNewCategories( array( 1, 2, 4 ) );

		$this->assertEquals( array( 4, 10 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 3, 7 ), $manip->getRemovedCategories() );
	}

	function testUnmanagedFilter() 
	{
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( true ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setUnmanagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4, 6 ) );

		$this->assertEquals( array( 6 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 7 ), $manip->getRemovedCategories() );
	}

	function testSkipPermissionChecks() 
	{
		$perms = new Perms;
		$perms->setResolverFactories( array(
			new Perms_ResolverFactory_StaticFactory( 'root', new Perms_Resolver_Default( false ) ),
		) );
		Perms::set( $perms );

		$manip = new Category_Manipulator( 'wiki page', 'Hello World' );
		$manip->overrideChecks();
		$manip->setCurrentCategories( array( 1, 2, 3, 7 ) );
		$manip->setUnmanagedCategories( range( 1, 5 ) );

		$manip->setNewCategories( array( 1, 2, 4, 6 ) );

		$this->assertEquals( array( 6 ), $manip->getAddedCategories() );
		$this->assertEquals( array( 7 ), $manip->getRemovedCategories() );
	}
}
