<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MemcacheSession
{

	private $enabled = false;
	private $lib;

	/**
	 * Set up the session cache, hijacking handlers from ADODB_Session 
	 * presumably already in place.
	 */
	function _init() {

		session_module_name( 'user' );
		session_set_save_handler(
			array( $this, 'open' ),
			array( $this, 'close' ),
			array( $this, 'read' ),
			array( $this, 'write' ),
			array( $this, 'destroy' ),
			array( $this, 'gc' )
		);

		global $memcachelib;
		$this->enabled = isset( $memcachelib ) &&
			$memcachelib->isEnabled();

		$this->lib = $memcachelib;
	}

	/**
	 * Build a memcache key based on a given session key
	 *
	 * @param  string Session key
	 * @return string Memcache key
	 */
	function _buildCacheKey( $session_key ) {
		return $this->lib ?
			$this->lib->buildKey(array(
				'role'        => 'session-cache',
				'session_key' => $session_key
			))
			: false;
	}

	function __destruct() {
		session_write_close();
	}

	function open( $save_path, $session_name, $persist = NULL ) {
		return $this->enabled;
	}

	function close() {
		return $this->enabled;
	}

	function read( $key ) {
		$cache_key = $this->_buildCacheKey( $key );

		if( $this->enabled ) {
			return $this->lib->get( $cache_key );
		}
	}

	function write( $key, $val ){
		global $prefs; 

		if( $this->enabled ) {
			$this->lib->set( $this->_buildCacheKey($key), $val, 60 * $prefs['session_lifetime'] );
		}

		return $this->enabled;
	}

	function destroy( $key ) {
		if( $this->enabled ) {
			$this->lib->delete( $this->_buildCacheKey($key) );
		}

		return $this->enabled;
	}

	function gc( $maxlifetime ) {
	}
}

$memcache_session = new MemcacheSession;
$memcache_session->_init();

