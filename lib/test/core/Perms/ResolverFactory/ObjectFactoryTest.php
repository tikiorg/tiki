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

class Perms_ResolverFactory_ObjectFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData = [];

	function setUp()
	{
		$db = TikiDb::get();

		$result = $db->query('SELECT groupName, permName, objectType, objectId FROM users_objectpermissions');
		while ($row = $result->fetchRow()) {
			$this->tableData[] = $row;
		}

		$db->query('DELETE FROM users_objectpermissions');
		$db->query('DELETE FROM tiki_tracker_items');
	}

	function tearDown()
	{
		$db = TikiDb::get();

		$db->query('DELETE FROM users_objectpermissions');

		foreach ($this->tableData as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}
	}

	function testHash()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;

		$this->assertEquals('object:wiki page:homepage', $factory->getHash(['type' => 'wiki page', 'object' => 'HomePage']));
	}

	function testHashParent()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory('parent');

		$this->assertEquals('object:trackeritemparent:12', $factory->getHash(['type' => 'trackeritem', 'object' => '12']));
	}

	function testHashMissingType()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$this->assertEquals('', $factory->getHash(['object' => 'HomePage']));
	}

	function testHashMissingObject()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$this->assertEquals('', $factory->getHash(['type' => 'wiki page']));
	}

	function testObtainPermissions()
	{
		$data = [
			['Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist')],
			['Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage')],
		];

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;

		$expect = new Perms_Resolver_Static(
			[
				'Admins' => ['admin'],
				'Anonymous' => ['edit', 'view'],
			],
			'object'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'wiki page', 'object' => 'HomePage']));
	}

	function testObtainParentPermissions()
	{
		$data = [
			['Anonymous', 'tiki_p_tracker_view', 'tracker', md5('tracker1')],
			['Anonymous', 'tiki_p_modify_object_categories', 'tracker', md5('tracker1')],
			['Admins', 'tiki_p_tracker_admin', 'tracker', md5('tracker1')],
		];

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$db->query("INSERT INTO tiki_tracker_items (itemId, trackerId) VALUES(2,1), (3,1)");

		$factory = new Perms_ResolverFactory_ObjectFactory('parent');

		$expect = new Perms_Resolver_Static(
			[
				'Admins' => ['tracker_admin'],
				'Anonymous' => ['modify_object_categories', 'tracker_view'],
			],
			'object'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'trackeritem', 'object' => 2]));
	}

	function testObtainPermissionsWhenNoneSpecific()
	{
		$data = [
			['Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist')],
			['Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage')],
		];

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;

		$this->assertNull($factory->getResolver(['type' => 'blog', 'object' => '234']));
	}

	function testObtainParentPermissionsWhenNoneSpecific()
	{
		$data = [
			['Anonymous', 'tiki_p_tracker_view', 'tracker', md5('tracker1')],
			['Anonymous', 'tiki_p_modify_object_categories', 'tracker', md5('tracker1')],
			['Admins', 'tiki_p_tracker_admin', 'tracker', md5('tracker1')],
		];

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$db->query("INSERT INTO tiki_tracker_items (itemId, trackerId) VALUES(2,5)");

		$factory = new Perms_ResolverFactory_ObjectFactory('parent');

		$this->assertNull($factory->getResolver(['type' => 'trackeritem', 'object' => 2]));
	}

	function testObtainResolverIncompleteContext()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;

		$this->assertNull($factory->getResolver(['type' => 'wiki page']));
		$this->assertNull($factory->getResolver(['object' => 'HomePage']));
	}

	function testBulkLoading()
	{
		$data = [
			['Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist')],
			['Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage')],
			['Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserpagefoobar')],
		];

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk(['type' => 'wiki page'], 'object', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HelloWorld'], $out);
	}

	function testBulkLoadingWithoutObject()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk(['type' => 'wiki page'], 'objectId', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}

	function testBulkLoadingWithoutType()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk([], 'object', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}

	function testBulkLoadingParentWithWrongType()
	{
		$factory = new Perms_ResolverFactory_ObjectFactory('parent');
		$out = $factory->bulk(['type' => 'wiki page'], 'object', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}
}
