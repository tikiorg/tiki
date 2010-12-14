<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/rating/ratinglib.php';

class Rating_AggregationTest extends TikiTestCase
{
	function setUp() {
		global $user; $user = null;
		global $tikilib;
		$tikilib = $this->getMock('TikiLib', array('get_ip_address'));
		$tikilib->expects($this->any())->method('get_ip_address')->will($this->returnValue('127.0.0.1'));
		parent::setUp();
		TikiDb::get()->query( 'DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array( 'test.%' ) );
	}
	
	function tearDown() {
		global $user; $user = null;
		parent::tearDown();
		TikiDb::get()->query( 'DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array( 'test.%' ) );
	}

	function testGetGlobalSum() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 4, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 112, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 9.0, $lib->collect( 'test', 111, 'sum' ) );
	}

	function testSumWithNoData() {
		$lib = new RatingLib;

		$this->assertEquals( 0.0, $lib->collect( 'test', 111, 'sum' ) );
	}

	function testAverageWithNoData() {
		$lib = new RatingLib;

		$this->assertEquals( 0.0, $lib->collect( 'test', 111, 'avg' ) );
	}

	function testGetGlobalAverage() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 112, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 10 / 3, $lib->collect( 'test', 111, 'avg' ), '', 1/1000 );
	}

	function testBadAggregateFunction() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 112, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertFalse( $lib->collect( 'test', 111, 'foobar' ) );
	}

	function testTimeRangeLimiter() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 112, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 5.0, $lib->collect( 'test', 111, 'sum', array(
			'range' => 2500,
		) ) );
	}

	function testIgnoreAnonymous() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 111, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 10.0, $lib->collect( 'test', 111, 'sum', array(
			'ignore' => 'anonymous',
		) ) );
	}

	function testKeepLatest() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 111, 3, time() - 1500 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 6.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'latest',
		) ) );

		$this->assertEquals( 3.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'latest',
			'range' => 1200,
		) ) );

		$this->assertEquals( 0.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'latest',
			'range' => 1200,
			'ignore' => 'anonymous',
		) ) );
	}

	function testKeepOldest() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 );
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 );
		$lib->record_user_vote( 'abc', 'test', 111, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 );

		$this->assertEquals( 8.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'oldest',
		) ) );

		$this->assertEquals( 5.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'oldest',
			'range' => 2500,
		) ) );

		$this->assertEquals( 2.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'oldest',
			'range' => 2500,
			'ignore' => 'anonymous',
		) ) );
	}

	function testConsiderPerPeriod() {
		$lib = new RatingLib;
		$lib->record_user_vote( 'abc', 'test', 111, 5, time() - 3000 ); // kept
		$lib->record_user_vote( 'abc', 'test', 111, 2, time() - 2000 ); // kept
		$lib->record_user_vote( 'abc', 'test', 111, 3, time() - 1000 );
		$lib->record_anonymous_vote( 'deadbeef01234567', 'test', 111, 3, time() - 1000 ); // kept

		$this->assertEquals( 10.0, $lib->collect( 'test', 111, 'sum', array(
			'keep' => 'oldest',
			'revote' => 2500,
		) ) );

		$this->assertEquals( 10 / 3, $lib->collect( 'test', 111, 'avg', array(
			'keep' => 'oldest',
			'revote' => 2500,
		) ), '', 1 / 1000 );
	}
}

