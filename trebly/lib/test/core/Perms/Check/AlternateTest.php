<?php

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
