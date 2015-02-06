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

class Perms_ResolverFactory_CategoryFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData;

	private function backupTable($name)
	{
		$this->tableData[$name] = array();

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
			$db->query('INSERT INTO ' . $name . ' VALUES(?' . str_repeat(',?', count($row)-1) . ')', array_values($row));
		}
	}

	function setUp()
	{
		$this->backupTable('users_objectpermissions');
		$this->backupTable('tiki_objects');
		$this->backupTable('tiki_category_objects');
	}

	function tearDown()
	{
		$this->restoreTable('users_objectpermissions');
		$this->restoreTable('tiki_objects');
		$this->restoreTable('tiki_category_objects');
	}

	function testHash()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'blog', 4));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertEquals('category:1:3:4', $factory->getHash(array('type' => 'wiki page', 'object' => 'HomePage')));
		$this->assertEquals('category:2:3', $factory->getHash(array('type' => 'wiki page', 'object' => 'Contact')));
		$this->assertEquals('', $factory->getHash(array('type' => 'wiki page', 'object' => 'Hello World')));
	}

	function testHashMissingType()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'blog', 4));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$this->assertEquals('', $factory->getHash(array('object' => 'HomePage')));
	}

	function testHashMissingObject()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'blog', 4));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$this->assertEquals('', $factory->getHash(array('type' => 'wiki page')));
	}

	function testObtainPermissions()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'blog', 4));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$db->query($permQuery, array('Registered', 'tiki_p_view', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 1));
		$db->query($permQuery, array('Anonymous', 'tiki_p_admin', 4));
		$db->query($permQuery, array('Hello', 'tiki_p_view', 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$expect = new Perms_Resolver_Static(
			array(
				'Anonymous' => array('admin'),
				'Registered' => array('edit', 'view'),
			),
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(array('type' => 'wiki page', 'object' => 'HomePage')));

		$expect = new Perms_Resolver_Static(
			array('Hello' => array('view'),),
			'category'
		);

		$this->assertEquals($expect, $factory->getResolver(array('type' => 'blog', 'object' => 4)));
	}

	function testGetResolverWithoutCategories()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));

		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$db->query($permQuery, array('Registered', 'tiki_p_view', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 1));
		$db->query($permQuery, array('Anonymous', 'tiki_p_admin', 4));
		$db->query($permQuery, array('Hello', 'tiki_p_view', 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(array('type' => 'wiki page', 'object' => 'HomePage')));
	}

	function testObtainPermissionsWhenNoneSpecific()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'blog', 4));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$db->query($permQuery, array('Registered', 'tiki_p_edit', 1));
		$db->query($permQuery, array('Anonymous', 'tiki_p_admin', 4));

		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(array('type' => 'wiki page', 'object' => 'Contact')));
	}

	function testObtainResolverIncompleteContext()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;

		$this->assertNull($factory->getResolver(array('type' => 'wiki page')));
		$this->assertNull($factory->getResolver(array('object' => 'HomePage')));
	}

	function testBulkLoading()
	{
		$objectQuery = 'INSERT INTO tiki_objects (objectId, type, itemId) VALUES(?, ?, ?)';
		$categQuery = 'INSERT INTO tiki_category_objects (catObjectId, categId) VALUES(?, ?)';
		$permQuery = 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,\'category\',MD5(CONCAT("category",?)))';

		$db = TikiDb::get();
		$db->query($objectQuery, array(1, 'wiki page', 'HomePage'));
		$db->query($objectQuery, array(2, 'wiki page', 'Contact'));
		$db->query($objectQuery, array(3, 'wiki page', 'Hello World'));

		$db->query($categQuery, array(1, 1));
		$db->query($categQuery, array(1, 4));
		$db->query($categQuery, array(1, 3));
		$db->query($categQuery, array(2, 3));
		$db->query($categQuery, array(2, 2));
		$db->query($categQuery, array(3, 2));

		$db->query($permQuery, array('Registered', 'tiki_p_view', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 3));
		$db->query($permQuery, array('Registered', 'tiki_p_edit', 1));
		$db->query($permQuery, array('Anonymous', 'tiki_p_admin', 4));
		$db->query($permQuery, array('Hello', 'tiki_p_view', 2));

		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk(array('type' => 'wiki page'), 'object', array('HomePage', 'UserPageFoobar', 'Hello World'));

		$this->assertEquals(array('UserPageFoobar'), $out);
	}

	function testBulkLoadingWithoutObject()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk(array('type' => 'wiki page'), 'objectId', array('HomePage', 'UserPageFoobar', 'HelloWorld'));

		$this->assertEquals(array('HomePage', 'UserPageFoobar', 'HelloWorld'), $out);
	}

	function testBulkLoadingWithoutType()
	{
		$factory = new Perms_ResolverFactory_CategoryFactory;
		$out = $factory->bulk(array(), 'object', array('HomePage', 'UserPageFoobar', 'HelloWorld'));

		$this->assertEquals(array('HomePage', 'UserPageFoobar', 'HelloWorld'), $out);
	}
}
