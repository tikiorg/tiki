<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Factory
{
	private $trackerDefinition;
	private $typeMap = array();
	private $infoMap = array();

	function __construct($trackerDefinition)
	{
		$this->trackerDefinition = $trackerDefinition;

		$fieldMap = $this->buildTypeMap(array(
			'lib/core/Tracker/Field' => 'Tracker_Field_',
		));
	}

	private function buildTypeMap($paths)
	{
		global $prefs;
		$cachelib = TikiLib::lib('cache');
		$cacheKey = 'fieldtypes.' . $prefs['language'];

		if ($data = $cachelib->getSerialized($cacheKey)) {
			$this->typeMap = $data['typeMap'];
			$this->infoMap = $data['infoMap'];
			return;
		}

		foreach ($paths as $path => $prefix) {
			foreach (glob("$path/*.php") as $file) {
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

		$cachelib->cacheItem($cacheKey, serialize(array(
			'typeMap' => $this->typeMap,
			'infoMap' => $this->infoMap,
		)));
	}

	function getFieldTypes()
	{
		return $this->infoMap;
	}

	function getHandler($field_info, $itemData = array())
	{
		$type = $field_info['type'];

		if (isset($this->typeMap[$type])) {
			$class = $this->typeMap[$type];

			if (is_callable(array($class, 'build'))) {
				return call_user_func(array($class, 'build'), $type, $this->trackerDefinition, $field_info, $itemData); 
			} else {
				return new $class($field_info, $itemData, $this->trackerDefinition);
			}
		}
	}
}

