<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
class Rating_AggregationTest extends TikiTestCase
{
	protected $ratingDefaultOptions;
	protected $ratingAllowMultipleVotes;

	function setUp()
	{
		global $user, $testhelpers, $prefs;

		$user = null;

		$tikilib = $this->createMock('TikiLib');
		$tikilib->expects($this->any())->method('get_ip_address')->will($this->returnValue('127.0.0.1'));

		$testableTikiLib = new TestableTikiLib;
		$testableTikiLib->overrideLibs(array('tiki' => $tikilib));

		parent::setUp();
		TikiDb::get()->query('DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array('test.%'));

		$testhelpers = new TestHelpers();

		$ratinglib = TikiLib::lib('rating');

		$this->ratingDefaultOptions = $prefs['rating_default_options'];
		$prefs['rating_default_options'] = '0,1,2,3,4,5';
		$this->ratingAllowMultipleVotes = $prefs['rating_allow_multi_votes'];
		$prefs['rating_allow_multi_votes'] = 'y';
	}

	function tearDown()
	{
		global $testhelpers, $user, $prefs; $user = null;
		parent::tearDown();
		TikiDb::get()->query('DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array('test.%'));

        $testhelpers->reset_all();
		$prefs['rating_default_options'] = $this->ratingDefaultOptions;
		$prefs['rating_allow_multi_votes'] = $this->ratingAllowMultipleVotes;
	}

	function testGetGlobalSum()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 4, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(9.0, $lib->collect('test', 111, 'sum'));
	}

	function testGetGlobalSumSingleVote()
	{
		global $prefs;
		$prefs['rating_allow_multi_votes'] = '';

		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 4, time() - 3000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(5.0, $lib->collect('test', 111, 'sum'));
	}

	function testSumWithNoData()
	{
		$lib = new RatingLib;

		$this->assertEquals(0.0, $lib->collect('test', 111, 'sum'));
	}

	function testAverageWithNoData()
	{
		$lib = new RatingLib;

		$this->assertEquals(0.0, $lib->collect('test', 111, 'avg'));
	}

	function testGetGlobalAverage()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(10 / 3, $lib->collect('test', 111, 'avg'), '', 1/1000);
	}

	function testGetGlobalAverageSingleVote()
	{
		global $prefs;
		$prefs['rating_allow_multi_votes'] = '';

		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(5 / 2, $lib->collect('test', 111, 'avg'), '', 1/1000);
	}

	function testBadAggregateFunction()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertFalse($lib->collect('test', 111, 'foobar'));
	}

	function testTimeRangeLimiter()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 112, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(5.0, $lib->collect('test', 111, 'sum', array('range' => 2500)));
	}

	function testIgnoreAnonymous()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(10.0, $lib->collect('test', 111, 'sum', array('ignore' => 'anonymous')));
	}

	function testIgnoreAnonymousSingleVote()
	{
		global $prefs;
		$prefs['rating_allow_multi_votes'] = '';

		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(3.0, $lib->collect('test', 111, 'sum', array('ignore' => 'anonymous')));
	}

	function testKeepLatest()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1500);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(6.0, $lib->collect('test', 111, 'sum', array('keep' => 'latest')));

		$this->assertEquals(3.0, $lib->collect('test', 111, 'sum', array('keep' => 'latest', 'range' => 1200)));

		$this->assertEquals(0.0, $lib->collect('test', 111, 'sum', array('keep' => 'latest', 'range' => 1200,	'ignore' => 'anonymous')));
	}

	function testKeepOldest()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000);
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000);
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(8.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest')));

		$this->assertEquals(5.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'range' => 2500)));

		$this->assertEquals(2.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'range' => 2500,	'ignore' => 'anonymous')));
	}

	function testKeepOldestSingleVote()
	{
		global $prefs;
		$prefs['rating_allow_multi_votes'] = '';

		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000);

		$this->assertEquals(6.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest')));

		$this->assertEquals(6.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'range' => 2500)));

		$this->assertEquals(3.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'range' => 2500,	'ignore' => 'anonymous')));
	}

	function testConsiderPerPeriod()
	{
		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000); // kept
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000); // kept
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000);
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000); // kept

		$this->assertEquals(10.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'revote' => 2500)));

		$this->assertEquals(
			10 / 3,
			$lib->collect('test', 111, 'avg', array('keep' => 'oldest', 'revote' => 2500)),
			'',
			1 / 1000
		);
	}

	function testConsiderPerPeriodSingleVote()
	{
		global $prefs;
		$prefs['rating_allow_multi_votes'] = '';

		$lib = new RatingLib;
		$lib->record_user_vote('abc', 'test', 111, 5, time() - 3000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 2, time() - 2000); // overridden
		$lib->record_user_vote('abc', 'test', 111, 3, time() - 1000); //kept
		$lib->record_anonymous_vote('deadbeef01234567', 'test', 111, 3, time() - 1000); // kept

		$this->assertEquals(6.0, $lib->collect('test', 111, 'sum', array('keep' => 'oldest', 'revote' => 2500)));

		$this->assertEquals(
			6 / 2,
			$lib->collect('test', 111, 'avg', array('keep' => 'oldest', 'revote' => 2500)),
			'',
			1 / 1000
		);
	}
}

