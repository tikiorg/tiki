<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** 
 * @group integration
 */

require_once 'lib/auth/tokens.php';

class AuthTokensTest extends PHPUnit_Framework_TestCase
{
	private $db;
	
	private $table;
	
	private $obj;

	function setUp()
	{	
		$this->db = TikiDb::get();
		$this->db->query('TRUNCATE tiki_auth_tokens');
		
		$this->table = $this->db->table('tiki_auth_tokens');
		
		$this->obj = new AuthTokens($this->db);
	}

	function tearDown()
	{
		if ($this->db) {
			$this->db->query('TRUNCATE tiki_auth_tokens');
		}
	}

	function testNoTokensIsDenied()
	{
		$params = array();
		$groups = $this->obj->getGroups('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'tiki-index.php', $params);
		$this->assertNull($groups);
	}

	function testCreateToken()
	{
		$data = array(
				'tokenId' => 1,
				'timeout' => 5,
				'entry' => 'tiki-index.php',
				'parameters' => '{"page":"HomePage"}',
				'groups' => '["Registered"]',
				);

		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('timeout' => 5));

		$this->assertEquals($data, $this->db->query('SELECT tokenId, timeout, entry, parameters, groups FROM tiki_auth_tokens')->fetchRow());
		$this->assertEquals(32, strlen($token['token']));
	}

	function testTokenMatchesCompleteHash()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));

		$row = $this->db->query('SELECT tokenId, creation, timeout, entry, parameters, groups FROM tiki_auth_tokens')->fetchRow();

		$this->assertEquals(md5(implode('', $row)), $token['token']);
	}

	function testRetrieveGroupsForToken()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->assertEquals(array('Registered'), $this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testAccessExpiredToken()
	{
		$this->db->query('INSERT INTO tiki_auth_tokens (tokenId, creation, timeout, entry, parameters, groups, token) VALUES(?, ?, ?, ?, ?, ?, ?)', array(1, '2009-11-05 11:45:16', 5, 'tiki-index.php', '{"page":"HomePage"}', '["Registered"]', "946fc2fa0a5e1cecd54440ce733b8fb4"));

		$this->assertNull($this->obj->getGroups("946fc2fa0a5e1cecd54440ce733b8fb4", 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testAlteredDataCancels()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->db->query('UPDATE tiki_auth_tokens SET groups = \'["Admins"]\'');
		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testExtraDataCancels()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage', 'hello' => 'world')));
	}

	function testMissingDataCancels()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage', 'foobar' => 'baz'), array('Registered'));
		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testDifferingEntryCancels()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-print.php', array('page' => 'HomePage')));
	}

	function testDifferingValueCancels()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'Home')));
	}

	function testNoParamerers()
	{
		$token = $this->obj->createToken('tiki-index.php', array(), array('Registered'));
		$this->assertEquals(array('Registered'), $this->obj->getGroups($token['token'], 'tiki-index.php', array()));
	}

	function testMaximumTimeout()
	{
		$lib = new AuthTokens($this->db, array(
					'maxTimeout' => 10,
					));
		$token = $lib->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('timeout' => 3600));

		$this->assertEquals(10, $this->db->getOne('SELECT timeout FROM tiki_auth_tokens WHERE tokenId = 1'));
	}

	function testSameTokenTwice()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));

		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testAllowMultipleHits()
	{
		$lib = new AuthTokens($this->db, array('maxHits' => 100));
		$token = $lib->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('hits' => 3));
		$lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));
		$lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));

		$this->assertEquals(array('Registered'), $lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
		$this->assertNull($lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testLimitOnAccessCount()
	{
		$lib = new AuthTokens($this->db, array(
					'maxHits' => 10,
					));
		$token = $lib->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('hits' => 3600));

		$this->assertEquals(10, $this->db->getOne('SELECT hits FROM tiki_auth_tokens WHERE tokenId = 1'));
	}

	function testIncludeToken()
	{
		$url = 'http://example.com/tiki/tiki-index.php?page=SomePage';
		$new = $this->obj->includeToken($url);

		$this->assertRegExp('/TOKEN=[a-z0-9]{32}/i', $new);
		$this->assertContains('http://example.com/tiki/tiki-index.php', $new);
		$this->assertContains('page=SomePage', $new);
	}

	function testIncludeTokenNoPath()
	{
		$url = 'http://example.com/tiki-index.php';
		$new = $this->obj->includeToken($url);

		$this->assertRegExp('/TOKEN=[a-z0-9]{32}/i', $new);
		$this->assertContains('http://example.com/tiki-index.php', $new);
	}

	function testWithFragment()
	{
		$url = 'http://example.com/tiki-index.php#Test';
		$new = $this->obj->includeToken($url);

		$this->assertRegExp('/TOKEN=[a-z0-9]{32}#Test/i', $new);
	}
	
	function testGetTokens_shouldReturnEmptyArrayIfNoToken()
	{
		$this->assertEquals(array(), $this->obj->getTokens());
	}
	
	function testGetTokens_shouldReturnAllTokens()
	{
		$url1 = 'tiki-index.php';
		$url2 = 'tiki-user_send_reports.php';
		
		$this->obj->createToken($url1, array('page' => 'HomePage'), array('Registered'));
		$this->obj->createToken($url2, array(), array('Admin'));
		
		$tokens = $this->obj->getTokens();
		
		$this->assertEquals(2, count($tokens));
		$this->assertEquals($url1, $tokens[0]['entry']);
		$this->assertEquals($url2, $tokens[1]['entry']);
	}
	
	function testDeleteToken()
	{
		$token = $this->obj->createToken('tiki-user_send_reports.php', array(), array('Registered'));
		
		$this->obj->deleteToken($token['tokenId']);

		$this->assertEmpty($this->table->fetchRow(array('entry'), array('tokenId' => $token['tokenId'])));
	}
}

