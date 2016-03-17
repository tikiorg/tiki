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

class Perms_ResolverFactory_GlobalFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData = array();

	function setUp()
	{
		$db = TikiDb::get();

		$result = $db->query('SELECT groupName, permName FROM users_grouppermissions');
		while ($row = $result->fetchRow()) {
			$this->tableData[] = $row;
		}

		$db->query('DELETE FROM users_grouppermissions');
	}

	function tearDown()
	{
		$db = TikiDb::get();

		$db->query('DELETE FROM users_grouppermissions');

		foreach ($this->tableData as $row) {
			$db->query('INSERT INTO users_grouppermissions (groupName, permName) VALUES(?,?)', array_values($row));
		}
	}

	function testHashIsConstant()
	{
		$factory = new Perms_ResolverFactory_GlobalFactory;

		$this->assertEquals('global', $factory->getHash(array()));
		$this->assertEquals('global', $factory->getHash(array('type' => 'wiki page', 'object' => 'HomePage')));
	}

	function testObtainGlobalPermissions()
	{
		$db = TikiDb::get();
		$query = 'INSERT INTO users_grouppermissions (groupName, permName) VALUES(?,?)';
		$db->query($query, array('Anonymous', 'tiki_p_view'));
		$db->query($query, array('Anonymous', 'tiki_p_edit'));
		$db->query($query, array('Registered', 'tiki_p_remove'));
		$db->query($query, array('Admins', 'tiki_p_admin'));

		$expect = new Perms_Resolver_Static(
			array(
				'Anonymous' => array('view', 'edit'),
				'Registered' => array('remove'),
				'Admins' => array('admin'),
			)
		);

		$factory = new Perms_ResolverFactory_GlobalFactory;
		$this->assertEquals($expect, $factory->getResolver(array()));
	}
}
