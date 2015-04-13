<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Factory
{
	private static $trackerFieldLocalCache;

	private $trackerDefinition;
	private $typeMap = array();
	private $infoMap = array();

	function __construct($trackerDefinition = null)
	{
		$this->trackerDefinition = $trackerDefinition;

		$fieldMap = $this->buildTypeMap(
			array(
				'lib/core/Tracker/Field' => 'Tracker_Field_',
			)
		);
	}

	private function getPreCacheTypeMap()
	{
		if (!empty(self::$trackerFieldLocalCache)) {
			$this->typeMap = self::$trackerFieldLocalCache['type'];
			$this->infoMap = self::$trackerFieldLocalCache['info'];
			return true;
		}

		return false;
	}

	private function setPreCacheTypeMap($data)
	{
		self::$trackerFieldLocalCache = array(
			'type' => $data['typeMap'],
			'info' => $data['infoMap']
		);
	}

	private function buildTypeMap($paths)
	{
		global $prefs;
		$cacheKey = 'fieldtypes.' . $prefs['language'];

		if ($this->getPreCacheTypeMap()) {
			return;
		}

		$cachelib = TikiLib::lib('cache');
		if ($data = $cachelib->getSerialized($cacheKey)) {
			$this->typeMap = $data['typeMap'];
			$this->infoMap = $data['infoMap'];

			$this->setPreCacheTypeMap($data);
			return;
		}

		foreach ($paths as $path => $prefix) {
			foreach (glob("$path/*.php") as $file) {
				if ($file === "$path/index.php")
					continue;
				$class = $prefix . substr($file, strlen($path) + 1, -4);
				$reflected = new ReflectionClass($class);

				if ($reflected->isInstantiable() && $reflected->implementsInterface('Tracker_Field_Interface')) {
					$providedFields = call_user_func(array($class, 'getTypes'));

					foreach ($providedFields as $key => $info) {
						$this->typeMap[$key] = $class;
						$this->infoMap[$key] = $info;
					}
				}
			}
		}

		uasort($this->infoMap, array($this, 'compareName'));

		$data = array(
			'typeMap' => $this->typeMap,
			'infoMap' => $this->infoMap,
		);

		if (defined('TIKI_PREFS_DEFINED')) {
			$cachelib->cacheItem($cacheKey, serialize($data));
			$this->setPreCacheTypeMap($data);
		}
	}

	function compareName($a, $b)
	{
		return strcasecmp($a['name'], $b['name']);
	}

	function getFieldTypes()
	{
		return $this->infoMap;
	}

	function getFieldInfo($type)
	{
		if (isset($this->infoMap[$type])) {
			return $this->infoMap[$type];
		}
	}
	
	/**
	 * Get a list of field types by their letter type and the corresponding class name
	 * @Example 'q' => 'Tracker_Field_AutoIncrement', ... 
	 * @return array letterType => classname
	 */
	function getTypeMap() {
		return $this->typeMap;
	}

	function getHandler($field_info, $itemData = array())
	{
		if (!isset($field_info['type'])) {
			return null;
		}
		$type = $field_info['type'];

		if (isset($this->typeMap[$type])) {
			$info = $this->infoMap[$type];
			$class = $this->typeMap[$type];

			global $prefs;
			foreach ($info['prefs'] as $pref) {
				if ($prefs[$pref] != 'y') {
					TikiLib::lib('errorreport')->report(tr('Tracker Field Factory Error: Pref "%0" required for field type "%1"', $pref, $class));
					return null;
				}
			}

			if (class_exists($class) && is_callable(array($class, 'build'))) {
				return call_user_func(array($class, 'build'), $type, $this->trackerDefinition, $field_info, $itemData);
			} else {
				return new $class($field_info, $itemData, $this->trackerDefinition);
			}
		}
	}
}

