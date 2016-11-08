<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ObjectFactoryTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

/**
 * @group unit
 *
 */

class Perms_ResolverFactory_TrackerParentFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData = array();
	private $itemIds = array();

	function setUp()
	{
		$db = TikiDb::get();

		$result = $db->query('SELECT groupName, permName, objectType, objectId FROM users_objectpermissions');
		while ($row = $result->fetchRow()) {
			$this->tableData[] = $row;
		}

		$db->query('DELETE FROM users_objectpermissions');

		$db->query('INSERT INTO tiki_tracker_items (trackerId) values (12)');
		$this->itemIds[] = $db->lastInsertId();
		$db->query('INSERT INTO tiki_tracker_items (trackerId) values (12)');
		$this->itemIds[] = $db->lastInsertId();
	}

	function tearDown()
	{
		$db = TikiDb::get();

		$db->query('DELETE FROM users_objectpermissions');

		foreach ($this->tableData as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$db->query('DELETE FROM tiki_tracker_items WHERE itemId in (?, ?)', $this->itemIds);
		$this->itemIds = array();
	}

	function testHash()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;

		$this->assertEquals('object:trackeritem:123', $factory->getHash(array('type' => 'trackeritem', 'object' => '123')));
	}

	function testHashMissingType()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$this->assertEquals('', $factory->getHash(array('object' => '123')));
	}

	function testHashWrongType()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$this->assertEquals('', $factory->getHash(array('type' => 'wiki page', 'object' => '123')));
	}

	function testHashMissingObject()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$this->assertEquals('', $factory->getHash(array('type' => 'trackeritem')));
	}

	function testObtainPermissions()
	{
		$data = array(
			array('Anonymous', 'tiki_p_view', 'tracker', md5('tracker12')),
			array('Anonymous', 'tiki_p_modify_tracker_items', 'tracker', md5('tracker12')),
			array('Admins', 'tiki_p_admin', 'tracker', md5('tracker12')),
		);

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_TrackerParentFactory;

		$expect = new Perms_Resolver_Static(
			array(
				'Admins' => array('admin'),
				'Anonymous' => array('modify_tracker_items', 'view'),
			),
			'object'
		);

		$this->assertEquals($expect, $factory->getResolver(array('type' => 'trackeritem', 'object' => $this->itemIds[0])));
	}

	function testObtainPermissionsWhenNoneSpecific()
	{
		$data = array(
			array('Anonymous', 'tiki_p_view', 'tracker', md5('tracker12')),
			array('Anonymous', 'tiki_p_modify_tracker_items', 'tracker', md5('tracker12')),
			array('Admins', 'tiki_p_admin', 'tracker', md5('tracker12')),
		);

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_TrackerParentFactory;

		$this->assertNull($factory->getResolver(array('type' => 'trackeritem', 'object' => $this->itemIds[1]+1)));
	}

	function testObtainResolverIncompleteContext()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;

		$this->assertNull($factory->getResolver(array('type' => 'trackeritem')));
		$this->assertNull($factory->getResolver(array('object' => $this->itemIds[0])));
	}

	function testBulkLoading()
	{
		$data = array(
			array('Anonymous', 'tiki_p_view', 'tracker', md5('tracker12')),
			array('Anonymous', 'tiki_p_modify_tracker_items', 'tracker', md5('tracker12')),
			array('Admins', 'tiki_p_admin', 'tracker', md5('tracker12')),
		);

		$db = TikiDb::get();
		foreach ($data as $row) {
			$db->query('INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values($row));
		}

		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$out = $factory->bulk(array('type' => 'trackeritem'), 'object', array($this->itemIds[0], $this->itemIds[1], $this->itemIds[1]+1));

		$this->assertEquals(array($this->itemIds[1]+1), $out);
	}

	function testBulkLoadingWithoutObject()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$out = $factory->bulk(array('type' => 'trackeritem'), 'objectId', $this->itemIds);

		$this->assertEquals($this->itemIds, $out);
	}

	function testBulkLoadingWithoutType()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$out = $factory->bulk(array(), 'object', $this->itemIds);

		$this->assertEquals($this->itemIds, $out);
	}

	function testBulkLoadingWithWrongType()
	{
		$factory = new Perms_ResolverFactory_TrackerParentFactory;
		$out = $factory->bulk(array('type' => 'wiki page'), 'object', $this->itemIds);

		$this->assertEquals($this->itemIds, $out);
	}
}
