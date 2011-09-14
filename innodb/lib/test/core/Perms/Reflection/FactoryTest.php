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

class Perms_Reflection_FactoryTest extends TikiTestCase
{
	function testEmptyFactory() {
		$factory = new Perms_Reflection_Factory;

		$this->assertNull( $factory->get( 'wiki page', 'HomePage' ) );
	}

	function testConfiguredPositive() {
		$factory = new Perms_Reflection_Factory;
		$factory->register( 'wiki page', 'Perms_Reflection_Object' );

		$expect = new Perms_Reflection_Object( $factory, 'wiki page', 'HomePage' );
		$get = $factory->get( 'wiki page', 'HomePage' );

		$this->assertEquals( $expect, $get );
	}

	function testConfiguredNegative() {
		$factory = new Perms_Reflection_Factory;
		$factory->register( 'category', 'Perms_Reflection_Category' );

		$this->assertNull( $factory->get( 'wiki page', 'HomePage' ) );
	}

	function testFallback() {
		$factory = new Perms_Reflection_Factory;
		$factory->registerFallback( 'Perms_Reflection_Object' );

		$this->assertEquals(
			new Perms_Reflection_Object( $factory, 'wiki page', 'HomePage' ),
			$factory->get( 'wiki page', 'HomePage' ) );
	}

	function testFallbackPrevented() {
		$factory = new Perms_Reflection_Factory;
		$factory->registerFallback( 'Perms_Reflection_Object' );
		$factory->register( 'category', 'Perms_Reflection_Category' );

		$expect = new Perms_Reflection_Category( $factory, 'category', 4 );
		$this->assertEquals( $expect, $factory->get( 'category', 4 ) );
	}

	function testDefaultConfiguration() {
		$expect = new Perms_Reflection_Factory;
		$expect->registerFallback( 'Perms_Reflection_Object' );
		$expect->register( 'category', 'Perms_Reflection_Category' );
		$expect->register( 'global', 'Perms_Reflection_Global' );

		$this->assertEquals( $expect, Perms_Reflection_Factory::getDefaultFactory() );
	}
}
