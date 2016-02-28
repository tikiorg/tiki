<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_PageCache
{
	private $cacheData = array();
	private $key;
	private $meta = null;
	private $headerLibCopy = null;

	public static function create()
	{
		return new self;
	}

	function disableForRegistered()
	{
		global $user;

		if ( $user ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function onlyForGet()
	{
		if ( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function requiresPreference( $preference )
	{
		global $prefs;

		if ( $prefs[$preference] != 'y' ) {
			$this->cacheData = null;
		}

		return $this;
	}

	function addKeys( $array, $keys )
	{
		if ( is_array($this->cacheData) ) {
			foreach ( $keys as $k ) {
				if (!isset($this->cacheData[$k])) {
					$this->cacheData[$k] = isset($array[$k]) ? $array[$k] : null;
				}
			}
		}

		return $this;
	}

	function addArray( $array )
	{
		if ( is_array($this->cacheData) ) {
			$this->cacheData = array_merge($this->cacheData, $array);
		}

		return $this;
	}

	function addValue( $key, $value )
	{
		if ( is_array($this->cacheData) ) {
			$this->cacheData[$key] = $value;
		}

		return $this;
	}

	function checkMeta( $role, $data )
	{
		$this->meta = array_merge(array( 'role' => $role ), $data);

		return $this;
	}

	function applyCache()
	{
		if ( is_array($this->cacheData) ) {
			$memcachelib = TikiLib::lib("memcache");

			if ( TikiLib::lib("memcache")->isEnabled() ) {
				$this->key = $memcachelib->buildKey($this->cacheData);

				if ( $this->meta ) {
					list($cachedOutput, $metaTime) = $memcachelib->getMulti(
						array(
							$this->key,
							$this->meta,
						)
					);

					if ( $cachedOutput && $metaTime && $metaTime > $cachedOutput['timestamp'] ) {
						$cachedOutput = null;
					}
				} else {
					$cachedOutput = $memcachelib->get($this->key);
				}

				if ( $cachedOutput && $cachedOutput['output'] ) {
					$headerlib = TikiLib::lib('header');
					if (is_array($cachedOutput['jsfiles'])) {
						foreach ($cachedOutput['jsfiles'] as $rank => $files) {
							foreach ($files as $file) {
								$skip_minify = isset($cachedOutput['skip_minify']) ? true : false;
								$headerlib->add_jsfile_by_rank($file, $rank, $skip_minify);
							}
						}
					}
					if (is_array($cachedOutput['js'])) {
						foreach ($cachedOutput['js'] as $rank => $js) {
							foreach ($js as $j) {
								$headerlib->add_js($j, $rank);
							}
						}
					}
					if (is_array($cachedOutput['jq_onready'])) {
						foreach ($cachedOutput['jq_onready'] as $rank => $js) {
							foreach ($js as $j) {
								$headerlib->add_jq_onready($j, $rank);
							}
						}
					}
					if (is_array($cachedOutput['css'])) {
						foreach ($cachedOutput['css'] as $rank => $css) {
							foreach ($css as $c) {
								$headerlib->add_css($c, $rank);
							}
						}
					}
					if (is_array($cachedOutput['cssfile'])) {
						foreach ($cachedOutput['cssfile'] as $rank => $css) {
							foreach ($css as $c) {
								$headerlib->add_cssfile($c, $rank);
							}
						}
					}


					echo $cachedOutput['output'];
					echo "\n<!-- memcache ".htmlspecialchars($this->key)."-->";
					exit;
				}

				// save state of headerlib
				$this->headerLibCopy = unserialize(serialize(TikiLib::lib('header')));

				// Start caching, automatically gather at destruction
				ob_start();
			}
		}

		return $this;
	}

	function cleanUp()
	{
		if ( $this->key ) {
			$cachedOutput = array(
				'timestamp' => time(),
				'output'    => ob_get_contents()
			);

			if ($this->headerLibCopy) {
				$headerlib = TikiLib::lib('header');
				$cachedOutput['jsfiles']    = array_diff($headerlib->jsfiles, $this->headerLibCopy->jsfiles);
				$cachedOutput['skip_minify']= array_diff($headerlib->skip_minify, $this->headerLibCopy->skip_minify);
				$cachedOutput['jq_onready'] = array_diff($headerlib->jq_onready, $this->headerLibCopy->jq_onready);
				$cachedOutput['js']         = array_diff($headerlib->js, $this->headerLibCopy->js);
				$cachedOutput['css']        = array_diff($headerlib->css, $this->headerLibCopy->css);
				$cachedOutput['cssfiles']   = array_diff($headerlib->cssfiles, $this->headerLibCopy->cssfiles);
			}

			if ( $cachedOutput['output'] ) {
				TikiLib::lib("memcache")->set($this->key, $cachedOutput);
			}

			ob_end_flush();
		}

		$this->cacheData = array();
		$this->key = null;
		$this->meta = null;
	}

	function invalidate()
	{
		if ( $this->meta ) {
			TikiLib::lib("memcache")->set($this->meta, time());
		}
	}

	function __destruct()
	{
		$this->cleanUp();
	}
}


