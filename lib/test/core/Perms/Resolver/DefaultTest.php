<?php

/** 
 * @group unit
 * 
 */

class Perms_Resolver_DefaultTest extends TikiTestCase
{
	function testAsExpected() {
		$resolver = new Perms_Resolver_Default( true );
		$this->assertTrue( $resolver->check( 'view', array() ) );

		$resolver = new Perms_Resolver_Default( false );
		$this->assertFalse( $resolver->check( 'view', array() ) );
	}

	function testApplicableGroups() {
		$resolver = new Perms_Resolver_Default(true);
		$this->assertContains('Anonymous', $resolver->applicableGroups());
		$this->assertContains('Registered', $resolver->applicableGroups());
	}
}

