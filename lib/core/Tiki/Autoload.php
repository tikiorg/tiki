<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tiki internal autoload, to enable a cleaner autoload process.
 *
 * This is specially useful when depending on preferences to decide what files to load.
 */
class Tiki_Autoload
{
	/**
	 * @var array Map class to file, for static class resolution
	 */
	protected static $mapInternalClassesNotInComposer = [
		'PdfGenerator' => 'lib/pdflib.php',
	];

	/**
	 * Entry point to the autoload
	 *
	 * @param string $class the name of the class to be autoloaded
	 */
	public static function autoload($class)
	{
		switch ($class) {
			default:
				if (array_key_exists($class, static::$mapInternalClassesNotInComposer)) {
					self::loadInternalClassesNotInComposer($class);
				}
				break;
		}
	}

	/**
	 * Static loader for classes in Tiki not loaded automatically by composer (not PSR-0, PSR-4)
	 *
	 * Note: this should move in the future to use static mapping in composer (after removing duplicated class names)
	 *
	 * @param $class
	 */
	protected static function loadInternalClassesNotInComposer($class)
	{
		global $tikipath;

		include_once $tikipath . DIRECTORY_SEPARATOR . static::$mapInternalClassesNotInComposer[$class];
	}
}
