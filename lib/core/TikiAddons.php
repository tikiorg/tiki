<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class TikiAddons
{
	private static $installed = array();
	private static $paths = array();
	protected static $addons = array();

	public static function refresh()
	{
		self::$installed = array();
		self::$paths = array();
		foreach ( glob(TIKI_PATH . '/addons/*/tikiaddon.json') as $file ) {
			try {
				$conf = json_decode(file_get_contents($file));
				$package = str_replace('_', '/', basename(dirname($file)));
				self::$installed[$package] = $conf;
				self::$paths[$package] = dirname($file);
			} catch (InvalidArgumentException $e) {
				// Do nothing, absence of tikiaddon.json
			}
		}
	}

	public static function get($name)
	{
		if (isset(self::$addons[$name])) {
			return self::$addons[$name];
		}

		return new TikiAddons_Addon($name);
	}

	public static function getInstalled()
	{
		return self::$installed;
	}

	public static function getPaths()
	{
		return self::$paths;
	}

}