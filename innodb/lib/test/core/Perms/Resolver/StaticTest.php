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

