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

class Perms_ResolverFactory_CategoryFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData;

	private function backupTable($name)
	{
		$this->tableData[$name] = [];

		$db = TikiDb::get();

		$result = $db->query('SELECT * FROM ' . $name);
		while ($row = $result->fetchRow()) {
			$this->tableData[$name][] = $row;
		}

		$db->query('DELETE FROM ' . $name);
	}

	private function restoreTable($name)
	{
		$db = TikiDb::get();

		$db->query('DELETE FROM ' . $name);

		foreach ($this->tableData[$name] as $row) {
			$db->query('INSERT INTO ' . $name . ' VALUES(?' . str_repeat(',?', count($row) - 1) . ')', array_values($row));
		}
	}

	function setUp()
	{
		$this->backupTable('users_objectpermissions');
		$this->backupTable('tiki_objects');
		$this->backupTable('tiki_category_objects');
		$this->backupTable('tiki_tracker_items');
	}

	function tearDown()
	{
		$this->restoreTable('users_objectpermissions');
		$this->restoreTable('tiki_objects');
		$this->restoreTable('tiki_category_objects');
		$this->restoreTable('tiki_tracker_items');
	}

	function testHash()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$itemQuery = 'INSERT INTO tiki_tracker_items (itemId, trackerId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'blog', 4]);
		$db->query($objectQuery, [4, 'tracker', 1]);
		$db->query($objectQuery, [5, 'trackeritem', 12]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);
		$db->query($categQuery, [4, 1]);
		$db->query($categQuery, [5, 2]);

		$db->query($itemQuery, [12, 1]);

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertEquals('category:1:3:4', $factory->getHash(['type' => 'wiki page', 'object' => 'HomePage']));
		$this->assertEquals('category:2:3', $factory->getHash(['type' => 'wiki page', 'object' => 'Contact']));
		$this->assertEquals('', $factory->getHash(['type' => 'wiki page', 'object' => 'Hello World']));
		$this->assertEquals('category:2', $factory->getHash(['type' => 'trackeritem', 'object' => 12]));

		$factory = new Perms_ResolverFactory_CategoryFactory('parent');
		$this->assertEquals('category:1', $factory->getHash(['type' => 'trackeritem', 'object' => 12]));
	}

	function testHashMissingType()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'blog', 4]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$this->assertEquals('', $factory->getHash(['object' => 'HomePage']));
	}

	function testHashMissingObject()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'blog', 4]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$this->assertEquals('', $factory->getHash(['type' => 'wiki page']));
	}

	function testObtainPermissions()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'blog', 4]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$db->query($permQuery, ['Registered', 'tiki_p_view', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 1]);
		$db->query($permQuery, ['Anonymous', 'tiki_p_admin', 4]);
		$db->query($permQuery, ['Hello', 'tiki_p_view', 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$expect = new Perms_Resolver_Static(
			[
				'Anonymous' => ['admin'],
				'Registered' => ['edit', 'view'],
			],
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'wiki page', 'object' => 'HomePage']));

		$expect = new Perms_Resolver_Static(
			['Hello' => ['view'],],
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'blog', 'object' => 4]));
	}

	function testObtainParentPermissions()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';
		$itemQuery = 'INSERT INTO tiki_tracker_items (itemId, trackerId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'tracker', 1]);
		$db->query($objectQuery, [2, 'trackeritem', 12]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);

		$db->query($itemQuery, [12, 1]);

		$db->query($permQuery, ['Registered', 'tiki_p_tracker_view', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_tracker_edit', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_tracker_edit', 1]);
		$db->query($permQuery, ['Anonymous', 'tiki_p_tracker_admin', 4]);
		$db->query($permQuery, ['Hello', 'tiki_p_tracker_view', 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$expect = new Perms_Resolver_Static(
			[
				'Registered' => ['tracker_edit', 'tracker_view'],
				'Hello' => ['tracker_view'],
			],
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'trackeritem', 'object' => 12]));

		$factory = new Perms_ResolverFactory_CategoryFactory('parent');

		$expect = new Perms_Resolver_Static(
			[
				'Anonymous' => ['tracker_admin'],
				'Registered' => ['tracker_edit', 'tracker_view'],
			],
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(['type' => 'trackeritem', 'object' => 12]));
	}

	function testGetResolverWithoutCategories()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);

		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$db->query($permQuery, ['Registered', 'tiki_p_view', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 1]);
		$db->query($permQuery, ['Anonymous', 'tiki_p_admin', 4]);
		$db->query($permQuery, ['Hello', 'tiki_p_view', 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(['type' => 'wiki page', 'object' => 'HomePage']));
	}

	function testObtainPermissionsWhenNoneSpecific()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'blog', 4]);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$db->query($permQuery, ['Registered', 'tiki_p_edit', 1]);
		$db->query($permQuery, ['Anonymous', 'tiki_p_admin', 4]);

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(['type' => 'wiki page', 'object' => 'Contact']));
	}

	function testObtainResolverIncompleteContext()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(['type' => 'wiki page']));
		$this->assertNull($factory->getResolver(['object' => 'HomePage']));
	}

	function testBulkLoading()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, [1, 'wiki page', 'HomePage']);
		$db->query($objectQuery, [2, 'wiki page', 'Contact']);
		$db->query($objectQuery, [3, 'wiki page', 'Hello World']);

		$db->query($categQuery, [1, 1]);
		$db->query($categQuery, [1, 4]);
		$db->query($categQuery, [1, 3]);
		$db->query($categQuery, [2, 3]);
		$db->query($categQuery, [2, 2]);
		$db->query($categQuery, [3, 2]);

		$db->query($permQuery, ['Registered', 'tiki_p_view', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 3]);
		$db->query($permQuery, ['Registered', 'tiki_p_edit', 1]);
		$db->query($permQuery, ['Anonymous', 'tiki_p_admin', 4]);
		$db->query($permQuery, ['Hello', 'tiki_p_view', 2]);

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk(['type' => 'wiki page'], 'object', ['HomePage', 'UserPageFoobar', 'Hello World']);

		$this->assertEquals(['UserPageFoobar'], $out);
	}

	function testBulkLoadingWithoutObject()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk(['type' => 'wiki page'], 'objectId', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}

	function testBulkLoadingWithoutType()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk([], 'object', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}

	function testBulkLoadingParentWithWrongType()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory('parent');
		$out = $factory->bulk(['type' => 'wiki page'], 'object', ['HomePage', 'UserPageFoobar', 'HelloWorld']);

		$this->assertEquals(['HomePage', 'UserPageFoobar', 'HelloWorld'], $out);
	}
}
