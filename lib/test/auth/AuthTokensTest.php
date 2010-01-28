<?php

/*
 * Test groups that this PHPUnit test belongs to
 * 
 * @group integration
 * 
 */

require_once 'lib/auth/tokens.php';

class AuthTokensTest extends PHPUnit_Framework_TestCase
{
	private $db;
	
	function setUp() {
		$this->db = TikiDb::get();
		$this->db->query( 'TRUNCATE tiki_auth_tokens' );
	}

	function tearDown() {
		if ($this->db) {
			$this->db->query( 'TRUNCATE tiki_auth_tokens' );
		}
	}
		
	function testNoTokensIsDenied() {
		$lib = new AuthTokens( $this->db );

		$params = array();
		$groups = $lib->getGroups( 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'tiki-index.php', $params );
		$this->assertNull( $groups );
	}

	function testCreateToken() {
		$lib = new AuthTokens( $this->db );
		$data = array(
			'tokenId' => 1,
			'timeout' => 5,
			'entry' => 'tiki-index.php',
			'parameters' => '{"page":"HomePage"}',
			'groups' => '["Registered"]',
		);

		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );

		$this->assertEquals( $data, $this->db->query( 'SELECT tokenId, timeout, entry, parameters, groups FROM tiki_auth_tokens' )->fetchRow() );
		$this->assertEquals( 32, strlen( $token ) );
	}

	function testTokenMatchesCompleteHash() {
		$lib = new AuthTokens( $this->db );

		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );

		$row = $this->db->query( 'SELECT tokenId, creation, timeout, entry, parameters, groups FROM tiki_auth_tokens' )->fetchRow();

		$this->assertEquals( md5( implode('', $row) ), $token );
	}

	function testRetrieveGroupsForToken() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );
		$this->assertEquals( array( 'Registered' ), $lib->getGroups( $token, 'tiki-index.php', array('page' => 'HomePage') ) );
	}

	function testAccessExpiredToken() {
		$lib = new AuthTokens( $this->db );
		$this->db->query( 'INSERT INTO tiki_auth_tokens (tokenId, creation, timeout, entry, parameters, groups, token) VALUES(?, ?, ?, ?, ?, ?, ?)', array( 1, '2009-11-05 11:45:16', 5, 'tiki-index.php', '{"page":"HomePage"}', '["Registered"]', "946fc2fa0a5e1cecd54440ce733b8fb4" ) );

		$this->assertNull( $lib->getGroups( "946fc2fa0a5e1cecd54440ce733b8fb4", 'tiki-index.php', array( 'page' => 'HomePage' ) ) );
	}

	function testAlteredDataCancels() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );
		$this->db->query( 'UPDATE tiki_auth_tokens SET groups = \'["Admins"]\'' );
		$this->assertNull( $lib->getGroups( $token, 'tiki-index.php', array('page' => 'HomePage') ) );
	}

	function testExtraDataCancels() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );
		$this->assertNull( $lib->getGroups( $token, 'tiki-index.php', array('page' => 'HomePage', 'hello' => 'world') ) );
	}

	function testMissingDataCancels() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage', 'foobar' => 'baz'), array( 'Registered' ), 5 );
		$this->assertNull( $lib->getGroups( $token, 'tiki-index.php', array('page' => 'HomePage') ) );
	}

	function testDifferingEntryCancels() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );
		$this->assertNull( $lib->getGroups( $token, 'tiki-print.php', array('page' => 'HomePage') ) );
	}

	function testDifferingValueCancels() {
		$lib = new AuthTokens( $this->db );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 5 );
		$this->assertNull( $lib->getGroups( $token, 'tiki-index.php', array('page' => 'Home') ) );
	}

	function testMaximumTimeout() {
		$lib = new AuthTokens( $this->db, array(
			'maxTimeout' => 10,
		) );
		$token = $lib->createToken( 'tiki-index.php', array('page' => 'HomePage'), array( 'Registered' ), 3600 );
		
		$this->assertEquals( 10, $this->db->getOne( 'SELECT timeout FROM tiki_auth_tokens WHERE tokenId = 1' ) );
	}
}

