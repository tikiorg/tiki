<?php

class MemcacheSession {

	private $enabled = false;

	/**
	 * Set up the session cache, hijacking handlers from ADODB_Session 
	 * presumably already in place.
	 */
	function _init() {

		session_module_name( 'tikimemcache' );
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
	}

	/**
	 * Build a memcache key based on a given session key
	 *
	 * @param  string Session key
	 * @return string Memcache key
	 */
	function _buildCacheKey( $session_key ) {
		global $memcachelib;
		return isset( $memcachelib ) ?
			$memcachelib->buildKey(array(
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
		global $memcachelib;

		$cache_key = $this->_buildCacheKey( $key );

		if( $this->enabled ) {
			return $memcachelib->get( $cache_key );
		}
	}

	function write( $key, $val ){
		global $memcachelib, $prefs; 

		if( $this->enabled ) {
			$memcachelib->set( $this->_buildCacheKey($key), $val, 60 * $prefs['session_lifetime'] );
		}

		return $this->enabled;
	}

	function destroy( $key ) {
		global $memcachelib; 

		if( $this->enabled ) {
			$memcachelib->delete( $this->_buildCacheKey($key) );
		}

		return $this->enabled;
	}

	function gc( $maxlifetime ) {
	}
}

$memcache_session = new MemcacheSession;
$memcache_session->_init();

