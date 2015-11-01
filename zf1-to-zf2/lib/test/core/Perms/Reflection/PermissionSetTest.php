<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class Perms_Reflection_PermissionSetTest extends TikiTestCase
{
	function testEmptySet()
	{
		$set = new Perms_Reflection_PermissionSet;

		$this->assertEquals(array(), $set->getPermissionArray());
	}

	function testBasicSet()
	{
		$set = new Perms_Reflection_PermissionSet;
		$set->add('Registered', 'view');
		$set->add('Registered', 'edit');
		$set->add('Anonymous', 'view');

		$this->assertEquals(
			array(
				'Registered' => array('view', 'edit'),
				'Anonymous' => array('view'),
			),
			$set->getPermissionArray()
		);
	}

	function testDuplicateEntry()
	{
		$set = new Perms_Reflection_PermissionSet;
		$set->add('Registered', 'view');
		$set->add('Registered', 'edit');
		$set->add('Registered', 'view');

		$this->assertEquals(
			array('Registered' => array('view', 'edit'),),
			$set->getPermissionArray()
		);
	}

	function testPositiveHas()
	{
		$set = new Perms_Reflection_PermissionSet;
		$set->add('Anonymous', 'view');

		$this->assertTrue($set->has('Anonymous', 'view'));
	}

	function testNegativeHas()
	{
		$set = new Perms_Reflection_PermissionSet;

		$this->assertFalse($set->has('Anonymous', 'view'));
	}

	function testAddMultiple()
	{
		$equivalent = new Perms_Reflection_PermissionSet;
		$equivalent->add('Anonymous', 'a');
		$equivalent->add('Anonymous', 'b');
		$equivalent->add('Anonymous', 'c');

		$multi = new Perms_Reflection_PermissionSet;
		$multi->add('Anonymous', array('a', 'b', 'c'));

		$this->assertEquals($equivalent, $multi);
	}
}
