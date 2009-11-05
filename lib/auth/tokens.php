<?php

class AuthTokens
{
	const SCHEME = 'MD5( CONCAT(tokenId, creation, timeout, entry, parameters, groups) )';
	private $db;
	private $maxTimeout;

	public static function build( $prefs ) {
		return new AuthTokens( TikiDb::get(), array(
			'maxTimeout' => $prefs['auth_token_access_maxtimeout'],
		) );
	}

	function __construct( $db, $options = array() ) {
		$this->db = $db;

		if( isset( $options['maxTimeout'] ) ) {
			$this->maxTimeout = (int) $options['maxTimeout'];
		}
	}

	function getGroups( $token, $entry, $parameters ) {
		$this->db->query( 'DELETE FROM tiki_auth_tokens WHERE creation + timeout < NOW()' );
		$data = $this->db->query( 'SELECT entry, parameters, groups FROM tiki_auth_tokens WHERE token = ? AND token = ' . self::SCHEME, array( $token ) )
			->fetchRow();

		if( $data['entry'] != $entry ) {
			return null;
		}

		$registered = json_decode( $data['parameters'], true );
		if( ! $this->allPresent( $registered, $parameters )
			|| ! $this->allPresent( $parameters, $registered ) ) {
			return null;
		}

		return json_decode( $data['groups'], true );
	}

	private function allPresent( $a, $b ) {
		foreach( $a as $key => $value ) {
			if( ! isset($b[$key]) || $value != $b[$key] ) {
				return false;
			}
		}

		return true;
	}

	function createToken( $entry, array $parameters, array $groups, $timeout = 3600 ) {
		if( ! is_null( $this->maxTimeout ) ) {
			$timeout = min( $this->maxTimeout, $timeout );
		}

		$this->db->query( 'INSERT INTO tiki_auth_tokens ( timeout, entry, parameters, groups ) VALUES( ?, ?, ?, ? )', array(
			(int) $timeout,
			$entry,
			json_encode( $parameters ),
			json_encode( $groups ),
		) );
		$max = $this->db->getOne( 'SELECT MAX(tokenId) FROM tiki_auth_tokens' );

		$this->db->query( 'UPDATE tiki_auth_tokens SET token = ' . self::SCHEME . ' WHERE tokenId = ?', array( $max ) );

		return $this->db->getOne( 'SELECT token FROM tiki_auth_tokens WHERE tokenId = ?', array( $max ) );
	}
}

?>
