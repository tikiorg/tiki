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

class Perms_Check_AlternateTest extends PHPUnit_Framework_TestCase
{
	function testUnconfigured() {
		$resolver = new Perms_Resolver_Default( true );

		$check = new Perms_Check_Alternate( 'admin' );
		$this->assertFalse( $check->check( $resolver, array(), 'view', array( 'Registered' ) ) );
	}

	function testWithReplacementResolver() {
		$resolver = new Perms_Resolver_Default( false );
		$replacement = new Perms_Resolver_Static( array(
			'Registered' => array( 'admin' ),
		) );

		$check = new Perms_Check_Alternate( 'admin' );
		$check->setResolver( $replacement );
		$this->assertTrue( $check->check( $resolver, array(), 'view', array( 'Registered' ) ) );
	}

	function testWithReplacementNotAllowing() {
		$resolver = new Perms_Resolver_Default( false );
		$replacement = new Perms_Resolver_Static( array(
			'Registered' => array( 'view', 'edit' ),
		) );

		$check = new Perms_Check_Alternate( 'admin' );
		$check->setResolver( $replacement );
		$this->assertFalse( $check->check( $resolver, array(), 'view', array( 'Registered' ) ) );
	}
}
