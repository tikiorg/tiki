<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: AttributeTest.php 25339 2010-02-18 08:28:32Z changi67 $

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

		$this->assertEquals( array(), $lib->get_relations_from( 'wiki page', 'HomePage' ) );
	}

	function testAddRelation() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );

		$this->assertEquals(
			array( array( 'relation' => 'tiki.test.link', 'type' => 'wiki page', 'itemId' => 'SomePage' ) ),
			$this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage' ) ) );
	}

	function testDuplicateRelation() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );

		$this->assertEquals(
			array( array( 'relation' => 'tiki.test.link', 'type' => 'wiki page', 'itemId' => 'SomePage' ) ),
			$this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage' ) ) );
	}

	function testMultipleResults() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'tracker item', '23' );
		$lib->add_relation( 'tiki.test.something', 'wiki page', 'HomePage', 'tracker item', '23' );
		$lib->add_relation( 'tiki.test.link', 'tracker item', '23', 'wiki page', 'SomePage' );

		$result = $this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage' ) );

		$this->assertContains( array( 'relation' => 'tiki.test.link', 'type' => 'wiki page', 'itemId' => 'SomePage' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.link', 'type' => 'tracker item', 'itemId' => '23' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.something', 'type' => 'tracker item', 'itemId' => '23' ), $result );
	}

	function testFilterByType() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'tracker item', '23' );
		$lib->add_relation( 'tiki.test.something', 'wiki page', 'HomePage', 'tracker item', '23' );
		$lib->add_relation( 'tiki.test.link', 'tracker item', '23', 'wiki page', 'SomePage' );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.something', 'type' => 'tracker item', 'itemId' => '23' ),
		), $this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage', 'tiki.test.something' ) ) );
	}

	function testRelationNamesChecked() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.link', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'TIKI . test  . link  ', 'wiki page', 'HomePage', 'tracker item', '23' );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.link', 'type' => 'tracker item', 'itemId' => '23' ),
		), $this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage' ) ) );
	}

	function testLoadGroupOfRelations() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.sem.related', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.sem.source', 'wiki page', 'HomePage', 'external', 'http://wikipedia.org' );
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'external', 'http://wikipedia.org' );

		$result = $this->removeId( $lib->get_relations_from( 'wiki page', 'HomePage', 'tiki.test.sem.' ) );

		$this->assertContains( array( 'relation' => 'tiki.test.sem.related', 'type' => 'wiki page', 'itemId' => 'SomePage' ), $result );
		$this->assertContains( array( 'relation' => 'tiki.test.sem.source', 'type' => 'external', 'itemId' => 'http://wikipedia.org' ), $result );
		$this->assertNotContains( array( 'relation' => 'tiki.test.link', 'type' => 'external', 'itemId' => 'http://wikipedia.org' ), $result );
	}

	function testRevert() {
		$lib = new RelationLib;
		$lib->add_relation( 'tiki.test.sem.related', 'wiki page', 'HomePage', 'wiki page', 'SomePage' );
		$lib->add_relation( 'tiki.test.sem.source', 'wiki page', 'HomePage', 'external', 'http://wikipedia.org' );
		$lib->add_relation( 'tiki.test.link', 'wiki page', 'HomePage', 'external', 'http://wikipedia.org' );

		$result = $this->removeId( $lib->get_relations_to( 'external', 'http://wikipedia.org', 'tiki.test.sem.' ) );

		$this->assertEquals( array(
			array( 'relation' => 'tiki.test.sem.source', 'type' => 'wiki page', 'itemId' => 'HomePage' )
		), $result );
	}

	private function removeId( $data ) {
		foreach( $data as & $row ) {
			unset( $row['relationId'] );
		}

		return $data;
	}
}

