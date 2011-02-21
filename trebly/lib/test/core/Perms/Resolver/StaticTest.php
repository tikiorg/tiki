<?php

/** 
 * @group unit
 * 
 */

class Perms_Resolver_StaticTest extends TikiTestCase
{
	function testGroupNotDefined() {
		$static = new Perms_Resolver_Static( array(
		) );

		$this->assertFalse( $static->check( 'view', array() ) );
		$this->assertEquals(array(), $static->applicableGroups());
	}

	function testNotRightGroup() {
		$static = new Perms_Resolver_Static( array(
			'Registered' => array( 'view', 'edit' ),
		) );

		$this->assertFalse( $static->check( 'view', array( 'Anonymous' ) ) );
		$this->assertEquals(array('Registered'), $static->applicableGroups());
	}

	function testRightGroup() {
		$static = new Perms_Resolver_Static( array(
			'Anonymous' => array( 'view' ),
			'Registered' => array( 'view', 'edit' ),
		) );

		$this->assertTrue( $static->check( 'edit', array( 'Anonymous', 'Registered' ) ) );
		$this->assertEquals(array('Anonymous', 'Registered'), $static->applicableGroups());
	}
}

