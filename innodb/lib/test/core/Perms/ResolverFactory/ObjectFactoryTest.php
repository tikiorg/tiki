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

class Perms_ResolverFactory_ObjectFactoryTest extends PHPUnit_Framework_TestCase
{
	private $tableData = array();

	function setUp() {
		$db = TikiDb::get();

		$result = $db->query( 'SELECT groupName, permName, objectType, objectId FROM users_objectpermissions' );
		while( $row = $result->fetchRow() ) {
			$this->tableData[] = $row;
		}

		$db->query( 'DELETE FROM users_objectpermissions' );
	}

	function tearDown() {
		$db = TikiDb::get();

		$db->query( 'DELETE FROM users_objectpermissions' );
		
		foreach( $this->tableData as $row ) {
			$db->query( 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values( $row ) );
		}
	}

	function testHash() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		
		$this->assertEquals( 'object:wiki page:homepage', $factory->getHash( array( 'type' => 'wiki page', 'object' => 'HomePage' ) ) );
	}

	function testHashMissingType() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$this->assertEquals( '', $factory->getHash( array( 'object' => 'HomePage' ) ) );
	}

	function testHashMissingObject() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$this->assertEquals( '', $factory->getHash( array( 'type' => 'wiki page' ) ) );
	}

	function testObtainPermissions() {
		$data = array(
			array( 'Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist') ),
			array( 'Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage') ),
		);

		$db = TikiDb::get();
		foreach( $data as $row ) {
			$db->query( 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values( $row ) );
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;
		
		$expect = new Perms_Resolver_Static( array(
			'Admins' => array( 'admin' ),
			'Anonymous' => array( 'edit', 'view' ),
		) , 'object' );

		$this->assertEquals( $expect, $factory->getResolver( array( 'type' => 'wiki page', 'object' => 'HomePage' ) ) );
	}

	function testObtainPermissionsWhenNoneSpecific() {
		$data = array(
			array( 'Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist') ),
			array( 'Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage') ),
		);

		$db = TikiDb::get();
		foreach( $data as $row ) {
			$db->query( 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values( $row ) );
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;
		
		$this->assertNull( $factory->getResolver( array( 'type' => 'blog', 'object' => '234' ) ) );
	}

	function testObtainResolverIncompleteContext() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		
		$this->assertNull( $factory->getResolver( array( 'type' => 'wiki page' ) ) );
		$this->assertNull( $factory->getResolver( array( 'object' => 'HomePage' ) ) );
	}

	function testBulkLoading() {
		$data = array(
			array( 'Anonymous', 'tiki_p_view', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_edit', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'blog', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserlist') ),
			array( 'Admins', 'tiki_p_admin', 'wiki page', md5('wiki pagehomepage') ),
			array( 'Anonymous', 'tiki_p_admin', 'wiki page', md5('wiki pageuserpagefoobar') ),
		);

		$db = TikiDb::get();
		foreach( $data as $row ) {
			$db->query( 'INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) VALUES(?,?,?,?)', array_values( $row ) );
		}

		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk( array( 'type' => 'wiki page' ), 'object', array('HomePage', 'UserPageFoobar', 'HelloWorld') );
		
		$this->assertEquals( array( 'HelloWorld' ), $out );
	}

	function testBulkLoadingWithoutObject() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk( array( 'type' => 'wiki page' ), 'objectId', array('HomePage', 'UserPageFoobar', 'HelloWorld') );
		
		$this->assertEquals( array('HomePage', 'UserPageFoobar', 'HelloWorld'), $out );
	}

	function testBulkLoadingWithoutType() {
		$factory = new Perms_ResolverFactory_ObjectFactory;
		$out = $factory->bulk( array(), 'object', array('HomePage', 'UserPageFoobar', 'HelloWorld') );
		
		$this->assertEquals( array('HomePage', 'UserPageFoobar', 'HelloWorld'), $out );
	}
}
