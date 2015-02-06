<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$ratinglib = TikiLib::lib('rating');

class Rating_RegisterVoteTest extends TikiTestCase
{
	function setUp()
	{
		global $user; $user = null;
		parent::setUp();
		TikiDb::get()->query('DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array('test.%'));
	}

	function tearDown()
	{
		global $user; $user = null;
		parent::tearDown();
		TikiDb::get()->query('DELETE FROM `tiki_user_votings` WHERE `id` LIKE ?', array('test.%'));
	}

	function tokenFormats()
	{
		return array(
			'unknown' => array('foobar', 233, null),
			'comment' => array('comment', 123, 'comment123'),
			'article' => array('article', 123, 'article123'),
			'wiki' => array('wiki page', 123, 'wiki123'),
			'wikiAsString' => array('wiki page', '123', 'wiki123'),
			'wikiPageName' => array('wiki page', 'HomePage', null),
			'test' => array('test', 111, 'test.111'),
		);
	}

	/**
	 * @dataProvider tokenFormats
	 */
	function testGetToken($type, $object, $token)
	{
		$lib = new RatingLib;

		$this->assertEquals($token, $lib->get_token($type, $object));
	}

	function testRecordUserVotes()
	{
		$lib = new RatingLib;
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 3));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 2));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 112, 4));
		$this->assertTrue($lib->record_user_vote('def', 'test', 111, 1));
		$this->assertTrue($lib->record_user_vote('def', 'test', 113, 1));
		$this->assertTrue($lib->record_user_vote('def', 'test', 112, 5));

		$this->assertEquals(
			array(
				array('user' => 'abc', 'id' => 'test.111', 'optionId' => 2),
				array('user' => 'abc', 'id' => 'test.111', 'optionId' => 3),
				array('user' => 'abc', 'id' => 'test.112', 'optionId' => 4),
				array('user' => 'def', 'id' => 'test.111', 'optionId' => 1),
				array('user' => 'def', 'id' => 'test.112', 'optionId' => 5),
				array('user' => 'def', 'id' => 'test.113', 'optionId' => 1),
			),
			$this->getTestData()
		);
	}

	function testAnonymousVotes()
	{
		$lib = new RatingLib;

		$key1 = 'deadbeef01234567';
		$key2 = 'deadbeef23456789';
		$this->assertTrue($lib->record_anonymous_vote($key1, 'test', 111, 3));
		$this->assertTrue($lib->record_anonymous_vote($key1, 'test', 111, 2));
		$this->assertTrue($lib->record_anonymous_vote($key1, 'test', 112, 4));
		$this->assertTrue($lib->record_anonymous_vote($key2, 'test', 111, 1));
		$this->assertTrue($lib->record_anonymous_vote($key2, 'test', 113, 1));
		$this->assertTrue($lib->record_anonymous_vote($key2, 'test', 112, 5));

		$this->assertEquals(
			array(
				array('user' => "anonymous\0$key1", 'id' => 'test.111', 'optionId' => 2),
				array('user' => "anonymous\0$key1", 'id' => 'test.111', 'optionId' => 3),
				array('user' => "anonymous\0$key1", 'id' => 'test.112', 'optionId' => 4),
				array('user' => "anonymous\0$key2", 'id' => 'test.111', 'optionId' => 1),
				array('user' => "anonymous\0$key2", 'id' => 'test.112', 'optionId' => 5),
				array('user' => "anonymous\0$key2", 'id' => 'test.113', 'optionId' => 1),
			),
			$this->getTestData()
		);
	}

	function testDiscardInvalidValue()
	{
		$lib = new RatingLib;
		$this->assertFalse($lib->record_user_vote('abc', 'test', '123', 6));

		$this->assertEquals(range(1, 5), $lib->get_options('test'));
		$this->assertEquals(
			array(),
			$this->getTestData()
		);
	}

	function testGetWikiPageRange()
	{
		global $prefs;
		$prefs['wiki_simple_ratings_options'] = range(2, 8);

		$lib = new RatingLib;
		$this->assertEquals(range(2, 8), $lib->get_options('wiki page'));
	}

	function testGetArticleRange()
	{
		global $prefs;
		$prefs['article_user_rating_options'] = range(-2, 2);

		$lib = new RatingLib;
		$this->assertEquals(range(-2, 2), $lib->get_options('article'));
	}

	function testGetUserVote()
	{
		$lib = new RatingLib;
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 4, time() - 3000));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 2, time() - 2000));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 112, 3, time() - 1000));
		$this->assertTrue($lib->record_user_vote('def', 'test', 111, 3, time() - 1000));

		$this->assertEquals(2.0, $lib->get_user_vote('abc', 'test', 111));
	}

	function testGetMissingVote()
	{
		$lib = new RatingLib;
		$this->assertNull($lib->get_user_vote('abc', 'test', 111));
	}

	function testGetAnonymousVote()
	{
		$lib = new RatingLib;
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 4, time() - 3000));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 111, 2, time() - 2000));
		$this->assertTrue($lib->record_user_vote('abc', 'test', 112, 3, time() - 1000));
		$this->assertTrue($lib->record_anonymous_vote('deadbeef12345678', 'test', 111, 3, time() - 1000));

		$this->assertEquals(3.0, $lib->get_anonymous_vote('deadbeef12345678', 'test', 111));
	}

	function testEnvironmentUserLookup()
	{
		global $user;
		$user = 'foobar';

		$lib = new RatingLib;
		$this->assertTrue($lib->record_vote('test', '123', 2));

		$this->assertEquals(
			array(
				array('user' => "foobar", 'id' => 'test.123', 'optionId' => 2),
			),
			$this->getTestData()
		);

		$this->assertEquals(2.0, $lib->get_vote('test', '123'));
	}

	function testEnvironmentAnonymousLookup()
	{
		session_id('deadbeef01234567');

		$lib = new RatingLib;
		$this->assertTrue($lib->record_vote('test', '123', 2));

		$this->assertEquals(
			array(
				array('user' => "anonymous\0deadbeef01234567", 'id' => 'test.123', 'optionId' => 2),
			),
			$this->getTestData()
		);

		$this->assertEquals(2.0, $lib->get_vote('test', '123'));
	}

	function testCannotRecordOnUnknownObjectType()
	{
		$lib = new RatingLib;
		$this->assertFalse($lib->record_user_vote('abc', 'foobar', 111, 4));

		$this->assertEquals(
			array(),
			$this->getTestData()
		);
	}

	private function getTestData()
	{
		return TikiDb::get()->fetchAll('SELECT `user`, `id`, `optionId` FROM `tiki_user_votings` WHERE `id` LIKE ? ORDER BY `user`, `id`, `optionId`', array('test.%'));
	}
}

