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
			case 'mPDF':
				self::loadMpdf($class);
				break;
			default:
				if (array_key_exists($class, static::$mapInternalClassesNotInComposer)) {
					self::loadInternalClassesNotInComposer($class);
				}
				break;
		}
	}

	/**
	 * Handle mPDF autoload process based on tiki preferences
	 *
	 * @param string $class the name of the class to be autoloaded
	 */
	protected static function loadMpdf($class)
	{
		global $prefs;

		if ($class == 'mPDF'
			&& ! empty($prefs['print_pdf_from_url']) && $prefs['print_pdf_from_url'] === 'mpdf'
			&& ! empty($prefs['print_pdf_mpdf_path'])) {
			$path = $prefs['print_pdf_mpdf_path'];
			if (substr($prefs['print_pdf_mpdf_path'], -1) != '/') {
				$path .= '/';
			}
			$path .= 'mpdf.php';

			if (file_exists($path)) {
				include_once($path);
			}
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
