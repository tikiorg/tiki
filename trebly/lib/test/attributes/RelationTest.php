<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/attributes/relationlib.php';

class RelationTest extends TikiTestCase
{
	function setUp() {
		parent::setUp();
		TikiDb::get()->query( 'DELETE FROM `tiki_object_relations` WHERE `relation` LIKE ?', array( 'tiki.test%' ) );
	}

	function tearDown() {
		parent::tearDown();
		TikiDb::get()->query( 'DELETE FROM `tiki_object_relations` WHERE `relation` LIKE ?', array( 'tiki.test%' ) );
	}

	function testNoRelations() {
		$lib = new RelationLib;

		$this->assertEquals( array(), $lib->get_relations_from( 'test wiki page', 'HomePage' ) );
	}

	function testAddRelation() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );

		$this->assertEquals(
			array( array( 'relation' => 'tiki.test.link', 'type' => 'test wiki page', 'itemId' => 'SomePage' ) ),
			$this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage' ) ) );
	}

	function testDuplicateRelation() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );

		$this->assertEquals(
			array( array( 'relation' => 'tiki.test.link', 'type' => 'test wiki page', 'itemId' => 'SomePage' ) ),
			$this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage' ) ) );
	}

	function testMultipleResults() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test tracker item', '23' );
		$lib->add_relation( 'tiki.test.something', 'test wiki page', 'HomePage', 'test tracker item', '23' );
		$lib->add_relation( 'tiki.test.link', 'test tracker item', '23', 'test wiki page', 'SomePage' );

		$result = $this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage' ) );

		$this->assertContains( array( 'relation' => 'tiki.test.link', 'type' => 'test wiki page', 'itemId' => 'SomePage' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.link', 'type' => 'test tracker item', 'itemId' => '23' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.something', 'type' => 'test tracker item', 'itemId' => '23' ), $result );
	}

	function testFilterByType() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test tracker item', '23' );
		$lib->add_relation( 'tiki.test.something', 'test wiki page', 'HomePage', 'test tracker item', '23' );
		$lib->add_relation( 'tiki.test.link', 'test tracker item', '23', 'test wiki page', 'SomePage' );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.something', 'type' => 'test tracker item', 'itemId' => '23' ),
		), $this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage', 'tiki.test.something' ) ) );
	}

	function testRelationNamesChecked() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.link', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'TIKI . test  . link  ', 'test wiki page', 'HomePage', 'test tracker item', '23' );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.link', 'type' => 'test tracker item', 'itemId' => '23' ),
		), $this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage' ) ) );
	}

	function testLoadGroupOfRelations() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.sem.related', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.sem.source', 'test wiki page', 'HomePage', 'test external', 'http://wikipedia.org' );
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test external', 'http://wikipedia.org' );

		$result = $this->removeId( $lib->get_relations_from( 'test wiki page', 'HomePage', 'tiki.test.sem.' ) );

		$this->assertContains( array( 'relation' => 'tiki.test.sem.related', 'type' => 'test wiki page', 'itemId' => 'SomePage' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.sem.source', 'type' => 'test external', 'itemId' => 'http://wikipedia.org' ), $result );
		$this->assertNotContains( array( 'relation' => 'tiki.test.link', 'type' => 'test external', 'itemId' => 'http://wikipedia.org' ), $result );
	}

	function testRevert() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.sem.related', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.sem.source', 'test wiki page', 'HomePage', 'test external', 'http://wikipedia.org' );
		$lib->add_relation( 'tiki.test.link', 'test wiki page', 'HomePage', 'test external', 'http://wikipedia.org' );

		$result = $this->removeId( $lib->get_relations_to( 'test external', 'http://wikipedia.org', 'tiki.test.sem.' ) );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.sem.source', 'type' => 'test wiki page', 'itemId' => 'HomePage' )
		), $result );
	}

	function testGetSingle() {
		$lib = new RelationLib;
		$id = $lib->add_relation( 'tiki.test.sem.related', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );

		$data = $lib->get_relation( $id );

		$this->assertEquals( 'tiki.test.sem.related', $data['relation'] );
	}

	function testRemoveSingle() {
		$lib = new RelationLib;
		$id = $lib->add_relation( 'tiki.test.sem.related', 'test wiki page', 'HomePage', 'test wiki page', 'SomePage' );

		$lib->remove_relation( $id );

		$this->assertFalse( $lib->get_relation( $id ) );
	}

	private function removeId( $data ) {
		foreach( $data as & $row ) {
			unset( $row['relationId'] );
		}

		return $data;
	}
}

