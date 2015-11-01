<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Dynamically loads and caches profile symbols for usage in templates.
 *
 * The following provides the value for the most recent entry with such name
 * Smarty: {$symbols.some_name} 
 * PHP: $symbols['some_name']
 *
 * Common names may appear in multiple profiles. It is possible to narrow them down:
 * 
 * Smarty: {$symbols->Profile_Name_Here.some_name}
 * PHP: $symbols->Profile_Name_Here['some_name']
 */
class Tiki_Profile_SymbolLoader implements ArrayAccess
{
	private $store;
	private $filters;
	private $nextFilters;

	function __construct($store = null, array $filters = null, array $nextFilters = array('profile', 'domain'))
	{
		$this->store = $store ?: new Tiki_Profile_SymbolLoader_Store;
		$this->nextFilters = $nextFilters;
		$this->filters = $filters ?: array(
			'profile' => '',
			'domain' => '',
		);
	}

	function offsetGet($name)
	{
		return $this->store->get($name, $this->filters);
	}

	function offsetExists($name) { return true; }
	function offsetSet($name, $value) {}
	function offsetUnset($name) {}

	function __get($name)
	{
		$nextFilters = $this->nextFilters;
		$next = array_shift($nextFilters);
		if ($next) {
			$filters = $this->filters;
			$filters[$next] = $name;
			return new self($this->store, $filters, $nextFilters);
		}
	}
}

class Tiki_Profile_SymbolLoader_Store
{
	const KEY = 'profile_symbols_lookup';
	private $data = false;

	function get($name, $filters)
	{
		$this->loadData();
		$profile = $filters['profile'];
		$domain = $filters['domain'];

		if (! isset($this->data[$domain][$profile][$name])) {
			$this->data[$domain][$profile][$name] = $this->fetch($name, $filters);

			// Storage should be done at most once per request, but this will only be possible
			// in 13 reliably
			$this->storeData();
		}

		return $this->data[$domain][$profile][$name];
	}

	function fetch($name, $filters)
	{
		$filters = array_filter($filters);
		$filters['object'] = $name;

		$table = TikiDb::get()->table('tiki_profile_symbols');
		return $table->fetchOne('value', $filters, 'creation_date_desc');
	}

	private function loadData()
	{
		if ($this->data !== false) {
			return;
		}

		$cache = TikiLib::lib('cache');
		if (! $data = $cache->getSerialized(self::KEY)) {
			$data = array();
		}

		$this->data = $data;
	}

	private function storeData()
	{
		$cache = TikiLib::lib('cache');
		$cache->cacheItem(self::KEY, serialize($this->data));
	}
}
