<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Schema;
use TikiDb;

/**
 * Helper class to create an in-memory cache to avoid excessive database queries while
 * importing. The class will initially attempt to load a reasonable amount of data. If
 * the source contains more values than could reasonably be loaded, additional values
 * will be looked up one at a time before being cached.
 */
class CachedLookupHelper
{
	private $baseCount;
	private $cache = [];
	private $init;
	private $lookup;
	private $enableLookup = false;

	function __construct($baseCount = 100)
	{
		$this->baseCount = $baseCount;
	}

	function setInit(callable $fn)
	{
		$this->init = $fn;
	}

	function setLookup(callable $fn)
	{
		$this->lookup = $fn;
		$this->enableLookup = true;
	}

	function get($value)
	{
		if ($this->init) {
			// Enable lookup on missing values only if not all values have been initially
			// loaded after attempting to load a fixed amount.
			$this->cache = call_user_func($this->init, $this->baseCount);
			$this->enableLookup = $this->enableLookup && count($this->cache) >= $this->baseCount;
			$this->init = null;
		}

		if (isset($this->cache[$value])) {
			return $this->cache[$value];
		}

		if ($this->enableLookup) {
			return $this->cache[$value] = call_user_func($this->lookup, $value);
		}
	}

	public static function fieldLookup($fieldId)
	{
		$table = TikiDb::get()->table('tiki_tracker_item_fields');
		
		$cache = new self;
		$cache->setInit(function ($count) use ($table, $fieldId) {
			return $table->fetchMap('itemId', 'value', [
				'fieldId' => $fieldId,
			], $count, 0);
		});
		$cache->setLookup(function ($value) use ($table, $fieldId) {
			return $table->fetchOne('value', [
				'fieldId' => $fieldId,
				'itemId' => $value,
			]);
		});

		return $cache;
	}

	public static function fieldInvert($fieldId)
	{
		$table = TikiDb::get()->table('tiki_tracker_item_fields');
		
		$cache = new self;
		$cache->setInit(function ($count) use ($table, $fieldId) {
			return $table->fetchMap('value', 'itemId', [
				'fieldId' => $fieldId,
			], $count, 0);
		});
		$cache->setLookup(function ($value) use ($table, $fieldId) {
			return $table->fetchOne('itemId', [
				'fieldId' => $fieldId,
				'value' => $value,
			]);
		});

		return $cache;
	}
}

