<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Api_NavBar extends TikiAddons_Api
{
	protected static $templates = array();

	// overriding isInstalled in TikiAddons_Utilities
	function isInstalled($folder) {
		$installed1 = array_keys(self::$templates);
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (parent::isInstalled($folder) && in_array($folder, $installed1) ) {
			return true;
		} else {
			return false;
		}
	}

	static function setTemplate($folder, $tpl) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		self::$templates[$folder] = $tpl;
		return true;
	}

	function getNavBar($token) {
		$folder = $this->getFolderFromToken($token);
		if (!$this->isInstalled($folder)) {
			return '';
		}
		TikiLib::lib('smarty')->assign('from', 'forum');
		$ret = TikiLib::lib('smarty')->fetch(self::$templates[$folder]);

		return $ret;
	}
}