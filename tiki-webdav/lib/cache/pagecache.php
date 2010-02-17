<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_PageCache
{
	private $cacheData = array();
	private $key;
	private $meta = null;

	public static function create() {
		return new self;
	}

	function disableForRegistered() {
		global $user;

		if( $user ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function onlyForGet() {
		if( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function requiresPreference( $preference ) {
		global $prefs;

		if( $prefs[$preference] != 'y' ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function addKeys( $array, $keys ) {
		if( is_array( $this->cacheData ) ) {
			foreach( $keys as $k ) {
				$this->cacheData[$k] = isset( $array[$k] ) ? $array[$k] : null;
			}
		}

		return $this;
	}

	function addArray( $array ) {
		if( is_array( $this->cacheData ) ) {
			$this->cacheData = array_merge( $this->cacheData, $array );
		}

		return $this;
	}

	function addValue( $key, $value ) {
		if( is_array( $this->cacheData ) ) {
			$this->cacheData[$key] = $value;
		}
		
		return $this;
	}

	function checkMeta( $role, $data ) {
		$this->meta = array_merge( array( 'role' => $role ), $data );
		
		return $this;
	}

	function applyCache() {
		if( is_array( $this->cacheData ) ) {
			global $memcachelib;

			if( $memcachelib && $memcachelib->isEnabled() ) {
				$this->key = $memcachelib->buildKey( $this->cacheData );

				if( $this->meta ) {
					list($cachedOutput, $metaTime) = $memcachelib->getMulti(array(
						$this->key,
						$this->meta,
					));

					if( $cachedOutput && $metaTime && $metaTime > $cachedOutput['timestamp'] ) {
						$cachedOutput = null;
					}
				} else {
					$cachedOutput = $memcachelib->get( $this->key );
				}

				if( $cachedOutput && $cachedOutput['output'] ) {
					echo $cachedOutput['output']; 
					echo "\n<!-- memcache ".htmlspecialchars($this->key)."-->";
					exit;
				}

				// Start caching, automatically gather at destruction
				ob_start();
			}
		}

		return $this;
	}

	function cleanUp() {
		global $memcachelib;

		if( $this->key && $memcachelib ) {
			$cachedOutput = array(
				'timestamp' => time(),
				'output'    => ob_get_contents()
			);

			if( $cachedOutput['output'] ) {
				$memcachelib->set( $this->key, $cachedOutput );
			}

			ob_end_flush();
		}

		$this->cacheData = array();
		$this->key = null;
		$this->meta = null;
	}

	function invalidate() {
		global $memcachelib;

		if( $this->meta && $memcachelib ) {
			$memcachelib->set( $this->meta, time() );
		}
	}

	function __destruct() {
		$this->cleanUp();
	}
}


