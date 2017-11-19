<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class Perms_Reflection_QuickTest extends TikiTestCase
{
	function testUnconfigured()
	{
		$quick = new Perms_Reflection_Quick;

		$obtained = $quick->getPermissions(
			new Perms_Reflection_PermissionSet,
			[
				'Anonymous' => 'basic',
				'Registered' => 'editor',
			]
		);
		$this->assertEquals(new Perms_Reflection_PermissionSet, $obtained);
	}

	function testSimpleConfigurations()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('tester', ['view', 'edit', 'comment']);
		$quick->configure('basic', ['view']);

		$obtained = $quick->getPermissions(
			new Perms_Reflection_PermissionSet,
			[
				'Anonymous' => 'basic',
				'Registered' => 'editor',
				'Tester' => 'tester',
			]
		);

		$expect = new Perms_Reflection_PermissionSet;
		$expect->add('Anonymous', 'view');
		$expect->add('Tester', 'view');
		$expect->add('Tester', 'edit');
		$expect->add('Tester', 'comment');

		$this->assertEquals($expect, $obtained);
	}

	function testInheritance()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('basic', ['view']);
		$quick->configure('registered', ['edit']);
		$quick->configure('editors', ['remove']);

		$obtained = $quick->getPermissions(
			new Perms_Reflection_PermissionSet,
			[
				'Anonymous' => 'basic',
				'Registered' => 'registered',
				'Editor' => 'editors',
			]
		);

		$expect = new Perms_Reflection_PermissionSet;
		$expect->add('Anonymous', 'view');
		$expect->add('Registered', 'view');
		$expect->add('Registered', 'edit');
		$expect->add('Editor', 'view');
		$expect->add('Editor', 'edit');
		$expect->add('Editor', 'remove');

		$this->assertEquals($expect, $obtained);
	}

	function testAssignNone()
	{
		$quick = new Perms_Reflection_Quick;
		$current = new Perms_Reflection_PermissionSet;
		$current->add('Anonymous', 'view');

		$obtained = $quick->getPermissions(
			$current,
			['Anonymous' => 'none',]
		);

		$expect = new Perms_Reflection_PermissionSet;

		$this->assertEquals($expect, $obtained);
	}

	function testOnbtainUserdefined()
	{
		$quick = new Perms_Reflection_Quick;
		$current = new Perms_Reflection_PermissionSet;
		$current->add('Anonymous', 'view');

		$obtained = $quick->getPermissions(
			$current,
			['Anonymous' => 'userdefined',]
		);

		$expect = new Perms_Reflection_PermissionSet;
		$expect->add('Anonymous', 'view');

		$this->assertEquals($expect, $obtained);
	}

	function testDefaultRetrieveGroups()
	{
		$quick = new Perms_Reflection_Quick;

		$permissions = new Perms_Reflection_PermissionSet;
		$permissions->add('Registered', 'view');

		$expect = [
			'Anonymous' => 'none',
			'Registered' => 'userdefined',
		];

		$obtained = $quick->getAppliedPermissions($permissions, ['Anonymous', 'Registered']);

		$this->assertEquals($expect, $obtained);
	}

	function testMatch()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('basic', ['view']);

		$permissions = new Perms_Reflection_PermissionSet;
		$permissions->add('Registered', 'view');

		$expect = ['Registered' => 'basic'];

		$obtained = $quick->getAppliedPermissions($permissions, ['Registered']);

		$this->assertEquals($expect, $obtained);
	}

	function testNoMatchOnExtra()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('basic', ['view']);

		$permissions = new Perms_Reflection_PermissionSet;
		$permissions->add('Registered', 'view');
		$permissions->add('Registered', 'edit');

		$expect = ['Registered' => 'userdefined'];

		$obtained = $quick->getAppliedPermissions($permissions, ['Registered']);

		$this->assertEquals($expect, $obtained);
	}

	function testNoMatchOnMissing()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('basic', ['view', 'edit']);

		$permissions = new Perms_Reflection_PermissionSet;
		$permissions->add('Registered', 'view');

		$expect = ['Registered' => 'userdefined'];

		$obtained = $quick->getAppliedPermissions($permissions, ['Registered']);

		$this->assertEquals($expect, $obtained);
	}

	function testInheritenceAppiesInMatching()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('basic', ['view']);
		$quick->configure('registered', ['edit']);

		$permissions = new Perms_Reflection_PermissionSet;
		$permissions->add('Registered', 'view');
		$permissions->add('Registered', 'edit');

		$expect = ['Registered' => 'registered'];

		$obtained = $quick->getAppliedPermissions($permissions, ['Registered']);

		$this->assertEquals($expect, $obtained);
	}

	function testRegisterNoneIsIgnored()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('none', ['view']);

		$expect = new Perms_Reflection_Quick;
		$this->assertEquals($expect, $quick);
	}

	function testRegisterUserDefinedIsIgnored()
	{
		$quick = new Perms_Reflection_Quick;
		$quick->configure('userdefined', ['view']);

		$expect = new Perms_Reflection_Quick;
		$this->assertEquals($expect, $quick);
	}
}
