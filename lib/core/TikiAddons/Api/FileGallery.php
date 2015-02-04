<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_FileGallery extends TikiAddons_Api
{
	protected static $parents = array();

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

	static function setParents($folder, $parent) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$parents[$folder] = $parent;
		return true;
	}

}
