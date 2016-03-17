<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter
{
	private $valueSet;
	static private $pageTitle = '';
	static private $pageDescription = '';

	function __construct($valueSet)
	{
		$this->valueSet = $valueSet;
	}

	function getPlainValues()
	{
		return $this->valueSet;
	}

	function __call($format, $arguments)
	{
		$name = array_shift($arguments);
		if (! $arguments = array_shift($arguments)) {
			$arguments = array();
		}

		if (isset($arguments['pagetitle']) && $arguments['pagetitle'] !== 'n' && empty(self::$pageTitle)) {
			self::$pageTitle = $this->valueSet[$name];
			TikiLib::lib('smarty')->assign('title', self::$pageTitle);
		}

		if (isset($arguments['pagedescription']) && $arguments['pagedescription'] !== 'n' && empty(self::$pageDescription)) {
			self::$pageDescription = $this->valueSet[$name];
			TikiLib::lib('smarty')->assign('description', self::$pageDescription);
		}

		// ugly exception for wikiplugin - TODO better?
		if ($format !== 'wikiplugin' && (! isset($this->valueSet[$name]) || is_null($this->valueSet[$name]))) {
			return tr("No value for '%0'", $name);
		}

		$class = 'Search_Formatter_ValueFormatter_' . ucfirst($format);
		if (class_exists($class)) {
			global $prefs;
			$cachelib = TikiLib::lib('cache');
			$cacheName = $format . ':' . $name . ':' . $prefs['language'] . ':' . serialize($this->valueSet[$name]);
			$cacheType = 'search_valueformatter';

			if (in_array($format, $prefs['unified_cached_formatters']) && $cachelib->isCached($cacheName, $cacheType)) {
				return $cachelib->getCached($cacheName, $cacheType);
			} else {
				$formatter = new $class($arguments);
				$ret = $formatter->render($name, $this->valueSet[$name], $this->valueSet);
				if (in_array($format, $prefs['unified_cached_formatters']) && $formatter->canCache()) {
					$cachelib->cacheItem($cacheName, $ret, $cacheType);
				}
				return ($ret);
			}
		} else {
			return tr("Unknown formatting rule '%0' for '%1'", $format, $name);
		}
	}
}

