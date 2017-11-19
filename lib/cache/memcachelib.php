<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Class Memcachelib
 *
 * Simple central point for configuring and using memcache support.
 *
 * This utility library is not a complete wrapper for PHP memcache functions,
 * and only provides a minimal set currently in use in SUMO.
 */
class Memcachelib
{

	public $memcache;
	public $options;

	/**
	 * Memcachelib constructor.
	 * @param bool $memcached_servers
	 * @param bool $memcached_options
	 */
	function __construct($memcached_servers = false, $memcached_options = false)
	{
		global $prefs, $tikidomainslash;

		if ($memcached_servers === false && $memcached_options === false) {
			if (is_array($prefs['memcache_servers'])) {
				$memcached_servers = $prefs['memcache_servers'];
			} else {
				$memcached_servers = unserialize($prefs['memcache_servers']);
			}

			$memcached_options = [
				'enabled' => true,
				'expiration' => (int) $prefs['memcache_expiration'],
				'key_prefix' => $prefs['memcache_prefix'],
				'compress' => $prefs['memcache_compress'],
			];
		}

		$localphp = "db/{$tikidomainslash}local.php";

		if (is_readable($localphp)) {
			// Should be defined by unserializing $prefs['memcache_options']
			// and $prefs['memcache_servers']. Currently happens in
			// /webroot/tiki-setup_base.php
			// preferences are overwritten in local.php (if defined)
			require($localphp);
		}

		if (! $memcached_servers || (! empty($memcached_options) && ! $memcached_options['enabled']) || ! class_exists('Memcache')) {
			$this->memcache = false;
			$this->options  = [ 'enabled' => false ];
		} else {
			if ($memcached_options['compress'] == 'y') {
				$memcached_options['flags'] = MEMCACHE_COMPRESSED;
				unset($memcached_options['compress']);
			} else {
				$memcached_options['flags'] = 0;
			}

			$this->options  = $memcached_options;
			$this->memcache = new Memcache();
			foreach ($memcached_servers as $server) {
				if ($server['host'] == 'localhost') {
					$server['host'] = '127.0.0.1';
				}

				$this->memcache->addServer(
					$server['host'],
					(int) $server['port'],
					isset($server['persistent']) ? $server['persistent'] : false,
					isset($server['weight']) ? (int)$server['weight'] : 1
				);
			}
		}

		$this->key_prefix = $this->getOption('key_prefix', '');
	}

	/**
	 * Return a reference to the memcache object.
	 * @return object
	 */
	function getMemcache()
	{
		return $this->memcache;
	}

	/**
	 * Get an option, with default.
	 * @param  string $name of the option
	 * @param  mixed  $default value
	 * @return mixed  value of the option, or default.
	 */
	function getOption($name, $default = null)
	{
		return isset($this->options[$name]) ?
			$this->options[$name] : $default;
	}

	/**
	 * Return whether this thing is usable.
	 * @return boolean
	 */
	function isEnabled()
	{
		global $prefs;
		if (isset($prefs['memcache_enabled']) && $prefs['memcache_enabled'] == 'y') {
			return $this->memcache && $this->getOption('enabled', false);
		} else {
			return false;
		}
	}

	/**
	 * Get a key from memcache
	 *
	 * @param  mixed $key, passed through buildKey() before use
	 * @param  mixed $default value returned if result from memcache is NULL
	 * @return mixed Value from memcache, or the default
	 */
	function get($key, $default = null)
	{
		$key = $this->buildKey($key);
		$val = $this->memcache->get($key);
		return ($val !== null) ? $val : $default;
	}

	/**
	 * Get multiple keys from memcache at once.
	 *
	 * This differs from native memcache get() behavior in that all keys
	 * passed in will result in a corresponding value returned.  If the
	 * key was not found in the cache, the returned value will be NULL.
	 *
	 * @param  array $keys, each will be passed through buildKey() before use
	 * @return array Values, in order of keys passed.
	 */
	function getMulti($keys)
	{

		// Run each key passed in through the buildKey() method.
		$keys_built = [];
		foreach ($keys as $key) {
			$keys_built[] = $this->buildKey($key);
		}

		// Fetch the assoc array of keys/values available in memcache.
		$values_in = $this->memcache->get($keys_built);

		// Construct a list of values corresponding to the keys passed in.
		$values_out = [];
		foreach ($keys_built as $kb) {
			$values_out[] = (isset($values_in[$kb])) ? $values_in[$kb] : null;
		}

		return $values_out;
	}

	/**
	 * Set a key in memcache

	 * @param mixed $key, passed through buildKey() before use
	 * @param mixed $value
	 * @param bool $flags       Optional memcache flags
	 * @param bool $expiration  Optional expiration time
	 * @return bool
	 */
	function set($key, $value, $flags = false, $expiration = false)
	{
		$key = $this->buildKey($key);
		$flags = ($flags) ?
			$flags : $this->getOption('flags', 0);
		$expiration = ($expiration) ?
			$expiration : $this->getOption('expiration', 0);

		if (isset($this->memcache) && method_exists($this->memcache, "set")) {
			return $this->memcache->set($key, $value, $flags, $expiration);
		}
	}

	/**
	 * Delete a key in memcache
	 *
	 * @param $key, passed through buildKey() before use
	 * @return bool
	 */
	function delete($key)
	{
		$key = $this->buildKey($key);
		return $this->memcache->delete($key);
	}

	/**
	 * Flush the memcache cache
	 */
	function flush()
	{
		return $this->memcache->flush();
	}

	/**
	 * Build a cache key from a given parameter
	 *
	 * @param  mixed  A string, or an object to be turned into a key.
	 * @return string The cache key.
	 */

	/**
	 * Build a cache key from a given parameter
	 *
	 * @param mixed $key            a string, or an object to be turned into a key
	 * @param bool $use_md5
	 * @return string               the cache key
	 */
	function buildKey($key, $use_md5 = false)
	{

		if (is_string($key)) {
			return (strpos($key, $this->key_prefix) !== 0) ?
				$this->key_prefix . $key : $key;
		}

		if (is_array($key)) {
			$keys = array_keys($key);
			sort($keys);

			$parts = [];
			foreach ($keys as $name) {
				$val = $key[$name];
				if ($val !== null) {
					$parts[] = $name . '=' . $val;
				}
			}

			$str_key = join(':', $parts);
			return $this->key_prefix .
				( $use_md5 ? md5($str_key) : '[' . $str_key . ']' );
		}
	}
}
