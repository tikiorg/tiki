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

class Perms_AccessorTest extends TikiTestCase
{
	function testGetSetResolver()
	{
		$resolver = new Perms_Resolver_Default(true);

		$accessor = new Perms_Accessor;
		$accessor->setResolver($resolver);

		$this->assertSame($resolver, $accessor->getResolver());
	}

	function testGetSetGroups()
	{
		$accessor = new Perms_Accessor;
		$accessor->setGroups(['Test']);

		$this->assertEquals(['Test'], $accessor->getGroups());
	}

	function testGetSetPrefix()
	{
		$accessor = new Perms_Accessor;
		$accessor->setPrefix('hello_');

		$this->assertEquals('hello_', $accessor->getPrefix());
	}

	function testGetSetContext()
	{
		$accessor = new Perms_Accessor;
		$accessor->setContext(['type' => 'wiki page', 'object' => 'HomePage']);

		$this->assertEquals(['type' => 'wiki page', 'object' => 'HomePage'], $accessor->getContext());
	}

	function testGetDefaultGroups()
	{
		$accessor = new Perms_Accessor;

		$this->assertEquals([], $accessor->getGroups());
	}

	function testDefaultPrefix()
	{
		$accessor = new Perms_Accessor;

		$this->assertEquals('', $accessor->getPrefix());
	}

	function testCheckPermissionWithoutResolver()
	{
		$accessor = new Perms_Accessor;

		$this->assertFalse($accessor->view);
	}

	function testCheckPermissionWithResolver()
	{
		$accessor = new Perms_Accessor;

		$accessor->setResolver(
			new Perms_Resolver_Static(['Anonymous' => ['view', 'edit'],])
		);

		$this->assertFalse($accessor->view);
		$this->assertFalse($accessor->view_history);

		$accessor->setGroups(['Anonymous']);

		$this->assertTrue($accessor->view);
		$this->assertFalse($accessor->view_history);
	}

	function testReadWithPrefix()
	{
		$accessor = new Perms_Accessor;
		$accessor->setGroups(['Anonymous']);
		$accessor->setPrefix('tiki_p_');

		$accessor->setResolver(
			new Perms_Resolver_Static(['Anonymous' => ['view', 'edit'],])
		);

		$this->assertTrue($accessor->view);
		$this->assertTrue($accessor->tiki_p_view);
		$this->assertFalse($accessor->tiki_p_view_history);
	}

	function testGlobalize()
	{
		$accessor = new Perms_Accessor;
		$accessor->setPrefix('tiki_p_');
		$accessor->setGroups(['Anonymous']);

		$accessor->setResolver(
			new Perms_Resolver_Static(['Anonymous' => ['view', 'edit', 'comment'],])
		);

		$accessor->globalize(['view', 'edit', 'view_history', 'tiki_p_comment']);

		global $tiki_p_view, $tiki_p_view_history, $tiki_p_comment;
		$this->assertEquals('y', $tiki_p_view);
		$this->assertEquals('y', $tiki_p_comment);
		$this->assertEquals('n', $tiki_p_view_history);
	}

	function testArrayAccess()
	{
		$accessor = new Perms_Accessor;
		$accessor->setGroups(['Anonymous']);
		$accessor->setPrefix('tiki_p_');

		$accessor->setResolver(
			new Perms_Resolver_Static(
				['Anonymous' => ['view', 'edit'],]
			)
		);

		$this->assertTrue($accessor['view']);
		$this->assertTrue($accessor['tiki_p_view']);
		$this->assertFalse($accessor['tiki_p_view_history']);
	}

	function testApplicableGroupsInsideAccessor()
	{
		$accessor = new Perms_Accessor;
		$static = new Perms_Resolver_Static(
			[
				'Anonymous' => ['view'],
				'Registered' => ['view', 'edit'],
			]
		);

		$accessor->setResolver($static);

		$this->assertEquals(['Anonymous', 'Registered'], $accessor->applicableGroups());
	}

	function testApplicableGroupsThroughCheckSequence()
	{
		$accessor = new Perms_Accessor;
		$static = new Perms_Resolver_Static(
			[
				'Anonymous' => ['view'],
				'Registered' => ['view', 'edit'],
			]
		);

		$global = new Perms_Resolver_Static(
			['Admins' => ['admin'],]
		);

		$accessor->setResolver($static);
		$accessor->setCheckSequence(
			[
				$g = new Perms_Check_Alternate('admin'),
				new Perms_Check_Direct,
			]
		);

		$g->setResolver($global);

		$this->assertEquals(['Admins', 'Anonymous', 'Registered'], $accessor->applicableGroups());
	}
}
