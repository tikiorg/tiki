<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group integration
 */

require_once 'lib/auth/tokens.php';

class AuthTokensTest extends TikiDatabaseTestCase
{
	private $db;

	private $dt;

	private $table;

	private $obj;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/auth_tokens_dataset.xml');
	}

	function setUp()
	{
		$this->db = TikiDb::get();

		$this->dt = new DateTime;
		$this->dt->setTimezone(new DateTimeZone('UTC'));
		// 2012-02-03 15:25:07
		$this->dt->setTimestamp('1328282707');

		$this->table = $this->db->table('tiki_auth_tokens');

		$this->obj = new AuthTokens($this->db, array(), $this->dt);

		parent::setUp();
	}

	function testNoTokensIsDenied()
	{
		$params = array();
		$groups = $this->obj->getGroups('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'tiki-index.php', $params);
		$this->assertNull($groups);
	}

	function testCreateToken()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/auth_tokens_dataset_create.xml')
			->getTable('tiki_auth_tokens');

		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('timeout' => 5));

		$queryTable = $this->getConnection()->createQueryTable('tiki_auth_tokens', 'SELECT * FROM tiki_auth_tokens');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	function testTokenMatchesCompleteHash()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));

		$row = $this->db->query('SELECT tokenId, creation, timeout, entry, parameters, groups FROM tiki_auth_tokens ORDER BY creation desc')->fetchRow();

		$this->assertEquals(md5(implode('', $row)), $token['token']);
	}

	function testRetrieveGroupsForToken()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $this->dt->setTimestamp(time());
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->assertEquals(array('Registered'), $this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testAccessExpiredToken()
	{
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
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $this->dt->setTimestamp(time());
		$token = $this->obj->createToken('tiki-index.php', array(), array('Registered'));
		$this->assertEquals(array('Registered'), $this->obj->getGroups($token['token'], 'tiki-index.php', array()));
	}

	function testMaximumTimeout()
	{
		$lib = new AuthTokens(
			$this->db,
			array(
				'maxTimeout' => 10,
			)
		);
		$token = $lib->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('timeout' => 3600));

		$this->assertEquals(10, $this->db->getOne('SELECT timeout FROM tiki_auth_tokens ORDER BY creation desc'));
	}

	function testSameTokenTwice()
	{
		$token = $this->obj->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'));
		$this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));

		$this->assertNull($this->obj->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testAllowMultipleHits()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $lib = new AuthTokens($this->db, array('maxHits' => 100));
		$token = $lib->createToken('tiki-index.php', array('page' => 'HomePage'), array('Registered'), array('hits' => 3));
		$lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));
		$lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage'));

		$this->assertEquals(array('Registered'), $lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
		$this->assertNull($lib->getGroups($token['token'], 'tiki-index.php', array('page' => 'HomePage')));
	}

	function testLimitOnAccessCount()
	{
		$lib = new AuthTokens(
			$this->db,
			array(
				'maxHits' => 10,
			)
		);
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
		$this->db->query('TRUNCATE tiki_auth_tokens');
		$this->assertEquals(array(), $this->obj->getTokens());
	}

	function testGetTokens_shouldReturnAllTokens()
	{
		$token1 = '91bba2f998b48fce0146016809886127';
		$token2 = '823bde97a717c55b2cfbf9fbd6c81816';
		$token3 = 'e2990f7983b7b6c46b3987536aa38d32';

		$tokens = $this->obj->getTokens();

		$this->assertEquals(3, count($tokens));
		$this->assertEquals($token1, $tokens[0]['token']);
		$this->assertEquals($token2, $tokens[1]['token']);
		$this->assertEquals($token3, $tokens[2]['token']);
	}

	function testDeleteToken()
	{
		$token = $this->obj->createToken('tiki-user_send_reports.php', array(), array('Registered'));

		$this->obj->deleteToken($token['tokenId']);

		$this->assertEmpty($this->table->fetchRow(array('entry'), array('tokenId' => $token['tokenId'])));
	}

	function testGetGroups_shouldDeleteExpiredTokens()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/auth_tokens_dataset_delete_timeout.xml')
			->getTable('tiki_auth_tokens');

		$this->obj->getGroups('bcbcbf5a4fffa9cd734ec8aec6c66c2a', 'tiki-index.php', array());

		$queryTable = $this->getConnection()->createQueryTable('tiki_auth_tokens', 'SELECT * FROM tiki_auth_tokens');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	function testGetGroups_shouldDeleteTokensWithoutHitsLeft()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        // 2012-02-01 13:25:07
		$this->dt->setTimestamp('1328109907');

		$this->db->query('UPDATE tiki_auth_tokens set maxHits = -1, hits = -1 WHERE tokenId = 1');
		$this->db->query('UPDATE tiki_auth_tokens set maxHits = 10, hits = 0 WHERE tokenId = 2');

		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/auth_tokens_dataset_delete_hits.xml')
			->getTable('tiki_auth_tokens');

		$this->obj->getGroups('91bba2f998b48fce0146016809886127', 'tiki-index.php', array());

		$queryTable = $this->getConnection()->createQueryTable('tiki_auth_tokens', 'SELECT * FROM tiki_auth_tokens');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	function testGetGroups_shouldDecrementHits()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $this->obj->getGroups('91bba2f998b48fce0146016809886127', 'tiki-index.php', array());

		$this->assertEquals('9', $this->db->getOne('SELECT hits FROM tiki_auth_tokens WHERE tokenId = 2'));
	}

	function testGetGroups_shouldDecrementIfUnlimitedHits()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $this->db->query('UPDATE tiki_auth_tokens set maxHits = -1, hits = -1 WHERE tokenId = 2');

		$this->obj->getGroups('91bba2f998b48fce0146016809886127', 'tiki-index.php', array());

		$this->assertEquals('-1', $this->db->getOne('SELECT hits FROM tiki_auth_tokens WHERE tokenId = 2'));
	}
}

