<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class AuthTokens
{
	const SCHEME = 'MD5( CONCAT(tokenId, creation, timeout, entry, parameters, groups) )';
	private $db;
	private $maxTimeout = 3600;
	private $maxHits = 1;
	public $ok = false;

	public static function build( $prefs ) {
		return new AuthTokens( TikiDb::get(), array(
			'maxTimeout' => $prefs['auth_token_access_maxtimeout'],
			'maxHits' => $prefs['auth_token_access_maxhits'],
		) );
	}

	function __construct( $db, $options = array() ) {
		$this->db = $db;

		if( isset( $options['maxTimeout'] ) ) {
			$this->maxTimeout = (int) $options['maxTimeout'];
		}

		if( isset( $options['maxHits'] ) ) {
			$this->maxHits = (int) $options['maxHits'];
		}
	}

	function getToken( $token ){
		$data = $this->db->query( 'SELECT * FROM tiki_auth_tokens WHERE token = ? AND token = ' . self::SCHEME, array( $token ) )
			->fetchRow();
		return $data;
	}
	
	
	function getGroups( $token, $entry, $parameters ) {
		$this->db->query( 'DELETE FROM tiki_auth_tokens WHERE UNIX_TIMESTAMP(creation) + timeout < UNIX_TIMESTAMP() OR `hits` <= 0' );
		$data = $this->db->query( 'SELECT tokenId, entry, parameters, groups FROM tiki_auth_tokens WHERE token = ? AND token = ' . self::SCHEME, array( $token ) )
			->fetchRow();

		if( $data['entry'] != $entry ) {
			return null;
		}

		$registered = (array) json_decode( $data['parameters'], true );
		if( ! $this->allPresent( $registered, $parameters )
			|| ! $this->allPresent( $parameters, $registered ) ) {
			return null;
		}

		$this->db->query( 'UPDATE `tiki_auth_tokens` SET `hits` = `hits` - 1 WHERE `tokenId` = ?', array( $data['tokenId'] ) );
		$this->ok = true;
		return (array) json_decode( $data['groups'], true );
	}

	private function allPresent( $a, $b ) {
		foreach( $a as $key => $value ) {
			if( ! isset($b[$key]) || $value != $b[$key] ) {
				return false;
			}
		}

		return true;
	}

	function createToken( $entry, array $parameters, array $groups, array $arguments = array() ) {
		if( isset( $arguments['timeout'] ) ) {
			$timeout = min( $this->maxTimeout, $arguments['timeout'] );
		} else {
			$timeout = $this->maxTimeout;
		}

		if( isset( $arguments['hits'] ) ) {
			$hits = min( $this->maxHits, $arguments['hits'] );
		} else {
			$hits = $this->maxHits;
		}

		$this->db->query( 'INSERT INTO tiki_auth_tokens ( timeout, maxhits, hits, entry, parameters, groups, email ) VALUES( ?, ?, ?, ?, ?, ?, ? )', array(
			(int) $timeout,
			(int) $hits,
			(int) $hits,
			$entry,
			json_encode( $parameters ),
			json_encode( $groups ),
			$arguments['email']
		) );
		$max = $this->db->getOne( 'SELECT MAX(tokenId) FROM tiki_auth_tokens' );

		$this->db->query( 'UPDATE tiki_auth_tokens SET token = ' . self::SCHEME . ' WHERE tokenId = ?', array( $max ) );

		return $this->db->query( 'SELECT * FROM tiki_auth_tokens WHERE tokenId = ?', array( $max ) )
		->fetchRow();
	}

	function includeToken( $url, array $groups = array(), $email = "" ) {
		$data = parse_url( $url );
		if( isset( $data['query'] ) ) {
			parse_str( $data['query'], $args );
			unset( $args['TOKEN'] );
		} else {
			$args = array();
		}

		$token = $this->createToken( $data['path'], $args, $groups, array('email'=>$email) );
		$args['TOKEN'] = $token['token'];

		$query = '?' . http_build_query( $args, '', '&' );

		if( ! isset( $data['fragment'] ) ) {
			$anchor = '';
		} else {
			$anchor = "#{$data['fragment']}";
		}

		return "{$data['scheme']}://{$data['host']}{$data['path']}$query$anchor";
	}
	
	function includeTokenReturn( $url, array $groups = array(), $email = "" ) {
		$data = parse_url( $url );
		if( isset( $data['query'] ) ) {
			parse_str( $data['query'], $args );
			unset( $args['TOKEN'] );
		} else {
			$args = array();
		}
		
		$token = $this->createToken( $data['path'], $args, $groups, array('email'=>$email) );
		$args['TOKEN'] = $token['token'];
		
		$query = '?' . http_build_query( $args, '', '&' );

		if( ! isset( $data['fragment'] ) ) {
			$anchor = '';
		} else {
			$anchor = "#{$data['fragment']}";
		}
		
		$token['url'] = "{$data['scheme']}://{$data['host']}{$data['path']}$query$anchor";
		
		return $token;
	}
}
