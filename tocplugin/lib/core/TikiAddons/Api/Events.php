<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_Events extends TikiAddons_Api {
	protected static $eventMap = [];

	// overriding isInstalled in TikiAddons_Utilities
	function isInstalled($folder) {
		$installed1 = array_keys(self::$parents);
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (parent::isInstalled($folder) && in_array($folder, $installed1) ) {
			return true;
		} else {
			return false;
		}
	}

	static function setEventMap($folder, $eventMap) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		foreach ($eventMap as $event) {
			$event->folder = $folder;
			self::$eventMap[] = $event;
		}
		return true;
	}

	static function bindEvents($events) {
		foreach (self::$eventMap as $event) {
			$events->bind($event->event, Tiki_Event_Lib::defer($event->lib, $event->function), array("addon_args" => (array) $event->params));
		}
	}
}